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

class SpecialViewStatsUniqueUsers30 extends SpecialPage {
	function __construct() {
		parent::__construct( 'ViewStatsUniqueUsers30', '', false, false, '', true );
	}

	function execute( $par ) {
		$output = $this->getOutput();
		$this->setHeaders();
		
		$dbr = wfGetDB( DB_SLAVE );
		
		$wikitext = $this->displayUniqueUsers( $dbr );
		
		$output->addWikiText( $wikitext );
	}
	
	private function displayUniqueUsers( $dbr )
	{
		$userCount = $dbr->selectField( 'view_increment',
			[ 'count(distinct user_name)' ]
		);
		
		$wikitext .= "'''Unique users in the last 30 days:''' " . $userCount;
		
		return $wikitext;
	}
}