// JavaScript Document

function isNumberKey(evt){

  var charCode = (evt.which) ? evt.which : evt.keyCode;

   if(charCode==8 || charCode==13|| charCode==99|| charCode==118 || charCode==46)
     {    return true;  }
    if (charCode > 31 && (charCode < 48 || charCode > 57))
    {  return false; }
    return true;
}


function isNumber(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode(key);
  if (key.length == 0) return;
  var regex = /^[0-9.,\b]+$/;
  if (!regex.test(key)) {
	  theEvent.returnValue = false;
	  if (theEvent.preventDefault) theEvent.preventDefault();
  }
}