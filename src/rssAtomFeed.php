<?php

namespace rssAtomFeed;

/**
 * Description of rssAtomFeed
 *
 * @author senteza
 */
class rssAtomFeed {
    var $db;
    var $output_object;
    var $user_interface; // html output
    var $allowed_actions;
    
    function __construct( $db ){
        $this->db = $db;
        $this->allowed_actions = $this->getAllowedActions();
        $this->output_object = new outputParse( $this->db );
        if ( !filter_input_array(INPUT_POST) ) {
            $this->output_object->getMainPage();
            $this->user_interface = $this->output_object->output;
        }
        else {
            $posts = filter_input_array(INPUT_POST);
            if ( in_array( $posts['action'], array_keys( $this->allowed_actions ) ) ) {
                $page = $this->allowed_actions[$posts['action']];
                $this->output_object->$page();
                $this->user_interface = $this->output_object->output;
            }
            else {
                $this->output_object->getErrorPage();
                $this->user_interface = $this->output_object->output;
            }
        }
    }
    
    function getAllowedActions(){
        return array(
            'download_feed' => 'getFeedDownloadPage',
            'select_feed'   => 'getFeedEditorPage',
            'update_feed'   => 'getFeedUpdatePage',
            'delete_feed'   => 'getFeedDeletePage',
            'error'         => 'getErrorPage' // :)
        );
    }
}