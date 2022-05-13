<?php

/*
   +------------------------------------------------------------------------+
   | CloudMonster - Handle multi-Cloud Storage in parallel
   | Copyright (c) 2021 PHPCloudMonster. All rights reserved.
   +------------------------------------------------------------------------+
   | @author John Antonio
   | @author_url 1: https://phpcloudmonster.com
   | @author_url 2: https://www.codester.com/johnanta
   | @author_email: johnanta89@gmail.com
   +------------------------------------------------------------------------+
*/

namespace CloudMonster\Services;

use CloudMonster\Core\TmpFile;
use CloudMonster\Helpers\Help;
use CloudMonster\Utils\FileInfo;

/**
 * Class LocalUpload
 * @author John Antonio
 * @package CloudMonster\Services
 */
class LocalUpload {

    /**
     * File upload directory
     * @var string
     */
    protected string $uploadDir;

    /**
     * Uniq file ID
     * @var string
     */
    protected string $fileId;

    /**
     * Error message
     * @var string
     */
    protected string $error;

    /**
     * Temporary file
     * @var string
     */
    protected string $tmpFile;


    /**
     * LocalUpload constructor.
     * @param string $fileId
     */
    public function __construct(string $fileId) {
        $this->uploadDir = Help::storagePath('files');
        $this->fileId = $fileId;
        $this->error = '';
        $this->tmpFile = '';
    }

    /**
     * Set Upload directory
     * @param string $dirFolder
     */
    public function setUploadDir(string $dirFolder) {
        $this->uploadDir = $dirFolder;
    }

    /**
     * Set temporary file
     * @param string $tmpFile
     */
    public function setTmpFile(string $tmpFile) {
        $this->tmpFile = $tmpFile;
    }

    /**
     * Chunk upload
     * @param string $file
     * @param $chunkIndex
     * @return $this
     */
    public function chunk(string $file, $chunkIndex): static {
        $tmpFile = new TmpFile($file);
        if ($tmpFile->isOk()) {
            $fileExt = $tmpFile->getExt();
            $fileName = "{$chunkIndex}.{$fileExt}";
            $tmpDir = Help::storagePath('tmp') . '/' . $this->fileId;
            $this->setUploadDir("{$tmpDir}/$fileName");
            $this->setTmpFile($tmpFile->getTmpName());
            //create tmp directory if does not exist
            if (!file_exists($tmpDir) || !is_dir($tmpDir)) {
                if (!mkdir($tmpDir)) {
                    $this->addError('Unable to create tmp directory');
                } else {
                    if (!@chmod(realpath($tmpDir), 0777)) {
                        $this->addError("could not modify directory permissions");
                    }
                }
            }
        } else {
            //file error
            $this->addError($tmpFile->getError());
        }
        return $this;
    }

    /**
     * Chunk concatenate
     * @param int $chunkTotal
     * @return FileInfo|bool
     */
    public function chunkConcat(int $chunkTotal = 0): FileInfo | bool {
        $finishDir = realpath(Help::storagePath('tmp') . '/' . $this->fileId);
        $finishFile = '';
        if (file_exists($finishDir) && is_dir($finishDir)) {
            //attempt to find file extension
            $files = scandir($finishDir);
            $firstFile = $files[2]??'';
            if (!empty($firstFile)) {
                $ext = pathinfo($firstFile, PATHINFO_EXTENSION);
                $finishFile = "{$finishDir}/tmp.{$ext}";
                for ($i = 1;$i <= $chunkTotal;$i++) {
                    if ($tmpFile = realpath("{$finishDir}/{$i}.{$ext}")) {
                        // attempt to get and copy chunk
                        $chunk = @file_get_contents($tmpFile);
                        if (empty($chunk)) {
                            $this->addError('Chunks are uploading as empty data');
                            break;
                        }
                        // add chunk to main file
                        $success = @file_put_contents($finishFile, $chunk, FILE_APPEND | LOCK_EX);
                        // delete chunk
                        if ($success) {
                            @unlink($tmpFile);
                        }
                    } else {
                        $this->addError('Your chunk was lost mid-upload.');
                        break;
                    }
                }
            } else {
                $this->addError('no chunk files');
            }
        } else {
            $this->addError('chunk directory does not exist');
        }
        return $this->isOk() ? new FileInfo($finishFile) : false;
    }

    /**
     * Attempt to upload file
     * @param string $tmpFile
     * @return bool
     */
    public function upload(string $tmpFile = ''): bool {
        //check tmp file before upload
        if (!empty($tmpFile)) $this->tmpFile = $tmpFile;
        if (empty($this->tmpFile) || !file_exists($this->tmpFile)) {
            $this->addError('tmp file missing');
        }
        //now attempt to upload
        if ($this->isOk()) {
            if (!@move_uploaded_file($this->tmpFile, $this->uploadDir) || !file_exists($this->uploadDir)) {
                $this->addError("upload failed");
            }
        }
        return $this->isOk();
    }

    /**
     * Add error message
     * @param string $e
     */
    private function addError(string $e) {
        $this->error = $e;
    }

    /**
     * Get error message
     * @return string
     */
    public function getError(): string {
        return $this->error;
    }

    /**
     * check status
     * @return bool
     */
    public function isOk(): bool {
        return empty($this->error);
    }


    /**
     * Cancel local uploaded file
     * @param $fileId
     * @param string $dir
     * @return bool
     */
    public static function cancel($fileId, string $dir = 'tmp'): bool
    {

        $deletedFile = Help::storagePath('cache')  . '/' . 'del~' . $fileId . '.txt';
        $filePath = Help::cleanDS(Help::storagePath($dir) . '/' . $fileId);

        if($filePath = realpath($filePath)){
            //create deleted file note
            @file_put_contents($deletedFile, 'file deleted');
            Help::deleteDir($filePath);
            return true;
        }

        return false;

    }

    public static function isCanceled($fileId): bool
    {

        $dir = Help::storagePath('cache');
        if(realpath($dir  . '/del~' . $fileId . '.txt')){
            return true;
        }
        return false;

    }


}
