<?php


namespace CloudMonster\Drives\Onedrive;


use CloudMonster\Helpers\Logger;
use CloudMonster\Drives\BaseUploader;
use CloudMonster\Exception\DriveException;
use CloudMonster\Services\UploadProgress;
use GuzzleHttp\Exception\ClientException;
use Tsk\OneDrive\Http\MediaFileUpload;



/**
 * Class Upload
 * @package CloudMonster\Drives\Onedrive
 */

class Upload extends BaseUploader
{


    /**
     * Onedrive http media class
     * @var MediaFileUpload
     */
    private MediaFileUpload $media;

    protected string $sharedLink;


    /**
     * Run upload process
     * @throws DriveException
     */
    public function run()
    {
        $this->setFile();
        if($this->issetFile()){
            try{
                $this->beforeUpload();
                $this->uploadChunks();
                $this->afterUpload();
            }catch(DriveException $e){
                throw $e;
            }
        }else{
            throw new DriveException('OnedriveUpload: Upload file is not set');
        }
        if(!$this->isOk){

            throw new DriveException('OnedriveUpload: ' . $this->getError());

        }
    }

    /**
     * Do something before upload
     * @throws DriveException
     */
    private function beforeUpload() : void{

        //init media file
        try{
            $folderId = $this->getParentId();
            if(empty($folderId)) $folderId = 'root';
            $this->media = new MediaFileUpload($this->app->client, $this->getFilename(), $folderId, true, $this->chunkSize);
            $this->media->setFileSize($this->file->size);
        }catch(ClientException $e){
            Logger::debug('OnedriveUpload: ' . $e->getMessage());
            throw new DriveException('OnedriveUpload: Unable to setup onedrive media file for upload');
        }


    }

    /**
     * Do something after upload
     */
    private function afterUpload() : void{

        if(!empty($this->getFileId())){

            try{

                //enable shared link
                $resp  = $this->app->service->items->shareLink($this->getFileId(), 'view', 'anonymous');

                $this->sharedLink = $resp->getLink()->getWebUrl();

            }catch(ClientException $e){

                Logger::debug('OneDriveUpload: ' . $e->getMessage());
            }



        }

    }

    /**
     *  Attempt to upload file as chunks
     * @throws DriveException
     */
    private function uploadChunks() : void{
        try{

            $handle = @fopen($this->file->path, 'rb');
            $status = false;
            $uploaded = 0;
            if(!empty($handle)){
                $progress = new UploadProgress($this->getId(), [
                    'fileSize' => $this->file->size
                ]);
                $progress->wait();
                while (!feof($handle)) {
                    $chunk = $this->readChunk($handle);
                    $status = $this->media->nextChunk($chunk);
                    $uploaded += strlen($chunk);
                    if($uploaded == $this->chunkSize){
//                        dnd('STARTED::: ', true);
                        $progress->start($this->media->getSessionStartedTime());
                    }
                    $progress->record($uploaded);
//                    dnd($progress->get(), true);
                }

                $progress->close();
                fclose($handle);
                if(isset($status['id']) && !empty($status['id'])){
                    $this->uploadedFile = $status;
                    $this->isOk = true;
                    Logger::info('File uploaded to onedrive successfully');
                }else{
                    Logger::debug('Upload status failed');
                }
            }else{
                Logger::debug('Unable to open File:: ' . $this->file->path);
            }
        }catch (\Throwable $e){
            $error = $this->app->decodeError($e->getMessage());
            if(empty($error)) Logger::debug('OnedriveUpload: ' . $e->getMessage());
            throw new DriveException('OnedriveUpload: ' . $error);
        }
    }

    /**
     * Read each chunk
     * @param $handle
     * @return string
     */
    private function readChunk($handle): string
    {
        $byteCount = 0;
        $giantChunk = '';
        while (!feof($handle)) {
            $chunk = fread($handle, min($this->chunkSize - $byteCount, 8192));
            $byteCount += strlen($chunk);
            $giantChunk .= $chunk;
            if ($byteCount >= $this->chunkSize) {
                return $giantChunk;
            }
        }
        return $giantChunk;
    }

    /**
     * Get uploaded file ID
     * @return string
     */
    public function getFileId(): string
    {
        return !empty($this->uploadedFile) ? $this->uploadedFile['id'] : '';
    }


}