<!DOCTYPE html >
<html lang="eng">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--meta name="viewport" content="width=device-width, initial-scale=1"-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css' integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>
<title>Countries</title>
<?php
require_once 'Unirest.php';
?>
<style>
.countryForm {
  font-size: 1em;
}
#myDIV {
font-size: 1.5em;
}
.SubmitButton {
  background-color: white; 
  border: none;
  color: black;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
}
.LinkButton {
  background-color: #0055DD; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
}
</style>
</head>
<body><center>
<header>
<script type="text/javascript">
{
    "require-dev": {
        "mashape/unirest-php": "3.*"
    }
}
</script>

</header><br>
<div id="myDIV">
<?php
Unirest\Request::verifyPeer(false); 
$response = Unirest\Request::get("https://restcountries-v1.p.rapidapi.com/all",
  array(
    "X-RapidAPI-Key" => "903cb22939mshc95c2d8531fe42dp10c2e1jsnc9f392158f30"
  )
);
	//print_r($response->body);

	echo "<h2>Country Info</h2>";
	echo "<form method='post' action='CountryInfo.php'>";
	echo "<select class='countryForm' name='countryname'>";
foreach($response->body as $country) {
		//echo $row['SeasonName']."<br>";
		echo "<option value='".ucfirst($country->alpha2Code)."'>".ucfirst($country->name)."</option>";
		//echo "<option value='".ucfirst($country->name)."'>".ucfirst($country->name)."</option>";
	}
	echo "</select>"; 
	echo "<button class='SubmitButton' type='submit' name='add-league'><i class='fa fa-info-circle' style='font-size:24px;color:blue'></i> View Info</button>";
	echo "</form><br><br><br>";
	
	
//foreach($response->body as $country) {
//    echo ucfirst($country->capital).", ".ucfirst($country->name) ."(".ucfirst($country->region).",".ucfirst($country->subregion). ")<br>";
//}	
?>
<button onclick="window.location.href='CapitolTest.php'" class="LinkButton">Capital-Country<br>Pickum Quiz</button>
</div>
</center></body>
</html>
