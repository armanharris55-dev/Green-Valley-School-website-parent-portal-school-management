<?php
/* One-time web installer. Open https://your-site/setup.php, fill the form, then DELETE this file. */
define('APP', 1);
require __DIR__ . '/api/lib.php';
require __DIR__ . '/api/install_lib.php';
session_start();
if (is_file(__DIR__ . '/api/.installed')) { http_response_code(403); echo '<!doctype html><meta charset="utf-8"><body style="font-family:sans-serif;padding:40px"><h2>Already installed</h2><p>Delete <code>setup.php</code> from your server. To reinstall, delete <code>api/config.php</code> and <code>api/.installed</code> and use a fresh database.</p><p><a href="admin.html">Go to admin</a></p>'; exit; }
if (empty($_SESSION['tok'])) $_SESSION['tok'] = bin2hex(random_bytes(16));
$err = ''; $done = false; $v = ['db_host' => 'localhost', 'db_name' => 'greenvalley_school', 'db_user' => 'root', 'db_pass' => '', 'name' => 'Owner', 'email' => '', 'demo' => '1'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['db_host', 'db_name', 'db_user', 'db_pass', 'name', 'email'] as $k) $v[$k] = trim((string)($_POST[$k] ?? ''));
    $v['demo'] = isset($_POST['demo']) ? '1' : '';
    $pass = (string)($_POST['pass'] ?? '');
    if (!hash_equals($_SESSION['tok'], (string)($_POST['tok'] ?? ''))) $err = 'Session expired, reload the page.';
    elseif (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $err = 'Enter a valid admin e-mail.';
    elseif (strlen($pass) < 8) $err = 'Admin password must be at least 8 characters.';
    else {
        $r = install_run(['db_host' => $v['db_host'], 'db_name' => $v['db_name'], 'db_user' => $v['db_user'], 'db_pass' => $v['db_pass'], 'db_port' => 3306], ['name' => $v['name'] ?: 'Owner', 'email' => $v['email'], 'pass' => $pass], (bool)$v['demo']);
        if ($r['ok']) $done = true; else $err = $r['msg'];
    }
}
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Install — Green Valley School</title>
<style>body{font:16px/1.5 system-ui,sans-serif;background:#f7f6f0;color:#161616;margin:0;padding:24px}.c{max-width:520px;margin:30px auto;background:#fff;border:2px solid #161616;border-radius:20px;padding:28px;box-shadow:6px 8px 0 #161616}h1{margin:0 0 4px;font-size:26px}h2{font-size:16px;margin:22px 0 8px}label{display:block;font-weight:600;font-size:13px;margin:10px 0 4px}input[type=text],input[type=email],input[type=password]{width:100%;box-sizing:border-box;padding:11px 12px;border:2px solid #161616;border-radius:10px;font:inherit;background:#f7f6f0}button{margin-top:22px;width:100%;padding:14px;background:#c8ff2e;border:2px solid #161616;border-radius:10px;font:800 16px system-ui;cursor:pointer}.e{background:#ffe3dc;border:2px solid #161616;border-radius:10px;padding:10px 12px;margin:14px 0}.ok{background:#e8ffb0;border:2px solid #161616;border-radius:10px;padding:14px}small{color:#666}.r{display:flex;gap:8px;align-items:center;margin-top:14px;font-weight:600}</style></head><body><div class="c">
<h1>Green Valley School — setup</h1><small>Creates the database tables and your admin account.</small>
<?php if ($done): ?>
<div class="ok" style="margin-top:18px"><b>Installed!</b><br>1. <b>Delete <code>setup.php</code></b> from the server now.<br>2. Open <a href="admin.html">admin.html</a> (or <a href="login.html">login.html</a>) and sign in with the e-mail and password you just set.<?php if ($v['demo']): ?><br>3. Demo mode is ON: the sign-in page has one-click demo buttons. Turn it OFF in Admin → Settings before going live.<?php endif; ?></div>
<?php else: ?>
<?php if ($err): ?><div class="e"><?= $h($err) ?></div><?php endif; ?>
<form method="post" autocomplete="off"><input type="hidden" name="tok" value="<?= $h($_SESSION['tok']) ?>">
<h2>1. MySQL database</h2><small>Create an empty database in cPanel / phpMyAdmin first (recommended), then enter its details.</small>
<label>Host</label><input type="text" name="db_host" value="<?= $h($v['db_host']) ?>"><label>Database name</label><input type="text" name="db_name" value="<?= $h($v['db_name']) ?>"><label>User</label><input type="text" name="db_user" value="<?= $h($v['db_user']) ?>"><label>Password</label><input type="password" name="db_pass" value="<?= $h($v['db_pass']) ?>">
<h2>2. Admin account</h2><label>Your name</label><input type="text" name="name" value="<?= $h($v['name']) ?>"><label>Admin e-mail</label><input type="email" name="email" value="<?= $h($v['email']) ?>"><label>Admin password (8+ characters)</label><input type="password" name="pass">
<label class="r"><input type="checkbox" name="demo" value="1" <?= $v['demo'] ? 'checked' : '' ?>> Load demo data (students, results, fees, attendance, admissions) + one-click demo login so you can see how every feature looks</label>
<button type="submit">Install</button></form>
<?php endif; ?></div></body></html>
