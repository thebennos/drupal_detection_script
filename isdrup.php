<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
</head>
<body>

<?php
/*
 **	This script tests whether multiple URLS are Drupal sites
 **	The script loads the URLs from an external CSV file and stores them into an array
 **	Then we go through the array and check them using http headers and /node to see if they are Drupal sites
 ** The results are printed in a CSV file called "results.csv" which appears in the same directory as the script
 ** If you want the results to appear on the screen, uncomment the echo statements which print them
 **
 **
 ** For the future - configure ssl so that https requests work
 **
 ** Script by Jon Cohen 4/30/12
*/
 
$target_path = basename($_FILES['uploadedURLs']['name']);

if(move_uploaded_file($_FILES['uploadedURLs']['tmp_name'], $target_path)) {
	echo "The file ". basename ($_FILES['uploadedURLs']['name']) . " has been uploaded";
} else {
	echo "There was an error uploading the file!";
}

// there is an automatic timeout if this takes too long
// we can set this number to 0 if we want the script to continue and override timeouts
// i.e. if we want to do a very large number of URLs as once

set_time_limit(0);

$input = fopen(basename($_FILES['uploadedURLs']['name']), "r");
$array = fgetcsv($input);
fclose($input);

// initialize prevValue so we can track the previous value of the array
$prevValue = "";

echo "<table border='1px'>";

$output = fopen("results.csv", "w");

$header = array("URL", "Is it Drupal?", "Detection Method");

fputcsv($output, $header);

// array index
?> 

<script type="text/javascript">
	var currentURL = 1;
</script>

<?php

echo "<br /> <br /> <strong> <span id='current'></span> out of " . count($array) . " URLs analyzed </strong> <br />";

foreach($array as &$value) {

	// if $value is equal to $prevValue it is a duplicate, so skip it
	if($prevValue == $value) {
		continue;
	}
	
	// give prevValue the current value so it will be the previous on next iteration
	$prevValue = $value;
	
	// get the http headers for the url at value
	$headers = get_headers($value);
	
	// set headers to false so the loop continues if it is not found
	$hasHeader = false;
	
	// go through the headers and look for the "Expires" header
	// if it is found, print yes and stop the loop
	foreach($headers as &$http) {
		if($http == "Expires: Sun, 19 Nov 1978 05:00:00 GMT") {
			$outputLine = array($value, "Yes", "Detected by the HTTP Headers");
			fputcsv($output, $outputLine);
			$hasHeader = true;
			break;
		}
	}
	
	// if the header is found, continue to the next URL
	if($hasHeader) {
		continue;
	}

	// if the checks don't work, output that the site is not Drupal
	
	$outputLine = array($value, "No");
	fputcsv($output, $outputLine);
	
	?> 
	
	<script type="text/javascript"> 
		currentURL++;
		$('#current').html(currentURL);
	</script>
	
	<?php

}

echo "</table>";

// make sure we print out all our content by using the flush function
// this executes after the 5 minutes is over
flush();
fclose($output);

echo "<h3>Check the results.csv file in this directory for the results of the script!</h3>";

?>

</body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> 32e45f2d77efc705f85996df48115a661155fbaf
