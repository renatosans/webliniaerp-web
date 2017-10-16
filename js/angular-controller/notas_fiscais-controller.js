app.controller('NotasFiscaisController', function($scope, $http, $window, $dialogs, UserService,ConfigService,$timeout,NFService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			= baseUrl();
	ng.userLogged 		= UserService.getUserLogado();
	ng.configuracoes 	= ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.notas 			= null;
	ng.paginacao 		= {};
	
	ng.reset = function(){
		ng.Notas = {itens:[]};
	}

	ng.busca = { text: "", numeroo: "" };
	ng.resetFilter = function() {
		$("#inputDtaEmissao").val("");
		ng.busca.text = "" ;
		ng.busca.numeroo = "" ;
		ng.reset();
		ng.loadNotas(0,10);
	}

	ng.loadNotas = function(offset,limit) {
		ng.notas = [];
		var query_string = "?cod_empreendimento="+ ng.userLogged.id_empreendimento;

		if(ng.busca.nome != ""){
			query_string += "&("+$.param({nome_destinatario:{exp:"like'%"+ng.busca.text+"%')"}});
		}

		if(ng.busca.numeroo != ""){
			query_string += "&("+$.param({numero:{exp:"like'%"+ng.busca.numeroo+"%')"}});
		}

		if($("#inputDtaEmissao").val() != ""){
			var data_emissao = moment($("#inputDtaEmissao").val(), 'DD/MM/YYYY').format('YYYY-MM-DD');

			query_string += "&("+$.param({'2':{exp:"=2 AND cast(data_emissao as date) = '"+ data_emissao +"' )"}});
		}

		aj.get(baseUrlApi()+"notas/"+ offset +"/"+ limit + query_string)
			.success(function(data, status, headers, config) {
				ng.notas 			= data.notas;
				ng.paginacao.notas 	= data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.notas = null;
			});
	}

	ng.atualzarStatus = function(cod_nota_fiscal,index,event){
		if(!empty(event)){
		var element = $(event.target);
		event.stopPropagation();
			if(!element.is('a'))
				element = $(event.target).parent();
			element.button('loading');
		}	

		var url = "";
		if(!empty(ng.notas[index].cod_ordem_servico))
			url = baseUrlApi()+"nfse/"+ cod_nota_fiscal +"/atualizar/status/"+ ng.userLogged.id_empreendimento;
		else
			url = baseUrlApi()+"nota_fiscal/"+ cod_nota_fiscal +"/"+ ng.userLogged.id_empreendimento +"/atualizar/status";

		aj.get(url)
			.success(function(data, status, headers, config) {
				if(!empty(event)){
					element.html('<i class="fa fa-check-circle-o"></i> Atualizado');
					if(!(ng.notas[index].status == data.status))
						ng.notas[index] = data ;
					$timeout(function(){
						element.html('<i class="fa fa-refresh"></i> Atualizar Status');
					}, 2000);	
				}else{
					if(!(ng.notas[index].status == data.status))
						ng.notas[index] = data ;
				}
			})
			.error(function(data, status, headers, config) {
				if(!empty(event)){
					element.html('<i class="fa fa-times-circle"></i> Erro ao atualizar');
					nota = data;
					$timeout(function(){
						element.html('<i class="fa fa-refresh"></i> Atualizar Status');
					}, 2000);	
				}
		});

	}

	ng.showDANFEModal = function(nota){
		eModal.setEModalOptions({
			loadingHtml: '<div><div style="text-align: center;margin-top:5px;margin-bottom:3px"><span class="fa fa-circle-o-notch fa-spin fa-3x text-primary"></span></div><div style="text-align: center;"><span class="h4">Carregando, aguarde...</span></div></div>'
		});
        eModal
            .iframe({
            	message: nota.caminho_danfe, 
            	title: 'DANFE NF-e Nº '+ nota.numero, 
            	size: 'lg'
            })
            .then(function(){
            	t8.success('iFrame loaded!!!!', title)
        	});
	}

	ng.notaCancelar = null ;
	ng.modalCancelar = function(item,index){
		aj.get(baseUrlApi()+"nota_fiscal/?cod_empreendimento="+ng.userLogged.id_empreendimento+"&cod_venda="+item.cod_venda)
			.success(function(data, status, headers, config) {
				data.dados_emissao.data_emissao = formatDateBR(data.dados_emissao.data_emissao);
				ng.notaCancelar = data ;
				ng.notaCancelar.dados_emissao.chave_nfe = item.chave_nfe ;
				ng.notaCancelar.dados_emissao.valor_total = item.valor_total ;
				ng.notaCancelar.dados_emissao.id_ref = item.cod_nota_fiscal
				ng.notaCancelar.index = index ;
				
				$('#modal-cencelar-nota').modal('show');
			})
			.error(function(data, status, headers, config) {
				ng.notaCancelar = [] ;
		});
	}

	ng.cacelarNfe = function(){
		if(ng.configuracoes.flg_ambiente_nfe == 0){
			var server = 'http://homologacao.acrasnfe.acras.com.br/';
			var token  =  ng.configuracoes.token_focus_homologacao ;
		}

		var btn = $('#btn-cancelar-nota');
		btn.button('loading');

		aj.get(baseUrlApi()+'nota_fiscal/cancelar/'+ng.notaCancelar.dados_emissao.id_ref+'/'+ng.notaCancelar.justificativa+'/'+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				$('#modal-cencelar-nota').modal('hide');
				ng.mensagens('alert-success','<b>Pedido de Cancelamento enviado com sucesso</b>','.alert-list-notas');
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				$('#modal-cencelar-nota').modal('hide');
				ng.mensagens('alert-danger','<b>Erro ao cacelar nota</b>','.alert-list-notas');
				btn.button('reset');	
		});	
	}

	ng.flg_nova_correcao = false ;
	ng.showNovaCorrrecao = function(status){
		ng.flg_nova_correcao = status ;
	}

	ng.notaCorrigir = null ;
	ng.nota_correcoes = [] ;

	ng.modalCorrecao = function(item,index){
		ng.nota_correcoes = [] ;
		aj.get(baseUrlApi()+"nota_fiscal/?cod_empreendimento="+ng.userLogged.id_empreendimento+"&cod_venda="+item.cod_venda)
			.success(function(data, status, headers, config) {
				data.dados_emissao.data_emissao = formatDateBR(data.dados_emissao.data_emissao);
				ng.notaCorrigir = data ;
				ng.notaCorrigir.dados_emissao.chave_nfe = item.chave_nfe ;
				ng.notaCorrigir.dados_emissao.valor_total = item.valor_total ;
				ng.notaCorrigir.dados_emissao.id_ref = item.cod_nota_fiscal
				ng.notaCorrigir.index = index ;
				$('#modal-corrigir-nota').modal('show');
			})
			.error(function(data, status, headers, config) {
				ng.notaCorrigir = [] ;
		});
		aj.get(baseUrlApi()+"correcoes_nota_fiscal?cod_nota_fiscal="+item.cod_nota_fiscal)
			.success(function(data, status, headers, config) {
				ng.nota_correcoes = data ;
			})
			.error(function(data, status, headers, config) {
				ng.nota_correcoes = [] ;
		});
	}

	ng.atualizarCorrecao = function(item,index){
		var btn = $(event.target);
			if(!btn.is('button'))
				btn = $(event.target).parent();
		btn.button('loading');
		aj.get(baseUrlApi()+"correcao_nota_fiscal/atualizar/"+item.cod_nota_fiscal+"/"+item.id+"/"+item.numero_sequencial_evento+"/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, header =s, config) {
				btn.button('reset');
				ng.nota_correcoes[index] = data ;
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				alert('erro ao atualizar');
		});
	}

	ng.corrgirNfe = function(){
		var btn = $('#btn-corrigir-nota');
		btn.button('loading');

		aj.post(baseUrlApi()+'nota_fiscal/corrigir/'+ng.notaCorrigir.dados_emissao.id_ref+'/'+ng.userLogged.id_empreendimento, { correcao: ng.notaCorrigir.correcao })
			.success(function(data, status, headers, config) {
				$('#modal-cencelar-nota').modal('hide');
				ng.mensagens('alert-success','<b>Pedido de correção enviado com sucesso</b>','.alert-list-correcoes');
				btn.button('reset');
				ng.showNovaCorrrecao(false);
				ng.nota_correcoes = data ;
				ng.notaCorrigir.correcao = null ;
				aj.get(baseUrlApi()+"correcoes_nota_fiscal?cod_nota_fiscal="+ng.notaCorrigir.dados_emissao.id_ref)
				.success(function(data, status, headers, config) {
					ng.nota_correcoes = data ;
				})
				.error(function(data, status, headers, config) {
					ng.nota_correcoes = [] ;
		});
			})
			.error(function(data, status, headers, config) {
				$('#modal-cencelar-nota').modal('hide');
				ng.mensagens('alert-danger','<b>Erro ao corrigir nota</b>','.alert-correcao');
				btn.button('reset');	
		});	
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().removeClass('alert-success alert-danger alert-warning').addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.loadNotas(0,10);
});
