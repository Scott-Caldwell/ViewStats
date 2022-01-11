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

class SpecialViewStatsUtility {

    private function __construct() {}

    public static function getPageIdSubquery() {
        global $wgViewStatsHiddenNamespaces;
        global $wgViewStatsHiddenUserIds;
        global $wgViewStatsHiddenUserNames;

        $query = 'select page_id from page where 1 = 1';

        if ( !empty( $wgViewStatsHiddenNamespaces ) ) {
            $namespaces = join( ',', $wgViewStatsHiddenNamespaces );
            $query = "{$query} and page_namespace not in ({$namespaces})";
        }

        if ( !empty( wgViewStatsHiddenUserIds ) ) {
            $userIds = join ( ',', $wgViewStatsHiddenUserIds );
            $query = "{$query} and user_id not in ({$userIds})";
        }

        if ( !empty( wgViewStatsHiddenUserNames ) ) {
            $userNames = join ( ',', $wgViewStatsHiddenUserNames );
            $query = "{$query} and user_name not in ({$userNames})";
        }

        return $query;
    }
}
