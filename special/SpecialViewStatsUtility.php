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

class SpecialViewStatsUtility {

    private function __construct() {}

    public static function getPageIdSubquery() {
        global $wgViewStatsHiddenNamespaces;

        $query = 'select page_id from page';

        if ( !empty( $wgViewStatsHiddenNamespaces ) ) {
            $namespaces = join( ',', $wgViewStatsHiddenNamespaces );
            $query .= " where page_namespace not in ({$namespaces})";
        }

        return $query;
    }

    public static function getViewIncrementConditions($interval = '') {
        $pageIdSubquery = $this->getPageIdSubquery();
        $conditions = [ "view_increment.page_id not in ({$pageIdSubquery})" ];

        $userIdCondition = $this->getUserIdCondition();
        if ( !empty( $userIdCondition ) ) {
            $conditions[] = $userIdCondition;
        }

        $userNameCondition = $this->getUserNameCondition();
        if ( !empty( $userNameCondition ) ) {
            $conditions[] = $userNameCondition;
        }

        if ( !empty( $interval ) ) {
            $conditions[] = "view_increment.update_timestamp > timestamp(date_sub(now(), interval {$interval}))";
        }

        return $conditions;
    }

    private static function getUserIdCondition() {
        global $wgViewStatsHiddenUserIds;

        if ( empty( $wgViewStatsHiddenUserIds ) ) {
            return "";
        }
        
        $userIds = join ( ',', $wgViewStatsHiddenUserIds );
        return "view_increment.user_id not in ({$userIds})";
    }

    private static function getUserNameCondition() {
        global $wgViewStatsHiddenUserNames;

        if ( empty( $wgViewStatsHiddenUserNames ) ) {
            return "";
        }

        $quotedNames = array_map( function ( $x ) { return addslashes( "'{$x}'" ); }, $wgViewStatsHiddenUserNames);
        $userNames = join ( ',', $quotedNames );
        return "view_increment.user_name not in ({$userNames})";
    }
}
