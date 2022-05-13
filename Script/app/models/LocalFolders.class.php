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

namespace CloudMonster\Models;

use CloudMonster\Core\Model;
use CloudMonster\Helpers\Help;
use CloudMonster\Services\CloudFile;


/**
 * Class LocalFolders
 * @author John Antonio
 * @package CloudMonster\Models
 */
class LocalFolders extends Model {

    /**
     * Database table
     * @var string table name
     */
    protected static string $tmpTbl = "local_folders";

    /**
     * Data With Buckets
     * @var bool
     */
    protected bool $withBuckets = false;

    /**
     * Is Parent Obj
     * @var bool
     */
    protected bool $isParent = false;

    /**
     * Root folder ID
     */
    const ROOT_FOLDER = 1;

    /**
     * LocalFolders constructor.
     */
    public function __construct() {
        parent::__construct($this::$tmpTbl);
    }

    /**
     * Create new local folder
     * @param string $name folder name
     * @param int $pid parent folder ID
     * @return bool success or failure
     */
    public function create(string $name, int $pid = 0): bool {

        if ($this->assign( [ 'name' => $name, 'parentId' => $pid ] )->save()) {
            //create cloud drive process for create new folder in cloud drives
            CloudFile::process('create', $this);
            return true;
        }

        return false;

    }

    /**
     * Perform the following tasks before saving the
     * relevant local folder.
     */
    protected function beforeSave() {
        if (!$this->isEdit()) {
            //default : set root folder ID
            if (empty($this->getParentId()))
                $this->addVal( [ 'parentId' => $this::ROOT_FOLDER ] );
        }
        //check folder name is already exist or not
        if ($this->isNameExist()) {
            $this->addError('Folder name already exist');
        }
    }

    /**
     * Check folder name already exist or not
     * @return bool exist or not exist
     */
    public function isNameExist(): bool {

        if (!empty($this->getName())) {
            $this->db->where('id', $this->getID(), '!=');
            return $this->has( [
                'name' => $this->getName(),
                'parentId' => $this->getParentId()
            ] );
        }

        return false;

    }

    /**
     * Get Data with buckets
     * @return $this
     */
    public function withBuckets(): static {
        $this->withBuckets = true;
        return $this;
    }

    /**
     * Get child folders list by parent folder ID
     * @param int $id parent folder ID
     * @param array $cols  (optional) target columns
     * @param int $driveId (optional) cloud drive ID
     * @return array child folder list
     */
    public function getByParentId(int $id, array $cols = [], int $driveId = 0): array {

        $folderList = $bucketList = [];

        if(empty($driveId)){

            $folderList = $this->get(['parentId' => $id], ['name' => 'ASC'], $cols);
            if ($this->withBuckets) {

                $this->withBuckets = false;
                $buckets = new Buckets();
                $bucketList = $buckets->get(['folderId' => $id], ['name' => 'ASC']);

                unset($buckets);

            }

        }else{

            $this->db->join(CloudFolder::getTbl() . ' cf', 'lf.id = cf.localFolderId', 'RIGHT');
            $this->db->join(CloudDrives::getTbl() . ' cd', 'cd.id = cf.cloudDriveId', 'RIGHT');
            $this->db->where("cd.id", $driveId);

            $this->db->where("lf.parentId", $id);
            $this->db->where("lf.status", 0);
            $this->db->groupBy("lf.id");
            $this->db->orderBy("lf.name" , 'ASC');

            $folderList = $this->db->get($this->tbl . ' lf', null, 'lf.*');

            if($this->withBuckets){

                $this->withBuckets = false;
                $this->db->join(Files::getTbl() . ' f', 'b.id = f.BucketId', 'RIGHT');
                $this->db->join(CloudDrives::getTbl() . ' cd', 'cd.id = f.cloudDriveId', 'RIGHT');
                $this->db->where("cd.id", $driveId);

                $this->db->where("b.status", 0);
                $this->db->where("b.folderId", $id);
                $this->db->groupBy("b.id");
                $this->db->orderBy("b.name" , 'ASC');

                $bucketList = $this->db->get(Buckets::getTbl() . ' b', null, 'b.*');

            }


        }

        $results = array_merge($folderList, $bucketList);

        return !empty($results) ? $results : [];

    }


    /**
     * Get parent folder list by child folder ID
     * @param int $childId child folder ID
     * @param bool $isTrash (optional) with trashed folders
     * @return array parent folder list
     */
    public function getParentList(int $childId, bool $isTrash = false): array {
        $list = [];
        $st = '';
        if ($childId != $this::ROOT_FOLDER) {
            if ($isTrash != '') {
                $st = $isTrash ? 1 : 0;
            }
            while (true) {
                $results = $this->getOne(['id' => $childId, 'status' => $st], ['id', 'parentId', 'name'], ['name', 'asc']);
                if (!empty($results)) {
                    $list[] = $results;
                    if ($results['parentId'] !== $this::ROOT_FOLDER) {
                        $childId = $results['parentId'];
                    } else {
                        break;
                    }
                } else {
                    break;
                }
            }
        }
        if (!empty($list)) krsort($list);
        return $list;
    }

    /**
     * Get folder location of the current folder
     * @param false $skipCurrentFolder
     * @return string
     */
    public function getLocation(bool $skipCurrentFolder = false): string {
        $location = '';
        if ($this->isEdit()) {
            //get parent folder list
            $parentList = $this->getParentList($this->getID(), '');
            $list = [];
            if (!empty($parentList)) {
                //attempt to create path array
                foreach ($parentList as $val) {
                    if ($skipCurrentFolder && $val['id'] === $this->getID()) {
                        continue;
                    }
                    array_push($list, $val['name']);
                }
            }
            $location = implode('/', $list);
        }
        return $location;
    }

    /**
     * Get child folder list by parent ID
     * @param int $pid parent folder ID
     * @return array
     */
    public function getChildList(int $pid = 0): array {
        $list = [];
        if (empty($pid)) $pid = $this::ROOT_FOLDER;
        $status = '';
        $results = $this->get(['parentId' => $pid, ], ['name', 'asc']);
        if (!empty($results)) {
            $list = $results;
            foreach ($results as $key => $val) {
                $childList = $this->getChildList($val['id']);
                if (!empty($childList)) {
                    $list[$key]['child'] = $childList;
                }
            }
        }
        return $list;
    }

    /**
     * Extract child folder ID by child folder list
     * @param array $childList
     * @return array
     */
    public function extractChildIds(array $childList = []): array {
        $ids = Help::extractData($childList, 'id');
        if (!empty($ids)) {
            foreach ($childList as $k => $v) {
                if (!empty($v['child'])) {
                    $ids = array_merge($ids, $this->extractChildIds($v['child']));
                }
            }
        }
        return $ids;
    }

    /**
     * Check folder is Empty or not
     * @param array $childIds target child folders IDs
     * @return bool empty or not
     */
    public function isEmpty(array $childIds = []): bool {
        $isEmpty = true;
        if ($this->isEdit()) {
            $childIds[] = $this->getID();
            if (!empty($childIds)) {
                $this->db->where('folderId', $childIds, 'IN');
                $isEmpty = !$this->db->has(Buckets::getTbl());
            }
        }
        return $isEmpty;
    }

    /**
     * Get by parent folder ID
     * @param $pid
     * @return array
     */
    protected function getByPId( $pid ): array {
        return $this->get( [ 'parentId' => $pid, 'status' => '' ] );
    }

    /**
     * Set as parent object
     * @return $this
     */
    public function parent(): static {
        $this->isParent = true;
        return $this;
    }

    /**
     * Actions to be taken before deleting the relevant folder.
     */
    protected function beforeDelete() {
        if (!$this->isRoot()) {
            //attempt to delete all child folders
            $childFolders = $this->getByPId( $this->getID() );
            if ( !empty( $childFolders ) ) {
                $tmpLocalFolder = new LocalFolders();
                $tmpLocalFolder->softDelete = $this->softDelete;
                foreach ($childFolders as $childFolder) {
                    if ($tmpLocalFolder->load( $childFolder['id']) ) {
                        $tmpLocalFolder->delete();
                    }
                    $tmpLocalFolder->clean();
                }
            }
            //del cloud folders
            if (!$this->isSoftDelete()) {
                $tmpCloudFolder = new CloudFolder();
                $tmpCloudFolder->delByLocalFolderId( $this->getID() );
            }
        } else {
            $this->addError('Can not delete root folder');
        }
    }

    /**
     * Actions to be taken after deleting the relevant folder.
     */
    protected function afterDelete() {
        if ( $this->isParent && $this->isSoftDelete() ) {
            //create cloud drive process for delete folders in cloud drives
            CloudFile::process( 'delete', $this );
        }
    }

    /**
     * Is root folder
     * @return bool
     */
    public function isRoot(): bool {
        if ( $this->isEdit() ) {
            return $this->getID() === $this::ROOT_FOLDER;
        }
        return true;
    }

    /**
     * Move the relevant folder into another folder.
     *
     * -- This also starts the process of moving folder
     * in the cloud drive.
     *
     * @param int $moveTo The ID of the destination folder.
     * @return bool success or failure
     */
    public function move( int $moveTo = 0 ): bool {

        $tmpFolder = $this::getInstance();
        $success = false;

        if ($this->isEdit()) {

            //attempt to load destination folder
            if ($tmpFolder->load($moveTo)) {

                if (!empty($moveTo) && !$this->isRoot() && $this->getID() != $moveTo) {

                    //check folder name already exist in destination folder
                    $isExistName = $this->has( [
                        'name' => $this->getName(),
                        'parentId' => $tmpFolder->getID()
                    ] );

                    if (!$isExistName) {

                        //tmp data for cloud operators
                        $tmpData = [
                            'pid' => $this->getParentId(),
                            'location' => $this->getLocation()
                        ];

                        $tmpData = Help::toJson($tmpData);

                        $data = [
                            'parentId' => $moveTo,
                            'tmp' => $tmpData
                        ];

                        if ($this->assign( $data )->save()) {
                            //also start move folders in the cloud drives
                            CloudFile::process('move', $this);
                            $success = true;
                        }
                    } else {

                        $this->addError('A folder with the name <i>' . $this->getName() . '</i> already exists in destination folder');

                    }

                } else {

                    $this->addError('Unable to move the selected folder');

                }

            } else {

                $this->addError('destination folder not found');

            }

        }

        return $success;
    }

    /**
     * Rename the relevant folder name.
     *
     * -- This also starts the process of renaming folders
     * in the cloud drive.
     *
     * @param string $name new folder name
     * @return bool success or failure
     */
    public function rename(string $name): bool {

        $tmpData = [ 'location' => $this->getLocation() ];
        $tmpData = Help::toJson($tmpData);

        if ($this->assign( [ 'name' => $name, 'tmp' => $tmpData ] )->save()) {
            //create cloud drive process for rename folders in cloud drives
            CloudFile::process('rename', $this);
            return true;
        }

        return false;

    }


}
