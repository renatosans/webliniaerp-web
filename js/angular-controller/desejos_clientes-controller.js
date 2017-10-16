app.controller('DesejosClienteController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.desejos 		= [];
	ng.paginacao 	= {desejos:null}
	ng.detalhes = [];
	ng.allSelected = false;
	ng.selected = false;

	ng.loadDesejos = function(offset,limit) {
		ng.allSelected = false;
		ng.selected = false;

		aj.get(baseUrlApi()+"clientes/desejos/"+ offset +"/"+ limit +"?tdu->id_empreendimento="+ ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				$.each(data.desejos, function(i, item) {
					item.flgExcluir = false;
				});

				ng.desejos 			= data.desejos;
				ng.paginacao.desejos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.desejos = [] ;
			});
	}

	ng.select = function(item) {
		var b = false;

		$.each(ng.desejos, function(i, item) {
			if(item.flgExcluir)
				b = true;
		});

		ng.selected = b;
	}

	ng.selectAll = function() {
		ng.allSelected = !ng.allSelected;

		$.each(ng.desejos, function(i, item) {
			item.flgExcluir = ng.allSelected;
		});

		ng.select();
	}

	ng.excluir = function() {
		var arrDelete = [];

		$.each(ng.desejos, function(i, item) {
			if(item.flgExcluir)
				arrDelete.push(item);
		});

		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir '+ arrDelete.length +' desejos dos clientes?</strong>');

		dlg.result.then(function(btn){
			aj.post(baseUrlApi()+"clientes/desejos/delete", {arrDesejos: arrDelete})
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Desejos excluidos com sucesso</strong>');
					ng.loadDesejos(0,10);
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null ? alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.loadDesejos(0,10);
});
