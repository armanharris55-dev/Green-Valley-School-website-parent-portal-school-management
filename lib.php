<?php
/* Green Valley School — backend helpers: database, session, JSON I/O, rate limiting. PHP 7.4+ */
if (!defined('APP')) { http_response_code(403); exit; }

const KINDS = ['notice','news','event','gallery','teacher','student','fee','homework','routine','exam','result','leave','admission','message','msg','expense','log'];

function cfg() {
    static $c = null;
    if ($c === null) {
        $f = __DIR__ . '/config.php';
        if (!is_file($f)) fail(503, 'not-installed', 'The site is not installed yet. Open /setup.php');
        $c = require $f;
        if (!empty($c['timezone'])) date_default_timezone_set($c['timezone']);
    }
    return $c;
}
function db() {
    static $pdo = null;
    if ($pdo === null) {
        $c = cfg();
        try {
            $pdo = new PDO('mysql:host=' . $c['db_host'] . ';port=' . (int)($c['db_port'] ?? 3306) . ';dbname=' . $c['db_name'] . ';charset=utf8mb4',
                $c['db_user'], $c['db_pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]);
        } catch (Exception $e) { error_log('DB connect: ' . $e->getMessage()); fail(503, 'db', 'Database connection failed'); }
    }
    return $pdo;
}
function q($sql, $args = []) { $s = db()->prepare($sql); $s->execute($args); return $s; }
function now_ms() { return (int)round(microtime(true) * 1000); }

function out($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
    exit;
}
function fail($code, $err, $msg) { out(['ok' => false, 'code' => $err, 'msg' => $msg], $code); }
function body() {
    static $b = null;
    if ($b === null) {
        $raw = file_get_contents('php://input', false, null, 0, 2000000);
        if (strlen($raw) >= 1999999) fail(413, 'too-large', 'Request too large');
        $b = $raw === '' ? [] : json_decode($raw, true);
        if (!is_array($b)) fail(400, 'bad-json', 'Invalid JSON');
    }
    return $b;
}
function dec($s) { $v = json_decode((string)$s, true); return $v === null ? [] : $v; }
function enc($v) { return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); }
function str($v, $max = 200) { $v = is_scalar($v) ? (string)$v : ''; $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $v); return function_exists('mb_substr') ? mb_substr($v, 0, $max, 'UTF-8') : substr($v, 0, $max); }
function uid($p) { return $p . bin2hex(random_bytes(6)); }

/* ---------- session / users ---------- */
function start_session() {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_name('gvsid');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
    ini_set('session.gc_maxlifetime', '2592000');
    session_start();
    if (!empty($_SESSION['uid'])) setcookie(session_name(), session_id(), ['expires' => time() + 2592000, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
}
function demo_on() { $r = q("SELECT d FROM site WHERE name='demo'")->fetchColumn(); return $r !== false && !empty(dec($r)['on']); }
function me() {
    static $u = false;
    if ($u !== false) return $u;
    $u = null;
    if (empty($_SESSION['uid'])) return null;
    $r = q('SELECT id,login,role,ref,name,active,demo FROM users WHERE id=?', [$_SESSION['uid']])->fetch();
    if (!$r || !$r['active'] || ($r['demo'] && !demo_on())) { unset($_SESSION['uid']); return null; }
    if ($r['role'] !== 'student' && $r['role'] !== 'parent') {            // staff: 12 h idle timeout
        if (time() - (int)($_SESSION['t'] ?? 0) > 43200) { unset($_SESSION['uid']); return null; }
        $_SESSION['t'] = time();
    }
    return $u = $r;
}
function need_login($roles = null) {
    $u = me(); if (!$u) fail(401, 'auth/login-required', 'Please sign in');
    if ($roles && !in_array($u['role'], $roles, true)) fail(403, 'forbidden', 'Not allowed for your role');
    return $u;
}
function staff_roles() { return ['admin', 'accountant', 'teacher']; }
function need_staff() { return need_login(staff_roles()); }
function perms($role) {
    switch ($role) {
        case 'admin': return ['r' => KINDS, 'w' => KINDS];
        case 'accountant': return ['r' => ['student', 'fee', 'expense', 'admission', 'notice', 'news', 'event', 'teacher', 'exam', 'log'], 'w' => ['fee', 'expense']];
        case 'teacher': return ['r' => ['student', 'teacher', 'notice', 'news', 'event', 'gallery', 'routine', 'exam', 'result', 'homework', 'leave', 'msg'], 'w' => ['result', 'homework', 'msg']];
    }
    return ['r' => [], 'w' => []];
}
/* demo accounts can look at everything but nothing is saved */
function no_demo($u) { if (!empty($u['demo'])) out(['ok' => true, 'demo' => true]); }

/* ---------- misc ---------- */
function client_ip() {
    $c = cfg();
    if (!empty($c['trust_cloudflare']) && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) return substr($_SERVER['HTTP_CF_CONNECTING_IP'], 0, 45);
    return substr($_SERVER['REMOTE_ADDR'] ?? '0', 0, 45);
}
function rate($key, $max, $win) {
    $k = substr($key . ':' . client_ip(), 0, 80); $t = time();
    q('DELETE FROM attempts WHERE t < ?', [$t - 86400]);
    if ((int)q('SELECT COUNT(*) FROM attempts WHERE k=? AND t>?', [$k, $t - $win])->fetchColumn() >= $max) return false;
    q('INSERT INTO attempts (k,t) VALUES (?,?)', [$k, $t]);
    return true;
}
function need_rate($key, $max, $win) { if (!rate($key, $max, $win)) fail(429, 'too-many', 'Too many attempts. Please wait a few minutes and try again.'); }
function clean_phone($p) { $p = preg_replace('/[\s\-+]/', '', (string)$p); if (strpos($p, '88') === 0) $p = substr($p, 2); return $p; }
function valid_phone($p) { return (bool)preg_match('/^01[3-9]\d{8}$/', $p); }

function site_get($name, $def = []) { $r = q('SELECT d FROM site WHERE name=?', [$name])->fetchColumn(); return $r === false ? $def : dec($r); }
function site_put($name, $d) { q('INSERT INTO site (name,d,t) VALUES (?,?,?) ON DUPLICATE KEY UPDATE d=VALUES(d),t=VALUES(t)', [$name, enc($d), now_ms()]); }
function rec_rows($k, $where = '', $args = [], $order = 't DESC', $limit = 5000) {
    $rows = q('SELECT id,cls,sid,d,t FROM rec WHERE k=?' . $where . ' ORDER BY ' . $order . ' LIMIT ' . (int)$limit, array_merge([$k], $args))->fetchAll();
    $o = [];
    foreach ($rows as $r) { $d = dec($r['d']); $d['id'] = $r['id']; $d['_t'] = (int)$r['t']; $o[] = $d; }
    return $o;
}
function rec_get($id) { $r = q('SELECT id,k,cls,sid,d,t FROM rec WHERE id=?', [$id])->fetch(); if (!$r) return null; $d = dec($r['d']); $d['id'] = $r['id']; $d['_k'] = $r['k']; return $d; }
function rec_put($k, $id, $d) {
    unset($d['id'], $d['_t'], $d['_k']);
    q('INSERT INTO rec (id,k,cls,sid,d,t) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE cls=VALUES(cls),sid=VALUES(sid),d=VALUES(d),t=VALUES(t)',
        [$id, $k, str($d['cls'] ?? '', 8), str($d['sid'] ?? '', 24), enc($d), now_ms()]);
}
function log_act($u, $txt) { rec_put('log', uid('lg'), ['by' => $u['name'] ?? '-', 'role' => $u['role'] ?? '', 'text' => str($txt, 200), 'date' => date('Y-m-d H:i')]); }
function next_roll($cls) { $m = 0; foreach (rec_rows('student', ' AND cls=?', [$cls]) as $s) $m = max($m, (int)($s['roll'] ?? 0)); return $m + 1; }
function next_login($prefix) {
    $n = (int)q("SELECT COUNT(*) FROM users WHERE login LIKE ?", [$prefix . '%'])->fetchColumn() + 1001;
    while (q('SELECT 1 FROM users WHERE login=?', [$prefix . $n])->fetch()) $n++;
    return $prefix . $n;
}
function rand_pass() { $a = 'abcdefghjkmnpqrstuvwxyz23456789'; $p = ''; for ($i = 0; $i < 8; $i++) $p .= $a[random_int(0, strlen($a) - 1)]; return $p; }
/* creates the student login + parent login for a student record; returns credentials (shown once) */
function make_accounts($sid, $name, $pass = null, $ppass = null) {
    $sl = next_login('S'); $pl = 'P' . substr($sl, 1);
    $sp = $pass ?: rand_pass(); $pp = $ppass ?: rand_pass();
    q('INSERT INTO users (id,login,pass_hash,role,ref,name,active,demo,t) VALUES (?,?,?,?,?,?,1,0,?)', [uid('u'), $sl, password_hash($sp, PASSWORD_DEFAULT), 'student', $sid, $name, now_ms()]);
    q('INSERT INTO users (id,login,pass_hash,role,ref,name,active,demo,t) VALUES (?,?,?,?,?,?,1,0,?)', [uid('u'), $pl, password_hash($pp, PASSWORD_DEFAULT), 'parent', $sid, 'Parent of ' . $name, now_ms()]);
    return ['student' => ['login' => $sl, 'pass' => $sp], 'parent' => ['login' => $pl, 'pass' => $pp]];
}
