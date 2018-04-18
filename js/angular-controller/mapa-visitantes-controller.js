app.controller('MapaController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();

	ng.qtd_visitantes_total = 0;
	ng.qtd_visitantes_hoje 	= 0;
	ng.qtd_clientes_total 	= 0;
	ng.qtd_novos_cadastros 	= 0;

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.loadCountClientes = function() {
		ng.qtd_clientes_total = 0;

		var query_string =  '?(tue->id_empreendimento[exp]=='+ng.userLogged.id_empreendimento+')';
			query_string += '&usu->flg_tipo=cliente';

		aj.get(baseUrlApi() +'usuarios'+ query_string)
			.success(function(data, status, headers, config) {
				ng.qtd_clientes_total = data.usuarios.length;
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.loadCountNovosClientes = function() {
		ng.qtd_novos_cadastros = 0;

		var query_string =  '?(tue->id_empreendimento[exp]=='+ng.userLogged.id_empreendimento+')';
			query_string += '&usu->flg_tipo=cliente';
			query_string += "&usu->dta_cadastro[exp]=> '"+ moment('14/04/2018', 'DD/MM/YYYY').format('YYYY-MM-DD 23:59:59') +"'";

		aj.get(baseUrlApi() +'usuarios'+ query_string)
			.success(function(data, status, headers, config) {
				ng.qtd_novos_cadastros = data.usuarios.length;
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.getAddresses = function() {
		ng.clientes = [];
		aj.get(baseUrlApi()+"usuarios/mapa/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.clientes = data.usuarios;
				ng.getVisitantesTotal();
				ng.getVisitantesHoje();

				angular.forEach(ng.clientes, function(cliente){
					showAddress(cliente);
				});
			})
			.error(function(data, status, headers, config) {
				arr = null;
			});
	}

	ng.getVisitantesTotal = function() {
		var req_url = baseUrlApi()+'clientes/visitantes';
			req_url += '?tue->id_empreendimento='+ ng.userLogged.id_empreendimento;
			req_url += '&group=true';

		aj.get(req_url)
			.success(function(visitas, status, headers, config) {
				angular.forEach(ng.clientes, function(cliente){
					var visita = _.findWhere(visitas, {id_cliente: cliente.id_cliente});
					
					if(!empty(visita)){
						ng.qtd_visitantes_total++;
						cliente.flg_visitou = true;
					}
					else
						cliente.flg_visitou = false;

					showAddress(cliente);
				});
			})
			.error(function(data, status, headers, config) {
				console.log(data);
			});
	}

	ng.getVisitantesHoje = function() {
		var dta_inicio_dia = moment().format('YYYY-MM-DD 00:00:00');
		var dta_final_dia = moment().format('YYYY-MM-DD 23:59:59');

		var req_url = baseUrlApi()+'clientes/visitantes';
			req_url += '?tue->id_empreendimento='+ ng.userLogged.id_empreendimento;
			req_url += "&tvc->dta_visita[exp]=BETWEEN '"+ dta_inicio_dia +"' AND '"+ dta_final_dia +"'";
			req_url += '&group=true';

		aj.get(req_url)
			.success(function(visitas, status, headers, config) {
				angular.forEach(ng.clientes, function(cliente){
					var visita = _.findWhere(visitas, {id_cliente: cliente.id_cliente});
					if(!empty(visita))
						ng.qtd_visitantes_hoje++;
				});
			})
			.error(function(data, status, headers, config) {
				console.log(data);
			});
	}

	ng.resizeMap = function() {
		if($("#top-nav").css("display") == "block"){
			$("#map_canvas").css("height", 650);
			$("footer").css("margin-left", 0);
			$("#main-container").css("margin-left", 0).css("padding-top", 0);
			$("#top-nav").toggle();
			$("aside").toggle();
			$("#breadcrumb").toggle();
		}
		else {
			$("#map_canvas").css("height", 550);
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
		map = new google.maps.Map(document.getElementById("map_canvas"), {
			zoom: 4,
			center: new google.maps.LatLng(-23.55052,-46.633309)
		});

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
				mapPin += (obj.flg_visitou) ? 'map-pin-green.png' : 'map-pin-red.png';

			var title = (obj.flg_visitou) ? obj.nme_cliente + " (bloqueado)" : obj.nme_cliente + " (liberado)";

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
	ng.loadCountClientes();
	ng.loadCountNovosClientes();
	ng.resizeMap();
});