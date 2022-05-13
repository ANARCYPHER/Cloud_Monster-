<?php


namespace CloudMonster\Drives\Streamlare;



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
                    'file' => $cFile
                ];

                $postData = array_merge($postData, $this->app->getAuthData());
                if(!empty($this->getParentId())){
                    $postData['folder'] = $this->getParentId();
                }

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
            if($results['message'] == 'OK'){

                if(isset($results['result'])){

                    $this->uploadeFile = $results['result'];
                    $success = true;

                }

            }else{

                $this->app->addError($results['message']);

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
            '/file/upload/generate',
            [
                'query' => []
            ]
        );


        return !empty($results) ? $results : '';

    }



    /**
     * Get uploaded file ID
     * @return string
     */
    public function getFileId(): string
    {
        return $this->uploadeFile['hashid'] ?? '';
    }




}