<?php
/**
 * ViewStats extension
 *
 * @file
 * @ingroup Extensions
 * @author Scott Caldwell, 2018
 * @author Steven Orvis, 2018
 * @license GNU General Public Licence 2.0 or later
 */

class SpecialViewStats extends SpecialPage {
	function __construct() {
		parent::__construct( 'ViewStats' );
	}

	function execute( $par ) {
		$output = $this->getOutput();
		$this->setHeaders();
		
		$dbr = wfGetDB( DB_SLAVE );
		
		$wikitext = '';

		$wikitext .= "{{Special:ViewStatsRecent}}\r\n";	
		$wikitext .= "{{Special:ViewStats30}}\r\n";
		$wikitext .= "{{Special:ViewStatsAllTime}}\r\n";
		
		$output->addWikiText( $wikitext );
	}
}