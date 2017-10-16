app.controller('MaquinetasController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 						= baseUrl();
	ng.userLogged 					= UserService.getUserLogado();
    ng.contas    					= [];
    ng.paginacao           			= {maquinetas:null} ;
    ng.busca               			= {empreendimento:""} ;
    ng.conta                        = {} ;
    ng.maquineta 					= {per_margem_credito:0,per_margem_debito:0} ;

    ng.taxa_maquineta               = [];
    ng.nova_taxa                    = {id_maquineta:null,qtd_parcelas_inicio:null,qtd_parcelas_fim:null,prc_taxa:null} ;

    ng.editing = false;

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

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.reset = function(show) {
		show = show == true ? true : false ;
		ng.maquineta = {};
		$('[name="perc_taxa_maquineta"]').val('');
		ng.empreendimentosAssociados = [];
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		ng.taxa_maquineta = [] ;
		if(show)
			ng.showBoxNovo();
	}

	ng.loadContas = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;
		ng.contas = [] ;
		aj.get(baseUrlApi()+"contas_bancarias/"+offset+"/"+limit+"?id_tipo_conta[exp]=IN (1,2)&id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.contas = data.contas;
				ng.paginacao.conta = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.depositos = [];
			});
	}

	ng.salvar = function() {
		var url   = ng.editing ? "maquineta/update" : "maquineta";
		var msg   = ng.editing ? "Maquinera Atualizada com sucesso"	: "Maquineta salva com sucesso!"
		var maquineta = angular.copy(ng.maquineta);

		//maquineta.perc_taxa_maquineta = maquineta.perc_taxa_maquineta / 100 ;
		maquineta.id_empreendimento   = ng.userLogged.id_empreendimento;
		maquineta.per_margem_debito   = maquineta.per_margem_debito / 100 ;
		maquineta.per_margem_credito  = maquineta.per_margem_credito / 100 ;


		/*if(isNaN(maquineta.perc_taxa_maquineta))
			maquineta.perc_taxa_maquineta = 0 ;
		*/

		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error-plano")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		$($(".has-error-plano")).removeClass("has-error-plano");

		maquineta.taxas = ng.taxa_maquineta == null ? [] : angular.copy(ng.taxa_maquineta) ;
		if(ng.editing){
			maquineta.delete_taxa = ng.itensdeleteTaxa.length == 0 ? [] : angular.copy(ng.itensdeleteTaxa) ;
			maquineta.update_taxa = angular.copy(ng.update_taxa) ;

			if(maquineta.update_taxa.length > 0){
				$.each(maquineta.update_taxa,function(i,v){
					maquineta.update_taxa[i].prc_taxa = Number(v.prc_taxa) / 100 ;
				});
			}
		}

		if(maquineta.taxas.length > 0){
			$.each(maquineta.taxas,function(i,v){
				v.prc_taxa = Number(v.prc_taxa) / 100 ;
			});
		}


		aj.post(baseUrlApi()+url, maquineta)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>'+msg+'</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.nova_taxa  = {id_maquineta:null,qtd_parcelas_inicio:null,qtd_parcelas_fim:null,prc_taxa:null} ;
				ng.itensdeleteTaxa = [] ;
				ng.update_taxa     = [] ;
				ng.loadMaquinetas();
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
		ng.itensdeleteTaxa = [] ;
		ng.update_taxa     = [] ;

		$('[name="per_margem_debito"]').val(numberFormat(item.per_margem_debito,'2',',','.'));

		ng.maquineta = angular.copy(item);
		ng.maquineta.per_margem_debito   = ng.maquineta.per_margem_debito * 100 ;
		ng.maquineta.per_margem_credito  = ng.maquineta.per_margem_credito * 100 ;

		ng.taxa_maquineta  = ng.maquineta.taxas == null ? [] : angular.copy(ng.maquineta.taxas)  ;

		if(ng.taxa_maquineta.length > 0){
			$.each(ng.taxa_maquineta,function(i,v){
				ng.taxa_maquineta[i].prc_taxa = Number(v.prc_taxa) * 100 ;
			});
		}
		
		if(!$('#box-novo').is(':visible')){
			ng.showBoxNovo();
		}
	}
	ng.itensdeleteTaxa = [] ;
	ng.deleteTaxa = function(index,item){
		ng.taxa_maquineta.splice(index,1);
		if(!empty(item.id))
			ng.itensdeleteTaxa.push(angular.copy(item));
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir esta conta?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"maquineta/delete/"+item.id_maquineta)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Maquineta excluida com sucesso</strong>','.alert-delete');
					ng.reset();
					ng.loadMaquinetas();
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

	ng.busca = { text: "" };
	ng.resetFilter = function() {
		ng.busca.text = "" ;
		ng.reset();
		ng.loadMaquinetas(0,10);
	}

	ng.loadMaquinetas = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?maq->id_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0";

		if(ng.busca.text != "")
			query_string += "&("+$.param({num_serie_maquineta:{exp:"like '%"+ng.busca.text+"%' OR id_maquineta = '"+ng.busca.text+"' OR dsc_conta_bancaria like '%"+ng.busca.text+"%'"}})+")";

		ng.maquinetas = [];

		aj.get(baseUrlApi()+"maquinetas/"+offset+"/"+limit+ query_string)
			.success(function(data, status, headers, config) {
				ng.maquinetas 			= data.maquinetas;
				ng.paginacao.maquinetas = [] ;
			})
			.error(function(data, status, headers, config) {
				ng.paginacao.maquinetas = [] ;
			});
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.modalAddtaxa = function(){
		ng.removeError();
		ng.nova_taxa  = {id_maquineta:null,qtd_parcelas_inicio:null,qtd_parcelas_fim:null,prc_taxa:null} ;
		$('#modal-add-taxa').modal('show');
	}

	ng.cancelarModal = function(identificador){
		$(identificador).modal('hide');
	}

	ng.validaTaxa = function(taxa){
		var taxa = Number(taxa);
		var init;
		var fim ;
		var saida = true;
		$.each(ng.taxa_maquineta,function(i,faixa){
			init = Number(faixa.qtd_parcelas_inicio);
			fim  = Number(faixa.qtd_parcelas_fim);
			if(taxa >= init && taxa <= fim){
				saida = false ;
				return false;
			}
		});
		return saida ;
	}
	ng.update_taxa = [] ;
	ng.addtaxa = function(){
		var taxa = angular.copy(ng.nova_taxa);
		taxa.prc_taxa = empty(taxa.prc_taxa,false) ? 0 : taxa.prc_taxa  ;
		
		if(empty(taxa.qtd_parcelas_inicio) && empty(taxa.qtd_parcelas_fim) ){
			ng.mensagens('alert-danger','Qtd. parcelas início e Qtd. parcelas fim não podem estar fazias juntas','.alert-add-taxa');
			return false;
		}
		if(empty(taxa.prc_taxa,false)){
			$("#prc_taxa").addClass("has-error");
			var formControl = $("#prc_taxa")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'Informe o valor da taxa')
				.attr("data-original-title", 'Informe o valor da taxa');
			formControl.tooltip();
			return false;
		}

		taxa.qtd_parcelas_inicio = taxa.qtd_parcelas_inicio == null ? 0 : taxa.qtd_parcelas_inicio ;
		taxa.qtd_parcelas_fim    = taxa.qtd_parcelas_fim    == null ? 0 : taxa.qtd_parcelas_fim ;

		if(empty(ng.validaTaxa(taxa.qtd_parcelas_inicio))){
			$("#inicio_taxa").addClass("has-error");
			var formControl = $("#inicio_taxa")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'Este valor já está incluido em outra faixa')
				.attr("data-original-title", 'Este valor já está incluido em outra faixa');
			formControl.tooltip();
			return false;
		}

		if(empty(ng.validaTaxa(taxa.qtd_parcelas_fim))){
			$("#fim_taxa").addClass("has-error");
			var formControl = $("#fim_taxa")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'Este valor já está incluido em outra faixa')
				.attr("data-original-title", 'Este valor já está incluido em outra faixa');
			formControl.tooltip();
			return false;
		}

		ng.taxa_maquineta.push(taxa);
		ng.update_taxa.push(taxa);
		ng.nova_taxa             = {id_maquineta:null,qtd_parcelas_inicio:null,qtd_parcelas_fim:null,prc_taxa:null} ;
		ng.cancelarModal('#modal-add-taxa');
	}


	ng.removeError = function(){
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass('has-error');
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.loadContas(0,100000);
	ng.loadBancos();
	ng.loadMaquinetas(0,10);
});
