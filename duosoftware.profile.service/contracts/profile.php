<?php

Class Profile
{
	public $profileID;
	public $profileClass;//Customer,//Supplier
	public $profileType;// {Customer--> Company, Individual }{Supplier-> default}
	public $profileCategory;//{Default}
	public $email;
	public $profileName;
	public $firstName;
	public $lastName;
// 	public $name;
	public $phone;
	public $mobile;
	public $fax;
	public $website;
// 	public $adminMail;
	public $billingAddress = array(); // Address Type
	public $shippingAddress = array(); // Address Type
	public $deleteStatus;
	public $favouriteStar;	
	public $favouriteStarNo;
	public $notes = array(); // Notes Type
	public $status;
// 	public $tag;		
	public $lastTranDate;	
	public $createDate;
	public $modifyDate;
	public $createUser;
	public $modifyUser;
	
}

?>