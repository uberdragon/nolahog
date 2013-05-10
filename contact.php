<?php session_start(); ?>
<?php 
include_once('inc/functions.php');
require_once('inc/php/class.sendmail.php');
require_once('inc/php/class.ubercrypt.php');
require_once('inc/php/class.validate.php');
require_once('inc/securimage/securimage.php');

// Initiate the captcha image
$img = new securimage();

//Change some settings
$img->image_width = 250;
$img->image_height = 40;
$img->perturbation = 0.85;
$img->image_bg_color = new Securimage_Color("#FF0000");
$img->multi_text_color = array(new Securimage_Color("#FF0000"),
                               new Securimage_Color("#FF8040"),
                               new Securimage_Color("#3333cc"),
                               new Securimage_Color("#6666ff"),
                               new Securimage_Color("#99cccc")
                               );
//$img->use_multi_text = true;
$img->text_angle_minimum = -5;
$img->text_angle_maximum = 5;
$img->use_transparent_text = true;
$img->text_transparency_percentage = 100; // 100 = completely transparent
$img->num_lines = 0;
$img->line_color = new Securimage_Color("#eaeaea");
$img->image_signature = 'neworleanshog.com';
$img->signature_color = new Securimage_Color(rand(0, 64), rand(64, 128), rand(128, 255));
if ($img->check($_POST['captcha_code']) == false) {
  $badCaptcha = true;
}


//      $toWho = 'info@neworleanshog.com';  // email address to email form to
//      $toWho = "shanekretzmann@gmail.com";
      $toBCC = 'shane@kretzmann.net';  // email address to email form to
      $nextURL = '?event=post'; // where to go after successful form submission
      $requiredFields = array('FirstName','LastName','EMAIL','comments','captcha_code');   
  
      $lock = new ubercrypt();  
      
      // establish the submission fingerprint 
      // Prevents Form submissions from unauthorized sources
      $siteID = md5($_SERVER['SERVER_NAME'].$_SERVER['REMOTE_ADDR']);
      $salt = rand(10,99);
      $sitePrint = $lock->encrypt($siteID,'',$salt).$salt;
      
      // Initialize all used variables not already declared
      $scriptVars = array('FirstName','LastName','comments','EMAIL','errors','fingerprint');
      foreach ($scriptVars as $key => $pair) {
        $$pair = '';
      }
      
      // Initiate Validation Class
      $valid = new validate();
      $valid->setRequired($requiredFields);
      
      /* 
       * Sanatize all the inputs against XSS
       * Creates global vars on array keynames
       * Creates the Email Formated Data
      */  

      $emailInputs = $valid->cleanInput($_POST,'HTML');     

      /* Form was submitted, process */
      if (!empty($Submit) || !empty($Submit_x) || !empty($submit) || !empty($submit_x)) {
        // Match the submission fingerprint or error out
        if (empty($fingerprint)) {
          die('Submission Fingerprint Empty... Exiting...');
        } else {
          $psalt = substr($fingerprint,-2);
          $key = substr($fingerprint,0,-2);
          if ($lock->decrypt($key,'',$psalt) != $siteID) {
            die('Invalid Submission Fingerprint...  Exiting...');
          }
        }             
      
        
        /* Check to see required fields are completed and formatted correctly */
        $errors = $valid->checkRequired($_POST);
      
        if (empty($errors)) {
              // No Errors time to send an email
             	$m= new SendMail; // start the mail
             	$m->From( "HOG Website <info@NewOrleansHOG.com>" );
              $m->setHTML();
            	$m->To( $toWho );
//            	$m->Cc( $toCC );
            	$m->Bcc( $toBCC );
              $m->Subject( "The New Orleans HOG Contact Form has been submitted" );	
            	$m->Body($emailInputs);	// set the body
            	$m->Priority(4) ;	// set the priority to Low 
            	$m->Send();	// send the mail
      
              header('Location: '.$nextURL);
        } else { /* continue to show page passing along the $errors */ }

      } // end of submit check  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"> 
  <head>
    <title>New Orleans Harley Owners Group (H.O.G.)</title>
    <meta http-equiv="Content-Type"	content="text/html; charset=iso-8859-1" /> 
    <meta name="keywords" content="New Orleans,Harley Owners Group,HOG,Harley-Davidson" /> 
    <meta name="description" content="The home of New Orleans Harley Owners Group (HOG) Chapter #2023." /> 
    <?php include('inc/universalHead.php'); ?> 
  </head>
    <body><div class="loader"></div> 
    <div id="container">

      <div id="header">
      </div><!-- end header-->
      <div id="middleleft">
        <div id="middleright">
          <!-- begin left menu-->
            <?php include('inc/menu.php'); ?>
          <!-- end menu-->
          <div id="content">
<?php if ($_GET['event'] == 'post') {
?>
  <center><br><br><br><div width="650">Thank you for contacting New Orleans HOG, we will respond to your request A.S.A.P.</div> </center>
<?php
}
else {
?>
                    <h1>Contact New Orleans HOG Chapter #2023</h1>
                    <center><p>You can email us at 
                <script type="text/javascript">
<!-- Hide From Non-Java Browsers

	/********************************************************************
	* safeEmail.php - Protect mailto links from harvester bots
	* Developed by Web Services Group at www.WalkerSystemsSupport.com
	* Lead Software Engineer: Shane Kretzmann 
	* *******************************************************************/

	var emailarray2321= new Array(105,110,102,111,64,110,101,119,111,114,108,101,97,110,115,104,111,103,46,99,111,109)
	var safeEmail2321=''
	for (i=0;i<emailarray2321.length;i++)
	   safeEmail2321+=String.fromCharCode(emailarray2321[i])
	document.write('<a href="mailto:'+safeEmail2321+'">'+safeEmail2321+'</a>')

-->
</script>, contact one of the 
                <a href="chapter-officers.php">chapter officers</a> directly or call the <a href="sponsoring-dealer.php">dealership</a>.</p> </center>
                    <?php if (!empty($errors)) { echo '<table border="0"><tr><td width="80%">'.$errors.'</td></tr></table>'; } ?>
                    <form method="POST" action="contact.php">
                      <input type="hidden" name="fingerprint" value="<?php echo $sitePrint; ?>">
                      <table width="97%" cellspacing="1" cellpadding="0" border="0">
                        <tr> 
                          <td width="40%"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">First 
                            Name:</font></td>
                          <td width="60%"> <font face="Verdana, Arial, Helvetica, sans-serif" size="1"> 
                            <input name="FirstName" size="25" maxlength="50" value="<?php echo $FirstName; ?>">
                            </font></td>
                        </tr>
                        <tr> 
                          <td width="40%"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Last 
                            Name:</font></td>
                          <td width="60%"> <font face="Verdana, Arial, Helvetica, sans-serif" size="1"> 
                            <input name="LastName" size="25" maxlength="50" value="<?php echo $LastName; ?>">
                            </font></td>
                        </tr>
                        <tr> 
                          <td width="40%"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Email 
                            Address:</font></td>
                          <td width="60%"> <font face="Verdana, Arial, Helvetica, sans-serif" size="1"> 
                            <input name="EMAIL" size="25" maxlength="50" type="text" value="<?php echo $EMAIL; ?>">
                            </font></td>
                        </tr>
                        <tr> 
                          <td width="40%"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">Comments:</font></td>
                          <td width="60%"> <font face="Verdana, Arial, Helvetica, sans-serif" size="1"> 
                            <textarea name="comments" style="width:300px; height:100px;"><?php echo $comments; ?></textarea>
                            </font></td>
                        </tr>
                        <tr><td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" style="margin:15px"><br><br>The internet is a dangerous place and much like the open road, it's the "other guys" that can be the biggest problem.  Please enter the captcha code you see on your right into the box below it to show us you are a human harley lovin' biker :)</td>
                        <td>
 <p class="formSmall">
     <br style="clear:both" /> 

<!--<label for="captcha"></label>-->
<!-- Start Captcha -->
<table border="0" cellpadding="0"><tr><td>
<div style="width: 430px; float: left; height: 40px">
      <img id="siimage" align="left" width="250" height="40" style="padding-right: 5px; border: 1px solid;" src="inc/securimage/securimage_show.php?sid=<?php echo md5(time()) ?>" />

        <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="19" height="19" id="SecurImage_as3" align="middle">
			    <param name="allowScriptAccess" value="sameDomain" />
			    <param name="allowFullScreen" value="false" />
			    <param name="movie" value="inc/securimage/securimage_play.swf?audio=inc/securimage/securimage_play.php&bgColor1=#777&bgColor2=#fff&iconColor=#000&roundedCorner=5" />
			    <param name="quality" value="high" />
			
			    <param name="bgcolor" value="#ffffff" />
			    <embed src="inc/securimage/securimage_play.swf?audio=inc/securimage/securimage_play.php&bgColor1=#777&bgColor2=#fff&iconColor=#000&roundedCorner=5" quality="high" bgcolor="#ffffff" width="19" height="19" name="SecurImage_as3" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
			  </object>

        <br />
        
        <!-- pass a session id to the query string of the script to prevent ie caching -->
        <a tabindex="-1" style="border-style: none" href="#" title="Refresh Image" onclick="document.getElementById('siimage').src = 'inc/securimage/securimage_show.php?sid=' + Math.random(); return false"><img border="0" src="inc/securimage/images/refresh.gif" alt="Reload Image" border="0" onclick="this.blur()" align="bottom" /></a>
</div>
</td></tr><tr><td>
<input style="width:255px;" type="text" name="captcha_code" size="20" maxlength="6" />
</td></tr></table>
<!-- End Captcha -->
</p>                         </td></tr>
                          
                      </table>
                      <p align="center"> <br>
                        <font face="Verdana, Arial, Helvetica, sans-serif" size="-1"> 
                        <input type="submit" name="submit" value="Submit" >
                        </font> 
                      </p>
                      </form>
<?php } ?>
  
          </div><!-- end content-->
        </div><!-- end middleright-->
      </div><!-- end middleleft-->      

      <!-- begin footer-->
        <?php include('inc/footer.php'); ?>
      <!-- end footer-->
    </div><!-- end container-->
  </body>
</html>