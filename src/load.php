<?php

namespace rssAtomFeed;

/**
 * Description of load
 *
 * @author senteza
 */
class load {
    
    public $db;
    public $data;
    
    function __construct( $db ){
        $this->db = $db;
    }
    
    function loadFeed( $feed_id ){
        $result = $this->db->query(
            "SELECT * FROM `rss_atom_feeds` WHERE `id` = '{$feed_id}';"
        );
        $this->data = mysqli_fetch_assoc($result);
    }
}
