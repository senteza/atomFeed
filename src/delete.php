<?php

namespace rssAtomFeed;

/**
 * Description of delete
 *
 * @author senteza
 */
class delete {
    
    public $db;
    public $feed_id;
    
    function __construct( $db ){
        $this->db = $db;
    }
    
    function deleteFeed( $feed_id ){
        $this->feed_id = $feed_id;
        if ( $this->deleteFeedEntries( $this->feed_id ) ) {
            if ( $this->db->con->query( "DELETE FROM `rss_atom_feeds` WHERE `id` = '{$this->feed_id}'" ) ) {
                return true;
            }
        }
        return false;
    }
    
    function deleteFeedEntries( $feed_id ){
        $result = $this->db->query(
            "SELECT `id` FROM `rss_atom_feeds_entries` WHERE `feed_id` = '{$feed_id}';"
        );
        $entries = array();
        while ( $row = mysqli_fetch_assoc($result) ){
            $entries[] = $row['id'];
        }
        if ( $this->deleteFeedEntriesCategories( $entries ) ) {
            if ( $this->db->con->query( "DELETE FROM `rss_atom_feeds_entries` WHERE `feed_id` = '{$this->feed_id}'" ) ) {
                return true;
            }
        }
        return false;
    }
    
    function deleteFeedEntriesCategories( $entries_id_array ){
        $entries_id_string = "('" . implode( $entries_id_array, "', '" ) . "')";
        if ( $this->db->con->query( "DELETE FROM `rss_atom_feeds_entries_categories` WHERE `entry_id` IN {$entries_id_string};" ) ) {
            return true;
        }
        return false;
    }
}