<?php


namespace CloudMonster\Drives\Dropbox;


use CloudMonster\Drives\BaseUploader;
use CloudMonster\Exception\DriveException;
use CloudMonster\Services\UploadProgress;
use Kunnu\Dropbox\DropboxFile;



class Upload extends BaseUploader{

    /**
     * Shared file link
     * @var string
     */
    protected string $sharedLink;

    /**
     * Run upload process
     * @return void
     * @throws DriveException
     */
    public function run()
    {

        $success = false;
        if($this->issetFile()){

            try{

                $filename = $this->getFilename();
                $filesize = $this->file->size;
                $dropboxFile = new DropboxFile($this->file->path);

                //start progress
                $progress = new UploadProgress($this->id, [
                    'fileSize' => $filesize
                ]);

                //make dropbox file
                $dropboxFile = $this->app->service->makeDropboxFile($dropboxFile);

                //start upload session
                $sessionId = $this->app->service->startUploadSession($dropboxFile->getFilePath(), $this->chunkSize);

                $uploaded = $this->chunkSize;
                $progress->record($uploaded) ;
//                dnd($progress->get(), true);

                //Remaining
                $remaining = $filesize - $this->chunkSize;

                while ($remaining > $this->chunkSize) {
                    //Append the next chunk to the Upload session
                    $sessionId = $this->app->service->appendUploadSession($dropboxFile, $sessionId, $uploaded, $this->chunkSize);

                    //Update remaining and uploaded
                    $uploaded = $uploaded + $this->chunkSize;
                    $remaining = $remaining - $this->chunkSize;

                    $progress->record($uploaded);
//                    dnd($progress->get(), true);

                }

                //close progress
                $progress->close();

                //Finish the Upload Session and return the Uploaded File Metadata
                $response = $this->app->service->finishUploadSession($dropboxFile, $sessionId, $uploaded, $remaining, "/" . $this->getLocation(), ['autorename' => false]);

                if(is_object($response) && !empty($response->getId())){
                    $this->uploadedFile = $response;
                    $success = true;
                    $this->enableSharedLink();
                }

            }catch (\Throwable $e){

                $this->app->addError($e->getMessage());

            }

        }
        if(!$success){

            throw new DriveException('Dropbox upload failed');

        }
    }


    protected function beforeUpload(){



    }

    protected function enableSharedLink(){

        try{
            //enable shared link
            $response = $this->app->service->postToAPI("/sharing/create_shared_link_with_settings", [
                "path" => "/" . $this->getLocation()
            ]);

            $data = $response->getDecodedBody();

            if(!empty($data['url'])){

                $this->sharedLink = $data['url'];

            }
        }catch(\Exception $e){

            $this->app->addError($e->getMessage());

        }


    }



    /**
     * Get uploaded file ID
     * @return string
     */
    public function getFileId(): string
    {
        return !empty($this->uploadedFile) ? $this->uploadedFile->getPathDisplay() : '';
    }







}