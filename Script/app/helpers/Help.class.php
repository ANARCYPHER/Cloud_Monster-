<?php


namespace CloudMonster\Helpers;

use CloudMonster\App;
use CloudMonster\Models\ProcessTracker;
use JetBrains\PhpStorm\NoReturn;

class Help{




    /**
     * Clean string data
     * @param $data
     * @return string
     */
    public static function clean($data): string
    {
        // Fix &entity\n;
        $data = str_replace(
            ["&amp;", "&lt;", "&gt;"],
            ["&amp;amp;", "&amp;lt;", "&amp;gt;"],
            $data
        );
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace("/(&#x*[0-9A-F]+);*/iu", '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, "UTF-8");
        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace(
            '#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu',
            '$1>',
            $data
        );
        // Remove javascript: and vbscript: protocols
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2nojavascript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2novbscript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u',
            '$1=$2nomozbinding...',
            $data
        );
        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
            '$1>',
            $data
        );
        // Remove namespaced elements (we do not need them)
        $data = preg_replace("#</*\w+:\w[^>]*+>#i", "", $data);
        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace(
                "#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i",
                "",
                $data
            );
        } while ($old_data !== $data);
        // we are done...
        return trim($data);
    }

    /**
     * Format date time
     * @param $dt
     * @param string $format
     * @return string
     */
    public static function formatDT($dt, string $format = "M jS, Y - h:i A"): string
    {
        return date($format, strtotime($dt));
    }

    /**
     * Format seconds to min:sec
     * @param int $sec
     * @return string
     */
    public static function formatSec(int $sec): string
    {
        return  gmdate("i:s", $sec);
    }

    /**
     * Get random string
     * @param int $length
     * @return string
     */
    public static function random(int $length = 15): string
    {
        $characters =
            "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Validate JSON string
     * @param string $string
     * @return bool
     */
    public static function isJson(string $string): bool
    {
        if(is_string($string) && !is_numeric($string)){
            json_decode($string);
            return json_last_error() == JSON_ERROR_NONE;
        }
        return false;
    }

    /**
     * Create url string
     * @param $text
     * @param string $divider
     * @return string
     */
    public static function slugify($text, string $divider = '-'): string
    {

        if(empty($text)) return '';

        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, $divider);
        $text = preg_replace('~-+~', $divider, $text);
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;

    }

    /**
     * Redirect URL
     * @param string $url
     * @param false $fullUrl
     */
     public static function redirect ( string $url = "", bool $fullUrl = false ) : void {

        //get app alerts and save it in session
        if (!empty(APP::getAlerts())) {
            APP::saveAlerts();
        }

        //self redirect
        if ($url == "self") {
            $url = $_SERVER["REQUEST_URI"];
            $fullUrl = true;
        }

        //set developer sign
        header('developer: John Anta');

        if ($fullUrl) {
            header("Location: $url");
            exit();
        }

        header("Location: " . APP_URI . "/$url");
        exit();
    }

    /**
     * Get user agent
     * @return string
     */
    public static function getUserAgent(): string
    {
        return "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36";
    }

    /**
     * Array to JSON string
     * @param $data
     * @return string
     */
    public static function toJson($data) : string{
        $json = '[]';
        if(is_object($data))
            $data = (array) $data;
        if(is_array($data))
            $json = json_encode($data);
        return $json;
    }

    /**
     * JSON string to array
     * @param string $json
     * @return mixed
     */
    public static function toArray(string $json) : array{
        $resp = json_decode($json, true);
        return is_array($resp) ? $resp : [];
    }

    /**
     * Array to Object
     * @param $data
     * @return mixed
     */
    public static function toObject($data): mixed
    {
        $obj = new \stdClass;
        if(is_array($data)){
            $obj = json_decode(json_encode($data));
        }elseif(self::isJson($data)){
            $obj = json_decode($data);
        }
        return $obj;
    }

    /**
     * Validate URL
     * @param $url
     * @return bool
     */
    public static function isUrl($url): bool
    {
        if (
            preg_match(
                "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",
                $url
            ) &&
            filter_var($url, FILTER_VALIDATE_URL)
        ) {
            return true;
        }
        return false;
    }


    /**
     * Format size units
     * @param $size
     * @return string
     */
    public static function formatSizeUnits($size): string
    {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        if(isset($units[$power])){
            return number_format($size / pow(1024, $power), 0, '.', ',') . ' ' . $units[$power];
        }
        return '0 B';
    }

    /**
     * Get current datetime
     * @param string $format
     * @return string
     */
    public static function timeNow(string $format = "Y-m-d H:i:s" ): string
    {
        $dt = new \DateTime("now");
        return $dt->format($format);
    }

    /**
     * Extract data from associative array
     * @param array $array
     * @param string|int $id
     * @return array
     */
    public static function extractData(array $array = [], string|int $id = 0): array
    {

        $result = [];

        if(!empty($array)){
            $result = array_map(function($var) use ($id) { return $var[$id] ?? '';  }, $array);
        }

        return $result;

    }

    /**
     * Date validation
     * @param $date
     * @param string $separator
     * @return bool
     */
    public static function isValidDate($date , string $separator = '-'): bool
    {
        $dateArr  = explode($separator, $date);
        if(count($dateArr) == 3){
            if (checkdate($dateArr[0], $dateArr[1], $dateArr[2])) {
                return true;
            }
        }
        return false;

    }

    /**
     * IP validation
     * @param $ip
     * @return bool
     */
    public static function isValidIp($ip): bool
    {
        return (bool) filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * Remove special chars from string
     * @param $string
     * @return string
     */
    public static function removeSpecialChars($string) : string{
        $string =  preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '', $string);
    }

    /**
     * Get app stored array data
     * @param string $filename
     * @return array
     */
    public static function getVarData(string $filename) : array{
        $file = self::cleanDS(ROOT . '/app/vars/' . $filename . '.php');
        if(is_file($file)){
            return include $file;
        }
        return [];
    }

    /**
     * Get directory size
     * @param $path
     * @return int
     */
    public static function GetDirectorySize($path): int
    {
        $bytesTotal = 0;
        $path = realpath($path);
        if($path!==false && $path!='' && file_exists($path)){
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
                $bytesTotal += $object->getSize();
            }
        }
        return $bytesTotal;
    }

    /**
     * Get countries list
     * @return array
     */
    public static function getCountryList() : array{
        $file = ROOT . '/app/vars/country_list.php';
        if(file_exists($file)){
            return include $file;
        }
        return [];
    }

    /**
     * Get country name by country code
     * @param string $code
     * @return string
     */
    public static function getCountryByCode(string $code) : string
    {
        $countries = self::getCountryList();
        $code = strtoupper($code);
        if (array_key_exists($code, $countries)) {
            return $countries[$code];
        }
        return "";
    }

    /**
     * Append file type (to buckets/files data)
     * @param array $data
     * @return array
     */
    public static function appendFileType(array $data = []): array
    {
        if(!empty($data)){
            foreach ($data as $k => $v){
                if(!isset($v['bucketId'])){
                    $data[$k]['ftype'] = 'bucket';
                }else{
                    unset($data[$k]['bucketId']);
                    $data[$k]['ftype'] = 'file';
                }
            }
        }
        return $data;
    }

    /**
     * Check unique action
     * @param $slug
     * @return bool
     */
    public static function isUniqSystemAction($slug): bool
    {
        $passed = false;
        $cpanelActionClass = "\\CloudMonster\\CPanel\\".$slug;
        $publicActionClass = "\\CloudMonster\\".$slug;
        if(!class_exists($cpanelActionClass) && !class_exists($publicActionClass)){
            $passed = true;
        }
        return $passed;
    }

    /**
     * Get storage path
     * @param string $subFolder
     * @return string
     */
    public static function storagePath(string $subFolder = ''): string
    {
        $dir = ROOT . '/' . STORAGE_DIR;
        if(!empty($subFolder)){
            $dir .= '/' . $subFolder;
            if(!file_exists($dir)){
                @mkdir($dir);
            }
        }
        return self::cleanDS($dir);
    }

    /**
     * Fix directory separator
     * @param $uri
     * @return string
     */
    public static function cleanDS($uri) : string{
        $ds = DIRECTORY_SEPARATOR;
        return str_replace(['/','\\'], $ds, $uri);
    }

    /**
     * Get locally uploaded file
     * @param $folderId
     * @return string
     */
    public static function getUploadedFile($folderId) : string{
        $dir =  self::cleanDS(Help::storagePath('tmp') . '/' . $folderId);
        return self::getFirstFile($dir);
    }

    /**
     * Get first file from directory
     * @param $dir
     * @return string
     */
    public static function getFirstFile($dir) : string{
        if(file_exists($dir) && is_dir($dir)){
            $filesList = scandir($dir);
            if(isset($filesList[2])){
                return Help::cleanDS($dir . '/' . $filesList[2]);
            }
        }
        return '';
    }

    /**
     * Create random string for slug
     * @param int $length
     * @return string
     */
    public static function slug(int $length = 15): string
    {
        $characters =
            "abcdefghijklmnopqrstuvwxyz";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Auto rename filename
     * @param $filename
     * @return string
     */
    public static function autoRename($filename): string
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        return pathinfo($filename, PATHINFO_FILENAME) . '_' . self::random(5) . '.'. $ext;
    }

    /**
     * Get http status code list
     * @return array
     */
    public static function getHttpStatusCodes() : array{
        $file = ROOT . '/app/vars/http_status_codes.php';
        if(file_exists($file)){
            return include $file;
        }
        return [];
    }

    /**
     * Get JSON data file
     * @param $file
     * @return string
     */
    public static function getVarJson($file) : string{
        $file = ROOT . '/app/vars/json/'.$file.'.json';
        if(file_exists($file)){
            return @file_get_contents($file);
        }else{
            die($file . ' :: Required var file does not exist');
        }
    }

    /**
     * Encrypt data
     * @param $str
     * @return string
     */
    public static function encrypt($str): string
    {
        $enc = openssl_encrypt($str, "AES-128-ECB", ENCRYPTION_KEY);
        return base64_encode($enc);
    }

    /**
     * Decrypt data
     * @param $str
     * @return string
     */
    public static function decrypt($str): string
    {
        $dec = base64_decode($str);
        $resp = openssl_decrypt($dec, "AES-128-ECB", ENCRYPTION_KEY);
        if(is_string($resp)){
            return $resp;
        }
        return '';
    }

    /**
     * file mime to extension
     * @param string $mime
     * @return string
     */
    public static function mime2ext(string $mime) : string {
        $mimeMap = self::getVarData('mime_types');
        return $mimeMap[$mime] ?? '';
    }

    /**
     * Delete directory
     * @param $dirPath
     * @return bool
     */
    public static function deleteDir($dirPath): bool
    {
        if (! is_dir($dirPath) ) {
            throw new \InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }

        $files = glob($dirPath . '*', GLOB_MARK);

        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }

        return rmdir($dirPath);

    }

    /**
     * Quick redirect to cpanel dashboard
     */
    public static function gotoCpanel() : void{
        self::redirect('cpanel/dashboard');
    }

    /**
     * Get public file link
     * @param $slug
     * @return string
     */
    public static function getFileLink($slug): string
    {
        return siteurl() . '/' . App::getCustomSlug('file') . '/' . $slug;
    }

    /**
     * Get public bucket link
     * @param $slug
     * @return string
     */
    public static function getBucketLink($slug): string
    {
        return siteurl() . '/' . App::getCustomSlug('bucket') . '/' . $slug;
    }


    /**
     * Format Drive status
     * @param $status
     * @return string
     */
    public static function formatDriveStatus($status): string
    {

        switch ($status){
            case 'active':
                $stHtml = '<span class="badge bg-success">'.$status.'</span>';
                break;
            case 'error':
                $stHtml = '<span class="badge bg-danger">'.$status.'</span>';
                break;
            case 'paused':
                $stHtml = '<span class="badge bg-warning">'.$status.'</span>';
                break;
            default:
                $stHtml = '<span class="badge bg-secondary">' .$status. '</span>';
                break;
        }

        return $stHtml;
    }

    /**
     * Size units to Bytes
     * @param string $from
     * @return int
     */
    public static  function convertToBytes(string $from): int {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $number = substr($from, 0, -2);
        $suffix = strtoupper(substr($from,-2));

        //B or no suffix
        if(is_numeric(substr($suffix, 0, 1))) {
            return preg_replace('/[^\d]/', '', $from);
        }

        $exponent = array_flip($units)[$suffix] ?? null;
        if($exponent === null) {
            return 1;
        }

        return $number * (1024 ** $exponent);
    }

    /**
     * 404 error request
     */
    public static function e404(){
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

    /**
     * Check development mode or not
     * @return bool
     */
    public static function isDev(): bool
    {
        return DEVELOPMENT_MODE;
    }

    /**
     * Get google drive file id
     * @param string $url
     * @return string
     */
    public static function getGoogleDriveId(string $url) : string{
        $path = explode('/', parse_url($url) ['path']);
        return (isset($path[3]) && !empty($path[3])) ? $path[3] : '';
    }

    /**
     * Get string between from content
     * @param string $string
     * @param string $start
     * @param string $end
     * @return string
     */
    public static function getStringBetween(string $string, string $start, string $end) : string {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * Check google drive file link
     * @param string $url
     * @return string
     */
    public static function isGoogleDrive(string $url) : string{
        if (str_contains($url, 'drive.google.com/file/d/')) {
            $id = self::getGoogleDriveId($url);
        }
        return !empty($id);
    }

    /**
     * Check onedrive file link
     * @param string $url
     * @return string
     */
    public static function isOneDrive(string $url) : string {
        if (str_contains($url, '1drv.ms') || str_contains($url, 'my.sharepoint.com')) {
            return true;
        }
        return false;
    }



}