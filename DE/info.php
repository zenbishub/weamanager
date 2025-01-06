<?php 

phpinfo();
exit;

$url = 'https://zenbis.de/daimler/scanner.php';
// $data = array("first_name" => "First name","last_name" => "last name","email"=>"email@gmail.com","addresses" => array ("address1" => "some address" ,"city" => "city","country" => "CA", "first_name" =>  "Mother","last_name" =>  "Lastnameson","phone" => "555-1212", "province" => "ON", "zip" => "123 ABC" ) );

// $postdata = json_encode($data);

// $ch = curl_init($url); 
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
// curl_exec($ch);

//URL, Where the JSON data is going to be sent
// sending post request to reqres.in
//$url = "https://reqres.in/api/users";

//initialize CURL
$ch = curl_init();

//setup json data and using json_encode() encode it into JSON string
$data = array(
	'Employee' => 'Aman',
	'Job' => 'Data Scientist',
	'Company' => '<b>GeeksForGeeks</b>'
	);
$new_data = json_encode($data);

//options for curl
$array_options = array(
	
	//set the url option
	CURLOPT_URL=>$url,
	
	//switches the request type from get to post
	CURLOPT_POST=>true,
	
	//attach the encoded string in the post field using CURLOPT_POSTFIELDS
	CURLOPT_POSTFIELDS=>$new_data,
	
	//setting curl option RETURNTRANSFER to true 
	//so that it returns the response
	//instead of outputting it 
	CURLOPT_RETURNTRANSFER=>true,
	
	//Using the CURLOPT_HTTPHEADER set the Content-Type to application/json
	CURLOPT_HTTPHEADER=>array('Content-Type:application/json')
);

//setting multiple options using curl_setopt_array
curl_setopt_array($ch,$array_options);

// using curl_exec() is used to execute the POST request
$resp = curl_exec($ch);
print_R($resp);
	//decode the response
	// $final_decoded_data = json_decode($resp);
	// foreach($final_decoded_data as $key => $val){
	// echo $key . ': ' . $val . '<br>';
	// }

//close the cURL and load the page
curl_close($ch);
echo "durch";