<?php





class MediaWikiPages {
    /**
	 * @function loadMediaWikiRows
	 * 
	 * SphinxSearch will return a list of docIds corresponding to MediaWiki page_ids.
	 * Use these docIds to load the corresponding MediaWiki pages.
	 */
	public static function load($page_ids) {
		
		$db = wfGetDB(DB_SLAVE);

		$mResultSet = array();

		if(empty($page_ids)) return array();


		$page_ids = !is_array($page_ids) ? array($page_ids) : $page_ids;


		// $this->total_hits = $resultSet[ 'total_found' ];

		// foreach ( $resultSet['matches'] as $id => $docinfo ) { // Comment out b/c docInfo isn't being used.
		foreach( $page_ids as $page_id ) {
			$res = $db->select(
				'page',
				array( 'page_id', 'page_title', 'page_namespace' ),
				array( 'page_id' => $page_id ),
				__METHOD__,
				array()
			);
			if ( $db->numRows( $res ) > 0 ) {
				$mResultSet[] = $db->fetchObject( $res );
			}
		}

		return $mResultSet;
	}

}