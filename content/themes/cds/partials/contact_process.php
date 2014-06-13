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

   if($website != ''){
	   // website field is filled in then it is probaly a bot so don't submit form
   		echo "You are a bot!";
	   exit();
	}
   	// else post form
	/*$data = array('subject' => $subject, 'customer_service' => $customer_service, 'emailAddress' => $emailAddress, 'firstName' => $firstName, 'lastName' => $lastName, 'comments' => $comments, 'elqFormName' => $elqFormName, 'elqSiteId' => $elqCampaignId, 'elqCampaignId' => $elqCampaignId);
	//$data = array('key1' => 'value1', 'key2' => 'value2');

	foreach ($data as $k => $v) {
	    echo "\$data[$k] => $v.\n";
	}

# Form data string
$postString = http_build_query($data, '', '&');


	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data),
	    ),
	);
	$context  = stream_context_create($options);
	//$result = file_get_contents($url, false, $context);
	$result = http_post_data($url, $data);

	var_dump($result);



*/


	# Our new data
	$data = array('subject' => $subject, 'customer_service' => $customer_service, 'emailAddress' => $emailAddress, 'firstName' => $firstName, 'lastName' => $lastName, 'comments' => $comments, 'elqFormName' => $elqFormName, 'elqSiteId' => $elqCampaignId, 'elqCampaignId' => $elqCampaignId);


	# Create a connection
	//$url = '/api/update';
	$ch = curl_init();

	# Form data string
	//$postString = http_build_query($data, '', '&');
	//url-ify the data for the POST
	foreach($data as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	# Setting our options
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, count($fields));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

	# Get the response
	$response = curl_exec($ch);
echo "response: " + $response;
	curl_close($ch);
/*
	// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 0,
    CURLOPT_URL => 'https://s1851.t.eloqua.com/e/f2',
    CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $data
    
));

// Send the request & save response to $resp
$resp = curl_exec($curl);
echo $resp;
// Close request to clear up some resources
curl_close($curl);*/
?>
</script>