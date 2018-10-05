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
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();

		$param = $request->getText( 'param' );
		
		$dbr = wfGetDB( DB_SLAVE );
		
		$wikitext = '';

		$wikitext .= $this->displayRecentViews( $dbr );	
		$wikitext .= $this->displayCommonViews30( $dbr );
		$wikitext .= $this->displayCommonViewsAll( $dbr );
		
		$output->addWikiText( $wikitext );
	}
	
	private function displayRecentViews( $dbr )
	{
		$wikitext = "==Recently viewed pages==\r\n";
		
		$recentViews = $dbr->select( 'view_increment',
					[ 'view_increment.page_id', 'view_increment.update_timestamp' ],
					'',
					__METHOD__,
					[ 'ORDER BY' => 'view_increment.update_timestamp DESC LIMIT 10' ]
		);
		
		foreach( $recentViews as $row ){
			$page = WikiPage::newFromID( $row->page_id );

			$wikitext .= "* '''" . $page->getTitle() . "''' ''at " . $row->update_timestamp . "''\r\n";
		}
		
		return $wikitext;
	}
	
	private function displayCommonViews30( $dbr )
	{
		$wikitext = "==Most viewed pages in the past 30 days==\r\n";
		
		$recentViews = $dbr->select(
					'view_increment',
					[ 'max(total_views) AS QUERYCOUNT', 'page_id' ],
					'update_timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 day))',
					__METHOD__,
					[ 'GROUP BY' => 'page_id',
					  'ORDER BY' => 'QUERYCOUNT DESC LIMIT 10' ]
		);
		
		$wikitext .= "{| class=\"wikitable sortable\" \r\n !Page \r\n !Views \r\n";
		
		foreach( $recentViews as $row ) {
			$page = WikiPage::newFromID( $row->page_id );

			$wikitext .= "|- \r\n |" . $page->getTitle() . "\r\n |" . $row->QUERYCOUNT . " \r\n";
		}
		
		$wikitext .= "|} \r\n";
		
		return $wikitext;
	}
	
	private function displayCommonViewsAll( $dbr )
	{
		$wikitext = "==Most viewed pages of all time==\r\n";

		$totalViews_v = $dbr->select(
			'view_increment',
			[ 'max(total_views) AS QUERYCOUNT', 'page_id' ],
			'',
			__METHOD__,
			[ 'GROUP BY' => 'page_id',
			  'ORDER BY' => 'QUERYCOUNT DESC LIMIT 10' ]
		);

		if ( $dbr->tableExists( 'hit_counter' ) ) {
			$totalViews_h = $dbr->select(
				'hit_counter',
				[ 'max(page_counter) AS QUERYCOUNT', 'page_id' ],
				'',
				__METHOD__,
				[ 'ORDER BY' => 'QUERYCOUNT DESC LIMIT 10' ]
			);

			if ( $totalViews_h >= $totalViews_v ) {
				$totalViews = $totalViews_h;
			}
			else {
				$totalViews = $totalViews_v;
			}
		}
		else {
			$totalViews = $totalViews_v;
		}
		
		$wikitext .= "{| class=\"wikitable sortable\" \r\n !Page \r\n !Views \r\n";
		
		foreach( $totalViews as $row ) {
			$page = WikiPage::newFromID( $row->page_id );

			$wikitext .= "|- \r\n |" . $page->getTitle() . "\r\n |" . $row->QUERYCOUNT . " \r\n";
		}
		
		$wikitext .= "|} \r\n";
		
		return $wikitext;
	}
}