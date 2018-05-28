app.controller('relPagamentosController', function($scope, $http, $window, $dialogs, UserService, FuncionalidadeService, EmpreendimentoService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 						= baseUrl();
	ng.userLogged 					= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
    ng.contas    					= [];
    ng.paginacao           			= {conta:null} ;
    ng.busca               			= {id_forma_pagamento:"",tipoData:"" } ;
    ng.busca_aux               		= {id_forma_pagamento:"",tipoData:"", cliente: ""} ;
    ng.conta                        = {} ;
    ng.movimentacao 				= {};
    ng.movimentacoes 				= null;
    ng.all_selected = false;

    var params = getUrlVars();

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
});
