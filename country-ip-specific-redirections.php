<?php

/* Plugin Name: Country IP Specific Redirections
  Plugin URI: http://wordpress.org/extend/plugins/country-ip-specific-redirections/
  Description: The powerful geolocation targeting plugin let's you drive away unwanted traffic. The plugin automatically detects the country of your visitors using ipstack.com's API
  Version: 1.0
  Author: Polyphasic Developers (inf@PolyphasicDevs.com)
  Author URI: https://www.PolyphasicDevs.com/
  @copyright  2018 Polyphasic Developers
  License: GPLv2
 */

/* * **** SECURITY ***** */
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

/**
 * SECURITY 
 *  if called directly
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Debugging 
 */
if(0) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


/**
 * Defines a named constant
 */
define('WP_CIP_PATH', plugin_dir_path(__FILE__));
define('WP_CIP_URL', plugin_dir_url(__FILE__));
define('WP_CIP_CLASS', WP_CIP_PATH . 'inc/classes/');
define('WP_CIP_FUNC', WP_CIP_PATH . 'inc/functions/');
define('WP_CIP_VIEWS', WP_CIP_PATH . 'views/');
define('WP_CIP_PREFIX', 'wp_cip_');
define('WP_CIP_SLUG', 'wp-cip-');
define('WP_CIP_MENU', WP_CIP_SLUG . 'admin');
define('WP_CIP_BASENAME', plugin_basename(__FILE__));


/**
 * load base plugin class
 */
require_once WP_CIP_CLASS . 'wpCipRedirectBase.php';
require_once WP_CIP_CLASS . 'http.php';
// mangaWorkbench::$unInstallDeletTables = TRUE;
wpCipRedirectBase::install_actions();
wpCipRedirectBase::run();

function wp_cip_scripts_enqueue() {
    $style = 'bootstrap';
    if (is_admin()) {
        if ((!wp_style_is($style, 'queue') ) && (!wp_style_is($style, 'done') )) {
            //  queue up your bootstrap

            wp_register_script('bootstrap-js', WP_CIP_URL . 'bootstrap/js/bootstrap.min.js', array('jquery'), NULL, true);
            wp_register_style('bootstrap-css', WP_CIP_URL . 'bootstrap/css/bootstrap.min.css', false, NULL, 'all');
            //  wp_register_style('font-awesome-css', WP_CIP_URL . 'bootstrap/custom/font-awesome.min.css', array('bootstrap-css'), NULL, 'all');

            wp_enqueue_script('bootstrap-js');
            wp_enqueue_style('bootstrap-css');
            //  wp_enqueue_style('font-awesome-css');
        }
        wp_register_style(WP_CIP_SLUG . 'bootstrap-css', WP_CIP_URL . 'bootstrap/custom/style.css', array('bootstrap-css'), NULL, 'all');
        wp_enqueue_style(WP_CIP_SLUG . 'bootstrap-css');
    }
}

/**
 *  no directily call 
 */
//add_action('wp_enqueue_scripts', 'jmpt_scripts_enqueue');
// add_action('admin_enqueue_scripts', 'wp_cip_scripts_enqueue');

/**
 * load on my plugin only
 * 
 * This function is only called when our plugin's page loads!
 */
function wp_cip_load_admin_scripts() {
    // Unfortunately we can't just enqueue our scripts here - it's too early. So register against the proper action hook to do it
    add_action('admin_enqueue_scripts', WP_CIP_PREFIX . 'scripts_enqueue');
}

/**
 * admin menu function
 */
function wp_cip_admin_menu() {
    $menu = array();
    $menu[] = add_menu_page('Country IP Redirections', 'Country IP Specific Redirections', 'manage_options', WP_CIP_MENU, WP_CIP_PREFIX . 'admin_rule_view', WP_CIP_URL . 'bootstrap/images/icon.png');
    $menu[] = add_submenu_page(WP_CIP_MENU, 'Country IP Redirections', 'Country IP Specific Redirections', 'manage_options', WP_CIP_MENU, WP_CIP_PREFIX . 'admin_rule_view');
    $menu[] = add_submenu_page(WP_CIP_MENU, 'Country Redirections LOG', 'Country Redirections LOG', 'manage_options', WP_CIP_SLUG . 'log', WP_CIP_PREFIX . 'admin_log_view');
    $menu[] = add_submenu_page(WP_CIP_MENU, 'Country Mass Redirect', 'Country Mass Redirect', 'manage_options', WP_CIP_SLUG . 'mass', WP_CIP_PREFIX . 'admin_redirect_view');
    $menu[] = add_submenu_page(WP_CIP_MENU, 'Country NO Redirect', 'Country NO Redirect', 'manage_options', WP_CIP_SLUG . 'noredirect', WP_CIP_PREFIX . 'admin_noredirect_view');

    foreach ($menu as $page) {
        add_action('load-' . $page, WP_CIP_PREFIX . 'load_admin_scripts');
    }
}

add_action('admin_menu', 'wp_cip_admin_menu'); // hook so we can add menus to our admin left-hand menu
