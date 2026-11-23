#!/usr/bin/env python3
"""Replay local commits onto a Framagit branch through the commits API (see CLAUDE.md).

usage: framagit-push.py <project_id> <branch> <remote_start_sha> <local_base> <local_tip> [--dry-run]

The first commit overwrites <branch> (force) starting at <remote_start_sha>, which must
already be on Framagit and have the same tree as <local_base>; the following ones are
appended. Each commit keeps its author and message. At the end the tree of every remote
commit is compared with the local one.
"""
import base64
import json
import subprocess
import sys
import urllib.error
import urllib.request

API = "https://framagit.org/api/v4"
REPO = "https://framagit.org/Korko/SecretSanta.git"


def git(*args, binary=False):
    out = subprocess.run(["git", *args], check=True, capture_output=True).stdout
    return out if binary else out.decode()


def api(method, path, payload=None):
    data = json.dumps(payload).encode() if payload is not None else None
    req = urllib.request.Request(API + path, data=data, method=method,
                                 headers={"Content-Type": "application/json"})
    try:
        with urllib.request.urlopen(req, timeout=300) as r:
            return json.load(r)
    except urllib.error.HTTPError as e:
        sys.exit(f"{method} {path} -> {e.code}: {e.read().decode()[:2000]}")


def actions_for(commit):
    parent = git("rev-parse", commit + "^").strip()
    raw = git("diff", "--raw", "--no-renames", "-z", parent, commit, binary=True).split(b"\0")
    actions = []
    i = 0
    while i < len(raw) - 1:
        meta = raw[i].decode()
        path = raw[i + 1].decode()
        i += 2
        old_mode, new_mode, _, _, status = meta.lstrip(":").split()
        if status == "D":
            actions.append({"action": "delete", "file_path": path})
            continue
        if status not in ("A", "M"):
            sys.exit(f"unsupported status {status} for {path}")
        if new_mode not in ("100644", "100755"):
            sys.exit(f"unsupported mode {new_mode} for {path}")
        content = git("cat-file", "blob", f"{commit}:{path}", binary=True)
        actions.append({
            "action": "create" if status == "A" else "update",
            "file_path": path,
            "encoding": "base64",
            "content": base64.b64encode(content).decode(),
        })
        if (status == "A" and new_mode == "100755") or (status == "M" and old_mode != new_mode):
            actions.append({"action": "chmod", "file_path": path,
                            "execute_filemode": new_mode == "100755"})
    return actions


def main():
    project, branch, start_sha, base, tip = sys.argv[1:6]
    dry = "--dry-run" in sys.argv
    commits = git("rev-list", "--reverse", "--no-merges", f"{base}..{tip}").split()
    if git("rev-list", "--merges", f"{base}..{tip}").strip():
        sys.exit("merge commits in range: not supported")
    if git("rev-parse", base + "^{tree}") != git("rev-parse", start_sha + "^{tree}"):
        sys.exit(f"{base} and {start_sha} do not have the same tree")
    created = []
    for n, c in enumerate(commits):
        author_name = git("log", "-1", "--format=%an", c).strip()
        author_email = git("log", "-1", "--format=%ae", c).strip()
        message = git("log", "-1", "--format=%B", c).rstrip("\n")
        acts = actions_for(c)
        payload = {"branch": branch, "commit_message": message, "actions": acts,
                   "author_name": author_name, "author_email": author_email}
        if n == 0:
            payload.update({"start_sha": start_sha, "force": True})
        print(f"{c[:7]} {message.splitlines()[0]!r}: {len(acts)} actions, "
              f"{len(json.dumps(payload)) // 1024} KiB")
        if dry:
            continue
        res = api("POST", f"/projects/{project}/repository/commits", payload)
        print(f"  -> {res['id'][:7]} on {branch}")
        created.append((c, res["id"]))
    if dry:
        return
    # Verify each remote commit has exactly the local tree
    subprocess.run(["git", "fetch", "-q", REPO, branch], check=True)
    ok = True
    for local, remote in created:
        lt, rt = git("rev-parse", local + "^{tree}").strip(), git("rev-parse", remote + "^{tree}").strip()
        print(f"{local[:7]} -> {remote[:7]}: tree {'OK' if lt == rt else 'MISMATCH'}")
        ok &= lt == rt
    sys.exit(0 if ok else 1)


main()
