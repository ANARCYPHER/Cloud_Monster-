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


/**
 * Class CloudFolder
 * @author John Antonio
 * @package CloudMonster\Models
 */
class CloudFolder extends Model
{

    /**
     * Database table
     * @var string table name
     */
    protected static string $tmpTbl = "cloud_folders";

    /**
     * CloudFolder constructor.
     */
    public function __construct()
    {
        parent::__construct($this::$tmpTbl);
    }

    /**
     * Delete cloud drives folders by local folder ID
     * @param int $id local folder ID
     * @return bool success or failure
     */
    public function delByLocalFolderId(int $id): bool
    {
        $this->db->where('localFolderId', $id);
        return $this->db->delete($this->tbl);
    }


}