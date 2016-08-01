<?php

// Pull the Provider token from the query
$token = $_GET["token"];

$path_to_api_key_file = "/Janrain/apiKey.txt";

if (file_exists($path_to_api_key_file)) {}
else { 
    echo "<p>Sorry, could not find the api key file $path_to_api_key_file</p>";
    exit;
}

$api_key = trim(file_get_contents($path_to_api_key_file));

// Get ready to send the token and your api key to the Janrain API endpoint
$api_params = array (
    'token' => $token,
    'apiKey' => $api_key
 );

// Initialize the call to the Janrain API endpoint
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://rpxnow.com/api/v2/auth_info");
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $api_params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

// Make the call to the Janrain API endpoint
$auth_info_json = curl_exec($ch);

// Decode and read the response into an array
$auth_info = json_decode($auth_info_json, TRUE);

// Check to see if the request to Janrain was successful
if ($auth_info["stat"] === "ok") {
            
    // Establish values to be returned to browser
    
    // Status of query
    $returnValues["status"] = "OK";
    
    // Extract the user's profile into an array
    $profile = $auth_info["profile"];
    
    // Unique identifier
    $returnValues["identifier"] = $profile["identifier"];
    
    // Name
    $returnValues["name"] = $profile["name"]["givenName"];
    
    // Email
    $returnValues["email"] = $profile["email"];
    
}

else {
    $returnValues["status"] = "error";
}

// Encode the return values in json and send to browser
echo json_encode($returnValues);