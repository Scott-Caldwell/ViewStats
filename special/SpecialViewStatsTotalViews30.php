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

class SpecialViewStatsTotalViews30 extends SpecialPage {

    function __construct() {
        parent::__construct( 'ViewStatsTotalViews30', '', false, false, '', true );
    }

    function execute( $par ) {
        $output = $this->getOutput();
        $this->setHeaders();

        $dbr = wfGetDB( DB_SLAVE );

        $wikitext = $this->displayUniqueUsers( $dbr );

        $output->addWikiText( $wikitext );
    }

    private function displayUniqueUsers( $dbr ) {
        $pageIdSubquery = SpecialViewStatsUtility::getPageIdSubquery();

        $userCount = $dbr->selectField( 'view_increment',
            [ 'count(*)' ],
            [ "page_id in ({$pageIdSubquery})",
              'update_timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 day))' ]
        );

        return "'''Total views in the last 30 days:''' {$userCount}";
    }
}
