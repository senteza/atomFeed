<?php

namespace rssAtomFeed;

use mysqli;

/**
 * Description of db
 *
 * @author senteza
 */
class db {
    private $host;
    private $user;
    private $pass;
    private $db;
    public $con;

    public function __construct( $host = 'localhost', $user = 'root', $pass = '', $db = 'rss_atom' ) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->db   = $db;
        $this->db_connect();
    }

    private function db_connect(){
        $this->con = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if ( !$this->con ) {
            return false;
        }
        $this->con->query( 'SET NAMES \'cp1250\';' );
        $this->con->query( 'SET CHARSET \'utf8\';' );
        if ( $this->checkTables() ) {
            return $this->con;
        }
        else {
            if ( $this->createTables() ) {
                return $this->con;
            }
            else {
                return false;
            }
        }
    }

    public function query($query){
        $result = $this->con->query($query);
        if ( $result ){
            return $result;
        }
        else {
            return $this->con->error;
        }
    }
    
    public function multiQuery($query){
        $result = $this->con->multi_query($query);
        while ($this->con->next_result()) {
            if (!$this->con->more_results()) break; // flush multi_queries
        }
        if ( $result ){
            return $result;
        }
        else {
            return $this->con->error;
        }
    }
    
    public function checkTables(){
        $query1 = 
        "SELECT count(*)
        FROM information_schema.tables
        WHERE table_name = 'rss_atom_feeds'";
        $query2 = 
        "SELECT count(*)
        FROM information_schema.tables
        WHERE table_name = 'rss_atom_feeds_entries'";
        $query3 = 
        "SELECT count(*)
        FROM information_schema.tables
        WHERE table_name = 'rss_atom_feeds_entries_categories'";
        
        $result1 = $this->con->query( $query1 );
        $result2 = $this->con->query( $query2 );
        $result3 = $this->con->query( $query3 );
        
        if ( mysqli_fetch_row($result1)[0] && mysqli_fetch_row($result2)[0] && mysqli_fetch_row($result3)[0] ) {
            return true;
        }
        return false;
    }
    
    public function createTables(){
        $query1 =
        "CREATE TABLE IF NOT EXISTS `rss_atom_feeds` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `feed_orig_id` text COLLATE utf8_polish_ci NOT NULL COMMENT 'required',
        `feed_title` text COLLATE utf8_polish_ci NOT NULL COMMENT 'required',
        `feed_updated` timestamp NOT NULL COMMENT 'required',
        `feed_url` text COLLATE utf8_polish_ci NOT NULL COMMENT 'recommended',
        `feed_site_url` text COLLATE utf8_polish_ci NOT NULL COMMENT 'recommended',
        `feed_icon` text COLLATE utf8_polish_ci NOT NULL COMMENT 'optional',
        `feed_logo` text COLLATE utf8_polish_ci NOT NULL COMMENT 'optional',
        `feed_subtitle` text COLLATE utf8_polish_ci NOT NULL COMMENT 'optional',
        `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;";
        
        $query2 = 
        "CREATE TABLE IF NOT EXISTS `rss_atom_feeds_entries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `feed_id` int(11) NOT NULL,
        `entry_orig_id` text COLLATE utf8_polish_ci NOT NULL COMMENT 'required',
        `entry_title` text COLLATE utf8_polish_ci NOT NULL COMMENT 'required',
        `entry_content` text COLLATE utf8_polish_ci NOT NULL COMMENT 'required',
        `entry_updated` timestamp NOT NULL COMMENT 'required',
        `entry_published` timestamp NOT NULL COMMENT 'optional',
        `entry_url` text COLLATE utf8_polish_ci NOT NULL COMMENT 'recommended',
        `entry_author` text COLLATE utf8_polish_ci NOT NULL COMMENT 'recommended',
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;";
        
        $query3 = 
        "CREATE TABLE IF NOT EXISTS `rss_atom_feeds_entries_categories` (
        `entry_id` int(11) NOT NULL,
        `category` text COLLATE utf8_polish_ci NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;";
        
        if ( $this->con->query( $query1 ) && $this->con->query( $query2 ) && $this->con->query( $query3 ) ) {
            return true;
        }
        return false;
    }
}