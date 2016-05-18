<?php
include_once 'config.php';
require_once (CONTRACTS . 'profile.php');
require_once (CONTRACTS . 'address.php');
require_once (CONTRACTS . 'notes.php');
require_once (CONTRACTS . 'activityAndComment.php');
require_once (CONTRACTS . 'profileIncrement.php');
class profilehandller extends HttpResponse {
	public $dbtablename = "ProfileMaster";
	public $profileIncrementtb = "profileInc";
	public $profileActivityttb = "ProfileLog";
	public function getAll($skip, $take, $orderby, $IsAscending) {
		$client = ObjectStoreClient::WithNamespace ( DuoWorldCommon::GetHost (), $this->dbtablename, securityToken );
		$client->get ()->skip ( $skip );
		$client->get ()->take ( $take );
		
		if ($IsAscending) {
			$client->get ()->orderBy ( $orderby );
		} else {
			$client->get ()->orderByDsc ( $orderby );
		}
		
		$rawData = $client->get ()->all ();
		// //$rawData=array('testNo' => '1');
		if (empty ( $rawData )) {
			$statusCode = 404;
			$rawData = array (
					'error' => 'Not found!' 
			);
		} else {
			$statusCode = 200;
		}
		
		$this->publishResponse ( $rawData, 'application/json', $statusCode );
	}
	public function getAllByQuery($skip, $take, $orderby, $jsonstring, $IsAscending) {
		
		$query = json_decode ( $jsonstring, TRUE );
		
		$query1 = "select * from ProfileMaster where " . $query ["where"] . " ";
		//$query1 = "select * from ProfileMaster where " . $jsonstring . " ";
		//echo $query1;
		$client = ObjectStoreClient::WithNamespace ( DuoWorldCommon::GetHost (), $this->dbtablename, securityToken );
		
		$client->get ()->skip ( $skip );
		$client->get ()->take ( $take );
		$rawData = $client->get ()->byFiltering ( $query1 );
		if ($IsAscending)
			$client->get ()->orderBy ( $orderby );
		else
			$client->get ()->orderByDsc ( $orderby );
		
		if (empty ( $rawData )) {
			$statusCode = 404;
			$rawData = array (
					'error' => 'Not found!' 
			);
		} else
			$statusCode = 200;
		
		$this->publishResponse ( $rawData, 'application/json', $statusCode );
	}
	
	/*
	 * public function save($jsonstring)
	 * {
	 * $client=ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),$this->dbtablename,securityToken);
	 *
	 * $profile=new Profile();
	 * $profile= $this->convertToProfileObject(json_decode( $jsonstring, TRUE ));
	 *
	 * $rawData = $client->store()->byKeyField("email")->andStore($profile);
	 *
	 *
	 * if(empty($rawData)) {
	 * $statusCode = 404;
	 * $rawData = array('error' => 'error has occured while saving');
	 * } else {
	 * $statusCode = 200;
	 * }
	 *
	 * $this->publishResponse($rawData,'application/json',$statusCode);
	 *
	 * }
	 */
	public function insert($jsonstring) {
		$profile = new profile ();
		$input = json_decode ( $jsonstring, TRUE );
		$profile = $this->convertToProfileObject ( $input );
		$client = ObjectStoreClient::WithNamespace ( DuoWorldCommon::GetHost (), $this->dbtablename, securityToken );
		$newprofileCode = "";
		if (empty ( $profile->profileCode )) {
			$d = $profile->profileName;
			$newprofileCode = $this->generateProfileCode ( $d );
		}
		
		if (empty ( $newprofileCode )) {
			$statusCode = 404;
			$rawData = array (
					'error' => 'error has occured while generating profile code' 
			);
			$this->publishResponse ( $rawData, 'application/json', $statusCode );
		}
		
		$profile->profileCode = $newprofileCode;
		$profile->profileID = "-888";
		$rawData = $client->store ()->byKeyField ( "profileID" )->andStore ( $profile );
		
		if (empty ( $rawData )) {
			$statusCode = 404;
			$rawData = array (
					'error' => 'error has occured while saving profile' 
			);
		} else if ($rawData->IsSuccess == false) {
			$statusCode = 404;
			$rawData = array (
					'error' => 'error has occured while saving profile' 
			);
		} else {
// 			if (isset ( $input ["profileLog"] )) {
				
// 			} else
// 				$statusCode = 200;
			
			$activityObj = $this->convertToActivityObject ( $input ["profileLog"] );
			$activityObj->profileCode = $profile->profileCode;
			$activityObj->profileID = $rawData->Data [0]->ID;
			$activityRowData = $this->saveToProfileActivitytoOjectStore ( $activityObj ); // successfully saved profile id profile Object
			
			if (empty ( $activityRowData )) {	
				$statusCode = 404;
				$activityRowData = array (
						'error' => 'error has occured while saving activity'
				);
			} else
				$statusCode = 200;
			
		}
		
		$this->publishResponse ( $rawData, 'application/json', $statusCode );
	}
	public function Update($jsonstring) {
		$profile = new Profile ();
		$input = json_decode ( $jsonstring, TRUE );
		$profile = $this->convertToProfileObject ( $input );
		
		$client = ObjectStoreClient::WithNamespace ( DuoWorldCommon::GetHost (), $this->dbtablename, securityToken );
		
		$rawData = $client->store ()->byKeyField ( "profileID" )->andStore ( $profile );
		
		if (empty ( $rawData )) {
			$statusCode = 404;
			$rawData = array (
					'error' => 'error has occured while updating profile' 
			);
		} else {
			$activityObj = $this->convertToActivityObject ( $input ["profileLog"] );
			$activityObj->profileCode = $profile->profileCode;
			$activityObj->profileID = $rawData->Data [0]->ID;
			
			$activityRowData = $this->saveToProfileActivitytoOjectStore ( $activityObj ); // successfully saved profile id profile Object
			
			if (empty ( $activityRowData )) {
				$statusCode = 404;
				$activityRowData = array (
						'error' => 'error has occured while saving activity' 
				);
			} else
				$statusCode = 200;
		}
		
		$this->publishResponse ( $rawData, 'application/json', $statusCode );
	}
	public function getProfileByKey($profilekey) {
		$client = ObjectStoreClient::WithNamespace ( DuoWorldCommon::GetHost (), $this->dbtablename, securityToken );
		$rawData = $client->get ()->bykey ( $profilekey );
		if (empty ( $rawData )) {
			$statusCode = 404;
			$rawData = array (
					'error' => 'Not found!' 
			);
		} else {
			$statusCode = 200;
		}
		
		$this->publishResponse ( $rawData, 'application/json', $statusCode );
	}
	public function generateProfileCode($profileName) {
		$IsExistingProfile = TRUE;
		$client = ObjectStoreClient::WithNamespace ( DuoWorldCommon::GetHost (), $this->profileIncrementtb, securityToken );
		$clientprofile = ObjectStoreClient::WithNamespace ( DuoWorldCommon::GetHost (), $this->dbtablename, securityToken );
		$newprofilecode = "";
		$suffixLength = 4;
		$profileInc = new profileIncrement ();
		
		if (strlen ( $profileName ) >= 3) {
			$newcode = strtoupper ( substr ( $profileName, 0, 3 ) );
			$profileInc = $client->get ()->bykey ( $newcode );
			do {
				if (empty ( $profileInc )) {
					// generate new code if empty
					$newprofilecode = $newcode . "-" . "000" . "1";
					$profileInc = new profileIncrement ();
					$profileInc->profileSuffix = $newcode;
					$profileInc->nextNumber = 2;
					// echo "save code new";
					$rawData = $client->store ()->byKeyField ( "profileSuffix" )->andStore ( $profileInc );
				} else {
					// this logic is tempary .. need to get fix length and generate it
					$nextnum = $profileInc->nextNumber + 1;
					$nextnumlength = strlen ( $nextnum );
					
					if ($nextnumlength > $suffixLength) {
						$suffixLength = $nextnumlength;
					}
					
					$noofzeros = $suffixLength - strlen ( $nextnum );
					$zero = "";
					for($i = 1; $i <= strlen ( $noofzeros ); $i ++) {
						$zero = $zero . "0";
					}
					
					$newprofilecode = $newcode . "-" . $zero . $nextnum;
					$profileInc->nextNumber = $nextnum;
					// echo "update code";
					$rawData = $client->store ()->byKeyField ( "profileSuffix" )->andStore ( $profileInc );
				}
				
				$existProfile = $clientprofile->get ()->bykey ( $newprofilecode );
				
				if (empty ( $existProfile )) {
					$IsExistingProfile = FALSE;
				} else {
					$profile->profileCode = $newprofileCode;
				}
			} while ( $IsExistingProfile );
		}
		
		return $newprofilecode;
	}
	
	public function convertToProfileObject($input) {
		$outobject = new profile ();
		
		$outobject->profileClass = $input ["profileClass"];
		$outobject->profileType = $input ["profileType"];
		$outobject->profileCategory = $input ["profileCategory"];
		$outobject->email = $input ["email"];
		$outobject->profileName = $input ["profileName"];
		$outobject->firstName = $input ["firstName"];
		$outobject->lastName = $input ["lastName"];
		$outobject->name = $input ["name"];
		$outobject->phone = $input ["phone"];
		$outobject->mobile = $input ["mobile"];
		$outobject->website = $input ["website"];
		$outobject->adminMail = $input ["adminMail"];
		$outobject->billingAddress = $input ["billingAddress"];
		$outobject->shippingAddress = $input ["shippingAddress"];
		$outobject->deleteStatus = $input ["deleteStatus"];
		$outobject->favouriteStar = $input ["favouriteStar"];
		$outobject->favouriteStarNo = $input ["favouriteStarNo"];
		$outobject->notes = $input ["notes"];
		$outobject->status = $input ["status"];
		$outobject->tag = $input ["tag"];
		$outobject->lastTranDate = $input ["lastTranDate"];
		// chanuri Added
		$outobject->createDate = $input ["createDate"];
		$outobject->modifyDate = $input ["modifyDate"];
		$outobject->createUser = $input ["createUser"];
		$outobject->modifyUser = $input ["modifyUser"];
		
		return $outobject;
	}
	
	public function convertToActivityObject($input) {
		$activityObj = new activityAndComment ();
		
		$activityObj->profileCode = $input ["profileCode"];
		$activityObj->profileID = $input ["profileID"];
		$activityObj->logID = $input ["logID"];
		$activityObj->type = $input ["type"];
		$activityObj->description = $input ["description"];
		$activityObj->UIHeight = $input ["UIHeight"];
		$activityObj->status = $input ["status"];
		$activityObj->userName = $input ["userName"];
		$activityObj->lastTranDate = $input ["lastTranDate"];
		$activityObj->createDate = $input ["createDate"];
		$activityObj->modifyDate = $input ["modifyDate"];
		$activityObj->createUser = $input ["createUser"];
		$activityObj->modifyUser = $input ["modifyUser"];
		
		return $activityObj;
	}
	public function saveToProfileActivity($jsonstring) {
		$input = json_decode ( $jsonstring, TRUE );
		$activity = $this->convertToActivityObject ( $input );
		if (empty ( $activity->logID )) {
			$activity->logID = "-888";
		}
		$rawData = $this->saveToProfileActivitytoOjectStore ( $activity );
		
		if (empty ( $rawData )) {
			$statusCode = 404;
			$rawData = array (
					'error' => 'error has occured while saving profile activity and comment' 
			);
		} else {
			$statusCode = 200;
		}
		$this->publishResponse ( $rawData, 'application/json', $statusCode );
	}
	public function saveToProfileActivitytoOjectStore($proObj) {
		$client = ObjectStoreClient::WithNamespace ( DuoWorldCommon::GetHost (), $this->profileActivityttb, securityToken );
		return $client->store ()->byKeyField ( "logID" )->andStore ( $proObj );
	}
}

?>