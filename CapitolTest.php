<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css' integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>
<title>Capital Quiz</title>
<style>
#myDIV {
  width: 100%;
  padding: 10px 0;
  text-align: center;
  background-color: lightblue;
  margin-top: 20px;
  display: none;
}
.CountryButton {
  background-color: blue; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
      height:150px;
    width:150px;
}
.SubmitButton {
  background-color: #0055DD; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
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
<?php
require_once 'Unirest.php';
?>
</head>
<body>
<center>
<script type="text/javascript">
{
    "require-dev": {
        "mashape/unirest-php": "3.*";
    }
}

</script>


<?php
// ***** load up country data
Unirest\Request::verifyPeer(false); 
$response = Unirest\Request::get("https://restcountries-v1.p.rapidapi.com/all",
  array(
    "X-RapidAPI-Key" => "903cb22939mshc95c2d8531fe42dp10c2e1jsnc9f392158f30"
  )
);

foreach($response->body as $country) {
		//echo $row['SeasonName']."<br>";
		//echo "<option value='".ucfirst($country->alpha2Code)."'>".ucfirst($country->name)."</option>";
		//echo "<option value='".ucfirst($country->name)."'>".ucfirst($country->name)."</option>";
	}

?>


<button id="StartButton1" onclick="Start(1)" class="SubmitButton">Pick the Correct Country</button>
<button id="StartButton2" onclick="Start(0)" class="SubmitButton">Pick the Correct Capital</button>

<div id="myDIV">
<p id="capital" class="capital"></p>
<p id="instructions" class="instructions">Select the matching country</p>
		<center><table width:100%><tr><td>
             <button onclick="SelectBox(1,0)" class="CountryButton" name="StoreSelections" id="button0">1</button>
		</td><td>
             <button onclick="SelectBox(2,1)" class="CountryButton" name="StoreSelections" id="button1">2</button>
		</td></tr><tr><td>
             <button onclick="SelectBox(3,2)" class="CountryButton" name="StoreSelections" id="button2">3</button>
		</td><td>
             <button onclick="SelectBox(4,3)" class="CountryButton" name="StoreSelections" id="button3">4</button>
		</td></tr></table></center>

</div>
<div id="myResults">
<p id="result"></p>
<p id="country"></p>
<!--<button id="RestartButton" onclick="Start()">Restart</button> -->

<p></p>
</div>

<script>

	var CorrectButton = 0;
	var CorrectCountry = 0;
var PHPCountries = <?php echo json_encode($response->body); ?>;
//alert(PHPCountries[0].name);
var NumCountries = PHPCountries.length;
var MyString = "NumCountries=" + NumCountries;
	// remove countries with no listed capital
	for (var id = NumCountries-1; id >= 0; id--) {
		if (PHPCountries[id].capital.length === 0) {
			MyString += "<br>" + PHPCountries[id].capital + "," + PHPCountries[id].name;
			PHPCountries.splice(id,1);
		}
	}
	NumCountries = PHPCountries.length;
	MyString += "<br>new NumCountries = " + NumCountries;
	//document.getElementById("result").innerHTML = MyString;
		



var Results = [];
var SelectCountry = 1;
var UsedCountries = [];


var testCount = 0;
function Start(n) {
	SelectCountry = n;
  var x = document.getElementById("myDIV");
  //if (x.style.display === "none") {
    x.style.display = "block";
  //} else {
  //  x.style.display = "none";
  //}
  document.getElementById("StartButton1").style.display = 'none';
  document.getElementById("StartButton2").style.display = 'none';
  testCount = 0;
  Results = [];
  UsedCountries = [];
  FillButtons();
}
function FillButtons() {
	
	if (testCount >= 5) {
		DisplayResults();
	}
	else {
		
		// *** clear results
		document.getElementById("result").innerHTML = "";
		document.getElementById("country").innerHTML =  "" ;
		
		CorrectButton = Math.floor(Math.random() * 4);
		var id;
		for (id = 0; id < 4; id++) {
			//var RandomCountry = Math.floor(Math.random() * NumCountries);
			var RandomCountry = GetUniqueCountry();
			// reset button class
			document.getElementById('button'+id).disabled = false;
			document.getElementById('button'+id).style.backgroundColor = "blue";
			document.getElementById('button'+id).setAttribute('onclick','SelectBox('+RandomCountry+','+id+')')
			if (SelectCountry) {
				document.getElementById('button'+id).innerHTML = PHPCountries[RandomCountry].name;
			} else {
				document.getElementById('button'+id).innerHTML = PHPCountries[RandomCountry].capital;
			}
			if (id === CorrectButton) {
				if (SelectCountry) {
					document.getElementById('capital').innerHTML = PHPCountries[RandomCountry].capital;
					document.getElementById('instructions').innerHTML = "Select the correct country";
				} else {
					document.getElementById('capital').innerHTML = PHPCountries[RandomCountry].name;
					document.getElementById('instructions').innerHTML = "Select the correct capital";
				}
				CorrectCountry = RandomCountry;
			}
				
		}
	}
}

function GetUniqueCountry() {
	//alert("Calling GetUniqueCountry");
	var Duplicate = 1;
	var CountryNum = 0;
	while (Duplicate > 0) {
		CountryNum = Math.floor(Math.random() * NumCountries);
		Duplicate = UsedCountries.indexOf(CountryNum);	
	}
	UsedCountries.push(CountryNum);
	//var i;
	//var text = "";
	//for (i = 0; i < UsedCountries.length; i++) {
	//	text += UsedCountries[i] + " ";
	//}
	//alert(text);
	return CountryNum;
}
function SelectBox(countrynum,buttonnum) {
	testCount++;
	//document.getElementById("country").innerHTML =  " Country: " + PHPCountries[countrynum].name ;
	document.getElementById('button'+CorrectButton).style.backgroundColor = "green";
	var NewResult = {countrynum: CorrectCountry, correct: 1};
	//NewResult.countrynum = countrynum;
	//NewResult.correct = 1;
	if (CorrectButton != buttonnum) {
		document.getElementById('button'+buttonnum).style.backgroundColor = "red";
		NewResult.correct = 0;
	}
	Results.push(NewResult);
	
	//var MyString = "";
	//var i;
	//for (i = 0;i < Results.length; i++) {
	//	MyString += Results[i].countrynum + "," + Results[i].correct + "<br>";
	//}
	//document.getElementById("result").innerHTML = MyString;
	
	// *** disable buttons 
	for (var id = 0; id < 4; id++) {
			document.getElementById('button'+id).disabled = true;
	}

	setTimeout(function(){ FillButtons(); }, 1500);
	
}
function DisplayResults() {
	var x = document.getElementById("myDIV");
	x.style.display = "none";
	 

	var MyString = "<table>";
	var i;
	
	for (i = 0;i < Results.length; i++) {
		var countrynum = Results[i].countrynum;
		MyString += "<tr><td>";
		if (Results[i].correct) {
			MyString += "<i class='fas fa-check' style='color:green'>";
		} else {
			MyString += "<i class='fas fa-times' style='color:red'>";
		}
		MyString += "</td><td>" + PHPCountries[countrynum].capital + "</td><td>" + PHPCountries[countrynum].name + "</td>";
		MyString += "<td><a href='CountryInfo.php?num="+PHPCountries[countrynum].alpha2Code+"'><i class='fa fa-info-circle' style='font-size:24px;color:blue'></i></a></td></tr>";
	}
	MyString += "</table>";
	
	document.getElementById("result").innerHTML = MyString;
	  document.getElementById("StartButton1").style.display = 'inline-block';
	  document.getElementById("StartButton2").style.display = 'inline-block';
	
	//setTimeout(function(){ FillButtons(); }, 3000);
	
}</script>

</center></body>
</html>

