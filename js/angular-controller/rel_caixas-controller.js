app.controller('relCaixasController', function($scope, $http, $window, $dialogs, UserService,FuncionalidadeService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 						= baseUrl();
	ng.userLogged 					= UserService.getUserLogado();
    ng.contas    					= [];
    ng.paginacao           			= {conta:null} ;
    ng.busca               			= {empreendimento:"",caixas_string:""} ;
    ng.conta                        = {} ;

    ng.editing = false;

    ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

     ng.funcioalidadeAuthorized = function(cod_funcionalidade){
    	return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
    }

    ng.showBoxNovo = function(onlyShow){
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

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').removeClass("alert-danger");
		$('.alert-sistema').removeClass("alert-success");
		$('.alert-sistema').removeClass("alert-warning");
		$('.alert-sistema').removeClass("alert-info");
		$('.alert-sistema')
			.fadeIn()
			.addClass(classe)
			.html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function(show) {
		show = show == true ? true : false ;
		ng.conta = {};
		$('[name="perc_taxa_maquineta"]').val('');
		ng.empreendimentosAssociados = [];
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		if(show)
			ng.showBoxNovo();
	}

	ng.loadContas = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.contas = [] ;

		var query_string = "";
		if(!ng.funcioalidadeAuthorized('listar_todos_caixas'))
		    query_string += "&id_operador="+ng.userLogged.id;

		if(!empty(ng.busca.caixas_string)){
			query_string += "&("+$.param({'cnt_bancaria->dsc_conta_bancaria':
											{exp:"LIKE '%"+ng.busca.caixas_string+"%' OR usu.nome LIKE '%"+ng.busca.caixas_string+"%')"
											}
										})+"";
		}

		var dta_inicial = $("#dtaInicial").val();
		var dta_final   = $("#dtaFinal").val();
		
		if( !empty(dta_inicial) && !empty(dta_final)){
			dta_inicial = formatDate(dta_inicial)+" 00:00:00";
			dta_final   = formatDate(dta_final)+" 23:59:59";

			query_string = query_string+"&("+$.param({'abt_caixa->dta_abertura':
											{exp:">= '"+dta_inicial+"' AND abt_caixa.dta_fechamento <= '"+dta_final+"')"}
										})+"";
		}else if(!empty(dta_inicial)){
			dta_inicial = formatDate(dta_inicial)+" 00:00:00";	
			query_string = query_string+"&("+$.param({'abt_caixa->dta_abertura':
											{exp:">= '"+dta_inicial+"')"}
										})+"";
		}else if(!empty(dta_final)){
			dta_final   = formatDate(dta_final)+" 23:59:59";

			query_string = query_string+"&("+$.param({'abt_caixa->dta_fechamento':
											{exp:" <= '"+dta_final+"')"}
										})+"";
		}

		aj.get(baseUrlApi()+"caixa/allAberturas/"+offset+"/"+limit+"?abt_caixa->id_empreendimento="+ng.userLogged.id_empreendimento+query_string)
			.success(function(data, status, headers, config) {
				ng.caixas_mov = data.aberturas;
				ng.paginacao.caixas_mov = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.caixas_mov = [];
					ng.paginacao.caixas_mov = [];

			});
	}

	ng.loadConta = function() {
		ng.conta = {} ;
		aj.get(baseUrlApi()+"conta_bancaria")
			.success(function(data, status, headers, config) {
				ng.conta = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.conta = [];
	 	});
	}

	ng.loadDepositos = function() {
		aj.get(baseUrlApi()+"depositos?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.depositos = data.depositos;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.depositos = [];
	 	});
	}

	ng.salvar = function() {

		var url   = ng.editing ? "conta_bancaria/update" : "conta_bancaria";
		var conta = angular.copy(ng.conta);
		
		//conta.perc_taxa_maquineta = conta.perc_taxa_maquineta / 100 ;
		conta.id_empreendimento   = ng.userLogged.id_empreendimento;
		conta.id_tipo_conta       = 5 ;

		/*if(isNaN(conta.perc_taxa_maquineta))
			conta.perc_taxa_maquineta = 0 ;
		*/

		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error-plano")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		$($(".has-error-plano")).removeClass("has-error-plano");

		aj.post(baseUrlApi()+url, conta)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>Conta salva com sucesso!</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.loadContas();
			})
			.error(function(data, status, headers, config) {
				if(status == 406) {
					var errors = data;

					$.each(errors, function(i, item) {
						$("#"+i).addClass("has-error");

						var formControl = $($("#"+i).find(".form-control")[0])
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});
				}
			});
	}

	ng.editar = function(item) {
		ng.editing = true;
		item.perc_taxa_maquineta = item.perc_taxa_maquineta * 100 ;
		$('[name="perc_taxa_maquineta"]').val(numberFormat(item.perc_taxa_maquineta,'2',',','.'));
		ng.conta = angular.copy(item);
		if(!$('#box-novo').is(':visible')){
			ng.showBoxNovo();
		}
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir esta conta?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"conta_bancaria/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Conta excluida com sucesso</strong>','.alert-delete');
					ng.reset();
					ng.loadContas();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	ng.loadBancos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.bancos = [];

		aj.get(baseUrlApi()+"bancos")
			.success(function(data, status, headers, config) {
				ng.bancos = data.bancos;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.loadtipos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.tipos = [];

		aj.get(baseUrlApi()+"contas_bancarias/tipos")
			.success(function(data, status, headers, config) {
				ng.tipos = data.tipos;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.resetFilter = function(){
		ng.busca.caixas_string = "" ;
		$("#dtaInicial").val('');
		$("#dtaFinal").val('');
		ng.loadContas(0,10);
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}


	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.loadContas(0,10);
	ng.loadBancos();
	ng.loadtipos();
	ng.loadDepositos();
});
