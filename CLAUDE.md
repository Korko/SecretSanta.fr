# SecretSanta.fr

## Where the code lives

- **Framagit is the source of truth**: https://framagit.org/Korko/SecretSanta (project id `19814`).
  Merge requests, reviews and CI (`.gitlab-ci.yml`) are there.
- **GitHub (`Korko/SecretSanta.fr`) is a mirror** that syncs from Framagit. Do not push to it:
  anything pushed there is not on Framagit and gets overwritten.

## Writing to Framagit from a cloud session

`git push` to Framagit does not work in a cloud session: the proxy authenticates with a
`PRIVATE-TOKEN` header, which the API accepts but git over HTTPS does not. Everything goes
through the REST API (`https://framagit.org/api/v4`, no token to pass, the proxy adds it):

- Reading works with plain git: `git fetch https://framagit.org/Korko/SecretSanta.git <branch>`.
- Commits: `POST /projects/19814/repository/commits` with `branch`, `commit_message`,
  `author_name`/`author_email` and `actions` (`create`/`update`/`delete`/`chmod`, content in
  `base64`). The committer is the token's owner. A new branch starts from `start_branch` or
  `start_sha`.
- `.claude/framagit-push.py` replays a range of local commits that way, then checks that every
  commit on Framagit has exactly the local tree:
  `python3 .claude/framagit-push.py 19814 <branch> <remote_start_sha> <local_base> <local_tip>`
  (`--dry-run` lists the commits and their size).
- Merge requests: `POST /projects/19814/merge_requests`, descriptions in French.

The API cannot create merge commits. To fix an MR in conflict: rebase it locally on its target,
then rewrite its branch with the script (`<remote_start_sha>` = the target's tip on Framagit, the
first commit is pushed with `force`). For stacked MRs (an MR based on another), rebase the base
first, then replay only the next MR's own commits with `<remote_start_sha>` = the base MR's new
tip on Framagit, so both share the same commits. They are merged in order, without squash.

Branch names must not end in `.x` (e.g. `ccr-…-bounce-reason-1.10.x`): the `*.*.x` protection rule,
meant for `1.10.x` and the like, matches them too, and a protected branch can't be rewritten
(`force`), so its MR can't be rebased.

## Generated files that are committed

- `public/build` (no node in prod): in a conflict, do not merge it, run `npm run build` on the
  resolved sources and commit the result. CI fails if it does not match the sources.
- `resources/js/langs` comes from `lang/*.php`: `php artisan vue-i18n:generate --multi-locales`
  (`composer build` runs both).

## Checks

`composer test` (Pint, Rector, PHPStan, type coverage, Pest), like the `php` CI job. PHP 8.4 comes
from packages.sury.org, with the key committed in `.gitlab/sury-php.gpg`.
