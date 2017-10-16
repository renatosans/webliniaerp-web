var Utils = {
	getBaseUrlWeb: function(){
		var pos   = window.location.pathname.lastIndexOf("/");
		var pasta = "";

		if(location.hostname == 'localhost' || window.location.hostname.indexOf("192.168.") != -1)
			pasta = "/webliniaerp-web";

		return location.protocol+'//'+location.hostname+pasta+'/';
	},
	getBaseUrlApi: function(){
	    if(location.hostname.indexOf("192.168.") != -1)
	        return "http://"+ location.hostname +"/webliniaerp-api/";
		else if(location.hostname == 'localhost')
			return "http://localhost/webliniaerp-api/";
		else
			return location.protocol+'//'+location.hostname+'/api/';
	}
};

var APIService = {
	getEmpreendimentosAtivos: function() {
		var items = null;
		$.ajax({
			url: Utils.getBaseUrlApi() +'empreendimentos/ativos',
			async: false,
			success: function(data) {
				items = data;
			},
			error: function(error) {
				console.error(error);
			}
		});
		return items;
	}
}

var Map = {
	map: null,
	initMap: function(){
		var boundaries = new google.maps.LatLngBounds(new google.maps.LatLng(-25.33402602913433, -53.1377734375), new google.maps.LatLng(-19.7670355171696, -44.128984375));
		var bgmap = new google.maps.GroundOverlay('img/saopaulo.png', boundaries);
		var mapOptions = {
			streetViewControl: false,
			scrollWheel: false,
	        zoom: 6,
	        center: new google.maps.LatLng(-23.550339, -46.633455)
	    };
	    Map.map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
	    Map.map.setCenter(new google.maps.LatLng(-23.550339, -46.633455));
	    bgmap.setMap(Map.map);
	    Front.getDataFromDatabase();
	    Map.createWebliniaERPMapItem();
	},
	createWebliniaERPMapItem: function() {
		var marker = new google.maps.Marker({
			map: Map.map,
			icon: 'img/logo-vector.png',
			position: new google.maps.LatLng(-23.681661, -46.682290),
			title: 'WebliniaERP'
		});

		var content = '<strong>WebliniaERP</strong>';

		var infowindow = new google.maps.InfoWindow();
		google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){
			return function() {
				infowindow.setContent(content);
				infowindow.open(Map.map,marker);
			};
		})(marker,content,infowindow));
	},
	createMapItem: function(item) {
		var marker = new google.maps.Marker({
			map: Map.map,
			position: new google.maps.LatLng(item.latitude, item.longitude),
			title: item.nome_empreendimento
		});

		var content = '<strong>'+ item.nome_empreendimento +'</strong>';

		var infowindow = new google.maps.InfoWindow();
		google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){
			return function() {
				infowindow.setContent(content);
				infowindow.open(Map.map,marker);
			};
		})(marker,content,infowindow));
	}
}

var Front = {
	getDataFromDatabase: function (){
		var empreendimentosAtivos = APIService.getEmpreendimentosAtivos();
		
		var activeClients 	= 0;
		var paidClients 	= 0;
		var testingClients 	= 0;
		var partnerClients 	= 0;
		var notPaidClients 	= 0;
		var debitClients 	= 0;

		$.each(empreendimentosAtivos, function(i, item) {
			Map.createMapItem(item);
			activeClients 	+= (item.flg_ativo 		== 1) ? 1 : 0;
			paidClients 	+= (item.flg_pagante 	== 1 && item.flg_teste == 0) ? 1 : 0;
			testingClients 	+= (item.flg_teste 		== 1) ? 1 : 0;
			partnerClients 	+= (item.flg_parceiro 	== 1) ? 1 : 0;
			notPaidClients 	+= (item.flg_pagante 	== 0 && item.flg_teste == 0) ? 1 : 0;
			debitClients 	+= (item.flg_debito 	== 1) ? 1 : 0;
		});

		$(".stat-icon.active-clients").text(activeClients);
		$(".stat-icon.paid-clients").text(paidClients);
		$(".stat-icon.testing-clients").text(testingClients);
		$(".stat-icon.partner-companies").text(partnerClients);
		$(".stat-icon.not-paid-clients").text(notPaidClients);
		$(".stat-icon.debit-clients").text(debitClients);

		var categories 	= _.keys(_.groupBy(_.sortBy(empreendimentosAtivos, 'dsc_segmento'), 'dsc_segmento'));
		var values 		= [];
		$.each(_.groupBy(_.sortBy(empreendimentosAtivos, 'dsc_segmento'), 'dsc_segmento'), function(i,item){values.push(item.length)});

		Highcharts.chart('graph', {
			chart: {
				type: 'bar'
			},
			title: {
				text: 'Clientes por Segmento'
			},
			xAxis: {
				categories: categories,
				title: {
					text: null
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Qtd.',
					align: 'high'
				},
				labels: {
					overflow: 'justify'
				}
			},
			plotOptions: {
				bar: {
					dataLabels: {
						enabled: true
					}
				}
			},
			legend: {
				layout: 'vertical',
				align: 'right',
				verticalAlign: 'top',
				x: -40,
				y: 80,
				floating: true,
				borderWidth: 1,
				backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
				shadow: true
			},
			credits: {
				enabled: false
			},
			series: [{
				name: 'Empreendimentos',
				data: values
			}]
		});
	}
};

google.maps.event.addDomListener(window, 'load', Map.initMap);