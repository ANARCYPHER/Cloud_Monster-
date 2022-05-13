<?php


namespace CloudMonster\Drives\Dropapk;



use CloudMonster\Core\CURL;
use CloudMonster\Drives\BaseUploader;
use CloudMonster\Services\UploadProgress;
use Exception;


/**
 * Class Upload
 * @package CloudMonster\Drives\Dropapk
 */
class Upload extends BaseUploader {


    /**
     * Run upload process
     * @throws Exception
     */
    public function run()
    {

        $success = false;

        if($this->issetFile()){

            //attempt to fetch upload token data
            $tokenData = $this->fetchTokenData();

            if(!empty($tokenData)){

                //create curl file
                if (function_exists('curl_file_create')) {
                    $cFile = curl_file_create($this->file->path);
                } else { //
                    $cFile = '@' . realpath($this->file->path);
                }

                //upload post data
                $postData = [
                    'sess_id' => $tokenData['sess_id'],
                    'file' => $cFile
                ];

                //target upload url
                $url = $tokenData['result'];

                //attempt to upload file
                $success = $this->upload($url, $postData);


            }else{

                $this->app->addError('Unable to fetch dropak upload token');

            }

        }else{

            $this->app->addError('File not set');

        }

        if(!$success){

            throw new Exception('DropApk upload failed');

        }


    }


    /**
     * Attempt to upload file to dropApk
     * @param string $url
     * @param array $postData
     * @return bool
     */
    protected function upload(string $url, array $postData): bool
    {

        $curl = new Curl;
        $curl->timeout = 1000;
        $success = false;

        $curl->progress(new UploadProgress($this->id, [
            'fileSize' => $this->file->size,
            'keepUpload' => true,
            'chunkSize' => $this->chunkSize
        ]));

        $curl->post($url, $postData)->exec();

        if($curl->isOk()){

            $results = $curl->getResults();

            if(!empty($results)){

                $uploadedFile = array_shift($results);

                if($uploadedFile['file_status'] == 'OK'){

                    $this->uploadedFile = $uploadedFile;
                    $success = true;

                }

            }else{

                $this->app->addError('empty response received');

            }


        }else{

            $this->app->addError($curl->getError());

        }

        $curl->close();

        return $success;

    }


    /**
     * Fetch upload token data
     * @return array
     */
    protected function fetchTokenData(): array
    {

        $results = $this->app->call(
            'GET',
            '/upload/server',
            [],
            false
        );

        return isset($results['sess_id']) ? $results : [];

    }

    /**
     * Get uploaded file ID
     * @return string
     */
    public function getFileId(): string
    {
        return !empty($this->uploadedFile['file_code']) ? $this->uploadedFile['file_code'] : '';
    }



}