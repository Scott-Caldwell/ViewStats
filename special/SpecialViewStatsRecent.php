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

class SpecialViewStatsRecent extends SpecialPage {
	function __construct() {
		parent::__construct( 'ViewStatsRecent', '', false, false, '', true );
	}

	function execute( $par ) {
		$output = $this->getOutput();
		$this->setHeaders();
		
		$dbr = wfGetDB( DB_SLAVE );
		
		$wikitext = $this->displayRecentViews( $dbr );
		
		$output->addWikiText( $wikitext );
	}
	
	private function displayRecentViews( $dbr )
	{
		$wikitext = "==Recently viewed pages==\r\n";
		
		$recentViews = $dbr->select( 'view_increment',
			[ 'view_increment.page_id', 'view_increment.update_timestamp' ],
			'view_increment.page_id in (select page_id from page)',
			__METHOD__,
			[ 'ORDER BY' => 'view_increment.update_timestamp DESC LIMIT 10' ]
		);
		
		foreach( $recentViews as $row ){
			$page = WikiPage::newFromID( $row->page_id );

			$wikitext .= "* '''[[:" . $page->getTitle() . "]]''' ''at " . $row->update_timestamp . "''\r\n";
		}
		
		return $wikitext;
	}
}
