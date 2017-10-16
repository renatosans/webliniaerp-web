app.controller('EmpreendimentoController', function($scope, $http, $window, $dialogs, UserService){
	var ng = $scope
		aj = $http;
	ng.baseUrl 			= baseUrl();

	ng.userLogged 		= UserService.getUserLogado();
	ng.empreendimento 	= {
		flg_teste:0,cod_regime_tributario:null,cod_regime_pis_cofins:null,
		cod_tipo_empresa:null,flg_contribuinte_icms:0,
		flg_contribuinte_ipi:0,cod_zoneamento:null,regime_especial:[]
	};
    ng.empreendimentos 	= [];
    ng.roleList = [];
    ng.paginacao = {} ;
    ng.busca     = {} ;

    ng.editing 	= false;

    ng.showBoxNovo = function(onlyShow){
    	//ng.editing = !ng.editing;
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
		$("select").trigger("chosen:updated");
		$('.ui-layout-toggler-north').click(function(){
		    $('#chosen_select').toggleClass('hide');
		});
	}

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.empreendimento 	= {
			flg_teste:0,cod_regime_tributario:null,cod_regime_pis_cofins:null,
			cod_tipo_empresa:null,flg_contribuinte_icms:0,
			flg_contribuinte_ipi:0,cod_zoneamento:null,regime_especial:[]
		};
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		ng.editing = false ;
	}

	ng.load = function() {
		aj.get(baseUrlApi()+"empreendimento/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.editar(data);
				ng.loadCidadesByEstado();
				$('html,body').animate({scrollTop: 0},'slow');
			})
			.error(function(data,
			 status, headers, config) {
				if(status == 404)
					ng.empreendimentos = [];
			});
	}

	ng.salvar = function() {
		ng.removeError();
		var url = 'empreendimento';
		var itemPost = {};
		var btn = $('#salvar-empreendimento');
		btn.button('loading');

		if(ng.empreendimento.id != null && ng.empreendimento.id > 0) {
			itemPost.id = ng.empreendimento.id;
			url += '/update';
		}
		itemPost                     = angular.copy(ng.empreendimento); 
		itemPost.nome_empreendimento = ng.empreendimento.nome_empreendimento;
		itemPost.id_usuario          = ng.userLogged.id;
		itemPost.flg_teste           = isNaN(Number(ng.empreendimento.flg_teste)) ? 0 : Number(ng.empreendimento.flg_teste) ;
		itemPost.qtd_dias_teste      = isNaN(Number(ng.empreendimento.qtd_dias_teste)) ? null : Number(ng.empreendimento.qtd_dias_teste) ;

		/*console.log(itemPost);
		return ;*/

		aj.post(baseUrlApi()+url, itemPost)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				$('html,body').animate({scrollTop: 0},'slow',ng.mensagens('alert-success','<strong>Alterações salvas com sucesso!</strong>'));
				
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
				}
			});
	}

	ng.editar = function(item) {
		ng.editing = true ;
		ng.empreendimento = angular.copy(item);
		/*ng.empreendimento.cod_regime_pis_cofins = ng.empreendimento.cod_regime_pis_cofins  === null ? 0 : ng.empreendimento.cod_regime_pis_cofins ;
		ng.empreendimento.cod_tipo_empresa      = ng.empreendimento.cod_tipo_empresa       === null ? 0 : ng.empreendimento.cod_tipo_empresa  ;
		ng.empreendimento.cod_zoneamento        = ng.empreendimento.cod_zoneamento         === null ? 0 : ng.empreendimento.cod_zoneamento ;
		ng.empreendimento.cod_regime_tributario = ng.empreendimento.cod_regime_tributario  === null ? 0 : ng.empreendimento.cod_regime_tributario ;*/
		ng.empreendimento.regime_especial = [] ;
		//ng.empreendimento.cod_cidade            = Number(ng.empreendimento.cod_cidade);
		//ng.empreendimento.cod_estado            = Number(ng.empreendimento.cod_estado);
		ng.showBoxNovo(true);
		ng.loadRegimeEmpreendimento(ng.empreendimento.id);
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

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.removeError = function(){
		$('.has-error').removeClass('has-error');
		$('.has-error').find('[data-toggle="tooltip"]').tooltip('destroy');
	}

	ng.loadPlanoContas = function() {
		aj.get(baseUrlApi()+"planocontas")
			.success(function(data, status, headers, config) {
				ng.roleList = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.roleList = [];
			});
	}

	ng.ClearChosenSelect = function(item){
		if(ng.empreendimento[item] == ''){
			ng.empreendimento[item] = null;
		}
	}

	ng.regimeTributario = [{cod_controle_item_nfe:'',nme_item:'--- Selecione ---'}] ;
	ng.regimePisCofins  = [{cod_controle_item_nfe:'',nme_item:'--- Selecione ---'}] ;
	ng.tipoEmpresa      = [{cod_controle_item_nfe:'',nme_item:'--- Selecione ---'}] ;
	ng.zoneamentos       = [{cod_zoneamento:'',dsc_zoneamento:'--- Selecione ---'}] ;

	ng.loadControleNfe = function(ctr,key) {
		aj.get(baseUrlApi()+"nfe/controles/null/"+ctr)
			.success(function(data, status, headers, config) {
				ng[key] = ng[key].concat(data) ;
				setTimeout(function(){$("select").trigger("chosen:updated");},300);
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

	ng.loadEstados = function () {
		ng.estados = [];

		aj.get(baseUrlApi()+"estados")
		.success(function(data, status, headers, config) {
			ng.estados = data;
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.empreendimento.cod_cidade = ""  ;
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


	ng.showModalRegimeEspecial = function(){
		$('#list-regime-especial').modal('show');
		ng.loadRegimeEspecial();

	}

	ng.selectedRegimeEspecial = function(item){
		var saida = false ;
		$.each(ng.empreendimento.regime_especial,function(i,x){
			if(Number(x.cod_regime_especial) == Number(item.cod_regime_especial)){
				saida = true ;
			}
		});
		return saida ;
	}

	ng.selRegimeEspecial = function(item){
		item = angular.copy(item);
		ng.empreendimento.regime_especial.push(item);
	}

	ng.delRegimeEspecial = function(index){
		ng.empreendimento.regime_especial.splice(index,1);
	}	

	ng.loadRegimeEspecial = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.regimes = null ;
		aj.get(baseUrlApi()+"regime_especial/get/"+offset+"/"+limit+"?cod_empreendimento="+ng.empreendimento.id+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.regimes = data.regimes;
				ng.paginacao.regimes = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.regimes = [];
					ng.paginacao.regimes = [];
				}
			});
	}

	ng.loadRegimeEmpreendimento = function (cod_empreendimento) {
		aj.get(baseUrlApi()+"regime_especial/empreendimento/get/"+cod_empreendimento)
		.success(function(data, status, headers, config) {
			ng.empreendimento.regime_especial = data;
		})
		.error(function(data, status, headers, config) {
			ng.empreendimento.regime_especial = [];
		});
	}

	ng.load();
	ng.loadZoneamento();
	ng.loadControleNfe('regime_tributario','regimeTributario');
	ng.loadControleNfe('regime_tributario_pis_cofins','regimePisCofins');
	ng.loadControleNfe('tipo_empresa','tipoEmpresa');
	ng.loadEstados();
	if(ng.userLogged.id_empreendimento != 6){
		ng.showBoxNovo(true);
	}
});
