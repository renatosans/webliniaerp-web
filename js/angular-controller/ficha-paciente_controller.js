app.controller('FichaPacienteController', function($scope, $http, $window, $dialogs, UserService, ConfigService){

	var ng = $scope ,
		aj = $http;
	$scope.userLogged = UserService.getUserLogado();
	ng.configuracoes = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	var params      = getUrlVars();
	ng.fichaPaciente = null ;

	ng.mesclarDados = function(procedimentos,pagamentos){
		dados = [] ;
		if(procedimentos.length >= pagamentos.length ){
			$.each(procedimentos,function(i,x){
				var item = {dta_venda : null,cod_dente : null,cod_procedimento : null,nome_profissional : null,valor_real_item : null,data_pagamento : null,descricao_forma_pagamento : null,valor_pagamento : null}
				item.dta_venda 			= x.dta_venda;
				item.cod_dente 			= x.cod_dente;
				item.cod_procedimento 	= x.cod_procedimento ;
				item.nome_profissional 	= x.nome_profissional ;
				item.valor_real_item   	= x.valor_real_item ;

				item.dta_entrada 		    = (typeof  pagamentos[i] == 'object') ?  pagamentos[i].dta_entrada 		: null ;
				item.descricao_forma_pagamento 	= (typeof  pagamentos[i] == 'object') ?  pagamentos[i].descricao_forma_pagamento 	: null ;
				item.data_pagamento 		    = (typeof  pagamentos[i] == 'object') ?  pagamentos[i].data_pagamento 		: null ;
				item.valor_pagamento 		    = (typeof  pagamentos[i] == 'object') ?  pagamentos[i].valor_pagamento 		: null ;
				item.id_forma_pagamento 		= (typeof  pagamentos[i] == 'object') ?  pagamentos[i].id_forma_pagamento 		: null ;
				item.num_parcelas 				= (typeof  pagamentos[i] == 'object') ?  pagamentos[i].num_parcelas 		: null ;
	
				dados.push(item);
			});
		}else{
			$.each(pagamentos,function(i,x){
				var item = {dta_venda : null,cod_dente : null,cod_procedimento : null,nome_profissional : null,valor_real_item : null,data_pagamento : null,descricao_forma_pagamento : null,valor_pagamento : null}
				item.dta_venda 			= (typeof  procedimentos[i] == 'object') ? procedimentos[i].dta_venda : null;
				item.cod_dente 			= (typeof  procedimentos[i] == 'object') ? procedimentos[i].cod_dente : null;
				item.cod_procedimento 	= (typeof  procedimentos[i] == 'object') ? procedimentos[i].cod_procedimento : null ;
				item.nome_profissional 	= (typeof  procedimentos[i] == 'object') ? procedimentos[i].nome_profissional : null ;
				item.valor_real_item    = (typeof  procedimentos[i] == 'object') ? procedimentos[i].valor_real_item : null ;

				item.dta_entrada 		 =  x.dta_entrada 	 ;
				item.descricao_forma_pagamento =  x.descricao_forma_pagamento ;
				item.data_pagamento 	 =  x.data_pagamento 	 ;
				item.valor_pagamento 	 =  x.valor_pagamento 	 ;
				item.id_forma_pagamento  =  x.id_forma_pagamento ;
				item.num_parcelas  		 =  x.num_parcelas ;
				dados.push(item);
			});
		}

		if(dados.length < 28){
			var count = dados.length ;
			for(count;count < 28;count++){
				var item = {dta_venda : null,cod_dente : null,cod_procedimento : null,nome_profissional : null,valor_real_item : null,data_pagamento : null,descricao_forma_pagamento : null,valor_pagamento : null}
				dados.push(item);
			}
		}

		ng.fichaPaciente = dados ;
		ng.totalizar();
	}

	ng.procedimentosPaciente = [];
    ng.getProcedimentosPaciente = function(){
    	ng.procedimentosPaciente = null;
    	if( !(typeof params.id_paciente == "undefined") ){
	   		aj.get(baseUrlApi()+"clinica/paciente/"+ params.id_paciente +"/procedimentos")
				.success(function(data, status, headers, config) {
					ng.procedimentosPaciente = data;
						aj.get(baseUrlApi()+"pagamentos/cliente/"+params.id_paciente)
							.success(function(data, status, headers, config) {
								ng.PagamentosPaciente = data.pagamentos;
								ng.mesclarDados(ng.procedimentosPaciente,ng.PagamentosPaciente );
								//totalizar();
							})
							.error(function(data, status, headers, config) {
								if(status == 404)
									ng.procedimentosPaciente = [];
						});
				})
				.error(function(data, status, headers, config) {
					if(status == 404)
						ng.procedimentosPaciente = [];
			});
		}
	}

	ng.paciente = null;
	ng.getDadosPaciente = function(){
		if( !(typeof params.id_paciente == "undefined") ){
			aj.get(baseUrlApi()+"usuario/"+ng.userLogged.id_empreendimento+"/"+params.id_paciente)
	        	.success(function(data, status, headers, config) {
		        	ng.paciente	= data ;
	        		ng.loadCidadesByEstado();
	        	});
        }
	}

	ng.loadCidadesByEstado = function(nome_cidade) {
		ng.cidades = [];

		aj.get(baseUrlApi()+"cidades/"+ ng.paciente.id_estado)
		.success(function(data, status, headers, config) {
			ng.cidades = data;
			if(nome_cidade != null){
				$.each(ng.cidades,function(i, item){
					if(removerAcentos(nome_cidade) == removerAcentos(item.nome)){
						ng.paciente.id_cidade = item.id;
						return false;
					}
				});
			}
		});
	}

	ng.vlrTotalProcedimentos = 0;
	ng.vlrTotalPagamentos = 0;
	ng.totalizar = function (){
		ng.vlrTotalProcedimentos = 0 ;
		$.each(ng.fichaPaciente,function(index, item){
			if($.isNumeric(item.valor_real_item))
			ng.vlrTotalProcedimentos += Number(item.valor_real_item);

			if($.isNumeric(item.valor_pagamento))
				ng.vlrTotalPagamentos += Number(item.valor_pagamento);
		});
	}

	ng.getDadosPaciente();
	//ng.getProcedimentosPaciente();
	$('#loading-relatorio').modal('show');
	$('#iframe-ficha-paciente').html('<iframe id="iframe-ficha-paciente"  src="'+baseUrlApi()+'relPDF?template=ficha_paciente&dados[id_paciente]='+params.id_paciente+'&dados[id_empreendimento]='+ng.userLogged.id_empreendimento+'&dados[id_especialidade]='+params.id_especialidade+'&papel=A4-L" frameborder=0 allowTransparency="true"  style=" width: 100%;height: 900px;background: #fff;border: none;overflow: hidden; display:none"></iframe>')
	$('#iframe-ficha-paciente iframe').load(function(){
        $(this).show();
       	$('#loading-relatorio').modal('hide');
    });
});