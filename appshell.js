/* Shared dashboard shell for the portal and admin panel */
(function () {
const { $, $$, esc, ic, api, toast, modal } = GV;
GV.marksheet = (r, sc) => {
 const g = GV.gpa(r.marks); const rows = Object.keys(r.marks).map(k => { const m = +r.marks[k], gg = GV.grade(m); return '<tr><td>' + GV.esc(k) + '</td><td class="n">100</td><td class="n"><b>' + m + '</b></td><td>' + gg[0] + '</td><td class="n">' + gg[1].toFixed(2) + '</td></tr>'; }).join('');
 return '<div class="ms rv in"><h3>' + GV.esc(sc.name || '') + '</h3><div class="sub">' + GV.esc(r.exam.name) + ' — Marksheet</div><div class="meta"><span>Name: <b>' + GV.esc(r.student.name) + '</b></span><span>Class: <b>' + GV.clsName(r.student.cls) + (r.student.sec ? ' (' + GV.esc(r.student.sec) + ')' : '') + '</b></span><span>Roll: <b>' + GV.esc(r.student.roll) + '</b></span><span>Father: <b>' + GV.esc(r.student.father) + '</b></span></div><div class="tw"><table class="tbl"><thead><tr><th>Subject</th><th class="n">Full</th><th class="n">Obtained</th><th>Grade</th><th class="n">Point</th></tr></thead><tbody>' + rows + '</tbody></table></div><div class="gpa"><div class="' + (g.fail ? 'fail' : '') + '"><b>' + g.gpa.toFixed(2) + '</b><small>GPA</small></div><div class="' + (g.fail ? 'fail' : '') + '"><b>' + g.g + '</b><small>Letter grade</small></div><div><b>' + g.total + '</b><small>Total marks</small></div><div class="' + (g.fail ? 'fail' : '') + '"><b>' + (g.fail ? 'Failed' : 'Passed') + '</b><small>Result</small></div></div><p style="font-size:12.5px;color:var(--mut);margin:14px 0 0">This is a computer-generated result for information only. Official marksheet is issued by the school office.</p></div><p style="text-align:center" class="noprint"><button class="btn ghost" onclick="print()">' + GV.ic('print') + ' Print marksheet</button></p>';
};


GV.printHTML = html => { const f = document.createElement('iframe'); f.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0'; document.body.appendChild(f); const d = f.contentWindow.document; d.open(); d.write('<!doctype html><html><head><meta charset="utf-8"><link rel="stylesheet" href="css/school.css"><style>body{background:#fff;padding:20px}.rv{opacity:1!important;transform:none!important}</style></head><body class="' + document.body.className + '">' + html + '</body></html>'); d.close(); f.onload = () => { try { f.contentWindow.focus(); f.contentWindow.print(); } catch (e) {} setTimeout(() => f.remove(), 4000); }; setTimeout(() => { try { f.contentWindow.focus(); f.contentWindow.print(); } catch (e) {} }, 700); };
GV.csv = (name, rows) => { const q = v => '"' + String(v == null ? '' : v).replace(/"/g, '""') + '"'; const b = new Blob(['﻿' + rows.map(r => r.map(q).join(',')).join('\r\n')], { type: 'text/csv' }); const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = name + '.csv'; a.click(); setTimeout(() => URL.revokeObjectURL(a.href), 2000); };
GV.pwModal = () => { const m = modal('<h3>Change password</h3><form id="pwf"><div class="f"><label>Current password</label><input id="pw0" type="password" required></div><div class="f"><label>New password (6+ characters)</label><input id="pw1" type="password" minlength="6" required></div><button class="btn">Save</button><div id="pwm"></div></form>'); $('#pwf', m.el).onsubmit = async e => { e.preventDefault(); try { const r = await api('change_pw', { body: { old: $('#pw0').value, new: $('#pw1').value } }); if (r.demo) { toast('Demo mode — nothing is saved'); } else toast('Password changed'); m.close(); } catch (x) { $('#pwm').innerHTML = '<div class="msgbox err">' + esc(x.message) + '</div>'; } }; };
/* cfg: {brand, sub, nav:[{g}|{id,ic,l}], views:{id:fn(ct)}, user, home} */
GV.app = function (cfg) {
 const root = $('#root'), u = cfg.user; const ini = String(u.name || '?').split(/\s+/).map(w => w[0]).slice(0, 2).join('').toUpperCase();
 root.innerHTML = '<div class="app"><aside class="sb" id="sb"><a class="brand" href="index.html" style="display:flex;gap:10px;align-items:center">' + GV.LOGO + '<span><b>' + esc(cfg.brand) + '</b><small>' + esc(cfg.sub || '') + '</small></span></a>'
  + cfg.nav.map(n => n.g ? '<div class="grp">' + esc(n.g) + '</div>' : '<button class="nv" data-v="' + n.id + '">' + ic(n.ic) + '<span>' + esc(n.l) + '</span><span class="bd" style="display:none"></span></button>').join('')
  + '<div style="flex:1"></div><a class="nv" href="index.html">' + ic('home') + '<span>Public website</span></a><button class="nv" id="lo">' + ic('out') + '<span>Sign out</span></button></aside><div class="mn"><div class="tb"><button class="bg2" id="sbt" aria-label="Menu">' + ic('menu') + '</button><h1 id="ttl"></h1><button class="btn ghost sm" id="pwb" title="Change password">' + ic('lock') + '</button><div class="who"><span class="av" style="background:var(--g700)">' + esc(ini) + '</span><span>' + esc(u.name) + '<small>' + esc(u.role) + '</small></span></div></div>'
  + (u.demo ? '<div class="demob">' + ic('star') + ' <span>Demo mode — explore freely. Changes you make are <b>not saved</b>.</span></div>' : '') + '<div class="ct" id="ct"></div></div></div><div class="scrim" id="scr"></div>';
 const sb = $('#sb'), scr = $('#scr'); const tog = o => { sb.classList.toggle('open', o); scr.classList.toggle('on', o); };
 $('#sbt').onclick = () => tog(!sb.classList.contains('open')); scr.onclick = () => tog(false);
 $('#lo').onclick = async () => { await api('logout', { body: {} }).catch(() => {}); location.href = 'login.html'; };
 $('#pwb').onclick = GV.pwModal;
 const first = (cfg.nav.find(n => n.id) || {}).id; let cur = null;
 const render = async () => {
  const id = (location.hash.replace(/^#\/?/, '') || first).split('/')[0]; const nv = cfg.nav.find(n => n.id === id) || cfg.nav.find(n => n.id === first);
  cur = nv.id; $$('.nv[data-v]').forEach(b => b.classList.toggle('on', b.dataset.v === cur)); $('#ttl').textContent = nv.l; tog(false);
  const ct = $('#ct'); ct.innerHTML = '<div class="sk" style="height:120px;margin-bottom:16px"></div><div class="sk" style="height:260px"></div>';
  try { await cfg.views[cur](ct); } catch (e) { if (e.status === 401) { location.href = 'login.html'; return; } ct.innerHTML = '<div class="msgbox err">' + esc(e.message) + '</div>'; console.error(e); }
  scrollTo(0, 0);
 };
 $$('.nv[data-v]').forEach(b => b.onclick = () => { if (location.hash === '#/' + b.dataset.v) render(); else location.hash = '#/' + b.dataset.v; });
 addEventListener('hashchange', render); render();
 return { render, badge: (id, n) => { const b = $('.nv[data-v=' + id + '] .bd'); if (b) { b.textContent = n; b.style.display = n ? '' : 'none'; } }, go: id => location.hash = '#/' + id };
};
})();
