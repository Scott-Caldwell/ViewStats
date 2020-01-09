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

class ViewStatsHooks {

    public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ) {
        $updater->addExtensionTable( 'view_increment',
            __DIR__ . '/sql/view_increment.sql' );

        return true;
    }

    public static function onPageViewUpdates( WikiPage $wikipage, User $user ) {
        if ( $user->isAllowed( 'bot' ) || !$wikipage->exists() ) {
            return;
        }

        $dbw = wfGetDB( DB_MASTER );
        $pageId = intval( $wikipage->getId() );
        $userId = intval( $user->getId() );
        $userName = $user->getName();

        $dbw->onTransactionIdle( function () use ( $dbw, $pageId, $userId, $userName ) {
            $dbw->begin( __METHOD__ );

            $nextViews = ViewStatsHooks::getNextViews( $dbw, $pageId );

            $dbw->insert( 'view_increment', [
                'page_id'     => $pageId,
                'user_id'     => $userId,
                'user_name'   => $userName,
                'total_views' => $nextViews
            ]);
            
            $dbw->commit( __METHOD__ );
        });
    }

    private static function getNextViews( $dbr, $pageId ) {
        $nextViews_v = intval( $dbr->selectField( 'view_increment',
            'coalesce(max(total_views), 0) + 1',
            "page_id = {$pageId}"
        ));

        if ( $dbr->tableExists( 'hit_counter' ) ) {
            $nextViews_h = intval( $dbr->selectField( 'hit_counter',
                'coalesce(max(page_counter), 0)',
                "page_id = {$pageId}"
            ));

            if ( $nextViews_h >= $nextViews_v ) {
                return $nextViews_h;
            }
        }

        return $nextViews_v;
    }

    public static function onSkinTemplateNavigation( &$sktemplate, &$links ) {
        $title = $sktemplate->getTitle();
        $namespace = $title->getNamespace();

        if ( $title->exists() && $namespace != NS_SPECIAL ) {

            $page = WikiPage::factory( $title );
            $pageid = $page->getId();
            $special = Title::newFromText( 'PageViews', NS_SPECIAL )->getInternalURL( [ 'pageid' => $pageid ] );
            
            $links['views']['PageViews'] = [
                'class' => false,
                'text'  => wfMessage( 'tabpageviews' ),
                'href'  => $special
            ];
        }

        return true;
    }
}
