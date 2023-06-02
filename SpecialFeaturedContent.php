<?php

use function Mysql\select;
use Mysql\Database;
use Mysql\DbHelper;
use Ocdla\Template;


require "src/MediaWikiPages.php";

class SpecialFeaturedContent extends SpecialPage {
	
    public function __construct() {


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

		global $wgOcdlaFeaturedContentEditor,

		$wgOcdlaFeaturedContentTitle;

		$wgOcdlaFeaturedContentTitle = !empty($wgOcdlaFeaturedContentTitle) ? $wgOcdlaFeaturedContentTitle : "RECENT LOD UPDATES";

		$params = empty($params) ? "50" : $params;

		list($numRows, $field, $value) = explode("/", $params);
		// $field = "subject_1" == $field ? "subject" : $field;
		
		$output = $this->getOutput();

		$path = __DIR__ . "/templates/featured";
		$tpl = new Template($path);

		if(!$this->including()) {}



		$pages = $this->getRecentPages();

		// var_dump($pages);exit;
		$html = "<h2>$wgOcdlaFeaturedContentTitle</h2>";
		$counter = 0;
		foreach($pages as $page) {

			$html .= $tpl->render(array("page"=>$page));
			
			if($counter++ == 0) {
				$title 		= Title::makeTitle( $page->page_namespace, $page->page_title );
				$revision 	= Revision::newFromTitle( $title );
				$text 		= $revision->getText();	
			}
		}

		$html = str_replace(array("\r", "\n"), '', $html);
		$output->addHTML("<ul>{$html}</ul>");
    }



	protected function getRecentPages() {

		$db = new Database();
		$query = "SELECT page_namespace, page_id, page_title, page_touched FROM page WHERE page_namespace = 0 AND page_title NOT IN('Main_Page','Welcome_to_The_Library') AND page_title NOT LIKE '%jpg%' AND page_title NOT LIKE '%jpeg%' AND page_title NOT LIKE 'Case_Review%' AND page_title NOT LIKE '%Local%' ORDER BY page_touched DESC LIMIT 25";

		$result = $db->query($query);
		$page_ids = array();
		foreach($result as $record) {
			$page_ids []= $record["page_id"];
		}

		$rows = MediaWikiPages::load($page_ids);

		return $rows;
	}







}
