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

class SpecialViewStatsYourRecent extends SpecialPage {

    function __construct() {
        parent::__construct( 'ViewStatsYourRecent', '', false, false, '', true );
    }

    function execute( $par ) {
        $output = $this->getOutput();
        $this->setHeaders();

        $dbr = wfGetDB( DB_SLAVE );

        $wikitext = $this->displayYourRecentViews( $dbr );

        $output->addWikiText( $wikitext );
    }

    private function displayYourRecentViews( $dbr ) {
        $wikitext = "==Your recently viewed pages==\r\n";

        $pageIdSubquery = SpecialViewStatsUtility::getPageIdSubquery();

        $user = $this->getUser();
        $userName = $user->getName();

        $recentViews = $dbr->select( 'view_increment',
            [ 'view_increment.page_id', 'view_increment.update_timestamp' ],
            [ "view_increment.page_id in ({$pageIdSubquery})",
              "user_name = '{$userName}'" ],
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
