// End-to-end flow on a real browser: npm run e2e (see tests/e2e/run.sh)
const { chromium } = require('playwright');
const fs = require('fs');
const BASE = 'http://127.0.0.1:8765';
(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext(); await ctx.grantPermissions(['clipboard-read', 'clipboard-write']); const page = await ctx.newPage();
  const errors = [];
  page.on('console', m => { if (['error', 'warning'].includes(m.type())) errors.push(m.type() + ': ' + m.text().slice(0, 300)); });
  page.on('response', async r => { if (r.status() >= 400) errors.push(r.status() + ' ' + r.request().method() + ' ' + r.url() + ' ' + (await r.text().catch(() => '')).slice(0, 200)); });
  page.on('pageerror', e => errors.push('pageerror: ' + e.message));
  let failed = 0;
  const step = async (name, fn) => { try { await fn(); console.log('OK  ', name); } catch (e) { failed++; console.log('FAIL', name, '-', e.message.split('\n')[0]); } };

  await page.goto(BASE + '/');
  await step('3 participant rows', async () => { const n = await page.locator('tr.participant').count(); if (n !== 3) throw new Error('rows=' + n); });
  await step('empty submit shows participant errors', async () => {
    await page.locator('#randomForm button[type=submit]').click();
    await page.waitForTimeout(300);
    const n = await page.locator('tr.participant .is-invalid').count(); if (n < 3) throw new Error('invalid=' + n);
  });
  await step('add participant', async () => { await page.click('.participant-add'); if (await page.locator('tr.participant').count() !== 4) throw new Error('no 4th'); });
  await step('remove participant', async () => { await page.locator('.participant-remove').last().click(); await page.waitForTimeout(1500); if (await page.locator('tr.participant').count() !== 3) throw new Error('not removed'); });
  await step('CSV import ISO-8859-15 with exclusions', async () => {
    await page.click('.participants-import');
    await page.setInputFiles('#uploadCsv input[type=file]', __dirname + '/latin15.csv');
    await page.locator('.modal-footer .btn-primary, button:has-text("Importer")').last().click();
    await page.waitForTimeout(1500);
    const names = await page.locator('.participant-name').evaluateAll(e => e.map(x => x.value));
    const excl = await page.locator('tr.participant').nth(1).locator('.multiselect__tag').allInnerTexts();
    console.log('     names', names, 'excl row2', excl);
    if (names.join() !== 'Zoé,Cœur,Max' || excl.join().trim() !== 'Zoé') throw new Error('bad import');
  });
  await step('exclusion via multiselect', async () => {
    await page.locator('tr.participant').nth(2).locator('.multiselect').click();
    await page.locator('tr.participant').nth(2).locator('.multiselect__element').first().click();
    const n = await page.locator('tr.participant').nth(2).locator('select option').count(); if (n !== 1) throw new Error('options=' + n);
  });
  await step('{TARGET} link copies to clipboard (dynamic i18n component)', async () => {

    await page.locator('h2, legend').first().click(); await page.waitForTimeout(300);
    await page.locator('a.link').last().click(); await page.waitForTimeout(300);
    const t = await page.evaluate(() => navigator.clipboard.readText()); if (!/\{(SANTA|TARGET)\}/.test(t)) throw new Error('clipboard=' + t);
  });
  await page.goto(BASE + '/');
  await step('email typo suggestion fills the field', async () => {
    const f = page.locator('.participant-email').first();
    await f.fill('zoe@gmial.com');
    const sug = page.locator('.email-suggest').first(); await sug.waitFor({ timeout: 2000 });
    console.log('     suggest:', await sug.innerText());
    await sug.locator('a').click();
    if (await f.inputValue() !== 'zoe@gmail.com') throw new Error('value=' + await f.inputValue());
    if (await page.locator('.email-suggest').count()) throw new Error('still shown');
  });
  await step('fill & submit', async () => {
    const ppl = [['Alice', 'alice@example.com'], ['Bob', 'bob@example.com'], ['Chloe', 'chloe@example.com']];
    for (let i = 0; i < 3; i++) { await page.locator('.participant-name').nth(i).fill(ppl[i][0]); await page.locator('.participant-email').nth(i).fill(ppl[i][1]); }
    await page.fill('#mailTitle, input[name=title]', 'Noel');
    await page.fill('#mailContent', 'Salut {SANTA}, offre a {TARGET}');
    const d = new Date(Date.now() + 5 * 864e5).toISOString().slice(0, 10);
    await page.fill('input[name=data-expiration]', d);
    await page.locator('#randomForm button[type=submit]').click();
    await page.waitForSelector('text=/envoy/i', { timeout: 8000 });
  });
  await step('toggle organizer out shows organizer fields', async () => {
    await page.goto(BASE + '/'); await page.locator('.organizerToggle input').evaluate(e => e.click());
    await page.waitForSelector('#organizer', { timeout: 2000 });
  });
  await step('mail content textarea autosizes', async () => {
    const h1 = await page.$eval('#mailContent', e => e.offsetHeight);
    await page.fill('#mailContent', 'a\n'.repeat(15)); await page.locator('#mailContent').press('End');
    const h2 = await page.$eval('#mailContent', e => e.offsetHeight); if (h2 <= h1) throw new Error(h1 + '->' + h2);
  });

  const log = fs.readFileSync(process.env.LOG, 'utf8').replace(/=\r?\n/g, '').replace(/=3D/g, '=');
  const org = (log.match(/https?:\/\/127\.0\.0\.1:8765\/org\/[^"'\s<]+/g) || []).pop();
  const ds = (log.match(/https?:\/\/127\.0\.0\.1:8765\/dearSanta\/[^"'\s<]+/g) || []).pop();
  console.log('org', !!org, 'dearSanta', !!ds);
  await step('participating organizer gets a single email (target + organizer block)', async () => {
    if (!log.includes('En tant qu')) throw new Error('no organizer block in the target email');
    if (log.includes('capitulatif Organisateur')) throw new Error('separate recap sent');
  });
  if (ds) {
    await page.goto(ds);
    await step('dearSanta page shows form', async () => { await page.waitForSelector('textarea', { timeout: 8000 }); });
    await step('dearSanta shows targets not revealed', async () => {
      const t = await page.locator('.targets-revealed').innerText(); console.log('     indicator:', t.trim());
      if (!/pas été consultée/.test(t)) throw new Error(t);
    });
    await step('dearSanta empty submit shows error', async () => {
      await page.locator('button[type=submit]').click(); await page.waitForTimeout(300);
      if (!await page.locator('textarea.is-invalid').count()) throw new Error('no error');
    });
  }
  if (org) {
    await page.goto(org);
    await step('organizer page lists participants', async () => { await page.waitForSelector('text=Alice', { timeout: 8000 }); });
    await step('resend failed emails in one click', async () => {
      const btn = page.locator('button.btn-warning:has(.fa-redo)');
      if (!await btn.isDisabled()) throw new Error('enabled with no failure');
      require('child_process').execFileSync('php', ['-r', `$p = new PDO('sqlite:${process.env.DB_DATABASE}'); $p->exec("update mails set delivery_status='error' where id in (select id from mails order by id desc limit 2)");`]);
      await page.reload(); await page.waitForSelector('text=Alice', { timeout: 8000 });
      console.log('     button:', await btn.innerText());
      const posts = []; page.on('request', r => { if (r.method() === 'POST' && r.url().includes('changeEmail')) posts.push(r.url()); });
      await btn.click(); await page.waitForTimeout(1500);
      if (posts.length !== 2) throw new Error('posts=' + posts.length);
      if (!await btn.isDisabled()) throw new Error('still enabled');
    });
    await step('organizer email edit: error shown on blur, submit blocked', async () => {
      await page.locator('button:has(.fa-edit)').first().click();
      const input = page.locator('input[name=input]').first();
      await input.fill('pas-un-email'); await input.blur(); await page.waitForTimeout(300);
      if (!await page.locator('input[name=input].is-invalid').count()) throw new Error('no invalid class');
      if (!await page.locator('.invalid-tooltip').count()) throw new Error('no tooltip');
      if (!await page.locator('button:has(.fa-check-circle)').first().isDisabled()) throw new Error('submit enabled');
      await page.locator('button:has(.fa-times-circle)').first().click();
    });
    await step('organizer email edit: valid change is saved', async () => {
      await page.locator('button:has(.fa-edit)').first().click();
      const input = page.locator('input[name=input]').first();
      await input.fill('alice2@example.com'); await input.blur(); await page.waitForTimeout(200);
      await page.locator('button:has(.fa-check-circle)').first().click();
      await page.waitForSelector('[data-state=viewUpdated]', { timeout: 5000 });
    });
    await step('organizer changes the event date, past date refused', async () => {
      const help = page.locator('#expires_at + small');
      const before = await help.innerText();
      const d = new Date(Date.now() + 40 * 864e5).toISOString().slice(0, 10);
      const [r] = await Promise.all([page.waitForResponse(/changeDate/), page.locator('#expires_at').fill(d)]);
      if (!r.ok()) throw new Error('status ' + r.status());
      await page.waitForFunction(b => document.querySelector('#expires_at + small').innerText !== b, before);
      console.log('     ', await help.innerText());
      const [r2] = await Promise.all([page.waitForResponse(/changeDate/), page.locator('#expires_at').fill('2020-01-01')]);
      if (r2.status() !== 422) throw new Error('past date status ' + r2.status());
      console.log('     alert:', await page.locator('.ajs-content:visible').innerText()); await page.locator('.ajs-footer .btn-primary:visible').click();
      const v = await page.locator('#expires_at').inputValue(); if (v !== d) throw new Error('input not restored: ' + v + ' vs ' + d);
    });
    const ok = async () => { await page.waitForTimeout(500); await page.locator('.ajs-footer .btn-primary:visible').last().click(); };
    const rows = () => page.locator('tbody tr:not(.pending-add)').count();
    await step('organizer adds a participant', async () => {
      await page.fill('.participant-add-name', 'Dan'); await page.fill('.participant-add-email', 'dan@example.com');
      await page.locator('button.participant-add').click();
      const w = await page.locator('.ajs-content:visible .alert-warning').innerText(); console.log('     warning:', w.trim());
      if (!/il y a 0 jour/.test(w)) throw new Error('no warning');
      await ok();
      await page.waitForSelector('tbody td:text-is("Dan")', { timeout: 8000 });
      await ok();
      if (await rows() !== 4) throw new Error('rows=' + await rows());
      const c = await page.locator('.received-count').innerText(); if (!/\/4/.test(c)) throw new Error(c);
    });
    await step('several at once: 2 added, 1 removed, a single request', async () => {
      const posts = []; const h = r => { if (r.method() === 'POST' && r.url().includes('/participants')) posts.push(r.url()); }; page.on('request', h);
      await page.locator('.multi-toggle').click();
      for (const [n, e] of [['Eve', 'eve@example.com'], ['Fred', 'fred@example.com']]) {
        await page.fill('.participant-add-name', n); await page.fill('.participant-add-email', e); await page.locator('button.participant-add').click();
      }
      await page.locator('tbody tr:has(td:text-is("Dan")) .participant-remove').click();
      if (await page.locator('tr.pending-add').count() !== 2 || await page.locator('tr.pending-remove').count() !== 1) throw new Error('pending not shown');
      if (posts.length) throw new Error('sent before validation');
      await page.locator('.multi-validate').click();
      await ok();
      await page.waitForSelector('tbody td:text-is("Fred")', { timeout: 8000 });
      await ok();
      page.off('request', h);
      const names = await page.locator('tbody tr td:first-child').allInnerTexts();
      console.log('     ', names.join(', '), '- posts', posts.length);
      if (posts.length !== 1 || names.length !== 5 || names.some(n => n.includes('Dan'))) throw new Error('bad result');
    });
    await step('organizer reveals targets after confirmation', async () => {
      if (await page.locator('td.target').count()) throw new Error('targets visible before reveal');
      await page.locator('button:has-text("Révéler les cibles")').click();
      await page.locator('.ajs-footer .btn-primary:visible').click();
      await page.waitForSelector('td.target', { timeout: 5000 });
      const t = await page.locator('td.target').allInnerTexts(); console.log('     targets:', t);
      if (t.length !== await rows() || t.some(x => !x.trim())) throw new Error('bad targets');
      await page.reload(); await page.waitForSelector('td.target', { timeout: 8000 });
    });
    await step('final CSV downloadable once revealed', async () => {
      const b = page.locator('button.btn-primary:has(.fa-download), button.btn-primary:has-text("cibles")').last();
      if (await b.isDisabled()) throw new Error('final CSV still disabled');
      const [dl] = await Promise.all([page.waitForEvent('download', { timeout: 8000 }), b.click()]);
      console.log('     ', dl.suggestedFilename());
    });
    await step('dearSanta now shows targets revealed', async () => {
      const org = page.url(); await page.goto(ds); await page.waitForSelector('.targets-revealed');
      const t = await page.locator('.targets-revealed').innerText(); if (!/a consulté/.test(t)) throw new Error(t);
      await page.goto(org); await page.waitForSelector('text=Alice', { timeout: 8000 });
    });
    await step('purge asks for the confirmation word, wrong word keeps it open, right word purges', async () => {
      await page.locator('button.btn-danger:visible').last().click();
      await page.waitForSelector('.ajs-content input', { timeout: 2000 });
      const help = await page.locator('.ajs-content').innerText(); console.log('     dialog:', help.replace(/\s+/g, ' ').slice(0, 200));
      await page.fill('.ajs-content input', 'nope'); await page.locator('.ajs-footer .btn-primary').click(); await page.waitForTimeout(400);
      if (!await page.locator('.ajs-content input:visible').count()) throw new Error('closed on wrong word');
      const word = (help.match(/"([^"]+)"/) || [])[1]; await page.fill('.ajs-content input', word); await page.locator('.ajs-footer .btn-primary:visible').click();
      await page.waitForSelector('.ajs-header:has(.fa-check)', { timeout: 5000 });
    });
  }
  for (const u of ['/faq', '/fix', '/nope']) await page.goto(BASE + u);
  await step('fix form validates', async () => {
    await page.goto(BASE + '/fix'); await page.locator('button[type=submit]').click(); await page.waitForTimeout(300);
    if (!await page.locator('.is-invalid').count()) throw new Error('no error');
  });
  await step('non participating organizer gets a separate recap', async () => {
    const start = fs.readFileSync(process.env.LOG, 'utf8').length;
    await page.goto(BASE + '/'); await page.locator('.organizerToggle input').evaluate(e => e.click());
    await page.fill('input[name="organizer[name]"]', 'Orga'); await page.fill('input[name="organizer[email]"]', 'orga@example.com');
    const ppl = [['Gus', 'gus@example.com'], ['Hugo', 'hugo@example.com'], ['Ines', 'ines@example.com']];
    for (let i = 0; i < 3; i++) { await page.fill(`input[name="participants[${i}][name]"]`, ppl[i][0]); await page.fill(`input[name="participants[${i}][email]"]`, ppl[i][1]); }
    await page.fill('#mailTitle, input[name=title]', 'Noel 2');
    await page.fill('#mailContent', 'Salut {SANTA}, offre a {TARGET}');
    await page.fill('input[name=data-expiration]', new Date(Date.now() + 5 * 864e5).toISOString().slice(0, 10));
    await page.locator('#randomForm button[type=submit]').click();
    await page.waitForSelector('text=/envoy/i', { timeout: 8000 }).catch(async e => {
      console.log('     invalid:', await page.locator('.is-invalid').evaluateAll(l => l.map(x => x.name + '=' + x.value)), await page.locator('.invalid-feedback:visible, .invalid-tooltip:visible').allInnerTexts());
      throw e;
    });
    const added = fs.readFileSync(process.env.LOG, 'utf8').slice(start).replace(/=\r?\n/g, '');
    if (!added.includes('capitulatif Organisateur')) throw new Error('no recap');
    if (added.includes('En tant qu')) throw new Error('organizer block in a participant email');
  });
  console.log('console:', errors.length ? '\n  ' + [...new Set(errors)].join('\n  ') : 'clean');
  await browser.close();
  process.exit(failed ? 1 : 0);
})().catch(e => { console.error(e); process.exit(1); });
