app.controller('RelatorioVendasClienteController', function($scope, $http, $window, UserService) {
	var ng = $scope,
		aj = $http;
	ng.userLogged = UserService.getUserLogado();
	ng.itensPorPagina = 10;
	ng.deposito = {};
	ng.depositos = [];
	ng.itens = [];
	ng.paginacao = {};

	var params = getUrlVars();

	ng.reset = function() {
		ng.itens = [];
		$(".has-error").removeClass("has-error");
	}

	ng.resetFilter = function() {
		ng.deposito = {};
		ng.reset();
	}

	ng.aplicarFiltro = function() {
		ng.reset();

		$(".modal").modal('show');

		ng.loadItens(0);
	}

	ng.loadVendas = function(offset, limit) {
		var queryString = "?ven->id_empreendimento="+ng.userLogged.id_empreendimento;
			queryString += "&usu->id="+params.id_cliente;

		aj.get(baseUrlApi()+"relatorio/vendas/analitico/cliente/"+offset+'/'+limit+"/"+queryString)
			.success(function(data, status, headers, config) {
				ng.vendas = data.vendas;
				ng.paginacao.vendas = data.paginacao ;
				$("#modal-aguarde").modal('hide');

			})
			.error(function(data, status, headers, config) {
				ng.vendas = [];
				ng.paginacao.vendas = [];
				$("#modal-aguarde").modal('hide');
			});

		// aj.get(baseUrlApi()+"cliente/vendas/"+params.id_cliente +"?venda_confirmada=1&id_empreendimento="+ng.userLogged.id_empreendimento)
		// 	.success(function(data, status, headers, config) {
		// 		ng.vendas = data;
		// 	})
		// 	.error(function(data, status, headers, config) {

		// 	});
	}

	ng.loadView= function(id_venda) {
		aj.get(baseUrlApi()+"venda/itens/"+id_venda)
			.success(function(data, status, headers, config) {
				ng.detalhes = data;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.view = function(id_venda){
		ng.id_venda = id_venda ;
		ng.loadView(id_venda);
		$('#list_clientes').modal('show');
	}

	ng.loadCliente = function() {
		aj.get(baseUrlApi()+"usuarios?usu->id="+params.id_cliente)
			.success(function(data, status, headers, config) {
				ng.cliente = data.usuarios[0];
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.loadDepositos = function() {
		aj.get(baseUrlApi()+"depositos?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.depositos = data.depositos;
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	ng.reset();
	ng.loadVendas(0,10);
	ng.loadCliente();
});
