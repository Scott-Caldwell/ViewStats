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

require_once( 'SpecialViewStatsUtility.php' );

class SpecialViewStats7 extends SpecialPage {
    
    function __construct() {
        parent::__construct( 'ViewStats7', '', false, false, '', true );
    }

    function execute( $par ) {
        $output = $this->getOutput();
        $this->setHeaders();
        
        $dbr = wfGetDB( DB_SLAVE );
        
        $wikitext = $this->displayCommonViews7( $dbr );
        
        $output->addWikiText( $wikitext );
    }
    
    private function displayCommonViews7( $dbr ) {
        $pageIdSubquery = SpecialViewStatsUtility::getPageIdSubquery();
        
        $recentViews = $dbr->select( 'view_increment',
            [ 'count(*) AS QUERYCOUNT', 'page_id' ],
              "view_increment.page_id in ({$pageIdSubquery}) and update_timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 day))",
            __METHOD__,
            [ 'GROUP BY' => 'page_id',
              'ORDER BY' => 'QUERYCOUNT DESC, page_id DESC LIMIT 10' ]
        );
        
        $wikitext = "==Most viewed pages in the last 7 days==\r\n{| class=\"wikitable sortable\"\r\n !Page\r\n !Views\r\n";
        
        foreach ( $recentViews as $row ) {
            $page = WikiPage::newFromID( $row->page_id );
            $title = $page->getTitle();
            $count = $row->QUERYCOUNT;

            $wikitext .= "|-\r\n |[[:{$title}]]\r\n |{$count}\r\n";
        }
        
        return $wikitext . "|}\r\n";
    }
}
