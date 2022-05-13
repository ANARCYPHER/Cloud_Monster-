<?php


namespace CloudMonster\Drives\Streamsb;



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
                    'api_key' => $this->app->getAuthData('key')
                ];

                if(!empty($this->getParentId())){
                    $postData['fld_id'] = $this->getParentId();
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
            throw new Exception('Upload failed');
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


        if(!empty($curl->getResponse())){

            preg_match('/<textarea name="fn">(.*?)<\/textarea>/s', $curl->getResponse(), $match);
            if(!empty($match[1])){

                $this->uploadeFile = [
                    'id' => $match[1]
                ];
                $success = true;

            }else{
                $this->app->addError('unknown error');
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
        return $this->uploadeFile['id'] ?? '';
    }




}