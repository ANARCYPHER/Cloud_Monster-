<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Request;
use CloudMonster\Helpers\Help;
use CloudMonster\CPanel;

/**
 * Class Search
 * @author John Anta
 * @package CloudMonster\CPanel
 */
class Search extends CPanel {


    /**
     * Default action
     * @return void
     */
    public function init(){


        $searchTerm = Request::get('term');

        $buckets = new \CloudMonster\Models\Buckets();
        $files = new \CloudMonster\Models\Files();

        //search in buckets
        $results1 = $buckets
            ->search('name', $searchTerm)
            ->get([],['name','ASC'], ['id','name']);

        //search in file slug
        $results2 = $files
            ->search('slug', $searchTerm)
            ->get([],[],['id','bucketId','slug as name.skip']);

        //search in file code
        $results3 = $files
            ->search('code', $searchTerm, Help::extractData($results2, 'id'))
            ->get([],[],['id','bucketId','code as name.skip']);


        $finalResults = array_merge($results1, $results2, $results3);
        $finalResults = Help::appendFileType($finalResults);

        $this->ajaxResponse($finalResults, true);

    }

}