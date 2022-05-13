<?php

namespace CloudMonster\Utils;


class DriveItem{


    private string $baseUrl;
    private string $sharedFileUrl = '';
    private array $userInput;
    private array $systemInput;
    private bool $isActive;


    public function __construct(array $data = []){
        $this->initData($data);
    }

    private function initData($data){

        $this->baseUrl = $data['baseUrl'] ?? '';
        $this->sharedFileUrl = $data['sharedFileUrl'] ?? '';
        $this->userInput = $data['authDataFormat']['userInput'] ?? [];
        $this->systemInput = $data['authDataFormat']['systemInput'] ?? [];
        $this->isActive = $data['isActive'] ?? false;


    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getUserInput(): array
    {
        return $this->userInput;
    }

    public function getSystemInput(): array
    {
        return $this->systemInput;
    }

    public function getSharedFileUrl(): string
    {
        return $this->sharedFileUrl;
    }


    public function bind(array $data): static
    {
        foreach ($data as $key => $val){
            if(array_key_exists($key, $this->userInput)){
                $this->userInput[$key]['val'] = $val;
            }
        }
        return $this;
    }



    public function getData(): array
    {
        $data = [];
        $reqData = array_merge($this->userInput, $this->systemInput);
        foreach ($reqData as $key => $item){
            $val = $item['val'] ?? '';
            $data[$key] = $val;
        }
        return $data;
    }

    public function isActive() : bool{
        return $this->isActive;
    }



}