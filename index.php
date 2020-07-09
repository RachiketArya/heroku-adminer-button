<?php
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
//          new AdminerTinymce,
         new AdminerTablesFilter,
         new AdminerEnumTypes,
         new AdminerEnumOption
    );
    
    class AdminerCustomization extends AdminerPlugin {
         function database() {
           // database name, will be escaped by Adminer
           return getenv('CUSTOMIZE_DATABASE_NAME');
         }
    }
    return new AdminerCustomization($plugins);
}

// include original Adminer or Adminer Editor
include "./adminer-4.7.7.php";
?>
