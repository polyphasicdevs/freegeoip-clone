<?php

/**
 * SECURITE / SECURITY 
 *  if called directly
 */
if (!defined('WPINC')) {
    die;
}

/**
 * required classes
 */
require_once WP_CIP_CLASS . 'wp-cip-schema.php';
require_once WP_CIP_CLASS . 'jellDB.php';

/**
 * wp cip base class
 *
 * The Plugin fundamental functions
 *
 * PHP version 5.5.9
 *
 * @author     Mudassar Ali <sahil_bwp@yahoo.com>
 * @copyright  2016 egooty.com
 */
class wpCipRedirectBase {

    private static $runInstallCalled = false;
    public static $unInstallDeletTables = true; // True if you want to delet table at uninstall plugin
    private static $loaderMap = array();

    public static function installPlugin() {
        self::runInstall();
    }

    public static function uninstallPlugin() {
        if (self::$unInstallDeletTables) {
            $schema = new cipSchema();
            $schema->dropAll();
            wpCipRedirectBase::removeWPOptions();
        }
    }

    public static function runInstall() {
        if (self::$runInstallCalled) {
            return;
        }
        self::$runInstallCalled = true;

        $schema = new cipSchema();
        $schema->createAll(); //if not exists
        //Must be the final line
        wpCipRedirectBase::addWPOptions();
    }

    public static function hasLoginCookie() {
        if (isset($_COOKIE)) {
            if (is_array($_COOKIE)) {
                foreach ($_COOKIE as $key => $val) {
                    if (strpos($key, 'wordpress_logged_in') == 0) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function addWPOptions() {
        add_option(WP_CIP_PREFIX . 'mass_redirect', '0');
        add_option(WP_CIP_PREFIX . 'mass_url', 'http://');
        add_option(WP_CIP_PREFIX . 'no_redirect', 0);
    }

    public static function removeWPOptions() {
        delete_option(WP_CIP_PREFIX . 'mass_redirect');
        delete_option(WP_CIP_PREFIX . 'mass_url');
        delete_option(WP_CIP_PREFIX . 'no_redirect');
    }

    public static function install_actions() {
        if (wpCipRedirectBase::hasLoginCookie()) { //Fast way of checking if user may be logged in. Not secure, but these are only available if you're signed in.
            register_activation_hook(WP_CIP_PATH . 'country-ip-specific-redirections.php', 'wpCipRedirectBase::installPlugin');
            register_deactivation_hook(WP_CIP_PATH . 'country-ip-specific-redirections.php', 'wpCipRedirectBase::uninstallPlugin');
        }
    }

    public static function wpHookActionLinks() {
        /**
         * plugin action link alter Hook
         */
        add_filter("plugin_action_links_" . WP_CIP_BASENAME, WP_CIP_PREFIX . 'settings_link');
    }

    public static function wpAddActions() {
        /**
         * plugin head Hook
         */
        add_action('wp_head', 'wp_cip_redirect');
        add_action('init', 'wp_cip_init');
    }

    public static function enqueueClasses() {
        // no need here yet
    }

    public static function run() {
        wpCipRedirectBase::tableVars(); // tables vars
        wpCipRedirectBase::capture();
        wpCipRedirectBase::bootLoader();
        wpCipRedirectBase::wpAddActions(); // call actions
        wpCipRedirectBase::wpHookActionLinks(); // call actions
    }

    public static function bootLoader() {
        if (!empty(self::$loaderMap)) {
            foreach (self::$loaderMap as $load) {
                require_once $load;
            }
        }
    }

    public static function capture($capture = 'all') {
        $functions = wpCipRedirectBase::enqueueFunctions();
        $views = wpCipRedirectBase::enqueueViews();
        self::$loaderMap = array_merge($functions, $views);
    }

    /**
     * Adding views
     */
    public static function enqueueViews() {
        $load = array();
        foreach (glob(WP_CIP_VIEWS . "*.php") as $view) {
            $load[] = $view;
        }
        return $load;
    }

    /**
     * Adding custom function
     */
    public static function enqueueFunctions() {
        $load = array();
        foreach (glob(WP_CIP_FUNC . "*.php") as $func) {
            $load[] = $func;
        }
        return $load;
    }

    public static function tableVars() {
        $schema = new cipSchema();
        $tables = $schema->getTableNames();
        foreach ($tables as $key => $table) {
            /**
             * Defines a named constant
             */
            //var_dump($key, $table);
            define($key, $table);
        }
    }

}
