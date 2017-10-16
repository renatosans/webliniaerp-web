app.controller('MapaController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.getAddresses = function() {
		var arr = [];

		aj.get(baseUrlApi()+"usuarios/mapa/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				arr = data.usuarios;

				for (var i = 0; i < arr.length; i++) {
					showAddress(arr[i]);
				};
			})
			.error(function(data, status, headers, config) {
				arr = null;
			});
	}

	ng.resizeMap = function() {
		if($("#top-nav").css("display") == "block"){
			$("#map_canvas").css("height", 700);
			$("footer").css("margin-left", 0);
			$("#main-container").css("margin-left", 0).css("padding-top", 0);
			$("#top-nav").toggle();
			$("aside").toggle();
			$("#breadcrumb").toggle();
		}
		else {
			$("#map_canvas").css("height", 600);
			$("footer").css("margin-left", 194);
			$("#main-container").css("margin-left", 194).css("padding-top", 45);
			$("#top-nav").toggle();
			$("aside").toggle();
			$("#breadcrumb").toggle();
		}

		setCenter();
	}

	var geocoder;
	var map;
	var marker;

	function initializeGMAPI() {
		//MAP
		var latlng = new google.maps.LatLng(-23.55052,-46.633309);

		var options = {
			zoom: 4,
			center: latlng
		};

		map = new google.maps.Map(document.getElementById("map_canvas"), options);

		//GEOCODER
		geocoder = new google.maps.Geocoder();

		marker = new google.maps.Marker({
			map: map,
			draggable: false
		});
	}

	function setCenter() {
		map.setCenter(new google.maps.LatLng(-23.55052,-46.633309));
	}

	function showAddress(obj) {
		var location = new google.maps.LatLng(obj.num_latitude, obj.num_longitude);

		if(location) {
			map.setCenter(location);

			obj.dta_ultima_compra = (obj.dta_ultima_compra == null) ? "" : obj.dta_ultima_compra;

			var infowindow = new google.maps.InfoWindow({
				content: "<div id='content'>"+
							"<div id='siteNotice'></div>"+
							"<h4 id='firstHeading' class='firstHeading'>"+ obj.nme_cliente +"</h4>"+
							"<div id='bodyContent'>"+
								"<p>Última Compra: "+ obj.dta_ultima_compra +"</p>"+
								"<p class='text-danger'>Saldo Devedor:  R$ "+ obj.vlr_saldo_devedor +"</p>"+
								"<p class='text-success'>Total Acum. Compras: R$ "+ obj.vlr_saldo_acumulado_compras +"</p>"+
							"</div>"+
						"</div>"
			});

			var mapPin = 'img/';
				mapPin += (obj.acesso_restrito == 1) ? 'map-pin-green.png' : 'map-pin-red.png';

			var title = (obj.acesso_restrito == 0) ? obj.nme_cliente + " (bloqueado)" : obj.nme_cliente + " (liberado)";

			var marker = new google.maps.Marker({
				map: map,
				position: location,
				title: title,
				icon: mapPin
			});

			google.maps.event.addListener(marker, 'click', function() {
				infowindow.close();
				infowindow.open(map, marker);
			});
		}
	}

	initializeGMAPI();
	ng.getAddresses();
});
