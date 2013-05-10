<?php
  session_start();


  
  $issue = $_GET['issue'];
  $year = substr($issue,0,4);
  $month = substr($issue,-2);  
  if (file_exists('____hogpenIssues/'.$year.'-'.$month.'.PDF')) {
    $pdfFile = '____hogpenIssues/'.$year.'-'.$month.'.PDF';
  }
  else if (file_exists('____hogpenIssues/'.$year.'-'.$month.'.pdf')) {
    $pdfFile = '____hogpenIssues/'.$year.'-'.$month.'.pdf';
  }
  else {
    die('<br><br><br><br><br><center>Invalid HOGPEN Issue Number!');
  }

if ($_SESSION['LoggedIn'] == md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_HOST'])) {
?>

<html><head><script language="JavaScript">
<!--
function calcHeight()
{
//find the height of the internal page
var the_height=
document.getElementById('pdf').contentWindow.
document.body.scrollHeight;

//change the height of the iframe
document.getElementById('pdf').height=
the_height;
}
//-->
</script>
<style type="text/css">
* {padding:0px;margin:0px;}
</style>
</head>
<body style="padding:0; margin:0;">
<iframe id="pdf" src="<?php echo $pdfFile; ?>" width="100%" style="height:96%;padding:1px; margin:1px;">
</iframe>
</body>
</html>
<?php } else {
  echo '<br><br><br><br><br><center><h1>You MUST be logged in to see our newsletter!';
} ?>