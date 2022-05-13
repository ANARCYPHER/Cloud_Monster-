<?php

namespace CloudMonster\Utils;



class FileInfo{


    public string $filepath = '';
    public string $filename = '';
    public string $basename = '';
    public string $mime = '';
    public string $extension = '';
    public string $dirname = '';
    public int $size = 0;


    public function __construct($filepath){

        if(!empty($filepath)){
            $this->filepath = $filepath;
            $this->init();
        }


    }

    protected function init(){


        if(file_exists($this->filepath) && !is_dir($this->filepath)){

            $fInfo = new \finfo();

            $this->mime = $fInfo->file($this->filepath, FILEINFO_MIME_TYPE);
            $this->basename = pathinfo($this->filepath, PATHINFO_BASENAME);
            $this->filename = pathinfo($this->filepath, PATHINFO_FILENAME);
            $this->extension = pathinfo($this->filepath, PATHINFO_EXTENSION);
            $this->dirname = pathinfo($this->filepath, PATHINFO_DIRNAME);
            $this->size = filesize($this->filepath);

        }

    }



}