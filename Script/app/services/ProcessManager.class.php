<?php

/*
   +------------------------------------------------------------------------+
   | CloudMonster - Handle multi-Cloud Storage in parallel
   | Copyright (c) 2021 PHPCloudMonster. All rights reserved.
   +------------------------------------------------------------------------+
   | @author John Antonio
   | @author_url 1: https://phpcloudmonster.com
   | @author_url 2: https://www.codester.com/johnanta
   | @author_email: johnanta89@gmail.com
   +------------------------------------------------------------------------+
*/

namespace CloudMonster\Services;

use CloudMonster\App;
use CloudMonster\models\Files;




/**
 * Class ProcessManager
 * @author John Antonio
 * @package CloudMonster\Services
 */
class ProcessManager {

    /**
     * process type
     * @var string|mixed
     */
    protected string $type;

    /**
     * current active process
     * @var array
     */
    protected array $currentProcess = [];

    /**
     * Waiting process
     * @var array
     */
    protected array $waitingProcess = [];

    /**
     * process limit
     * @var int|mixed
     */
    protected int $processLimit = 0;

    /**
     * Thread request
     * @var Thread
     */
    protected Thread $thread;

    /**
     * Status
     * @var bool
     */
    protected bool $isOk = false;

    /**
     * Thread arguments
     * @var array
     */
    protected array $args = [];


    /**
     * ProcessManager constructor.
     * @param string $type
     * @param array $args
     */
    public function __construct(string $type = 'upload', array $args = []) {
        $this->type = $type;
        $this->processLimit = App::getConfig('max_upload_process');
        $this->args = $args;
    }

    /**
     * Run process manager
     * @param false $local
     * @return bool|$this
     */
    public function run(bool $local = false):
    bool | static {
        if (!$local) {
            $this->loadData();
            $this->verifyExistProcess();
            $this->updateProcess();
        } else {
            $threadType = '';
            if($this->type == 'upload'){
                $threadType = 'run-process';
            }else if($this->type == 'remote-upload'){
                $threadType = 'remote-upload';
            }
            $this->thread = new Thread($threadType, $this->args);

            $this->thread->withId()->create();
            return $this;
        }
        return true;
    }


    /**
     * Await till complete process request
     */
    public function await() {
        if (isset($this->thread)) {
            $i = 0;
            while ($i < 3) {
                if ($this->thread->isRequestReceived()) {
                    $this->isOk = true;
                    break;
                }
                sleep(1);
                $i++;
            }
        }
    }

    /**
     * check status
     * @return bool
     */
    public function isOk():
    bool {
        return $this->isOk;
    }

    /**
     * Stop process
     */
    public function stop() {
    }

    /**
     * Update process manager
     */
    protected function updateProcess() {

        $numOfCurrentProcess = count($this->currentProcess);
        $availableProcess = $this->processLimit - $numOfCurrentProcess;
        if ($availableProcess > 0) {
            for ($i = 0;$i < $availableProcess;$i++) {
                $file = $this->getNextFile();
                if(!empty($file)){
                    $thread = new Thread(
                        'upload',
                        ['file_id' => $file['id']]
                    );
                    $thread->create();
                    usleep(1500000 - 1000000);
                    unset($thread);
                }else{
                    break;
                }
            }
        }

    }

    /**
     * Verify exist process
     */
    protected function verifyExistProcess() {
        if (!empty($this->currentProcess)) {
            $uploadProgress = new UploadProgress();
            $tmpFile = new Files();
            foreach ($this->currentProcess as $key => $val) {
                //check process is active
                if (!$uploadProgress->isActive($val['id'])) {
                    //change process status
                    if ($tmpFile->load($val['id'])) {
                        if (!$tmpFile->isAlive()) {
                            $tmpFile->canceled();
                            $tmpFile->addMsg('upload process has been canceled');
                            unset($this->currentProcess[$key]);
                            Logger::debug('[FileId#' . $val['id'] . '] upload process has been canceled due to long time inactive');
                        }
                        $tmpFile->clean();
                    }
                }
            }
            unset($uploadProgress);
            unset($tmpFile);
        }
    }

    /**
     * Load data for process
     */
    protected function loadData() {
        $tmpFile = new Files();
        //get current process
        $this->currentProcess = $tmpFile->get(['pstatus' => Files::PROCESSING, 'isUsed' => 1], [], ['id']);

        unset($tmpFile);
    }

    protected function getNextFile(){
        $tmpFile = new Files();
        $nextFile = $tmpFile->getOne([
            'pstatus' => Files::WAITING,
            'isUsed' => 0
        ], ['id' => 'ASC'], ['id']);
        unset($tmpFile);
        return $nextFile;
    }

    public static function update(){
        $tmp = new self;
        $tmp->run();
        unset($tmp);
    }

}


