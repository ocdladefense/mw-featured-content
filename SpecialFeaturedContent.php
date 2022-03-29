

<?php

use function Mysql\select;
use Mysql\Database;
use Mysql\DbHelper;

use Ocdla\Template;


class SpecialFeaturedContent extends SpecialPage {
	
    public function __construct() {

		global $wgCaseReviewsDBtype;
		global $wgCaseReviewsDBserver;
		global $wgCaseReviewsDBname;
		global $wgCaseReviewsDBuser;
		global $wgCaseReviewsDBpassword;

        parent::__construct("FeaturedContent");

		$this->getOutput()->addModules("ext.featuredContent");

		$this->mIncludable = true;

		
		global $wgDBserver;
		global $wgDBname;
		global $wgDBuser;
		global $wgDBpassword;
		
		$dbCredentials = array(
			"host"       =>  $wgDBserver,
			"user"  	 =>  $wgDBuser,
			"password"   =>  $wgDBpassword,
			"name"       =>  $wgDBname
		);


		Database::setDefault($dbCredentials);	
			
    }




    public function execute($params) {

		// global $wgOcdlaCaseReviewsDefaultRecordLimit;


		$params = empty($params) ? "50" : $params;

		list($numRows, $field, $value) = explode("/", $params);
		// $field = "subject_1" == $field ? "subject" : $field;
		
		$output = $this->getOutput();

		$template = __DIR__ . "/templates/featured.tpl.php";

		if(!$this->including()) {

			
		}
		$this->db = wfGetDB(DB_SLAVE);

		// Define a subject query, too.
		// $query = "SELECT court, year, month, day, published_date, subject, secondary_subject FROM car WHERE COALESCE(is_draft, 0) != 1 ORDER BY year DESC, month DESC, day DESC LIMIT {$numRows}";
		$pages = $this->getRecentPages();

		// var_dump($pages);exit;
		$html = "<h2>RECENT UPDATES</h2>";//$this->getHTML();
		foreach($pages as $page) {
			$html .= "<div class='featured'>{$page->page_title}</div>";
		}
		$output->addHTML($html);
    }



	protected function getRecentPages($d1 = null) {

		$db = new Database();
		$query = "SELECT page_namespace, page_id, page_title, page_touched FROM page WHERE page_title NOT LIKE '%jpg%' AND page_title NOT LIKE  '%jpeg%' AND page_title NOT LIKE 'Case_Review%' AND page_namespace = 0 ORDER BY  page_touched DESC limit 25";

		$result = $db->query($query);
		$page_ids = array();
		foreach($result as $record) {
			$page_ids []= $record["page_id"];
		}

		return $this->loadMediaWikiPages($page_ids);
	}



	public function getHTML($days, $summaryTemplate) {
		return "<h2>Hello World!</h2>";

		$subjectTemplate = __DIR__ . "/templates/subjects.tpl.php";

		// If the page is being rendered as a standalone page, add the additional html.
		$html = !$this->including() ? $this->getSummaryLinksHTML() : "";
		
		// Opening container tags
		$html .= "<div class='car-wrapper'>";
		$html .= "<div class='car-roll'>";


		foreach($days as $key => $cars){

			$params["cars"] = $cars;

			$params = $this->preprocess($key, $cars);

			$params["subjectsHTML"] = Template::renderTemplate($subjectTemplate, $params);

			$html .= Template::renderTemplate($summaryTemplate, $params);
		}

		// Closing container tags
		$html .= "</div></div>";

		return str_replace(array("\r", "\n"), '', $html);
	}


	


	public function preprocess($key, $cars){

		global $wgOcdlaAppDomain, $wgOcdlaCaseReviewAuthor;


		return $data;
	}



	/**
	 * @function loadMediaWikiRows
	 * 
	 * SphinxSearch will return a list of docIds corresponding to MediaWiki page_ids.
	 * Use these docIds to load the corresponding MediaWiki pages.
	 */
	protected function loadMediaWikiPages($page_ids) {
		
		$mResultSet = array();

		if(empty($page_ids)) return array();


		$page_ids = !is_array($page_ids) ? array($page_ids) : $page_ids;


		// $this->total_hits = $resultSet[ 'total_found' ];

		// foreach ( $resultSet['matches'] as $id => $docinfo ) { // Comment out b/c docInfo isn't being used.
		foreach( $page_ids as $page_id ) {
			$res = $this->db->select(
				'page',
				array( 'page_id', 'page_title', 'page_namespace' ),
				array( 'page_id' => $page_id ),
				__METHOD__,
				array()
			);
			if ( $this->db->numRows( $res ) > 0 ) {
				$mResultSet[] = $this->db->fetchObject( $res );
			}
		}

		return $mResultSet;
	}
}
