<?php session_start(); include_once('inc/functions.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"> 
  <head>
    <title>The HogPen Newsletter from New Orleans Harley Owners Group (H.O.G.)</title>
    <meta property="og:title" content="The HogPen Newsletter from New Orleans Harley Owners Group #2023 (H.O.G.)" />
    <meta http-equiv="Content-Type"	content="text/html; charset=iso-8859-1" /> 
    <meta name="keywords" content="New Orleans,Harley Owners Group,HOG,Harley-Davidson" /> 
    <meta name="description" content="The HogPen Newsletter is for New Orleans HOG members only.  If you are a current member, ask any of our officers for a password to gain access." /> 
    <?php include('inc/universalHead.php'); ?> 
    <script language=JavaScript src="/inc/js/hogpen.js"></script>
    <script type="text/javascript">
      function toggle(objname){
        if(document.getElementById(objname).style.display == "none"){
          document.getElementById(objname).style.display = "block";
        }else{
          // div is not hidden, so slide up
          document.getElementById(objname).style.display = "none";        }
      }    

    </script>
  </head>

    <body><div class="loader"></div> 
          <div id="preLoad" style="display:none">
             <img src="images/HOGNewsletter.jpg" alt="New Orleans HOG NewsLetter" />
             <img src="images/Newsletter.jpg" alt="New Orleans HOG NewsLetter" />
          </div> <!-- this allows facebook and other social sites to have an image to use in the post -->
    <div id="container">
      <div id="header">
      </div><!-- end header-->
      <div id="middleleft">
        <div id="middleright">
          <!-- begin left menu-->
            <?php include('inc/menu.php'); ?>
          <!-- end menu-->
          <div id="content">
            <h1>New Orleans HOG Newsletters: The HogPen</h1>

<?php /************************************************************************/ 
  if ($_POST['verify']) {  
    if ($_POST['verify'] == 'hogpen') {
      $_SESSION['LoggedIn'] = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_HOST']);
    } else {
      echo '<center><font color=red><h3>Invalid Phrase</h3></font></center>';
    }
  }
  if (!$_SESSION['LoggedIn']) {
    echo '<center><div style="text-align:center;width:650px;">You must be logged in to see our newsletters. If you are a New Orleans H.O.G. member and have not been issued a password, please <a href="mailto:hognewsletter@yahoo.com?cc=hogpen@neworleanshog.com&subject=HOGPEN Online Access Request">send an email</a> with your full name and H.O.G. number.<br>
          <br><center><table border="0"><tr><td>What is the secret phrase?
          <td> <form method="POST" action="">
          <input type="password" name="verify" size="15" />
          <input type="submit" name="submit" value="submit" />
          </form>
          </td></tr></table></center></div></center>';
  } elseif ($_SESSION['LoggedIn'] == md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_HOST'])) {
    echo '<center>You are currently logged in, enjoy the newsletters!</center>';
  }
/******************************************************************************/?>
            <h2 onmousedown="toggle(2012)" style="cursor:pointer;">2012</h2>
            <ul id="2012" style="">
              <?php echo showNewsletters('2012'); ?>
            </ul>
            <h2 onmousedown="toggle(2011)" style="cursor:pointer;">2011</h2>
            <ul id="2011" style="">
              <?php echo showNewsletters('2011'); ?>
            </ul>
            <h2 onmousedown="toggle(2010)" style="cursor:pointer;">2010</h2>
            <ul id="2010" style="display:none;">
              <?php echo showNewsletters('2010'); ?>
            </ul>
            <h2 onmousedown="toggle(2009)" style="cursor:pointer;">2009</h2>
            <ul id="2009" style="display:none;">
              <?php echo showNewsletters('2009'); ?>
            </ul>
            <h2 onmousedown="toggle(2008)" style="cursor:pointer;">2008</h2>
            <ul id="2008" style="display:none;">
              <?php echo showNewsletters('2008'); ?>
            </ul>
            <h2 onmousedown="toggle(2007)" style="cursor:pointer;">2007</h2>
            <ul id="2007" style="display:none;">
              <?php echo showNewsletters('2007'); ?>
            </ul>
            <h2 onmousedown="toggle(2006)" style="cursor:pointer;">2006</h2>
            <ul id="2006" style="display:none;">
              <?php echo showNewsletters('2006'); ?>
            </ul>
            <h2 onmousedown="toggle(2005)" style="cursor:pointer;">2005</h2>
            <ul id="2005" style="display:none;">
              <?php echo showNewsletters('2005'); ?>
            </ul>
            <h2 onmousedown="toggle(2004)" style="cursor:pointer;">2004</h2>
            <ul id="2004" style="display:none;">
              <?php echo showNewsletters('2004'); ?>
            </ul>
            <h2 onmousedown="toggle(2003)" style="cursor:pointer;">2003</h2>
            <ul id="2003" style="display:none;">
              <?php echo showNewsletters('2003'); ?>
            </ul>

<?php include('inc/fb_like.php'); ?>
  
          </div><!-- end content-->
        </div><!-- end middleright-->
      </div><!-- end middleleft-->      

      <!-- begin footer-->
        <?php include('inc/footer.php'); ?>
      <!-- end footer-->
    </div><!-- end container-->
  </body>
</html>

<?php
function showNewsLetters($year) {
  $month = 12;
  while ($month > 0) {
    if (strlen($month) == 1) { $m = '0'.$month; } else { $m = $month; }
    $month_name =  date( 'F', mktime(0, 0, 0, $month) );

    if (file_exists('____hogpenIssues/'.$year.'-'.$m.'.PDF') || file_exists('____hogpenIssues/'.$year.'-'.$m.'.pdf')) {
      if ($_SESSION['LoggedIn'] == md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_HOST'])){
        if (!detect_ie()) { $class = 'class="outbound"'; }
        else { $class = 'target="NEW"'; }
        $return .= '<li><a '.$class.' title="NOLA H.O.G. Newsletter '.$month_name.' '.$year.'" href="/hogpen.php?issue='.$year.$m.'">'.$month_name.' Issue</a></li>';
      } else {
        $return .= '<li><a onClick="alert(\'You must login to see this newsletter\');return false;" title="You must login to see this newsletter" href="">'.$month_name.' Issue</a></li>';
      
      }
    }  

    $month--;
  }  
  return $return;
}

?>