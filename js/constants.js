var AMBIENTE = 'CLIENTES';

function baseUrl(){
	var pos   = window.location.pathname.lastIndexOf("/");
	var pasta = "";

	if(location.hostname == 'localhost' || window.location.hostname.indexOf("192.168.") != -1 || window.location.hostname.indexOf("120.1.") != -1)
		pasta = "/~filipecoelho/webliniaerp-web";

	return location.protocol+'//'+location.hostname+pasta+'/';
}

function baseUrlApi(){
	return location.protocol +'//'+ location.hostname +'/~filipecoelho/webliniaerp-api/';
}
