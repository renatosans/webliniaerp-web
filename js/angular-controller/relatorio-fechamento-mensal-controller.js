app.controller('RelatorioFechamentoMensalController', function($scope, $http, $window, UserService,ConfigService,FuncionalidadeService) {
	var ng = $scope,
		aj = $http;

	ng.userLogged = UserService.getUserLogado();
	ng.config     = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.dados;

	ng.qtdCompraTotal;
	ng.vlrCompraUnitario;
	ng.vlrCompraTotal;

	ng.qtdVendaTotal;
	ng.vlrVendaUnitario;
	ng.vlrVendaTotal;

	ng.saldoTotal;

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.funcioalidadeAuthorized = function(cod_funcionalidade){
    	return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
    }

	ng.reset = function() {
		ng.dados = [];

		ng.qtdCompraTotal = 0;
		ng.vlrCompraUnitario = 0;
		ng.vlrCompraTotal = 0;

		ng.qtdVendaTotal = 0;
		ng.vlrVendaUnitario = 0;
		ng.vlrVendaTotal = 0;

		ng.saldoTotal = 0;

		$("#dtaInicial").tooltip('destroy');
		$("#dtaFinal").tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.resetFilter = function() {
		$("#dtaInicial").val("");
		$("#dtaFinal").val("");
		ng.reset();
	}

	ng.aplicarFiltro = function() {
		ng.reset();

		var dtaInicial 	= $("#dtaInicial").val();
		var dtaFinal 	= $("#dtaFinal").val();

		if(dtaInicial == "") {
			$("#dtaInicialDiv").addClass("has-error");

			$("#dtaInicial")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Campo obrigatório!")
				.attr("data-original-title", "Campo obrigatório!");
			$("#dtaInicial").tooltip();

			return;
		}


		if(dtaFinal == "") {
			$("#dtaFinalDiv").addClass("has-error");

			$("#dtaFinal")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Campo obrigatório!")
				.attr("data-original-title", "Campo obrigatório!");
			$("#dtaFinal").tooltip();

			return;
		}

		dtaInicial 	= dtaInicial.split("/");
		dtaInicial 	= dtaInicial[2] + "-" + dtaInicial[1] + "-" + dtaInicial[0];
		dtaFinal 	= dtaFinal.split("/");
		dtaFinal 	= dtaFinal[2] + "-" + dtaFinal[1] + "-" + dtaFinal[0];

		$("#modal-loading").modal('show');

		var id_deposito = "";
		/*if(!ng.funcioalidadeAuthorized('buscar_venda_todos_depositos')){
			var id_deposito = "&id_deposito="+ng.config.id_deposito_padrao;
		}*/

		aj.get(baseUrlApi()+"relatorio/mensal/"+ng.userLogged.id_empreendimento+"?di="+ dtaInicial +"&df="+ dtaFinal + id_deposito)
			.success(function(data, status, headers, config) {
				ng.reset();

				$.each(data, function(i, item){
					item.qtd_total_compra 	= parseFloat(item.qtd_total_compra, 2);
					item.vlr_media_compra 	= parseFloat(item.vlr_media_compra, 2);
					item.vlr_total_compra 	= parseFloat(item.vlr_total_compra, 2);
					item.qtd_total_venda 	= parseFloat(item.qtd_total_venda, 2);
					item.vlr_media_venda 	= parseFloat(item.vlr_media_venda, 2);
					item.vlr_total_venda 	= parseFloat(item.vlr_total_venda, 2);
					item.vlr_percent 		= parseFloat(item.vlr_percent, 2);
					item.vlr_saldo 			= parseFloat(item.vlr_saldo, 2);

					ng.qtdCompraTotal 		+= item.qtd_total_compra;
					ng.vlrCompraUnitario 	+= item.vlr_media_compra;
					ng.vlrCompraTotal 		+= item.vlr_total_compra;

					ng.qtdVendaTotal		+= item.qtd_total_venda;
					ng.vlrVendaUnitario		+= item.vlr_media_venda;
					ng.vlrVendaTotal		+= item.vlr_total_venda;

					ng.saldoTotal			+= item.vlr_saldo;

					ng.dados.push(item);

					$(".modal").modal('hide');
				});
			})
			.error(function(data, status, headers, config) {
				$(".modal").modal('hide');
				ng.reset();
			});
	}

	ng.reset();
});
