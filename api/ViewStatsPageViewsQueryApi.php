<?php
class ViewStatsPageViewsQueryApi extends ApiBase {

	public function getAllowedParams() {
		return [
			'pageid' => [
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			],
		];
	}

	public function execute() {
		$params = $this->extractRequestParams();

        $pageid = $params['pageid'];
        
        $dbr = wfGetDB( DB_REPLICA );
        
        $views = ViewStatsPageViewsQueryApi::getViews( $dbr, $pageid );

		$this->getResult()->addValue( null, $this->getModuleName(), [ 'pageid' => $pageid, 'views' => $views ] );
    }
    
    private static function getViews( $dbr, $pageid ) {
        $rows = $dbr->select( 'view_increment',
            [ 'update_timestamp' ],
            'page_id = ' . intval( $pageid ),
            __METHOD__,
            [ 'ORDER BY' => 'update_timestamp ASC' ]
        );

        $views = array();

        foreach ( $rows as $row ) {
            $views[] = $row->update_timestamp;
        }

        return $views;
    }
}
