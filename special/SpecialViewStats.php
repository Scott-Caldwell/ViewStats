<?php
/**
 * ViewStats extension
 *
 * @file
 * @ingroup Extensions
 * @author Scott Caldwell, 2022
 * @author Steven Orvis, 2022
 * @license MIT
 */

class SpecialViewStats extends SpecialPage {
    
    function __construct() {
        parent::__construct( 'ViewStats' );
    }

    function execute( $par ) {
        $output = $this->getOutput();
        $this->setHeaders();
        
        $wikitext = '';

        $wikitext .= "{{Special:ViewStatsUniqueUsers30}}\r\n";
        $wikitext .= "{{Special:ViewStatsTotalViews30}}\r\n";
        $wikitext .= "{{Special:ViewStatsRecent}}\r\n";    
        $wikitext .= "{{Special:ViewStats7}}\r\n";
        $wikitext .= "{{Special:ViewStats30}}\r\n";
        $wikitext .= "{{Special:ViewStatsAllTime}}\r\n";
        
        $output->addWikiTextAsContent( $wikitext );
    }
}
