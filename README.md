# Green Valley School — website, parent portal & school management (PHP + MySQL)

A complete school website for a Bangladeshi school: public site, **online admission**, online results, student/parent portal,
teacher panel, accounts panel and a full admin panel. No Firebase, no framework — plain PHP 7.4+ and MySQL/MariaDB.
English / বাংলা switch. Works on cPanel shared hosting.

## What is inside
| Area | Pages / features |
|---|---|
| Public site | Home (animated hero, notice ticker, stats, events, news, gallery), About (history, mission, committee, facilities), Academics (curriculum, class routine, calendar, exam routine, fee structure, GPA scale), Teachers, Notices (search/filter), News · Events · Photo gallery, Result (class + roll), Contact (form + map) |
| Online admission | 4-step form, validation, reference number, printable slip, bKash/Nagad fee info, status check (reference + mobile) |
| Student / Parent portal | Overview, attendance calendar, results + GPA trend + printable marksheet, fee statement + receipts, homework, routine, leave applications, messages |
| Teacher panel | Attendance, marks entry (live GPA), homework, routine, messages to parents, leave requests |
| Accounts panel | Fees (collect, receipts, generate for a class), expenses, income-vs-expense chart |
| Admin panel | Dashboard, admissions review (accept → student + logins created), students (ID card, promote class, CSV), teachers, logins/passwords, exams, merit list, admit cards, notices/news/events/gallery, site settings, activity log, JSON backup |

## Install (5 minutes)
1. Upload everything to your hosting (e.g. `public_html/`). PHP 7.4+ with `pdo_mysql`.
2. Create an empty MySQL database + user in cPanel.
3. Open `https://your-site/setup.php`, enter the database details and your admin e-mail/password.
   Tick **Load demo data** to see every feature with sample students, results, fees, attendance and admissions.
4. **Delete `setup.php`** from the server.
5. Sign in at `/login.html` with your admin e-mail.

## One-click demo
With demo data installed, `/login.html` shows buttons: Admin · Teacher · Accountant · Student · Parent.
Demo accounts can look at everything, but **nothing they change is saved** and sensitive actions (passwords, backup, settings) are blocked.
**Before going live: Admin → Settings → Demo mode → OFF.** (This also disables the demo accounts.)
Sample students get random passwords — use *Logins → reset password* if you want to sign in as one.

## Default content
Even without demo data the installer adds editable starter content: school settings, notices, news, events, gallery tiles, 12 teachers and class routines.
Edit or delete them in the admin panel. Change the school name, address, fees, calendar, admission dates, bKash/Nagad numbers in **Settings**.

## Roles & security
* Roles: `admin`, `accountant`, `teacher`, `student`, `parent`. Permissions are enforced **on the server** (not only hidden in the UI).
* Passwords hashed with `password_hash`; PHP sessions (HttpOnly, SameSite=Lax, Secure on HTTPS); staff sessions expire after 12 h idle.
* CSRF defence (custom header required on every POST), per-IP rate limits (login, admission, contact, result lookup), all output escaped.
* `api/` and `database/` are not browsable (`.htaccess`). Enable HTTPS and the redirect lines in `.htaccess`.
* Behind Cloudflare? Set `'trust_cloudflare' => true` in `api/config.php` so rate-limits use the real visitor IP.
* Nginx: deny everything in `/api/` except `index.php`, and deny `/database/`.

## Honest limits
* Admission fee and tuition payments via bKash/Nagad are **not auto-verified** — the accountant checks the TrxID and marks the fee paid.
* No SMS gateway built in (parents are told on the portal/website). Add one later in `api/index.php` if you have an SMS API.
* Photos: paste image links in *Gallery* (no file upload). Notices can link to files on Google Drive etc.
* Result lookup is by class + roll (like most BD schools); marks are only visible after you tick *Published* on the exam.
* UI text is English/Bangla; the school's own content (notices, settings) is shown exactly as you type it.

## Files
`index.html about.html academics.html teachers.html admission.html notices.html news.html results.html contact.html login.html` — public site ·
`portal.html` — student/parent · `admin.html` — admin/teacher/accountant · `api/` — PHP API · `database/schema.sql` · `js/` · `css/`.
