function display(n) {


  if (LogIn()==true)  {
    return true;
  } else {
    return false;
  }

}

function LogIn(){

	password="";
	password=prompt("Please enter your Password:","");
	if (password!=null) {
		password=password.toLowerCase();
		if (password=="hogpen") {
			return true;
		} else  {
			alert("You must provide a valid password to continue.  \n\nIf you are a New Orleans H.O.G. member and have not been issued a password, \nplease send an email to hogpen@hdno.com with your full name and H.O.G. number.");
			return false;
		}
	}
}

