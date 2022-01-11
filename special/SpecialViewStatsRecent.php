<?php
/**
 * ViewStats extension
 *
 * @file
 * @ingroup Extensions
 * @author Scott Caldwell, 2022
 * @author Steven Orvis, 2022
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

        $dbr = wfGetDB( DB_REPLICA );

        $wikitext = $this->displayRecentViews( $dbr );

        $output->addWikiTextAsContent( $wikitext );
    }

    private function displayRecentViews( $dbr ) {
        $wikitext = "==Trending pages==\r\n";

        $conditions = SpecialViewStatsUtility::getViewIncrementConditions();

        $recentViews = $dbr->select( 'view_increment',
            [ 'view_increment.page_id',
              'max(view_increment.update_timestamp) as update_timestamp' ],
            $conditions,
            __METHOD__,
            [ 'GROUP BY' => 'view_increment.page_id',
              'ORDER BY' => 'max(view_increment.update_timestamp) DESC LIMIT 10' ]
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
