app.controller('relPagamentosController', function($scope, $http, $window, $dialogs, UserService, ConfigService, FuncionalidadeService, EmpreendimentoService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 						= baseUrl();
	ng.userLogged 					= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.configuracoes	 		= ConfigService.getConfig(ng.userLogged.id_empreendimento);
    ng.contas    					= [];
    ng.paginacao           			= {conta:null} ;
    ng.busca               			= {id_forma_pagamento:"",tipoData:"" } ;
    ng.busca_aux               		= {id_forma_pagamento:"",tipoData:"", cliente: ""} ;
    ng.conta                        = {} ;
    ng.movimentacao 				= {};
    ng.movimentacoes 				= null;
    ng.all_selected = false;
    ng.transferencia = {
    	option_selected: 0,
    	id_fornecedor: ng.configuracoes.id_fornecedor_movimentacao_caixa,
		id_cliente: ng.configuracoes.id_cliente_movimentacao_caixa,
		id_empreendimento: ng.userLogged.id_empreendimento
    }

    var params = getUrlVars();

    ng.showModalTransferencia = function(){
    	ng.msg_error = null;

    	$('#modalTransferencia').modal('show');
		
		var selectedItems = _.where(ng.movimentacoes, {selected: true});

		ng.totais_transferencia = {
			vlr_total_selected : 0,
			vlr_total_taxa_maquineta_selected : 0,
			vlr_total_desconto_selected : 0
		};

		angular.forEach(selectedItems, function(item, index){
			ng.totais_transferencia.vlr_total_selected += item.valor_pagamento;
			ng.totais_transferencia.vlr_total_taxa_maquineta_selected += item.vlr_taxa_maquineta;
			ng.totais_transferencia.vlr_total_desconto_selected += item.valor_desconto_maquineta;
		});
    }

    ng.salvarTransferencia = function(){
    	ng.transferencia.dta_transferencia = (!empty($("#dta_transferencia").val())) ? formatDate($("#dta_transferencia").val()) : "";
    	if (empty(ng.transferencia.dta_transferencia)) {
			ng.msg_error = "Selecione uma data";
			return false;
		} else if(empty(ng.transferencia.id_conta_bancaria_origem)) {
			ng.msg_error = "Selecione uma conta de origem";
			return false;
		} else if(empty(ng.transferencia.id_conta_bancaria_destino)) {
			ng.msg_error = "Selecione uma conta de destino";
			return false;
		} else if(ng.transferencia.id_conta_bancaria_destino == ng.transferencia.id_conta_bancaria_origem) {
			ng.msg_error = "Não é possível fazer uma transferência para a mesma conta";
			return false;
		} else if(empty(ng.transferencia.option_selected, true)) {
			ng.msg_error = "Selecione o que deseja transferir";
			return false;
		}

		if (!empty(ng.transferencia.option_selected, true) && ng.transferencia.option_selected == 0)
			ng.transferencia.vlr_transferencia = ng.totais_transferencia.vlr_total_selected;

		else if (!empty(ng.transferencia.option_selected, true) && ng.transferencia.option_selected == 1)
			ng.transferencia.vlr_transferencia = ng.totais_transferencia.vlr_total_desconto_selected;
		
		aj.post(baseUrlApi()+"lancamento/transferencia", ng.transferencia)
			.success(function(data, status, headers, config) {
				ng.msg_error = "Transferência atualizado com sucesso";

				ng.transferencia.dta_transferencia = "";
				ng.transferencia.id_conta_bancaria_origem = null;
				ng.transferencia.id_conta_bancaria_destino = null;
				ng.transferencia.vlr_transferencia = 0;
				ng.transferencia.obs_transferencia = "";
				ng.transferencia.id_fornecedor = ng.configuracoes.id_fornecedor_movimentacao_caixa;
				ng.transferencia.id_cliente = ng.configuracoes.id_cliente_movimentacao_caixa;
				ng.transferencia.id_empreendimento = ng.userLogged.id_empreendimento;

				$('#modalTransferencia').modal('hide');
				
				ng.loadMovimentacoes();
			})
			.error(function(data, status, headers, config){
				ng.msg_error = "Erro ao lançar transferência";	
			});
    }

    ng.loadContas = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.contas = [];

		aj.get(baseUrlApi()+"contas_bancarias?id_empreendimento="+ng.userLogged.id_empreendimento+"&id_tipo_conta[exp]=<>5")
			.success(function(data, status, headers, config) {
				ng.contas = data.contas;
			})
			.error(function(data, status, headers, config) {

			});
	}

    ng.selectAllItens = function(){
    	angular.forEach(ng.movimentacoes, function(item, index){
    		item.selected = !item.selected;
    	});
    }

    ng.showUpdateSelectedRecords = function() {
    	var can_show = false;
    	angular.forEach(ng.movimentacoes, function(item, index){
    		if(item.selected)
    			can_show = true;
    	});
    	return can_show;
    }

    ng.confirmUpdateSelectedRecords = function(){

    	dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja dar baixa em todos os lançamentos marcados?</strong>');

		dlg.result.then(function(btn){
    		var selectedItems = _.where(ng.movimentacoes, {selected: true});
			aj.post(baseUrlApi()+"financeiro/receita/update/status", {data: JSON.stringify(selectedItems)})
				.success(function(data, status, headers, config) {
					ng.loadMovimentacoes();
				})
				.error(function(data, status, headers, config) {

	 			});
		}, undefined);
    }

    ng.loadBandeiras = function() {
		ng.bandeiras = [{id:'', nome: ''}];
		aj.get(baseUrlApi()+"bandeiras")
			.success(function(data, status, headers, config) {
				ng.bandeiras = data;
			});
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

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
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

	ng.total_desconto_taxa_maquineta = 0 ;
	ng.total_desconto_taxa_maquineta_debito = 0 ;
	ng.total_desconto_taxa_maquineta_credito = 0;

	ng.totais = { total: 0};
	ng.ccDetalhes = false;

	ng.resetFilter =function(){
		$("#dtaInicial").val('');
		$("#dtaFinal").val('');
		ng.busca.id_forma_pagamento = "";
		ng.busca_aux.cliente = "";
		ng.msg_error = null;
		ng.movimentacoes = null;
	}

	ng.loadMovimentacoes= function() {
		ng.msg_error = null;
		ng.movimentacoes = [] ;
		ng.totais = {total:0} ;
		ng.total_desconto_taxa_maquineta = 0 ;
		ng.total_desconto_taxa_maquineta_debito = 0 ;
		ng.total_desconto_taxa_maquineta_credito = 0;
		
		query_string = "?tpv->id_empreendimento="+ng.userLogged.id_empreendimento ;
		
		var dtaInicial =  empty($("#dtaInicial").val()) ? "" : formatDate($("#dtaInicial").val()) ;
		var dtaFinal   =  empty($("#dtaFinal").val())   ? "" : formatDate($("#dtaFinal").val()) ;
		var hraInicial  = empty($("#hraInicial").val()) ? '00:00:00' : $("#hraInicial").val();
		var hraFinal  = empty($("#hraFinal").val()) ? '23:59:59' : $("#hraFinal").val();

		ng.busca = angular.copy(ng.busca_aux);

		if(ng.busca.tipoData == "lan"){
			query_string += "&tpv->id_parcelamento[exp]=IS NULL";
			query_string += !empty(dtaInicial) && empty(dtaFinal)  ?  "&date_format(tcpv->dta_pagamento,'%Y-%m-%d')[exp]=>='"+dtaInicial+"'" : ""  ;
			query_string += !empty(dtaFinal) && empty(dtaInicial)  ?  "&date_format(tcpv->dta_pagamento,'%Y-%m-%d')[exp]=<='"+dtaFinal+"'" : ""  ;
			query_string += !empty(dtaInicial) && !empty(dtaFinal)  ? "&"+$.param({'tcpv->dta_pagamento':{exp:"between '"+dtaInicial+" "+ hraInicial +"' AND '"+dtaFinal+" "+ hraFinal +"'"}}) : ""  ;
		}else if(ng.busca.tipoData == "pag"){
			query_string += !empty(dtaInicial) && empty(dtaFinal)  ?  "&date_format(tpv->data_pagamento,'%Y-%m-%d')[exp]=>='"+dtaInicial+"'" : ""  ;
			query_string += !empty(dtaFinal) && empty(dtaInicial)  ?  "&date_format(tpv->data_pagamento,'%Y-%m-%d')[exp]=<='"+dtaFinal+"'" : ""  ;
			query_string += !empty(dtaInicial) && !empty(dtaFinal)  ? "&"+$.param({'tpv->data_pagamento':{exp:"between '"+dtaInicial+" "+ hraInicial +"' AND '"+dtaFinal+" "+ hraFinal +"'"}}) : ""  ;
		}else{
			query_string += !empty(dtaInicial) && empty(dtaFinal)  ?  "&(date_format(tcpv->dta_pagamento,'%Y-%m-%d')[exp]=>='"+dtaInicial+"' OR tpv.data_pagamento >='"+dtaInicial+"')" : ""  ;
			query_string += !empty(dtaFinal) && empty(dtaInicial)  ?  "&(date_format(tcpv->dta_pagamento,'%Y-%m-%d')[exp]=<='"+dtaFinal+"'  OR tpv.data_pagamento <='"+dtaFinal+"')" : ""  ;
			query_string += !empty(dtaInicial) && !empty(dtaFinal)  ? "&("+$.param({'tcpv->dta_pagamento':{exp:"between '"+dtaInicial+" "+ hraInicial +"' AND '"+dtaFinal+" "+ hraFinal +"' OR tpv.data_pagamento between '"+dtaInicial+" "+ hraInicial +"' AND '"+dtaFinal+" "+ hraFinal +"')"}}) : ""  ;
		}

		if(ng.busca.cliente != "")
				query_string += "&("+$.param({'tu->nome':{exp:"like '%"+ng.busca.cliente+"%' "}})+")";
		
		query_string += !empty(ng.busca.id_forma_pagamento) ? "&tpv->id_forma_pagamento="+ng.busca.id_forma_pagamento : ""  ;
		query_string += !empty(ng.busca.id_bandeira) ? "&tpv->id_bandeira="+ng.busca.id_bandeira : "";

		query_string += !empty(ng.busca.status_pagamento,false)  ?  "&tpv->status_pagamento="+ng.busca.status_pagamento : ""  ;

		aj.get(baseUrlApi()+"relatorio/pagamentos"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data,function(i,v){
						
						if(v.id_forma_pagamento == 5){
							data[i].vlr_taxa_maquineta           = (Math.round(v.valor_pagamento * 100) / 100) * v.taxa_maquineta;
							data[i].valor_desconto_maquineta     = (Math.round(v.valor_pagamento * 100) / 100) - data[i].vlr_taxa_maquineta ;
							ng.total_desconto_taxa_maquineta     += data[i].vlr_taxa_maquineta ;
							ng.total_desconto_taxa_maquineta_debito += data[i].vlr_taxa_maquineta ;
							ng.totais.total += v.valor_pagamento ;
						}else if(v.id_forma_pagamento == 6 ){
							if(ng.busca.tipoData == 'lan'){
								data[i].vlr_taxa_maquineta           = (Math.round((v.valor_pagamento * v.parcelas.length) * 100) / 100) * v.taxa_maquineta;
								data[i].valor_desconto_maquineta     = (Math.round((v.valor_pagamento * v.parcelas.length) * 100) / 100) - data[i].vlr_taxa_maquineta ;
								ng.total_desconto_taxa_maquineta     += data[i].vlr_taxa_maquineta ;
								ng.total_desconto_taxa_maquineta_credito += data[i].vlr_taxa_maquineta ;
								ng.totais.total += v.valor_pagamento * v.parcelas.length;
							}else{
								data[i].vlr_taxa_maquineta           = (Math.round((v.valor_pagamento) * 100) / 100) * v.taxa_maquineta;
								data[i].valor_desconto_maquineta     = (Math.round((v.valor_pagamento) * 100) / 100) - data[i].vlr_taxa_maquineta ;
								ng.total_desconto_taxa_maquineta     += data[i].vlr_taxa_maquineta ;
								ng.total_desconto_taxa_maquineta_credito += data[i].vlr_taxa_maquineta ;
								ng.totais.total += v.valor_pagamento;	
							}
							
						}else{
							data[i].vlr_taxa_maquineta           = (Math.round(v.valor_pagamento * 100) / 100) * v.taxa_maquineta;
							data[i].valor_desconto_maquineta     = (Math.round(v.valor_pagamento * 100) / 100) - data[i].vlr_taxa_maquineta ;
							ng.total_desconto_taxa_maquineta     += data[i].vlr_taxa_maquineta ;
							ng.totais.total += v.valor_pagamento ;
						}	
				});
				
				ng.movimentacoes = data;
			})
			.error(function(data, status, headers, config) {
				ng.movimentacoes = null;
				ng.status = status;
				ng.msg_error = data;
	 	});
	}

	ng.changeDetalhesCC = function(status){
		ng.ccDetalhes = status ;
		
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

	ng.loadBandeiras();
	ng.loadContas();
});
