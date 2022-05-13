<?php


namespace CloudMonster\Drives\Onedrive;


use CloudMonster\Core\CURL;
use CloudMonster\Drives\BaseDownload;
use CloudMonster\Helpers\Help;
use CloudMonster\Helpers\RemoteDownload;
use CloudMonster\Services\UploadProgress;
use Exception;



class Download extends BaseDownload
{


    /**
     * @throws Exception
     */
    public function run(){

        // Download and the save the file at the given path

       $downloadUrl = $this->app->service->items->getDownloadUrl($this->id);

       $remoteDownload = new RemoteDownload($downloadUrl);
       $remoteDownload->curl->progress(new UploadProgress($this->uniqId, [
           'fileSize' => $this->size,
           'keepUpload' => true,
           'chunkSize' => $this->chunkSize
       ]), 'CurlDownloadProgress');

       $remoteDownload->setDestPath($this->filepath);

       if(!$remoteDownload->saveFile()){
           throw new Exception($remoteDownload->getError());
       }

    }

    public static function getDL($url): array
    {
        $link = '';
        if(str_contains($url, 'my.sharepoint.com')){
            $link = $url . '&download=1';
        }else{
            $headers = get_headers($url, true);
            if(isset($headers['Location'])){
                $url = $headers["Location"];
                $url = str_replace('redir?','embed?', $url);
                $curl = new CURL();
                $curl->timeout = 5;
                $curl->get($url)->exec();
                if($curl->isOk()){
                    $response = $curl->getResponse();
                    $pageData = Help::getStringBetween($response, 'window.itemData =', ';');
                    if(!empty($pageData) && Help::isJson($pageData)){
                        $jsonData = Help::toArray($pageData);
                        if(isset($jsonData['items'][0]['urls']['download'])){
                            $link = $jsonData['items'][0]['urls']['download'];
                        }
                    }
                }
                $curl->close();
            }
        }
        $data = [
            'link' => $link,
            'filename' => ''
        ];

        return $data;
    }


}