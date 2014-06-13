<?php
   	$url = 'https://s1851.t.eloqua.com/e/f2';
   	$subject = $_POST["subject"];
   	$customer_service = $_POST["customer_service"];
   	$emailAddress = $_POST["emailAddress"];
   	$firstName = $_POST["firstName"];
   	$lastName = $_POST["lastName"];
   	$comments = $_POST["comments"];
   	$elqFormName = $_POST["elqFormName"];
	$elqSiteId = $_POST["elqSiteId"];
	$elqCampaignId = $_POST["elqCampaignId"];
	$website = $_POST["website"];

   	// else post form
	$data = array('subject' => $subject, 'customer_service' => $customer_service, 'emailAddress' => $emailAddress, 'firstName' => $firstName, 'lastName' => $lastName, 'comments' => $comments, 'elqFormName' => $elqFormName, 'elqSiteId' => $elqCampaignId, 'elqCampaignId' => $elqCampaignId);
	//$data = array('key1' => 'value1', 'key2' => 'value2');

	foreach ($data as $k => $v) {
	    echo "\$data[$k] => $v.\n";
	}


?>