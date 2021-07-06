<?php
function exceptionHandler($e) {
   $msg = array("status" => "500", "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine());
   $usr_msg = array("status" => "500", "message" => "Sorry! Internal server error");

   header("Access-Control-Allow-Origin: *"); 
   header("Content-Type: application/json; charset=UTF-8"); 
   header("Access-Control-Allow-Methods: GET, POST");
   echo json_encode($usr_msg);
   logError($msg);
}
set_exception_handler('exceptionHandler');

function errorHandler($errno, $errstr, $errfile, $errline) {
 if ($errno != 2 && $errno != 8) {
   throw new Exception("Fatal Error Detected: [$errno] $errstr line: $errline", 1);
 } else {
 	logError("Fatal Error Detected: [$errno] $errstr line: $errline");
 }
}
set_error_handler('errorHandler');

function logError($msg) {
 	file_put_contents('log.txt', $msg."\n",FILE_APPEND);
}

function autoloadClasses($className) {
 $filename = "classes\\" . strtolower($className) . ".class.php";
 $filename = str_replace('\\', DIRECTORY_SEPARATOR, $filename);
 if (is_readable($filename)) {
   include_once $filename;
 } else {
 	throw new exception("File not found: " . $className . " (" . $filename . ")");
 }

}
spl_autoload_register("autoloadClasses");

$ini['main'] = parse_ini_file("config.ini",true);

define('BASEPATH', $ini['main']['paths']['basepath']);
define('CSSPATH', $ini['main']['paths']['css']);
define('JWTKEY', $ini['main']['JWTKey']['key']);
?>