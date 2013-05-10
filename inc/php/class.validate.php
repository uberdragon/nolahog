<?php
/**
 * class.validate.php
 * 
 * This program was created by Walker Systems Support's Web Services Group under the direction of Shane Kretzmann.  
 * 
 * This class is used to validate input data from online forms though any input will do.
 *
 * @package Input-Output
**/
/**
 * Data validation and sanitation class for user input forms.
 * 
 * This class will validate multiple data based on what is expected to be received.  All known ways to enter each type<br>
 * of data is accounted for.  For example, the function phone() will allow for (555) 555-5555 and 5555555555 and<br>
 * 555-555-5555 etc.  The function cleanInputs() should be used on all user input arrays to protect against XSS Attacks.<br>
 * @author Shane Kretzmann <SKretzmann@WalkerSystemsSupport.com>
 * @version 2.8:09200811 
 *
*/
class validate{
	var $country_code;
	var $string;

	/**
        * Regular expressions for postcode of the following countries
        * at = austria <br>
        * au = australia <br>
        * ca = canada <br>
        * de = german <br>
        * ee = estonia <br>
        * nl = netherlands <br>
        * it = italy <br>
        * pt = portugal <br>
        * se = sweden <br>
        * uk = united kingdom <br>
        * us = united states <br>
		* @var array $pattern_postocde
        **/
	var $pattern_postcode=array(
           'at'=>'^[0-9]{4}$', // austria
           'au'=>'^[2-9][0-9]{2,3}$', // australia
           'ca'=>'^[ABCEGHJKLMNPRSTVXY][0-9][A-Z] [0-9][A-Z][0-9]$', // canada
           'de'=>'^[0-9]{5,5}$', // germany
           'ee'=>'^[0-9]{5,5}$', // estonia
           'nl'=>'^[0-9]{4,4}\s[a-zA-Z]{2,2}$', // netherlands
           'it'=>'^[0-9]{5,5}$', // italy
           'pt'=>'^[0-9]{4,4}-[0-9]{3,3}$', // portugal
           'se'=>'^[0-9]{3,3}\s[0-9]{2,2}$', // sweden
           'uk'=>'^(GIR 0AA|[A-PR-UWYZ]([0-9]{1,2}|([A-HK-Y][0-9]|[A-HK-Y][0-9]([0-9]|[ABEHMNPRV-Y]))|[0-9][A-HJKS-UW]) [0-9][ABD-HJLNP-UW-Z]{2})$', // United Kingdom

		   'us'=>'^[0-9]{5,5}(-[0-9]{4,4})?$' // United States

        );
	
	/** Regular expression to match a single email address
	 * Allows -> "Name" <email@email.com> <- or -> email@email.com
	 * @var regex $pattern_email
	 */
	var $pattern_email='^((["][a-zA-Z0-9_\-\.]+["])[ ]+[<]*)?([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)[>]?$';
	/** Regular expression to match ip addresses
	 * ie: 64.233.169.147
	 * @var regex $pattern_ip
	 */
	var $pattern_ip='([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})';
	/** Regular Expression to match a website address
	 * Accepts: www.website.com or http://www.website.com or https://www.website.com
	 * @var regex $pattern_url
	 */	
	var $pattern_url ='^(https?://)?(([0-9a-z_!~*().&=+$%-]+:)?[0-9a-z_!~*().&=+$%-]+@)?(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-z_!~*()-]+\.)*([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.[a-z]{2,6})(:[0-9]{1,4})?((/?)|(/[0-9a-z_!~*().;?:@&=+$,%#-]+)+/?)$';
//	var $pattern_url = "^(https?://)?(([0-9a-z_!~*'().&=+$%-]+:)?[0-9a-z_!~*'().&=+$%-]+@)?(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-z_!~*'()-]+\.)*([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.[a-z]{2,6})(:[0-9]{1,4})?((/?)|(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$";
	/** Regular expression to match us telephone patterns
	 * Accepts: (555)555-5555 or 555-555-5555 or 5555555555 or 555 555 5555
	 * @var regex $pattern_phone
	 */
	var $pattern_phone='/^[(\[]?\d{3}[)-\.\] ]*\d{3}[-\. ]?\d{4}$/';
	
	
/**
*  This holds the data taken from the September 2007 SSA Monthly Issuance Table.
*  You can update the data at any time by simply cutting and pasting the data<br>
*  found at http://www.ssa.gov/employer/highgroup.txt into the variable below.
*
*  NOTE: Chances are you will only be using this function to validate adult
*  SSN's-<br>  If that is the case, then you have a good 18 years before there are<br>
*  any adults with SSNs that are not included in the data below.
* @var tableData $ssn_data 
* @url http://www.ssa.gov/employer/highgroup.txt
*/
var $ssn_data = "
001 06  002 04	003 04	004 08	005 08	006 08
007 06 	008 90	009 90  010 90  011 90  012 90
013 90	014 90	015 90	016 90	017 90	018 90
019 90	020 90	021 90	022 90	023 90	024 90
025 90	026 90	027 90*	028 88	029 88	030 88
031 88	032 88 	033 88	034 88	035 72	036 72
037 72  038 72	039 70	040 11	041 11  042 11
043 11	044 11	045 11	046 11	047 11	048 08
049 08	050 96	051 96	052 96	053 96	054 96
055 96	056 96	057 96	058 96	059 96  060 96 
061 96  062 96  063 96  064 96  065 96	066 96
067 96	068 96	069 96	070 96	071 96	072 96
073 96	074 96	075 96	076 96	077 96	078 96
079 96	080 96	081 96	082 96	083 96	084 96
085 96	086 96	087 96	088 96	089 96	090 96
091 96	092 96	093 96	094 96	095 96	096 96
097 96	098 96	099 96	100 96	101 96	102 96
103 96	104 96	105 96	106 96	107 96	108 96
109 96	110 96*	111 96*	112 96*	113 96* 114 94
115 94  116 94  117 94  118 94  119 94	120 94
121 94	122 94	123 94	124 94 	125 94 	126 94 
127 94  128 94	129 94	130 94	131 94	132 94
133 94	134 94	135 19	136 19	137 19	138 19
139 19	140 19	141 19	142 19* 143 19*	144 17
145 17	146 17	147 17	148 17	149 17	150 17
151 17	152 17	153 17	154 17  155 17	156 17
157 17	158 17	159 84	160 84	161 84	162 84
163 84	164 84	165 84	166 84	167 84	168 84
169 84	170 84	171 84	172 84	173 84	174 84
175 84	176 84	177 84	178 84*	179 84*	180 82
181 82	182 82	183 82	184 82	185 82	186 82
187 82	188 82	189 82	190 82	191 82	192 82
193 82	194 82	195 82  196 82	197 82	198 82
199 82 	200 82	201 82	202 82	203 82	204 82
205 82	206 82	207 82	208 82	209 82	210 82
211 82	212 79	213 79	214 79	215 79	216 79 
217 79*	218 77	219 77	220 77	221 06	222 04
223 99	224 99  225 99  226 99  227 99	228 99
229 99	230 99	231 99	232 53	233 53	234 53
235 53	236 53	237 99	238 99	239 99	240 99
241 99	242 99	243 99  244 99  245 99	246 99
247 99	248 99	249 99	250 99	251 99	252 99
253 99	254 99	255 99	256 99	257 99	258 99
259 99	260 99	261 99	262 99	263 99	264 99
265 99	266 99	267 99	268 13	269 13	270 13
271 13 	272 13 	273 13	274 13	275 13	276 13
277 13	278 13	279 13	280 13	281 13	282 13
283 13	284 13*	285 13*	286 11  287 11	288 11
289 11 	290 11	291 11	292 11	293 11	294 11
295 11	296 11	297 11	298 11	299 11	300 11
301 11	302 11	303 33*	304 31	305 31	306 31
307 31	308 31	309 31	310 31 	311 31	312 31
313 31	314 31  315 31	316 31	317 31	318 06
319 06	320 06	321 06	322 06	323 06	324 06
325 06	326 06	327 06	328 06	329 06	330 06 
331 06 	332 06	333 06	334 06  335 06  336 06
337 06	338 06	339 06	340 06	341 06	342 06
343 06	344 06	345 06	346 06	347 06*	348 06*
349 04	350 04	351 04 	352 04 	353 04	354 04
355 04	356 04	357 04	358 04	359 04	360 04
361 04	362 35	363 35*	364 35*	365 33	366 33
367 33	368 33	369 33	370 33	371 33	372 33
373 33	374 33	375 33	376 33	377 33 	378 33 
379 33	380 33	381 33	382 33	383 33	384 33
385 33	386 33  387 29	388 29	389 29	390 29
391 29	392 29	393 27  394 27	395 27	396 27
397 27	398 27	399 27	400 67	401 67 	402 67  
403 67	404 67	405 67	406 67*	407 65	408 99
409 99	410 99	411 99  412 99	413 99	414 99
415 99	416 61	417 61	418 61	419 61 	420 61
421 61	422 61	423 61	424 61	425 99	426 99
427 99	428 99	429 99	430 99	431 99	432 99
433 99	434 99	435 99	436 99	437 99	438 99
439 99  440 23	441 23 	442 23	443 23	444 23
445 23	446 23*	447 21	448 21	449 99	450 99
451 99	452 99	453 99	454 99	455 99	456 99
457 99	458 99	459 99	460 99	461 99	462 99
463 99	464 99	465 99	466 99	467 99	468 51
469 51*	470 49	471 49	472 49	473 49	474 49
475 49	476 49	477 49	478 37	479 37	480 37
481 37  482 37	483 37	484 37	485 35	486 25
487 25	488 25 	489 25	490 25	491 25	492 25
493 25	494 25	495 25*	496 23	497 23	498 23
499 23  500 23	501 33	502 33*	503 41  504 39 
505 53	506 51	507 51 	508 51	509 27	510 27
511 27	512 27	513 27  514 27	515 27*	516 45
517 43 	518 77	519 77  520 53  521 99	522 99
523 99	524 99	525 99	526 99	527 99	528 99
529 99	530 99	531 63*	532 61	533 61	534 61
535 61  536 61	537 61	538 61  539 61*	540 73 
541 73	542 73	543 73	544 73	545 99	546 99
547 99	548 99	549 99	550 99	551 99	552 99
553 99	554 99	555 99	556 99	557 99	558 99
559 99	560 99	561 99	562 99	563 99	564 99
565 99	566 99	567 99	568 99	569 99	570 99
571 99	572 99	573 99	574 49	575 99	576 99
577 45	578 45* 579 43	580 37	581 99	582 99
583 99	584 99	585 99	586 61	587 99  588 03
589 99  590 99	591 99	592 99	593 99  594 99  
595 99  596 84	597 84	598 84*	599 82	600 99	
601 99  602 65	603 65	604 65	605 65	606 65	
607 65* 608 65*	609 65*	610 65*	611 65*	612 65*	
613 65* 614 65*	615 63	616 63	617 63	618 63	
619 63  620 63	621 63	622 63	623 63	624 63 	
625 63  626 63	627 11	628 11	629 11	630 11	
631 11* 632 11*	633 11*	634 11*	635 11* 636 11* 	
637 08  638 08	639 08	640 08  641 08	642 08	
643 08  644 08	645 08	646 96  647 94	648 44	
649 44  650 46  651 46*	652 44  653 44	654 26	
655 26  656 26	657 26	658 24	659 16  660 16*  
661 14  662 14  663 14  664 14	665 14	667 34  
668 34  669 34  670 34	671 34	672 34*	673 34*	
674 32  675 32  676 14	677 14* 678 12  679 12	
680 90* 681 14*	682 12  683 12  684 12	685 12 	
686 12  687 12	688 12	689 12	690 12	691 07  
692 07  693 07  694 07	695 07  696 07  697 07	
698 07  699 07* 700 18	701 18  702 18  703 18  
704 18  705 18  706 18	707 18  708 18  709 18  
710 18  711 18	712 18	713 18  714 18  715 18  
716 18  717 18	718 18	719 18  720 18  721 18  
722 18  723 18	724 28	725 18  726 18  727 10	
728 14  729 10	730 10	731 10  732 09	733 09	
750 09  751 07	752 01  753 01  756 05  757 05 	
758 05  759 05  760 05* 761 03 	762 03  763 03 
764 80  765 80*	766 64* 767 64*	768 62  769 62
770 62  771 62  772 62*
";
/** A list of known bad SSNs
 * @var array $known_bogus_ssn
 */
var $known_bogus_ssn = array(111111111, 222222222, 333333333, 444444444, 555555555,
                     666666666, 777777777, 888888888, 999999999, 000000000,
                     123456789, 987654321, 121212121, 101010101, 010101010,
                     002281852, 042103580, 062360749, 078051120, 095073645,
                     128036045, 135016629, 141186941, 165167999, 165187999, 
                     165207999, 165227999, 165247999, 189092294, 212097694, 
                     212099999, 306302348, 308125070, 468288779, 549241889);
											
	
	/**
	* The constructor of the validation class
	*/	
	function validate(){ }

	/**
	* Validates the string if it consists of any alphabetical chars (case insensitive)
	* @param string $string String of data to evaluate
	* @param int $num_chars the number of chars in the string (default value is 0)
	* @param string $behave defines how to check the string: min, max or exactly number of chars (default is min)
	*/	
	function alpha($string,$num_chars=0,$behave="min"){
		if($behave=="min"){
			$pattern="^[a-zA-Z ]{".$num_chars.",}$";
		}else if ($behave=="max"){
			$pattern="^[a-zA-Z ]{0,".$num_chars."}$";
		}else if ($behave=="exact"){
			$pattern="^[a-zA-Z ]{".$num_chars.",".$num_chars."}$";
		}
		return ereg($pattern,$string);
	}
	
	/**
	* Validates the string if it consists of lowercase alphabetical chars
	* @param string $string String of data to evaluate
	* @param int $num_chars the number of chars in the string (default value is 0)
	* @param string $behave defines how to check the string: min, max or exact number of chars (default is min)
	*/	
	function alphaLower($string,$num_chars=0,$behave="min"){
		if($behave=="min"){
			$pattern="^[a-z ]{".$num_chars.",}$";
		}else if ($behave=="max"){
			$pattern="^[a-z ]{0,".$num_chars."}$";
		}else if ($behave=="exact"){
			$pattern="^[a-z ]{".$num_chars.",".$num_chars."}$";
		}
		return ereg($pattern,$string);
		
	}
	
	/**
	* Validates the string if it consists of uppercase alphabetical chars
	* @param string $string String of data to evaluate
	* @param int $num_chars the number of chars in the string (default value is 0)
	* @param string $behave defines how to check the string: min, max or exact number of chars (default is min)
	*/	
	function alphaUpper($string,$num_chars=0,$behave="min"){
		if($behave=="min"){
			$pattern="^[A-Z ]{".$num_chars.",}$";
		}else if ($behave=="max"){
			$pattern="^[A-Z ]{0,".$num_chars."}$";
		}else if ($behave=="exact"){
			$pattern="^[A-Z ]{".$num_chars.",".$num_chars."}$";
		}
		return ereg($pattern,$string);
		
	}
	
	/**
	* Validates the string if it consists of numeric chars
	* @param string $string String of data to evaluate
	* @param int $num_chars the number of chars in the string (default value is 0)
	* @param string $behave defines how to check the string: min, max or exact number of chars (default is min)
	*/	
	function num($string,$num_chars=0,$behave="min"){
		if($behave=="min"){
			$pattern="^[0-9 ]{".$num_chars.",}$";
		}else if ($behave=="max"){
			$pattern="^[0-9 ]{0,".$num_chars."}$";
		}else if ($behave=="exact"){
			$pattern="^[0-9 ]{".$num_chars.",".$num_chars."}$";
		}
		return ereg($pattern,$string);
	}
	
	/**
	* Validates the string if it consists of alphanumerical chars (case insensitive)
	* @param string $string String of data to evaluate
	* @param int $num_chars the number of chars in the string (default value is 0)
	* @param string $behave defines how to check the string: min, max or exact number of chars (default is min)
	*/		
	function alphaNum($string,$num_chars=0,$behave="min"){
		if($behave=="min"){
			$pattern="^[0-9a-zA-Z ]{".$num_chars.",}$";
		}else if ($behave=="max"){
			$pattern="^[0-9a-zA-Z ]{0,".$num_chars."}$";
		}else if ($behave=="exact"){
			$pattern="^[0-9a-zA-Z ]{".$num_chars.",".$num_chars."}$";
		}
		return ereg($pattern,$string);
	}
	/**
	* Validates the string if it consists of alphanumerical chars also allows for normal punctuation
	*
	* This will fail on =, >, or >  keeping you safe from script injections.
	* @param string $string String of data to evaluate
	* @param int $num_chars the number of chars in the string (default value is 0)
	* @param string $behave defines how to check the string: min, max or exact number of chars (default is min)
	*/		
	function text($string,$num_chars=0,$behave="min"){
		if($behave=="min"){
			$pattern="^[0-9a-zA-Z :;!@#$%^&*()_+\"',.?/|\-`~]{".$num_chars.",}$";
		}else if ($behave=="max"){
			$pattern="^[0-9a-zA-Z :;!@#$%^&*()_+\"',.?/|\-`~]{0,".$num_chars."}$";
		}else if ($behave=="exact"){
			$pattern="^[0-9a-zA-Z :;!@#$%^&*()_+\"',.?/|\-`~]{".$num_chars.",".$num_chars."}$";
		}
		return ereg($pattern,$string);
	}

	/**
	* Validates the string if its a valid postcode
	*
	*	Valid $country values: <br>
	*		at = austria <br>
	*	 	au = australia <br>
	*		ca = canada <br>
	*		de = german <br>
	*		ee = estonia <br>
	*		nl = netherlands <br>
	*		it = italy <br>
	*		pt = portugal <br>
	*		se = sweden <br>
	*		uk = united kingdom <br>
	*		us = united states <br>
	* @param string $string String of data to evaluate
	* @param string $country_code the country code for the country of the postcode (ie: us)
	*/	
	function postcode($string,$country_code='us'){
		if(array_key_exists($country_code,$this->pattern_postcode)){
			return ereg($this->pattern_postcode[$country_code],$string);
		}else{
			return false;
		}
	}

	/**
	* Validates the string if its a valid email address
	* 
	* Will allow both "Name" <email@email.com> and just email@email.com<br>
	* You can use commas to include multiple addresses.  Make sure you set 
	* $num to the exact number of email accounts you are passing to the method.<br>
	*
	* ie: email('"test1" <test1@test.com>, test2@test.com, test3@test.com',3);
	* @return boolean FALSE if invalid email format, 0 if wrong number of emails expected, otherwise TRUE
	* @param string $string String of data to evaluate
	* @param int $num Number of emails expected to evaluate
	*/	
	function email($string,$num=1){
		$emails = explode(',',$string);
		if (isset($emails)) { // Anything to evaluate?
			if (count($emails) == $num) { // are we expecting this many emails?
				foreach ($emails as $key => $addr)
					if (!ereg($this->pattern_email,trim($addr))) { return FALSE; } 
			} else { return 0; }
		} 
		// If we have rejected by now it must be good
		return TRUE;
	}
	
	/**
	* Validates the string if its a valid us phone number
	* 
	* Will allow for any normal way a person would enter a phone number
	* @param string $string String of data to evaluate
	*/	
	function phone($string){
		if ($string==0) { return false; }
		return preg_match($this->pattern_phone, $string, $scrap);
	}
	
	/**
	* Validates the string if its a valid ip address
	*
	* ie: 255.255.255.255
	* @param string $string String of data to evaluate
	*/	
	function ip($string){
		$parts = explode(".",$string);
		$i = 0;
		foreach ($parts as $key => $pair) {
			if ($pair == 0) { return false; }
		}
		return ereg($this->pattern_ip,$string); 
	}
	
	/**
	* Validates the string if its a valid URL
	* 
	* Will validate with or without http(s)://
	* @param string $string String of data to evaluate
	*/	
	function url($string){
		return ereg($this->pattern_url,$string);
	}
	
/**
 * CC - Credit Card method.
 *
 * This function accepts a credit card number and, optionally, a code for
 * a credit card name. If a Name<br> code is specified, the number is checked
 * against card-specific criteria, then validated with the Luhn Mod 10
 * formula. <br>Otherwise it is only checked against the formula. Valid name
 * codes are:
 *
 *    mcd - Master Card <br>
 *    vis - Visa <br>
 *    amx - American Express <br>
 *    dsc - Discover <br>
 *    dnc - Diners Club <br>
 *    jcb - JCB <br>
 *    swi - Switch <br>
 *    dlt - Delta <br>
 *    enr - EnRoute <br>
 *
 * You can also optionally specify an expiration date in the formay mmyy.
 * If the validation fails on the date,<br> the function returns 0. If it
 * fails on the number validation, it returns false.
 * @param int $Num The Credit Card number to evaluate
 * @param string $Name The type of credit card received (optional)
 * @param int $Exp Expiration date of card MMYY
 */	
	function CC($Num, $Name = "n/a", $Exp = "") {

	//  Check the expiration date first
	    if (strlen($Exp)) {
	      $Month = substr($Exp, 0, 2);
	      $Year  = substr($Exp, -2);

	      $WorkDate = "$Month/01/$Year";
	      $WorkDate = strtotime($WorkDate);
	      $LastDay  = date("t", $WorkDate);
	
	      $Expires  = strtotime("$Month/$LastDay/$Year 11:59:59");
	      if ($Expires < time()) return 0;
	    }

	//  Innocent until proven guilty
	    $GoodCard = true;

	//  Get rid of any non-digits
	    $Num = ereg_replace("[^0-9]", "", $Num);

	//  Perform card-specific checks, if applicable
	    switch ($Name) {

		    case "mcd" :
		      $GoodCard = ereg("^5[1-5].{14}$", $Num);
		      break;

		    case "vis" :
		      $GoodCard = ereg("^4.{15}$|^4.{12}$", $Num);
		      break;

		    case "amx" :
		      $GoodCard = ereg("^3[47].{13}$", $Num);
    		  break;

		    case "dsc" :
		      $GoodCard = ereg("^6011.{12}$", $Num);
			  break;

		    case "dnc" :
		      $GoodCard = ereg("^30[0-5].{11}$|^3[68].{12}$", $Num);
		      break;

		    case "jcb" :
		      $GoodCard = ereg("^3.{15}$|^2131|1800.{11}$", $Num);
		      break;
  
		    case "dlt" :
		      $GoodCard = ereg("^4.{15}$", $Num);
		      break;
	
		    case "swi" :
		      $GoodCard = ereg("^[456].{15}$|^[456].{17,18}$", $Num);
		      break;

		    case "enr" :
		      $GoodCard = ereg("^2014.{11}$|^2149.{11}$", $Num);
		      break;
	    }

	//  The Luhn formula works right to left, so reverse the number.
	    $Num = strrev($Num);

	    $Total = 0;

	    for ($x=0; $x<strlen($Num); $x++) {
	      $digit = substr($Num,$x,1);

	//    If it's an odd digit, double it
	      if ($x/2 != floor($x/2)) {
	        $digit *= 2;

		//    If the result is two digits, add them
    	    if (strlen($digit) == 2)
    	      $digit = substr($digit,0,1) + substr($digit,1,1);
    	  }

	//    Add the current digit, doubled and added if applicable, to the Total
	      $Total += $digit;
	    }

	//  If it passed (or bypassed) the card-specific check and the Total is
	//  evenly divisible by 10, it's cool!
	    if ($GoodCard && $Total % 10 == 0) return true; else return false;
	}	
	/**
	*  Determines whether or not a particular Social Security Number
	*  has been issued by the Social Security Administration<br> based on their
	*  (bizzare) sequence of number issuance.  It also checks for known bogus
	*  numbers, such as ones<br> that have been used in advertising.  
	* @param int $ssn The social security number to evaluate 
	*/	  
	function ssn($ssn) {
	  	// get rid of any non digits
		$ssn = ereg_replace("[^0-9]", "", $ssn);
		$area = intval(substr($ssn,0,3));
		$group = intval(substr($ssn,3,2));
		$serial = intval(substr($ssn,5,4));
		preg_match_all("/(\d{3} \d{2})/", $this->ssn_data, $results, PREG_SET_ORDER); //pull in ssn dataTable
		for($i=0;$i<sizeof($results);$i++) {
	    	list($area_temp,$group_temp) = explode(" ",$results[$i][1]);
			$group_val[$area_temp] = $group_temp;
	  	}
		$high = isset($group_val[$area]);
		$group_level = $this->_getLevelSSN($group);
		$high_level = $this->_getLevelSSN($high);
		if ($group_level<$high_level) $pass=true;
		if ( ($group_level==$high_level) && ($group<=$high) ) $pass=true; 
		if ( ($area==0) || ($group==0) || ($serial==0) ) $pass=false; // cant have 0 valued ssn groups
		if ( ($area==666) || ( ($area>699) && ($area<729) ) || ($area>899) ) $pass=false; // known invalid areas
		if ( (strlen($ssn)!=9) || (!is_numeric($ssn)) ) $pass=false; // make sure its 9 numeric digits
		// fail if data input is one of the known bogus ssn 
		for($i=0;$i<sizeof($this->known_bogus_ssn);$i++) if (intval($ssn)==$this->known_bogus_ssn[$i]) $pass=false;
		return $pass; 
	}	
	/** used by ssn() function to determine SSN level
	* @access private
	*/
	function _getLevelSSN($num) {
	  if ( ($num<10) && (($num%2)==1) ) $level=1;
	  if ( ($num>09) && (($num%2)==0) ) $level=2;
	  if ( ($num<10) && (($num%2)==0) ) $level=3;
	  if ( ($num>09) && (($num%2)==1) ) $level=4;
	  return $level;
	}

  /** Sanatizes user input to protect against XSS attacks.  Also strips HTML from input
   * optional $forEmail - 'HTML' or 'PLAIN': returns each key and pair combination in the array as a formated string
   *    
   * @access public
   */          
  function cleanInput($array,$forEmail=false) {
    if ((strtoupper($forEmail) != 'PLAIN') && (strtoupper($forEmail) != 'HTML')) { $forEmail = false; }
    $emailData = '';

    if (is_array($array)) { 
      foreach ($array as $key => $val) {
  
        global ${$key}; // globalize the array key as a variable name
         // remove server slashing and HTML tags as needed.
         $val = (get_magic_quotes_gpc()) ? stripslashes(strip_tags($val)) : strip_tags($val);
  
      
         // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
         // this prevents some character re-spacing such as <java\0script>
         // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
         $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
         
         // straight replacements, the user should never need these since they're normal characters
         // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
         $search = 'abcdefghijklmnopqrstuvwxyz';
         $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
         $search .= '1234567890!@#$%^&*()';
         $search .= '~`";:?+/={}[]-_|\'\\';
         for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
         
            // &#x0040 @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
            // &#00064 @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
         }
         
         // now the only remaining whitespace attacks are \t, \n, and \r
         $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
         $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
         $ra = array_merge($ra1, $ra2);
         
         $found = true; // keep replacing as long as the previous round replaced something
         while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
               $pattern = '/';
               for ($j = 0; $j < strlen($ra[$i]); $j++) {
                  if ($j > 0) {
                     $pattern .= '(';
                     $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                     $pattern .= '|';
                     $pattern .= '|(&#0{0,8}([9|10|13]);)';
                     $pattern .= ')*';
                  }
                  $pattern .= $ra[$i][$j];
               }
               $pattern .= '/i';
               $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
               $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
               if ($val_before == $val) {
                  // no replacements were made, so exit the loop
                  $found = false;
               }
            }
         }
         $$key = $val;

         if (!empty($forEmail)) {
            if ((strtoupper($key) != 'SUBMIT') && (strtoupper($key) != 'SUBMIT_X') && (strtoupper($key) != 'SUBMIT_Y')
                 && (strtoupper($key) != 'FINGERPRINT')) {
              if ($forEmail == 'HTML') {
                $emailData.="<strong>$key</strong>: $val<br />";
              } else { $emailData.= "$key : $val\n"; }
            }
         }
      }
      
      if ($forEmail) { return $emailData; }
    } else { // input is a string not an array, process once and return the clean value
    
       $val = $array; 
       if ($forEmail) { die('ERROR::class.validate.php::cleanInputs()::You cannot return a string suitable for emailing unless you input an array to this function.'); }
       // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
       // this prevents some character re-spacing such as <java\0script>
       // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
       $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
       
       // straight replacements, the user should never need these since they're normal characters
       // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
       $search = 'abcdefghijklmnopqrstuvwxyz';
       $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
       $search .= '1234567890!@#$%^&*()';
       $search .= '~`";:?+/={}[]-_|\'\\';
       for ($i = 0; $i < strlen($search); $i++) {
          // ;? matches the ;, which is optional
          // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
       
          // &#x0040 @ search for the hex values
          $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
          // &#00064 @ 0{0,7} matches '0' zero to seven times
          $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
       }
       
       // now the only remaining whitespace attacks are \t, \n, and \r
       $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
       $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
       $ra = array_merge($ra1, $ra2);
       
       $found = true; // keep replacing as long as the previous round replaced something
       while ($found == true) {
          $val_before = $val;
          for ($i = 0; $i < sizeof($ra); $i++) {
             $pattern = '/';
             for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                   $pattern .= '(';
                   $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                   $pattern .= '|';
                   $pattern .= '|(&#0{0,8}([9|10|13]);)';
                   $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
             }
             $pattern .= '/i';
             $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
             $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
             if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
             }
          }
       }
       return $val;
    }
  }
  
  function setRequired($array) {
    $this->requiredFields = $array;
  }
  function checkRequired($array) {
    if (!$this->requiredFields) { return false; }   
    
    $errors ='';
    
    foreach ($array as $key => $pair) {
      if (in_array($key,$this->requiredFields)) {
        if (empty($pair)) { $errors.= $this->_formatErr(ucwords(str_replace('_',' ',$key)).' is a required field.'); }
        else {
          if (stristr($key,'captcha')) {
            global $badCaptcha;
            if ($badCaptcha) { $errors.= $this->_formatErr('Required Captcha Code does not match'); }
          }
           if (stristr($key,'email')) {
            if (!$this->email($pair)) { $errors.= $this->_formatErr('Please enter a valid Email address.'); }
          }
          if (stristr($key,'phone')) {
            if (!$this->phone($pair)) { $errors.= $this->_formatErr('Please enter a valid Phone number.'); }
          }
          if (stristr($key,'zip')) { 
            if (!$this->postcode($pair)) { $errors.= $this->_formatErr('Please enter a valid US ZIP Code.'); }
          }
          if ((stristr($key,'url')) || (stristr($key,'website'))) { 
            if (!$this->url($pair)) { $errors.= $this->_formatErr('Please enter a valid website URL.'); }
          }
          if (stristr($key,'ssn')) { 
            if (!$this->ssn($pair)) { $errors.= $this->_formatErr('Please enter a valid Social Security number.'); }
          }

        }
      }      
    }

    if (!empty($errors)) {
      $errors = '
        <fieldset class="errors" width="50%">
          <legend class="errors">
              <strong>Please Correct Your Errors:</strong>
          </legend>
          <ul class="errors">
            '.$errors.'
          </ul>
        </fieldset>
        <br />';
    }
     
    return $errors;   
  } 
  function _formatErr($text) {
    return '<li class="errors">'.$text.'</li>';
  }
}
?>