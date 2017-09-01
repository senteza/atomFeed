<?php

namespace rssAtomFeed;

/**
 * Description of outputParse
 *
 * @author senteza
 */
class outputParse {
    
    public $db;
    public $output = '';
    
    function __construct( $db ){
        $this->db = $db;
    }
    
    function getMainPage(){
        $this->output .= '<div style="text-align: center;">';
        $this->output .= $this->getFeedDownloadInput( 'http://php.net/feed.atom' );
        $this->output .= 'or';
        $this->output .= $this->getFeedSelector();
        $this->output .= '</div>';
    }
    
    function getErrorPage( $error_message = 'Something went wrong!' ){
        $this->output = 
        "<div style='height:130px; border: 1px solid #bbb; background: #f5f5f5; width: 400px; margin: 0 auto; margin-top:25px; text-align: center;'>
            <h3>Error</h3>
            <p>{$error_message}</p>" .
            $this->getBackButton() .
        "</div>";
    }
    
    function getFeedDownloadPage(){
        $posts = filter_input_array(INPUT_POST);
        if ( in_array( 'feed_url', array_keys( $posts ) ) ){
            $new_feed = new download( $posts['feed_url'] );
            if ( $new_feed->new_feed ) {
                $save = new save( $this->db );
                if ( $save->saveFeed( $new_feed ) ){
                    $this->output = 
                    "<div style='height:110px; border: 1px solid #bbb; background: #f5f5f5; width: 400px; margin: 0 auto; margin-top:25px; text-align: center;'>
                        <h3>Feed downloaded!</h3>" .
                        $this->getBackButton() .
                    "</div>";
                }
                else {
                    $this->getErrorPage( 'Problem with saving data' );
                }
            }
            else {
                $this->getErrorPage( 'Bad url' );
            }
        }
        else {
            $this->getErrorPage( 'Please enter url' );
        }
    }
    
    function getFeedEditorPage(){
        $posts = filter_input_array(INPUT_POST);
        if ( in_array( 'feed_id', array_keys( $posts ) ) ){
            $feed_id = $posts['feed_id'];
            $feed = new load( $this->db );
            $feed->loadFeed( $feed_id );
            $this->output = 
            "<div style='border: 1px solid #bbb; background: #f5f5f5; width: 500px; margin: 0 auto; margin-top:25px; text-align: right;'>
                <h3 style='text-align: center;'>Editing feed id:{$feed_id}</h3>
                <form method='post'>" .
                    $this->getFeedEditorInputs($feed->data) .
                    // "<button name='action' value='update_feed' type='submit' style='width:80px; display: block; margin: 0 auto; margin-top:10px;'>update</button>
                    "<button name='action' value='delete_feed' type='submit' style='width:80px; display: block; margin: 0 auto; margin-top:10px;'>delete</button>
                </form>".
                $this->getBackButton() .
            "</div>";
        }
        else {
            $this->getErrorPage( 'Please select feed to edit' );
        }
    }
    
    function getFeedDeletePage(){
        $posts = filter_input_array(INPUT_POST);
        if ( in_array( 'id', array_keys( $posts ) ) ){
            $feed_id = $posts['id'];
            $delete = new delete( $this->db );
            if ( $delete->deleteFeed($feed_id) ) {
            $this->output = 
                "<div style='border: 1px solid #bbb; background: #f5f5f5; width: 500px; margin: 0 auto; margin-top:25px; text-align: right;'>
                    <h3 style='text-align: center;'>Deleted</h3>" .
                    $this->getBackButton() .
                "</div>";
            }
            else {
                $this->getErrorPage( "couldn't delete" );
            }
        }
        else {
            $this->getErrorPage( 'Please select feed to edit' );
        }
    }
    
    function getFeedEditorInputs($feed){
        return "
            <input type='hidden' name='id' value='{$feed['id']}' />
            title: <input type='text' name='feed_title' value='{$feed['feed_title']}' style='width:250px; margin-right:100px; text-align: center;' /><br />
            original feed id: <input type='text' name='feed_orig_id' value='{$feed['feed_orig_id']}' style='width:250px; margin-right:100px; margin-top:15px; text-align: center;' /><br />
            feed url: <input type='text' name='feed_url' value='{$feed['feed_url']}' style='width:250px; margin-right:100px; margin-top:15px; text-align: center;' /><br />
            site: <input type='text' name='feed_site_url' value='{$feed['feed_site_url']}' style='width:250px; margin-right:100px; margin-top:15px; text-align: center;' /><br />
            feed icon: <input type='text' name='feed_icon' value='{$feed['feed_icon']}' style='width:250px; margin-right:100px; margin-top:15px; text-align: center;' /><br />
            feed logo: <input type='text' name='feed_logo' value='{$feed['feed_logo']}' style='width:250px; margin-right:100px; margin-top:15px; text-align: center;' /><br />
            feed description: <input type='text' name='feed_subtitle' value='{$feed['feed_subtitle']}' style='width:250px; margin-right:100px; margin-top:15px; text-align: center;' /><br />
            added date: <input type='text' name='added' value='{$feed['added']}' style='width:250px; margin-right:100px; margin-top:15px; text-align: center;' />
        ";
    }
    
    function getBackButton(){
        $post = filter_input_array(INPUT_SERVER);
        if ( $post['HTTP_REFERER'] ) {
            $back = $post['HTTP_REFERER'];
            return "<p style='text-align:center;'><a href='{$back}'>back</a></p>";
        }
        return false;
    }
    
    function getFeedDownloadInput( $url = null ){
        /*
         * feed examples
         * 
         * http://php.net/feed.atom
         * http://blog.case.edu/news/feed.atom
         * http://drpeterjones.com/feed.php
         * http://wintermute.com.au/bits.atom
         * http://use.perl.org/use.perl.org/index.atom
         * 
         */
        return "
            <div style='height:120px; border: 1px solid #bbb; background: #f5f5f5; width: 400px; margin: 0 auto; margin-top:100px; margin-bottom:25px; text-align: center;'>
                <form method='post'>
                    <h3>
                        enter atom-feed url to download
                    </h3>
                    <input type='text' name='feed_url' value='{$url}' />
                    <button name='action' value='download_feed' type='submit' style='width:80px;'>download</button>
                </form>
            </div>
        ";
    }
    
    function getFeedSelector(){
        $feeds = array();
        $result = $this->db->con->query( "SELECT `id`, `feed_site_url`, `added` FROM `rss_atom_feeds` ORDER BY `added` DESC;" );
        while ($row = mysqli_fetch_assoc($result)){
            $feeds[] = $row;
        }
        $feed_options = '';
        foreach ( $feeds AS $feed ) {
            $id = $feed['id'];
            $added = $feed['added'];
            $site =  $feed['feed_site_url'];
            $feed_options .= "\n<option value='{$id}'>{$added} - {$site}</option>";
        }
        return "
            <div style='height:120px; border: 1px solid #bbb; background: #f5f5f5; width: 400px; margin: 0 auto; margin-top:25px;'>
                <form method='post'>
                    <h3>select one of previously saved atom-feeds</h3>
                    <select name='feed_id'>
                        {$feed_options}
                    </select>
                    <button name='action' value='select_feed' type='submit' style='width:80px;'>select</button>
                </form>
            </div>
        
        ";
    }
}
