<?php

/** 
 * class.sendmail.php
 *
 * This program was created by Walker Systems Support's Web Services Group under the direction of Shane Kretzmann.  
 * 
 * This class encapsulates the PHP mail() function and implements CC, Bcc, attachments,  Priority headers etc
 *
 * @package Input-Output
 *
**/
/** 
 * SendMail - Securely email from php.
 * 
 * This PHP class encapsulates the PHP mail() function and implements CC, Bcc, attachments, priority headings etc.
 * 
 * Usage Example:
 * <code>
 * <?php
 * 	include "class.sendmail.php";
 * 	
 * 	$m= new SendMail; // create the mail
 * 	$m->From( "myMail@WalkerSystemsSupport.com" );
 * 	$m->To( "destination@somewhere.com" );
 * 	$m->Subject( "the subject of the mail" );	
 * 
 * 	$message= "Hello world!\nthis is a test of the Mail class\nplease ignore\nThanks.";
 * 	$m->Body( $message);	// set the body
 * 	$m->Cc( "someone@somewhere.com");
 * 	$m->Bcc( "someoneelse@somewhere.com");
 * 	$m->Priority(4) ;	// set the priority to Low 
 * 	$m->Attach( "/home/SendMail/picture.gif", "image/gif" ) ;	// attach a file of type image/gif
 * 	$m->Send();	// send the mail
 * 	echo "the mail below has been sent:<br><pre>", $m->Get(), "</pre>";
 * ?>
 * </code>
 *
 * @author Shane Kretzmann <SKretzmann@WalkerSystemsSupport.com>
 * @version 1.1:06200704 
 *
*/


class SendMail
{
	/**
	 * List of To addresses
	 * @var	array
	*/
	var $sendto = array();
	/**
	 * @access private Internal
	 * @var	array
	*/
	var $acc = array();
	/**
	* @access private Internal	
	* @var	array
	*/
	var $abcc = array();
	/**
	* Paths of attached files
	* @var array
	*/
	var $aattach = array();
	/**
	 * List of message headers
	 * @var array
	*/
	var $xheaders = array();
	/**
	 * Message priorities referential
	 * @var array
	*/
	var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
	/**
	* Character set of message
	* @var string
	*/
	var $charset = "us-ascii";
	var $ctencoding = "7bit";
	var $receipt = 0;
	var $emailType = "text/plain"; // set email form to plain text	

/**
* Initiate the SendMail Class
*/

function SendMail()
{
	$this->autoCheck( true );
	$this->boundary= "--" . md5( uniqid("myboundary") );
}


/**		
 * Activate or desactivate the email addresses validator
 * ex: autoCheck( true ) turn the validator on
 * by default autoCheck feature is on
 * 
 * @param boolean	$bool set to true to turn on the auto validation
*/
function autoCheck( $bool )
{
	if( $bool )
		$this->checkAddress = true;
	else
		$this->checkAddress = false;
}


/**
 * Define the subject line of the email
 * @param string $subject any monoline string
*/
function Subject( $subject )
{
	$this->xheaders['Subject'] = strtr( $subject, "\r\n" , "  " );
}


/**
 * Set the sender of the mail
 * @param string $from should be single email address
 *
 * Ex: $SendMail->From('"My Email" <myemail@myemail.com>');
 * 
 * Ex: $SendMail->From("myemail@myemail.com");
*/
 
function From( $from )
{

	if( ! is_string($from) ) {
		echo "Class Mail: error, From is not a string";
		exit;
	}
	$this->xheaders['From'] = $from;
}

/**
 * Set the Reply-to header 
 * @param string $address should be an email address
*/ 
function ReplyTo( $address )
{

	if( ! is_string($address) ) 
		return false;
	
	$this->xheaders["Reply-To"] = $address;
		
}


/**
 * Add a receipt to the mail ie.  a confirmation is returned to the "From" address (or "ReplyTo" if defined) 
 * when the receiver opens the message.
 * 
 * @warning this functionality is *not* a standard, thus only some mail clients are compliants.
*/
 
function Receipt()
{
	$this->receipt = 1;
}


/**
 * Set the mail recipient
 * @param string $to email address, accept both a single address or an array of addresses
*/
function To( $to )
{

	// TODO : test validité sur to
	if( is_array( $to ) )
		$this->sendto= $to;
	else 
		$this->sendto[] = $to;

	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->sendto );

}


/**		Cc()
 *		Set the CC headers ( carbon copy )
 *		@param mixed $cc email address(es), accept both array and string
 */

function Cc( $cc )
{
	if( is_array($cc) )
		$this->acc= $cc;
	else 
		$this->acc[]= $cc;
		
	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->acc );
	
}



/**		Bcc()
 *		Set the Bcc headers ( blank carbon copy ). 
 *		@param mixed $bcc email address(es), accept both array and string
 */

function Bcc( $bcc )
{
	if( is_array($bcc) ) {
		$this->abcc = $bcc;
	} else {
		$this->abcc[]= $bcc;
	}

	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->abcc );
}


/**		Body( text [, charset] )
 *		Set the body (message) of the mail. 
 *		Define the charset if the message contains extended characters (accents)
 *		defaults to us-ascii
 *		@param string $body ie: "Hello World!"
 * 		@param string $charset ie: "iso-8859-1" 
 */
function Body( $body, $charset="" )
{
	$this->body = $body;
	
	if( $charset != "" ) {
		$this->charset = strtolower($charset);
		if( $this->charset != "us-ascii" )
			$this->ctencoding = "8bit";
	}
}


/**		Organization( $org )
 *		set the Organization header
 */
 
function Organization( $org )
{
	if( trim( $org != "" )  )
		$this->xheaders['Organization'] = $org;
}


/**		Priority( $priority )
 *		set the mail priority 
 *		$priority : integer taken between 1 (highest) and 5 ( lowest )
 *		ex: $mail->Priority(1) ; => Highest
 */
 
function Priority( $priority )
{
	if( ! intval( $priority ) )
		return false;
		
	if( ! isset( $this->priorities[$priority-1]) )
		return false;

	$this->xheaders["X-Priority"] = $this->priorities[$priority-1];
	
	return true;
	
}


/**	
 * Attach a file to the mail
 * 
 * @param string $filename : path of the file to attach
 * @param string $filetype : MIME-type of the file. default to 'application/x-unknown-content-type'
 * @param string $disposition : instruct the Mailclient to display the file if possible ("inline") or always as a link ("attachment") possible values are "inline", "attachment"
 */

function Attach( $filename, $filetype = "", $disposition = "inline" )
{
	// TODO : si filetype="", alors chercher dans un tablo de MT connus / extension du fichier
	if( $filetype == "" )
		$filetype = "application/x-unknown-content-type";
		
	$this->aattach[] = $filename;
	$this->actype[] = $filetype;
	$this->adispo[] = $disposition;
}

/**
 *
 * Build the email message
 * 
 * @access protected
*/
function BuildMail()
{

	// build the headers
	$this->headers = "";
//	$this->xheaders['To'] = implode( ", ", $this->sendto );
	
	if( count($this->acc) > 0 )
		$this->xheaders['CC'] = implode( ", ", $this->acc );
	
	if( count($this->abcc) > 0 ) 
		$this->xheaders['BCC'] = implode( ", ", $this->abcc );
	

	if( $this->receipt ) {
		if( isset($this->xheaders["Reply-To"] ) )
			$this->xheaders["Disposition-Notification-To"] = $this->xheaders["Reply-To"];
		else 
			$this->xheaders["Disposition-Notification-To"] = $this->xheaders['From'];
	}
	
	if( $this->charset != "" ) {
		$this->xheaders["Mime-Version"] = "1.0";
		$this->xheaders["Content-Type"] = $this->emailType."; charset=$this->charset";
		$this->xheaders["Content-Transfer-Encoding"] = $this->ctencoding;
	}

	$this->xheaders["X-Mailer"] = "Php/myMailv1.3";
	
	// include attached files
	if( count( $this->aattach ) > 0 ) {
		$this->_build_attachement();
	} else {
		$this->fullBody = $this->body;
	}

	reset($this->xheaders);
	while( list( $hdr,$value ) = each( $this->xheaders )  ) {
		if( $hdr != "Subject" )
			$this->headers .= "$hdr: $value\n";
	}
	

}

/**		
 * 	format and send the mail
 * 	@access public
	
*/ 
function Send()
{
	$this->BuildMail();
	
	$this->strTo = implode( ", ", $this->sendto );
	
	// envoie du mail
	$res = @mail( $this->strTo, $this->xheaders['Subject'], $this->fullBody, $this->headers );

}



/**
 *		return the whole e-mail , headers + message
 *		can be used for displaying the message in plain text or logging it
 */

function Get()
{
	$this->BuildMail();
  $this->strTo = implode( ", ", $this->sendto );
	$mail = "To: " . $this->strTo . "\n";
	$mail .= $this->headers . "\n";
	$mail .= $this->fullBody;
	return $mail;
}
/**
 * Used to set email formatting to allow html
 */
function setHTML() {
	$this->emailType = "text/html";
}

/**
 * 	check an email address validity
 *	@access private
 *	@param string $address : email address to check
 *	@return true if email adress is ok
 */
 function _validEmail($string){
	$pattern_email = '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$';
	$emails = explode(',',$string);
	if (isset($emails)) { // Anything to evaluate?
		foreach ($emails as $key => $addr)
			if (!eregi($pattern_email,trim($addr))) { return FALSE; } 
	} 
	// If we haven't rejected by now it must be good
	return TRUE;
}
/**
 *	check validity of email addresses 
 *	@param	array $aad - 
 *	@return if unvalid, output an error message and exit, this may -should- be customized
 */

function CheckAdresses( $aad )
{
	for($i=0;$i< count( $aad); $i++ ) {
		if( ! $this->_validEmail( $aad[$i]) ) {
			echo "Class SendMail, method CheckAdresses : invalid address $aad[$i]";	
			exit;
		}
	}
}


/**
 * check and encode attach file(s) . internal use only
 * @access private
*/

function _build_attachement()
{

	$this->xheaders["Content-Type"] = "multipart/mixed;\n boundary=\"$this->boundary\"";

	$this->fullBody = "This is a multi-part message in MIME format.\n--$this->boundary\n";
	$this->fullBody .= "Content-Type: ".$this->emailType."; charset=$this->charset\nContent-Transfer-Encoding: $this->ctencoding\n\n" . $this->body ."\n";
	
	$sep= chr(13) . chr(10);
	
	$ata= array();
	$k=0;
	
	// for each attached file, do...
	for( $i=0; $i < count( $this->aattach); $i++ ) {
		
		$filename = $this->aattach[$i];
		$basename = basename($filename);
		$ctype = $this->actype[$i];	// content-type
		$disposition = $this->adispo[$i];
		
		if( ! file_exists( $filename) ) {
			echo "Class SendMail, method attach : file $filename can't be found"; exit;
		}
		$subhdr= "--$this->boundary\nContent-type: $ctype;\n name=\"$basename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$basename\"\n";
		$ata[$k++] = $subhdr;
		// non encoded line length
		$linesz= filesize( $filename)+1;
		$fp= fopen( $filename, 'r' );
		$ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));
		fclose($fp);
	}
	$this->fullBody .= implode($sep, $ata);
}


} // class Mail


?>
