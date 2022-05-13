<?php


namespace CloudMonster\Drives\Google;


use CloudMonster\Core\CURL;
use CloudMonster\Drives\BaseDownload;
use CloudMonster\Services\UploadProgress;
use Exception;


class Download extends BaseDownload
{


    /**
     * @throws Exception
     */
    public function run(){

        $http = $this->app->client->authorize();
        $fp = @fopen($this->filepath, 'w');

        if(!empty($fp)){

            $chunkSizeBytes = $this->chunkSize;
            $chunkStart = 0;

            //start download progress
            $progress = new UploadProgress($this->getProgressId(), [
                'fileSize' => $this->size
            ]);

            while ($chunkStart < $this->size) {

                $chunkEnd = $chunkStart + $chunkSizeBytes;

                $response = $http->request(
                    'GET',
                    sprintf('/drive/v3/files/%s', $this->id),
                    [
                        'query' => ['alt' => 'media'],
                        'headers' => [
                            'Range' => sprintf('bytes=%s-%s', $chunkStart, $chunkEnd)
                        ]
                    ]
                );

                $chunkStart = $chunkEnd + 1;
                fwrite($fp, $response->getBody()->getContents());

                //record progress
                $progress->record($chunkStart);

            }

            unset($progress);
            fclose($fp);
        }




        if(!file_exists($this->filepath)){
            throw new Exception('File not downloaded');
        }

    }

    public static function getDL($url): array
    {
        $data = [];

        if($fileId = \CloudMonster\Helpers\Help::getGoogleDriveId($url)){
            $curl = new CURL();

            $curl->setHeaders([
                'accept-encoding: gzip, deflate, br',
                'content-length: 0',
                'content-type: application/x-www-form-urlencoded;charset=UTF-8',
                'origin: https://drive.google.com',
                'referer: https://drive.google.com/drive/my-drive',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36',
                'x-drive-first-party: DriveWebUi',
                'x-json-requested: true'
            ]);

            $curl->setOpt(CURLOPT_ENCODING, 'gzip,deflate');

            $curl->post('https://drive.google.com/uc?id='.$fileId.'&authuser=0&export=download')->exec();

            if($curl->isOk()){
                $resp = str_replace(")]}'",'',$curl->getResponse());
                if(\CloudMonster\Helpers\Help::isJson($resp)){
                    $resp = \CloudMonster\Helpers\Help::toArray($resp);
                    if(!empty($resp['downloadUrl'])){
                        $data = [
                            'link' => $resp['downloadUrl'],
                            'filename' => $resp['fileName']
                        ];
                    }
                }
            }

            $curl->close();
        }



        return $data;

    }


}