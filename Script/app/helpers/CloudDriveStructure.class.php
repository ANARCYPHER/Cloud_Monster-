<?php

namespace CloudMonster\Helpers;



use CloudMonster\Utils\DriveItem;
use JetBrains\PhpStorm\Pure;

class CloudDriveStructure{


    protected array $data = [];


    public function __construct(){
        $this->initData();
    }

    public function get(): array
    {
        return $this->data;
    }

    public function getDrives(): array
    {
        $drives = [];
        foreach ($this->data as $k => $val){
            $status = isset($val['isActive']) && $val['isActive'] == 1;
            if($status){
               array_push($drives, $k);
            }
        }
        return $drives;
    }

    public function isExist($source): bool
    {
       return array_key_exists($source, $this->data);
    }

    #[Pure] public function getByDrive($source) : DriveItem
    {
        $data = [];
        if(array_key_exists($source, $this->data)){
            $data = $this->data[$source];
        }
        return new DriveItem($data);
    }


    private function initData(){
        $jsonData = Help::getVarJson('cloud_drives');
        if(Help::isJson($jsonData)){
            $this->data = Help::toArray($jsonData);
        }
        if(empty($this->data)) {
            die('Cloud Drive Structure Initialization Failed.');
        }
    }





}