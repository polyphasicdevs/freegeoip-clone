<?php

/**
 * Common plugin functions
 *
 * The Plugin fundamental functions
 *
 * PHP version 5.5.9
 *
 * @author     Mudassar Ali <sahil_bwp@yahoo.com>
 * @copyright  2016 egooty.com
 */
/**
 * SECURITE / SECURITY 
 *  if called directly
 */
if (!defined('WPINC')) {
    die;
}

//die(TABLE_CIP_RULES);
//die(TABLE_CIP_LOGS);
/**
 * include required classes
 */
require_once WP_CIP_CLASS . 'freegeoip.php';
require_once WP_CIP_CLASS . 'jellDB.php';

/**
 * data validation
 * 
 * @param type $input
 * @param type $type
 * @return type
 */
function wp_cip_validate($input, $type = "email") {
    $is_valid = TRUE;
    switch ($type) {
        case 'email':
            $is_valid = is_email($input);
            break;
        case 'bool':
            $is_valid = is_bool($input);
            break;
        case 'null':
            $is_valid = is_null($input);
            break;
        case 'float':
            $is_valid = is_float($input);
            break;
        case 'int':
            $is_valid = is_int($input);
            break;
        case 'string':
            $is_valid = is_string($input);
            break;
        case 'object':
            $is_valid = is_object($input);
            break;
        case 'is_array':
            $is_valid = is_array($input);
            break;
        case 'numeric':
            $is_valid = is_numeric($input);
            break;
        case 'url':
            $is_valid = filter_var($input, FILTER_VALIDATE_URL);
            break;
        default:
            break;
    }

    return $is_valid;
}

/**
 * Clean Input
 *  
 * @param type $input
 * @param type $type
 * @return type
 */
function wp_cip_clean_input($input, $type = "input") {
    $input = trim($input);
    // $input = get_magic_quotes_gpc() ? stripslashes($input) : $input;
    $allowed = array('email', 'input');
    if (in_array($type, $allowed)) {
        $input = get_magic_quotes_gpc() ? stripslashes($input) : $input;
    }
    switch ($type) {
        case 'email':
            $input = sanitize_email($input);
            break;
        case 'url':
            $input = esc_url_raw($input);
            break;
        default:
            $input = sanitize_text_field($input);
            break;
    }

    return $input;
}

/**
 * Clean Output
 * 
 * @param type $out
 * @param type $type
 * @return type
 */
function wp_cip_clean_out($out, $type = "html") {
    // $input = get_magic_quotes_gpc() ? stripslashes($input) : $input;
    $allowed = array('email', 'input');
    if (in_array($allowed, $type)) {
        $out = get_magic_quotes_gpc() ? stripslashes($out) : $out;
    }
    switch ($type) {
        case 'html':
            $out = esc_html($out);
            break;
        case 'url':
            $out = esc_url($out);
            break;
        case 'js':
            $out = esc_js($out);
            break;
        case 'attr':
            $out = esc_attr($out);
            break;
        case 'textarea':
            $out = esc_textarea($out);
            break;
        case 'html_class':
            $out = esc_attr(sanitize_html_class($out));
            break;

        default:
            break;
    }

    return $out;
}

/**
 * Add WP-Admin plugin page setting link
 */
function wp_cip_settings_link($links) {
    $url = admin_url('admin.php?page=' . WP_CIP_MENU);
    $settings_link = '<a href="' . $url . '">Settings</a>';
    $mylinks = array($settings_link);
    return array_merge($links, $mylinks);
}

/**
 *  Get Geoip Country list
 */
function wp_cip_get_contry_codes() {
    $geoIP = new freeGeoIP();
    $countryList = array_combine($geoIP->GEOIP_COUNTRY_CODES, $geoIP->GEOIP_COUNTRY_NAMES);
    asort($countryList);
    return array_filter($countryList);
}

function wp_cip_get_posts($post_type = NULL) {
    $options = 'numberposts=-1&offset=0';
    if ($post_type) {
        $options .= '&post_type=' . $post_type;
    }
    return get_posts($options);
}

function wp_cip_get_all_content() {
    $posts['posts'] = wp_cip_get_posts();
    $posts['products'] = wp_cip_get_posts('product');
    $posts['download'] = wp_cip_get_posts('download');
    $posts['pages'] = get_pages('numberposts=-1&offset=0');
    return $posts;
}

function wp_cip_check_cache() {
    $all_plugins = get_plugins();
    $_plugins = array();

    foreach ($all_plugins as $plugin_name => $plugin_array)
        $_plugins[] = $plugin_name;

    return preg_grep("/cache/i", $_plugins);
}

function wp_cip_get_rules() {
    $db = new jellDB();
    $res = $db->querySelect("SELECT * FROM " . TABLE_CIP_RULES);
    return json_decode(json_encode($res), FALSE);  // array to object
}

function wp_cip_delete_rule($id) {
    $ruleID = intval($id);
    $db = new jellDB();
    return $db->queryWrite("DELETE FROM " . TABLE_CIP_RULES . " where id=%d", $ruleID);
}

function wp_cip_attach_rule($country, $target, $catID, $postID, $homeRule = 0) {
    $db = new jellDB();
    return $db->queryWrite("INSERT INTO " . TABLE_CIP_RULES . " (country_id, target_url, cat_id, post_id, home_rule) values ( %s, '%s', '%d', '%d', '%d')", $country, $target, $catID, $postID, $homeRule);
}

function wp_cip_attach_log($post_t, $message) {
    $db = new jellDB();
    return $db->queryWrite("INSERT INTO " . TABLE_CIP_LOGS . " (post, message) values ( %s, '%s')", $post_t, $message);
}

function wp_cip_add_rule($post) {
    $_POST = $post;
    if ($_POST['target'] != "http://www." && $_POST['target'] != "" && $_POST['country'] != "") {
        $country = esc_sql($_POST['country']);
        $target = esc_sql(trim($_POST['target']));
        $catID = intval($_POST['catID']);

        if ($_POST['postID'] == 'home') {
            $postID = 0;
            $rs = wp_cip_attach_rule($country, $target, $catID, $postID, 1);
        } else {
            $postID = intval($_POST['postID']);
            $rs = wp_cip_attach_rule($country, $target, $catID, $postID);
        }

        return '<div class="alert alert-success">Yep! Rule successfully created!</div>';
    } else {
        return '<div class="alert alert-danger">Country & Target URL must be specified</div>';
    }
}

function wp_cip_get_logs($limit = 100) {
    $db = new jellDB();
    $res = $db->querySelect("SELECT * FROM " . TABLE_CIP_LOGS . " ORDER BY id DESC LIMIT " . $limit);
    return json_decode(json_encode($res), FALSE);  // array to object
}

function wp_cip_update_mass($post) {
    $_POST = $post;
    update_option(WP_CIP_PREFIX . 'mass_redirect', wp_cip_clean_input($_POST['mass_redirect']));
    update_option(WP_CIP_PREFIX . 'mass_url', wp_cip_clean_input($_POST['mass_url'], 'url'));
    return '<div class="alert alert-success">Yep! Settings Updated</div>';
}

function wp_cip_update_no_rd($post) {
    $_POST = $post;
    update_option(WP_CIP_PREFIX . 'no_redirect', wp_cip_clean_input($_POST['no_redirect']));
    return '<div class="alert alert-success">Yep! Settings Updated</div>';
}

/**
 * Logging utilities | this function not in use
 * 
 * @global type $wpdb
 * @param type $action
 * @param type $message
 * 
 * @deprecated since version 3.0
 */
function wpgeoip_add_log($action, $message) {
    global $wpdb;
    $action = $wpdb->escape($action);
    $message = $wpdb->escape($message);
    return $wpdb->query("INSERT INTO " . $prefix . "cip_logs VALUES (null, '$action', '$message')");
}

function wp_cip_getIP() {

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function wp_cip_get_single_rule($country_id, $post_id = NULL, $cat_id = NULL) {
    $db = new jellDB();
    if ($post_id) {
        $res = $db->querySingleRec("SELECT target_url FROM " . TABLE_CIP_RULES . " WHERE country_id = %s AND post_id = %d", $country_id, $post_id);
    }
    if ($cat_id) {
        $res = $db->querySingleRec("SELECT target_url FROM " . TABLE_CIP_RULES . " WHERE country_id = %s AND cat_id = %d", $country_id, $cat_id);
    }
    if (!$post_id && !$cat_id) {
        $res = $db->querySingleRec("SELECT target_url FROM " . TABLE_CIP_RULES . " WHERE country_id = %s AND home_rule = %d", $country_id, 1);
    }

    return json_decode(json_encode($res), FALSE);  // array to object
}

/**
 * Ip Rediection logic
 * 
 * @global type $wpdb
 * @global type $wp_query
 * @global type $post
 * @return boolean
 */
function wp_cip_redirect() {

    if (get_option(WP_CIP_PREFIX . 'no_redirect', 0) == 1 AND isset($_GET['noredirect']))
        return false;

    global $wpdb;
    global $wp_query;
    global $post;

    $prefix = $wpdb->prefix;

    $postID = $post->ID;

    $catID = intval($wp_query->query_vars['cat']);
    $isHome = is_home();
    $the_page_name = '';

    //get user country
    $ip = wp_cip_getIP();
    // for localhost test
    $ip = ($ip == '127.0.0.1') ? '182.191.177.58' : $ip; //localhost check;
    //$ip = '182.191.177.58'; //localhost testing;
    $countryCode = geoip_country_code_by_ip($ip);
    //sitewide rule
    $rs_redirect = wp_cip_get_single_rule($countryCode, 999999);
    if (isset($rs_redirect) AND ( count($rs_redirect))) {
        $the_page_name = get_the_title($postID);
        $message = "Redirecting Country <strong>" . $countryCode . "</strong> to " . $rs_redirect->target_url;
        wp_cip_attach_log("SITEWIDE Redirect", $message);
        print '<meta http-equiv="refresh" content="0;url=' . $rs_redirect->targeturl . '"/>';
        exit;
    }

    //redirect if any rule for this country
    if ($postID != 0) {
        $rs_redirect = wp_cip_get_single_rule($countryCode, $postID);
        $the_page_name = get_the_title($postID);
    }
    if ($catID != 0) {
        $rs_redirect = wp_cip_get_single_rule($countryCode, FALSE, $catID);
        $the_page_name = 'Category : ' . get_the_category_by_ID($catID);
    }
    if ($isHome) {
        $rs_redirect = wp_cip_get_single_rule($countryCode, FALSE, FALSE);
        $the_page_name = 'Homepage';
    }
    if (!$rs_redirect) {
        //NOTHING TO DO
        #$wpdb->query("INSERT INTO ip_csr_log VALUES (null, 'Redirect', 'Nothing to do. No rules for Country <strong>".$countryCode."</strong>')");
    }

    if (isset($rs_redirect) AND ( count($rs_redirect))) {
        $message = "Redirecting Country <strong>" . $countryCode . "</strong> to " . $rs_redirect->target_url;
        wp_cip_attach_log("Redirect <em>" . $the_page_name . "</em>", $message);
        print '<meta http-equiv="refresh" content="0;url=' . $rs_redirect->target_url . '"/>';
        exit;
    } else {
        //CHECK COUNTRIES WITHOUT REDIRECT RULES
        $mass_redirect_enabled = get_option(WP_CIP_PREFIX . 'mass_redirect');
        if ($mass_redirect_enabled != "0") {
            $mass_url = get_option(WP_CIP_PREFIX . 'mass_url');
            $message = "'Redirecting Country <strong>" . $countryCode . "</strong> to " . $rs_redirect->target_url;
            wp_cip_attach_log('Mass Redirect', $message);
            print '<meta http-equiv="refresh" content="0;url=' . $mass_url . '"/>';
            exit;
        } else {
            //NOTHING TO DO AGAINM
        }
    }
}

/**
 * wp Init hook implemented
 */
function wp_cip_init() {
    if (!is_admin()) {
        $ip = wp_cip_getIP();
        // for localhost test
        $ip = ($ip == '127.0.0.1') ? '182.191.177.58' : $ip; //localhost check;
        //$ip = '182.191.177.58'; //localhost testing;
        $countryCode = geoip_country_code_by_ip($ip);
    }
}
