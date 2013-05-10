$(document).ready(function(){
  $(".outbound").ceebox();}
);
// The random number function
function RandomNumber(min,max) {
if(max<min) {
   var i=max;
   max=min;
   min=i;
   }
return Math.round(Math.random()*(max-min)+min);
}


