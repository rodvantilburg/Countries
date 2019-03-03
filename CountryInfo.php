<!DOCTYPE html >
<html lang="eng">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--meta name="viewport" content="width=device-width, initial-scale=1"-->
<title>Country Info</title>
<?php
require_once 'Unirest.php';
require 'initialize.php';
require 'APIKeys.php';
?>
<link rel="stylesheet" href="CSS/main.css" type="text/css" />

</head>
<body>
<center>
<header>

</header><br>

<?php

if (isset($_POST['countryname'])) {
	$countryname = $_POST['countryname'];
}
if (isset($_GET['num'])) {
	$countryname = $_GET['num'];
	//echo "num = ".$countryname."<br>";
}
Unirest\Request::verifyPeer(false); 
$response = Unirest\Request::get("https://restcountries-v1.p.rapidapi.com/alpha/$countryname",
  array(
    "X-RapidAPI-Key" => RAPIDAPI_KEY
  )
);
$country = $response->body;
$borderstring = "None";
$bordersearch = "";
$bordercount = 0;
foreach($country->borders as $border) {
	if ($bordercount > 0) {
		$bordersearch .= "%3b";  // semicolon
	}
	$bordersearch .= $border;
	$bordercount++;
}	
//echo $bordersearch."<br>";
if ($bordercount > 0) {
	$borderstring = "";
	$borderresponse = Unirest\Request::get("https://restcountries-v1.p.rapidapi.com/alpha/?codes=$bordersearch",
	  array(
		"X-RapidAPI-Key" => RAPIDAPI_KEY
	  )
	);
	foreach($borderresponse->body as $bordercountry) {
		$borderstring .= "<a href='CountryInfo.php?num=".$bordercountry->alpha2Code."'>";
		$borderstring .= $bordercountry->name;
		$borderstring .= "</a> ";
	}
}
// ** display Name and Flag
echo "<table><tr><td valign='center'><h1>".ucfirst($country->name)."</h1></td>";
echo "<td><img class='flagsmall' src='https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/flags/4x3/".strtolower($countryname).".svg'></td></tr></table>";
	


	
	
if (!$isMobile) {
	echo "<table width='100%'><col width='50%'><col width='50%'><tr><td colspan='2' valign='top'><center>";
}
//** table with summary on left and random fact on right
echo "<table width='100%'><col width='50%'><col width='50%'><tr>";
//** summary here
echo "<td valign='top'><center>";
echo "<table class='summarytable'>";
echo "<tr><td>Capital:</td><td>".ucfirst($country->capital)."</td></tr>";
echo "<tr><td>Region:</td><td>".ucfirst($country->region)."</td></tr>";
echo "<tr><td>SubRegion:</td><td>".ucfirst($country->subregion)."</td></tr>";
//echo "</table></center></td><td><center><table class='summarytable'>";
echo "<tr><td>Population:</td><td>".number_format($country->population)."</td></tr>";
echo "<tr><td>Area:</td><td>".number_format($country->area)." sq km</td></tr>";
echo "<tr><td>Borders:</td><td>".$borderstring."</td></tr>";
echo "</table></center></td>";
// ** random fact here
echo "<td valign='top'><center>";

// ** display Random Fact
$factURL = "https://vantilburger.com/CountryFactsAPI/fact/random.php?cc=".$countryname;
if ($factData = json_decode(@file_get_contents($factURL))) {
	//echo $factURL."<br>";
	//echo "<table><tr><td>";
	echo "<table class='facttable'><tr><td>".$factData->description."</td></tr>";
	if (strlen($factData->comment) > 0) {
		echo "<tr><td class='i'>".$factData->comment."</td></tr>";
	}
	if (strlen($factData->link) > 0) {
		echo "<tr><td><a href='".$factData->link."' target='_blank'>See more information</a></td></tr>";
	}
	echo "</table>";
}	

echo "</center></td>";
echo" </tr>";
echo "</table>";
if (!$isMobile) {
	echo "</center></td></tr><tr><td colspan='2' valign='top'><hr/></td></tr><tr><td valign='top'><center>";
} else {
	echo "<hr/>";
}
	
$NameAppend = "";
// remove spaces in the name
$NameURL = str_replace(' ','%20',ucfirst($country->name));

$wikiURL = "https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro&titles=".$NameURL."&redirects=true";
$content = "Error: Unable to retrieve Wikipedia summary";

if ($wikiData = json_decode(@file_get_contents($wikiURL))) {
	foreach ($wikiData->query->pages as $key=>$val) {
		$pageId = $key;
		break;
	}
	$content = $wikiData->query->pages->$pageId->extract;
}
// If a country name has multiple entries (like Georgia) specify '_country'
if (stripos($content,"usually refers to") > 0) {
	$NameAppend = "_country";
	$NameURL .= $NameAppend;
	$wikiURL = "https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro&titles=".$NameURL."&redirects=true";
	if ($wikiData = json_decode(@file_get_contents($wikiURL))) {
		foreach ($wikiData->query->pages as $key=>$val) {
			$pageId = $key;
			break;
		}
		$content = $wikiData->query->pages->$pageId->extract;
	}
}


?>
<table><tr><td valign='center'><h2>Wikipedia summary</h2></td><td> <a href='https://en.wikipedia.org/wiki/<?php echo $NameURL; ?>'><img src='wikipedia.png'></a></td></tr></table>

<div id="WikiDIV" class="WikiDIV">
<?php echo $content; ?>
</div>
<?php
if (!$isMobile) {
	echo "</center></td><td valign='top'><center>";
} else {
	echo "<br><br>";
}
?>
<table><tr><td valign='center'><h2>Google Map</h2></td><td></td></tr></table>

  <iframe
 style="border:1px solid; position: relative; height: 600px; width: 100%; "
  src="https://www.google.com/maps/embed/v1/place?key=<?php echo GOOGLE_KEY; ?>
    &q=<?php echo ucfirst($country->name).$NameAppend; ?>" allowfullscreen>
</iframe>
<?php
if (!$isMobile) {
	echo "</center></td></tr></table>";
}
?>
<h2><a href='index.php'>Home</a></h2>
</center></body>
</html>