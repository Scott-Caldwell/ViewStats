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

class SpecialViewStatsUtility {

    private function __construct() {}

    public static function getPageIdSubquery() {
        global $wgViewStatsHiddenNamespaces;

        $query = 'select page_id from page';

        if ( empty( $wgViewStatsHiddenNamespaces ) ) {
            return $query;
        }

        $namespaces = join( ',', $wgViewStatsHiddenNamespaces );

        return "{$query} where page_namespace not in ({$namespaces})";
    }
}
