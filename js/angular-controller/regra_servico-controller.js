app.controller('RegraServicoController', function($scope, $http, $window, $dialogs, UserService){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
    ng.editing = false;
    ng.busca = {};
    var regraServicoTO = {
    	id 							: null,
		nme_regra_servico  			: null,
		cod_empreendimento  		: ng.userLogged.id_empreendimento,
		cod_estado 					: null,
		cod_municipio 				: null,
		flg_retem_iss_pf 			: null,
		flg_retem_iss_pj 			: null,
		prc_retencao_iss 			: null,
		vlr_minimo_retencao_iss 	: null,
		flg_retem_inss 				: null,
		prc_retencao_inss 			: null,
		vlr_minimo_retencao_inss 	: null,
		flg_retem_pis 				: null,
		prc_retencao_pis 			: null,
		vlr_minimo_retencao_pis 	: null,
		flg_retem_cofins			: null,
		prc_retencao_cofins			: null,
		vlr_minimo_retencao_cofins 	: null,
		flg_retem_csll				: null,
		prc_retencao_csll			: null,
		vlr_minimo_retencao_csll	: null,
		flg_retem_ir				: null,
		prc_retencao_ir				: null,
		vlr_minimo_retencao_ir		: null
    }; 

    ng.regra_servico = angular.copy(regraServicoTO);


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
		$(alertClass).fadeIn().removeClass('alert-success alert-danger alert-warning').addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.regra_servico = angular.copy(regraServicoTO);
	}

	ng.loadEstados = function () {
		ng.estados = [];

		aj.get(baseUrlApi()+"estados")
		.success(function(data, status, headers, config) {
			ng.estados = data;
			setTimeout(function(){$("select").trigger("chosen:updated");},300);
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.loadCidadesByEstado = function (id_estado) {
		aj.get(baseUrlApi()+"cidades/"+id_estado)
		.success(function(data, status, headers, config) {
			ng.municipios = [{id:null,nome:'Selecione'}].concat(data);
			setTimeout(function(){$("select").trigger("chosen:updated");},300);
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.changeEstado = function(id_estado){
		ng.regra_servico.cod_municipio = null ;
		ng.loadCidadesByEstado(id_estado);
	}


	ng.salvarRegra = function() {
		clearValidationFormStyle();
		var btn = $('#btn_salvar');
		btn.button('loading');
		var post = angular.copy(ng.regra_servico);
		var msg = "<b>Regra criada com sucesso</b>" ;
		var url = "regra_servico";

		if(!empty(post.id)){
			var msg = "<b>Regra atualizada com sucesso</b>" ;
			var url = "regra_servico/update";	
			var id = post.id ;
			delete post.id ;
			post = {
				dados : post,
				where : 'id='+id
			}
		}

		aj.post(baseUrlApi()+url,post)
		.success(function(data, status, headers, config) {
			ng.busca.text = null ;
		    ng.loadRegras();
			ng.showBoxNovo();
			ng.reset();
			$('html,body').animate({scrollTop: 0},'slow');
			ng.mensagens('alert-success' , msg, '.alert-lista');
			btn.button('reset');	
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			if(status == 406){
				applyFormErrors(data,'regra_servico')
			}else{
				ng.mensagens('alert-danger' , '<b>Ocorreu um erro ao efetuar a operação</b>', '.alert-top');
			}
		});
	}

	ng.editar = function(item){
		ng.showBoxNovo(true);
		ng.regra_servico = angular.copy(item);
		ng.loadCidadesByEstado(ng.regra_servico.cod_estado);
		$('html,body').animate({scrollTop: 0},'slow');

	}

	ng.delete = function(id){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir esta regra?</strong>');
		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"regra_servico/delete/"+id)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>Regra excluida com sucesso</strong>', '.alert-lista');
				ng.loadRegras();
			})
			.error(function(data, status, headers, config) {
				ng.mensagens('alert-danger','<strong>Erro ao excluir regra</strong>', '.alert-lista');
			});
		}, undefined);
	}

	ng.loadRegras = function (offset,limit) {
		offset = empty(offset) ? 0 : offset ;
		limit = empty(limit) ? 10 :  limit ;
		ng.regrasCadastradas = null ;

		var queryString = "?cplSql= WHERE trs.cod_empreendimento = "+ng.userLogged.id_empreendimento+" AND trs.flg_excluido = 0";

		if(!empty(ng.busca.text)){
			var busca_like = ng.busca.text.replace(/\s/g, '%');
			queryString+= " AND (mi.nome LIKE '%"+busca_like+"%' OR te.uf LIKE '%"+busca_like+"%' OR te.nome LIKE '%"+busca_like+"%')" ;
		}
		

		aj.get(baseUrlApi()+"regras_servico/"+offset+"/"+limit+encodeURI(queryString) )
		.success(function(data, status, headers, config) {
			ng.regrasCadastradas = data ;
		})
		.error(function(data, status, headers, config) {
			ng.regrasCadastradas = {regras:[],paginacao:[]} ;
		});
	}

	ng.loadRegras();
	ng.loadEstados();

});
