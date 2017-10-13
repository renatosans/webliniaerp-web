
app.controller('PagamentoFornecedorController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			= baseUrl();

	ng.userLogged 		= UserService.getUserLogado();
	ng.empreendimento 	= {};
    ng.empreendimentos 	= [];

    ng.pagamento        = {};
    ng.formas_pagamento = [
    						{nome:"Cheque",id:2},
    						{nome:"Dinheiro",id:3},
    						{nome:"Boleto Bancário",id:4},
    						{nome:"Cartão de Débito",id:5},
    						{nome:"Cartão de Crédito",id:6},
    					  ]

    ng.editing 			= false;

    ng.showBoxNovo = function(onlyShow){
    	ng.editing = !ng.editing;

    	if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show();
		}
		else {
			$('#box-novo').toggle(400, function(){
				if($(this).is(':visible')){
					$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
				}else{
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
			});
		}
	}

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.empreendimento = {};
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.load = function() {
		aj.get(baseUrlApi()+"fornecedores_pagamentos")
			.success(function(data, status, headers, config) {
				ng.pagamentos = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.pagamentos = [];
			});
	}

	ng.salvar = function() {
		ng.pagamento.id_empreendimento = ng.userLogged.id_empreendimento;
		ng.pagamento.data_pagamento    = $("#pagamentoData").val();
		console.log(ng.pagamento);

		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");

		aj.post(baseUrlApi()+"fornecedor_pagamento", ng.pagamento)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>Pagamento salvo com sucesso!</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.load();
			})
			.error(function(data, status, headers, config) {
				if(status == 406) {

					var errors = data;

					$.each(errors, function(i, item) {
						$("#"+i).addClass("has-error");

						var formControl = $($("#"+i).find("button")[0])
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();

						var formControl = $($("#"+i).find(".form-control")[0])
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});

				}
			});

	}

	ng.editar = function(item) {
		ng.empreendimento = angular.copy(item);
		ng.showBoxNovo(true);
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este empreendimento?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"empreendimento/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Empreendimento excluido com sucesso</strong>');
					ng.reset();
					ng.load();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	ng.addFornecedor = function(item){
    	ng.pagamento.nome_fornecedor = item.nome_fornecedor;
    	ng.pagamento.id_fornecedor = item.id;
    	$("#list_fornecedores").modal("hide");
	}

	ng.selFornecedor = function(){
			ng.loadFornecedores();
			$("#list_fornecedores").modal("show");
	}

	ng.loadFornecedores = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;
		ng.fornecedores = [];
		aj.get(baseUrlApi()+"fornecedores?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.fornecedores = data.fornecedores;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.reset = function() {
		ng.pagamento = {} ;
		$("#pagamentoData").val('');
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.load();
});
