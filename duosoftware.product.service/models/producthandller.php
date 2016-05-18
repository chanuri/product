<?php

include_once 'config.php';
require_once(CONTRACTS.'brochure.php');
require_once(CONTRACTS.'product.php');
require_once(CONTRACTS.'tax.php');
require_once(CONTRACTS.'activityAndComment.php');
require_once(CONTRACTS.'productIncrement.php');

class producthandller extends HttpResponse
{
	public $dbtablename="productMaster";
	public $productIncrementtb="productInc";
	public $productActivityttb = "ProductLog";
	
	public function getAll($skip,$take,$orderby,$IsAscending)
	 { 		
	 	
	  $client=ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),$this->dbtablename,securityToken);
	  
	  $client->get()->skip($skip);
	  $client->get()->take($take);
	 
	  if($IsAscending) 
	   	$client->get()->orderBy($orderby);	  
	  else
	   	$client->get()->orderByDsc($orderby);
	 	 
	  $rawData=$client->get()->all();
	  
	  if(empty($rawData)) {
	   	$statusCode = 404;
	   	$rawData = array('error' => 'Not found!');
	  } else 
	   	$statusCode = 200;	  
	 
	  $this->publishResponse($rawData,'application/json',$statusCode);
	  
	 }
	
	public function getAllByQuery($skip,$take,$orderby,$jsonstring,$IsAscending)
	 {
	 	  $query= json_decode( $jsonstring, TRUE );
	  
 	 	  $query1 = "select * from ProductMaster where ".$query["where"]." ";
	 	 // echo $query1;
	 	  $client=ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),$this->dbtablename,securityToken);
		   
		  $client->get()->skip($skip);
		  $client->get()->take($take);
		  $rawData= $client->get()->byFiltering($query1);
		  if($IsAscending) 
		   	$client->get()->orderBy($orderby);		  
		  else
		   	$client->get()->orderByDsc($orderby);
		  	 
		  
		  if(empty($rawData)) {
		   	$statusCode = 404;
		   	$rawData = array('error' => 'Not found!');
		  } else
		   	$statusCode = 200;		  
		 
		  $this->publishResponse($rawData,'application/json',$statusCode);
	 }
	
	public function insert($jsonstring)
	{		
	 	$product=new Product(); 	
	 	$input=json_decode( $jsonstring, TRUE );
	 	$product= $this->convertToProductObject($input);	 	
	 	$client=ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),$this->dbtablename,securityToken);	 
	 	$newproductCode="";
	 	
	 	if(empty($product->productCode))
	 	{
	 		//echo "print".$product->productCode;
	 		$d=$product->productName;
	 		$newproductCode = $this->generateProductCode($d);	 		
	 	}	
	 	//echo "out".$product->productCode;
	 	if(empty($newproductCode))
	 	{
	 		$statusCode = 404;
	 		$rawData = array('error' => 'error has occured while generating product code');
	 		$this->publishResponse($rawData,'application/json',$statusCode);
	 	}
	 	
	 	$product->productCode= $newproductCode;	 
		$product->productID="-888";	  
		
		$rawData = $client->store()->byKeyField("productID")->andStore($product);
		//var_dump($rawData);
		if(($rawData->statusCode==404)) 
		{
			//$statusCode = 404;
			//$rawData = array('error' => 'error has occured while saving product');
		} else
		{
			$activityObj= $this->convertToActivityObject($input["productLog"]);
			
			$activityObj->productCode = $product->productCode;
		    $activityObj->productID =$rawData->ID;			
			$activityRowData = $this->saveToProductActivitytoOjectStore($activityObj); // successfully saved product id product Object 
			
// 			if(empty(($rawData->statusCode==404)) {
// 				$statusCode = 404;
// 				$activityRowData = array('error' => 'error has occured while saving activity');
// 			}else
// 				$statusCode = 200;
		}
	 
		$this->publishResponse($rawData,'application/json',$rawData->statusCode);
		
	}

	public function Update($jsonstring)
	{
		$product=new Product();
		$input=json_decode( $jsonstring, TRUE );
		$product= $this->convertToProductObject($input);	
		
		$client=ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),$this->dbtablename,securityToken);
		
		$rawData = $client->store()->byKeyField("productID")->andStore($product);
		
		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'error has occured while updating product');
		}
		else
		{
			$activityObj= $this->convertToActivityObject($input["productLog"]);				
			$activityObj->productCode = $product->productCode;
			$activityObj->productID =$rawData->Data[0]->ID;
			$activityObj->logID="-888";
			$activityRowData = $this->saveToProductActivitytoOjectStore($activityObj); // successfully saved product id product Object
			
			if(empty($activityRowData)) 
			{
				$statusCode = 404;
				$activityRowData = array('error' => 'error has occured while saving activity');
			}
			else
				$statusCode = 200;
		}
	
		$this->publishResponse($rawData,'application/json',$statusCode);
	
	}
	
	public function getProductByKey($produtkey)
	{
		$client = ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),$this->dbtablename,securityToken);
		$rawData=$client->get()->bykey($produtkey);
		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'Not found!');
		} else		{
			$statusCode = 200;
		}
	
		
		$this->publishResponse($rawData,'application/json',$statusCode);
	}

	public function generateProductCode($productName)
	{
		$IsExistingProduct=TRUE;
		$client=ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),$this->productIncrementtb ,securityToken);
		$clientproduct=ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),$this->dbtablename,securityToken);
		 
		$newproductcode="";
		$suffixLength=4;
		$producInc=new productIncrement();
		$prefixLength=3;
		//echo $productName;
		if(strlen($productName)>=3)
		{
			$newcode= strtoupper(substr($productName,0,$prefixLength));
			
			
			do
			{
				$producInc=$client->get()->bykey($newcode);
				if(empty($producInc))
				{
					//generate new code if empty
					$newproductcode= $newcode ."-" ."000"."1";
					$producInc = new productIncrement();
					$producInc->productSuffix=$newcode;
					$producInc->nextNumber=2;
					//echo "save code new";
					$rawData = $client->store()->byKeyField("productSuffix")->andStore($producInc);
				}
				else
				{
					//this logic is tempary .. need to get fix length and generate it
					$nextnum=$producInc->nextNumber+1;
					$nextnumlength = strlen($nextnum);
					
					if($nextnumlength>$suffixLength)
					{
						$suffixLength=$nextnumlength;
					}
					//echo "sub".$suffixLength . " ". $nextnum;
					$noofzeros=$suffixLength-strlen($nextnum);
					$zero="";
					//echo "no of zeros".$noofzeros;
					for ($i = 0; $i < $noofzeros; $i++)
					{
						$zero=$zero."0";
					}
					
					$newproductcode=$newcode ."-" .$zero.$nextnum;
					//echo "new code".$newproductcode;
					$producInc->nextNumber=$nextnum;				
					//echo "update code";
					$rawData = $client->store()->byKeyField("productSuffix")->andStore($producInc);
					
				}
				
				$existProduct=$clientproduct->get()->bykey($newproductcode);
				
				if(empty($existProduct))
				{
					$IsExistingProduct=FALSE;
				} 
				else
				{
					///$product->productCode= $newproductCode;
					$newcode=$newproductCode;
				}
				
				}
				while($IsExistingProduct);
					
		}
		
		return $newproductcode;
	}

	public function convertToProductObject($input)
	{	
		 
		$product=new Product();
		$product->productID= $input["productID"];
		$product->productCode= $input["productCode"];
		$product->productCategory= $input["productCategory"];
		$product->productUnit= $input["productUnit"];
		$product->productName= $input["productName"]; 		
		$product->baseCurrency= $input["baseCurrency"];
		$product->brand= $input["brand"];
		$product->costPrice= $input["costPrice"]; 
		$product->deleteStatus= $input["deleteStatus"];
		$product->description= $input["description"];
		$product->favouriteStar= $input["favouriteStar"];
		$product->favouriteStarNo= $input["favouriteStarNo"];
		$product->inventory= $input["inventory"];
		$product->productPrice= $input["productPrice"];
		$product->progressShow= $input["progressShow"];
		$product->status= $input["status"];
		$product->stockLevel= $input["stockLevel"];
		
 		$product->lastTranDate= $input["lastTranDate"];
		
		//chanuri Added
		$product->createDate= $input["createDate"];
		$product->modifyDate= $input["modifyDate"];
 		$product->createUser= $input["createUser"];
		$product->modifyUser= $input["modifyUser"];
		
		
		// upload brochure array 
		if (count($input["uploadBrochure"]) > 0){
			
			$brochure = new brochure();
			for($i=0; $i<=count($input["uploadBrochure"])-1; $i++){ 
				if(empty("broucherID"))
				{
					$brochure->broucherID="-888";
				}
				$brochure->name = $input["uploadBrochure"][$i]["name"];
				$brochure->size = $input["uploadBrochure"][$i]["size"]; 
				$brochure->type = "brochure";
				$product->uploadBrochure[] = $brochure;
			}
		} 
	 	
		// upload images array 
		if (count($input["uploadImages"]) > 0){
				
			$image = new brochure();
			for($i=0; $i<=count($input["uploadImages"])-1; $i++){
				if(empty("broucherID"))
				{
					$image->broucherID="-888";
				}
				$image->name = $input["uploadImages"][$i]["name"];
				$image->size = $input["uploadImages"][$i]["size"];
				$image->type = "image";
				$product->uploadImages[] = $image;
			}
		} 
		
		//product tax object 
		if (!empty((array) $input["productTax"])) {
			 $taxObj = new tax();
			 $taxObj->activate = $input["productTax"]["activate"];
			 $taxObj->compound = $input["productTax"]["compound"];
			 $taxObj->taxID = $input["productTax"]["taxID"];
			 $taxObj->labelIndividualTaxStatus = $input["productTax"]["labelIndividualTaxStatus"];
			 $taxObj->positionID = $input["productTax"]["positionID"];
			 $taxObj->rate = $input["productTax"]["rate"];
			 $taxObj->taxName = $input["productTax"]["taxName"];
			 $taxObj->type = $input["productTax"]["type"]; 
			 $product->productTax = $taxObj;
		} 
 
 		// array of customer fields 
		if (count($input["customFields"]) >0){
			for ($k=0; $k<=count($input["customFields"])-1; $k++){
				$product->customFields[] = $input["customFields"][$k];
			}
		}
		// array of tags
		if (count($input["tags"]) >0){
			for ($k=0; $k<=count($input["tags"])-1; $k++){
				$product->tag[] = $input["tags"][$k];
			}
		}		 
		 
		return $product;
	}
	
	public function convertToActivityObject($input)
	{
		$activityObj = new activityAndComment();
		
		$activityObj->productCode =  $input["productCode"];
		$activityObj->productID = $input["productID"];
		$activityObj->logID=$input["logID"];
		$activityObj->type = $input["type"];		
		$activityObj->description = $input["description"];
		$activityObj->UIHeight =$input["UIHeight"];
		$activityObj->status = $input["status"];
		$activityObj->userName = $input["userName"];
		$activityObj->lastTranDate = $input["lastTranDate"];
		$activityObj->createDate= $input["createDate"];
		$activityObj->modifyDate= $input["modifyDate"];
		$activityObj->createUser= $input["createUser"];
		$activityObj->modifyUser= $input["modifyUser"];
		
		return $activityObj;
	}
	
 	public function saveToProductActivity($jsonstring)
	{	  		
		$input=json_decode( $jsonstring, TRUE );
		$activity= $this->convertToActivityObject($input);
		if(empty($activity->logID))
		{
			$activity->logID="-888";
		}
	    $rawData=$this->saveToProductActivitytoOjectStore($activity);
	    
	   if(empty($rawData)) {
	   	$statusCode = 404;
	   	$rawData = array('error' => 'error has occured while saving product activity and comment');
	   } else
	   {
	   	$statusCode = 200;
	   }
	   $this->publishResponse($rawData,'application/json',$statusCode);
	  
 	}
 	
 	public function saveToProductActivitytoOjectStore($proObj)
 	{ 			
 			$client=ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),$this->productActivityttb,securityToken);
 			//echo "Hit";
 			//var_dump($proObj);
 			return $client->store()->byKeyField("logID")->andStore($proObj); 		
 	}
 	
} 


?>