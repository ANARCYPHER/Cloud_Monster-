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

use CloudMonster\Core\CURL;
use CloudMonster\Helpers\Help;

/**
 * Class Thread
 * @author John Antonio
 * @package CloudMonster\Services
 */
class Thread {

    /**
     * Thread type
     * @var string
     */
    protected string $type = '';

    /**
     * Thread data
     * @var array
     */
    protected array $data = [];

    /**
     * Target url
     * @var string
     */
    protected string $url = '';

    /**
     * Base action
     * @var string
     */
    protected string $baseAction = '';

    /**
     * Uniq ID
     * @var string
     */
    protected string $id;

    /**
     * Status
     * @var bool
     */
    protected bool $isOk = false;

    /**
     * Thread timeout
     * @var int
     */
    protected int $timeout = 5;


    /**
     * Thread constructor.
     * @param string $type
     * @param array $data
     */
    public function __construct(string $type, array $data = []) {
        $this->type = $type;
        $this->data = $data;
        $this->baseAction = 'new-thread';
    }

    /**
     * Send with ID
     * @return $this
     */
    public function withId(): static {
        $this->id = Help::random(7);
        $this->bindData(['id' => $this->id]);
        return $this;
    }

    /**
     * bind data
     * @param array $data
     */
    protected function bindData(array $data = []) {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Create thread
     * @return bool
     */
    public function create(): bool {

        $success = false;

        $this->setUrl();
        $headers = ['X-Token:' . THREAD_SECRET_TOKEN];
        $curl = new CURL();

        $curl->setHeaders($headers);
        $curl->timeout = $this->timeout;
        $curl->post($this->url, http_build_query($this->data))->exec();
        $curl->close();

        unset($curl);

        return $success;

    }

    /**
     * Set URL
     */
    private function setUrl() {
        //set url
        $this->url = siteurl() . '/' . $this->baseAction . '/' . $this->type;
    }

    /**
     * verify thread request
     */
    public static function isVerified() {

    }

    /**
     * Get verification file
     * @param $reqId
     * @return string
     */
    public static function getReqFile($reqId): string
    {
        return Help::cleanDS(Help::storagePath('tmp') . '/thread~' . $reqId . '.txt');
    }

    /**
     * Request received
     * @param $reqId
     */
    public static function requestReceived($reqId) {
        @file_put_contents(self::getReqFile($reqId), 'received');
    }

    /**
     * Is request received
     * @return bool
     */
    public function isRequestReceived(): bool {
        $tmpFile = $this::getReqFile($this->id);
        if (file_exists($tmpFile)) {
            @unlink($tmpFile);
            return true;
        }
        return false;
    }

    /**
     * Wait till receive request
     */
    public function await() {
        if (!$this->isOk) {
            $i = 0;
            while ($i < 3) {
                if ($this->isRequestReceived()) {
                    $this->isOk = true;
                    break;
                }
                sleep(1);
                $i++;
            }
        }
    }

    /**
     * Status
     * @return bool
     */
    public function isOk(): bool {
        return $this->isOk;
    }


}
