<?php
include_once 'config.php';
include_once (MODELS . 'profilehandller.php');

$handler = new profilehandller ();

switch ($view) {
	// GET/12thdoor/duosoftware.profile.service/profile/getAll/?skip=0&take=20&class=Customer&Isascending=true&orderby=profileID
	case "getAll" :
		$skip = $_GET ["skip"];
		$take = $_GET ["take"];
		$class = $_GET ['class'];
		$orderby = $_GET ['orderby'];
		if (isset ( $_GET ['isAscending'] )) {
			$isAscending = $_GET ['isAscending'];
		} else {
			$isAscending = false;
		}
		$handler->getAll ( $skip, $take, $orderby, $class, $isAscending );
		break;
	// GET/twelthdoor/getAllByQuery?skip=1&take=2&query=?
	case "getAllByQuery" :
		$skip = $_GET ["skip"];
		$take = $_GET ["take"];
		$class = $_GET ['class'];
		$orderby = $_GET ['orderby'];
		$isAscending = $_GET ['isAscending'];
		$query = file_get_contents ( 'php://input' );
		
		$handler->getAllByQuery ( $skip, $take, $orderby, $class, $query, $isAscending );
		break;
	
	// POST /12thdoor/duosoftware.profile.service/profile/insert
	case "insert" :
		$postString = file_get_contents ( 'php://input' );
		$handler->insert ( $postString );
		break;
	
	// POST /12thdoor/duosoftware.profile.service/profile/update
	case "update" :
		$postString = file_get_contents ( 'php://input' );
		$handler->update ( $postString );
		break;
	
	// GET /twelthdoor/getProfileByKey?profilekey=1
	case "getProfileByKey" :
		$id = $_GET ['profilekey'];
		$handler->getProfileByKey ( $id );
		break;
	// saveToProfileActivity
	case "saveActivity" :
		$postString = file_get_contents ( 'php://input' );
		$handler->saveActivityAndComment ( $postString );
		break;
	
	// POST /12thdoor/duosoftware.profile.service/profile/updateActivity
	case "updateActivity" :
		$postString = file_get_contents ( 'php://input' );
		$handler->updateActivityAndComment ( $postString );
		break;
	
	// GET/twelthdoor/getAllByQuery?profilekey=<profileID>
	case "getActivity" :
		$id = $_GET ['profilekey'];
		$handler->getActivity ( $id );
		break;
	
	// POST /12thdoor/duosoftware.profile.service/profile/updateLastTransaction send the profileID
	case "updateLastTransaction" :
		$postString = file_get_contents ( 'php://input' );
		$handler->updateLastTransaction ( $postString );
		break;
	
	// case "insertCountries" :
	// $postString = file_get_contents ( 'php://input' );
	// $handler->insertCountries ( $postString );
	// break;
	
	// case "updateCountry" :
	// $postString = file_get_contents ( 'php://input' );
	// $handler->updateCountry ( $postString );
	// break;
	// // / getAllCountries?skip=1&take=2&orderby=?&isAscending=true
	// case "getAllCountries" :
	// $skip = $_GET ["skip"];
	// $take = $_GET ["take"];
	// $orderby = $_GET ['orderby'];
	
	// if (isset ( $_GET ['isAscending'] )) {
	// $isAscending = $_GET ['isAscending'];
	// } else {
	// $isAscending = false;
	// }
	// $handler->getAllCountries ( $skip, $take, $orderby, $isAscending );
	// break;
	
	case "getCountries" :
		$handler->getCountriesJson ();
		break;
	
	case "" :
		header ( 'HTTP/1.1 404 Not Found' );
		break;
	
	default :
		header ( 'HTTP/1.1 404 Not Found' );
		break;
}

?>
