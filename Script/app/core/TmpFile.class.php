<?php

namespace CloudMonster\Core;

/**
 * Class TmpFile
 */

class TmpFile{

    /**
     * File Uniq ID
     * @var string
     */
    protected string $id;

    /**
     * File name
     * @var string
     */
    protected string $name;

    /**
     * File size
     * @var int
     */
    protected int $size;

    /**
     * File type
     * @var string
     */
    protected string $type;

    /**
     * File extension
     * @var string
     */
    protected string $ext;

    /**
     * File tmp_name
     * @var string
     */
    protected string $tmpName;

    /**
     * Errors
     * @var array
     */
    protected array $errors = [];


    /**
     * TmpFile constructor.
     * @param $id
     */
    public function __construct($id){
        if($this::issetFile($id)){
            $this->id = $id;
            $this->init();
        }else{
            $this->addError('requested file does not exist');
        }
    }

    /**
     * Add error message
     * @param $e
     */
    private function addError($e) : void{
        array_push($this->errors, $e);
    }

    /**
     * Get error message
     * @return string
     */
    public function getError() : string{
        return count($this->errors) > 0 ? $this->errors[0] : 'No error';
    }

    /**
     * Get all error messages
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get file name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get file tmp name
     * @return string
     */
    public function getTmpName(): string
    {
        return $this->tmpName;
    }

    /**
     * Get file size
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get file type
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get file extension
     * @return string
     */
    public function getExt(): string
    {
        return $this->ext;
    }

    /**
     * Check has errors or not
     * @return bool
     */
    public function isOk(): bool
    {
        return empty($this->errors);
    }

    /**
     * Initialize file
     */
    private function init() : void{
        $file = $_FILES[$this->id];
        if($file['error'] == 0){

            $this->name = $file['name'] ?? '';
            $this->size = $file['size'] ?? 0;
            $this->type = $file['type'] ?? '';
            $this->tmpName = $file['tmp_name'] ?? '';

            if(!empty($file['name'])){
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if(!empty($ext)){
                    $this->ext = $ext;
                }else{
                    $this->addError('Unable to detect file extension');
                }
            }

        }else{
            $this->addError($this->getErrMsg($file['error']));
        }
    }


    /**
     * Get file error message by error code
     * @param $code
     * @return string
     */
    private function getErrMsg($code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "the uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "the uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "no file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "file upload stopped by extension";
                break;
            default:
                $message = "unknown upload error";
                break;
        }
        return $message;
    }

    /**
     * Check file isset
     * @param $id
     * @return bool
     */
    public static function issetFile($id): bool
    {
        return isset($_FILES[$id]);
    }







}