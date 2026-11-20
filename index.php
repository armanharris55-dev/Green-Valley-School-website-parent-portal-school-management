<?php
/* Green Valley School — JSON API. Single entry point: api/index.php?r=<route> */
define('APP', 1);
require __DIR__ . '/lib.php';
mb_internal_encoding('UTF-8');
start_session();
$r = (string)($_GET['r'] ?? '');
$post = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($post && (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest')) fail(403, 'csrf', 'Bad request');   // CSRF defence
try { route($r, $post); } catch (PDOException $e) { error_log('SQL: ' . $e->getMessage()); fail(500, 'server', 'Server error'); }

function route($r, $post) {
    $b = $post ? body() : [];
    switch ($r) {
    /* ================= public ================= */
    case 'public': {
        $s = site_get('settings');
        $by = function ($a, $f, $desc = true) { usort($a, function ($x, $y) use ($f, $desc) { $c = strcmp((string)($x[$f] ?? ''), (string)($y[$f] ?? '')); return $desc ? -$c : $c; }); return $a; };
        $students = (int)q("SELECT COUNT(*) FROM rec WHERE k='student'")->fetchColumn();
        $teachers = rec_rows('teacher', '', [], 'id ASC');
        $exams = array_values(array_filter(rec_rows('exam'), function ($e) { return !empty($e['published']); }));
        out(['ok' => true, 'settings' => $s, 'demo' => demo_on(),
            'notices' => array_slice($by(rec_rows('notice'), 'date'), 0, 40),
            'news' => array_slice($by(rec_rows('news'), 'date'), 0, 12),
            'events' => array_slice($by(rec_rows('event'), 'date', false), 0, 30),
            'gallery' => array_slice(rec_rows('gallery'), 0, 36),
            'teachers' => $teachers, 'exams' => $by($exams, 'date'), 'routines' => rec_rows('routine'),
            'counts' => ['students' => $students, 'teachers' => count($teachers)]]);
    }
    case 'result': {
        need_rate('result', 40, 600);
        $exam = str($_GET['exam'] ?? '', 24); $cls = str($_GET['cls'] ?? '', 8); $roll = (int)($_GET['roll'] ?? 0);
        $ex = rec_get($exam);
        if (!$ex || $ex['_k'] !== 'exam' || empty($ex['published'])) fail(404, 'nf', 'Result not published for this exam');
        $st = null; foreach (rec_rows('student', ' AND cls=?', [$cls]) as $s) if ((int)($s['roll'] ?? 0) === $roll) { $st = $s; break; }
        if (!$st) fail(404, 'nf', 'No student found with this class and roll');
        $res = rec_rows('result', ' AND sid=?', [$st['id']]);
        $m = null; foreach ($res as $x) if (($x['exam'] ?? '') === $exam) $m = $x;
        if (!$m) fail(404, 'nf', 'Result not found for this student');
        out(['ok' => true, 'student' => ['name' => $st['name'], 'roll' => $st['roll'], 'cls' => $st['cls'], 'sec' => $st['sec'] ?? '', 'father' => $st['father'] ?? ''], 'exam' => ['id' => $ex['id'], 'name' => $ex['name'], 'year' => $ex['year'] ?? ''], 'marks' => $m['marks'] ?? []]);
    }
    case 'adm_apply': {
        if (!$post) fail(405, 'method', 'POST only');
        need_rate('adm', 6, 3600);
        $a = site_get('settings')['admission'] ?? [];
        if (isset($a['open']) && !$a['open']) fail(403, 'closed', 'Admission is closed');
        $d = is_array($b['d'] ?? null) ? $b['d'] : [];
        $o = [];
        foreach (['name', 'name_bn', 'dob', 'gender', 'blood', 'birthreg', 'cls', 'prevschool', 'prevclass', 'father', 'fjob', 'fphone', 'mother', 'mphone', 'guardian', 'email', 'present', 'permanent', 'religion', 'trx', 'payvia', 'note'] as $k) $o[$k] = str($d[$k] ?? '', $k === 'present' || $k === 'permanent' || $k === 'note' ? 300 : 120);
        $o['fphone'] = clean_phone($o['fphone']); $o['mphone'] = clean_phone($o['mphone']);
        if ($o['name'] === '' || $o['father'] === '' || $o['mother'] === '') fail(400, 'invalid', 'Student, father and mother names are required');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $o['dob'])) fail(400, 'invalid', 'Enter a valid date of birth');
        if (!in_array($o['gender'], ['Male', 'Female', 'Other'], true)) fail(400, 'invalid', 'Select gender');
        if (!valid_phone($o['fphone'])) fail(400, 'invalid', 'Enter a valid guardian mobile number (01XXXXXXXXX)');
        if ($o['cls'] === '') fail(400, 'invalid', 'Select the class');
        if ($o['present'] === '') fail(400, 'invalid', 'Enter the present address');
        $n = (int)q("SELECT COUNT(*) FROM rec WHERE k='admission'")->fetchColumn() + 1;
        $o['ref'] = 'ADM-' . date('Y') . '-' . str_pad((string)$n, 4, '0', STR_PAD_LEFT);
        $o['status'] = 'pending'; $o['date'] = date('Y-m-d'); $o['note_admin'] = '';
        rec_put('admission', uid('ad'), $o);
        out(['ok' => true, 'ref' => $o['ref']]);
    }
    case 'adm_status': {
        need_rate('admst', 20, 600);
        $ref = strtoupper(str($b['ref'] ?? '', 30)); $ph = clean_phone($b['phone'] ?? '');
        foreach (rec_rows('admission', '', [], 't DESC', 3000) as $x) if (strtoupper($x['ref']) === $ref && $x['fphone'] === $ph)
            out(['ok' => true, 'ref' => $x['ref'], 'name' => $x['name'], 'cls' => $x['cls'], 'status' => $x['status'], 'note' => $x['note_admin'] ?? '', 'date' => $x['date'], 'test' => (site_get('settings')['admission']['test'] ?? '')]);
        fail(404, 'nf', 'No application found. Check the reference number and the mobile number you used.');
    }
    case 'contact': {
        if (!$post) fail(405, 'method', 'POST only');
        need_rate('contact', 6, 3600);
        $o = ['name' => str($b['name'] ?? '', 80), 'phone' => str($b['phone'] ?? '', 20), 'email' => str($b['email'] ?? '', 80), 'text' => str($b['text'] ?? '', 1000), 'date' => date('Y-m-d H:i'), 'read' => false];
        if ($o['name'] === '' || strlen($o['text']) < 3) fail(400, 'invalid', 'Please enter your name and message');
        rec_put('message', uid('ms'), $o); out(['ok' => true]);
    }

    /* ================= auth ================= */
    case 'login': {
        if (!$post) fail(405, 'method', 'POST only');
        need_rate('login', 12, 600);
        $l = strtolower(trim(str($b['login'] ?? '', 80))); $p = (string)($b['pass'] ?? '');
        $u = q('SELECT * FROM users WHERE LOWER(login)=?', [$l])->fetch();
        if (!$u || !$u['active'] || $u['demo'] || !password_verify($p, $u['pass_hash'])) fail(401, 'auth/bad', 'Wrong ID or password');
        session_regenerate_id(true); $_SESSION['uid'] = $u['id']; $_SESSION['t'] = time();
        out(['ok' => true, 'role' => $u['role'], 'name' => $u['name']]);
    }
    case 'demo': {
        if (!demo_on()) fail(403, 'demo-off', 'Demo mode is switched off');
        need_rate('demo', 30, 600);
        $role = str($_GET['role'] ?? ($b['role'] ?? ''), 12);
        $u = q('SELECT * FROM users WHERE demo=1 AND role=? LIMIT 1', [$role])->fetch();
        if (!$u) fail(404, 'nf', 'No demo account for this role');
        session_regenerate_id(true); $_SESSION['uid'] = $u['id']; $_SESSION['t'] = time();
        out(['ok' => true, 'role' => $u['role'], 'name' => $u['name'], 'demo' => true]);
    }
    case 'logout': unset($_SESSION['uid']); session_regenerate_id(true); out(['ok' => true]);
    case 'me': { $u = me(); out(['ok' => true, 'user' => $u ? ['role' => $u['role'], 'name' => $u['name'], 'login' => $u['login'], 'ref' => $u['ref'], 'demo' => (bool)$u['demo'], 'perms' => perms($u['role'])] : null]); }
    case 'change_pw': {
        $u = need_login(); no_demo($u);
        need_rate('chpw', 8, 600);
        $row = q('SELECT pass_hash FROM users WHERE id=?', [$u['id']])->fetch();
        if (!password_verify((string)($b['old'] ?? ''), $row['pass_hash'])) fail(401, 'auth/bad', 'Current password is wrong');
        $n = (string)($b['new'] ?? ''); if (strlen($n) < 6) fail(400, 'invalid', 'New password must be at least 6 characters');
        q('UPDATE users SET pass_hash=? WHERE id=?', [password_hash($n, PASSWORD_DEFAULT), $u['id']]); out(['ok' => true]);
    }

    /* ================= student / parent portal ================= */
    case 'portal': {
        $u = need_login(['student', 'parent']);
        $st = rec_get($u['ref']); if (!$st || $st['_k'] !== 'student') fail(404, 'nf', 'Student record not found');
        $sid = $st['id']; $cls = $st['cls'];
        $att = q('SELECT day,st FROM att WHERE sid=? AND day>=? ORDER BY day', [$sid, date('Y-m-d', strtotime('-120 days'))])->fetchAll();
        $res = rec_rows('result', ' AND sid=?', [$sid]);
        $exams = []; foreach (rec_rows('exam') as $e) $exams[$e['id']] = $e;
        $res = array_values(array_filter($res, function ($x) use ($exams) { return isset($exams[$x['exam']]) && !empty($exams[$x['exam']]['published']); }));
        $hw = rec_rows('homework', ' AND cls=?', [$cls], 't DESC', 30);
        $routine = rec_rows('routine', ' AND cls=?', [$cls], 't DESC', 1);
        $msgs = array_values(array_filter(rec_rows('msg', '', [], 't DESC', 200), function ($m) use ($sid, $cls) { return ($m['sid'] ?? '') === $sid || ((($m['sid'] ?? '') === '') && (($m['cls'] ?? '') === $cls || ($m['cls'] ?? '') === '')); }));
        out(['ok' => true, 'role' => $u['role'], 'demo' => (bool)$u['demo'], 'student' => $st, 'att' => $att, 'results' => $res, 'exams' => array_values($exams),
            'fees' => rec_rows('fee', ' AND sid=?', [$sid]), 'homework' => $hw, 'routine' => $routine ? $routine[0] : null,
            'leaves' => rec_rows('leave', ' AND sid=?', [$sid]), 'msgs' => array_slice($msgs, 0, 30)]);
    }
    case 'leave_add': {
        $u = need_login(['student', 'parent']); no_demo($u);
        $st = rec_get($u['ref']); if (!$st) fail(404, 'nf', 'Student not found');
        $o = ['sid' => $st['id'], 'cls' => $st['cls'], 'name' => $st['name'], 'from' => str($b['from'] ?? '', 10), 'to' => str($b['to'] ?? '', 10), 'reason' => str($b['reason'] ?? '', 400), 'status' => 'pending', 'date' => date('Y-m-d'), 'by' => $u['role']];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $o['from']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $o['to']) || $o['reason'] === '') fail(400, 'invalid', 'Fill in dates and reason');
        rec_put('leave', uid('lv'), $o); out(['ok' => true]);
    }

    /* ================= staff: data ================= */
    case 'dash': {
        $u = need_staff(); $role = $u['role'];
        $students = rec_rows('student'); $strength = []; foreach ($students as $s) $strength[$s['cls']] = ($strength[$s['cls']] ?? 0) + 1;
        $days = []; foreach (q('SELECT day, SUM(st=\'P\') p, COUNT(*) n FROM att WHERE day>=? GROUP BY day ORDER BY day DESC LIMIT 7', [date('Y-m-d', strtotime('-14 days'))])->fetchAll() as $x) $days[] = ['day' => $x['day'], 'pct' => $x['n'] ? round(100 * $x['p'] / $x['n']) : 0, 'n' => (int)$x['n']];
        $o = ['ok' => true, 'students' => count($students), 'teachers' => (int)q("SELECT COUNT(*) FROM rec WHERE k='teacher'")->fetchColumn(), 'strength' => $strength, 'att' => array_reverse($days),
            'pendingAdm' => count(array_filter(rec_rows('admission'), function ($a) { return ($a['status'] ?? '') === 'pending'; })),
            'unreadMsg' => count(array_filter(rec_rows('message'), function ($m) { return empty($m['read']); })),
            'pendingLeave' => count(array_filter(rec_rows('leave'), function ($m) { return ($m['status'] ?? '') === 'pending'; }))];
        if ($role !== 'teacher') {
            $due = 0; $dueN = 0; $mon = [];
            foreach (rec_rows('fee') as $f) {
                if (empty($f['paid'])) { $due += (float)$f['amount']; $dueN++; } else { $m = substr($f['paidOn'] ?? '', 0, 7); if ($m) $mon[$m] = ($mon[$m] ?? 0) + (float)$f['amount']; }
            }
            $exp = []; foreach (rec_rows('expense') as $e) { $m = substr($e['date'] ?? '', 0, 7); if ($m) $exp[$m] = ($exp[$m] ?? 0) + (float)$e['amount']; }
            ksort($mon); ksort($exp);
            $o['due'] = $due; $o['dueN'] = $dueN; $o['income'] = $mon; $o['expense'] = $exp;
        }
        out($o);
    }
    case 'list': {
        $u = need_staff(); $k = str($_GET['k'] ?? '', 12);
        if (!in_array($k, perms($u['role'])['r'], true)) fail(403, 'forbidden', 'Not allowed');
        $w = ''; $a = [];
        if (!empty($_GET['cls'])) { $w .= ' AND cls=?'; $a[] = str($_GET['cls'], 8); }
        if (!empty($_GET['sid'])) { $w .= ' AND sid=?'; $a[] = str($_GET['sid'], 24); }
        out(['ok' => true, 'rows' => rec_rows($k, $w, $a, 't DESC', 5000)]);
    }
    case 'save': {
        $u = need_staff(); no_demo($u);
        $k = str($b['k'] ?? '', 12); $id = str($b['id'] ?? '', 24); $d = is_array($b['d'] ?? null) ? $b['d'] : [];
        if (!in_array($k, KINDS, true) || !in_array($k, perms($u['role'])['w'], true)) fail(403, 'forbidden', 'Not allowed');
        if (strlen(enc($d)) > 120000) fail(413, 'too-large', 'Too large');
        $creds = null;
        if ($id) { $old = rec_get($id); if (!$old || $old['_k'] !== $k) fail(404, 'nf', 'Record not found'); }
        else $id = uid(substr($k, 0, 2));
        if ($k === 'student') {
            if (trim((string)($d['name'] ?? '')) === '' || (string)($d['cls'] ?? '') === '') fail(400, 'invalid', 'Name and class are required');
            if (empty($b['id'])) { if (empty($d['roll'])) $d['roll'] = next_roll((string)$d['cls']); $d['status'] = $d['status'] ?? 'active'; $d['admitted'] = $d['admitted'] ?? date('Y-m-d'); }
        }
        if ($k === 'result' && empty($d['sid'])) fail(400, 'invalid', 'sid required');
        if ($k === 'fee') { $d['amount'] = (float)($d['amount'] ?? 0); if (empty($d['sid'])) fail(400, 'invalid', 'Choose a student'); }
        rec_put($k, $id, $d);
        if ($k === 'student') {
            if (empty($b['id'])) $creds = make_accounts($id, (string)$d['name']);
            else q("UPDATE users SET name=? WHERE ref=? AND role='student'", [str($d['name'], 120), $id]);
        }
        if (!in_array($k, ['result', 'msg', 'log'], true)) log_act($u, ($b['id'] ?? '') ? "Edited $k" : "Added $k: " . str($d['title'] ?? ($d['name'] ?? ''), 60));
        out(['ok' => true, 'id' => $id, 'creds' => $creds]);
    }
    case 'del': {
        $u = need_staff(); no_demo($u);
        $id = str($b['id'] ?? '', 24); $x = rec_get($id);
        if (!$x || !in_array($x['_k'], perms($u['role'])['w'], true)) fail(403, 'forbidden', 'Not allowed');
        if ($x['_k'] === 'student') { q('DELETE FROM users WHERE ref=?', [$id]); q('DELETE FROM att WHERE sid=?', [$id]); q("DELETE FROM rec WHERE sid=? AND k IN ('fee','result','leave','msg')", [$id]); }
        q('DELETE FROM rec WHERE id=?', [$id]); log_act($u, 'Deleted ' . $x['_k']); out(['ok' => true]);
    }
    case 'att_get': {
        $u = need_login(['admin', 'teacher']); $cls = str($_GET['cls'] ?? '', 8); $day = str($_GET['day'] ?? '', 10);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) fail(400, 'invalid', 'Bad date');
        $m = []; foreach (q('SELECT sid,st FROM att WHERE day=? AND cls=?', [$day, $cls])->fetchAll() as $x) $m[$x['sid']] = $x['st'];
        $rows = rec_rows('student', ' AND cls=?', [$cls], 'id ASC'); usort($rows, function ($a, $b) { return (int)$a['roll'] - (int)$b['roll']; });
        out(['ok' => true, 'rows' => array_map(function ($s) use ($m) { return ['id' => $s['id'], 'name' => $s['name'], 'roll' => $s['roll'], 'sec' => $s['sec'] ?? '', 'st' => $m[$s['id']] ?? '']; }, $rows), 'taken' => count($m) > 0]);
    }
    case 'att_set': {
        $u = need_login(['admin', 'teacher']); no_demo($u);
        $cls = str($b['cls'] ?? '', 8); $day = str($b['day'] ?? '', 10); $items = is_array($b['items'] ?? null) ? $b['items'] : [];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) fail(400, 'invalid', 'Bad date');
        $ok = []; foreach (rec_rows('student', ' AND cls=?', [$cls]) as $s) $ok[$s['id']] = 1;
        $n = 0;
        foreach ($items as $sid => $st) { if (!isset($ok[$sid]) || !in_array($st, ['P', 'A', 'L'], true)) continue; q('INSERT INTO att (day,sid,cls,st) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE st=VALUES(st)', [$day, $sid, $cls, $st]); $n++; }
        log_act($u, "Attendance Class $cls $day ($n)"); out(['ok' => true, 'n' => $n]);
    }
    case 'att_report': {
        $u = need_login(['admin', 'teacher']); $cls = str($_GET['cls'] ?? '', 8); $mon = str($_GET['month'] ?? '', 7);
        if (!preg_match('/^\d{4}-\d{2}$/', $mon)) fail(400, 'invalid', 'Bad month');
        $c = []; foreach (q('SELECT sid, SUM(st=\'P\') p, SUM(st=\'A\') a, SUM(st=\'L\') l FROM att WHERE cls=? AND day LIKE ? GROUP BY sid', [$cls, $mon . '%'])->fetchAll() as $x) $c[$x['sid']] = $x;
        $days = (int)q('SELECT COUNT(DISTINCT day) FROM att WHERE cls=? AND day LIKE ?', [$cls, $mon . '%'])->fetchColumn();
        $rows = rec_rows('student', ' AND cls=?', [$cls]); usort($rows, function ($a, $b) { return (int)$a['roll'] - (int)$b['roll']; });
        out(['ok' => true, 'days' => $days, 'rows' => array_map(function ($s) use ($c) { $x = $c[$s['id']] ?? ['p' => 0, 'a' => 0, 'l' => 0]; return ['id' => $s['id'], 'name' => $s['name'], 'roll' => $s['roll'], 'p' => (int)$x['p'], 'a' => (int)$x['a'], 'l' => (int)$x['l']]; }, $rows)]);
    }
    case 'adm_decide': {
        $u = need_login(['admin']); no_demo($u);
        $id = str($b['id'] ?? '', 24); $status = str($b['status'] ?? '', 12); $note = str($b['note'] ?? '', 300);
        if (!in_array($status, ['pending', 'accepted', 'rejected', 'waitlist'], true)) fail(400, 'invalid', 'Bad status');
        $a = rec_get($id); if (!$a || $a['_k'] !== 'admission') fail(404, 'nf', 'Application not found');
        $creds = null;
        if ($status === 'accepted' && empty($a['studentId'])) {
            $sid = uid('st');
            $d = ['name' => $a['name'], 'name_bn' => $a['name_bn'] ?? '', 'cls' => $a['cls'], 'sec' => 'A', 'roll' => next_roll($a['cls']), 'gender' => $a['gender'], 'dob' => $a['dob'], 'blood' => $a['blood'] ?? '', 'religion' => $a['religion'] ?? '',
                'father' => $a['father'], 'mother' => $a['mother'], 'phone' => $a['fphone'], 'addr' => $a['present'], 'status' => 'active', 'admitted' => date('Y-m-d'), 'fromAdm' => $a['ref']];
            rec_put('student', $sid, $d); $creds = make_accounts($sid, $a['name']); $a['studentId'] = $sid; $creds['roll'] = $d['roll']; $creds['cls'] = $d['cls'];
        }
        $a['status'] = $status; $a['note_admin'] = $note; rec_put('admission', $id, $a);
        log_act($u, "Admission {$a['ref']} → $status"); out(['ok' => true, 'creds' => $creds]);
    }
    case 'fee_collect': {
        $u = need_login(['admin', 'accountant']); no_demo($u);
        $f = rec_get(str($b['id'] ?? '', 24)); if (!$f || $f['_k'] !== 'fee') fail(404, 'nf', 'Fee not found');
        $f['paid'] = 1; $f['paidOn'] = date('Y-m-d'); $f['method'] = str($b['method'] ?? 'Cash', 20); $f['trx'] = str($b['trx'] ?? '', 40);
        $f['receipt'] = 'R-' . date('ymd') . '-' . strtoupper(substr(md5($f['id']), 0, 4));
        rec_put('fee', $f['id'], $f); log_act($u, 'Fee collected ' . $f['title'] . ' ৳' . $f['amount']); out(['ok' => true, 'receipt' => $f['receipt']]);
    }
    case 'fees_gen': {
        $u = need_login(['admin', 'accountant']); no_demo($u);
        $cls = str($b['cls'] ?? '', 8); $title = str($b['title'] ?? '', 80); $amt = (float)($b['amount'] ?? 0); $due = str($b['due'] ?? '', 10);
        if ($title === '' || $amt <= 0) fail(400, 'invalid', 'Enter fee title and amount');
        $have = []; foreach (rec_rows('fee') as $f) $have[$f['sid'] . '|' . $f['title']] = 1;
        $n = 0; foreach (rec_rows('student', $cls === '' ? '' : ' AND cls=?', $cls === '' ? [] : [$cls]) as $s) {
            if (($s['status'] ?? 'active') !== 'active' || isset($have[$s['id'] . '|' . $title])) continue;
            rec_put('fee', uid('fe'), ['sid' => $s['id'], 'cls' => $s['cls'], 'name' => $s['name'], 'title' => $title, 'amount' => $amt, 'due' => $due, 'paid' => 0]); $n++;
        }
        log_act($u, "Generated $n fees: $title"); out(['ok' => true, 'n' => $n]);
    }
    case 'promote': {
        $u = need_login(['admin']); no_demo($u);
        $from = str($b['from'] ?? '', 8); $to = str($b['to'] ?? '', 8); if ($from === '' || $to === '') fail(400, 'invalid', 'Choose classes');
        $n = 0; foreach (rec_rows('student', ' AND cls=?', [$from]) as $s) { $s['cls'] = $to; $s['roll'] = next_roll($to); rec_put('student', $s['id'], $s); $n++; }
        log_act($u, "Promoted $n students $from → $to"); out(['ok' => true, 'n' => $n]);
    }
    case 'users': { $u = need_login(['admin']); out(['ok' => true, 'rows' => q('SELECT id,login,role,ref,name,active,demo FROM users ORDER BY role,login LIMIT 3000')->fetchAll()]); }
    case 'user_pw': {
        $u = need_login(['admin']); no_demo($u);
        $p = (string)($b['pass'] ?? ''); if (strlen($p) < 6) fail(400, 'invalid', 'Password must be at least 6 characters');
        q('UPDATE users SET pass_hash=? WHERE id=? AND demo=0', [password_hash($p, PASSWORD_DEFAULT), str($b['id'] ?? '', 20)]); log_act($u, 'Password reset'); out(['ok' => true]);
    }
    case 'user_active': { $u = need_login(['admin']); no_demo($u); q('UPDATE users SET active=? WHERE id=? AND id<>? AND demo=0', [empty($b['active']) ? 0 : 1, str($b['id'] ?? '', 20), $u['id']]); out(['ok' => true]); }
    case 'user_create': {
        $u = need_login(['admin']); no_demo($u);
        $login = strtolower(trim(str($b['login'] ?? '', 80))); $role = str($b['role'] ?? '', 12); $p = (string)($b['pass'] ?? '');
        if (!in_array($role, ['admin', 'accountant', 'teacher'], true) || $login === '' || strlen($p) < 6 || trim(str($b['name'] ?? '', 120)) === '') fail(400, 'invalid', 'Fill name, login, role and a password of 6+ characters');
        if (q('SELECT 1 FROM users WHERE LOWER(login)=?', [$login])->fetch()) fail(409, 'exists', 'This login already exists');
        q('INSERT INTO users (id,login,pass_hash,role,ref,name,active,demo,t) VALUES (?,?,?,?,?,?,1,0,?)', [uid('u'), $login, password_hash($p, PASSWORD_DEFAULT), $role, str($b['ref'] ?? '', 24), str($b['name'], 120), now_ms()]);
        log_act($u, "Created $role login $login"); out(['ok' => true]);
    }
    case 'settings_save': {
        $u = need_login(['admin']); no_demo($u);
        $d = $b['d'] ?? null; if (!is_array($d) || strlen(enc($d)) > 180000) fail(400, 'invalid', 'Bad settings');
        site_put('settings', $d);
        log_act($u, 'Settings saved'); out(['ok' => true]);
    }
    case 'demo_set': { $u = need_login(['admin']); no_demo($u); site_put('demo', ['on' => !empty($b['on'])]); log_act($u, 'Demo mode ' . (!empty($b['on']) ? 'ON' : 'OFF')); out(['ok' => true]); }
    case 'backup': {
        $u = need_login(['admin']); no_demo($u);
        $all = []; foreach (q('SELECT id,k,cls,sid,d FROM rec')->fetchAll() as $x) { $all[] = ['id' => $x['id'], 'k' => $x['k'], 'd' => dec($x['d'])]; }
        out(['ok' => true, 'at' => date('c'), 'settings' => site_get('settings'), 'records' => $all, 'attendance' => q('SELECT day,sid,cls,st FROM att')->fetchAll()]);
    }
    }
    fail(404, 'no-route', 'Unknown route');
}
