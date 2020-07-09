<?php

$valid_passwords = array (getenv('CUSTOMIZE_BASIC_USER') => getenv('CUSTOMIZE_BASIC_AUTH'));
$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

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
         new AdminerEnumOption
    );
    
    class AdminerCustomization extends AdminerPlugin {
        function name() {
            return "Adminer - Data Entry";
        }

        /** Print login form
        * @return null
        */
        function loginForm() {
            global $drivers;
            $fixedDriver = getenv('CUSTOMIZE_DATABASE_DRIVER');
            $dbServer = getenv('CUSTOMIZE_DATABASE_SERVER');
            echo "<table cellspacing='0' class='layout'>\n";
            echo $this->loginFormField('driver', '<tr><th>' . lang('System') . '<td>', html_select("auth[driver]", $drivers, $fixedDriver, "loginDriver(this);") . "\n");
            echo $this->loginFormField('server', '<tr><th>' . lang('Server') . '<td>', '<input type="password" name="auth[server]" value="' . h($dbServer) . '" title="hostname[:port]" placeholder="..." autocapitalize="off">' . "\n");
            echo $this->loginFormField('username', '<tr><th>' . lang('Username') . '<td>', '<input name="auth[username]" id="username" value="' . h($_GET["username"]) . '" autocomplete="username" autocapitalize="off">' . script("focus(qs('#username')); qs('#username').form['auth[driver]'].onchange();"));
            echo $this->loginFormField('password', '<tr><th>' . lang('Password') . '<td>', '<input type="password" name="auth[password]" autocomplete="current-password">' . "\n");
            echo $this->loginFormField('db', '<tr><th>' . lang('Database') . '<td>', '<input name="auth[db]" value="' . h($_GET["db"]) . '" autocapitalize="off">' . "\n");
            echo "</table>\n";
            echo "<p><input type='submit' value='" . lang('Login') . "'>\n";
            echo checkbox("auth[permanent]", 1, $_COOKIE["adminer_permanent"], lang('Permanent login')) . "\n";
        }
        function database() {
           // database name, will be escaped by Adminer
           return DB;
        }
    }
    return new AdminerCustomization($plugins);
}

// include original Adminer or Adminer Editor
include "./adminer-4.7.7.php";
?>
