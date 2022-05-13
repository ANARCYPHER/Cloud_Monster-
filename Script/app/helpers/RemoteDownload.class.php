<?php

namespace CloudMonster\Helpers;


use CloudMonster\Core\CURL;
use CloudMonster\Services\UploadProgress;


/**
 * Class RemoteDownload
 * @author John Anta
 * @package CloudMonster\Helpers
 */
class RemoteDownload{

    /**
     * Target url
     * @var string
     */
    protected string $url;

    /**
     * File save directory
     * @var string
     */
    protected string $dir = '';

    /**
     * remote file info
     * @var array
     */
    protected array $fileInfo = [
        'filename' => '',
        'mime' => '',
        'ext' => '',
        'size' => 0
    ];

    /**
     * Error message
     * @var string
     */
    protected string $error = '';

    protected string $destPath = '';

    protected string $folder = '';

    public ?CURL $curl;


    /**
     * RemoteDownload constructor.
     * @param string $url
     * @param bool $loadFileInfo
     */
    public function __construct(string $url, bool $loadFileInfo = true){

        if(Help::isUrl($url)){

            $this->url = $url;
            $this->dir = Help::storagePath('tmp');

            $this->curl = new CURL();

            if($loadFileInfo && !$this->loadInfo()){
                $this->error = 'Unable to load file info';
            }

        }else{

            $this->error = 'Invalid URL';

        }

    }

    public function setFilename($filename): static
    {
        $this->fileInfo['filename'] = $filename;
        return $this;
    }

    public function setDestPath($path): static
    {
        $this->destPath = $path;
        return $this;
    }

    public function setFolder($fld): static
    {
        $this->folder = $fld;
        return $this;
    }

    /**
     * Load remote file into
     * @return int
     */
    public function loadInfo() : int
    {

        $success = false;

        $headers = get_headers($this->url, true);
        if(substr($headers[0], 9, 3) == 200){
            $size = $headers['Content-Length'] ?? 0;
            $contentType = $headers['Content-Type'] ?? '';
        }

        if(empty($size) || empty($contentType)){
            $curl = new CURL();
            $curl->noBody = $curl->header = true;
            $curl->get($this->url)->exec();
            $size = $curl->getInfo(CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            $contentType = $curl->getInfo(CURLINFO_CONTENT_TYPE);
            $curl->close();
        }

        if(!empty($size) && !empty($contentType)){

            $ext = Help::mime2ext($contentType);

            if(!empty($ext)){

                $this->fileInfo['size'] = $size;
                $this->fileInfo['ext'] = $ext;
                $this->fileInfo['mime'] = $contentType;

                if(empty($this->fileInfo['filename'])){
                    $this->fileInfo['filename'] = Help::random(15) . '.' . $ext;
                }

                $success = true;

            }

        }

        return $success;

    }


    /**
     * Get destination file path
     * @return string
     */
    private function getDestination(): string
    {
        if(empty($this->destPath)){
            $filepath = $this->getTargetDir() . '/' . $this->getFileInfo('filename');
        }else{
            $filepath = $this->destPath;
        }

        if(! is_dir($filepath)){
            return $filepath;
        }
        return false;
    }

    public function getTargetDir(): string
    {
        $dir = $this->dir . $this->folder;
        if(! file_exists($dir)){
            if(mkdir($dir, 0777, true)){
                return $dir;
            }
        }
        return $dir;
    }

    public function recordProgress(int $id){
        $this->curl->progress(new UploadProgress("b_$id", [
            'fileSize' => (int) $this->getFileInfo('size'),
            'keepUpload' => true,
            'chunkSize' => $this->curl->bufferSize
        ]), 'CurlDownloadProgress');
    }

    public static function getProgress(int $id): array
    {
        try{
            $progressData = UploadProgress::call("b_$id")->get();
            if(is_array($progressData)){
                return $progressData;
            }
        }catch(\Exception $e){

        }

        return [];
    }

    /**
     * Attempt to save file
     * @return bool
     */
    public function saveFile(): bool
    {

        if($desti = $this->getDestination()){

            $fp = @fopen($desti, 'wb');


            if(!empty($fp)){


                $this->curl->file = $fp;
                $this->curl->timeout = 0;

                $this->curl->get($this->url)->exec();

                if($this->curl->isOk()){

                    $tmpFileSize = filesize($this->getDestination());
                    if($tmpFileSize < ($this->getFileInfo('size')) - 5){

                        $this->error = 'Unable to download remote file';

                    }


                }else{

                    $this->error = $this->curl->getError();

                }


            }else{

                $this->error = 'Unable to open tmp file for write';

            }

        }else{

            $this->error = 'Destination file is invalid';

        }



        return $this->isOk();

    }

    /**
     * check status
     * @return bool
     */
    public function isOk(): bool
    {
        return empty($this->error);
    }

    /**
     * get error
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }


    /**
     * Get file info
     * @param string $t
     * @return mixed
     */
    public function getFileInfo(string $t = ''): mixed
    {
        if(!empty($t)){
            if(isset($this->fileInfo[$t])){
                return $this->fileInfo[$t];
            }
        }else{
            return  $this->fileInfo;
        }
        return '';
    }




    public function __destruct(){
        $this->curl = null;
    }



}