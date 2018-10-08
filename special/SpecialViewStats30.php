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

class SpecialViewStats30 extends SpecialPage {
	function __construct() {
		parent::__construct( 'ViewStats30', '', false, false, '', true );
	}

	function execute( $par ) {
		$output = $this->getOutput();
		$this->setHeaders();
		
		$dbr = wfGetDB( DB_SLAVE );
		
		$wikitext = '';

		$wikitext .= $this->displayCommonViews30( $dbr );
		
		$output->addWikiText( $wikitext );
	}
	
	private function displayCommonViews30( $dbr )
	{
		$wikitext = "==Most viewed pages in the last 30 days==\r\n";
		
		$recentViews = $dbr->select( 'view_increment',
			[ 'count(*) AS QUERYCOUNT', 'page_id' ],
				'update_timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 day))',
			__METHOD__,
			[ 'GROUP BY' => 'page_id',
				'ORDER BY' => 'QUERYCOUNT DESC, page_id DESC LIMIT 10' ]
		);
		
		$wikitext .= "{| class=\"wikitable sortable\" \r\n !Page \r\n !Views \r\n";
		
		foreach( $recentViews as $row ) {
			$page = WikiPage::newFromID( $row->page_id );

			$wikitext .= "|- \r\n |[[:" . $page->getTitle() . "]]\r\n |" . $row->QUERYCOUNT . " \r\n";
		}
		
		$wikitext .= "|} \r\n";
		
		return $wikitext;
	}
}