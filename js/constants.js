var AMBIENTE = 'CLIENTES';

function baseUrl(){
	var pos   = window.location.pathname.lastIndexOf("/");
	var pasta = "";

	if(location.hostname == 'localhost' || window.location.hostname.indexOf("192.168.") != -1)
		pasta = "/wbl-web";

	return location.protocol+'//'+location.hostname+pasta+'/';
}

function baseUrlApi(){
	return 'http://192.168.0.120/wbl-api/';
}
