<?php


namespace CloudMonster\Drives\Doodstream;



use CloudMonster\Core\CURL;
use CloudMonster\Drives\BaseUploader;
use CloudMonster\Services\UploadProgress;
use Exception;


class Upload extends BaseUploader {


    /**
     * Run upload process
     * @throws Exception
     */
    public function run()
    {

        $success = false;

        if($this->issetFile()){

            //attempt to fetch upload url
            if($url = $this->fetchUploadLink()){

                //create curl file
                if (function_exists('curl_file_create')) { // php 5.5+
                    $cFile = curl_file_create($this->file->path, '', $this->getFilename());
                } else { //
                    $cFile = '@' . realpath($this->file->path);
                }

                //post data
                $postData = [
                    'file' => $cFile,
                    'fld_id' => $this->getParentId(),
                    'api_key' => $this->app->getAuthData('key')
                ];

                //attempt to upload
                $success = $this->upload($url, $postData);


            }else{

                $this->app->addError('Unable to fetch upload link');

            }


        }else{
            $this->app->addError('File not set');
        }

        if(!$success){

            throw new Exception('upload failed');

        }


    }

    protected function upload(string $url, array $postData): bool
    {

        $success = false;


        $curl = new Curl;
        $curl->timeout = 1000;

        $curl->progress(new UploadProgress($this->id, [
            'fileSize' => $this->file->size,
            'keepUpload' => true,
            'chunkSize' => $this->chunkSize
        ]));

        $curl->post($url, $postData)->exec();

        if($curl->isOk()){
            $results = $curl->getResults();
            if($results['msg'] == 'OK'){

                if(isset($results['result'][0])){

                    $this->uploadeFile = $results['result'][0];
                    $success = true;

                }

            }else{

                $this->app->addError($results['msg']);

            }
        }else{

            $this->app->addError($curl->getError());

        }

        $curl->close();

        return $success;
    }

    protected function fetchUploadLink(): string
    {


        $results = $this->app->call(
            'GET',
            '/upload/server',
            []
        );

        return !empty($results) ? $results : '';

    }



    /**
     * Get uploaded file ID
     * @return string
     */
    public function getFileId(): string
    {
        return $this->uploadeFile['filecode'] ?? '';
    }




}