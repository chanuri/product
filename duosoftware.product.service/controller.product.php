<?php

include_once 'config.php';
include_once(MODELS.'producthandller.php');

$handler =  new producthandller();

switch($view){	
	//  GET/twelthdoor/getAll/?skip=1&take=2&orderby=productCode&Isascending=true
	case "getAll":
		$skip = $_GET["skip"];
		$take = $_GET["take"];
		$order = $_GET["orderby"];
		$Isascending = $_GET["Isascending"];	
		$handler->getAll($skip, $take,$order,$Isascending);
		break;		
	//  GET/twelthdoor/getAllByQuery?skip=1&take=2&query=?	
	case "getAllByQuery":
		$skip = $_GET["skip"];
	    $take = $_GET["take"];
	    $order = $_GET['orderby'];
	    $Isascending = $_GET['Isascending'];
	    $query = file_get_contents('php://input');		   
		$handler->getAllByQuery($skip, $take,$order,$query,$Isascending);
		break;
		
		// POST	/twelthdoor/saveProduct
	case "insert":
		$postString = file_get_contents('php://input');		
		$handler->insert($postString);
		break;
		
	case "update":
		$postString = file_get_contents('php://input');
		$handler->update($postString);
		break;
		
	// GET	/twelthdoor/getProductByKey?produtkey=1
	case "getProductByKey": 
		$id = $_GET['produtkey'];
		$handler->getProductByKey($id);
		break;
	//saveToProductActivity
	case "saveActivity":
		$postString = file_get_contents('php://input');		
		$handler->saveToProductActivity($postString);
		break;
		//getActivity?produtID=0&skip=0&take=10
	case "getActivity":		
		$productid = $_GET['produtID'];
		$skip = $_GET["skip"];
		$take = $_GET["take"];
		$handler->getActivity($productid,$skip,$take);
		break;
		
	case "" :
		header('HTTP/1.1 404 Not Found');
		break;
}

?>
