<?php session_start(); ?>
<?php 
require_once('inc/php/class.sendmail.php');
require_once('inc/php/class.ubercrypt.php');
require_once('inc/php/class.validate.php');
require_once('inc/securimage/securimage.php');

 if ($_GET['event'] == 'submit') { 
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

      $toWho = 'activities@neworleanshog.com';  // email address to email form to
      $Bcc = 'webmaster@neworleanshog.com';  // email address to email form to
      $nextURL = '?event=post'; // where to go after successful form submission
      $requiredFields = array('name','email','eventTitle','eventDescription','captcha_code');   
  
      $lock = new ubercrypt();  
      
      // establish the submission fingerprint 
      // Prevents Form submissions from unauthorized sources
      $siteID = md5($_SERVER['SERVER_NAME'].$_SERVER['REMOTE_ADDR']);
      $salt = rand(10,99);
      $sitePrint = $lock->encrypt($siteID,'',$salt).$salt;
      
      // Initialize all used variables not already declared
      $scriptVars = array('name','email','comments','errors','fingerprint');
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
             	$m->From( "NOLA HOG Event Submission <nolahog@gmail.com>" );
              $m->setHTML();
            	$m->To( $toWho );
            	$m->Bcc($Bcc);
            	$m->Subject( "An Event has been submited to New Orleans HOG" );	
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
    <title>Louisiana Biker Events, Rides and New Orleans HOG Chapter Meetings and Events Calendar</title>
    <meta property="og:title" content="Louisiana Biker Events, Rides and New Orleans HOG Chapter Meetings and Events Calendar" />
    <meta http-equiv="Content-Type"	content="text/html; charset=iso-8859-1" /> 
    <meta name="keywords" content="biker events,events,New Orleans,Harley Owners Group,HOG,calendar,Harley-Davidson" /> 
    <meta name="description" content="Find ALL the biker events in the New Orleans, Louisiana area on the New Orleans HOG Chapter Calendar!" /> 
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
    <p>Please complete the form below to submit your event details or email the <script type="text/javascript">
<!-- Hide From Non-Java Browsers

	/********************************************************************
	* safeEmail.php - Protect mailto links from harvester bots
	* Developed by Web Services Group at www.WalkerSystemsSupport.com
	* Lead Software Engineer: Shane Kretzmann 
	* *******************************************************************/

	var emailarray5697= new Array(97,99,116,105,118,105,116,105,101,115,64,110,101,119,111,114,108,101,97,110,115,104,111,103,46,99,111,109)
	var safeEmail5697=''
	for (i=0;i<emailarray5697.length;i++)
	   safeEmail5697+=String.fromCharCode(emailarray5697[i])
	document.write('<a href="mailto:'+safeEmail5697+'?subject=Event Submission to NOLA HOG">Activities Officer</a>')

-->
</script> directly.</p>

    <?php if (!empty($errors)) { echo '<table border="0"><tr><td width="300">'.$errors.'</td></tr></table>'; } ?>
    <p><form action="?event=submit" method="post">
      <input type="hidden" name="fingerprint" value="<?php echo $sitePrint; ?>">
      <table border ="0" width="100%">
      <tr><td width="30%">What is your Name?</td><td><input type="text" name="name" value="<?php echo $name; ?>" ></td></tr> 
      <tr><td>What is your Email Address?</td><td><input type="text" name="email" value="<?php echo $email; ?>"></td></tr> 
      <tr><td><strong>Event Title</strong></td><td><input type="text" name="eventTitle" value="<?php echo $eventTitle; ?>"></td></tr> 
      <tr><td><strong>Event Website</strong></td><td><input type="text" name="eventWebsite" value="<?php echo $eventWebsite; ?>"></td></tr> 
      <tr><td><strong>Event Details</strong><br />Please include start & end times, contact information etc!</td><td><textarea style="height:100px;width:60%;" name="eventDescription"><?php echo $eventDescription; ?></textarea></td></tr>
      <tr><td>Enter the captcha code shown.  If you are unable to determine the letters, click the speaker icon to have them read to you!</td><td><!-- Start Captcha -->
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
<!-- End Captcha --></td></tr>
      <tr><td></td><td><input type="submit" name="submit" value="Submit" ></td></tr> 
    </table>      
    </form></p>
  <?php  
  } else if ($_GET['event'] == 'post') { 
   
  ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"> 
  <head>
    <title>Louisiana Biker Events, Rides and New Orleans HOG Chapter Meetings and Events Calendar</title>
    <meta property="og:title" content="Louisiana Biker Events, Rides and New Orleans HOG Chapter Meetings and Events Calendar" />
    <meta http-equiv="Content-Type"	content="text/html; charset=iso-8859-1" /> 
    <meta name="keywords" content="biker events,events,New Orleans,Harley Owners Group,HOG,calendar,Harley-Davidson" /> 
    <meta name="description" content="Find ALL the biker events in the New Orleans, Louisiana area on the New Orleans HOG Chapter Calendar!" /> 
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
          <div id="content">    <p><h2>Thank you!</h2><br />We sincerely appreciate you informing us of this event and we will review it for inclusion in our online calendar as soon as possible. <br /><br /> Keep your knees in the bress and ride safe!<br /><br />Barbara deMonsabert<br />NOLA HOG Activities Director</p>
  <?php  
  } else { 
  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"> 
  <head>
    <title>Louisiana Biker Events, Rides and New Orleans HOG Chapter Meetings and Events Calendar</title>
    <meta property="og:title" content="Louisiana Biker Events, Rides and New Orleans HOG Chapter Meetings and Events Calendar" />
    <meta http-equiv="Content-Type"	content="text/html; charset=iso-8859-1" /> 
    <meta name="keywords" content="biker events,events,New Orleans,Harley Owners Group,HOG,calendar,Harley-Davidson" /> 
    <meta name="description" content="Find ALL the biker events in the New Orleans, Louisiana area on the New Orleans HOG Chapter Calendar!" /> 
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
              <p style="margin-bottom: 0;"><hr width="95%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please 
                <a href="?event=submit">submit any upcoming events to our Activities Director</a> or drop them off at <a class="outbound" title="Get Directions to New Orleans Harley-Davidson" href="http://maps.google.com/?daddr=New Orleans Harley-Davidson, Metairie, LA">Harley-Davidson 
                of New Orleans</a>.<hr width="95%"></p> 
 
        <div align="center"><font face="Arial, Helvetica, sans-serif" size="2"><a class="outbound" title="National Motorcycle Events" href="http://www.motorcycleevents.com/calendar.php" target="_blank"><br> 
                National Motorcycle Events</a> | <a class="outbound" title="National H.O.G. Events" href="http://www.harley-davidson.com/wcm/Content/Pages/HOG/event_calendar.jsp?locale=en_US&bmLocale=en_US" target="_blank">National 
                H.O.G. Events</a> <!--| <a href="http://www.supercalendar.com/view.php?a=568" target="_blank">Motorcycle 
                Events in LA</a>--></font> <br> 
                <font face="Arial, Helvetica, sans-serif" color="red"><b><font size="-2">Dates 
                and events subject to change. Refer to the <a href="newsletter.php">newsletter</a> 
                for the latest info.<br> 
                <br> 
        </font> </b></font></div> 
           
            <iframe src="https://www.google.com/calendar/embed?title=New%20Orleans%20HOG%20-%20Area%20Rides%20and%20Events%20Calendar&amp;height=600&amp;wkst=1&amp;bgcolor=%23FFFFFF&amp;src=fd55gths4sdb11ocpqve3ur004%40group.calendar.google.com&amp;color=%23754916&amp;src=nolahog%40gmail.com&amp;color=%2388880E&amp;src=hiooscq7ll9q5ki52bjvmqa4r0%40group.calendar.google.com&amp;color=%235B123B&amp;ctz=America%2FChicago" style=" border-width:0 " width="740" height="500" frameborder="0" scrolling="no"></iframe> 
            <div align="center"> 
              <p><font face="Arial, Helvetica, sans-serif"><b><font size="-2"> *Chapter 
                activities are conducted primarily for the benefit of H.O.G. chapter 
                members. There are three categories of activities, all activities are 
                identified as follows: <br> 
                <br/>
                <font color="red">Closed events</font> are those chapter events 
                which are open to chapter members and one guest per member. <br> 
                <font color="#FF6600">Member events</font> are events that are open 
                only to H.O.G. Chapter members <br> 
                <font color="#5B123B">Public events</font> are those events which 
                are open to chapter members, national H.O.G. members and usually the general public.<br /> 
                <br />If you are interested in becoming a New Orleans H.O.G. chapter member, click <a href="chapter-info.php">here</a>.</font></b></font></p> 
            </div> 
<?php include('inc/fb_like.php'); ?>
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