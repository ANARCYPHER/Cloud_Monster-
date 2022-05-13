<?php


namespace CloudMonster\CPanel;

use CloudMonster\Models\ProcessTracker;
use CloudMonster\Session\Thread;
use CloudMonster\Helpers\Help;
use CloudMonster\CPanel;

/**
 * Class Process
 * @author John Anta
 * @package CloudMonster\CPanel
 */
class Process extends CPanel {


    /**
     * Default action
     * @return void
     */
    public function init(){


        $this->view->setTitle('system process');

        $this->view->render('process');

    }

    /**
     * date request action
     */
    protected function data(){


        $tmpThreadData = [];
        $threadMemoryUsage = 0;

        Thread::start();
        $threadData = Thread::getAll();
        Thread::destroy();


        if(!empty($threadData)){

            foreach ($threadData as  $thread){

                if($thread->isHit()){

                    $tData = $thread->get();
                    $tMemory= $tData['memoryUsage'] ?? 0;
                    $threadMemoryUsage += $tMemory;
                    $tCreationDate = $tRunTime = 'unknown';

                    $dateObj = $thread->getCreationDate();

                    if(!empty($dateObj)){

                        $tCreationDate = $dateObj->date;
                        $tRunTime  = Help::formatSec(strtotime(Help::timeNow()) - strtotime($tCreationDate));


                    }

                    array_push($tmpThreadData, [
                        'pid' => $thread->getKey(),
                        'memoryUsage' => Help::formatSizeUnits($tMemory),
                        'creationDate' => Help::formatDT($tCreationDate),
                        'runTime' => $tRunTime
                    ]);


                }

            }

        }


        $uploadTracker = new ProcessTracker();
        $uploadProcessSummary = $uploadTracker->getProcessSummary();


        $results = [
            'threads'  => [
                'active' => count($threadData),
                'memoryUsage' => Help::formatSizeUnits($threadMemoryUsage),
                'data' => $tmpThreadData
            ],
            'upload' => $uploadProcessSummary
        ];


        $this->jsonResponse($results);


    }




}