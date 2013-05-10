<?php $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // full URL shown in browser address bar 
$page = (stristr($url,'PhotoAlbum'))?'Photo Album':'page';
if ($page == 'page') {
  $page = (stristr($url,'memoriam'))?'memoriam':'page';
}

if ($page == 'memoriam') {
  $header = '<h2>Have parting words or want to comment?  Use the space below.</h2>';
}
else {
  $header = '<h2>Like this '.$page.'?  We love to see your comments!</h2>';  
}

echo $header;
?>

<link rel="stylesheet" type="text/css" href="http://neworleanshog.com/css/facebook.css" />
<center>
<table><tr><td>
      &nbsp;&nbsp;&nbsp;<fb:like href="<?=$url?>" show_faces="false" width="450" font=""></fb:like>
</td></tr><tr><td>
    <div id="comments">
      <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=209015612449414&amp;xfbml=1"></script>
      <fb:comments send="true" href="<?=$url?>" css="http://neworleanshog.com/css/facebook.css" simple="1" num_posts="5" width="650" send_notification_uid="1028040698" ></fb:comments>
    </div>
</td></tr></table>
</center>
