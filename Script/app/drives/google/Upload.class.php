<?php


namespace CloudMonster\Drives\Google;


use CloudMonster\Helpers\Logger;
use CloudMonster\Drives\BaseUploader;
use CloudMonster\Exception\DriveException;
use CloudMonster\Services\UploadProgress;
use Google_Http_MediaFileUpload;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;


/**
 * Class Upload
 * @package CloudMonster\Drives\Google
 */

class Upload extends BaseUploader
{


    /**
     * Google http media class
     * @var Google_Http_MediaFileUpload
     */
    private Google_Http_MediaFileUpload $media;

    /**
     * Uploaded file
     * @var Google_Service_Drive_DriveFile
     */
    protected Google_Service_Drive_DriveFile $uploadedFile;



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
            throw new DriveException('GDriveUpload: Upload file is not set');
        }
        if(!$this->isOk){

            throw new DriveException('GDriveUpload: ' .  $this->getError());

        }
    }

    /**
     * Do something before upload
     * @throws DriveException
     */
    private function beforeUpload() : void{

        //init media file
        try{
            $params = !empty($this->getParentId()) ? ['parents'=>[$this->getParentId()]] : [];
            $file = new Google_Service_Drive_DriveFile($params);
            $file->name = $this->getFilename();
            $this->app->client->setDefer(true);
            $request = $this->app->service->files->create($file);
            $this->media = new Google_Http_MediaFileUpload($this->app->client, $request, $this->file->mimeType, null, true, $this->chunkSize);
            $this->media->setFileSize($this->file->size);
        }catch(\Throwable $e){
            Logger::debug('GDriveUpload: ' . $e->getMessage());
            throw new DriveException('GDriveUpload: Unable to setup gdrive media file for upload');
        }


    }

    /**
     * Do something after upload
     */
    private function afterUpload() : void{
        $this->app->client->setDefer(false);

        if(!empty($this->getFileId())){

            //enable shared link
            $data = ['role' => 'reader', 'type' => 'anyone'];
            $permission = new Google_Service_Drive_Permission($data);
            $this->app->service->permissions->create($this->getFileId(), $permission, array('fields' => 'id', 'sendNotificationEmail' => false));

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
            if(!empty($handle)){

                //start upload progress
                $progress = new UploadProgress($this->getId(), [
                    'fileSize' => $this->file->size
                ]);

                while (!feof($handle)) {

                    //read chunk and upload
                    $chunk = $this->readChunk($handle);
                    $status = $this->media->nextChunk($chunk);

                    //record progress
                    $progress->record($this->media->getProgress());

                }

                //close progress
                $progress->close();
                unset($progress);

                //close opened file
                fclose($handle);

                if($status){
                    $this->uploadedFile = $status;
                    $this->isOk = true;

                }else{
                    Logger::debug('GDriveUpload: Upload status failed');
                }
            }else{
                Logger::debug('GDriveUpload: Unable to open File:: ' . $this->file->path);
            }
        }catch (\Throwable $e){
            throw new DriveException('GDriveUpload: ' . $e->getMessage());
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
        return !empty($this->uploadedFile) ? $this->uploadedFile->id : '';
    }


}