<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

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


if (isset($_REQUEST['request_id']))
{
   $request_id = $_REQUEST['request_id'];
   echo("<!-- request id - ".$request_id."/-->".PHP_EOL);
   //$_SESSION['request_id']=$request_id;
   
   if(isset($_REQUEST['realm'])){
     $realm= $_REQUEST['realm'];
   }else{
     // $realm= "https://UPDATE_WITH_ENGAGE_APP_URL/";
    $realm= "https://janrain-se-demo.rpxnow.com";
  }
   echo("<!-- realm - ".$realm."/-->".PHP_EOL);
   
   if(isset($_REQUEST['requested_attributes'])){
     $requested_attributes= $_REQUEST['requested_attributes'];
   }else{
    $requested_attributes="sreg_nickname,sreg_email,sreg_fullname,sreg_dob,sreg_gender,sreg_postcode,sreg_country,sreg_language,sreg_timezone";
   }
   echo("<!-- req attr - ".$requested_attributes."/-->".PHP_EOL);

   if(isset($_REQUEST['blob'])){
     $blob= $_REQUEST['blob'];
   }else{
     $blob= "";
   }
   echo("<!-- blob - ".$blob."/-->".PHP_EOL);
   
   $show_login_form = TRUE;

   if($blob!=""){
     $ids_settings = json_decode($blob);
     //var_dump($ids_settings);
   }else{
   	$ids_settings = new stdClass();
   }
/*
    'sreg_nickname' => $decodedEntityResponse->result->displayName,
    'sreg_email' => $decodedEntityResponse->result->email,
    'sreg_gender'   => $gender,
    'sreg_dob'      => $dob,
    'sreg_country'   => $country,
    'sreg_postcode'  => $zip,
    'sreg_language'  => '',
    'sreg_timezone'  => '',
    
    login_username:"Userame",
	login_nickname: "Nickname",
   	login_email: "testrig@test.com",
    login_gender: "male",
    login_dob: "01-01-1970",
    login_country: "USA",
    login_postcode: "12345",
    login_language: "en-US",
    login_timezone: "PST"
*/ 
?>
<!DOCTYPE html>
<html>
<head>
<style>
input {width:100%;}

</style>
</head>
<body>
IDS Test Rig

<!--<form action="testrig_submit.php" method="POST" name="idsform">-->
<form action="ids_submit.php" method="POST" name="idsform">

<fieldset>
<legend>Form Elements:</legend>
Username:<br>
<input type="text" name="username" value="<?php echo(getSettingVal($ids_settings,'login_username'));?>">
<br>
Fullname:<br>
<input type="text" name="fullname" value="<?php echo(getSettingVal($ids_settings,'login_fullname'));?>">
<br>
Nickname:<br>
<input type="text" name="nickname" value="<?php echo(getSettingVal($ids_settings,'login_nickname'));?>">
<br>
Email:<br>
<input type="text" name="email" value="<?php echo(getSettingVal($ids_settings,'login_email'));?>">
<br>
Gender:<br>
<input type="text" name="gender" value="<?php echo(getSettingVal($ids_settings,'login_gender'));?>">
<br>
DOB:<br>
<input type="text" name="dob" value="<?php echo(getSettingVal($ids_settings,'login_dob'));?>">
<br>
Country:<br>
<input type="text" name="country" value="<?php echo(getSettingVal($ids_settings,'login_country'));?>">
<br>
Postal Code:<br>
<input type="text" name="postcode" value="<?php echo(getSettingVal($ids_settings,'login_postcode'));?>">
<br>
Language:<br>
<input type="text" name="language" value="<?php echo(getSettingVal($ids_settings,'login_language'));?>">
<br>
Time Zone:<br>
<input type="text" name="timezone" value="<?php echo(getSettingVal($ids_settings,'login_timezone'));?>">
<br>
realm:<br>
<input type="text" name="realm" value="<?php echo($realm);?>">
<br>
no_prompt:<br>
<select name="no_prompt">
  <option selected value="true">true</option>
  <option value="false">false</option>
</select>
<br>
requested_attributes:<br>
<input type="text" name="requested_attributes" value="<?php echo($requested_attributes);?>">
<br>
request_id:<br>
<input type="text" name="request_id" value="<?php echo($request_id);?>">
<br><br>
<input type="submit" value="Submit"></fieldset>
</form>


<?php
if(isset($ids_settings->auto_login)){
	if($ids_settings->auto_login == TRUE){
	    ?>

	    <script type="text/javascript">
		document.idsform.submit();
	    </script>

	    <?php
	}
}
?>

</body>
</html>

<?PHP
}else{
    //////////////////////////////////////////////////
    // If we got here something went wrong.
    // Update the content below to reflect a 
    // customer friendly error page
    //////////////////////////////////////////////////
?>
<!DOCTYPE html>
<html>
<head>
   <title>Janrain IDS Sample Form - Error</title>
</head>
<body>
    <h2>An error occurred.</h2>
    <h4>Please contact the administrator</h4>
    <br/>
    <?PHP
    echo "HTTP REQUEST ARRAY: <hr/>".PHP_EOL;
    var_dump($_REQUEST);
    echo("<hr>".PHP_EOL);
    ?>
</body>
</html>
<?PHP
}

function getSettingVal($settings, $key){
	$result = "";
	if(isset($settings->{$key})){
		$result = $settings->{$key};
	}
	return $result;
}