<?php
/**
 * class.ubercrypt.php
 * 
 * This program was created by Walker Systems Support's Web Services Group under the direction of Shane Kretzmann.  
 * 
 * This class is used to securely encrypt and decrypt sensitive information such as Credit Card Numbers.
 *
 * @package Security
 * @subpackage Encryption
 *
 * 
**/

/**
 * UberCrypt - Securely store and retrieve data
 * 
 * This PHP class will protect sensitive user information and finishes up with a base64 encoding to ensure compatibility with URL strings and database storage.
 * Average encryption bloat is 275% (For every character in unencrypted data there are 2.75 characters of encrypted base64 data)
 * 
 * Usage Example:
 *
 * <code>
 * require_once('class.ubercrypt.php');
 * 
 * $lock = new ubercrypt($key,$salt);  //Start of instance. $key and $salt are optional.  
 * 
 * $secret = 'Sensitive Information';  //Data that needs to be encrypted.
 * 
 * $protected = $lock->encrypt($secret,$key,$salt); // $key and $salt are optional.  
 * 
 * $unlocked = $lock->decrypt($protected,$key,$salt);  // $key and $salt are optional.  
 * 
 * </code>
 *
 * @author Shane Kretzmann <SKretzmann@WalkerSystemsSupport.com>
 * @version 1.5:05200730
 *
 * 
**/
class ubercrypt{
	/**
	* Used to Initialize UberCrypt
	* @param string $key (AlphaNumeric) [optional] 
	* @param int $salt (+/-Numeric) [optional]
	**/
	function ubercrypt($key=null,$salt=null){
  	########################### Edit the Key and Salt Internal Defaults Below #########################
    
  	$this->defaultKey = 'Main Street Community Foundation'; // (AlphaNumeric) Should be changed for each client
  	$this->defaultSalt = '45';  // (+/-Numeric) Should be changed for each client
  	  
  	###################################################################################################
  	$this->defaultSalt = substr($this->defaultSalt,0,14);
  	
    
    if($salt){$this->defaultSalt=substr($salt,0,14);}
    if($key){$this->defaultKey=$key;}
  	$this->key = $this->defaultKey;
    $this->salt=$this->defaultSalt;	
	}
	/** @access  private */
  function rinse($in,$val){
    $x = 0;
    $out = '';
    while($x<strlen($in)){
      $out.=chr((ord(substr($in,$x,1)))+$val);
      $x++;
    }
    return $out;
  }
  /** @access  private */
  function keyED($txt){
    $encrypt_key=md5($this->key);
    $ctr=0;
    $tmp="";
    for($i=0;$i<strlen($txt);$i++){
      if($ctr==strlen($encrypt_key)){$ctr=0;}
      $tmp.=substr($txt,$i,1)^substr($encrypt_key,$ctr,1);
      $ctr++;
    }return $tmp;
  }
  /** @access  private */
  function seeSalt(){
    return $this->salt;
  }
  /** @access  private */
  function seeKey(){return $this->key;}
  /** @access private */
  function setKey($key){$this->key=$key;}
  /** @access  private */
  function setSalt($salt){$this->salt=$salt;}
	/** This method securely encrypts data and returns a base64 string
	* @param string $txt The data you wish to secure.
	* @param string $key (AlphaNumeric) [optional] 
	* @param int $salt (+/-Numeric) [optional]
	**/
	function encrypt($txt,$key=null,$salt=null){
    if($salt){
      $this->salt=substr($salt,0,14);
    }else{
      $this->salt=$this->defaultSalt;
    }
    if($key){ $this->key=$key; }
    else{ $this->key=$this->defaultKey; }
    $txt=$this->rinse(strrev($txt),$this->salt);
    srand((double)microtime()*1000000);
    $encrypt_key = md5(rand(0,32000));
    $ctr=0;
    $tmp = "";
    for ($i=0;$i<strlen($txt);$i++){
      if($ctr==strlen($encrypt_key)){$ctr=0;}
      $tmp.= substr($encrypt_key,$ctr,1).(substr($txt,$i,1)^substr($encrypt_key,$ctr,1));
      $ctr++;
    }
    return base64_encode($this->keyED($tmp));
  }
	/** Method to decrypt data encrypted with this class.  $key and $salt must match encrypted data.
	* @param string $txt The data you wish to secure.
	* @param string $key [optional] 
	* @param int $salt [optional]
	**/
	function decrypt($txt,$key=null,$salt=	null){
    if($key){$this->key=$key;}
    else{$this->key=$this->defaultKey;}
    if($salt){
      $this->salt=substr($salt,0,14);}
    else{$this->salt=$this->defaultSalt;}
    $txt=$this->keyED(base64_decode($txt));
    $tmp="";
    for($i=0;$i<strlen($txt);$i++){
      $md5=substr($txt,$i,1);
      $i++;
      $tmp.=(substr($txt,$i,1)^$md5);
    }
    $tmp=$this->rinse(strrev($tmp),- $this->salt);
    return $tmp;
  }
}
?>