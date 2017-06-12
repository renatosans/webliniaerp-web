app.controller('NotaFiscalController', function($scope, $http, $window, $dialogs,$interval, UserService,ConfigService,NFService){
	var ng = $scope
		aj = $http;

	var url_params       		= getUrlVars();
	ng.baseUrl 		 			= baseUrl();
	ng.userLogged 	 			= UserService.getUserLogado();
	ng.configuracoes 			= ConfigService.getConfig(ng.userLogged.id_empreendimento);
    ng.id_transportadora 		= undefined;
    ng.disableSendNf 			= false;
    ng.nfeCalculada 			= false;
    ng.lista_modalidade_frete 	= [];

	if($.isNumeric(url_params.id_venda))
		ng.nota = NFService.getNota(ng.userLogged.id_empreendimento,url_params.id_venda);
    
    ng.editing 		= false;
    var nfTO        = {
    	dados_emissao: {
			tipo_documento: 	'',
			local_destino: 		'',
			finalidade_emissao: '',
			consumidor_final: 	'',
			forma_pagamento: 	'',
			presenca_comprador: ''
		},
		transportadora: {
			modalidade_frete: 	'' 
		}
    };

    ng.NF = (empty(ng.nota)) ? angular.copy(nfTO) : ng.nota;
    ng.NF.id_empreendimento 	= ng.userLogged.id_empreendimento;
    ng.processando_autorizacao 	= (ng.NF.dados_emissao.status  == 'processando_autorizacao');
    ng.autorizado              	= (ng.NF.dados_emissao.status  == 'autorizado');

    // ng.NF.dados_emissao.cod_operacao = null; 
    
    if(!empty(ng.NF.dados_emissao.data_emissao))
    	$('#inputDtaEmissao').val(moment(ng.NF.dados_emissao.data_emissao).format('DD/MM/YYYY'));
    
    if(!empty(ng.NF.dados_emissao.data_entrada_saida)){ 
    	$('#inputDtaSaida').val(moment(ng.NF.dados_emissao.data_entrada_saida).format('DD/MM/YYYY'));
    	$('#InputhrsSaida').val(moment(ng.NF.dados_emissao.data_entrada_saida).format('HH:mm'));
    }

	ng.calcularNfe = function(event, id_venda, cod_operacao) {
		var formControl = $('#cod_operacao');
			formControl.removeClass("has-error");
			formControl.tooltip('destroy');

		if(empty(cod_operacao)){
			formControl.addClass("has-error");
			formControl.attr("data-toggle", "tooltip").attr("data-placement", "top").attr("title", 'Selecione a operação').attr("data-original-title", 'Selecione a operação');
			formControl.tooltip('show');
			$('html,body').animate({scrollTop: 0},'slow');
			return ;
		}

		if(event != null) {
			var btn = $(event.target);
			if(!(btn.is(':button')))
				btn = $(btn.parent('button'));
			btn.button('loading');
		}
		else
			$('#modal-calculando').modal({backdrop: 'static', keyboard: false});

		var post = { 
			id_empreendimento 					: ng.userLogged.id_empreendimento,
			id_venda 							: id_venda,
			cod_operacao 						: cod_operacao,
			transportadora 						: ng.NF.transportadora,
			volumes 							: ng.NF.volumes,
			informacoes_adicionais_contribuinte : ng.NF.informacoes_adicionais_contribuinte,
			produtos							: ng.NF.itens
		};

		var copy_dados = angular.copy(nfTO);
			copy_dados.dados_emissao.cod_venda 			= ng.NF.dados_emissao.cod_venda;
			copy_dados.dados_emissao.cod_operacao 		= ng.NF.dados_emissao.cod_operacao;
			copy_dados.dados_emissao.local_destino 		= ng.NF.dados_emissao.local_destino;
			copy_dados.dados_emissao.tipo_documento 	= ng.NF.dados_emissao.tipo_documento;
			copy_dados.dados_emissao.forma_pagamento 	= ng.NF.dados_emissao.forma_pagamento;
			copy_dados.dados_emissao.cod_nota_fiscal 	= ng.NF.dados_emissao.cod_nota_fiscal;
			copy_dados.dados_emissao.consumidor_final 	= ng.NF.dados_emissao.consumidor_final;
			copy_dados.transportadora.modalidade_frete 	= ng.NF.transportadora.modalidade_frete;
			copy_dados.dados_emissao.finalidade_emissao = ng.NF.dados_emissao.finalidade_emissao;
			copy_dados.dados_emissao.presenca_comprador = ng.NF.dados_emissao.presenca_comprador;

		var copyNF = angular.copy(ng.NF);

		aj.post(baseUrlApi()+"nfe/calcular", {dados: JSON.stringify(post)})
			.success(function(data, status, headers, config) {
				ng.nfeCalculada 	= true;
				ng.disableSendNf 	= false;
				
				data.dados_emissao.cod_venda 			= copy_dados.dados_emissao.cod_venda;
				data.dados_emissao.cod_operacao 		= copy_dados.dados_emissao.cod_operacao;
				data.dados_emissao.local_destino 		= copy_dados.dados_emissao.local_destino;
				data.dados_emissao.tipo_documento 		= copy_dados.dados_emissao.tipo_documento;
				data.dados_emissao.forma_pagamento 		= copy_dados.dados_emissao.forma_pagamento;
				data.dados_emissao.cod_nota_fiscal 		= copy_dados.dados_emissao.cod_nota_fiscal;
				data.dados_emissao.consumidor_final 	= copy_dados.dados_emissao.consumidor_final;
				data.dados_emissao.finalidade_emissao 	= copy_dados.dados_emissao.finalidade_emissao;
				data.dados_emissao.presenca_comprador 	= copy_dados.dados_emissao.presenca_comprador;

				ng.NF 										= data;
				ng.NF.volumes 								= copyNF.volumes;
				ng.NF.transportadora 						= copyNF.transportadora;
				ng.NF.informacoes_adicionais_contribuinte 	= copyNF.informacoes_adicionais_contribuinte;

				if(event != null) {
					$('#modal-operacao').modal('hide');
					btn.button('reset');
					$('.tab-bar li a').eq(0).trigger('click');
				}
				else
					$('#modal-calculando').modal('hide');
			})
			.error(function(data, status, headers, config) {
				if(event != null)
					btn.button('reset');
				
				ng.nfeCalculada = false;
				ng.disableSendNf = true ;
				
				$('#modal-calculando').modal('hide');
				
				if(status == 406) {
					var msg = data.mensagem+"<br/>"; 
					$dialogs.error('<strong>'+msg+'</strong>');
					$('#notifyModal h4').addClass('text-warning');
					if(event != null)
						btn.button('reset');		
				}
				else
					$dialogs.notify('Desculpe!','<strong>Ocorreu um erro ao calcular a NF.</strong>');
			});
	}

	ng.loadControleNfe = function(ctr, key) {
		aj.get(baseUrlApi()+"nfe/controles/null/"+ ctr)
			.success(function(data, status, headers, config) {
				ng[key] = [{ num_item: null, nme_item: "Selecione" }];
				ng[key] = ng[key].concat(data);

				setTimeout(function(){
					$("select").trigger("chosen:updated");
				}, 300);
			})
			.error(function(data, status, headers, config) {
				ng[key] = [];
			});
	}

	ng.selTransportadora = function(){
		var item;

		$.each(ng.lista_traportadoras, function(i,v){
			if(Number(ng.NF.transportadora.id) == Number(v.id)) {
				item = v;
				return;
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

	ng.abreModalInclusaoVolume = function() {
		ng.volume = {};
		$('#modal-volume').modal('show');
	}

	ng.cancelaInclusaoVolume = function() {
		ng.volume = {};
		$('#modal-volume').modal('hide');
	}

	ng.incluiVolume = function() {
		if(empty(ng.NF.volumes))
			ng.NF.volumes = [];

		ng.NF.volumes.push(ng.volume);
		ng.volume = {};
		$('#modal-volume').modal('hide');
	}

	ng.removeVolume = function(item) {
		ng.NF.volumes = _.without(ng.NF.volumes, item);
	}

	ng.sendNfe = function(){
		var btnCalcula = $("#calcularNfe");
			btnCalcula.tooltip('destroy');

		if(!ng.nfeCalculada){
			btnCalcula.attr("data-toggle", "tooltip").attr("data-placement", "top").attr("title", 'Calcule a NF-e').attr("data-original-title", '');
			btnCalcula.tooltip('show');

			setTimeout(function(){
				btnCalcula.tooltip('destroy');
			},3000);

			return;
		}

		var btn = $(event.target) ;
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');

		var hrs_entrada_saida  	= $('#InputhrsSaida').val() ;
		var data_entrada_saida  = $('#inputDtaSaida').val() ;
			data_entrada_saida += empty(hrs_entrada_saida) ? "" : " "+ hrs_entrada_saida +":00";
			data_entrada_saida = moment(data_entrada_saida ,'DD/MM/YYYY HH:mm:ss');

		var data_emissao = moment($('#inputDtaEmissao').val(),'DD/MM/YYYY HH:mm:ss');
		
		var msg = "";
		var error = 0;

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

		ng.NF.id_empreendimento 				= ng.userLogged.id_empreendimento;
		ng.NF.dados_emissao.data_emissao  		= data_emissao.format("YYYY/MM/DD HH:mm:ss");
		ng.NF.dados_emissao.data_entrada_saida  = data_entrada_saida.format("YYYY/MM/DD HH:mm:ss");

		aj.post(baseUrlApi()+"nfe/send", ng.NF)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 202){
					ng.processando_autorizacao = true;
					$dialogs.notify('Sucesso','<strong>Nota transmitida com sucesso.</strong>');
				}
			})
			.error(function(data, status, headers, config) {
				var msg = "" ;

				if(typeof data != 'undefined') {
					$.each(data.erros,function(i,v){
						msg += v.mensagem+"<br/>";
					});
				}
			
				$dialogs.error('<strong>'+msg+'</strong>'+'<br><br><pre style="overflow:auto;height: 300px;" >'+data.json+'</pre>');
				$('#notifyModal h4').addClass('text-warning');
				btn.button('reset');		
		});
	}

	ng.loadDadosEmitente = function() {
		aj.get(baseUrlApi()+"empreendimento/"+ ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.NF.emitente = {
					CNPJ: data.num_cnpj,
					IE: data.inscricoes_estaduais[0].num_inscricao_estadual,
					IEST: data.inscricoes_estaduais[0].num_inscricao_estadual_st,
					CRT: data.num_regime_tributario,
					PercCreditoSimples: data.num_percentual_credito_simples,
					xNome: data.nme_razao_social,
					xFant: data.nme_fantasia,
					CEP: data.num_cep,
					nme_logradouro: data.nme_logradouro,
					num_logradouro: data.num_logradouro,
					nme_bairro_logradouro: data.nme_bairro_logradouro,
					estado: {
						uf: data.uf
					},
					cidade: {
						nome: data.nme_cidade
					}
				};
			});
	}

	ng.loadTransportadoras = function() {
		ng.lista_traportadoras = [{ id: '', nome_fornecedor: '' }];

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

	ng.loadOperacaoCombo = function() {
		ng.lista_operacao  = [{ cod_operacao: null, dsc_operacao: 'Selecione' }];
		aj.get(baseUrlApi()+"operacao/get/?cod_empreendimento="+ ng.userLogged.id_empreendimento +"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.lista_operacao = ng.lista_operacao.concat(data.operacao);
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(error, status, headers, config) {
				ng.lista_operacao = [];
			});
	}

	ng.loadDadosVenda = function() {
		ng.venda = null;
		aj.get(baseUrlApi()+"venda/"+ url_params.id_venda)
			.success(function(data, status, headers, config) {
				ng.venda = data;
				ng.loadItensVenda();
				ng.loadDadosDestinatario();
			});
	}

	ng.loadItensVenda = function() {
		aj.get(baseUrlApi()+"venda/itens/"+ ng.venda.id)
			.success(function(data, status, headers, config) {
				angular.forEach(data, function(item){
					item.prod = {
						cEAN: 	item.codigo_barra,
						xProd: 	item.nome_produto,
						NCM: 	item.cod_ncm,
						CFOP: 	'',
						uCom: 	item.dsc_unidade_medida,
						qCom: 	item.qtd,
						vUnCom: item.valor_real_item,
						vProd: 	item.sub_total
					};

					item.imposto = {
						ICMS: {
							CST: 0,
							CSOSN: 0,
							vBC: 0,
							vICMS: 0,
							pICMS: 0 
						},
						IPI: {
							vIPI: 0,
							pIPI: 0 
						},
						PIS: {
							vPIS: 0,
							pPIS: 0 
						},
						COFINS: {
							vCOFINS: 0,
							pCOFINS: 0 
						}
					};
				});
				ng.NF.itens = data;
			});
	}

	ng.loadDadosDestinatario = function() {
		aj.get(baseUrlApi()+"usuarios?usu->id="+ ng.venda.id_cliente)
			.success(function(data, status, headers, config) {
				data = data.usuarios[0];
				ng.NF.destinatario = {
					CNPJ: 						data.cnpj,
					CPF: 						data.cpf,
					IE: 						data.inscricao_estadual,
					IEST: 						data.inscricao_estadual_st,
					IM: 						data.num_inscricao_municipal,
					num_registro_estrangeiro: 	data.num_registro_estrangeiro,
					xFant: 						data.nome_fantasia,
					tipo_cadastro : 			data.tipo_cadastro,
					xNome: 						data.nome,
					email: 						data.email,
					CEP: 						data.cep,
					nme_logradouro: 			data.endereco,
					num_logradouro: 			data.numero,
					nme_bairro_logradouro: 		data.bairro,
					estado: {
						uf: data.uf
					},
					cidade: {
						nome: data.cidade
					}
				};
			});
	}

	ng.setDadosEmissao = function(){
		var operacao = _.findWhere(ng.lista_operacao, {cod_operacao: ng.NF.dados_emissao.cod_operacao});

		ng.NF.dados_emissao.local_destino 		= operacao.num_local_destino;
		ng.NF.dados_emissao.finalidade_emissao 	= operacao.num_finalidade_emissao;
		ng.NF.dados_emissao.consumidor_final 	= (ng.venda.tipo_cadastro == 'pf') ? "1" : "0"; // 0 - Normal | 1 - Consumidor Final
		ng.NF.dados_emissao.tipo_documento 		= (ng.venda) ? "1" : operacao.num_tipo_documento; // 0 - Nota de Entrada | 1 - Nota de Saída
		ng.NF.dados_emissao.presenca_comprador 	= operacao.num_presenca_comprador;

		angular.forEach(ng.NF.itens, function(item){
			if(empty(item['Operacao'])) {
				item['Operacao'] = {
					identificador: operacao.cod_operacao
				};
			}
		}, operacao);
	}

	ng.recalcularValorTotal = function(item){
		item.prod.vProd = parseInt(item.prod.qCom, 10) * parseFloat(item.prod.vUnCom);
	}

	ng.showDANFEModal = function(nota){
		eModal.setEModalOptions({
			loadingHtml: '<div><div style="text-align: center;margin-top:5px;margin-bottom:3px"><span class="fa fa-circle-o-notch fa-spin fa-3x text-primary"></span></div><div style="text-align: center;"><span class="h4">Carregando, aguarde...</span></div></div>'
		});
        eModal
            .iframe({
            	message: nota.caminho_danfe, 
            	title: 'DANFE NF-e Nº '+ nota.num_documento_fiscal, 
            	size: 'lg'
            })
            .then(function(){
            	t8.success('iFrame loaded!!!!', title)
        	});
	}

	if(($.isNumeric(url_params.id_venda))){
		ng.NF.dados_emissao.cod_venda = url_params.id_venda;
		
		ng.loadDadosVenda();

		if(($.isNumeric(url_params.id_venda) && $.isNumeric(url_params.cod_operacao))){
			ng.NF.dados_emissao.cod_operacao = Number(url_params.cod_operacao);
			ng.calcularNfe(null, url_params.id_venda, url_params.cod_operacao);
		}
	}
	else 
		$dialogs.notify('Desculpe!','<strong>Não foi possível calcular a NF, os paramentros estão incorretos.</strong>');
	
	ng.loadDadosEmitente();
	ng.loadTransportadoras();
	ng.loadOperacaoCombo();
	ng.loadControleNfe('local_destino', 		'lista_local_destino');
	ng.loadControleNfe('tipo_documento', 		'lista_tipo_documento');
	ng.loadControleNfe('forma_pagamento', 		'lista_forma_pagamento');
	ng.loadControleNfe('modalidade_frete', 		'lista_modalidade_frete');
	ng.loadControleNfe('consumidor_final', 		'lista_consumidor_final');
	ng.loadControleNfe('presenca_comprador', 	'lista_presenca_comprador');
	ng.loadControleNfe('finalidade_emissao', 	'lista_finalidade_emissao');

	$('#sizeToggle').trigger("click");
});
