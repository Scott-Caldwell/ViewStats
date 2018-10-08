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

class SpecialViewStatsAllTime extends SpecialPage {
	function __construct() {
		parent::__construct( 'ViewStatsAllTime', '', false, false, '', true );
	}

	function execute( $par ) {
		$output = $this->getOutput();
		$this->setHeaders();
		
		$dbr = wfGetDB( DB_SLAVE );
		
		$wikitext = $this->displayCommonViewsAll( $dbr );
		
		$output->addWikiText( $wikitext );
	}
	
	private function displayCommonViewsAll( $dbr )
	{
		$wikitext = "==Most viewed pages of all time==\r\n";

		$totalViews_v = $dbr->select( 'view_increment',
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
				[ 'GROUP BY' => 'page_id',
				  'ORDER BY' => 'QUERYCOUNT DESC, page_id ASC LIMIT 10' ]
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

			$wikitext .= "|- \r\n |[[:" . $page->getTitle() . "]]\r\n |" . $row->QUERYCOUNT . " \r\n";
		}
		
		$wikitext .= "|} \r\n";
		
		return $wikitext;
	}
}