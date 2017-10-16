app.controller('BaseTributariaController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			= baseUrl();
	ng.userLogged 		= UserService.getUserLogado();
	ng.base_tributaria = {
		cod_base_tributaria		: null,
		dsc_base_tributaria 	: null,
		base_tributaria_itens 	: [] 
	};
	ng.base_tributaria_item = {
			cod_base_tributaria_item 			: null,
			cod_base_tributaria 	 			: null,
			vlr_base_calculo_icms 				: null,
			vlr_base_calculo_icms_st 			: null,
			cod_tipo_tributacao_ipi 			: null,
			vlr_base_calculo_ipi 				: null,
			cod_tipo_tributacao_pis_cofins_st 	: null,
			vlr_pis 							: null,
			vlr_cofins 							: null,
			vlr_pis_st 							: null,
			vlr_cofins_st 						: null,
			nome_produto                        : null,
			cod_produto 						: null
	}
	ng.busca = {clientes:'',produtos:''} ;
	ng.editingBaseTributaria = false ;
	ng.editing = false;
	
	ng.paginacao = {situacoes:null} ;
    ng.situacoes	= [];
  

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
			$("select").trigger("chosen:updated");
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.base_tributaria = {
			cod_base_tributaria		: null,
			dsc_base_tributaria 	: null,
			base_tributaria_itens 	: [] 
		};
		ng.editing = false;
		ng.editingBaseTributaria = false ;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.load = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.bases_tributaria = null ;
		aj.get(baseUrlApi()+"base_tributaria/get/"+offset+"/"+limit+"?flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.bases_tributaria = data.bases_tributaria;
				ng.paginacao.bases_tributaria = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.bases_tributaria = [];
					ng.paginacao.bases_tributaria = [];
				}
			});
	}

	ng.salvar = function() {
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')

		var btn = $("#salvar-base-tributaria") ;
		btn.button('loading');
		var url = 'base_tributaria';
		var itemPost = {};
		var msg = "Base Tributária salva com sucesso!";

		if(ng.base_tributaria.cod_base_tributaria != null && ng.base_tributaria.cod_base_tributaria > 0) {
			url += '/update';
			msg = 'Base Tributária alterada com sucesso!'
		}

		itemPost = angular.copy(ng.base_tributaria);
		itemPost.cod_empreendimento = ng.userLogged.id_empreendimento ;

		aj.post(baseUrlApi()+url, itemPost)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>'+msg+'</strong>','.alert-list');
				ng.showBoxNovo();
				ng.reset();
				ng.load();
				btn.button('reset');
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

	ng.editar = function(item) {
		$('html,body').animate({scrollTop: 0},'slow');
		ng.base_tributaria = angular.copy(item);
		ng.base_tributaria.base_tributaria_itens = null ;
		ng.showBoxNovo(true);
		ng.loadBaseTrinutariaItens(ng.base_tributaria.cod_base_tributaria);
	}

	ng.loadBaseTrinutariaItens = function(cod_base_tributaria) {
		aj.get(baseUrlApi()+"base_tributaria/base_tributaria_itens/"+cod_base_tributaria)
			.success(function(data, status, headers, config) {
				ng.base_tributaria.base_tributaria_itens = data ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.base_tributaria.base_tributaria_itens = [] ;
				}
			});
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Excluir Esta Base Tributáruia ?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"base_tributaria/delete/"+item.cod_base_tributaria)
				.success(function(data, status, headers, config) {
					ng.load();
					ng.mensagens('alert-success','<strong>Base Tributáruia excluido com sucesso</strong>','.alert-list');
					ng.reset();
				})
				.error(function(data, status, headers, config) {
					ng.mensagens('alert-danger','<strong>Erro ao excluir</strong>','.alert-list');
				});
		}, undefined);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.incluirBaseTributaria = function(){
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')
		delete ng.base_tributaria_item.cod_tipo_tributacao_pis_cofins_st;
		var error = 0 ;
		$.each(ng.base_tributaria_item,function(i,x){
			if(i != 'cod_base_tributaria_item' && i != 'cod_base_tributaria' && i != 'index' &&  i != 'nome_produto' ){
				if(!$.isNumeric(x)){
					$("#"+i).addClass("has-error");
					var formControl = $("#"+i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", "Este campo é obrigatório")
						.attr("data-original-title", "Este campo é obrigatório");
					formControl.tooltip();
					error ++ ;
				}		
			}
		});

		if(error > 0)	
			return

		if(ng.editingBaseTributaria){
			ng.base_tributaria.base_tributaria_itens[ng.base_tributaria_item.index] = {
				cod_base_tributaria_item   			: ng.base_tributaria_item.cod_base_tributaria_item,
				cod_base_tributaria 				: ng.base_tributaria_item.cod_base_tributaria,
				vlr_base_calculo_icms 				: ng.base_tributaria_item.vlr_base_calculo_icms,
				vlr_base_calculo_icms_st 			: ng.base_tributaria_item.vlr_base_calculo_icms_st,
				cod_tipo_tributacao_ipi 			: ng.base_tributaria_item.cod_tipo_tributacao_ipi,
				vlr_base_calculo_ipi             	: ng.base_tributaria_item.vlr_base_calculo_ipi,
				cod_tipo_tributacao_pis_cofins_st   : ng.base_tributaria_item.cod_tipo_tributacao_pis_cofins_st,
				vlr_pis             				: ng.base_tributaria_item.vlr_pis,
				vlr_cofins             				: ng.base_tributaria_item.vlr_cofins,
				vlr_pis_st             				: ng.base_tributaria_item.vlr_pis_st,
				vlr_cofins_st             			: ng.base_tributaria_item.vlr_cofins_st,
				cod_produto                         : ng.base_tributaria_item.cod_produto
			};
		}else{
			ng.base_tributaria.base_tributaria_itens.push({
				cod_base_tributaria_item   			: null,
				cod_base_tributaria 				: ng.base_tributaria_item.cod_base_tributaria,
				vlr_base_calculo_icms 				: ng.base_tributaria_item.vlr_base_calculo_icms,
				vlr_base_calculo_icms_st 			: ng.base_tributaria_item.vlr_base_calculo_icms_st,
				cod_tipo_tributacao_ipi 			: ng.base_tributaria_item.cod_tipo_tributacao_ipi,
				vlr_base_calculo_ipi             	: ng.base_tributaria_item.vlr_base_calculo_ipi,
				cod_tipo_tributacao_pis_cofins_st   : ng.base_tributaria_item.cod_tipo_tributacao_pis_cofins_st,
				vlr_pis             				: ng.base_tributaria_item.vlr_pis,
				vlr_cofins             				: ng.base_tributaria_item.vlr_cofins,
				vlr_pis_st             				: ng.base_tributaria_item.vlr_pis_st,
				vlr_cofins_st             			: ng.base_tributaria_item.vlr_cofins_st,
				cod_produto                         : ng.base_tributaria_item.cod_produto
			});
		}

		ng.base_tributaria_item = {
			cod_base_tributaria_item 			: null,
			cod_base_tributaria 	 			: null,
			vlr_base_calculo_icms 				: null,
			vlr_base_calculo_icms_st 			: null,
			cod_tipo_tributacao_ipi 			: null,
			vlr_base_calculo_ipi 				: null,
			cod_tipo_tributacao_pis_cofins_st 	: null,
			vlr_pis 							: null,
			vlr_cofins 							: null,
			vlr_pis_st 							: null,
			vlr_cofins_st 						: null
		}

		ng.editingBaseTributaria = false ;
	}

	ng.itemEditing = function(index){
		return (Number(ng.base_tributaria_item.index) == Number(index) && ng.editingBaseTributaria) ;
	}

	ng.delBaseTributaria = function(index){
		ng.base_tributaria.base_tributaria_itens.splice(index,1);
	}

	ng.editarBaseTributaria = function(item,index){
		ng.base_tributaria_item = {
			cod_base_tributaria 	 			: item.cod_base_tributaria,
			vlr_base_calculo_icms 				: item.vlr_base_calculo_icms,
			vlr_base_calculo_icms_st 			: item.vlr_base_calculo_icms_st,
			cod_tipo_tributacao_ipi 			: item.cod_tipo_tributacao_ipi,
			vlr_base_calculo_ipi 	 			: item.vlr_base_calculo_ipi,
			cod_tipo_tributacao_pis_cofins_st 	: item.cod_tipo_tributacao_pis_cofins_st,
			vlr_pis 							: item.vlr_pis, 
			vlr_cofins 							: item.vlr_cofins,
			vlr_pis_st 							: item.vlr_pis_st,
			vlr_cofins_st 						: item.vlr_cofins_st,
			index                               : index,
			cod_produto                         : item.cod_produto,
			nome_produto                        : item.nome_produto
		}
		ng.editingBaseTributaria = true ;
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

	ng.loadControleNfe = function(ctr,key) {
		aj.get(baseUrlApi()+"nfe/controles/null/"+ctr)
			.success(function(data, status, headers, config) {
				ng[key] = ng[key].concat(data) ;
			})
			.error(function(data, status, headers, config) {
				
		});
	}

	ng.loadProdutos = function(offset, limit) {
		ng.produtos = null;

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
				ng.produtos = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.produtos = [] ;
				}
			});
	}

	ng.showProdutos = function(){
   		ng.loadProdutos(0,10);
   		$('#list_produtos').modal('show');
   	}

   	ng.addProduto = function(item){
   		ng.base_tributaria_item.nome_produto = item.nome;
   		ng.base_tributaria_item.cod_produto  = item.id_produto ;
   		$('#list_produtos').modal('hide');
   	}


	ng.chosen_tributacao_ipi  = [{num_item:'',nme_item:'--- Selecione ---'}] ;
   	ng.loadControleNfe('tipo_tributacao_ipi','chosen_tributacao_ipi');
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