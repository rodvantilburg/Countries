<!DOCTYPE html >
<html lang="eng">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--meta name="viewport" content="width=device-width, initial-scale=1"-->
<title>Country Info</title>
<?php
require_once 'Unirest.php';
require 'initialize.php';
$GoogleKey="AIzaSyCpcDGGdhbLZfwtQpk47nLioxtgwyF_sAM";
?>
<link rel="stylesheet" href="<?php echo CSS_PATH; ?>/main.css" type="text/css" />

</head>
<body>
<center>
<header>
<script type="text/javascript">
{
    "require-dev": {
        "mashape/unirest-php": "3.*"
    }
}
</script>

</header><br>

<?php

if (isset($_POST['countryname'])) {
	$countryname = $_POST['countryname'];
	//echo "countryname = ".$countryname."<br>";
}
//$countryname = "The_Bahamas";
if (isset($_GET['num'])) {
	$countryname = $_GET['num'];
	//echo "num = ".$countryname."<br>";
}
Unirest\Request::verifyPeer(false); 
$response = Unirest\Request::get("https://restcountries-v1.p.rapidapi.com/alpha/$countryname",
  array(
    "X-RapidAPI-Key" => "903cb22939mshc95c2d8531fe42dp10c2e1jsnc9f392158f30"
  )
);
	//print_r($response->body);
	//$country = $response->body[0];
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
			"X-RapidAPI-Key" => "903cb22939mshc95c2d8531fe42dp10c2e1jsnc9f392158f30"
		  )
		);
		foreach($borderresponse->body as $bordercountry) {
			$borderstring .= "<a href='CountryInfo.php?num=".$bordercountry->alpha2Code."'>";
			$borderstring .= $bordercountry->name;
			$borderstring .= "</a> ";
		}
	}
	
	echo "<table><tr><td valign='center'><h1>".ucfirst($country->name)."</h1></td>";
	echo "<td><img src='https://www.countryflags.io/".$countryname."/flat/64.png'></td></tr></table>";
if (!$isMobile) {
	echo "<table width='100%'><col width='50%'><col width='50%'><tr><td valign='top'><center>";
}
	echo "<table class='summarytable'>";
	echo "<tr><td>Capital</td><td>".ucfirst($country->capital)."</td></tr>";
	echo "<tr><td>Region</td><td>".ucfirst($country->region)."</td></tr>";
	echo "<tr><td>SubRegion</td><td>".ucfirst($country->subregion)."</td></tr>";
	echo "<tr><td>Population</td><td>".number_format($country->population)."</td></tr>";
	echo "<tr><td>Area</td><td>".number_format($country->area)." sq km</td></tr>";
	echo "<tr><td>Borders</td><td>".$borderstring."</td></tr>";
	
	
	echo "</table>";
	$NameAppend = "";
	$NameURL = str_replace(' ','%20',ucfirst($country->name));
	//$NameURL .= "_country";
	
	$wikiURL = "https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro&titles=".$NameURL."&redirects=true";
	//$wikiURL = str_replace(' ','%20',$wikiURL);
	//$content = "<h3>".ucfirst($country->name)."</h3>Wikipedia summary";
	$content = "Error: Unable to retrieve Wikipedia summary";
	
	if ($wikiData = json_decode(@file_get_contents($wikiURL))) {
		foreach ($wikiData->query->pages as $key=>$val) {
			$pageId = $key;
			break;
		}
		$content = $wikiData->query->pages->$pageId->extract;
		//echo $content;
	}
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
			//echo $content;
		}
	}


?>
<table><tr><td valign='center'><h2>Wikipedia summary</h2></td><td> <a href='https://en.wikipedia.org/wiki/<?php echo $NameURL; ?>'><img src='wikipedia.png'></a></td></tr></table>

<div id="WikiDIV" class="WikiDIV">
<?php echo $content; ?>
</div>
<?php
if (!$isMobile) {
	echo "</center></td><td valign='top'>";
} else {
	echo "<br><br>";
}
?>

  <!--width="800"
  height="800" 
  style="border:1px solid"-->
  <iframe
 style="border:1px solid; position: relative; height: 600px; width: 100%; "
  src="https://www.google.com/maps/embed/v1/place?key=<?php echo $GoogleKey; ?>
    &q=<?php echo ucfirst($country->name).$NameAppend; ?>" allowfullscreen>
</iframe>
<?php
if (!$isMobile) {
	echo "</td></tr></table>";
}
?>
<h2><a href='index.php'>Home</a></h2>
</center></body>
</html>