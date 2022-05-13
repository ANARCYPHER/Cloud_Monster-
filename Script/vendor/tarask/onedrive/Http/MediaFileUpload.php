<?php

namespace Tsk\OneDrive\Http;

use Tsk\OneDrive\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7;
use Tsk\OneDrive\Resources\ItemResource;

class MediaFileUpload
{
    /** @var Client */
    private $client;

    private $chunkSize;

    private $resumable;

    private $resumeUri;

    private $fileName;

    private $fileSize;

    private $folderId;

    private $uploadSession;

    private $item;

    private $start = 0;

    private $progress = 0;

    private $sessionStartedTime = '';

    public function __construct(
        Client $client,
        $fileName,
        $folderId,
        $resumable = false,
        $chunkSize = null
    )
    {
        $this->client = $client;
        $this->resumable = $resumable;
        $this->chunkSize = $chunkSize;
        $this->fileName = $fileName;
        $this->folderId = $folderId;
        $this->item = new ItemResource($this->client);
    }

    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @throws \Exception
     */
    public function nextChunk($chunk)
    {
        $end = $this->chunkSize + $this->start;
        $fileNbByte = $this->fileSize;
        if ($end > $fileNbByte) {
            $end = $fileNbByte;
        }
        $stream = Psr7\Utils::streamFor($chunk);
        $reponse = $this->item->uploadBytesToTheUploadSession($this->getUploadSession(), $stream, $this->start, $end, $this->fileSize);
        $this->start = $this->progress =  $end;


        return $reponse;
    }

    public function getProgress(){
        return $this->progress;
    }

    public function getUploadSession()
    {
        if (null === $this->uploadSession) {
            $this->uploadSession = $this->fetchUploadSession();
            $this->sessionStartedTime = microtime(true);
        }

        return $this->uploadSession;
    }

    public function getSessionStartedTime(): string
    {
        return $this->sessionStartedTime;
    }

    private function fetchUploadSession()
    {
        $uploadSession = $this->item->createUploadSession($this->fileName, $this->folderId);
        return $uploadSession;
    }
}