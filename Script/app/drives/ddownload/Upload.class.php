<?php


namespace CloudMonster\Drives\Ddownload;



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
            $urlData = $this->fetchUploadLink();
            if(!empty($urlData)){

                //create curl file
                if (function_exists('curl_file_create')) { // php 5.5+
                    $cFile = curl_file_create($this->file->path, '', $this->getFilename());
                } else { //
                    $cFile = '@' . realpath($this->file->path);
                }

                //post data
                $postData = [
                    'file' => $cFile,
                    'sess_id' => $urlData['sess_id'],
                    'utype' => 'prem'
                ];

                //attempt to upload
                $success = $this->upload($urlData['url'], $postData);


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
            if(!empty($results) && is_array($results)){

                $file = array_shift($results);

                if($file['file_status'] == 'OK'){

                    $this->uploadeFile = $file;
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

    protected function fetchUploadLink(): array
    {


        $results = $this->app->call(
            'GET',
            '/upload/server',
            [
                'query' => []
            ],
            false
        );

        if(isset($results['sess_id'])){
            return [
                'sess_id' => $results['sess_id'],
                'url' => $results['result']
            ];
        }

        return [];

    }



    /**
     * Get uploaded file ID
     * @return string
     */
    public function getFileId(): string
    {
        return $this->uploadeFile['file_code'] ?? '';
    }




}