<?php
/**
 * ViewStats extension
 *
 * @file
 * @ingroup Extensions
 * @author Scott Caldwell, 2020
 * @author Steven Orvis, 2020
 * @license GNU General Public Licence 2.0 or later
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
        
        $output->addWikiText( $wikitext );
    }
}
