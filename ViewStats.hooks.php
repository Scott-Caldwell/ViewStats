<?php

class ViewStatsHooks {
	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ) {
		$updater->addExtensionTable( 'view_increment',
			__DIR__ . "/sql/view_increment.sql" );

		return true;
	}

	public static function onPageViewUpdates( WikiPage $wikipage, User $user ) {
		if ( !$user->isAllowed( 'bot' )
		  && $wikipage->exists() ) {

			$db = wfGetDB( DB_MASTER );
			$pageId = intval( $wikipage->getId() );
			$userId = intval( $user->getId() );
			$userName = $user->getName();

			$nextViews = ViewStatsHooks::getNextViews( $db, $pageId );

			$db->onTransactionIdle( function () use ( $db, $pageId, $userId, $userName, $nextViews ) {
				$db->insert( 'view_increment',
					[ 'page_id'      => $pageId,
					  'user_id'      => $userId,
					  'user_name'    => $userName,
					  'total_views'  => $nextViews]
				);
			});
		}
	}

	private static function getNextViews( $db, $pageId ) {
		$nextViews_v = intval( $db->selectField( 'view_increment',
			'coalesce(max(total_views), 0) + 1',
			'page_id = ' . $pageId
		));

		if ( $db->tableExists( 'hit_counter' ) ) {
			$nextViews_h = intval( $db->selectField( 'hit_counter',
				'coalesce(max(page_counter), 0)',
				'page_id = ' . $pageId
			));

			if ( $nextViews_h >= $nextViews_v ) {
				return $nextViews_h;
			}
		}

		return $nextViews_v;
	}
}
