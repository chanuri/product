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
	public $name;
	public $phone;
	public $mobile;
	public $website;
	public $adminMail;
	public $billingAddress; // Address Type
	public $shippingAddress; // Address Type
	public $deleteStatus;
	public $favouriteStar;	
	public $favouriteStarNo;
	public $notes;// Notes Type
	public $status;
	public $tag;		
	public $lastTranDate;	
	public $createDate;
	public $modifyDate;
	public $createUser;
	public $modifyUser;
	
}

?>