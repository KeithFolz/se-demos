<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

ob_start();
if (!function_exists('curl_init')) {
  die('This script needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  die('This script needs the JSON PHP extension.');
}

echo('<!--'.PHP_EOL);
echo "SERVER VARIABLES:".PHP_EOL;
var_dump($_SERVER);
echo(PHP_EOL);
echo "HTTP REQUEST ARRAY:".PHP_EOL;
var_dump($_REQUEST);
echo(PHP_EOL);
echo('/-->'.PHP_EOL);



// $ids_api_key = 'REDACTED'; 
// $ids_postback_url = 'https://UPDATE_THIS.janrainfederate.com/ows_auth';
// $ids_return_url = 'https://UPDATE_THIS.janrainfederate.com/ows_return';

$ids_api_key = 'kBgXDqdthXu8c9zfk9X5DsidtxifXjWEFyXl0co2s';
$ids_postback_url = 'https://demos.janrainfederate.com/ows_auth';
$ids_return_url = 'https://demos.janrainfederate.com/ows_return';


$ids_request_id = $_REQUEST['request_id'];
if(!isset($ids_request_id) || $ids_request_id =="")
{
    die("ERROR: No IDS request id provided");
}

$requested_attributes = $_REQUEST['requested_attributes'];
if(!isset($requested_attributes) || $requested_attributes =="")
{
    die("ERROR: No requested_attributes provided");
}

$no_prompt = 'true';
if(isset($_REQUEST['no_prompt']) && $_REQUEST['no_prompt']== 'false')
{
    $no_prompt = 'false';
}

$params = array(
 	'request_id'    => $ids_request_id, 
	'api_key'       => $ids_api_key,
	'requested_attributes'  => $requested_attributes,
	'no_prompt'     => $no_prompt,
	'username'      => urlencode($_REQUEST['username']),
	'realm'         => $_REQUEST['realm'],
	'sreg_nickname' => $_REQUEST['nickname'],
    'sreg_fullname' => $_REQUEST['fullname'],
	'sreg_email' => $_REQUEST['email'],
	'sreg_gender'   => $_REQUEST['gender'],
	'sreg_dob'      => $_REQUEST['dob'],
	'sreg_country'   => $_REQUEST['country'],
	'sreg_postcode'  => $_REQUEST['postcode'],
	'sreg_language'  => $_REQUEST['language'],
	'sreg_timezone'  => $_REQUEST['timezone']
	//'blob'   => "lastName=#{".$decodedEntityResponse->result->familyName."}&firstName=#{".$decodedEntityResponse->result->givenName."}&uuid=#{".$decodedEntityResponse->result->uuid."}"
	);
            
echo('<!--'.PHP_EOL);
echo(http_build_query($params));
echo(PHP_EOL);
echo($ids_postback_url);
echo(' /-->'.PHP_EOL);

$idsResponse = postToIDS($ids_postback_url, $params);

echo('<!--'.PHP_EOL);

var_dump($params);
echo(PHP_EOL);
var_dump($idsResponse);

echo('/-->'.PHP_EOL);

if ($idsResponse == 200){

	//die("Killed");

	header('Location: '.$ids_return_url.'?request_id='.$ids_request_id);
	
	//echo('Location: '.$ids_return_url.'?request_id='.$ids_request_id);
}
else {
	echo "Error";
	
	echo "<p>the response is : $idsResponse";
	
	foreach ($params as $key => $param) {
		echo "<p>$key: $param</p>";
	}
}

$debug_out = ob_get_contents();
ob_end_clean();

function postToIDS($url, $data) {
    $agent = $_SERVER["HTTP_USER_AGENT"];
    $curlData = http_build_query($data);
    $curlData = str_replace(" ", '%20', $curlData);
    $curlData = str_replace("+", '%20', $curlData);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
    //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_USERAGENT,$agent);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlData);
    
    $response = curl_exec($curl);

     echo('<!-- CURLINFO_EFFECTIVE_URL'.PHP_EOL);
    var_dump(curl_getinfo($curl, CURLINFO_EFFECTIVE_URL));
    echo(PHP_EOL.'/-->'.PHP_EOL);
    

    if ($response === false) {
        echo "curl error: " . curl_error($curl);
    }
    else {
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    }
    curl_close($curl);
        
    return $http_status;
}

?>
<!DOCTYPE html>
<html>
<head>
<title>IDS Test Rig Submit Page
</head>
<body>
<pre>
<?php echo $debug_out; ?>
</pre>
</body>
</html>