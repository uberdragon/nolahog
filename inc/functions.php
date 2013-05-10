<?php
function detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}
function getAlbumLink($setID) {
  include_once('inc/php/phpFlickr.php');
  $apiKey = '0b5febc3341732163d9aff154d5bb26c';

  $f = new phpFlickr($apiKey);
  $days = rand(1,30);
  $f->enableCache('fs','/home/neworlea/public_html/cache',60*60*24*$days); // cache flickr API info calls for random number of days
                                                                           // This allows the caching to happen less noticeably
  $setInfo = $f->photosets_getInfo($setID);
  //create URL friendly link
  $albumTitle = getURI($setInfo['title']);
  echo "<a href='/PhotoAlbum/$setID/$albumTitle'>$setInfo[title]</a>";

}
function getURI($url) {
  $cleanURL =  str_replace(" ",'-',ucwords(preg_replace('/[^a-zA-Z0-9 ]/', "", str_replace(array('&','@'),array('and','at'),trim($url)))));
  while (strpos($cleanURL,'--') > 0) { $cleanURL = str_replace('--','-',$cleanURL); } // get rid of any excess -'s
  return $cleanURL;
}


?>