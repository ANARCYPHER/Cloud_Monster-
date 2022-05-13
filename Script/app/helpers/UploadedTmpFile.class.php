<?php

namespace CloudMonster\Helpers;


use CloudMonster\Utils\FileInfo;

class UploadedTmpFile{

    protected string $id;
    protected string $dir;
    protected string $path;
    protected bool $isExist = false;

    public function __construct($id){

        $this->id = $id;
        $this->dir = Help::cleanDS(Help::storagePath('tmp') . '/' . $id);
        $this->path = Help::getFirstFile($this->dir);

        $this->check();

    }

    protected function check(){

        if(is_file($this->path)){
            $this->isExist = true;
        }

    }

    public function getDir() : string{

        return $this->dir;

    }

    public function isExist(): bool
    {

        return $this->isExist;

    }

    public function get(): FileInfo
    {
        return new FileInfo($this->path);
    }


}