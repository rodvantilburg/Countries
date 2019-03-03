<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css' integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>
<title>Country Quiz</title>

<?php
require_once 'Unirest.php';
require 'initialize.php';
require 'APIKeys.php';
?>
<style>
#myDIV {
   padding: 10px 10px;
  text-align: center;
  background-color: lightblue;
  margin-top: 2px;
  display: none;
}
.capital {
  font-size: 24px;
  margin: 5px 5px;
}
.instructions {
  font-size: 16px;
  margin: 5px 5px;
}
</style>
<!--<link rel="stylesheet" href="<?php echo CSS_PATH; ?>/main.css" type="text/css" /> -->
<link rel="stylesheet" href="CSS/main.css" type="text/css" />
</head>
<body>
<center>



<?php
// ***** load up country data
Unirest\Request::verifyPeer(false); 
$response = Unirest\Request::get("https://restcountries-v1.p.rapidapi.com/all",
  array(
    "X-RapidAPI-Key" => RAPIDAPI_KEY
  )
);


?>

<div id="myResults">
	<p id="result"></p>
	<p id="country"></p>
</div>
<div id="myFilter">Filter by Region <select class='regionselect' id='regionselect'></select><br></div>

<div id="StartButtons">
<h3>Match Countries to Capitals</h3>
<button id="StartButton1" onclick="Start(1,0)" class="SubmitButton">Pick the<br>Correct Country</button>
<button id="StartButton2" onclick="Start(0,0)" class="SubmitButton">Pick the<br>Correct Capital</button>
<h3>Match Countries to Flags</h3>
<button id="StartButton3" onclick="Start(1,1)" class="SubmitButton">Pick the<br>Correct Country</button>
<button id="StartButton4" onclick="Start(0,1)" class="SubmitButton">Pick the<br>Correct Flag</button>
</div>

<div id="myDIV">
	<p id="instructions1" class="instructions">The capital of</p>
	<p id="capital" class="capital"></p>
	<p id="instructions2" class="instructions">is:</p>
	<center>
	<table>
	<tr>
		<td><button onclick="SelectBox(1,0)" class="CountryButton" name="StoreSelections" id="button0">1</button></td>
		<td><button onclick="SelectBox(2,1)" class="CountryButton" name="StoreSelections" id="button1">2</button></td>
	</tr><tr>
		<td><button onclick="SelectBox(3,2)" class="CountryButton" name="StoreSelections" id="button2">3</button></td>
		<td><button onclick="SelectBox(4,3)" class="CountryButton" name="StoreSelections" id="button3">4</button></td>
	</tr>
	</table>
	</center>
</div>


<script>

var CorrectButton = 0;
var CorrectCountry = 0;
var Regions = ['World'];
var PHPCountries = <?php echo json_encode($response->body); ?>;
//alert(PHPCountries[0].name);
var NumCountries = PHPCountries.length;
var MyString = "NumCountries=" + NumCountries;
// remove countries with no listed capital and fill the Regions array
for (var id = NumCountries-1; id >= 0; id--) {
	if (PHPCountries[id].capital.length === 0) {
		MyString += "<br>" + PHPCountries[id].capital + "," + PHPCountries[id].name;
		PHPCountries.splice(id,1);
	} else {
		if (Regions.indexOf(PHPCountries[id].region) < 0) {
			//alert(PHPCountries[id].region);
			Regions.push(PHPCountries[id].region);
		}
	}
}
NumCountries = PHPCountries.length;
MyString += "<br>new NumCountries = " + NumCountries;
//document.getElementById("result").innerHTML = MyString;

// populate the region selector
var select = document.getElementById("regionselect"); 
for(var i = 0; i < Regions.length; i++) {
	var opt = Regions[i];
	//alert(opt);
	var el = document.createElement("option");
	el.textContent = opt;
	el.value = i;
	select.appendChild(el);
}

var Results = [];
var SelectCountry = 1;
var ShowFlags = 0;
var UsedCountries = [];


var testCount = 0;
function Start(n,m) {
	SelectCountry = n;
	ShowFlags = m;
	var x = document.getElementById("myDIV");
	x.style.display = "inline-block";

	// hide the start buttons
	document.getElementById("StartButtons").style.display = 'none';
	//document.getElementById("StartButton1").style.display = 'none';
	//document.getElementById("StartButton2").style.display = 'none';
	// keep the region displayed but disable it
	//document.getElementById("regionselect").disabled = true;
	document.getElementById("myFilter").style.display = 'none';
	
	testCount = 0;
	// reset Results, UsedCountries
	Results = [];
	UsedCountries = [];
	// Fill the quiz buttons
	FillButtons();
}
function FillButtons() {
	
	// Display the results 
	if (testCount >= 5) {
		DisplayResults();
	}
	else {
		
		// *** clear results
		document.getElementById("result").innerHTML = "";
		document.getElementById("country").innerHTML =  "" ;
		var e = document.getElementById("regionselect");
		var RegionID = e.options[e.selectedIndex].value;
		
		// pick which button will contain the correct answer
		CorrectButton = Math.floor(Math.random() * 4);
		var id;
		for (id = 0; id < 4; id++) {
			// Get a random country number
			var RandomCountry = GetUniqueCountry(RegionID);
			// reset button class
			document.getElementById('button'+id).disabled = false;
			if (ShowFlags && !SelectCountry) {
				document.getElementById('button'+id).style.backgroundColor = "lightblue";
			} else {
				document.getElementById('button'+id).style.backgroundColor = "blue";
			}
			document.getElementById('button'+id).setAttribute('onclick','SelectBox('+RandomCountry+','+id+')')
			if (SelectCountry) {
				document.getElementById('button'+id).innerHTML = PHPCountries[RandomCountry].name;
			} else {
				if (ShowFlags) {
					var countrycode = PHPCountries[RandomCountry].alpha2Code;
					var flagurl = "https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/flags/4x3/"+countrycode.toLowerCase()+".svg";
					document.getElementById('button'+id).innerHTML = "<img class='flag' src='"+flagurl+"'>";
				} else {
					document.getElementById('button'+id).innerHTML = PHPCountries[RandomCountry].capital;
				}
			}
			if (id === CorrectButton) {
				if (SelectCountry) {
					if (ShowFlags) {
						var countrycode = PHPCountries[RandomCountry].alpha2Code;
						var flagurl = "https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/flags/4x3/"+countrycode.toLowerCase()+".svg";
						document.getElementById('capital').innerHTML = "<img class='flagmedium' src='"+flagurl+"'>";
						document.getElementById('instructions2').innerHTML = "Is the flag of:";
					} else {
						document.getElementById('capital').innerHTML = PHPCountries[RandomCountry].capital;
						document.getElementById('instructions2').innerHTML = "Is the capital of:";
					}
					document.getElementById('instructions1').innerHTML = "";
				} else {
					document.getElementById('capital').innerHTML = PHPCountries[RandomCountry].name;
					document.getElementById('instructions1').innerHTML = "The capital of";
					if (ShowFlags) {
						document.getElementById('instructions1').innerHTML = "The flag of";
					}
					document.getElementById('instructions2').innerHTML = "is:";
				}
				CorrectCountry = RandomCountry;
			}
		}
	}
}

function GetUniqueCountry(Region) {
	//alert("Calling GetUniqueCountry");
	var Duplicate = 1;
	var CountryNum = 0;
	while (Duplicate >= 0) {
		CountryNum = Math.floor(Math.random() * NumCountries);
		Duplicate = UsedCountries.indexOf(CountryNum);
		if ((Duplicate < 0) && (Region > 0)) {
			if (PHPCountries[CountryNum].region != Regions[Region]) {
				Duplicate = 1;
			}
		}
	}
	UsedCountries.push(CountryNum);

	return CountryNum;
}
function SelectBox(countrynum,buttonnum) {
	testCount++;
	// change the correct button to green
	document.getElementById('button'+CorrectButton).style.backgroundColor = "green";
	var NewResult = {countrynum: CorrectCountry, correct: 1};
	// if selected button is incorrect change color to red
	if (CorrectButton != buttonnum) {
		document.getElementById('button'+buttonnum).style.backgroundColor = "red";
		NewResult.correct = 0;
	}
	Results.push(NewResult);
	

	// *** disable the buttons so they can't be selected during the pause
	for (var id = 0; id < 4; id++) {
			document.getElementById('button'+id).disabled = true;
	}
	// wait 1.5 seconds before moving on
	setTimeout(function(){ FillButtons(); }, 1500);
	
}
function DisplayResults() {
	
	// remove the quiz buttons
	var x = document.getElementById("myDIV");
	x.style.display = "none";
	
	var MyString = "<table>";
	var i;
	
	// Display the results
	for (i = 0;i < Results.length; i++) {
		var countrynum = Results[i].countrynum;
		MyString += "<tr><td>";
		if (Results[i].correct) {
			MyString += "<i class='fas fa-check' style='color:green'>";
		} else {
			MyString += "<i class='fas fa-times' style='color:red'>";
		}
		MyString += "</td><td>";
		if (ShowFlags) {
			var countrycode = PHPCountries[countrynum].alpha2Code;
			var flagurl = "https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/flags/4x3/"+countrycode.toLowerCase()+".svg";
			MyString += "<img class='flagsmall' src='"+flagurl+"'>";
		} else {
			MyString += PHPCountries[countrynum].capital;
		}
		MyString += "</td><td>" + PHPCountries[countrynum].name + "</td>";
		MyString += "<td><a href='CountryInfo.php?num="+PHPCountries[countrynum].alpha2Code+"'><i class='fa fa-info-circle' style='font-size:24px;color:blue'></i></a></td></tr>";
	}
	MyString += "</table>";
	
	document.getElementById("result").innerHTML = MyString;
	
	// display the start buttons
	document.getElementById("StartButtons").style.display = 'inline-block';
	//document.getElementById("StartButton1").style.display = 'inline-block';
	//document.getElementById("StartButton2").style.display = 'inline-block';
	// enable the region selector
	//document.getElementById("regionselect").disabled = false;	
	document.getElementById("myFilter").style.display = 'block';	
}</script>

</center></body>
</html>

