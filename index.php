<?php

$valid_passwords = array (getenv('ADMINER_BASIC_USER') => getenv('ADMINER_BASIC_PASS_MD5'));
$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && (md5($pass) == $valid_passwords[$user]);

if (!$validated) {
  header('WWW-Authenticate: Basic realm="Adminer Web UI"');
  header('HTTP/1.0 401 Unauthorized');
  die ("Not authorized");
}

function adminer_object() {
    // required to run any plugin
    include_once "./plugins/plugin.php";
    
    // autoloader
    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }
    
    $plugins = array(
        // specify enabled plugins here
         new AdminerEditForeign,
         new AdminerTablesFilter,
         new AdminerEnumTypes,
         new AdminerEnumOption,
         new AdminerLoginServers([
            getenv('ADMINER_DATABASE_URL1') => getenv('ADMINER_DATABASE_NAME1'),
            getenv('ADMINER_DATABASE_URL2') => getenv('ADMINER_DATABASE_NAME2'),
        ]),
    );
    
    class AdminerCustomization extends AdminerPlugin {
        function name() {
            return "Adminer - Data Entry";
        }
    }
    return new AdminerCustomization($plugins);
}

// include original Adminer or Adminer Editor
include "./adminer-4.8.1-mysql.php";
?>
