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

class SpecialViewStatsTotalViews30 extends SpecialPage {

    function __construct() {
        parent::__construct( 'ViewStatsTotalViews30', '', false, false, '', true );
    }

    function execute( $par ) {
        $output = $this->getOutput();
        $this->setHeaders();

        $dbr = wfGetDB( DB_REPLICA );

        $wikitext = $this->displayTotalViews( $dbr );

        $output->addWikiTextAsContent( $wikitext );
    }

    private function displayTotalViews( $dbr ) {
        $conditions = SpecialViewStatsUtility::getViewIncrementConditions("30 day");

        $userCount = $dbr->selectField( 'view_increment',
            [ 'count(*)' ],
            $conditions
        );

        return "'''Total views in the last 30 days:''' {$userCount}";
    }
}
