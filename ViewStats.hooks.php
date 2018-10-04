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

			$nextViews = ViewStatsHooks::getNextViews( $db, $pageId );

			$db->onTransactionIdle( function () use ( $db, $pageId, $userId, $nextViews ) {
				$db->insert( 'view_increment',
					[ 'page_id'      => $pageId,
					  'user_id'      => $userId,
					  'total_views'  => $nextViews]
				);
			});
		}
	}

	private static function getNextViews( Wikimedia\Rdbms\Database $db, int $pageId ) {
		$nextViews = $db->selectField( 'view_increment',
			'coalesce(max(total_views), 0) + 1',
			'page_id = ' . $pageId
		);

		return intval( $nextViews );
	}
}
