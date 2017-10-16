app.controller('EspecializacaoNcmController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			   = baseUrl();
	ng.userLogged 		   = UserService.getUserLogado();
	var especializacao_ncm = {
		cod_especializacao_ncm : null,
		cod_ncm 			   : null,
		ex_tipi 			   : null,
		dsc_especializacao_ncm : null,
		ncm_view               : null,
		ex_tipi_view		   : null,
		cod_empreendimento     : ng.userLogged.id_empreendimento 
	}
	ng.especializacao_ncm = angular.copy(especializacao_ncm);
	ng.paginacao 		  = {especializacao_ncm:null} ;
    ng.lista_ncm		  = [];
    ng.editing 			  = false;
    ng.busca              = {ncm:''};
    ng.lista_especializacao_ncm = [];

    ng.showBoxNovo = function(onlyShow){
    	ng.editing = false;
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
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.especializacao_ncm = especializacao_ncm ;
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.load = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.lista_especializacao_ncm = null ;
		aj.get(baseUrlApi()+"especializacao_ncm/get/"+offset+"/"+limit+"?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.lista_especializacao_ncm = data.especializacao_ncm;
				ng.paginacao.lista_especializacao_ncm = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.lista_especializacao_ncm = [];
					ng.paginacao.lista_especializacao_ncm = [];
				}
			});
	}

	ng.salvar = function() {
		$('.has-error').tooltip('destroy')
		$('.has-error').removeClass('has-error')

		var btn = $("#salvar-especializacao-ncm") ;
		btn.button('loading');
		var url = 'especializacao_ncm';
		var itemPost = {};
		var msg = "Especialiação NCM salva com sucesso!";

		if(ng.especializacao_ncm.cod_especializacao_ncm != null && ng.especializacao_ncm.cod_especializacao_ncm > 0) {
			url += '/update';
			msg = 'Especialiação NCM  alterada com sucesso!'
		}

		itemPost = angular.copy(ng.especializacao_ncm);

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
				}else
					ng.mensagens('alert-danger','<strong>Erro ao efetuar cadastro!</strong>');
					$('html,body').animate({scrollTop: 0},'slow');
			});
	}

	ng.editar = function(item) {
		ng.especializacao_ncm = angular.copy(item);
		ng.especializacao_ncm.ncm_view 		= item.cod_ncm +" - "+item.dsc_ncm ;
		ng.showBoxNovo(true);
		$('html,body').animate({scrollTop: 0},'slow');
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Excluir Este Zoneamento ?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"zoneamento/delete/"+item.cod_zoneamento)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Zoneamento excluido com sucesso</strong>','.alert-list');
					ng.reset();
					ng.load();
				})
				.error(function(data, status, headers, config) {
					ng.mensagens('alert-danger','<strong>Erro ao excluir</strong>','.alert-list');
				});
		}, undefined);
	}

	ng.selNcm = function(){
		$('#list-ncm').modal('show');
		ng.loadNcm(0,10);
	}

	ng.changeNcm = function(item){
		ng.especializacao_ncm.cod_ncm      = item.cod_ncm ;
		ng.especializacao_ncm.ncm_view 	   = item.cod_ncm +" - "+item.dsc_ncm ;
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

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.load(0,10);

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
