app.controller('relMovimentacaoCaixaController', function($scope, $http, $window, $dialogs, UserService, FuncionalidadeService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 						= baseUrl();
	ng.userLogged 					= UserService.getUserLogado();
    ng.contas    					= [];
    ng.paginacao           			= {conta:null} ;
    ng.busca               			= {empreendimento:""} ;
    ng.conta                        = {} ;
    ng.movimentacao 				= {};
    ng.movimentacoes 				= null;
    var params      = getUrlVars();
    
	ng.show_details = {
		cartao_credito: {
			show: false
		},
		cartao_debito: {
			show: false
		},
		voucher: {
			show: false
		}
	}

   ng.funcioalidadeAuthorized = function(cod_funcionalidade){
	return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
   }

    ng.showBoxNovo = function(onlyShow){
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
		$('.alert-sistema').removeClass("alert-danger");
		$('.alert-sistema').removeClass("alert-success");
		$('.alert-sistema').removeClass("alert-warning");
		$('.alert-sistema').removeClass("alert-info");
		$('.alert-sistema')
			.fadeIn()
			.addClass(classe)
			.html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function(show) {
		show = show == true ? true : false ;
		ng.conta = {};
		$('[name="perc_taxa_maquineta"]').val('');
		ng.empreendimentosAssociados = [];
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		if(show)
			ng.showBoxNovo();
	}

	ng.loadMovimentacao = function() {
		ng.movimentacao = {};
		aj.get(baseUrlApi()+"caixa/allAberturas?abt_caixa->id="+params['id'])
			.success(function(data, status, headers, config) {
				ng.movimentacao = data[0];
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.movimentacao = {};
	 	});
	}

	ng.total_desconto_taxa_maquineta 			= 0;
	ng.total_desconto_taxa_maquineta_debito 	= 0;
	ng.total_desconto_taxa_maquineta_credito 	= 0;
	ng.total_reforco_caixa 						= 0;
	ng.total_vendas								= 0;

	ng.loadMovimentacoes = function() {
		ng.movimentacoes = [];
		aj.get(baseUrlApi()+"caixa/movimentacoes/"+params['id'])
			.success(function(data, status, headers, config) {
				$.each(data,function(i,v){
					data[i].vlr_taxa_maquineta           = (Math.round(v.valor_entrada * 100) / 100) * v.taxa_maquineta;
					data[i].valor_desconto_maquineta     = (Math.round(v.valor_entrada * 100) / 100) - data[i].vlr_taxa_maquineta ;
					ng.total_desconto_taxa_maquineta     += data[i].vlr_taxa_maquineta ;
					if(v.id_forma_pagamento_entrada == 5){
						ng.total_desconto_taxa_maquineta_debito += data[i].vlr_taxa_maquineta ;
					}else if(v.id_forma_pagamento_entrada == 6 ){
						ng.total_desconto_taxa_maquineta_credito += data[i].vlr_taxa_maquineta ;
					}

					if(v.id_tipo_movimentacao == 1){
						ng.total_reforco_caixa += Number(v.valor_entrada) ;
					}

					if(!empty(v.id_venda)) {
						ng.total_vendas += Number(v.valor_entrada);
					}
				});
				ng.movimentacoes = data;
				ng.totais_forma_pagamento = _.groupBy(ng.movimentacoes, 'id_forma_pagamento_entrada');
				angular.forEach(ng.totais_forma_pagamento, function(forma_pagamento, id_forma_pagamento){
					switch(parseInt(id_forma_pagamento,10)) {
						case 5: // Cartão de Dédito
						case 6: // Cartão de Crédito
						case 10: // Voucher
							ng.totais_forma_pagamento[id_forma_pagamento].maquinetas = _.groupBy(forma_pagamento, 'num_serie_maquineta');
							angular.forEach(ng.totais_forma_pagamento[id_forma_pagamento].maquinetas, function(maquineta, id_maquineta){
								ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].vlr_total = 0;
								ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].vlr_total_maquineta = 0;
								ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].vlr_total_com_desconto = 0;
								angular.forEach(maquineta, function(movimentacao){
									ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].vlr_total += movimentacao.total_venda;
									ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].vlr_total_maquineta += movimentacao.vlr_taxa_maquineta;
									ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].vlr_total_com_desconto += movimentacao.valor_desconto_maquineta;
								});

								ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].bandeiras = _.groupBy(maquineta, 'nome_bandeira');
								angular.forEach(ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].bandeiras, function(bandeira, id_bandeira){
									ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].bandeiras[id_bandeira].prc_taxa_maquineta = bandeira[0].taxa_maquineta;
									ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].bandeiras[id_bandeira].vlr_total = 0;
									ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].bandeiras[id_bandeira].vlr_total_maquineta = 0;
									ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].bandeiras[id_bandeira].vlr_total_com_desconto = 0;

									angular.forEach(bandeira, function(movimentacao){
										ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].bandeiras[id_bandeira].vlr_total += movimentacao.total_venda;
										ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].bandeiras[id_bandeira].vlr_total_maquineta += movimentacao.vlr_taxa_maquineta;
										ng.totais_forma_pagamento[id_forma_pagamento].maquinetas[id_maquineta].bandeiras[id_bandeira].vlr_total_com_desconto += movimentacao.valor_desconto_maquineta;
									});
								});
							});
							break;
					}
				});
				angular.forEach(ng.totais.formas_pagamento, function(forma_pagamento, index){
					switch(index){
						case 'cartao_debito':
							if(!empty(ng.totais_forma_pagamento['5']))
								ng.totais.formas_pagamento[index].maquinetas = ng.totais_forma_pagamento['5'].maquinetas;
							break;
						case 'cartao_credito':
							if(!empty(ng.totais_forma_pagamento['6']))
								ng.totais.formas_pagamento[index].maquinetas = ng.totais_forma_pagamento['6'].maquinetas;
							break;
						case 'voucher':
							if(!empty(ng.totais_forma_pagamento['10']))
								ng.totais.formas_pagamento[index].maquinetas = ng.totais_forma_pagamento['10'].maquinetas;
							break;
					}
				});
				console.log(ng.totais.formas_pagamento);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.movimentacoes = null;
	 	});
	}

	ng.isEntrada = function(item){
		return item.tipo_movimentacao == 'Reforco' || item.tipo_movimentacao == 'Pagamento' || item.tipo_movimentacao == 'Venda' ;
	}

	ng.isSaida = function(item){
		return item.tipo_movimentacao == 'Sangria' ;
	}

	ng.salvar = function() {

		var url   = ng.editing ? "conta_bancaria/update" : "conta_bancaria";
		var conta = angular.copy(ng.conta);

		//conta.perc_taxa_maquineta = conta.perc_taxa_maquineta / 100 ;
		conta.id_empreendimento   = ng.userLogged.id_empreendimento;
		conta.id_tipo_conta       = 5 ;

		/*if(isNaN(conta.perc_taxa_maquineta))
			conta.perc_taxa_maquineta = 0 ;
		*/

		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error-plano")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		$($(".has-error-plano")).removeClass("has-error-plano");

		aj.post(baseUrlApi()+url, conta)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>Conta salva com sucesso!</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.loadContas();
			})
			.error(function(data, status, headers, config) {
				if(status == 406) {
					var errors = data;

					$.each(errors, function(i, item) {
						$("#"+i).addClass("has-error");

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
		ng.editing = true;
		item.perc_taxa_maquineta = item.perc_taxa_maquineta * 100 ;
		$('[name="perc_taxa_maquineta"]').val(numberFormat(item.perc_taxa_maquineta,'2',',','.'));
		ng.conta = angular.copy(item);
		if(!$('#box-novo').is(':visible')){
			ng.showBoxNovo();
		}
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir esta conta?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"conta_bancaria/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Conta excluida com sucesso</strong>','.alert-delete');
					ng.reset();
					ng.loadContas();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	ng.loadBancos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.bancos = [];

		aj.get(baseUrlApi()+"bancos")
			.success(function(data, status, headers, config) {
				ng.bancos = data.bancos;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.loadtipos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.tipos = [];

		aj.get(baseUrlApi()+"contas_bancarias/tipos")
			.success(function(data, status, headers, config) {
				ng.tipos = data.tipos;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.totais = [] ;

	ng.formasPagamento = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.tipos = [];

		aj.get(baseUrlApi()+"caixa/movimentacoes/total/"+params['id'])
			.success(function(data, status, headers, config) {
				ng.totais = data;
				ng.loadMovimentacoes();
			})
			.error(function(data, status, headers, config) {
				ng.totais = [];
			});
	}

	ng.getSaldoDinheiro = function() {
		if(!empty(ng.totais.formas_pagamento) && !empty(ng.totais.formas_pagamento.dinheiro)) {
			var vlr_dinheiro_pgto_vendas = ng.totais.formas_pagamento.dinheiro.valor - ng.total_reforco_caixa;
			var vlr_reforcos = ng.total_reforco_caixa;
			var vlr_sangrias = ng.totais.formas_pagamento.sangria.valor;
			return (vlr_dinheiro_pgto_vendas + vlr_reforcos) - vlr_sangrias;
		}
		else
			return 0;
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}


	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.loadMovimentacao();
	ng.formasPagamento();
});
