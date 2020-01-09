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

    private function displayRecentViews( $dbr ) {
        $wikitext = "==Trending pages==\r\n";

        $pageIdSubquery = SpecialViewStatsUtility::getPageIdSubquery();

        $recentViews = $dbr->select( 'view_increment',
            [ 'view_increment.page_id', 'view_increment.update_timestamp' ],
            "view_increment.page_id in ({$pageIdSubquery})",
            __METHOD__,
            [ 'ORDER BY' => 'view_increment.update_timestamp DESC LIMIT 10' ]
        );

        foreach ( $recentViews as $row ){
            $page = WikiPage::newFromID( $row->page_id );
            $title = $page->getTitle();
            $timestamp = $row->update_timestamp;

            $wikitext .= "* '''[[:{$title}]]''' ''at {$timestamp}''\r\n";
        }

        return $wikitext;
    }
}
