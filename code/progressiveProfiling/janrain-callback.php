<?php
// ini_set("display_errors",1);
/*
* This script is intended as an educational tool.
* Please look at the PHP SDK if you are looking for something suited to a new project.
* https://github.com/janrain/Janrain-Sample-Code/tree/master/php/janrain-engage-php-sdk
*/


/* STEP 2: Use the token to make the auth_info API call, using curl */
// $post_data = $_POST;
// $post_data = $_GET;

$client_credentials = array(
    'nssbgbn2jvyjcbq6rmzubd9m4yxz6mcy' => 'cv2txgjeh54bssb7mxr5npbnrrx98cvf',
);

$post_url = $_POST['apiUrl'] != '' ? $_POST['apiUrl'] : $_GET['apiUrl'];

$post_data = '';
if (count($_POST) > 0) {
    if (($client_credentials[$_POST['client_id']] != '') && ($_POST['client_secret'] == '')) {
        $_POST['client_secret'] = $client_credentials[$_POST['client_id']];
    }
    foreach ($_POST as $k => $v) {
        $post_data .= "&$k" . '=' . $v;
    }
} elseif(count($_GET) > 0) {
    if (($client_credentials[$_GET['client_id']] != '') && ($_GET['client_secret'] == '')) {
        $_GET['client_secret'] = $client_credentials[$_GET['client_id']];
    }
    foreach ($_GET as $k => $v) {
        $post_data .= "&$k" . '=' . $v;
    }  
}
$post_data = trim($post_data);
// $post_data = "type_name=user&client_id=nssbgbn2jvyjcbq6rmzubd9m4yxz6mcy&client_secret=cv2txgjeh54bssb7mxr5npbnrrx98cvf";
// print_r($post_data);

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $post_url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_FAILONERROR, true);
$result = curl_exec($curl);

/* STEP 3: output the result of the APi call */
header('Content-Type: application/json');
if ($result == false){
	$result = json_encode(array(
		'stat'		=> "error",
		'err_msg'	=> curl_error($curl),
		'err_code'	=> curl_errno($curl),
		'data_dump'	=> $post_data
	));
}
echo $result;
curl_close($curl);
?>