<?php

namespace rssAtomFeed;

use PicoFeed\Reader\Reader;

/**
 * Description of download
 *
 * @author senteza
 */
class download {
    
    public $new_feed;
    public $url;
    
    function __construct( $url ) {
        $this->url = $url;
        if ( $this->validate() ) {
            $reader = new Reader;
            // Return a resource
            $resource = $reader->download( $url );
            // Return the right parser instance according to the feed format
            $parser = $reader->getParser(
                $resource->getUrl(),
                $resource->getContent(),
                $resource->getEncoding()
            );

            // Return a Feed object
            $this->new_feed = $parser->execute();
        }
        else {
            $this->new_feed = false;
        }
    }
    
    function validate() {
        $headers = get_headers( $this->url );
        if (!$headers || $headers[0] == 'HTTP/1.1 404 Not Found'){
            return false;
        }
        else {
            if( substr( file_get_contents( $this->url ), 0, 5 ) == "<?xml") {
                return true;
            }
            else {
                return false;
            }
        }
    }
}