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
        
        $views = ViewStatsPageViewsQueryApi::getViewsByWeek( $dbr, $pageid );

		$this->getResult()->addValue( null, $this->getModuleName(), [ 'pageid' => $pageid, 'views' => $views ] );
    }
    
    private static function getViewsByWeek( $dbr, $pageid ) {
        $rows = $dbr->select( 'view_increment',
            [ 'date(subdate(update_timestamp, dayofweek(update_timestamp) - 1)) as date', 'count(*) as viewcount' ],
            'page_id = ' . intval( $pageid ),
            __METHOD__,
            [ 'GROUP BY' => 'date(subdate(update_timestamp, dayofweek(update_timestamp) - 1))' ]
        );

        $views = array();
        $dates = array();

        foreach ( $rows as $row ) {
            $dates[] = new DateTimeImmutable( $row->date );
            $views[] = new ViewCount( new DateTimeImmutable( $row->date ), intval( $row->viewcount ) );
        }

        $minDate = min( $dates );
        $maxDate = max( $dates );
        $datePeriod = new DatePeriod( $minDate, new DateInterval( 'P1W' ), $maxDate);

        foreach ( $datePeriod as $key => $date ) {
            if ( !in_array( $date, $dates ) ) {
                $views[] = new ViewCount( $date, 0 );
            }
        }

        usort( $views, 'ViewCount::compare' );

        return $views;
    }
}

class ViewCount {

    private $internalDate;
    public $dateViewed;
    public $viewCount;

    function __construct(DateTimeImmutable $dt, $v) {
        $this->internalDate = $dt;
        $this->dateViewed = $dt->format("Y-m-d");
        $this->viewCount = $v;
    }

    public static function compare( ViewCount $first, ViewCount $second ) {
        if ( $first->internalDate < $second->internalDate ) {
            return -1;
        }

        if ( $first->internalDate > $second->internalDate ) {
            return 1;
        }

        return 0;
    }
}
