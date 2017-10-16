app.controller('ZeraEstoqueController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope,
		aj = $http;

	ng.baseUrl 				= baseUrl();
	ng.userLogged 			= UserService.getUserLogado();

	ng.itensPorPagina 		= {};
	ng.itensPorPaginaArr 	= [
		{ value: 0,  label:"Todos" },
		{ value: 10, label:10 },
		{ value: 30, label:30 },
		{ value: 50, label:50 }
	];

	ng.depositos 			= [];
	ng.fabricantes 			= [];
	ng.itens 				= [];
	ng.paginacao 			= {};
	ng.deposito 			= {};
	ng.fabricante 			= {};

	ng.zerado 				= false;
	ng.allSelected 			= false;
	ng.hasSelected 			= false;

	ng.makeDefaultValues = function() {
		ng.itensPorPagina 	= ng.itensPorPaginaArr[1];
		ng.deposito 		= {
			id: 0,
			nme_deposito: "N/A",
			id_empreendimento: ng.userLogged.id_empreendimento.toString()
		};
		ng.fabricante 		= {
			id: 0,
			nome_fabricante: "N/A",
			id_empreendimento: ng.userLogged.id_empreendimento.toString()
		};
	}

	ng.reset = function() {
		ng.itens 		= [];
		ng.paginacao 	= {};
		$(".has-error").removeClass("has-error");
	}

	ng.resetFilter = function() {
		ng.reset();
		ng.itensPorPagina 	= ng.itensPorPaginaArr[1];
		ng.deposito 		= ng.depositos[0];
		ng.fabricante 		= ng.fabricantes[0];
		ng.fabricante 		= ng.fabricantes[0];
		ng.loadProdutos(0);
	}

	ng.aplicarFiltro = function(offset,limit) {
		offset = offset == null ? 0 : offset;
		limit = limit == null ? ng.itensPorPagina : limit;

		ng.reset();
		ng.loadProdutos(offset);
	}

	ng.selectAll = function() {
		$.each(ng.itens, function(i, item) {
			item.excluir = true;
		});

		ng.allSelected = true;

		validateSelectedExcluir();
	}

	ng.unselectAll = function() {
		$.each(ng.itens, function(i, item) {
			item.excluir = false;
		});

		ng.allSelected = false;

		validateSelectedExcluir();
	}

	ng.selectItemExcluir = function(i, item) {
		//item.excluir = !item.excluir;
		validateSelectedExcluir();
	}

	ng.loadDepositos = function() {
		aj.get(baseUrlApi()+"depositos?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.depositos.push(ng.deposito);

				$.each(data.depositos, function(i, item){
					ng.depositos.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	ng.loadFabricantes = function() {
		aj.get(baseUrlApi()+"fabricantes?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.fabricantes.push(ng.fabricante);

				$.each(data.fabricantes, function(i, item){
					ng.fabricantes.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	ng.reset = function(){
		ng.Devolucoes = {itens:[]};
	}

	ng.busca = {produto: ""};

	ng.resetFilter = function() {
		ng.busca.produto = "" ;
		ng.reset();
		ng.loadProdutos(0,10);
	}

	ng.loadProdutos = function(offset) {
		offset = offset == null ? 0 : offset;

		var query_string = "";

		if(ng.itensPorPagina.value > 0)
			query_string += "/" + offset + "/" + ng.itensPorPagina.value;

		query_string += "?";

		if(ng.deposito != null && ng.deposito.id > 0)
			query_string += "&dep->id="+ng.deposito.id;

		if(ng.fabricante != null && ng.fabricante.id > 0)
			query_string += "&fab->id="+ng.fabricante.id;

		if(ng.busca.produto != ""){
			query_string += "&("+$.param({'pro->nome':{exp:"like'%"+ng.busca.produto+"%')"}});
		}

		aj.get(baseUrlApi()+"estoque/deposito/"+ ng.userLogged.id_empreendimento + query_string)
			.success(function(data, status, headers, config) {
				if(ng.itensPorPagina.value > 0) {
					ng.itens = transformData(data.produtos);
					ng.paginacao.itens = data.paginacao;
				}
				else {
					if(typeof data != "string")
						ng.itens = transformData(data);
				}

				ng.zerado = (ng.itens.length == 0);
				ng.hasSelected = false;
				ng.allSelected = false;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.itens = [];

				ng.zerado = (ng.itens.length == 0);
				ng.hasSelected = false;
			});
	}

	ng.deleteSelected = function() {
		$("#btExcluirSelecionados").button("loading");

		$(".text-delete").text("");
		$(".text-ok").text("");

		if(!$(".span-ok").hasClass("hide"))
			$(".span-ok").addClass("hide");

		if(!$(".span-delete").hasClass("hide"))
			$(".span-delete").addClass("hide");

		var selectedItems = _.where(ng.itens, {excluir: true});

		var formData = [];

		$.each(selectedItems, function(i, item) {
			formData.push({ id_estoque_produto : item.id_estoque_produto, id_produto : item.id, dta_validade : item.dta_validade, id_deposito : item.id_deposito, qtd_saida : item.qtd_item }) ;
		});

		aj.post(baseUrlApi()+"estoque/excluir", { id_usuario:ng.userLogged.id,id_empreendimento:ng.userLogged.id_empreendimento, produtos: formData }).
			success(function(data, status, headers, config) {
				ng.aplicarFiltro();

				if(status == 200) {
					$(".text-ok").text(data);
					$(".span-ok").removeClass("hide");
				}

				$("#btExcluirSelecionados").button("reset");
			}).
			error(function(data, status, headers, config) {
				if(status == 406) {
					$(".text-delete").text(data.id_estoque_produto);
				} else {
					$(".text-delete").text(data);
				}

				$(".span-delete").removeClass("hide");
				$("#btExcluirSelecionados").button("reset");
			});
	}

	function transformData(data) {
		$.each(data, function(i, item) {
			item.excluir = false;
		});

		return data;
	}

	function validateSelectedExcluir() {
		$.each(ng.itens, function(i, item){
			ng.hasSelected = (item.excluir == true);
			if(ng.hasSelected === true)
				return false;
		});
	}

	ng.makeDefaultValues();
	ng.loadDepositos();
	ng.loadFabricantes();
	ng.loadProdutos(0);
});
