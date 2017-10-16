app.controller('ControleNfeController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			= baseUrl();
	ng.userLogged 		= UserService.getUserLogado();
	ng.situacaoEspecial = {
		dsc_situacao_especial	: null,
		cod_empreendimento		: ng.userLogged.id_empreendimento,
		produto_cliente 		: []
	};
	ng.controleNfe = {
			nme_controle_nfe : null,
			itens   		 : []
	}
	ng.controleNfeItem = {
		cod_controle_item_nfe 	: null,
		cod_controle_nfe 		: null,
		nme_item 				: null,
		dsc_item 				: null,
		num_item 				: null
	}
	ng.busca = {clientes:'',produtos:''} ;
	ng.editingItem = false ;
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
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.controleNfe = {
			nme_controle_nfe : null,
			itens   		 : []
		}
		ng.editing = false;
		ng.editingItem = false ;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.load = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.controles = null ;
		aj.get(baseUrlApi()+"controle_nfe/get/"+offset+"/"+limit+"?flg_excluido=0")
			.success(function(data, status, headers, config) {
				if(data!= false){
					ng.controles = data.controles;
					ng.paginacao.controles = data.paginacao;
				}else{
					ng.controles = [];
					ng.paginacao.controles = [];
				}
				
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.controles = [];
					ng.paginacao.controles = [];
				}
			});
	}

	ng.salvar = function() {
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')

		var btn = $("#salvar-controle-nfe") ;
		btn.button('loading');
		var url = 'controle_nfe';
		var itemPost = {};
		var msg = "Controle salvo com sucesso!";

		if(ng.controleNfe.cod_controle_nfe != null && ng.controleNfe.cod_controle_nfe > 0) {
			url += '/update';
			msg = 'Controle alterado com sucesso!'
		}

		itemPost = angular.copy(ng.controleNfe);

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
					$('.has-error').eq(0).tooltip('show')
				}else{
					ng.mensagens('alert-danger','<strong>Erro ao efetuar cadastro!</strong>');
					$('html,body').animate({scrollTop: 0},'slow');
				}
			});
	}

	ng.incluirItem = function(){
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')
		var error = 0 ;
		if(empty(ng.controleNfeItem.nme_item)){
			$("#item-nme_item").addClass("has-error");
			var formControl = $("#item-nme_item")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Informe o nome do item")
				.attr("data-original-title", "Informe o nome do item");
			formControl.tooltip();
			error ++ ;
		}	
		if(empty(ng.controleNfeItem.num_item) && Number(ng.controleNfeItem.num_item) != 0 ){
			$("#item-num_item").addClass("has-error");
			var formControl = $("#item-num_item")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", "Informe o N° do item")
				.attr("data-original-title", "Informe o N° do item");
			formControl.tooltip();
			error ++ ;
		}

		if(error > 0)	
			return

		if(ng.editingItem){
			ng.controleNfe.itens[ng.controleNfeItem.index] = {
				cod_controle_item_nfe : ng.controleNfeItem.cod_controle_item_nfe,
				cod_controle_nfe 	  : ng.controleNfeItem.cod_controle_nfe,
				nme_item 			  : ng.controleNfeItem.nme_item,
				dsc_item 		 	  : ng.controleNfeItem.dsc_item,
				num_item 		      : ng.controleNfeItem.num_item
			};
		}else{
			ng.controleNfe.itens.push({
				cod_controle_nfe 	  : null,
				nme_item 			  : ng.controleNfeItem.nme_item,
				dsc_item 		 	  : ng.controleNfeItem.dsc_item,
				num_item 		      : ng.controleNfeItem.num_item
			});
		}

		ng.controleNfeItem = {
			cod_controle_item_nfe 	: null,
			cod_controle_nfe 		: null,
			nme_item 				: null,
			dsc_item 				: null,
			num_item 				: null
		}

		ng.editingItem = false ;
	}

	ng.editarItem = function(item,index){
		ng.controleNfeItem = angular.copy(item);
		ng.controleNfeItem.index = index ;
		ng.editingItem = true ;
	}

	ng.editar = function(item) {
		$('html,body').animate({scrollTop: 0},'slow');
		ng.controleNfe = angular.copy(item);
		ng.controleNfe.itens = null ;
		ng.showBoxNovo(true);
		ng.loadControleItens(ng.controleNfe.cod_controle_nfe);
	}

	ng.loadControleItens = function(cod_controle_nfe) {
		aj.get(baseUrlApi()+"nfe/controles/"+cod_controle_nfe)
			.success(function(data, status, headers, config) {
				ng.controleNfe.itens = data ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.controleNfe.itens = [] ;
				}
			});
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Excluir Este Controle ?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"controle_nfe/delete/"+item.cod_controle_nfe)
				.success(function(data, status, headers, config) {
					ng.load();
					ng.mensagens('alert-success','<strong>Controle excluido com sucesso</strong>','.alert-list');
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



	ng.itemEditing = function(index){
		return (Number(ng.controleNfeItem.index) == Number(index) && ng.editingItem) ;
	}

	ng.delItem = function(index){
		ng.controleNfe.itens.splice(index,1);
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
