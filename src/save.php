<?php

namespace rssAtomFeed;

/**
 * Description of save
 *
 * @author senteza
 */
class save {
    
    public $db;
    public $data;
    
    function __construct( $db ){
        $this->db = $db;
    }
    
    function saveFeed( $feed ){
        $new_feed_id = $this->saveFeedMeta( $feed->new_feed );
        if ( $new_feed_id ) {
            $this->saveFeedEntries( $new_feed_id );
            return true;
        }
        return false;
    }
    
    private function saveFeedMeta( $data ){
        $this->data = $data;
        $feed_orig_id =  addslashes( $this->data->getId() );
        $feed_title =    addslashes( $this->data->getTitle() );
        $feed_updated =  date_format( $this->data->getDate(), 'Y-m-d H:i:s' );
        $feed_url =      addslashes( $this->data->getFeedUrl() );
        $feed_site_url = addslashes( $this->data->getSiteUrl() );
        $feed_icon =     addslashes( $this->data->getIcon() );
        $feed_logo =     addslashes( $this->data->getLogo() );
        $feed_subtitle = addslashes( $this->data->getDescription() );
        $this->db->query(
            "INSERT INTO `rss_atom_feeds` 
            (`feed_orig_id`, `feed_title`, `feed_updated`, `feed_url`, `feed_site_url`, `feed_icon`, `feed_logo`, `feed_subtitle`) 
            VALUES 
            ('{$feed_orig_id}','{$feed_title}','{$feed_updated}','{$feed_url}','{$feed_site_url}','{$feed_icon}','{$feed_logo}','{$feed_subtitle}');"
        );
        return ( $this->db->con->insert_id );
    }
    
    private function saveFeedEntries( $feed_id ){
        $feed_entries = $this->data->items;
        foreach( $feed_entries AS $entry ) {
            $this->saveFeedEntry( $entry, $feed_id );
        }
    }
    
    private function saveFeedEntry( $entry, $feed_id ){
        $entry_orig_id =    addslashes( $entry->getId() );
        $entry_title =      addslashes( $entry->getTitle() );
        $entry_content =    addslashes( $entry->getContent() );
        $entry_updated =    date_format( $entry->getUpdatedDate(), 'Y-m-d H:i:s' );
        $entry_published =  date_format( $entry->getPublishedDate(), 'Y-m-d H:i:s' );
        $entry_url =        addslashes( $entry->getUrl() );
        $entry_author =     addslashes( $entry->getAuthor() );
        $this->db->query(
            "INSERT INTO `rss_atom_feeds_entries` 
            (`feed_id`, `entry_orig_id`, `entry_title`, `entry_content`, `entry_updated`, `entry_published`, `entry_url`, `entry_author`) 
            VALUES 
            ('{$feed_id}','{$entry_orig_id}','{$entry_title}','{$entry_content}','{$entry_updated}','{$entry_published}','{$entry_url}','{$entry_author}');"
        );            
        if ( count( $entry->getCategories() ) > 0 ) {
            $this->saveFeedEntryCategories( $entry->getCategories(), $this->db->con->insert_id );
        }
    }
    
    private function saveFeedEntryCategories( $entry_categories, $entry_id ){
        if ( count($entry_categories) == 1 ) {
            $category = $entry_categories[0];
            $query = 
                "INSERT INTO `rss_atom_feeds_entries_categories` 
                (`entry_id`, `category`) 
                VALUES 
                ('{$entry_id}','{$category}');";
            $this->db->query( $query );
        }
        else {
            $query = '';
            foreach ( $entry_categories AS $category ) {
                $query .= " INSERT INTO `rss_atom_feeds_entries_categories` 
                    (`entry_id`, `category`) 
                    VALUES 
                    ('{$entry_id}','{$category}');";
            }
            $this->db->multiQuery( $query );
        }
    }
}
