<?php

namespace ViewStats;

use DBError;
use DeferrableUpdate;

class ViewStatsUpdate implements DeferrableUpdate {
    protected $pageId;
    protected $userId;

	public function __construct( $pageId, $userId ) {
        $this->pageId = intval( $pageId );
        $this->userId = intval( $userId );
	}

	public function doUpdate() {
		$dbw = wfGetDB( DB_MASTER );
        $pageId = $this->pageId;
        $userId = $this->userId;

        $dbw->onTransactionIdle( function () use ( $dbw, $pageId, $userId ) {
            $currentViews = $dbw->selectField( 'view_increment',
                'max(total_views)',
                [ 'page_id' => $pageId ]
            );

            $dbw->insert( 'view_increment',
                [ 'page_id'      => $pageId,
                  'user_id'      => $userId,
                  'total_views'  => intval( $currentViews ) + 1]
            );
        });

        return;
	}
}
