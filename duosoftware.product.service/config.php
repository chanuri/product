

<?php
	/* 	main configurations file
		file - config.php
		created - 2016/03/25
		Auther - DuoSoftware
		Development by - Ruchira Perera
	*/
header("Access-Control-Allow-Origin: * ");
header("Access-Control-Allow-Methods: PUT,POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: origin, x-requested-with, content-type, securityToken");
	
define('VERSION',"6.0.0.3");

ini_set('display_errors', '1');
$server_environment = "live"; //local/live


$doc= $_SERVER['DOCUMENT_ROOT'];

//check PHP Variables in php info.php file

switch($server_environment){

case 'local':
	//Database name 
	//http://test.12thdoor.com:3000/
	define('DB_URL',"http://test.12thdoor.com:3000");
	define('NAMESPACE_DB',"twelthdoorV2");
	
	define('DEBUG',true);
	
///PHP/TwelthDoor/duosoftware.product.service/
	define('DOC_ROOT',$doc . '/PHP/TwelthDoor/duosoftware.product.service/');
	define('CONTRACTS', DOC_ROOT . 'contracts/');
	define('CONTROLLERS',DOC_ROOT . 'controllers/');
	define('MODELS', DOC_ROOT . 'models/');	
	define('HELPERS', DOC_ROOT . 'helpers/');
	
	define('HOST_URL','');
	
	define('ADMIN_NAME','chanuri');
	define('ADMIN_EMAIL','chanuri@duosoftware.com');
	define('CONTACT_EMAIL','chanuri@duosoftware.com');
	
	define('SMTP_SERVER','mail.gmail.com');
	define('SMTP_PWD','password');
	define('SMTP_USER','mail.gmail.com');
	
	$Requestheader = apache_request_headers();
	
	if(isset($Requestheader['securityToken']) == null){
		header("HTTP/1.1". " 203 ". " Non-Authoritative Information ");
		echo '<h1>Running under unautherized Access</h1>';
		exit();
	}else{
		define('securityToken',$Requestheader['securityToken']);
	}
break;

case 'live':
	//Database name....	
	//http://localhost:3000
	//test.twelthDoor.com
	define('DB_URL',"http://localhost:3000");
	define('NAMESPACE_DB',"twelthdoorV2");
	
	define('DEBUG',true);
///v2/services/
	define('DOC_ROOT',$doc . '/v2/services/duosoftware.product.service/');
	define('CONTRACTS', DOC_ROOT . 'contracts/');
	define('CONTROLLERS',DOC_ROOT . 'controllers/');
	define('MODELS', DOC_ROOT . 'models/');	
	define('HELPERS', DOC_ROOT . 'helpers/');
	
	define('HOST_URL','');
	
	define('ADMIN_NAME','chanuri');
	define('ADMIN_EMAIL','chanuri@duosoftware.com');
	define('CONTACT_EMAIL','chanuri@duosoftware.com');
	
	define('SMTP_SERVER','mail.gmail.com');
	define('SMTP_PWD','password');
	define('SMTP_USER','mail.gmail.com');
	
	//DuoWorldCommon::CheckAuth ();
	//define ( 'securityToken', $_COOKIE ['securityToken'] );
	
	$Requestheader = apache_request_headers();
	
	if(isset($Requestheader['securityToken']) == null){
		header("HTTP/1.1". " 203 ". " Non-Authoritative Information ");
		echo '<h1>Running with unautherized Access</h1>';
		exit();
	}
	else{
		define('securityToken',$Requestheader['securityToken']);
	}
break;

}

require_once(HELPERS.'Common.php');
require_once(HELPERS."HttpResponse.php");
require_once(HELPERS."ObjectStoreClient.php");

$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];
	

?>