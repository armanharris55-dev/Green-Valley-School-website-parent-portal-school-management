<?php
/* Installer logic used by setup.php */
if (!defined('APP')) { http_response_code(403); exit; }
require_once __DIR__ . '/seed_data.php';

function install_run($c, $owner, $demo) {
    try {
        $dsn = 'mysql:host=' . $c['db_host'] . ';port=' . (int)$c['db_port'] . ';charset=utf8mb4';
        $root = new PDO($dsn, $c['db_user'], $c['db_pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        if (!preg_match('/^[A-Za-z0-9_]+$/', $c['db_name'])) return ['ok' => false, 'msg' => 'Database name may only contain letters, numbers and underscore.'];
        try { $root->exec('CREATE DATABASE IF NOT EXISTS `' . $c['db_name'] . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'); } catch (Exception $e) { /* shared hosting: DB already exists */ }
    } catch (Exception $e) { return ['ok' => false, 'msg' => 'Could not connect to MySQL: ' . $e->getMessage()]; }
    $conf = "<?php\nreturn " . var_export(['db_host' => $c['db_host'], 'db_name' => $c['db_name'], 'db_user' => $c['db_user'], 'db_pass' => $c['db_pass'], 'db_port' => (int)$c['db_port'], 'trust_cloudflare' => !empty($c['trust_cloudflare']), 'timezone' => 'Asia/Dhaka'], true) . ";\n";
    if (@file_put_contents(__DIR__ . '/config.php', $conf) === false) return ['ok' => false, 'msg' => 'Could not write api/config.php — make the api/ folder writable and retry.'];
    @chmod(__DIR__ . '/config.php', 0640);
    try {
        $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
        foreach (array_filter(array_map('trim', explode(';', preg_replace('/^--.*$/m', '', $sql)))) as $stmt) db()->exec($stmt);
        if (q('SELECT COUNT(*) FROM users')->fetchColumn() > 0) { @unlink(__DIR__ . '/config.php'); return ['ok' => false, 'msg' => 'This database already has accounts. Use an empty database.']; }
        $id = uid('u');
        q('INSERT INTO users (id,login,pass_hash,role,ref,name,active,demo,t) VALUES (?,?,?,?,?,?,1,0,?)', [$id, strtolower($owner['email']), password_hash($owner['pass'], PASSWORD_DEFAULT), 'admin', '', $owner['name'], now_ms()]);
        seed_base();
        if ($demo) seed_demo($id); else site_put('demo', ['on' => false]);
        @file_put_contents(__DIR__ . '/.installed', date('c'));
    } catch (Exception $e) { @unlink(__DIR__ . '/config.php'); return ['ok' => false, 'msg' => 'Install failed: ' . $e->getMessage()]; }
    return ['ok' => true, 'msg' => 'Installed'];
}
