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

namespace CloudMonster\Core;

use CloudMonster\Helpers\Help;

/**
 * Class Model
 * @author John Antonio
 * @package CloudMonster\Core
 */
class Model{

    /**
     * DB table name
     * @var string
     */
    protected static string $tmpTbl = '';

    /**
     * DB table name
     * @var string
     */
    protected $tbl;

    /**
     * Database
     * @var object
     */
    protected $db;

    /**
     * Current object data
     * @var array
     */
    protected $object;

    /**
     *Blacklisted keys
     * @var array
     */
    private $blackListedKeys = ['id','status'];

    /**
     * Soft delete
     * @var bool
     */
    protected $softDelete = false;

    /**
     * Is recover
     * @var bool
     */
    protected static $isRecover = false;
    protected static $isHardDel = false;

    /**
     * Error
     * @var string
     */
    protected $error = '';

    /**
     * Model constructor.
     * @param $tbl
     */
    public function __construct($tbl){
        $this->tbl = $tbl;
        $this->db = Database::getInstance();
        $this->initObject();
    }

    /**
     * Find record by ID
     * @param int $id record ID
     * @return array
     */
    public function findById(int $id) : array {
        $this->db->where('id', $id);
        $result = $this->db->getOne($this->tbl);
        if ($this->db->count > 0) {
            return $result;
        }
        return [];
    }

    /**
     * Bind data to db query
     * @param array $data
     */
    protected function bindData(array $data = []){
        if(!empty($data)){
            foreach ($data as $key => $val){
                if($this->isObjVal($key)){
                    $this->db->where($key, $val);
                }
            }
        }
    }

    /**
     * Load current object
     * @param $id
     * @param bool $blocked
     * @return bool
     */
    public function load($id, bool $blocked = false): bool
    {

        $success = false;

        if($blocked){
            if($this->isObjVal('status')){
                $this->db->where('status', 0);
            }
        }

        if(!is_array($id)){
            if(empty($id) || !is_numeric($id)) return false;
            $result = $this->findById($id);
        }else{
            $this->bindData($id);
            $result = $this->db->getOne($this->tbl);
        }

        if (!empty($result)) {
            foreach ($result as $k => $v) {
                if (array_key_exists($k, $this->object)) {
                    $this->object[$k] = $v;
                }
            }
            $success = true;
        }

        return $success;

    }

    /**
     * Clean all inactive records (by expiring date)
     */
    public function cleanGarbage(){
        $deletedItems = $this->get(['status'=>1]);
        if(!empty($deletedItems)){
            foreach ($deletedItems as $item){
                $tmpObj = new static();
                $lastUpdated = $item['updatedAt'];
                $timeFirst = strtotime($lastUpdated) + 300;
                $timeSecond = strtotime(Help::timeNow());
                if ($timeFirst < $timeSecond) {
                    if($tmpObj->load($item['id'])){
                        $tmpObj->delete();
                    }

                }
                unset($tmpObj);
            }
        }
    }

    /**
     * Assign current object values
     */
    public function assign(array $data) : Model{
        foreach ($data as $k => $v) {
            if (array_key_exists($k, $this->object)) {
                $this->object[$k] = $v;
            }
        }
        return $this;
    }

    /**
     * Add val to obj
     * @param array $data
     */
    protected function addVal(array $data = []){
        foreach ($data as $k => $v){
            if($this->isObjVal($k)){
                $this->object[$k] = $v;
            }
        }
    }


    /**
     * Save current object data
     * @return bool
     * @throws \Exception
     */
    public function save() {

        $this->beforeSave();

        if (!$this->hasError()) {

            $this->beforeSaveFixDates();

            if (!$this->isEdit()) {
                $id = $this->db->insert($this->tbl, $this->getData());
                if ($id) {
                    $this->object['id'] = $id;
                } else {
                    $this->addError($this->db->getLastError());
                }
            } else {
                $this->db->where('id', $this->getID());
                if (!$this->db->update($this->tbl, $this->getData(), '1')) {
                    $this->addError('Update Filed ! -> ' . $this->db->getLastError());
                }
            }
            $this->afterSave();
        }

        return !$this->hasError();
    }

    /**
     * fix date times before save
     */
    private function beforeSaveFixDates(){

        if(!$this->isEdit()){
            if(array_key_exists('createdAt',$this->object)){
                $this->object['createdAt'] = Help::timeNow();

            }
        }

        if(array_key_exists('updatedAt',$this->object)){
            $this->object['updatedAt'] = Help::timeNow();
        }

    }


    /**
     * Delete record
     * @return bool
     * @throws \Exception
     */
    public function delete(){
        $success = false;

        if($this->isEdit()){
            $this->beforeDelete();

            $this->db->where('id', $this->getObj('id'));

            if($this->softDelete || $this::$isRecover){
                $status = $this::$isRecover ? 0 : 1;
                $this->removeBlackListedKeys(['status']);
                $this->object['status'] = $status;
                if($this->save()) $success = true;

                $this->afterDelete();

            }else{


                if($this->db->delete($this->tbl,1)) $success = true;

            }
        }

        return $success;

    }


    public static function recover(){
        self::$isRecover = true;
    }

    public static function hardDelete(){
        self::$isHardDel = true;
    }

    public function soft($t = true){
        if(!self::$isHardDel && $t){
            $this->softDelete = true;
        }
        return $this;
    }

    protected function beforeDelete(){

    }

    protected function afterDelete(){

    }

    protected function beforeSave(){

    }

    protected function afterSave(){

    }

    public function search($field , $term, array $skipIds = []): static
    {

        if($this->isObjVal($field)){
            $this->db->where($field, "%$term%", "LIKE");
            if(!empty($skipIds)){
                $this->db->where('id', $skipIds , 'NOT IN');
            }
        }

        return $this;

    }

    public function getObj($t = ''){
        if(!empty($t)){
            $val = '';
            if(array_key_exists($t, $this->object)){
                $val = $this->object[$t];
            }
            return $val;
        }
        return $this->object;
    }

    public function isObjVal($val){
        return array_key_exists($val, $this->object) ? true : false;
    }

    public function getOne($filters = [], $columns = [], $orders = []){

        $results = $this->get($filters, $orders, $columns);
        return !empty($results) ? $results[0] : [];
    }

    public function count($filters = []){

        //filters
        if(!empty($filters)){
            foreach($filters as $k => $v){
                if($this->isObjVal($k) && $v != ''){
                    $this->db->where($k, $v);
                }
            }
        }

        return $this->db->getValue($this->tbl, "count(*)");

    }

    public function reload(): bool
    {
        if($this->isLoaded()){
            $id = $this->getID();
            $this->clean();
            return $this->load($id);
        }
        return false;
    }

    public function get($filters = [],$orders = [], $columns = [], $limit = 0, $page = 0){

        $cols = [];


        //filters
        if(!empty($filters)){
            foreach($filters as $k => $v){
                if($this->isObjVal($k) && $v != ''){
                    if(!is_array($v)){
                        $this->db->where($k, $v);
                    }else{
                        if(count($v) === 2){
                            $this->db->where($k, $v[0], $v[1]);
                        }
                    }
                }
            }
        }

        if(!array_key_exists('status', $filters))
            $this->db->where("status", 0);

        //columns
        if(!empty($columns)){
            foreach($columns as $v){

                if(strpos($v, '.skip') === false){
                    if($this->isObjVal($v)){
                        $cols[] = $v;
                    }
                }else{
                    $v = str_replace('.skip', '', $v);
                    $cols[] = $v;
                }
            }
        }


        //orders
        if(!empty($orders)){
            foreach($orders as $k => $v){
                if($this->isObjVal($k) && $v != ''){
                    $this->db->orderBy($k, $v);
                }
            }
        }else{
            $this->db->orderBy("id", 'Desc');
        }

        //limit
        if(empty($limit)) $limit = null;



        if(empty($page)){

            $results = $this->db->get($this->tbl, $limit, $cols);
        }else{
            $this->db->pageLimit = $limit;
            $results = $this->db->arraybuilder()->paginate($this->tbl, $page, $cols);

        }

        return $this->db->count > 0 ? $results : [];


    }



    protected function isEdit() : bool{
        if (!empty($this->object['id'])) {
            return true;
        }
        return false;
    }

    public function cleanErrors(){
        $this->error = '';
    }

    public function isExist($id = '', $alt = []){
        if(!empty($id) && is_numeric($id)) {
            $this->db->where('id', $id);
        }
        if(!empty($alt) && is_array($alt)){
            foreach ($alt as $k => $v){
                $this->db->where($k, $v);
            }
        }
        return $this->db->has($this->tbl);
    }


    /**
     * Get object data for save
     */
    private function getData() : array {
        $data = $this->object;
        foreach ($this->blackListedKeys as $bl) {
            if (array_key_exists($bl, $data)) {
                unset($data[$bl]);
            }
        }
        return $data;
    }


    /**
     * Add blacklisted keys
     */
    protected function addBlackListedKeys(array $keys = []) : void{
        foreach($keys as $key){
            if(array_key_exists($key, $this->object)){
                $this->blackListedKeys[] = $key;
            }
        }
    }

    public function removeBlackListedKeys($keywords = []) {
        if (is_array($keywords)) {
            foreach ($keywords as $k => $keyword) {
                if (in_array($keyword, $this->blackListedKeys)) {
                    $tmpID = array_search($keyword, $this->blackListedKeys);
                    unset($this->blackListedKeys[$tmpID]);
                }
            }
        }
    }


    /**
     * Get current object ID
     */
    public function getID() {
        if ($this->isEdit()) {
            return $this->object['id'];
        }
        return '';
    }


    /**
     * Check error
     */
    public function hasError() : bool {
        if (!empty($this->error)) {
            return true;
        }
        return false;
    }

    /**
     * Get error
     */
    public function getError() : string {
        return !empty($this->error) ? $this->error :  'something went wrong';
    }

    /**
     * Get error
     */
    protected function addError(string $e) : void {
        $this->error = $e;
    }

    public static function getTbl(){
        return static::$tmpTbl;
    }

    /**
     * Initialize object properties
     */
    private function initObject() {
        if ($this->db->tableExists ($this->tbl)){
            $dbColumns = $this->db->rawQuery("DESCRIBE " . $this->tbl);
            if (!empty($dbColumns)) {
                foreach ($dbColumns as $col) {
                    $val = is_numeric($col['Default']) ? $col['Default'] : NULL;
                    $this->object[$col['Field']] = $val;
                }
            }else{
                $this->addError($this->db->getLastError());
            }
        }else{
            die('Required Database table does not exist . -> ' . $this->tbl);
        }

    }

    public function clean(){
        if(!empty($this->object)){
            foreach ($this->object as $k => $v){
                $this->object[$k] = '';
            }
        }
        $this->cleanErrors();
    }


    public function __call($name, $args){
        $name = lcfirst(str_replace('get','',$name));
        return $this->getObj($name);
    }

    public function has($data = []){
        if(!empty($data)){
            $this->bindData($data);
            return $this->db->has($this->tbl);
        }
        return false;
    }


    public static function getInstance(): static
    {
        return new static;
    }



    public function reset(){
        $this->object = [];
        $this->initObject();
    }

    public function isSoftDelete(): bool
    {
        return $this->softDelete;
    }

    public function checkPermission(){
        if($this->isEdit()){
            if($this->isObjVal('user_id')){
                //validate user access permission
                if(!empty($this->getUser_id())){
                    if($this->getUser_id() !== Users::getActiveUserId()){
                        die('Permission denied !');
                    }
                }
            }
        }
    }

    /**
     * Check file is removed or not
     * @return bool
     */
    public function isRemoved(): bool
    {
        if($this->isEdit()){
            if($this->getStatus() == 1){
                return true;
            }
        }
        return false;
    }

    protected function updateStatus(int $st = 0): bool
    {
        $this->removeBlackListedKeys(['status']);
        return $this->assign(['status'=>$st])->save();
    }


    public function isLoaded(): bool
    {
        return $this->isEdit();
    }

    public function __debugInfo() {
        $vars = get_object_vars($this);
        if(isset($vars['db'])){
            unset($vars['db']);
        }
        return $vars;
    }


    protected function bindObjVal($data){
        if(!empty($data)){
            foreach ($data as $key => $val){
                $this->object[$key] = $val;
            }
        }

    }

    public function isDeleted(): bool
    {
        return $this->getObj('status') === 1;
    }


}