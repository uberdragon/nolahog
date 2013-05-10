<?php session_start(); ?>
<html> 
<head> 
<title>Create GeoCode DB From Google Maps API</title> 
<script language='JavaScript'> 
  var time = <?php echo rand(4,8); ?>; //How long (in seconds) to countdown
  var page = "/googGEOauto.php"; //The page to redirect to
  function countDown(){
    time--;
    gett("container").innerHTML = time;
    if(time == 1){
      window.location = page;
    }
    if(time < 0) { time = 0; }
  }
  function gett(id){
    if(document.getElementById) return document.getElementById(id);
    if(document.all) return document.all.id;
    if(document.layers) return document.layers.id;
    if(window.opera) return window.opera.id;
  }
  function init(){
    if(gett('container')){
      setInterval(countDown, 1000);
      gett("container").innerHTML = time;
    }
    else{
      setTimeout(init, 50);
    }
  }
  document.onload = init();
</SCRIPT> 
</HEAD> 
<BODY> 
<?php
// Make a MySQL Connection
mysql_connect("72.52.221.50", "kretz_map", "l3tm31n") or die(mysql_error());
mysql_select_db("kretz_map") or die(mysql_error());


$dayMax = 2525; // Daily max requests, stop the script before the Max is reached (free api allows 2500 a day)
$limit = 10; // Google GEOcode API allows 10 requests per second



$sql = "SELECT ZIPCode as zip from zipcode WHERE pulled = 0 ORDER BY ZIPCode ASC LIMIT $limit";
$res = mysql_query($sql);
if (!$res) { die(mysql_error()); }
if (mysql_num_rows($res) < 1) { die('<h1>All Finished the database is complete!</h1>'); }

while ($row = mysql_fetch_assoc($res)) {
//print_r($row);echo ' #'.$_SESSION['lookupNum'];echo '<hr>';
  // First we need to make sure we haven't exceeded the max daily requests...
  if ($_SESSION['lookupNum'] >= $dayMax) {
    die('<h1>Daily max requests exceeded for today ('.$dayMax.'), continue the process tomorrow!</h1>
        Googles FREE GEO Code API allows a maximum of 2500 requests per day.
        <br><br>Please note you will need to clear your session cache tomorrow to continue!
    ');
  }
  $_SESSION['lookupNum']++;


/**/
  unset($zip,$lat,$long,$county,$city,$fullState,$state,$fullCountry,$country);
  $data = 'http://maps.google.com/maps/api/geocode/json?address='.$row['zip'].'&sensor=false';  
  $geocode=file_get_contents($data);
  $output= json_decode($geocode);

  if ($output->status == "OK") {
    $count = count($output->results[0]->address_components);
    $x = 0;
    while ($x <= $count) {
      switch($output->results[0]->address_components[$x]->types[0]) {
        case "country":
          $fullCountry = $output->results[0]->address_components[$x]->long_name;
          $country = $output->results[0]->address_components[$x]->short_name;
          break;
        case "administrative_area_level_1":
          if (strlen($output->results[0]->address_components[$x]->long_name) > 2) {
            $fullState = $output->results[0]->address_components[$x]->long_name;
            $state = $output->results[0]->address_components[$x]->short_name;      
          }
          break;
        case "administrative_area_level_2":
          $county = str_replace("'","&#39;",mb_convert_encoding(ucwords(strtolower($output->results[0]->address_components[$x]->long_name)),"iso-8859-1","utf-8"));
          break;
        case "locality":
          $city = str_replace("'","&#39;",mb_convert_encoding(ucwords(strtolower($output->results[0]->address_components[$x]->long_name)),"iso-8859-1","utf-8"));
          break;
        case "sublocality":
          $city = str_replace("'","&#39;",mb_convert_encoding(ucwords(strtolower($output->results[0]->address_components[$x]->long_name)),"iso-8859-1","utf-8"));
          break;
      }
      $x++;
    }
    $zip = $row['zip'];
    $lat = $output->results[0]->geometry->location->lat;
    $long = $output->results[0]->geometry->location->lng;
    if (empty($city)) { 
      $city = $county;
      unset($county);
    }
    if ($country != 'US') {
      unset($county,$fullState,$state);
    }

//    echo 'zip: '.$zip.'<br>';
//    echo 'lat: '.$lat.'<br>';
//    echo 'long: '.$long.'<br>';
//    echo 'city: '.$city.'<br>';
//    echo 'county: '.$county.'<br>';
//    echo 'state: '.$fullState.' ('.$state.')<br>';
//    echo 'country: '.$fullCountry.' ('.$country.')<br>';
//    die();

    $insert = "INSERT INTO googGEO VALUES ('$zip','$lat','$long','$county','$city','$fullState','$state','$fullCountry','$country')";
    echo '#'.$_SESSION['lookupNum'].' '.$insert.' <a href="http://maps.google.com/maps?q='.$lat.','.$long.'">map</a><hr>';
    if (!mysql_query($insert)) { die(mysql_error()); }    

    $pull = "UPDATE zipcode SET pulled=1 WHERE ZIPCode = '$zip'";
    if (!mysql_query($pull)) { die(mysql_error()); }    

  } else { echo '#'.$_SESSION['lookupNum'].' '.$row['zip'].' resulted in error: '.$output->status.'<hr>'; }

  if ($output->status == 'ZERO_RESULTS') {
    $pull = "UPDATE zipcode SET pulled=2 WHERE ZIPCode = '".$row['zip']."'";
    if (!mysql_query($pull)) { die(mysql_error()); }    
  
  }

  if ($output->status == 'OVER_QUERY_LIMIT') {
    $_SESSION['overLimit']++;
    if ($_SESSION['overLimit'] > 7) {
      die ('Program shut down after 7 Over the Limit Messages from Google<br> '.$_SESSION['lookupNum']);
    }
  }

/**/
}

echo '<h2>Processing <a href="/googGEOauto.php">another '.$limit.' zipcodes</a> in <span id="container"></span> seconds...</h2>';

?>

</body>
</html>