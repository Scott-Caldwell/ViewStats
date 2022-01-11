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

class SpecialViewStatsUniqueUsers30 extends SpecialPage {

    function __construct() {
        parent::__construct( 'ViewStatsUniqueUsers30', '', false, false, '', true );
    }

    function execute( $par ) {
        $output = $this->getOutput();
        $this->setHeaders();

        $dbr = wfGetDB( DB_REPLICA );

        $conditions = SpecialViewStatsUtility::getViewIncrementConditions("30 day");

        $wikitext = $this->displayUniqueUsers( $dbr, $conditions );
        $wikitext .= "\r\n\r\n";
        $wikitext .= $this->displayUniqueIPs( $dbr, $conditions );

        $output->addWikiTextAsContent( $wikitext );
    }

    private function displayUniqueUsers( $dbr, $conditions ) {
        $conditions[] = 'user_name in (select user_name from user)';

        $userCount = $dbr->selectField( 'view_increment',
            [ 'count(distinct user_name)' ],
            $conditions
        );

        return "'''Unique logged-in users in the last 30 days:''' {$userCount}";
    }

    private function displayUniqueIPs( $dbr, $conditions ) {
        $conditions[] = 'user_name not in (select user_name from user)';

        $userCount = $dbr->selectField( 'view_increment',
            [ 'count(distinct user_name)' ],
            $conditions
        );

        return "'''Unique IPs (not logged in) in the last 30 days:''' {$userCount}";
    }
}
