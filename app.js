/* Green Valley School — shared shell: API, i18n, icons, header/footer, animations */
(function () {
'use strict';
const $ = (s, r) => (r || document).querySelector(s), $$ = (s, r) => Array.from((r || document).querySelectorAll(s));
const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const P = {
 book: '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
 phone: '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>',
 mail: '<rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="22,6 12,13 2,6"/>',
 pin: '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
 clock: '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
 user: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
 users: '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
 calendar: '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
 bell: '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
 file: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M16 13H8M16 17H8"/>',
 check: '<polyline points="20 6 9 17 4 12"/>', x: '<path d="M18 6 6 18M6 6l12 12"/>',
 arrow: '<path d="M5 12h14"/><polyline points="12 5 19 12 12 19"/>', menu: '<path d="M3 6h18M3 12h18M3 18h18"/>',
 search: '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>',
 print: '<polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>',
 home: '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
 chart: '<path d="M12 20V10M18 20V4M6 20v-4"/>', money: '<path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
 cog: '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/>',
 out: '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><path d="M21 12H9"/>',
 edit: '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/>',
 trash: '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>',
 plus: '<path d="M12 5v14M5 12h14"/>', eye: '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
 shield: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>', award: '<circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>',
 msg: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
 flask: '<path d="M9 3h6M10 3v6L4.5 19a2 2 0 0 0 1.7 3h11.6a2 2 0 0 0 1.7-3L14 9V3"/><path d="M7.5 15h9"/>',
 laptop: '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M2 20h20"/>', ball: '<circle cx="12" cy="12" r="10"/><path d="M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20M2 12h20"/>',
 mic: '<rect x="9" y="2" width="6" height="12" rx="3"/><path d="M19 10v1a7 7 0 0 1-14 0v-1M12 18v4M8 22h8"/>',
 bus: '<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M3 11h18M7 21v-4M17 21v-4"/><circle cx="7.5" cy="14" r=".6"/><circle cx="16.5" cy="14" r=".6"/>',
 heart: '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/>',
 cup: '<path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"/>',
 trophy: '<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6M18 9h1.5a2.5 2.5 0 0 0 0-5H18M4 22h16M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22M18 2H6v7a6 6 0 0 0 12 0V2z"/>',
 tree: '<path d="M12 2l5 8h-3l4 6H6l4-6H7z"/><path d="M12 16v6"/>', music: '<path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>',
 palette: '<circle cx="13.5" cy="6.5" r=".8"/><circle cx="17.5" cy="10.5" r=".8"/><circle cx="8.5" cy="7.5" r=".8"/><circle cx="6.5" cy="12.5" r=".8"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1-.23-.27-.38-.62-.38-1 0-.83.67-1.5 1.5-1.5H16c3.31 0 6-2.69 6-6 0-4.96-4.49-9-10-9z"/>',
 flag: '<path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><path d="M4 22v-7"/>', cap: '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
 star: '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
 download: '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><path d="M12 15V3"/>',
 clip: '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/>',
 building: '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/>',
 grid: '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
 lock: '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>', card: '<rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/>',
 globe: '<circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
 up: '<polyline points="18 15 12 9 6 15"/>', list: '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>', refresh: '<polyline points="23 4 23 10 17 10"/><path d="M20.5 15a9 9 0 1 1-2.1-9.4L23 10"/>'
};
const ic = (n, cls) => '<svg class="ic ' + (cls || '') + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + (P[n] || P.star) + '</svg>';

/* ---------- i18n (UI text; school content is edited in the admin in whatever language you like) ---------- */
const T = {
 en: { home: 'Home', about: 'About', academics: 'Academics', teachers: 'Teachers', admission: 'Admission', notices: 'Notices', news: 'News', result: 'Result', contact: 'Contact', login: 'Login', apply: 'Apply Online', notice: 'Notice', quick: 'Quick links', checkResult: 'Check Result', portal: 'Student / Parent Login', allNotices: 'All notices', upcoming: 'Upcoming events', latest: 'Latest notices', why: 'Why Green Valley', whyP: 'Everything a child needs to learn, grow and shine — under one campus.', principal: "Principal's message", readMore: 'Read more', viewAll: 'View all', achievers: 'Our proud achievers', reviews: 'What parents say', gallery: 'Life at Green Valley', newsT: 'Latest news', students: 'Students', teachersN: 'Teachers', years: 'Years of excellence', pass: 'Pass rate %', openNow: 'Admission open', heroA: 'Where every child', heroB: 'grows', heroC: 'with confidence', heroP: 'A modern, disciplined and caring school from KG to Class 10 — with online admission, a parent portal and results at your fingertips.', visit: 'Contact us', facilities: 'Facilities', apply2: 'Ready to join Green Valley?', apply2p: 'Apply online in 5 minutes — no queue, no paperwork.', address: 'Address', follow: 'Quick links', rights: 'All rights reserved.', search: 'Search', send: 'Send message', name: 'Your name', phone: 'Mobile', msg: 'Message', hours: 'Office hours' },
 bn: { home: 'হোম', about: 'আমাদের সম্পর্কে', academics: 'একাডেমিক', teachers: 'শিক্ষকবৃন্দ', admission: 'ভর্তি', notices: 'নোটিশ', news: 'সংবাদ', result: 'ফলাফল', contact: 'যোগাযোগ', login: 'লগইন', apply: 'অনলাইনে ভর্তি', notice: 'নোটিশ', quick: 'দ্রুত লিংক', checkResult: 'ফলাফল দেখুন', portal: 'শিক্ষার্থী / অভিভাবক লগইন', allNotices: 'সব নোটিশ', upcoming: 'আসন্ন অনুষ্ঠান', latest: 'সর্বশেষ নোটিশ', why: 'কেন গ্রিন ভ্যালি', whyP: 'শেখা, বেড়ে ওঠা ও উজ্জ্বল হওয়ার জন্য একটি শিশুর যা যা দরকার — এক ক্যাম্পাসে।', principal: 'অধ্যক্ষের বাণী', readMore: 'আরও পড়ুন', viewAll: 'সব দেখুন', achievers: 'আমাদের গর্বিত কৃতী শিক্ষার্থী', reviews: 'অভিভাবকদের মতামত', gallery: 'গ্রিন ভ্যালির দিনগুলো', newsT: 'সর্বশেষ সংবাদ', students: 'শিক্ষার্থী', teachersN: 'শিক্ষক', years: 'বছরের সাফল্য', pass: 'পাসের হার %', openNow: 'ভর্তি চলছে', heroA: 'প্রতিটি শিশু', heroB: 'বেড়ে উঠুক', heroC: 'আত্মবিশ্বাসে', heroP: 'কেজি থেকে দশম শ্রেণি পর্যন্ত আধুনিক, শৃঙ্খলাবদ্ধ ও যত্নশীল বিদ্যালয় — অনলাইন ভর্তি, অভিভাবক পোর্টাল ও ফলাফল হাতের মুঠোয়।', visit: 'যোগাযোগ করুন', facilities: 'সুযোগ-সুবিধা', apply2: 'গ্রিন ভ্যালিতে ভর্তি হতে চান?', apply2p: '৫ মিনিটে অনলাইনে আবেদন করুন — লাইন নেই, ঝামেলা নেই।', address: 'ঠিকানা', follow: 'দ্রুত লিংক', rights: 'সর্বস্বত্ব সংরক্ষিত।', search: 'খুঁজুন', send: 'বার্তা পাঠান', name: 'আপনার নাম', phone: 'মোবাইল', msg: 'বার্তা', hours: 'অফিস সময়' }
};
let lang = 'en'; try { lang = localStorage.getItem('gv_lang') || 'en'; } catch (e) {}
const t = k => (T[lang] && T[lang][k]) || T.en[k] || k;
const MON = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const fd = d => { if (!d) return ''; const x = String(d).slice(0, 10).split('-'); return x.length === 3 ? (+x[2]) + ' ' + MON[+x[1] - 1] + ' ' + x[0] : d; };
const dbox = (d, c) => { const x = String(d || '').slice(0, 10).split('-'); return '<div class="dt ' + (c || '') + '"><b>' + (+x[2] || '') + '</b><small>' + (MON[+x[1] - 1] || '') + '</small></div>'; };
const money = n => '৳' + (Math.round(+n || 0)).toLocaleString('en-IN');

/* ---------- API ---------- */
async function api(route, o) {
 o = o || {}; const qs = new URLSearchParams(Object.assign({ r: route }, o.q || {}));
 const init = { method: o.body !== undefined ? 'POST' : 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } };
 if (o.body !== undefined) { init.headers['Content-Type'] = 'application/json'; init.body = JSON.stringify(o.body); }
 let r, j; try { r = await fetch((window.API_URL || 'api/index.php') + '?' + qs, init); j = await r.json(); } catch (e) { throw Object.assign(new Error('Cannot reach the server. Check your connection.'), { code: 'net' }); }
 if (!j.ok) throw Object.assign(new Error(j.msg || 'Error'), { code: j.code, status: r.status });
 return j;
}
function toast(m, err) { let e = $('#toast'); if (!e) { e = document.createElement('div'); e.id = 'toast'; document.body.appendChild(e); } e.textContent = m; e.className = 'on' + (err ? ' err' : ''); clearTimeout(e._t); e._t = setTimeout(() => e.className = '', 3200); }
function modal(html, onclose) { const m = document.createElement('div'); m.className = 'md'; m.innerHTML = '<div><button class="x" aria-label="Close">✕</button>' + html + '</div>'; document.body.appendChild(m); const c = () => { m.remove(); onclose && onclose(); }; m.addEventListener('click', e => { if (e.target === m || e.target.closest('.x')) c(); }); return { el: m, close: c }; }

/* ---------- school illustration (inline SVG, animated) ---------- */
const HERO_SVG = '<svg class="sch" viewBox="0 0 560 520" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="School building illustration"><defs><linearGradient id="sk" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#cfeedd"/><stop offset="1" stop-color="#fff8e0"/></linearGradient><linearGradient id="bd" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#f1ede0"/></linearGradient></defs>'
 + '<rect x="20" y="30" width="520" height="440" rx="44" fill="url(#sk)"/><g class="sun" style="transform-origin:440px 120px"><circle cx="440" cy="120" r="46" fill="#f4b400"/><g stroke="#f4b400" stroke-width="6" stroke-linecap="round"><path d="M440 52v-18M440 206v18M372 120h-18M526 120h18M392 72l-12-12M488 168l12 12M488 72l12-12M392 168l-12 12"/></g></g>'
 + '<g class="cloud" fill="#fff" opacity=".95"><ellipse cx="130" cy="110" rx="46" ry="18"/><ellipse cx="160" cy="98" rx="32" ry="20"/><ellipse cx="104" cy="102" rx="26" ry="14"/></g>'
 + '<g class="cloud" style="animation-delay:-9s" fill="#fff" opacity=".8"><ellipse cx="320" cy="70" rx="36" ry="13"/><ellipse cx="342" cy="62" rx="24" ry="15"/></g>'
 + '<path d="M20 400c90-34 170-30 260-8s170 14 260-18v96a44 44 0 0 1-44 44H64a44 44 0 0 1-44-44z" fill="#1fa862"/><path d="M20 430c110-26 200-18 290 0s150 6 230-14v54a44 44 0 0 1-44 44H64a44 44 0 0 1-44-44z" fill="#138a50"/>'
 + '<g><rect x="110" y="236" width="340" height="170" rx="8" fill="url(#bd)" stroke="#0e6a3d" stroke-width="4"/><path d="M96 238h368l-34-46H130z" fill="#d62839"/><path d="M96 238h368" stroke="#9d1020" stroke-width="5"/>'
 + '<rect x="238" y="170" width="84" height="70" rx="6" fill="#fff" stroke="#0e6a3d" stroke-width="4"/><path d="M228 172h104l-52-40z" fill="#0e6a3d"/><circle cx="280" cy="206" r="16" fill="#e4f5ec" stroke="#0e6a3d" stroke-width="3"/><path d="M280 196v10l7 5" stroke="#0e6a3d" stroke-width="3" fill="none" stroke-linecap="round"/>'
 + '<g fill="#bfe6cf" stroke="#0e6a3d" stroke-width="3"><rect x="136" y="262" width="52" height="52" rx="6"/><rect x="206" y="262" width="52" height="52" rx="6"/><rect x="302" y="262" width="52" height="52" rx="6"/><rect x="372" y="262" width="52" height="52" rx="6"/></g>'
 + '<g stroke="#0e6a3d" stroke-width="2.5"><path d="M162 262v52M136 288h52M232 262v52M206 288h52M328 262v52M302 288h52M398 262v52M372 288h52"/></g>'
 + '<rect x="250" y="320" width="60" height="86" rx="8" fill="#0e6a3d"/><path d="M280 320v86" stroke="#06361f" stroke-width="3"/><circle cx="270" cy="366" r="3" fill="#f4b400"/><circle cx="290" cy="366" r="3" fill="#f4b400"/>'
 + '<g fill="#e4f5ec" stroke="#0e6a3d" stroke-width="3"><rect x="232" y="410" width="96" height="10" rx="3"/><rect x="220" y="420" width="120" height="10" rx="3"/></g></g>'
 + '<g><rect x="478" y="120" width="6" height="290" rx="3" fill="#8a6a3b"/><g class="flagw"><rect x="484" y="126" width="64" height="40" fill="#0b6b3a"/><circle cx="512" cy="146" r="12" fill="#d62839"/></g></g>'
 + '<g><circle cx="96" cy="380" r="22" fill="#0e6a3d"/><circle cx="72" cy="392" r="16" fill="#138a50"/><rect x="92" y="392" width="8" height="30" fill="#7a5a30"/></g>'
 + '<g transform="translate(150 428)"><circle cx="0" cy="-30" r="9" fill="#f2c08d"/><path d="M-10 -20h20v26h-20z" fill="#2563eb"/><path d="M-8 6v16M8 6v16" stroke="#10231a" stroke-width="5" stroke-linecap="round"/></g><g transform="translate(420 432)"><circle cx="0" cy="-30" r="9" fill="#e0a877"/><path d="M-10 -20h20l4 26h-28z" fill="#d62839"/><path d="M-6 6v16M6 6v16" stroke="#10231a" stroke-width="5" stroke-linecap="round"/></g></svg>';
const LOGO = '<svg viewBox="0 0 64 64" aria-hidden="true"><defs><linearGradient id="lg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#1fa862"/><stop offset="1" stop-color="#06361f"/></linearGradient></defs><path d="M32 3 57 12v20c0 14-10 24-25 29C17 56 7 46 7 32V12z" fill="url(#lg)"/><circle cx="32" cy="22" r="7" fill="#d62839"/><path d="M14 50c6-12 12-18 18-18s12 6 18 18" fill="none" stroke="#f4b400" stroke-width="4" stroke-linecap="round"/><path d="M20 40c4-4 8-6 12-6s8 2 12 6" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" opacity=".8"/></svg>';

/* ---------- shell ---------- */
const NAV = [['home', 'index.html'], ['about', 'about.html'], ['academics', 'academics.html'], ['teachers', 'teachers.html'], ['admission', 'admission.html'], ['notices', 'notices.html'], ['news', 'news.html'], ['result', 'results.html'], ['contact', 'contact.html']];
const GV = { $, $$, esc, ic, t, fd, dbox, money, api, toast, modal, D: null, page: '', get lang() { return lang; }, HERO_SVG, LOGO, P };
GV.setLang = l => { lang = l; try { localStorage.setItem('gv_lang', l); } catch (e) {} document.body.classList.toggle('bn', l === 'bn'); document.documentElement.lang = l === 'bn' ? 'bn' : 'en'; };

function shell(pageKey) {
 const S = (GV.D && GV.D.settings && GV.D.settings.school) || {};
 const nm = lang === 'bn' && S.bn ? S.bn : (S.name || 'School');
 document.body.insertAdjacentHTML('afterbegin', '<div id="prog"></div><div class="top"><div class="wrap"><div class="l"><a href="tel:' + esc((S.phone || '').replace(/[^+\d]/g, '')) + '">' + ic('phone') + esc(S.phone || '') + '</a><a href="mailto:' + esc(S.email || '') + '" class="hidem">' + ic('mail') + esc(S.email || '') + '</a></div><div class="r"><div class="lang"><button data-l="en" class="' + (lang === 'en' ? 'on' : '') + '">EN</button><button data-l="bn" class="' + (lang === 'bn' ? 'on' : '') + '">বাংলা</button></div><a href="login.html">' + ic('lock') + t('login') + '</a></div></div></div>'
  + '<header class="hd"><div class="wrap"><a class="brand" href="index.html">' + LOGO + '<span><b>' + esc(nm) + '</b><small>' + esc(lang === 'bn' ? (S.tag_bn || '') : ('EIIN ' + (S.eiin || '') + ' · Est. ' + (S.est || ''))) + '</small></span></a><nav class="nav" id="nav">'
  + NAV.map(n => '<a href="' + n[1] + '" class="' + (n[0] === pageKey ? 'on' : '') + '">' + t(n[0]) + '</a>').join('') + '<a class="btn red sm" href="admission.html" style="color:#fff;margin-left:8px">' + t('apply') + '</a></nav><button class="burger" id="bg" aria-label="Menu">' + ic('menu') + '</button></div></header><div class="scrim" id="scrim"></div>');
 const ann = ((GV.D.settings || {}).announce || '').split('|').map(s => s.trim()).filter(Boolean);
 if (ann.length) $('.hd').insertAdjacentHTML('afterend', '<div class="tick"><div class="wrap"><span class="tag">' + t('notice') + '</span><div class="trk"><div class="mv">' + ann.concat(ann).map(a => '<span>' + esc(a) + '</span>').join('') + '</div></div></div></div>');
 const st = $('main').dataset;
 document.body.insertAdjacentHTML('beforeend', '<footer class="ft"><div class="wrap"><div class="grid"><div><div class="brand" style="margin-bottom:14px">' + LOGO + '<span><b style="color:#fff">' + esc(nm) + '</b></span></div><p>' + esc(S.tag || '') + '</p></div><div><h4>' + t('follow') + '</h4><ul>' + NAV.slice(1).map(n => '<li><a href="' + n[1] + '">' + t(n[0]) + '</a></li>').join('') + '</ul></div><div><h4>' + t('students') + ' &amp; ' + t('portal').split('/')[1] + '</h4><ul><li><a href="login.html">' + t('portal') + '</a></li><li><a href="results.html">' + t('checkResult') + '</a></li><li><a href="admission.html#status">Admission status</a></li><li><a href="notices.html">' + t('allNotices') + '</a></li></ul></div><div><h4>' + t('contact') + '</h4><ul><li>' + ic('pin') + '<span>' + esc(S.address || '') + '</span></li><li>' + ic('phone') + '<span>' + esc(S.phone || '') + '</span></li><li>' + ic('mail') + '<span>' + esc(S.email || '') + '</span></li><li>' + ic('clock') + '<span>' + esc(S.hours || '') + '</span></li></ul></div></div><div class="cp"><span>© ' + new Date().getFullYear() + ' ' + esc(S.name || '') + '. ' + t('rights') + '</span><span>' + (S.board ? esc(S.board) + ' · ' : '') + 'EIIN ' + esc(S.eiin || '') + '</span></div></div></footer><div class="fab"><button class="totop" id="top" aria-label="Back to top">' + ic('up') + '</button></div>');
 document.title = (document.title.split('—')[0] || '').trim() + ' — ' + (S.name || 'School');
 $$('.lang button').forEach(b => b.onclick = () => { GV.setLang(b.dataset.l); location.reload(); });
 const nav = $('#nav'), sc = $('#scrim'); const tg = o => { nav.classList.toggle('open', o); sc.classList.toggle('on', o); };
 $('#bg').onclick = () => tg(!nav.classList.contains('open')); sc.onclick = () => tg(false); $$('#nav a').forEach(a => a.onclick = () => tg(false));
 const hd = $('.hd'), pr = $('#prog'), tp = $('#top');
 const onS = () => { const y = scrollY, h = document.documentElement.scrollHeight - innerHeight; hd.classList.toggle('sc', y > 10); pr.style.width = (h > 0 ? y / h * 100 : 0) + '%'; tp.classList.toggle('on', y > 600); };
 addEventListener('scroll', onS, { passive: true }); onS(); tp.onclick = () => scrollTo({ top: 0, behavior: 'smooth' });
}
GV.reveal = function () {
 const els = $$('.rv:not(.in)'); if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in')); return; }
 const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: .12, rootMargin: '0px 0px -40px 0px' });
 els.forEach(e => io.observe(e));
 $$('[data-count]:not(.cd)').forEach(el => { el.classList.add('cd'); const io2 = new IntersectionObserver(es => { if (es[0].isIntersecting) { io2.disconnect(); const to = parseFloat(el.dataset.count), dec = (el.dataset.count.split('.')[1] || '').length, t0 = performance.now(); (function f(n) { const p = Math.min(1, (n - t0) / 1400), v = to * (1 - Math.pow(1 - p, 3)); el.firstChild.nodeValue = v.toLocaleString('en-IN', { minimumFractionDigits: dec, maximumFractionDigits: dec }); if (p < 1) requestAnimationFrame(f); })(t0); } }); io2.observe(el); });
};
/* boot(pageKey, renderFn(D) -> html, after(D)) */
GV.boot = async function (key, render, after) {
 GV.page = key; GV.setLang(lang);
 const m = $('main'); m.innerHTML = '<div class="wrap" style="padding:80px 20px"><div class="sk" style="height:46px;width:60%;margin-bottom:18px"></div><div class="sk" style="height:200px"></div></div>';
 try { GV.D = (await api('public')); } catch (e) { m.innerHTML = '<div class="wrap" style="padding:90px 20px;text-align:center"><h2>Site is not ready</h2><p>' + esc(e.message) + '</p><p><a class="btn" href="setup.php">Open installer</a></p></div>'; return; }
 shell(key); m.innerHTML = render(GV.D); GV.reveal(); after && after(GV.D);
 if (location.hash) { const e = document.getElementById(location.hash.slice(1)); e && setTimeout(() => e.scrollIntoView({ behavior: 'smooth', block: 'start' }), 300); }
};
/* grading — Bangladesh GPA scale (out of 100 per subject) */
GV.grade = m => m >= 80 ? ['A+', 5] : m >= 70 ? ['A', 4] : m >= 60 ? ['A-', 3.5] : m >= 50 ? ['B', 3] : m >= 40 ? ['C', 2] : m >= 33 ? ['D', 1] : ['F', 0];
GV.gpa = marks => { const v = Object.values(marks || {}).map(Number); if (!v.length) return { gpa: 0, total: 0, fail: true, g: 'F' }; const gs = v.map(x => GV.grade(x)); const fail = gs.some(g => g[1] === 0); const gpa = fail ? 0 : gs.reduce((a, g) => a + g[1], 0) / gs.length; const g = fail ? 'F' : gpa >= 5 ? 'A+' : gpa >= 4 ? 'A' : gpa >= 3.5 ? 'A-' : gpa >= 3 ? 'B' : gpa >= 2 ? 'C' : 'D'; return { gpa: gpa, total: v.reduce((a, b) => a + b, 0), fail: fail, g: g, n: v.length }; };
GV.subjects = (cls) => { const s = ((GV.D && GV.D.settings && GV.D.settings.subjects) || {}); const n = cls === 'KG' ? 0 : parseInt(cls, 10) || 0; return n >= 6 ? (s['6-10'] || []) : n >= 1 ? (s['1-5'] || []) : ['Bangla', 'English', 'Mathematics']; };
GV.CLASSES = ['KG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
GV.clsName = c => c === 'KG' ? 'KG' : 'Class ' + c;
window.GV = GV;
})();
