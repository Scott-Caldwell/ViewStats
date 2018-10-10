<?php

class SpecialPageViews extends SpecialPage {
	function __construct() {
		parent::__construct( 'PageViews', '', false, false, '', true );
	}
    
    function execute( $par ) {
        $request = $this->getRequest();
        $output = $this->getOutput();
        $this->setHeaders();
        
        $pageid = $request->getText( 'pageid' );

        $text = SpecialPageViews::buildText( $pageid );
		
		$output->addWikiText( $text );
    }

    private static function buildText( $pageid ) {
        return "<graph>{
            \"version\": 2,
            \"width\": 800,
            \"height\": 400,
            \"padding\": {\"top\": 10, \"left\": 30, \"bottom\": 30, \"right\": 10},
            \"data\": [{
                \"name\": \"table\",
                \"url\": \"wikiapi:///api.php?action=pageviews&format=json&pageid=" . $pageid . "\",
                \"format\": {
                  \"type\": \"json\",
                  \"property\": \"pageviews.views\",
                  \"parse\": {
                    \"viewCount\": \"number\",
                    \"dateViewed\": \"date:'%Y-%m-%d'\"
                  }
                }
              }
            ],
            \"scales\": [
              {
                \"name\": \"x\",
                \"type\": \"time\",
                \"range\": \"width\",
                \"domain\": {\"data\": \"table\", \"field\": \"dateViewed\"},
                \"nice\": \"day\"
              },
              {
                \"name\": \"y\",
                \"type\": \"linear\",
                \"range\": \"height\",
                \"domain\": {\"data\": \"table\", \"field\": \"viewCount\"}
              }
            ],
            \"axes\": [{
                \"type\": \"x\",
                \"scale\": \"x\",
                \"orient\": \"bottom\",
                \"format\": \"%b %-d, %Y\"
              }, {
                \"type\": \"y\",
                \"scale\": \"y\",
                \"orient\": \"left\"
              }
            ],
            \"marks\": [{
                \"type\": \"line\",
                \"from\": {
                  \"data\": \"table\"
                },
                \"properties\": {
                  \"enter\": {
                    \"x\": { \"scale\": \"x\", \"field\": \"dateViewed\" },
                    \"y\": { \"scale\": \"y\", \"field\": \"viewCount\" },
                    \"stroke\": {\"value\": \"#36c\"},
                    \"strokeWidth\": {\"value\": 3},
                    \"interpolate\": {\"value\": \"monotone\"}
                  }
                }
              }, {
                \"type\": \"area\",
                \"from\": {
                  \"data\": \"table\"
                },
                \"properties\": {
                  \"enter\": {
                    \"x\": {\"scale\": \"x\",\"field\": \"dateViewed\"},
                    \"y\": {\"scale\": \"y\",\"value\": 0},
                    \"y2\": {\"scale\": \"y\",\"field\": \"viewCount\"},
                    \"fill\": {\"value\": \"#36c\"},
                    \"fillOpacity\": {\"value\": 0.35},
                    \"interpolate\": {\"value\": \"monotone\"}
                  }
                }
              }
            ]
          }</graph>";
    }
}
