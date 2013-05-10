<?php
  if (substr($_SERVER['REQUEST_URI'],0,6) == '/index' || $_SERVER['REQUEST_URI'] == '/') { $home = 'id="selectedNav"'; }
  if (substr($_SERVER['REQUEST_URI'],0,9) == '/chapter-') { $chapter = 'id="selectedNav"'; }
  if (substr($_SERVER['REQUEST_URI'],0,9) == '/calendar') { $calendar = 'id="selectedNav"'; }
  if (substr($_SERVER['REQUEST_URI'],0,9) == '/photo-al' || substr($_SERVER['REQUEST_URI'],0,6) == '/Photo') { $album = 'id="selectedNav"'; }
  if (substr($_SERVER['REQUEST_URI'],0,9) == '/group-ri') { $ridingTips = 'id="selectedNav"'; }
  if (substr($_SERVER['REQUEST_URI'],0,9) == '/newslett') { $newsletter = 'id="selectedNav"'; }
  if (substr($_SERVER['REQUEST_URI'],0,4) == '/LOH') { $LOH = 'id="selectedNav"'; }
  if (substr($_SERVER['REQUEST_URI'],0,9) == '/sponsori') { $sponsor = 'id="selectedNav"'; }
  if (substr($_SERVER['REQUEST_URI'],0,9) == '/in-memor') { $memoriam = 'id="selectedNav"'; }
  if (substr($_SERVER['REQUEST_URI'],0,9) == '/contact.') { $contact = 'id="selectedNav"'; }
  if (substr($_SERVER['REQUEST_URI'],0,9) == '/Louisian') { $LAlaw = 'id="selectedNav"'; }
$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // full URL shown in browser address bar 
?>
          <div id="preLoad" style="display:none">
             <img src="images/NOLA Hog Mascot.gif" alt="New Orleans HOG Mascot" />
             <img src="images/design/pins/facebook-pin-over.png" />
             <img src="images/design/pins/hog-pin-over.png" />
             <img src="images/design/pins/la-pin-over.png" />
             <img src="images/design/pins/motorcycle-pin-over.png" />
             <img src="images/design/pins/twitterhog-pin-over.png" />
          </div> <!-- this allows facebook and other social sites to have an image to use in the post -->
          <div id="menu">
            <div class="cssnav" <?php echo $home; ?>><a href="/"><span>Home</span></a></div> 
            <div class="cssnav" <?php echo $chapter; ?>><a href="/chapter-info.php"><span>Chapter Info</span></a></div> 
            <div class="cssnav" <?php echo $calendar; ?>><a href="/calendar.php"><span>Event Calendar</span></a></div> 
            <div class="cssnav" <?php echo $album; ?>><a href="/photo-album.php"><span>Photo Albums</span></a></div> 
            <div class="cssnav" <?php echo $newsletter; ?>><a href="/newsletter.php"><span>Newsletters</span></a></div> 
            <div class="cssnav" <?php echo $contact; ?>><a href="/contact.php"><span>Contact Us</span></a></div> 
            <div class="cssnav" <?php echo $LOH; ?>><a href="/LOH.php"><span>Ladies of Harley</span></a></div> 
            <div class="cssnav" <?php echo $ridingTips; ?>><a href="/group-riding-tips.php"><span>Group Riding Tips</span></a></div> 
            <div class="cssnav" <?php echo $sponsor; ?>><a href="/sponsoring-dealer.php"><span>Sponsoring Dealer</span></a></div> 
            <div class="cssnav"><a class="outbound" title="National H.O.G. Website" href="http://www.hog.com" target="_blank"><span>National H.O.G.</span></a></div> 
            <div class="cssnav" <?php echo $memoriam; ?>><a href="/in-memoriam.php"><span>In Memoriam</span></a></div> 
            <div class="cssnav" <?php echo $LAlaw; ?>><a href="/Louisiana-Motorcycle-Laws.php"><span>LA Motorcycle Law</span></a></div>
<br /><br />
<center>
  <div style="width:180px;vertical-align:text-top;">
    <table border="0"><tr><td valign="top">
    <script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like send="true" href="<?=$url?>" layout="box_count" show_faces="false" width="100" font=""></fb:like>
    </td><td valign="top">
    <script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script><div style="height:10px"></div>
<!-- Place this tag in your head or just before your close body tag -->
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>

<!-- Place this tag where you want the +1 button to render -->
<g:plusone></g:plusone>
    </td></tr></table>

  </div>
</center>
</div>

