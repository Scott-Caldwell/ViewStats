<?php
/**
 * ViewStats extension
 *
 * @file
 * @ingroup Extensions
 * @author Scott Caldwell, 2020
 * @author Steven Orvis, 2020
 * @license MIT
 */

require_once( 'SpecialViewStatsUtility.php' );

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

    private function displayCommonViewsAll( $dbr ) {
        $wikitext = "==Most viewed pages of all time==\r\n";

        $pageIdSubquery = SpecialViewStatsUtility::getPageIdSubquery();

        $totalViews_v = $dbr->select( 'view_increment',
            [ 'max(total_views) AS QUERYCOUNT', 'page_id' ],
            "view_increment.page_id in ({$pageIdSubquery})",
            __METHOD__,
            [ 'GROUP BY' => 'page_id',
              'ORDER BY' => 'QUERYCOUNT DESC LIMIT 10' ]
        );

        if ( $dbr->tableExists( 'hit_counter' ) ) {
            $totalViews_h = $dbr->select( 'hit_counter',
                [ 'max(page_counter) AS QUERYCOUNT', 'page_id' ],
                "hit_counter.page_id in ({$pageIdSubquery})",
                __METHOD__,
                [ 'GROUP BY' => 'page_id',
                  'ORDER BY' => 'QUERYCOUNT DESC, page_id DESC LIMIT 10' ]
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

        $wikitext .= "{| class=\"wikitable sortable\"\r\n !Page\r\n !Views\r\n";

        foreach( $totalViews as $row ) {
            $page = WikiPage::newFromID( $row->page_id );
            $title = $page->getTitle();
            $count = $row->QUERYCOUNT;

            $wikitext .= "|-\r\n |[[:{$title}]]\r\n |{$count}\r\n";
        }

        $wikitext .= "|}\r\n";

        return $wikitext;
    }
}
