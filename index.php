<!DOCTYPE html >
<html lang="eng">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css' integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>
<title>Countries</title>
<?php
require_once 'Unirest.php';
require 'initialize.php';
require 'APIKeys.php';
?>
<!--<link rel="stylesheet" href="<?php echo CSS_PATH; ?>/main.css" type="text/css" />-->
<link rel="stylesheet" href="CSS/main.css" type="text/css" />
<style>
.countryForm {
  font-size: 1em;
}
#myDIV {
font-size: 1.0em;
}

</style>
</head>
<body><center>
<header>


</header><br>
<div id="myDIV">
<?php
Unirest\Request::verifyPeer(false); 
$response = Unirest\Request::get("https://restcountries-v1.p.rapidapi.com/all",
  array(
    "X-RapidAPI-Key" => RAPIDAPI_KEY
  )
);
//print_r($response->body);

echo "<h2>Countries and Territories</h2>";
echo "<form method='post' action='CountryInfo.php'>";
echo "<select class='countryForm' name='countryname'>";
foreach($response->body as $country) {
	echo "<option value='".ucfirst($country->alpha2Code)."'>".ucfirst($country->name)."</option>";
}
echo "</select>"; 
echo "<button class='SubmitButton' type='submit' name='add-league'>Info</button>";
echo "</form><br><br><br>";
	
	

?>
<button onclick="window.location.href='QuizPick.php'" class="LinkButton">Take a Quiz</button>
</div>
</center></body>
</html>
