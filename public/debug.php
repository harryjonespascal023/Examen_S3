<?php
/**
 * Fichier de diagnostic pour identifier les problèmes sur le serveur
 * Accès: http://172.16.7.131/ETU004094/Examen_S3/debug.php
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Debug Info</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#f5f5f5;}";
echo ".ok{color:green;font-weight:bold;}.error{color:red;font-weight:bold;}";
echo "h2{background:#333;color:white;padding:10px;margin-top:20px;}";
echo "pre{background:white;padding:10px;border:1px solid #ddd;overflow:auto;}</style></head><body>";

echo "<h1>🔍 Diagnostic Serveur - Examen_S3</h1>";
echo "<hr>";

// 1. Version PHP
echo "<h2>1. Version PHP</h2>";
echo "<p>Version: <strong>" . PHP_VERSION . "</strong></p>";
echo (version_compare(PHP_VERSION, '7.4.0', '>='))
  ? '<p class="ok">✅ PHP >= 7.4 (OK)</p>'
  : '<p class="error">❌ PHP < 7.4 (Mise à jour requise)</p>';

// 2. Extensions requises
echo "<h2>2. Extensions PHP Requises</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];
foreach ($required_extensions as $ext) {
  $loaded = extension_loaded($ext);
  echo $loaded
    ? "<p class='ok'>✅ $ext</p>"
    : "<p class='error'>❌ $ext (MANQUANT)</p>";
}

// 3. Fichiers critiques
echo "<h2>3. Fichiers Critiques</h2>";
$critical_files = [
  '../vendor/autoload.php' => 'Composer autoload',
  '../app/config/config.php' => 'Configuration',
  '../app/config/bootstrap.php' => 'Bootstrap',
  '../app/config/services.php' => 'Services',
  '../app/config/routes.php' => 'Routes',
  '.htaccess' => 'Htaccess'
];
foreach ($critical_files as $file => $description) {
  $exists = file_exists(__DIR__ . '/' . $file);
  echo $exists
    ? "<p class='ok'>✅ $description ($file)</p>"
    : "<p class='error'>❌ $description ($file) MANQUANT</p>";
}

// 4. Permissions dossiers
echo "<h2>4. Permissions Dossiers</h2>";
$dirs = [
  '../app/log' => 'Logs (doit être écrivable)'
];
foreach ($dirs as $dir => $desc) {
  $path = __DIR__ . '/' . $dir;
  if (file_exists($path)) {
    $writable = is_writable($path);
    $perms = substr(sprintf('%o', fileperms($path)), -4);
    echo $writable
      ? "<p class='ok'>✅ $desc - Permissions: $perms</p>"
      : "<p class='error'>❌ $desc - Permissions: $perms (NON ÉCRIVABLE)</p>";
  } else {
    echo "<p class='error'>❌ $desc - DOSSIER N'EXISTE PAS</p>";
  }
}

// 5. Test connexion DB
echo "<h2>5. Test Connexion Base de Données</h2>";
try {
  $config_file = __DIR__ . '/../app/config/config.php';
  if (file_exists($config_file)) {
    $config = require($config_file);

    echo "<p>Host: <strong>" . htmlspecialchars($config['database']['host']) . "</strong></p>";
    echo "<p>Database: <strong>" . htmlspecialchars($config['database']['dbname']) . "</strong></p>";
    echo "<p>User: <strong>" . htmlspecialchars($config['database']['user']) . "</strong></p>";

    $dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $config['database']['user'], $config['database']['password'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "<p class='ok'>✅ Connexion base de données réussie!</p>";

    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM BNR_ville");
    $result = $stmt->fetch();
    echo "<p class='ok'>✅ Nombre de villes: " . $result['count'] . "</p>";

  } else {
    echo "<p class='error'>❌ Fichier config.php introuvable</p>";
  }
} catch (PDOException $e) {
  echo "<p class='error'>❌ Erreur connexion DB: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
  echo "<p class='error'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 6. Variables serveur importantes
echo "<h2>6. Variables Serveur</h2>";
echo "<pre>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "SERVER_SOFTWARE: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "\n";
echo "</pre>";

// 7. Test mod_rewrite
echo "<h2>7. Apache mod_rewrite</h2>";
if (function_exists('apache_get_modules')) {
  $modules = apache_get_modules();
  echo in_array('mod_rewrite', $modules)
    ? "<p class='ok'>✅ mod_rewrite activé</p>"
    : "<p class='error'>❌ mod_rewrite désactivé ou non détectable</p>";
} else {
  echo "<p>⚠️ Impossible de détecter (fonction apache_get_modules non disponible)</p>";
}

// 8. Test .htaccess
echo "<h2>8. Configuration .htaccess</h2>";
if (file_exists(__DIR__ . '/.htaccess')) {
  echo "<pre>" . htmlspecialchars(file_get_contents(__DIR__ . '/.htaccess')) . "</pre>";
} else {
  echo "<p class='error'>❌ Fichier .htaccess manquant!</p>";
}

echo "<hr><p><strong>Diagnostic terminé!</strong></p>";
echo "<p><a href='index.php'>← Retour à l'application</a></p>";
echo "</body></html>";
