app.controller('RegraTributosController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			= baseUrl();
	ng.userLogged 		= UserService.getUserLogado();
	ng.regra_tributos = {
		cod_empreendimento  : ng.userLogged.id_empreendimento,
		cod_filtro_tributos:null,
		cod_regra_tributos : null,
		dsc_regra_tributos : null,
		filtro_tributos : {
			cod_filtro_tributos : null, 
			cod_regra_tributos : null, 
			cod_regime_especial_destinatario : null, 
			cod_regime_especial_emitente : null, 
			ex_tipi : null, 
			cod_ncm : null, 
			cod_especializacao_ncm : null, 
			cod_operacao : null, 
			cod_situacao : null, 
			cod_zoneamento_destinatario : null, 
			cod_zoneamento_emitente : null, 
			cod_estado_origem : null, 
			cod_tipo_empresa_destinatario : null, 
			cod_tipo_empresa_emitente : null, 
			cod_forma_aquisicao : null, 
			cod_origem_mercadoria : null, 
			cod_regime_tributario_destinatario : null, 
			cod_regime_tributario_emitente : null, 
			cod_crt_emitente : null, 
			flg_cont_ipi_destinatario : 0, 
			flg_cont_ipi_emitente : 0, 
			flg_cont_icms_destinatario : 0, 
			flg_cont_icms_emitente : 0, 
			cod_destinacao : null, 
			cod_estado_destino : null, 
			dta_inicio_vigencia : null, 
			dta_fim_vigencia : null,
			num_cest:null
		},
		configuracao_icms : {
			cod_filtro_tributos : null,
			cod_cstcsosn : null,
			flg_incluir_frete_base_ipi : 0,
			flg_incluir_frete_base_icms : 0,
			flg_incluir_ipi_base_icms : 0,
			num_percentual_reducao_icms : null,
			num_percentual_reducao_icms_st : null,
			cod_modalidade_base_icms : null,
			cod_modalidade_base_icms_st : null,
			vlr_aliquota_icms : null,
			vlr_aliquota_icms_st  : null,
			vlr_aliquota_icms_proprio_st : null,
			num_percentual_mva_ajustado_st : null,
			num_percentual_mva_proprio : null,
			num_percentual_base_icms_proprio : null,
			flg_destacar_icms_st : 0,
			flg_destacar_icms_des : 0,
			cod_motivo_des_icms : null,
			tag_icms : null,
			cod_convenio_st : null,
			cod_base_tributaria : null
		},
		configuracao_ipi : {
			cod_filtro_tributos : null,
			cst_ipi : null,
			vlr_alicota : null,
		},
		configuracao_pis_cofins : {
			cod_filtro_tributos : null,
			cst_pis_cofins : null,
			vlr_aliquota_pis : null,
			vlr_aliquota_cofins : null,
			vlr_aliquota_pis_st : null,
			vlr_aliquota_cofins_st : null
		}
	}
	
	ng.busca = {ncm:""} ;
	ng.editing = false;
	ng.editing_filtro = false ;
	
	ng.paginacao = {regras:null} ;
    ng.regras	= [] ;
    ng.filtros  = [] ;



    var data = null ;

  
  
    ng.chosen_convenio_st  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
    data = 	[];
    ng.chosen_convenio_st = ng.chosen_convenio_st.concat(data);

   


    ng.chosen_estado  = [{id:null,nome:'Selecione'}] ;
    ng.loadEstados = function () {
		aj.get(baseUrlApi()+"estados")
		.success(function(data, status, headers, config) {
			 ng.chosen_estado = ng.chosen_estado.concat(data);
		})
		.error(function(data, status, headers, config) {

		});
	}
	ng.loadEstados();

	ng.chosen_zoneamento  = [{cod_zoneamento:null,dsc_zoneamento:'Selecione'}] ;
	ng.loadZoneamento = function() {
		aj.get(baseUrlApi()+"zoneamento/get?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.chosen_zoneamento = ng.chosen_zoneamento.concat(data.zoneamentos);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.chosen_zoneamento = [];
			});
	}
	ng.loadZoneamento();

	ng.chosen_base_tributaria  = [{cod_base_tributaria:'',dsc_base_tributaria:'--- Selecione ---'}] ;
	 ng.loadBaseTributaria = function() {
		aj.get(baseUrlApi()+"base_tributaria/get?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				 ng.chosen_base_tributaria = ng.chosen_base_tributaria.concat(data.bases_tributaria);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.chosen_base_tributaria = [];
			});
	}
    ng.loadBaseTributaria();
    

	ng.chosen_situacao  = [{cod_situacao_especial:'',dsc_situacao_especial:'--- Selecione ---'}] ;
    ng.loadSituacao = function() {
		aj.get(baseUrlApi()+"situacao_especial/get?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				 ng.chosen_situacao = ng.chosen_situacao.concat(data.situacoes);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.chosen_situacao = [];
			});
	}
   ng.loadSituacao();

	ng.chosen_operacao  = [{cod_operacao:null,dsc_operacao:' Selecione '}] ;
	ng.loadOperacao = function() {
		aj.get(baseUrlApi()+"operacao/get?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				 ng.chosen_operacao = ng.chosen_operacao.concat(data.operacao);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.chosen_operacao = [];
			});
	}
    ng.loadOperacao();

	ng.chosen_regime_especial_destinatario  = [{cod_regime_especial:'',dsc_regime_especial:'--- Selecione ---'}] ;
    ng.loadRegimeDestinatario = function() {
		aj.get(baseUrlApi()+"regime_especial/get?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				 ng.chosen_regime_especial_destinatario = ng.chosen_regime_especial_destinatario.concat(data.regimes);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.chosen_regime_especial_destinatario = [];
			});
	}
	ng.loadRegimeDestinatario();
  	
    ng.loadRegimeEmitente = function() {
    	ng.chosen_regime_especial_emitente  = [] ;
		aj.get(baseUrlApi()+"regime_especial/get?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.chosen_regime_especial_emitente  = [{cod_regime_especial:null,dsc_regime_especial:''}] ;
				 ng.chosen_regime_especial_emitente = ng.chosen_regime_especial_emitente.concat(data.regimes);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.chosen_regime_especial_emitente = [];
			});
	}
    ng.loadRegimeEmitente();

    ng.loadEspecialazacaoNcm = function() {
    	ng.chosen_especializacao_ncm =[{cod_especializacao_ncm:null,dsc_especializacao_ncm:''}] ;
		aj.get(baseUrlApi()+"especializacao_ncm/get?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.chosen_especializacao_ncm  = [{cod_especializacao_ncm:null,dsc_especializacao_ncm:''}] ;
				ng.chosen_especializacao_ncm = ng.chosen_especializacao_ncm.concat(data.especializacao_ncm) ;
			})
			.error(function(data, status, headers, config) {
				
		});
	}
	ng.loadEspecialazacaoNcm();

	ng.chosen_cstcsosn  = [] ;
    ng.loadCstcsosn = function() {
		aj.get(baseUrlApi()+"cstcsosn/get")
			.success(function(data, status, headers, config) {
				ng.chosen_cstcsosn = ng.chosen_cstcsosn.concat(data) ;
			})
			.error(function(data, status, headers, config) {
				ng.chosen_cstcsosn = [] ;
		});
	}
	ng.loadCstcsosn();


    ng.loadControleNfe = function(ctr,key) {
		aj.get(baseUrlApi()+"nfe/controles/null/"+ctr)
			.success(function(data, status, headers, config) {
				ng[key] = ng[key].concat(data) ;
			})
			.error(function(data, status, headers, config) {
				
		});
	}

	ng.chosen_cst_ipi       = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
    ng.loadControleNfe('cst_ipi','chosen_cst_ipi');

    ng.chosen_pis_cofins    = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
    ng.loadControleNfe('cst_pis_cofins','chosen_pis_cofins');

    ng.chosen_tipo_empresa  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
    ng.loadControleNfe('tipo_empresa','chosen_tipo_empresa');

    ng.chosen_regime_tributario_emitente  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
    ng.loadControleNfe('regime_tributario','chosen_regime_tributario_emitente');

    ng.chosen_regime_tributario_destinatario  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
    ng.loadControleNfe('regime_tributario','chosen_regime_tributario_destinatario');

    ng.chosen_origem_mercadoria  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
    ng.loadControleNfe('origem_mercadoria','chosen_origem_mercadoria');

    ng.chosen_forma_aquisicao  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
   	ng.loadControleNfe('forma_aquisicao','chosen_forma_aquisicao');

   	ng.chosen_cod_destinacao  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
   	ng.loadControleNfe('destinacao','chosen_cod_destinacao');

   	ng.chosen_crt_emitente  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
    ng.loadControleNfe('crt_emitente','chosen_crt_emitente');

    ng.chosen_base_icms  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
    ng.loadControleNfe('modalidade_base_icms','chosen_base_icms');

    ng.chosen_base_icms_st  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
   	ng.loadControleNfe('modalidade_base_icms_st','chosen_base_icms_st');

   	ng.chosen_motivo_des_icms  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
   	ng.loadControleNfe('motivo_des_icms','chosen_motivo_des_icms');

   	ng.chosen_tributacao_ipi  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
   	ng.loadControleNfe('tipo_tributacao_ipi','chosen_tributacao_ipi');

   	ng.chosen_tributacao_pis_cofins  = [{cod_controle_item_nfe:null,num_item:null,nme_item:'Selecione',dsc_completa:'Selecione'}] ;
   	ng.loadControleNfe('tipo_tributacao_pis_cofins','chosen_tributacao_pis_cofins');


   	


    


    ng.showBoxNovo = function(onlyShow){
    	if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show();
			ng.editing = true;
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
		$("select").trigger("chosen:updated");
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.resetFiltro = function(){
		ng.regra_tributos.filtro_tributos = {
			cod_filtro_tributos : null, 
			cod_regra_tributos : ng.regra_tributos.cod_regra_tributos, 
			cod_regime_especial_destinatario : null, 
			cod_regime_especial_emitente : null, 
			ex_tipi : null, 
			cod_ncm : null, 
			cod_especializacao_ncm : null, 
			cod_operacao : null, 
			cod_situacao : null, 
			cod_zoneamento_destinatario : null, 
			cod_zoneamento_emitente : null, 
			cod_estado_origem : null, 
			cod_tipo_empresa_destinatario : null, 
			cod_tipo_empresa_emitente : null, 
			cod_forma_aquisicao : null, 
			cod_origem_mercadoria : null, 
			cod_regime_tributario_destinatario : null, 
			cod_regime_tributario_emitente : null, 
			cod_crt_emitente : null, 
			flg_cont_ipi_destinatario : 0, 
			flg_cont_ipi_emitente : 0, 
			flg_cont_icms_destinatario : 0, 
			flg_cont_icms_emitente : 0, 
			cod_destinacao : null, 
			cod_estado_destino : null, 
			dta_inicio_vigencia : null, 
			dta_fim_vigencia : null
		};
		ng.regra_tributos.configuracao_icms =  {
			cod_filtro_tributos : ng.regra_tributos.cod_regra_tributos,
			cod_cstcsosn : null,
			flg_incluir_frete_base_ipi : 0,
			flg_incluir_frete_base_icms : 0,
			flg_incluir_ipi_base_icms : 0,
			num_percentual_reducao_icms : null,
			num_percentual_reducao_icms_st : null,
			cod_modalidade_base_icms : null,
			cod_modalidade_base_icms_st : null,
			vlr_aliquota_icms : null,
			vlr_aliquota_icms_st  : null,
			vlr_aliquota_icms_proprio_st : null,
			num_percentual_mva_ajustado_st : null,
			num_percentual_mva_proprio : null,
			num_percentual_base_icms_proprio : null,
			flg_destacar_icms_st : 0,
			flg_destacar_icms_des : 0,
			cod_motivo_des_icms : null,
			tag_icms : null,
			cod_convenio_st : null,
			cod_base_tributaria : null
		},
		ng.regra_tributos.configuracao_ipi = {
			cod_filtro_tributos : ng.regra_tributos.cod_regra_tributos,
			cst_ipi : null,
			vlr_alicota : null,
		}
		ng.regra_tributos.configuracao_pis_cofins = {
			cod_filtro_tributos : ng.regra_tributos.cod_regra_tributos,
			cst_pis_cofins : null,
			vlr_aliquota_pis : null,
			vlr_aliquota_cofins : null,
			vlr_aliquota_pis_st : null,
			vlr_aliquota_cofins_st : null
		}
		ng.editing = false;
		ng.editing_filtro = true ;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.reset = function() {
		ng.regra_tributos = {
			cod_empreendimento  : ng.userLogged.id_empreendimento,
			cod_filtro_tributos:null,
			cod_regra_tributos : null,
			dsc_regra_tributos : null,
			filtro_tributos : {
				cod_filtro_tributos : null, 
				cod_regra_tributos : null, 
				cod_regime_especial_destinatario : null, 
				cod_regime_especial_emitente : null, 
				ex_tipi : null, 
				cod_ncm : null, 
				cod_especializacao_ncm : null, 
				cod_operacao : null, 
				cod_situacao : null, 
				cod_zoneamento_destinatario : null, 
				cod_zoneamento_emitente : null, 
				cod_estado_origem : null, 
				cod_tipo_empresa_destinatario : null, 
				cod_tipo_empresa_emitente : null, 
				cod_forma_aquisicao : null, 
				cod_origem_mercadoria : null, 
				cod_regime_tributario_destinatario : null, 
				cod_regime_tributario_emitente : null, 
				cod_crt_emitente : null, 
				flg_cont_ipi_destinatario : 0, 
				flg_cont_ipi_emitente : 0, 
				flg_cont_icms_destinatario : 0, 
				flg_cont_icms_emitente : 0, 
				cod_destinacao : null, 
				cod_estado_destino : null, 
				dta_inicio_vigencia : null, 
				dta_fim_vigencia : null
				},
				configuracao_icms : {
					cod_filtro_tributos : null,
					cod_cstcsosn : null,
					flg_incluir_frete_base_ipi : 0,
					flg_incluir_frete_base_icms : 0,
					flg_incluir_ipi_base_icms : 0,
					num_percentual_reducao_icms : null,
					num_percentual_reducao_icms_st : null,
					cod_modalidade_base_icms : null,
					cod_modalidade_base_icms_st : null,
					vlr_aliquota_icms : null,
					vlr_aliquota_icms_st  : null,
					vlr_aliquota_icms_proprio_st : null,
					num_percentual_mva_ajustado_st : null,
					num_percentual_mva_proprio : null,
					num_percentual_base_icms_proprio : null,
					flg_destacar_icms_st : 0,
					flg_destacar_icms_des : 0,
					cod_motivo_des_icms : null,
					tag_icms : null,
					cod_convenio_st : null,
					cod_base_tributaria : null
				},
				configuracao_ipi : {
					cod_filtro_tributos : null,
					cst_ipi : null,
					vlr_alicota : null,
				},
				configuracao_pis_cofins : {
					cod_filtro_tributos : null,
					cst_pis_cofins : null,
					vlr_aliquota_pis : null,
					vlr_aliquota_cofins : null,
					vlr_aliquota_pis_st : null,
					vlr_aliquota_cofins_st : null
				}
		}
		ng.editing = false;
		ng.editing_filtro = false ;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.load = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.regras = null ;
		aj.get(baseUrlApi()+"regra_tributos/"+offset+"/"+limit+"?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.regras = data.regras;
				ng.paginacao.regras = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.regras = [];
					ng.paginacao.regras = [];
				}
			});
	}

	ng.salvar = function() {
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')

		var btn = $("#salvar-regra-tributos") ;
		btn.button('loading');
		var url = 'regra_tributos';
		var itemPost = {};
		var msg = "Regra salva com sucesso!";

		if(ng.regra_tributos.cod_regra_tributos != null && ng.regra_tributos.cod_regra_tributos > 0) {
			url += '/update';
			msg = 'Regra alterada com sucesso!'
		}

		
		itemPost = angular.copy(ng.regra_tributos);

		aj.post(baseUrlApi()+url, itemPost)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>'+msg+'</strong>','.alert-list');
				ng.showBoxNovo();
				ng.reset();
				ng.load();
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406) {
					var errors = data;
					$.each(errors, function(i, item) {
						$("#"+i).addClass("has-error");
						var formControl = $("#"+i)
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});
					$('html,body').animate({scrollTop: $('.has-error').eq(0).offset().top-50},'slow');
					$('.has-error').eq(0).tooltip('show');
				}else{
					ng.mensagens('alert-danger','<strong>Erro ao efetuar cadastro!</strong>');
					$('html,body').animate({scrollTop: 0},'slow');
				}
			});
	}

	ng.loadFiltros = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.filtros = null ;
		aj.get(baseUrlApi()+"filtro_tributos/"+offset+"/"+limit+"?cod_regra_tributos="+ng.regra_tributos.cod_regra_tributos+"&tft->flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.filtros = data.filtros;
				ng.paginacao.filtros = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.filtros = [];
					ng.paginacao.filtros = [];
				}
			});
	}

	ng.salvarFiltro = function() {
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')

		var btn = $("#salvar-filtros-tributos") ;
		btn.button('loading');
		var url = 'filtro_tributos';
		var filtro_tributos   		 = {} ;
		var configuracao_icms 		 = {} ;
		var configuracao_ipi  		 = {} ;
		var configuracao_pis_cofins  = {} ;
		var msg = "Filtro salva com sucesso!";

		filtro_tributos = angular.copy(ng.regra_tributos.filtro_tributos);
		configuracao_icms = angular.copy(ng.regra_tributos.configuracao_icms);
		configuracao_ipi = angular.copy(ng.regra_tributos.configuracao_ipi);
		configuracao_pis_cofins =  angular.copy(ng.regra_tributos.configuracao_pis_cofins);
		if(ng.regra_tributos.filtro_tributos.cod_filtro_tributos != null && ng.regra_tributos.filtro_tributos.cod_filtro_tributos > 0) {
			url += '/update';
			msg = 'Filtro alterada com sucesso!';
		}
		filtro_tributos.dta_inicio_vigencia = empty(filtro_tributos.dta_inicio_vigencia)  ? null : formatDate(uiDateFormat(filtro_tributos.dta_inicio_vigencia,'99/99/999')) ;
		filtro_tributos.dta_fim_vigencia    = empty(filtro_tributos.dta_fim_vigencia) 	? null : formatDate(uiDateFormat(filtro_tributos.dta_fim_vigencia,'99/99/999')) ;

		var itemPost = {
			filtro_tributos    		: filtro_tributos,
			configuracao_icms  		: configuracao_icms,
			configuracao_ipi   		: configuracao_ipi,
			configuracao_pis_cofins : configuracao_pis_cofins
		}
		aj.post(baseUrlApi()+url, itemPost)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success','<strong>'+msg+'</strong>','.alert-list');
				ng.showBoxNovo();
				ng.resetFiltro();
				ng.loadFiltros();
				$('html,body').animate({scrollTop: 0},'slow');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406) {
					var errors = data;
					$.each(errors, function(i, item) {
						$("#"+i).addClass("has-error");
						var formControl = $("#"+i)
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item[0])
							.attr("data-original-title", item[0]);
						formControl.tooltip();
					});
					$('html,body').animate({scrollTop: $('.has-error').eq(0).offset().top-50},'slow');
					$('.has-error').eq(0).tooltip('show');
				}else{
					ng.mensagens('alert-danger','<strong>Erro ao efetuar cadastro!</strong>');
					$('html,body').animate({scrollTop: 0},'slow');
				}
			});
	}

	ng.editar = function(item) {
		$('html,body').animate({scrollTop: 0},'slow');
		ng.regra_tributos = angular.copy(item);
		ng.regra_tributos.filtro_tributos =  {
			cod_filtro_tributos : null, 
			cod_regra_tributos : item.cod_regra_tributos, 
			cod_regime_especial_destinatario : null, 
			cod_regime_especial_emitente : null, 
			ex_tipi : null, 
			cod_ncm : null, 
			cod_especializacao_ncm : null, 
			cod_operacao : null, 
			cod_situacao : null, 
			cod_zoneamento_destinatario : null, 
			cod_zoneamento_emitente : null, 
			cod_estado_origem : null, 
			cod_tipo_empresa_destinatario : null, 
			cod_tipo_empresa_emitente : null, 
			cod_forma_aquisicao : null, 
			cod_origem_mercadoria : null, 
			cod_regime_tributario_destinatario : null, 
			cod_regime_tributario_emitente : null, 
			cod_crt_emitente : null, 
			flg_cont_ipi_destinatario : 0, 
			flg_cont_ipi_emitente : 0, 
			flg_cont_icms_destinatario : 0, 
			flg_cont_icms_emitente : 0, 
			cod_destinacao : null, 
			cod_estado_destino : null, 
			dta_inicio_vigencia : null, 
			dta_fim_vigencia : null
		}
		ng.regra_tributos.configuracao_icms = {
			cod_filtro_tributos : null,
			cod_cstcsosn : null,
			flg_incluir_frete_base_ipi : 0,
			flg_incluir_frete_base_icms : 0,
			flg_incluir_ipi_base_icms : 0,
			num_percentual_reducao_icms : null,
			num_percentual_reducao_icms_st : null,
			cod_modalidade_base_icms : null,
			cod_modalidade_base_icms_st : null,
			vlr_aliquota_icms : null,
			vlr_aliquota_icms_st  : null,
			vlr_aliquota_icms_proprio_st : null,
			num_percentual_mva_ajustado_st : null,
			num_percentual_mva_proprio : null,
			num_percentual_base_icms_proprio : null,
			flg_destacar_icms_st : 0,
			flg_destacar_icms_des : 0,
			cod_motivo_des_icms : null,
			tag_icms : null,
			cod_convenio_st : null,
			cod_base_tributaria : null
		},
		ng.regra_tributos.configuracao_ipi = {
			cod_filtro_tributos : null,
			cst_ipi : null,
			vlr_alicota : null,
		}
		ng.regra_tributos.configuracao_pis_cofins = {
			cod_filtro_tributos : null,
			cst_pis_cofins : null,
			vlr_aliquota_pis : null,
			vlr_aliquota_cofins : null,
			vlr_aliquota_pis_st : null,
			vlr_aliquota_cofins_st : null
		}
		ng.showBoxNovo(true);
	}

	ng.loadProdutoCliente = function(cod_situacao_especial) {
		aj.get(baseUrlApi()+"situacao_especial/produto_cliente/"+cod_situacao_especial)
			.success(function(data, status, headers, config) {
				ng.situacaoEspecial.produto_cliente = data ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.situacaoEspecial.produto_cliente = [] ;
				}
			});
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Excluir Esta Regra Tributaria ?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"regra_tributos/delete/"+item.cod_regra_tributos)
				.success(function(data, status, headers, config) {
					ng.load();
					ng.mensagens('alert-success','<strong>Regra excluida com sucesso</strong>','.alert-list');
					ng.reset();
				})
				.error(function(data, status, headers, config) {
					ng.mensagens('alert-danger','<strong>Erro ao excluir</strong>','.alert-list');
				});
		}, undefined);
	}

	ng.deleteFiltro = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Excluir Esté Filtro ?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"filtro_tributos/delete/"+item.cod_filtro_tributos)
				.success(function(data, status, headers, config) {
					ng.loadFiltros();
					ng.mensagens('alert-success','<strong>Filtro excluida com sucesso</strong>','.alert-list');
					ng.resetFiltro();
				})
				.error(function(data, status, headers, config) {
					ng.mensagens('alert-danger','<strong>Erro ao excluir</strong>','.alert-list');
				});
		}, undefined);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.selProduto = function(busca_cdb){
   		ng.busca.produtos = "" ;
   		ng.loadProduto(0,10);
   		$('#list_produtos').modal('show');
   	}

   	ng.loadProduto = function(offset,limit) {
		ng.produtos = [];

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.produtos != ""){
			if(isNaN(Number(ng.busca.produtos)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produtos+"%' OR codigo_barra like '%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produtos+"%' OR codigo_barra like '%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%' OR pro.id = "+ng.busca.produtos+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos = data.produtos;
				ng.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.produtos = [];
					ng.paginacao.produtos = [];
				}
			});
	}

	ng.addProduto = function(item){
		ng.produto_cliente.cod_produto = item.id ;
		ng.produto_cliente.nme_produto = item.nome ;
		$("#list_produtos").modal("hide");
	}

	ng.selCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

		ng.loadCliente(offset,limit);
		$("#list_clientes").modal("show");
	}


	ng.addCliente = function(item){
		ng.produto_cliente.cod_cliente = item.id ;
		ng.produto_cliente.nme_cliente = item.nome ;
		$("#list_clientes").modal("hide");
	}

	ng.loadCliente= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+"&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.clientes+"%' OR usu.apelido LIKE '%"+ng.busca.clientes+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.usuarios,function(i,item){
					ng.clientes.push(item);
				});
				ng.paginacao_clientes = [];
				$.each(data.paginacao,function(i,item){
					ng.paginacao_clientes.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				ng.clientes = false ;
			});
	}

	ng.produto_cliente = {
			cod_situacao_especial 	: null,
			cod_produto 			: null,
			cod_cliente 			: null,
			dsc_texto_legal 		: null
	}

	ng.incluirProdutoCliente = function(){
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')
		var error = 0 ;
		if(empty(ng.produto_cliente.cod_cliente)){
			$("#produto-cliente-nme_cliente").addClass("has-error");
			var formControl = $("#produto-cliente-nme_cliente")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Selecione um cliente")
				.attr("data-original-title", "Selecione um cliente");
			formControl.tooltip();
			error ++ ;
		}	
		if(empty(ng.produto_cliente.cod_produto)){
			$("#produto-cliente-nme_produto").addClass("has-error");
			var formControl = $("#produto-cliente-nme_produto")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Selecione um produto")
				.attr("data-original-title", "Selecione um produto");
			formControl.tooltip();
			error ++ ;
		}

		if(error > 0)	
			return

		if(ng.editingProdutoCliente){
			ng.situacaoEspecial.produto_cliente[ng.produto_cliente.index] = {
				cod_situacao_especial_produto_cliente : ng.produto_cliente.cod_situacao_especial_produto_cliente,
				cod_situacao_especial 	: ng.produto_cliente.cod_situacao_especial,
				cod_produto 			: ng.produto_cliente.cod_produto,
				cod_cliente 			: ng.produto_cliente.cod_cliente,
				dsc_texto_legal 		: ng.produto_cliente.dsc_texto_legal,
				nme_cliente             : ng.produto_cliente.nme_cliente,
				nme_produto             : ng.produto_cliente.nme_produto
			};
		}else{
			ng.situacaoEspecial.produto_cliente.push({
				cod_situacao_especial 	: null,
				cod_produto 			: ng.produto_cliente.cod_produto,
				cod_cliente 			: ng.produto_cliente.cod_cliente,
				dsc_texto_legal 		: ng.produto_cliente.dsc_texto_legal,
				nme_cliente             : ng.produto_cliente.nme_cliente,
				nme_produto             : ng.produto_cliente.nme_produto
			});
		}

		ng.produto_cliente = {
			cod_situacao_especial 	: null,
			cod_produto 			: null,
			cod_cliente 			: null,
			dsc_texto_legal 		: null
		}

		ng.editingProdutoCliente = false ;
	}

	ng.itemEditing = function(index){
		return (Number(ng.produto_cliente.index) == Number(index) && ng.editingProdutoCliente) ;
	}

	ng.delProdutoCliente = function(index){
		ng.situacaoEspecial.produto_cliente.splice(index,1);
	}

	ng.editarProdutoCliente = function(item,index){
		ng.produto_cliente = {
			cod_situacao_especial_produto_cliente : item.cod_situacao_especial_produto_cliente,
			cod_situacao_especial 				  : item.cod_situacao_especial,
			cod_produto 						  : item.cod_produto,
			cod_cliente 						  : item.cod_cliente,
			dsc_texto_legal 					  : item.dsc_texto_legal,
			nme_produto             			  : item.nme_produto,
			nme_cliente 						  : item.nme_cliente,
			index                   			  : index
		}
		ng.editingProdutoCliente = true ;
	}

	ng.ClearChosenSelect = function(item){
		if(ng.regra_tributos.filtro_tributos[item] == ''){
			ng.regra_tributos.filtro_tributos[item] = null;
		}
	}

	ng.viewFiltros = function(x){
		ng.editing_filtro = x ;
		ng.loadFiltros();
		if(!x)
			ng.showBoxNovo(true);	
		else
			ng.showBoxNovo();
		$('html,body').animate({scrollTop: 0},'slow');
	}

	ng.editarFiltro = function(item){
		ng.regra_tributos.filtro_tributos = {} ;
		item = angular.copy(item);
		var configuracao_icms = null ;
		if(item.configuracao_icms == false){
			configuracao_icms = {
				cod_filtro_tributos : item.cod_filtro_tributos,
				cod_cstcsosn : null,
				flg_incluir_frete_base_ipi : 0,
				flg_incluir_frete_base_icms : 0,
				flg_incluir_ipi_base_icms : 0,
				num_percentual_reducao_icms : null,
				num_percentual_reducao_icms_st : null,
				cod_modalidade_base_icms : null,
				cod_modalidade_base_icms_st : null,
				vlr_aliquota_icms : null,
				vlr_aliquota_icms_st  : null,
				vlr_aliquota_icms_proprio_st : null,
				num_percentual_mva_ajustado_st : null,
				num_percentual_mva_proprio : null,
				num_percentual_base_icms_proprio : null,
				flg_destacar_icms_st : 0,
				flg_destacar_icms_des : 0,
				cod_motivo_des_icms : null,
				tag_icms : null,
				cod_convenio_st : null,
				cod_base_tributaria : null
			}
		}else{
			configuracao_icms = item.configuracao_icms ;
		}
		delete item.configuracao_icms ; 

		var configuracao_ipi = null ;
		if(item.configuracao_ipi == false){
			configuracao_ipi = {
				cod_filtro_tributos : item.cod_filtro_tributos,
				cst_ipi : null,
				vlr_alicota : 0,
			}
		}else{
			configuracao_ipi = item.configuracao_ipi ;
		}
		delete item.configuracao_ipi ; 

		var configuracao_pis_cofins = null ;
		if(item.configuracao_pis_cofins == false){
			configuracao_pis_cofins = {
				cod_filtro_tributos : item.cod_filtro_tributos,
				cst_pis_cofins : null,
				vlr_aliquota_pis : null,
				vlr_aliquota_cofins : null,
				vlr_aliquota_pis_st : null,
				vlr_aliquota_cofins_st : null
			}
		}else{
			configuracao_pis_cofins = item.configuracao_pis_cofins ;
		}
		delete item.configuracao_pis_cofins ;

		item.cod_especializacao_ncm = Number(item.cod_especializacao_ncm);
		ng.regra_tributos.filtro_tributos   = item ;
		ng.regra_tributos.configuracao_icms = configuracao_icms ;
		ng.regra_tributos.configuracao_ipi = configuracao_ipi ;
		ng.regra_tributos.configuracao_pis_cofins = configuracao_pis_cofins ;
		ng.regra_tributos.filtro_tributos.dta_inicio_vigencia = formatDateBR(ng.regra_tributos.filtro_tributos.dta_inicio_vigencia);
		ng.regra_tributos.filtro_tributos.dta_fim_vigencia    = formatDateBR(ng.regra_tributos.filtro_tributos.dta_fim_vigencia);
		ng.regra_tributos.filtro_tributos.ncm_view = item.cod_ncm+" - "+item.dsc_ncm ;

		$('html,body').animate({scrollTop: 0},'slow');
		ng.showBoxNovo(true);
		
	}

	ng.selNcm = function(){
		$('#list-ncm').modal('show');
		ng.loadNcm(0,10);
	}

	ng.changeNcm = function(item){
		ng.regra_tributos.filtro_tributos.cod_ncm      = item.cod_ncm ;
		ng.regra_tributos.filtro_tributos.ncm_view 	= item.cod_ncm +" - "+item.dsc_ncm ;
		$('#list-ncm').modal('hide');
	}

	ng.loadNcm = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.lista_ncm = [];
		var queryString = "" ;
		queryString += empty(ng.busca.ncm) ? "" : "?"+$.param({'(cod_ncm':{exp:"LIKE'%"+ng.busca.ncm+"%' OR dsc_ncm LIKE '%"+ng.busca.ncm+"%')"}}) ; 

		aj.get(baseUrlApi()+"ncm/"+offset+"/"+limit+"/"+queryString)
			.success(function(data, status, headers, config) {
				ng.lista_ncm = data.ncm;
				ng.paginacao.especializacao_ncm = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.clientes = false ;
			});
	}

	ng.cancelarModal = function(id){
		$('#'+id).modal('hide');
	}

	ng.loadConfig = function(){
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracoes = data ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.caixa_configurado = false ;
				}
			});
	}

	ng.load(0,10);
	ng.loadConfig() ;

});

app.directive('bsTooltip', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            $timeout(function () {
                	  element.find("[data-toggle=tooltip]").tooltip();
            });
        }
    }
});
