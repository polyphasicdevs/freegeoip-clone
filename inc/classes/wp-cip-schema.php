<?php

/**
 * wp cip install schema
 *
 * The functions and method being run on install / uninstall plugin
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

/**
 *  adding DB class
 */
require_once WP_CIP_CLASS . 'jellDB.php';

/**
 * Schema class
 */
if (!class_exists('cipSchema')) {

    class cipSchema {

        private $tables = array(
            "cip_rules" => "(
                `id` int(10) unsigned NOT NULL auto_increment,
                `country_id` varchar(255) NOT NULL,
                `target_url` varchar(255) NOT NULL,
                `cat_id` int(10) NOT NULL,
                `post_id` int(10) NOT NULL,
                `home_rule` int(1) NOT NULL,
                PRIMARY KEY  (`id`)
            )DEFAULT CHARSET=utf8 AUTO_INCREMENT=1",
            "cip_logs" => "(
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `post` VARCHAR( 255 ) NOT NULL ,
                `message` VARCHAR( 255 ) NOT NULL
                )DEFAULT CHARSET=utf8;"
        );
        private $db = false;
        private $prefix = 'wp_';

        public function __construct($dbhost = false, $dbuser = false, $dbpassword = false, $dbname = false) {
            /*
              if($dbhost){ //for testing
              $this->db = new jellDB(false, $dbhost, $dbuser, $dbpassword, $dbname);
              $this->prefix = 'wp_';
              } else {
             */
            global $wpdb;
            $this->db = new jellDB();
            $this->prefix = $wpdb->base_prefix;
        }

        public function dropAll() {
            foreach ($this->tables as $table => $def) {
                $this->db->queryWrite("DROP TABLE IF EXISTS " . $this->prefix . $table);
            }
        }

        public function createAll() {
            foreach ($this->tables as $table => $def) {
                $this->db->queryWrite("CREATE TABLE IF NOT EXISTS " . $this->prefix . $table . " " . $def);
            }
        }

        public function create($table) {
            $this->db->queryWrite("CREATE TABLE IF NOT EXISTS " . $this->prefix . $table . " " . $this->tables[$table]);
        }

        public function drop($table) {
            $this->db->queryWrite("DROP TABLE IF EXISTS " . $this->prefix . $table);
        }

        public function getTableNames() {
            $tables = array();
            foreach ($this->tables as $key => $table) {
                /**
                 * Defines a named constant
                 */
                
                $table = $this->prefix . $key;
                //define('TABLE_' . strtoupper($key), $table);
                $tables['TABLE_' . strtoupper($key)] = $table;
            }
            return $tables;
        }

    }

}