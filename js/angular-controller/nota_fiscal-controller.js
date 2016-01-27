app.controller('NotaFiscalController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
    ng.editing 		= false;
    ng.NF 			= {} ;
    ng.id_transportadora;
    var params      = getUrlVars();

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
	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}
	ng.calcularNfe = function(event) {
		if(event != null){
			var btn = $(event.target) ;
			if(!(btn.is(':button')))
				btn = $(btn.parent('button'));
			btn.button('loading');
		}else
			$('#modal-calculando').modal({ backdrop: 'static',keyboard: false});
		var post = { 
			id_empreendimento : ng.userLogged.id_empreendimento,
			id_venda          : params.id_venda,
			cod_operacao      : params.cod_operacao
		 } ;
		aj.post(baseUrlApi()+"nfe/calcular",post)
			.success(function(data, status, headers, config) {
				ng.NF = data;
				ng.NF.transportadora = {id:null} ;
				if(event != null){
					btn.button('reset');
					$('.tab-bar li a').eq(0).trigger('click');
				}else{
					$('#modal-calculando').modal('hide');
				}
			})
			.error(function(data, status, headers, config) {
				$dialogs.notify('Desculpe!','<strong>Ocorreu um erro ao calcular a NF.</strong>');
				$('#modal-calculando').modal('hide');
			});
	}

	ng.loadTransportadoras = function() {
		ng.lista_traportadoras = [{id:'',nome_fornecedor:''}];
		aj.get(baseUrlApi()+"fornecedores?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.lista_traportadoras  = ng.lista_traportadoras.concat(data.fornecedores);
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
				ng.lista_traportadoras = [] ;
			});
	}

	ng.lista_modalidade_frete = [] ;
	ng.loadControleNfe = function(ctr,key) {
		aj.get(baseUrlApi()+"nfe/controles/null/"+ctr)
			.success(function(data, status, headers, config) {
				ng[key] = [{ num_item : '', nme_item:'' }];
				ng[key] = ng[key].concat(data) ;
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
				ng[key] = [] ;	
		});
	}

	ng.selTransportadora = function(){
		var item ;
		$.each(ng.lista_traportadoras,function(i,v){
			if(Number(ng.NF.transportadora.id) == Number(v.id)){
				item = v ;
				return ;
			}
		});

		ng.NF.transportadora.xFant 					=  item.nme_fantasia;
		ng.NF.transportadora.CNPJ 					=  item.num_cnpj;
		ng.NF.transportadora.IE   					=  item.num_inscricao_estadual;
		ng.NF.transportadora.CEP 					=  item.num_cep;
		ng.NF.transportadora.nme_logradouro 		=  item.nme_endereco;
		ng.NF.transportadora.num_logradouro 		=  item.num_logradouro;
		ng.NF.transportadora.nme_bairro_logradouro 	=  item.nme_bairro;
		ng.NF.transportadora.estado 				=  ((typeof item.estado == 'object')  ? item.estado : null );
		ng.NF.transportadora.cidade 				=  ((typeof item.cidade == 'object')  ? item.cidade : null );
	}

	ng.sendNfe = function(){
		var btn = $(event.target) ;
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');

		var data_emissao 		= moment($('#inputDtaEmissao').val(),'DD/MM/YYYY H:m:s');
		var data_entrada_saida  = moment($('#inputDtaSaida').val() ,'DD/MM/YYYY H:m:s');
		
		var msg = "" ;
		var error = 0 ;	
		if(!data_emissao.isValid()){
			msg += "Informe a data de emissão<br/>";
			error ++ ;
		}
		if(!data_entrada_saida.isValid()){
			msg += "Informe a data de saida<br/>";
			error ++;
		}

		if(error > 0){
			btn.button('reset');
			$dialogs.error('<strong>'+msg+'</strong>');	
			return ;
		}

		ng.NF.dados_emissao.data_emissao  		= data_emissao.format();
		ng.NF.dados_emissao.data_entrada_saida  = data_entrada_saida.format();
		aj.post(baseUrlApi()+"nfe/send",ng.NF)
			.success(function(data, status, headers, config) {
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				var msg = "" ;
				if( typeof data != 'undefined'){
					$.each(data.erros,function(i,v){
						msg += v.mensagem+"<br/>";
					});
				}
				$dialogs.error('<strong>'+msg+'</strong>');
				$('#notifyModal h4').addClass('text-warning')
				btn.button('reset');		
		});
	}


	if($.isNumeric(params.id_venda) &&  $.isNumeric(params.cod_operacao) ){
		ng.calcularNfe();
		ng.loadTransportadoras();
		ng.loadControleNfe('modalidade_frete','lista_modalidade_frete');
		ng.loadControleNfe('tipo_documento','lista_tipo_documento');
		ng.loadControleNfe('forma_pagamento','lista_forma_pagamento');
		ng.loadControleNfe('presenca_comprador','lista_presenca_comprador');
	}else
		$dialogs.notify('Desculpe!','<strong>Não foi possível calcular a NF, os paramentros estão incorretos.</strong>');
					
	//ng.loadAllEmpreendimentos();
	//ng.loadDepositos();
});
