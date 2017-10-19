app.controller('Empreendimento_config-Controller', function($scope, $http, $window, $dialogs, UserService,ConfigService){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		 = baseUrl();
	ng.userLogged 	 = UserService.getUserLogado();
	ng.cfg  		 = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.currentNode 	 = null;
	ng.exists_cookie = null ; 
    serieDocumentoFiscalTO = {
		cod_empreendimento : ng.userLogged.id_empreendimento,
		serie_documento_fiscal : '',
		num_modelo_documento_fiscal : '',
		num_ultimo_documento_fiscal : '',
    }
    ng.serie_documento_fiscal = angular.copy(serieDocumentoFiscalTO) ;
    ng.lista_serie_documento_fiscal = []; 
    ng.edit_serie_documento_fiscal = false ;
    ng.notEmails = [] ;
    ng.valoresChinelos = { 
    	infantil: { tamanhos: { de: null , ate:null }, faixas: [/*{ de:null,ate:null,valor:null }*/] }, 
    	adulto: { tamanhos: { de: null , ate:null }, faixas: [/*{ de:null,ate:null,valor:null }*/] },
    	adicionais:{cor_adicional:null,chinelo_quadrado:null,acima_41:null}
    };
    ng.impressoras                  = [
    	{ value: null					, dsc:'Selecione' 			},
    	{ value:'bematech_mp_2500_th'	, dsc:'BEMATECH MP-2500 TH' },
    	{ value:'bematech_mp_4200_th'	, dsc:'BEMATECH MP-4200 TH' },
    	{ value:'epson_tm_t20'			, dsc:'EPSON TM T20' 		}
	];

	ng.status_venda = [
		{
			"id": 1,
			"dsc_status": "Pedido Recebido"
		},
		{
			"id": 2,
			"dsc_status": "Mercadoria Disponível"
		},
		{
			"id": 3,
			"dsc_status": "Em Transporte"
		},
		{
			"id": 4,
			"dsc_status": "Entregue/Finalizado"
		},
		{
			"id": 5,
			"dsc_status": "Pendente"
		},
		{
			"id": 6,
			"dsc_status": "Aprovada"
		},
		{
			"id": 7,
			"dsc_status": "Cancelada"
		}
	];

	ng.segmentos = [
		{'label':'Beleza', 		'value':'Beleza'},
		{'label':'Bijouterias', 'value':'Bijouterias'},
		{'label':'Clinica',		'value':'Clinica'},
		{'label':'Confecção', 	'value':'Confecção'},
		{'label':'Engenharia', 	'value':'Engenharia'},
		{'label':'Especiarias', 'value':'Especiarias'},
		{'label':'Indústria', 	'value':'Indústria'},
		{'label':'Saúde', 		'value':'Saúde'},
		{'label':'Segurança', 	'value':'Segurança'},
		{'label':'Suplemento', 	'value':'Suplemento'},
		{'label':'Tabacaria', 	'value':'Tabacaria'},
		{'label':'Vestuário', 	'value':'Vestuário'}
	];

	ng.colunas_pesquisa_produto = [
		{value: 0, name: 'id_produto', 				label: 'ID do Produto'},
		{value: 0, name: 'foto_produto', 			label: 'Foto do Produto'},
		{value: 0, name: 'codigo_barra', 			label: 'Código de Barras'},
		{value: 0, name: 'nome_categoria', 			label: 'Categoria'},
		{value: 0, name: 'nome_fabricante', 		label: 'Fabricante'},
		{value: 0, name: 'nome_tamanho', 			label: 'Tamanho'},
		{value: 0, name: 'nome_cor_sabor', 			label: 'Cor/Sabor'},
		{value: 0, name: 'desconto', 				label: 'Desconto'}
	];

	ng.colunas_ordenacao_produtos = [
		{value: 'id_produto', label: 'ID do Produto'},
		{value: 'codigo_barra', label: 'Código de Barras'},
		{value: 'nome_categoria', label: 'Categoria'},
		{value: 'nome_fabricante', label: 'Fabricante'},
		{value: 'nome_tamanho', label: 'Tamanho'},
		{value: 'sabor', label: 'Cor/Sabor'}
	];

	if(typeof parseJSON(ng.cfg.colunas_pesquisa_produto) == 'object')
		ng.colunas_pesquisa_produto = parseJSON(ng.cfg.colunas_pesquisa_produto);

	if(typeof parseJSON(ng.cfg.campos_ordenacao_produtos) == 'object')
		ng.campos_ordenacao_produtos = parseJSON(ng.cfg.campos_ordenacao_produtos);

	ng.addCampoOrdenacao = function(){
		if(empty(ng.campos_ordenacao_produtos))
			ng.campos_ordenacao_produtos = [];

		ng.campos_ordenacao_produtos.push({});
	}

	ng.delCampoOrdenacao = function(campo){
		ng.campos_ordenacao_produtos = _.without(ng.campos_ordenacao_produtos, campo);
	}

	ng.loadPlanoContasSelect = function() {
	 	ng.plano_contas = [{id:null,dsc_completa:"Selecione"}];
		aj.get(baseUrlApi()+"planocontas?tpc->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.plano_contas = ng.plano_contas.concat(data);
				setTimeout(function(){$("select").trigger("chosen:updated");},300);
			})
			.error(function(data, status, headers, config) {
				ng.plano_contas;
			});
	}

	ng.consultaCep = function(){
		aj.get("http://api.postmon.com.br/v1/cep/"+ng.cliente.cep)
		.success(function(data, status, headers, config) {

			ng.empreendimento.nme_logradouro = data.logradouro;
			ng.empreendimento.nme_bairro_logradouro = data.bairro;
			var estado = ng.getEstado(data.estado);
			ng.empreendimento.id_estado = estado.id;
			ng.loadCidadesByEstado(data.cidade);
			//ng.cliente.id_cidade = data.cidade_info.codigo_ibge.substr(0,6);
			$("#num_logradouro").focus();
			$('#busca-cep').modal('hide');
		})
		.error(function(data, status, headers, config) {
			$('#busca-cep').modal('hide');
			alert('CEP inválido');
		});
	}

	var cep_anterior = null;
	ng.validCep = function(cep){
		if(cep != cep_anterior){
			 var exp  = /^[0-9]{8}$/;
	         var cep = cep;
	         if(exp.test(cep)){
	         	cep_anterior = cep ;
	         	$('#busca-cep').modal({
				  backdrop: 'static',
				  keyboard: false
				});
				ng.consultaCep();
	         }
		}
	}

	ng.getEstado = function(uf){
		var estado = null ;
		$.each(ng.estados,function(i,x){
			if(x.uf.toUpperCase() == uf.toUpperCase()){
			    estado = x;
				return false;
			}
		});

		return estado;
	}

	ng.modalDepositos = function(){
		$('#modal-depositos').modal('show');
		ng.loadDepositos(0,10);
	}

	ng.busca_vazia 	= {};
	ng.busca 		= {};
	ng.paginacao 	= {};
	ng.loadDepositos = function(offset, limit,loadPag) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.busca_vazia.depositos = false ;
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(!empty(ng.busca.depositos))
			query_string  += "&"+$.param({nme_deposito:{exp:"like '%"+ng.busca.depositos+"%'"}});

    	aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+query_string)
			.success(function(data, status, headers, config) {
				ng.depositos = data.depositos ;
				if(loadPag == true){
					if(ng.depositos.length == 1)
						ng.addDeposito(ng.depositos[0]);
				}
				ng.paginacao.depositos = data.paginacao ;
			})
			.error(function(data, status, headers, config) {
				if(status != 404)
					alert("ocorreu um erro");
				else{
					ng.paginacao.depositos = [] ;
					ng.depositos = [] ;	
					ng.busca_vazia.depositos = true ;
				}
					
			});
	}

	ng.addDeposito = function(item){
		if(empty(ng.configuracoes.deposito_padrao))
			ng.configuracoes.deposito_padrao = {};

		ng.configuracoes.deposito_padrao.nome_deposito = item.nme_deposito;
		ng.configuracoes.deposito_padrao.id_deposito   = item.id;

		$('#modal-depositos').modal('hide');
	}

	ng.loadDepositoPadrao = function(id_deposito) {
		aj.get(baseUrlApi() + "deposito/" + id_deposito)
			.success(function(data, status, headers, config) {
				if(empty(ng.configuracoes.deposito_padrao)) {
					ng.configuracoes.deposito_padrao = {
						id_deposito: data.id,
						nome_deposito: data.nme_deposito
					};
				}

				setTimeout(function() {
					$scope.$apply();
				}, 500);
			})
			.error(function(data, status, headers, config) {
				if(status != 404)
					alert("ocorreu um erro");
				else {
					ng.configuracoes.deposito_padrao = null;
				}
			});
	}

	ng.salvarConfigDepositoPadrao = function(event){
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		var chaves = [];

		if(!empty(ng.configuracoes.deposito_padrao)){
			var item = {
				nome 				: 'id_deposito_padrao',
				valor 				: ng.configuracoes.deposito_padrao.id_deposito,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}

		if(!empty(ng.configuracoes.flg_exibir_produtos_sem_estoque) || ng.configuracoes.flg_exibir_produtos_sem_estoque == 0 ){
			var item = {
				nome 				: 'flg_exibir_produtos_sem_estoque',
				valor 				: ng.configuracoes.flg_exibir_produtos_sem_estoque,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}

		if(!empty(ng.configuracoes.flg_considerar_no_estoque_minimo) || ng.configuracoes.flg_considerar_no_estoque_minimo == 'deposito_padrao' ){
			var item = {
				nome 				: 'flg_considerar_no_estoque_minimo',
				valor 				: ng.configuracoes.flg_considerar_no_estoque_minimo,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}

		if(!empty(ng.configuracoes.flg_deposito_padrao_vitrine) || ng.configuracoes.flg_deposito_padrao_vitrine == 0 ){
			var item = {
				nome 				: 'flg_deposito_padrao_vitrine',
				valor 				: ng.configuracoes.flg_deposito_padrao_vitrine,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}

		if(!empty(ng.configuracoes.flg_controlar_validade_transferencia) || ng.configuracoes.flg_controlar_validade_transferencia == 0 ){
			var item = {
				nome 				: 'flg_controlar_validade_transferencia',
				valor 				: ng.configuracoes.flg_controlar_validade_transferencia,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}

		if(!empty(ng.configuracoes.flg_controlar_estoque) || ng.configuracoes.flg_controlar_estoque == 0 ){
			var item = {
				nome 				: 'flg_controlar_estoque',
				valor 				: ng.configuracoes.flg_controlar_estoque,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}

		btn.button('loading');
		
		aj.post(baseUrlApi()+"configuracao/save/",{ chaves: chaves })
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success', 'Configurações atualizadas com sucesso','.alert-config-estoque');
				ng.loadConfig();
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});
	}

	ng.salvarConfigPrestaShop = function(event){
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		var chaves = [];

		if(ng.flg_integrar_prestashop == 1){
			ng.configuracoes.sistemas_integrados = '["prestashop"]' ;
		}else{
			ng.configuracoes.sistemas_integrados = '[]' ;
		}

		var item = {
			nome 				: 'sistemas_integrados',
			valor 				: ng.configuracoes.sistemas_integrados,
			id_empreendimento	: ng.userLogged.id_empreendimento
		};

		chaves.push(item);

		if(!empty(ng.configuracoes.prestashop_id_perfil_padrao)){
			var item = {
				nome 				: 'prestashop_id_perfil_padrao',
				valor 				: ng.configuracoes.prestashop_id_perfil_padrao,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}
		if(!empty(ng.configuracoes.prestashop_depositos)){
			var item = {
				nome 				: 'prestashop_depositos',
				valor 				: ng.configuracoes.prestashop_depositos,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}
		if(!empty(ng.configuracoes.prestashop_id_categoria_root)){
			var item = {
				nome 				: 'prestashop_id_categoria_root',
				valor 				: ng.configuracoes.prestashop_id_categoria_root,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}
		if(!empty(ng.configuracoes.prestashop_id_categoria_inicio)){
			var item = {
				nome 				: 'prestashop_id_categoria_inicio',
				valor 				: ng.configuracoes.prestashop_id_categoria_inicio,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}
		if(!empty(ng.configuracoes.prestashop_id_attribute_group_tamanho)){
			var item = {
				nome 				: 'prestashop_id_attribute_group_tamanho',
				valor 				: ng.configuracoes.prestashop_id_attribute_group_tamanho,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}
		if(!empty(ng.configuracoes.prestashop_id_attribute_group_cor)){
			var item = {
				nome 				: 'prestashop_id_attribute_group_cor',
				valor 				: ng.configuracoes.prestashop_id_attribute_group_cor,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}
		if(!empty(ng.configuracoes.prestashop_ws_auth_key)){
			var item = {
				nome 				: 'prestashop_ws_auth_key',
				valor 				: ng.configuracoes.prestashop_ws_auth_key,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}
		if(!empty(ng.configuracoes.prestashop_shop_path)){
			var item = {
				nome 				: 'prestashop_shop_path',
				valor 				: ng.configuracoes.prestashop_shop_path,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}
		if(!empty(ng.configuracoes.prestashop_id_usuario_padrao)){
			var item = {
				nome 				: 'prestashop_id_usuario_padrao',
				valor 				: ng.configuracoes.prestashop_id_usuario_padrao,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}
		if(!empty(ng.configuracoes.prestashop_id_conta_bancaria_padrao)){
			var item = {
				nome 				: 'prestashop_id_conta_bancaria_padrao',
				valor 				: ng.configuracoes.prestashop_id_conta_bancaria_padrao,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}
		if(!empty(ng.configuracoes.prestashop_id_plano_conta_padrao)){
			var item = {
				nome 				: 'prestashop_id_plano_conta_padrao',
				valor 				: ng.configuracoes.prestashop_id_plano_conta_padrao,
				id_empreendimento	: ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}

		if(!empty(ng.status_venda)){
			var  status_venda = angular.copy(ng.status_venda) ;
			$.each(status_venda,function(i,v){
				status_venda[i].referencias = empty(status_venda[i].referencias) ? [] : parseJSON(status_venda[i].referencias);
			});

			var item = {
				nome : 'prestashop_referencia_status_venda',
				valor: JSON.stringify(status_venda),
				id_empreendimento	: ng.userLogged.id_empreendimento
			}
			chaves.push(item);
		}

		btn.button('loading');
		
		aj.post(baseUrlApi()+"configuracao/save/",{ chaves: chaves })
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success', 'Configurações atualizadas com sucesso','.alert-config-prestashop');
				ng.loadConfig();
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.loadEmpreendimento = function(id_empreendimento) {
		aj.get(baseUrlApi()+"empreendimento/"+id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.empreendimento = data;
				ng.loadCidadesByEstado();
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.empreendimento = [];
			});
	}

	ng.update = function(event) {
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');
		ng.reset();
		$('.formEmprendimento').ajaxForm({
		 	url: baseUrlApi()+"empreendimento/config/update",
		 	type: 'post',
		 	data: ng.empreendimento,
		 	success:function(data){
		 		ng.loadEmpreendimento(ng.userLogged.id_empreendimento);
				ng.loadEstados();
		 		btn.button('reset');
		 		ng.mensagens('alert-success', 'Configurações atualizadas com sucesso','.alert-basico-loja');
		 	},
		 	error:function(data){
		 		btn.button('reset');
		 		if(data.status == 406){
		 			$.each(data.responseJSON, function(i, item) {
						$("#"+i).addClass("has-error");

						var formControl = $($("#"+i))
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});
		 		}
		 	}
		}).submit(); 
	}
	
	ng.tipo_plano = null ;
	ng.config     = {} ;

	ng.modalPlanoContas = function(tipo){
		ng.tipo_plano = tipo;
		ng.loadPlanoContas();
		$('#modal-plano-contas').modal('show');
	}

	ng.escolherPlano = function(){
		//console.log(ng.tipo_plano,ng.currentNode);
		if(ng.tipo_plano =='movimentacao'){
			ng.config.nome_plano_movimentacao = ng.currentNode.dsc_plano ;
			ng.id_plano_movimentacao_caixa    = ng.currentNode.id;
		}
		else if(ng.tipo_plano =='fechamento'){
			ng.config.nome_plano_fechamento = ng.currentNode.dsc_plano ;
			ng.id_plano_fechamento_caixa    = ng.currentNode.id;
		}
		$('#modal-plano-contas').modal('hide');
	}

	ng.existsCookie = function(){
		 $.ajax({
		 	url: "setup_caixa.php?exists=true",
		 	async: false,
		 	success: function(data) {
		 		ng.exists_cookie = data;
		 		ng.config.pth_local = data.pth_local;
		 	},
		 	error: function(error) {
		 		ng.exists_cookie = false
		 	}
		 });
	}

	ng.keysConfig = {} ;
	ng.loadConfig = function(event){
		var error = 0 ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {

				if(!empty(data.prestashop_referencia_status_venda)){
					var status_venda = parseJSON(data.prestashop_referencia_status_venda);
					$.each(status_venda,function(i,v){
						status_venda[i].referencias = JSON.stringify(status_venda[i].referencias);
					});
					ng.status_venda = status_venda
				}

				if(!empty(data.regras_servico_padrao) && typeof parseJSON(data.regras_servico_padrao) == 'object' ){
					ng.regras_servico_padrao = parseJSON(data.regras_servico_padrao);
					$.each(ng.regras_servico_padrao,function(z,x){
						ng.loadCidadesByEstado(x.cod_estado,x);
						ng.loadRegrasServico(x);
					});
				}else{
					ng.regras_servico_padrao = [] ;
				}

				$.each(data,function(i,x){
					ng.keysConfig[i] = { 
											nome : i,
											valor : x,
											id_empreendimento : ng.userLogged.id_empreendimento
										}
				});

				if(data.sistemas_integrados == '["prestashop"]'){
					ng.flg_integrar_prestashop = 1 ;
				}else{
					ng.flg_integrar_prestashop = 0 ;
				}

				data.emails_notificacoes = !empty(data.emails_notificacoes) ? JSON.parse(data.emails_notificacoes) : [] ;
				var emails = [] ;
				$.each(data.emails_notificacoes,function(i,v){
					emails.push({text:v});
				});
				data.emails_notificacoes = emails ;
				ng.configuracoes = data;
				ng.configuracoes.id_plano_conta_pagamento_profissional = ""+ng.configuracoes.id_plano_conta_pagamento_profissional ;
				ng.notEmails = emails;



				if(!empty(data.id_deposito_padrao))
					ng.loadDepositoPadrao(data.id_deposito_padrao);

				if(!empty(data.valores_chinelos) && typeof parseJSON(data.valores_chinelos) == 'object' ){
					ng.valoresChinelos = parseJSON(data.valores_chinelos);
				}
				
				if(data.id_plano_caixa == undefined){
					$('#id_plano_caixa').addClass('has-error');
					error++ ;
				}else{
					ng.loadPlanoConta(data.id_plano_caixa,'movimentacao');
					$('#id_plano_caixa').removeClass('has-error');
				}

				if(!ng.exists_cookie){
					$('#pth_local').addClass('has-error');
					error++ ;
				}else{
					$('#pth_local').removeClass('has-error')
				}

				if(data.id_plano_fechamento_caixa == undefined){
					$('#id_plano_fechamento_caixa').addClass('has-error');
					error++;
				}else{
					ng.loadPlanoConta(data.id_plano_fechamento_caixa,'fechamento');
					$('#id_plano_fechamento_caixa').removeClass('has-error');
				}

				if(error > 0)
					$('.alert-error-config').show();
				else{
					$('.alert-error-config').hide();
				}

			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.configuracoes = [];
					$('#id_plano_caixa').addClass('has-error');
					$('#id_plano_fechamento_caixa').addClass('has-error');
					$('.alert-error-config').show();
					if(!ng.exists_cookie){
						$('#pth_local').addClass('has-error');
					}
				}
			});	
	}

	ng.loadPlanoConta = function(id,tipo) {
		var r  = false ;
		aj.get(baseUrlApi()+"planoconta/"+id)
			.success(function(data, status, headers, config) {
				//console.log(data);
				if(tipo == 'movimentacao'){
					ng.config.nome_plano_movimentacao = data.dsc_plano;
					ng.id_plano_movimentacao_caixa = data.id;
				}else if(tipo == 'fechamento'){
					ng.config.nome_plano_fechamento = data.dsc_plano ;
					ng.id_plano_fechamento_caixa = data.id;
				}
			})
			.error(function(data, status, headers, config) {
			});
	}
	ng.config = {} ;
	ng.salvarConfig = function(event){
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		var chaves = [];
		if(ng.id_plano_fechamento_caixa != undefined){
			var item1 = {
							nome 				:'id_plano_fechamento_caixa',
							valor 				:ng.id_plano_fechamento_caixa , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item1);
		}
		if(ng.id_plano_movimentacao_caixa != undefined){
			var item2 = {
							nome 				:'id_plano_caixa',
							valor 				:ng.id_plano_movimentacao_caixa , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item2);
		}
		
		if(ng.configuracoes.flg_emitir_nfe_pdv != undefined){
			var item3 = {
							nome 				:'flg_emitir_nfe_pdv',
							valor 				:ng.configuracoes.flg_emitir_nfe_pdv , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item3);
		}

		if(ng.configuracoes.id_plano_conta_pagamento_profissional != undefined){
			var item4 = {
							nome 				:'id_plano_conta_pagamento_profissional',
							valor 				:ng.configuracoes.id_plano_conta_pagamento_profissional , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item4);
		}

		if(ng.configuracoes.cadastro_cpf_pdv != undefined){
			var item6 = {
							nome 				:'cadastro_cpf_pdv',
							valor 				:ng.configuracoes.cadastro_cpf_pdv , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item6);
		}

		if(typeof ng.formas_pagamento_pdv == 'object'){
			var formas_pagamento_pdv = JSON.stringify(angular.copy(ng.formas_pagamento_pdv));
			var item9 = {
							nome 				:'formas_pagamento_pdv',
							valor 				:formas_pagamento_pdv , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item9);
		}

		if(typeof ng.perfis == 'object'){
			var perfis = JSON.stringify(angular.copy(ng.perfis));
			var item10 = {
							nome 				:'perfis_cadastro_rapido',
							valor 				:perfis , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item10);
		}

		if(typeof ng.colunas_pesquisa_produto == 'object'){
			var colunas_pesquisa_produto = JSON.stringify(angular.copy(ng.colunas_pesquisa_produto));
			var item11 = {
							nome 				:'colunas_pesquisa_produto',
							valor 				:colunas_pesquisa_produto , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item11);
		}

		if(typeof ng.campos_ordenacao_produtos == 'object'){
			var campos_ordenacao_produtos = JSON.stringify(angular.copy(ng.campos_ordenacao_produtos));
			var item11 = {
							nome 				:'campos_ordenacao_produtos',
							valor 				:campos_ordenacao_produtos , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item11);
		}

		if(ng.configuracoes.flg_questionar_manutencao_precos_orcamento != undefined){
			var item10 = {
							nome 				:'flg_questionar_manutencao_precos_orcamento',
							valor 				:ng.configuracoes.flg_questionar_manutencao_precos_orcamento , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item10);
		}

		if(ng.configuracoes.flg_considerar_pagamento_pendente_saldo_devedor != undefined){
			var item10 = {
							nome 				:'flg_considerar_pagamento_pendente_saldo_devedor',
							valor 				:ng.configuracoes.flg_considerar_pagamento_pendente_saldo_devedor , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item10);
		}

		if(ng.configuracoes.flg_baixa_automatica_pagamento_cartao_credito != undefined){
			var item10 = {
							nome 				:'flg_baixa_automatica_pagamento_cartao_credito',
							valor 				:ng.configuracoes.flg_baixa_automatica_pagamento_cartao_credito , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item10);
		}

		if(ng.configuracoes.flg_filtrar_cliente_por_vendedor != undefined){
			var item10 = {
							nome 				:'flg_filtrar_cliente_por_vendedor',
							valor 				:ng.configuracoes.flg_filtrar_cliente_por_vendedor , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item10);
		}

		if(ng.configuracoes.flg_auto_focus_pesquisa_produtos != undefined){
			var item10 = {
							nome 				:'flg_auto_focus_pesquisa_produtos',
							valor 				:ng.configuracoes.flg_auto_focus_pesquisa_produtos , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item10);
		}

		if(ng.configuracoes.flg_auto_focus_pesquisa_produtos_codigo_barra != undefined){
			var item10 = {
							nome 				:'flg_auto_focus_pesquisa_produtos_codigo_barra',
							valor 				:ng.configuracoes.flg_auto_focus_pesquisa_produtos_codigo_barra , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item10);
		}

		if(ng.configuracoes.flg_configurar_colunas_pesquisa_produtos != undefined){
			var item10 = {
							nome 				:'flg_configurar_colunas_pesquisa_produtos',
							valor 				:ng.configuracoes.flg_configurar_colunas_pesquisa_produtos , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item10);
		}

		if(ng.configuracoes.cod_identificador_balanca != undefined){
			var item10 = {
							nome 				:'cod_identificador_balanca',
							valor 				:ng.configuracoes.cod_identificador_balanca , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item10);
		}

		if(ng.configuracoes.qtd_casas_decimais != undefined){
			var item10 = {
							nome 				:'qtd_casas_decimais',
							valor 				:ng.configuracoes.qtd_casas_decimais , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item10);
		}

		if(ng.configuracoes.qtd_registros_pesquisa_produtos != undefined){
			var item2000 = {
							nome 				:'qtd_registros_pesquisa_produtos',
							valor 				:ng.configuracoes.qtd_registros_pesquisa_produtos , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item2000);
		}

		if(ng.configuracoes.dsc_titulo_cnf != undefined){
			var item5 = {
							nome 				:'dsc_titulo_cnf',
							valor 				:ng.configuracoes.dsc_titulo_cnf , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item5);
		}

		if(ng.configuracoes.dsc_observacoes_cnf != undefined){
			var item5 = {
							nome 				:'dsc_observacoes_cnf',
							valor 				:ng.configuracoes.dsc_observacoes_cnf , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item5);
		}

		btn.button('loading');
		var pth_local_sucess = false ;
		if(ng.config.pth_local != undefined){

			aj.post("setup_caixa.php",{pth_local: ng.config.pth_local } )
				.success(function(data, status, headers, config) {
					ng.exists_cookie = true ;
				})
				.error(function(data, status, headers, config) {

				});
		}

		aj.post(baseUrlApi()+"configuracao/save/",{ chaves:chaves, pth_local: ng.config.pth_local} )
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success', 'Configurações atualizadas com sucesso','.alert-config');
				ng.loadConfig();
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});
	}

	ng.cancelarModal = function(id){
		$('#'+id).modal('hide');
	}


	ng.loadOperacaoCombo = function() {
		ng.lista_operacao  = [{cod_operacao:'',dsc_operacao:'--- Selecione ---'}] ;
		aj.get(baseUrlApi()+"operacao/get/?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.lista_operacao = ng.lista_operacao.concat(data.operacao);
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
					
			});
	}

	ng.loadVersoesIBPTCombo = function() {
		ng.lista_versao_ibpt  = [{versao:'--- Selecione ---'}] ;
		aj.get(baseUrlApi()+"ibpt/versoes")
			.success(function(data, status, headers, config) {
				ng.lista_versao_ibpt = ng.lista_versao_ibpt.concat(data);
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
					
			});
	}

	 ng.loadControleNfe = function(ctr,key) {
	 	ng[key] = [];
		aj.get(baseUrlApi()+"nfe/controles/null/"+ctr)
			.success(function(data, status, headers, config) {
				ng[key] = [{cod_controle_item_nfe:'',num_item:''}];
				$.each(data,function(i,v){
					data[i].descricao = v.nme_item+' - '+v.dsc_item ;
				});
				ng[key] = ng[key].concat(data);
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
				
		});
	}

	ng.loadSerieDocumentoFiscal = function() {
		ng.lista_serie_documento_fiscal = null ;
		aj.get(baseUrlApi()+"serie_documento_fiscal/?cod_empreendimento="+ng.userLogged.id_empreendimento+"&tsdf->flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.lista_serie_documento_fiscal = data;
				$("select").trigger("chosen:updated");
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.lista_serie_documento_fiscal = [];
			});
	}

	ng.incluirSerieDocumentoFiscal = function(event){
		var btn = $(event.target) ;
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');
		var item = angular.copy(ng.serie_documento_fiscal) ;
		var error = 0 ;
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')
		$.each(item,function(i,v){
			if(empty(v) && (i == 'serie_documento_fiscal' ||  i == 'num_modelo_documento_fiscal' ||  i == 'num_ultimo_documento_fiscal')){
				$("#produto-cliente-nme_cliente").addClass("has-error");
					var formControl = $("#"+i);
						formControl.addClass("has-error")
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", "Campo  obrigatório")
						.attr("data-original-title", "Campo  obrigatório");
				if(error == 0) formControl.tooltip('show');
				else  formControl.tooltip() ;
				error ++ ;
			}else if(i == 'serie_documento_fiscal' && !(ng.edit_serie_documento_fiscal)){
				$.each(ng.lista_serie_documento_fiscal,function(y,z){
					if(v == z.serie_documento_fiscal){
						var formControl = $("#"+i);
						formControl.addClass("has-error")
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", "Número de série ja existe")
						.attr("data-original-title", "Número de série ja existe");
						if(error == 0) formControl.tooltip('show');
						else  formControl.tooltip() ;
						error ++ ;
						return ;
					}
				});

			}
		});


		if(error > 0){
			btn.button('reset');
			return ;
		}


		$.each(ng.chosen_modelo_nota_fiscal,function(i,v){
			
			if(v.num_item == item.num_modelo_documento_fiscal){
				item.dsc_modelo_documento_fiscal =  v.nme_item +' - '+ v.dsc_item ;
				return ;
			}
		});

		if(ng.edit_serie_documento_fiscal){
			ng.lista_serie_documento_fiscal[ng.index_edit_serie_documento_fiscal] = item ;
			ng.index_edit_serie_documento_fiscal = null ;
			ng.edit_serie_documento_fiscal = false ;
		}
		else ng.lista_serie_documento_fiscal.push(item);
		ng.serie_documento_fiscal = angular.copy(serieDocumentoFiscalTO) ;
		btn.button('reset');
	}

	ng.indexEditSerieDocumentoFiscal ;
	ng.editSerieDocumentoFiscal = function(index,item){
		ng.edit_serie_documento_fiscal = true ;
		ng.index_edit_serie_documento_fiscal = index ;
		ng.serie_documento_fiscal = angular.copy(item);
		console.log(ng.serie_documento_fiscal);
	}

	var deleteSerieFiscal = [] ;
	ng.delSerieDocumentoFiscal = function(index){
		if(ng.lista_serie_documento_fiscal[index].id == undefined)
			ng.lista_serie_documento_fiscal.splice(index,1);
		else
			ng.lista_serie_documento_fiscal[index].flg_excluido = 1 ;

		console.log(deleteSerieFiscal);
	}

	ng.salvarConfigFiscal = function(event){
		ng.update(event);
		
		var serie = angular.copy(ng.serie_documento_fiscal);
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		var chaves = [];
		if(ng.configuracoes.id_operacao_padrao_venda != undefined){
			var item = {
							nome:'id_operacao_padrao_venda',
							valor:ng.configuracoes.id_operacao_padrao_venda,
							id_empreendimento: ng.userLogged.id_empreendimento}
			chaves.push(item);
		}

		if(ng.configuracoes.num_versao_ibpt != undefined){
			var item = {
							nome:'num_versao_ibpt',
							valor:ng.configuracoes.num_versao_ibpt,
							id_empreendimento: ng.userLogged.id_empreendimento}
			chaves.push(item);
		}

		if(ng.configuracoes.id_serie_padrao_nfce != undefined){
			var item = {
							nome :'id_serie_padrao_nfce',
							valor:ng.configuracoes.id_serie_padrao_nfce ,
							id_empreendimento: ng.userLogged.id_empreendimento}
			chaves.push(item);
		}

		if(ng.configuracoes.id_serie_padrao_nfe != undefined){
			var item = {
							nome :'id_serie_padrao_nfe',
							valor:ng.configuracoes.id_serie_padrao_nfe , 
							id_empreendimento: ng.userLogged.id_empreendimento}
			chaves.push(item);
		}

		if(ng.configuracoes.flg_ambiente_nfe != undefined){
			var item = {
							nome :'flg_ambiente_nfe',
							valor:ng.configuracoes.flg_ambiente_nfe , 
							id_empreendimento: ng.userLogged.id_empreendimento}
			chaves.push(item);
		}

		if(ng.configuracoes.token_focus_producao != undefined){
			var item = {
							nome :'token_focus_producao',
							valor:ng.configuracoes.token_focus_producao , 
							id_empreendimento: ng.userLogged.id_empreendimento}
			chaves.push(item);
		}

		if(ng.configuracoes.token_focus_homologacao != undefined){
			var item = {
							nome :'token_focus_homologacao',
							valor:ng.configuracoes.token_focus_homologacao , 
							id_empreendimento: ng.userLogged.id_empreendimento}
			chaves.push(item);
		}

		if(ng.configuracoes.patch_socket_sat != undefined){
			var item = {
							nome 				:'patch_socket_sat',
							valor 				:ng.configuracoes.patch_socket_sat , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}
		
		if(ng.configuracoes.num_cnpj_sw != undefined){
			var item = {
							nome 				:'num_cnpj_sw',
							valor 				:ng.configuracoes.num_cnpj_sw , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}

		if(ng.configuracoes.txt_sign_ac != undefined){
			var item = {
							nome 				:'txt_sign_ac',
							valor 				:ng.configuracoes.txt_sign_ac , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}

		if(ng.configuracoes.dta_validade_certificado_digital != undefined){
			var item = {
							nome 				:'dta_validade_certificado_digital',
							valor 				:ng.configuracoes.dta_validade_certificado_digital , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}

		if(ng.configuracoes.qtd_dias_antecedencia_alerta_vencimento_certificado_digital != undefined){
			var item = {
							nome 				:'qtd_dias_antecedencia_alerta_vencimento_certificado_digital',
							valor 				:ng.configuracoes.qtd_dias_antecedencia_alerta_vencimento_certificado_digital , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}

		btn.button('loading');
		if(empty(ng.lista_serie_documento_fiscal)){
			ng.lista_serie_documento_fiscal = [] ;
		}
		aj.post(baseUrlApi()+"serie_documento_fiscal",{series:ng.lista_serie_documento_fiscal})
			.success(function(data, status, headers, config) {
				aj.post(baseUrlApi()+"configuracao/save/",{ chaves:chaves, pth_local: ng.config.pth_local} )
					.success(function(data, status, headers, config) {
						ng.loadSerieDocumentoFiscal();
						btn.button('reset');
						ng.mensagens('alert-success', 'Configurações atualizadas com sucesso','.alert-config-fiscal');
					})
					.error(function(data, status, headers, config) {
						btn.button('reset');
					});
			})
			.error(function(data, status, headers, config) {
				alert('Erro Fatal')
				btn.button('reset');
			});
		
	}
 
	ng.salvarConfigNotificacoes = function(event){
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		var chaves = [];
		if(!empty(ng.notEmails) || ng.notEmails.length > 0){
			var emails = [] ;
			$.each(ng.notEmails,function(i,v){
				emails.push(v.text);
			});
			var x = JSON.stringify(emails);
			item = {nome:'emails_notificacoes',valor:x,id_empreendimento:ng.userLogged.id_empreendimento}
			chaves.push(item);
		}else{
			return ;
		}

		btn.button('loading');
		aj.post(baseUrlApi()+"configuracao/save/",{ chaves:chaves, pth_local: ng.config.pth_local} )
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success', 'Configurações atualizadas com sucesso','.alert-config-not');
				ng.loadConfig();
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});
		
	}

	ng.salvarConfigAtendimento = function(event){
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		var chaves = [];


		if(ng.configuracoes.id_plano_conta_pagamento_profissional != undefined){
			var item = {
							nome 				:'id_plano_conta_pagamento_profissional',
							valor 				:ng.configuracoes.id_plano_conta_pagamento_profissional , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}

		if(ng.configuracoes.flg_controlar_tempo_atendimento != undefined){
			var item = {
							nome 				:'flg_controlar_tempo_atendimento',
							valor 				:ng.configuracoes.flg_controlar_tempo_atendimento , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}

		btn.button('loading');
		
		aj.post(baseUrlApi()+"configuracao/save/",{ chaves:chaves, pth_local: ng.config.pth_local} )
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success', 'Configurações atualizadas com sucesso','.alert-config-atendimento');
				ng.loadConfig();
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});
	}

	ng.salvarConfigControleMesas = function(event){
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		var chaves = [];

		if(ng.configuracoes.prc_taxa_servico != undefined){
			var item = {
							nome 				:'prc_taxa_servico',
							valor 				:ng.configuracoes.prc_taxa_servico , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}

		if(ng.configuracoes.id_produto_taxa_servico != undefined){
			var item = {
							nome 				:'id_produto_taxa_servico',
							valor 				:ng.configuracoes.id_produto_taxa_servico , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}

		if(ng.configuracoes.printer_model_op != undefined){
			var item = {
				nome 				:'printer_model_op',
				valor 				:ng.configuracoes.printer_model_op , 
				id_empreendimento	:ng.userLogged.id_empreendimento
			};
			chaves.push(item);
		}

		if(ng.configuracoes.flg_fechar_guia_ao_finalizar_uma_comanda != undefined){
			var item = {
							nome 				:'flg_fechar_guia_ao_finalizar_uma_comanda',
							valor 				:ng.configuracoes.flg_fechar_guia_ao_finalizar_uma_comanda , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}
		if(ng.configuracoes.flg_imprimir_cnf_antes_de_fechar_guia != undefined){
			var item = {
							nome 				:'flg_imprimir_cnf_antes_de_fechar_guia',
							valor 				:ng.configuracoes.flg_imprimir_cnf_antes_de_fechar_guia , 
							id_empreendimento	:ng.userLogged.id_empreendimento
						}
			chaves.push(item);
		}

		btn.button('loading');
		
		aj.post(baseUrlApi()+"configuracao/save/",{ chaves: chaves })
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success', 'Configurações atualizadas com sucesso','.alert-config-mesas');
				ng.loadConfig();
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});
	}

	ng.loadEstados = function () {
		ng.estados = [];

		aj.get(baseUrlApi()+"estados")
		.success(function(data, status, headers, config) {
			ng.estados = data;
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.cidades = [{id: "" ,nome:"Selecione um estado"}];
	ng.loadCidadesByEstado = function () {
		ng.cidades = [];
		aj.get(baseUrlApi()+"cidades_by_id_estado/"+ng.empreendimento.cod_estado)
		.success(function(data, status, headers, config) {
			ng.cidades = data;
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.loadZoneamento = function() {
		aj.get(baseUrlApi()+"zoneamento/get?cod_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.zoneamentos = ng.zoneamentos.concat(data.zoneamentos);
				setTimeout(function(){$("select").trigger("chosen:updated");},300);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.zoneamentos = [];
			});
	}

	ng.loadPlanoContas = function() {
		aj.get(baseUrlApi()+"plano_contas_treeview?tpc->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.planoContas = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.planoContas = [];
			});
	}

	ng.loadFormasPagamento = function() {
		ng.formas_pagamento = [];
		aj.get(baseUrlApi()+"formas_pagamento")
			.success(function(data, status, headers, config) {
				ng.formas_pagamento_pdv = data ;
				var aux = typeof parseJSON(ng.cfg.formas_pagamento_pdv) == 'object' ?  parseJSON(ng.cfg.formas_pagamento_pdv) : [] ;
				$.each(ng.formas_pagamento_pdv,function(i,x){ 
					ng.formas_pagamento_pdv[i].value = 0 ;
					var exists = false ;
					$.each(aux,function(y,z){ 
						if(x.id == z.id && Number(z.value) == 1){
							exists = true
							return ;
						}
					});
					if(exists)
						ng.formas_pagamento_pdv[i].value = aux[i].value ;
					else
						ng.formas_pagamento_pdv[i].value = 0 ;

				});
			});
	}
	ng.incluirFaixa = function(tipo){
		ng.valoresChinelos[tipo].faixas.push({de:null,ate:null,valor:null });
	}

	ng.excluirFaixa = function(tipo,obj){
		ng.valoresChinelos[tipo].faixas = _.without(ng.valoresChinelos[tipo].faixas,obj);
	}

	ng.salvarConfigPedidoPersonalizado = function(valoresChinelos){
		var btn = $('#btn-pedido-personalizado');
		btn.button('loading');
		valoresChinelos = angular.copy(valoresChinelos);
		var json = JSON.stringify(valoresChinelos);
		var chaves = [];
		var item = {
			nome 				:'valores_chinelos',
			valor 				: json , 
			id_empreendimento	:ng.userLogged.id_empreendimento
		}
		chaves.push(item);
		
		aj.post(baseUrlApi()+"configuracao/save/",{chaves:chaves} )
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success', 'Configurações atualizadas com sucesso','.alert-config-pedido-personalizado');
				ng.loadConfig();
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});

		console.log(json);
	}

	ng.loadPerfis = function() {
		aj.get(baseUrlApi()+"perfis?tpue->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				var aux = typeof parseJSON(ng.cfg.perfis_cadastro_rapido) == 'object' ?  parseJSON(ng.cfg.perfis_cadastro_rapido) : [] ;
				console.log(aux);
				$.each(data,function(i,x){
					index = getIndex('id',data[i].id,data);
					if($.isNumeric(index) && !empty(aux[i])){
						data[i].value = aux[i].value ;
					}else{
						data[i].value = 0 ;
					}
				});
				ng.perfis = data ;
			})
			.error(function(data, status, headers, config) {
				ng.perfis = [] ;
			});
	}


	ng.loadControleNfe('modelo_nota_fiscal','chosen_modelo_nota_fiscal');
	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.regimeTributario = [{num_item:null,nme_item:null}] ;
	ng.regimePisCofins = [{num_item:null,nme_item:null}] ;
	ng.tipoEmpresa = [{num_item:null,nme_item:null}] ;
	ng.zoneamentos = [{num_item:null,nme_item:null}] ;


	ng.addIncricaoEstadual = function() {
		if(empty(ng.empreendimento.inscricoes_estaduais))
			ng.empreendimento.inscricoes_estaduais = [];

		 ng.empreendimento.inscricoes_estaduais.push({
		 	id_empreendimento : ng.userLogged.id_empreendimento,
		 	id_estado : null
		 });	
	}

	ng.deleteInscricoesEstaduais= function(index){
		ng.empreendimento.inscricoes_estaduais.splice(index,1);
	}

	
	ng.addRegraServico = function(){
		ng.regras_servico_padrao.push({
			cod_estado : null,
			cod_municipio : null,
			cod_regra_servico : null 
		});
	}
	ng.deleteRegraServico = function(index){
		ng.regras_servico_padrao.splice(index,1);	
	}

	ng.loadCidadesByEstado = function (id_estado,item) {
		aj.get(baseUrlApi()+"cidades/"+id_estado)
		.success(function(data, status, headers, config) {
			ng.cidades = [{id:null,nome:'Selecione'}].concat(data);
			setTimeout(function(){$("select").trigger("chosen:updated");},300);
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.loadRegrasServico = function (item) {
		var queryString = "?cplSql= WHERE trs.cod_empreendimento = "+ng.userLogged.id_empreendimento+" AND trs.flg_excluido = 0 ";
		queryString += "AND trs.cod_estado = "+item.cod_estado+" AND trs.cod_municipio = "+item.cod_municipio;
		aj.get(baseUrlApi()+"regras_servico/"+encodeURI(queryString) )
		.success(function(data, status, headers, config) {
			item.regras = data ;
			setTimeout(function(){$("select").trigger("chosen:updated");},300);
		})
		.error(function(data, status, headers, config) {
			ng.regrasCadastradas = {regras:[],paginacao:[]} ;
		});
	}

	ng.salvarConfigFiscalServico = function(event){
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		var chaves = [];
		if(typeof ng.regras_servico_padrao == 'object'){
			regras_servico_padrao = angular.copy(ng.regras_servico_padrao);
			$.each(regras_servico_padrao,function(i,x){
				delete regras_servico_padrao[i].municipios ;
				delete regras_servico_padrao[i].regras ;
			});
			regras_servico_padrao = JSON.stringify(angular.copy(regras_servico_padrao));
		}
		else
			var regras_servico_padrao = JSON.stringify([]);
		var item = {
						nome 				:'regras_servico_padrao',
						valor 				:regras_servico_padrao , 
						id_empreendimento	:ng.userLogged.id_empreendimento
					}
		chaves.push(item);
		btn.button('loading');
		aj.post(baseUrlApi()+"configuracao/save/",{ chaves:chaves} )
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success', 'Configurações atualizadas com sucesso','.alert-config-fiscal-servico');
				ng.loadConfig();
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});
	}

	/*ng.modalSincronizacaoPrestashop = {
	    "status": "in_progress",
	    "executando_agora": {
	        "mensagem": "Sincronizando categorias",
	        "loading": 20,
	        "index": "categorias",
	        "qtd": 10,
	        "feito": 2
	    },
	    "dados_sincronizados": {
	        "categorias": {
	            "status": "in_progress",
	            "qtd": 10,
	            "feito": 2
	        }
	    }
	}*/


	ng.sincronizarDadosPrestaShop = function(event){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja Sincronizar todos os dados do empreendimento com o PrestaShop?</strong>');

		dlg.result.then(function(btn){
			
			var but = $(event.target);
			if(!(but.is(':button')))
			but = $(but.parent('button'));


			but.button('loading');

			ng.modalSincronizacaoPrestashop = {status:'init'};
			$('#modal-sincronizacao-prestashop').modal('show');

			var post = {
				script:'teste.php',
				id_empreendimento: ng.userLogged.id_empreendimento,
				params:{
					id_empreendimento: ng.userLogged.id_empreendimento
				}
			};

			aj.post(baseUrlApi()+"background/start",post )
			.success(function(data, status, headers, config) {
				ng.existsAtualizacaoEmMassa();
				but.button('reset');
			})
			.error(function(data, status, headers, config) {
				alert('Erro ao iniciar processo');
				but.button('reset');
			});

		}, undefined);
	}

	var setintervalStatusSincronizacaoPrestaShop = null ;
	function AtualizarStatusSincronizacaoPrestaShop(url){
		setintervalStatusSincronizacaoPrestaShop = setInterval(function(){
			aj.get(url)
			.success(function(data, status, headers, config) {
				ng.modalSincronizacaoPrestashop = data ;
				if(data.status != 'in_progress')
					clearInterval(setintervalStatusSincronizacaoPrestaShop);
			})
			.error(function(data, status, headers, config) {

			});
		},2000);
	}

	ng.isObject = function(item){
		return (typeof item == 'object') ;
	}

	ng.currentAtualizacaoEmMassa = null ;

	ng.existsAtualizacaoEmMassa = function () {
		ng.estados = [];

		aj.get(baseUrlApi()+"background/atualizacao_em_massa/exists/"+ng.userLogged.id_empreendimento)
		.success(function(data, status, headers, config) {
			ng.currentAtualizacaoEmMassa = data;
			$('#modal-sincronizacao-prestashop').modal('hide');
		})
		.error(function(data, status, headers, config) {
			$('#modal-sincronizacao-prestashop').modal('hide');
		});
	}

	ng.verSincronizacao = function(){
		$('#modal-atualizacao_em_massa').modal('show');
		aj.get(baseUrlApi()+"background/atualizacao_em_massa/"+ng.currentAtualizacaoEmMassa.id)
		.success(function(data, status, headers, config) {
			ng.modalAtualizacaoEmMassa = data;
			var jsonObj = JSON.parse(ng.modalAtualizacaoEmMassa.dados_json);
			ng.modalAtualizacaoEmMassa.jsonPretty = JSON.stringify(jsonObj, null, '\t');
		})
		.error(function(data, status, headers, config) {
			alert('Erro ao carregar dados');
		});	
	}

	ng.getSincronizacao = function(event){
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));


		btn.button('loading');

		aj.get(baseUrlApi()+"background/atualizacao_em_massa/"+ng.currentAtualizacaoEmMassa.id)
		.success(function(data, status, headers, config) {
			ng.modalAtualizacaoEmMassa = data;
			var jsonObj = JSON.parse(ng.modalAtualizacaoEmMassa.dados_json);
			ng.modalAtualizacaoEmMassa.jsonPretty = JSON.stringify(jsonObj, null, '\t');
			btn.button('reset');
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			alert('Erro ao carregar dados');
		});	
	}

	ng.ultimaAtualizacaoEmMassa = function () {
		$('#modal-ultima-atualizacao_em_massa').modal('show');
		aj.get(baseUrlApi()+"background/atualizacao_em_massa/ultima/"+ng.userLogged.id_empreendimento)
		.success(function(data, status, headers, config) {
			ng.ultimaAtualizacaoEmMassa = data;
			var jsonObj = JSON.parse(ng.ultimaAtualizacaoEmMassa.dados_json);
			ng.ultimaAtualizacaoEmMassa.jsonPretty = JSON.stringify(jsonObj, null, '\t');
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.existsAtualizacaoEmMassa();
	ng.loadPerfis();
	ng.loadEmpreendimento(ng.userLogged.id_empreendimento);
	ng.existsCookie();
	ng.loadConfig();
	ng.loadOperacaoCombo();
	ng.loadVersoesIBPTCombo();
	ng.loadSerieDocumentoFiscal();
	ng.loadEstados();
	ng.loadZoneamento();
	ng.loadControleNfe('regime_tributario','regimeTributario');
	ng.loadControleNfe('regime_tributario_pis_cofins','regimePisCofins');
	ng.loadControleNfe('tipo_empresa','tipoEmpresa');
	ng.loadPlanoContas();
	ng.loadPlanoContasSelect();
	ng.loadFormasPagamento();

});
