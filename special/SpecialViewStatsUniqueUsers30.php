<?php
/**
 * ViewStats extension
 *
 * @file
 * @ingroup Extensions
 * @author Scott Caldwell, 2019
 * @author Steven Orvis, 2019
 * @license GNU General Public Licence 2.0 or later
 */

require_once( 'SpecialViewStatsUtility.php' );

class SpecialViewStatsUniqueUsers30 extends SpecialPage {

    function __construct() {
        parent::__construct( 'ViewStatsUniqueUsers30', '', false, false, '', true );
    }

    function execute( $par ) {
        $output = $this->getOutput();
        $this->setHeaders();

        $dbr = wfGetDB( DB_SLAVE );

        $pageIdSubquery = SpecialViewStatsUtility::getPageIdSubquery();

        $wikitext = $this->displayUniqueUsers( $dbr, $pageIdSubquery );
        $wikitext .= "\r\n\r\n";
        $wikitext .= $this->displayUniqueIPs( $dbr, $pageIdSubquery );

        $output->addWikiText( $wikitext );
    }

    private function displayUniqueUsers( $dbr, $pageIdSubquery ) {
        $userCount = $dbr->selectField( 'view_increment',
            [ 'count(distinct user_name)' ],
            [ "page_id in ({$pageIdSubquery})",
              'update_timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 day))',
              'user_name in (select user_name from user)' ]
        );

        return "'''Unique logged-in users in the last 30 days:''' {$userCount}";
    }

    private function displayUniqueIPs( $dbr, $pageIdSubquery ) {
        $userCount = $dbr->selectField( 'view_increment',
            [ 'count(distinct user_name)' ],
            [ "page_id in ({$pageIdSubquery})",
              'update_timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 day))',
              'user_name not in (select user_name from user)' ]
        );

        return "'''Unique IPs (not logged in) in the last 30 days:''' {$userCount}";
    }
}
