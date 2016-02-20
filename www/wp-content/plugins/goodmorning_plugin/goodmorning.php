<?php
/**
 * @package goodmorning_plugin
 */
/*
Plugin Name: The Good Morning App Control Plugin
Plugin URI: http://www.luehrsen-heinrich.de
Description: The plugin that provides the data structures needed for the Good Morning Page.
Version: 1
Author: Hendrik Luehrsen
Author URI: http://www.luehrsen-heinrich.de
License: none
Text Domain: goodmorning
Domain Path: /lang
*/
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
define( 'GM__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GM__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GM__PLUGIN_FILE',  __FILE__  );
define( 'GM__LANG',  'goodmorning'  );

require_once( GM__PLUGIN_DIR . "/inc/goodmorning.core.php" ); // The core class of the plugin
require_once( GM__PLUGIN_DIR . "/inc/goodmorning.admin.php" ); // The core class of the plugin
require_once( GM__PLUGIN_DIR . "/inc/goodmorning.metaboxes.php" ); // The core class of the plugin
require_once( GM__PLUGIN_DIR . "/inc/goodmorning.crawler.php" ); // The core class of the plugin
require_once( GM__PLUGIN_DIR . "/inc/goodmorning.rest.php" ); // The core class of the plugin

if(is_admin()){
	$goodmorning_plugin = new GOODMORNING_ADMIN();
} else {
	$goodmorning_plugin = new GOODMORNING_CORE();
}

$morning_crawler = new GOODMORNING_CRAWLER();
$morning_rest = new GOODMORNING_REST();

// The activation hook
register_activation_hook( __FILE__, array($goodmorning_plugin, "onInstall") );

register_deactivation_hook(__FILE__, array($goodmorning_plugin, "onUninstall") );
