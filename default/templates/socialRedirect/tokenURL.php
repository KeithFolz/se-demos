<?php

// This is where all of the output from this script gets stored
$output = "";

// You must provide a path to your API key.
// The API key should be stored in a text file, in a local directory not 
// exposed to the web. The text file should contain your API key and nothing 
// else.

$path_to_api_key_file = "/Janrain/apiKey.txt";

if (file_exists($path_to_api_key_file)) {}
else { 
    echo "<p>Sorry, could not find the api key file $path_to_api_key_file</p>";
    exit;
}

$api_key = trim(file_get_contents($path_to_api_key_file));

$engage_pro = TRUE;

/* STEP 1: Extract token POST parameter */
// This POST comes from the Janrain Social Login server
$token = $_POST['token'];

if(strlen($token) == 40) { //test the length of the token; it should be 40 characters

  /* STEP 2: Use the token to make the auth_info API call */
  $post_data = array('token'  => $token,
                     'apiKey' => $api_key,
                     'format' => 'json',
                     'extended' => 'true'); //Extended is not available to Basic.

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info');
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_FAILONERROR, true);
  
  $result = curl_exec($curl);
  
  if ($result == false){
    $output .= "\n".'Curl error: ' . curl_error($curl);
    $output .= "\n".'HTTP code: ' . curl_errno($curl);
    $output .= "\n"; 
    $output .= print_r($post_data, TRUE);
  }
  curl_close($curl);

  /* STEP 3: Parse the JSON auth_info response */
  $auth_info = json_decode($result, true);

  if ($auth_info['stat'] == 'ok') {
    $output .= "\n auth_info:";
    $output .= "\n";
    $output .= print_r($auth_info, TRUE);

  }
  else {
     // Gracefully handle auth_info error.  Hook this into your native error handling system.
     $output .= "\n".'An error occured: ' . $auth_info['err']['msg']."\n";
     $output .= print_r($auth_info);
     $output .= "\n";
     $output .= print_r($post_data, TRUE);
  }
} else {
  // Gracefully handle the missing or malformed token.  Hook this into your native error handling system.
  $output .= "<p>Something wrong with the token.</p>";
}

$homeDir = "JanrainDemoSites";

// Finds out where we are in the filesystem and fixes path to fsHome

$fsHome = strstr(getcwd(), $homeDir, TRUE) . $homeDir;

include $fsHome . "/includes/includes.php";

/***************************************/

$params["content"] = "<p><b>Authentication was successful!</b></p>";
$params["content"] .= "<p>Here is the user's profile:</p>";
$params["content"] .= "<pre>" . $output . "</pre>";

$params["typeOfDemo"] = "socialRedirect";

showDemo($params, $fsHome);
