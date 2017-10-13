app.controller('CaixasController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 						= baseUrl();
	ng.userLogged 					= UserService.getUserLogado();
    ng.contas    					= [];
    ng.paginacao           			= {conta:null} ;
    ng.busca               			= {empreendimento:""} ;
    ng.conta                        = {depositos:[]} ;
    ng.impressoras                  = [
    	{ value: null					, dsc:'Selecione' 			},
    	{ value:'bematech_mp_2500_th'	, dsc:'BEMATECH MP-2500 TH' },
    	{ value:'bematech_mp_4200_th'	, dsc:'BEMATECH MP-4200 TH' },
    	{ value:'epson_tm_t20'			, dsc:'EPSON TM T20' 		}
	];

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

	ng.busca = { text: "" };
	ng.resetFilter = function() {
		ng.busca.text = "" ;
		ng.reset();
		ng.loadContas(0,10);
	}

	ng.contas = [];
	ng.paginacao.conta = [];
	ng.loadContas = function(offset,limit,overlay) {
		overlay = overlay == null ? false : overlay ;
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "/?id_tipo_conta=5&id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.text != "")
			query_string += "&("+$.param({dsc_conta_bancaria:{exp:"like '%"+ng.busca.text+"%' OR pth_local = '"+ng.busca.text+"'"}})+")";

		ng.contas = null ;
		aj.get(baseUrlApi()+"contas_bancarias/"+offset+"/"+limit+ query_string)
			.success(function(data, status, headers, config) {
				ng.contas = data.contas;
				ng.paginacao.conta = data.paginacao;
			})
			.error(function(data, status, headers, config) {
					ng.contas = [];
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
				ng.depositos = [{id:null,nme_deposito:'Selecione'}];
				$.each(data.depositos,function(i,x){
					data.depositos[i].id = Number(x.id);
				});
				ng.depositos = ng.depositos.concat(data.depositos);
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.depositos = [];
	 	});
	}

	ng.salvar = function() {

		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error-plano")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		$($(".has-error-plano")).removeClass("has-error-plano");

		var error = 0 ;

		$.each(ng.conta.depositos,function(i,x){
			if(empty(x.ordem_saida)){
				$("#input-ordem-saida-"+i).parent().addClass("has-error");
				var input = $("#input-ordem-saida-"+i).attr("data-toggle", "tooltip").attr("data-placement", "top").attr("title", 'Informe a ordem de saida');
				if(error == 0) input.tooltip('show');
				else input.tooltip();
				error ++ ;
				return ;
			}
		});

		if(error > 0)
			return ;

		var url   = ng.editing ? "conta_bancaria/update" : "conta_bancaria";
		var conta = angular.copy(ng.conta);

		//conta.perc_taxa_maquineta = conta.perc_taxa_maquineta / 100 ;
		conta.id_empreendimento   = ng.userLogged.id_empreendimento;
		conta.id_tipo_conta       = 5 ;

		/*if(isNaN(conta.perc_taxa_maquineta))
			conta.perc_taxa_maquineta = 0 ;
		*/

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

						var formControl = $("#"+i)
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
		ng.loadDepositosCaixa(item.id);
		item.perc_taxa_maquineta = item.perc_taxa_maquineta * 100 ;
		$('[name="perc_taxa_maquineta"]').val(numberFormat(item.perc_taxa_maquineta,'2',',','.'));
		ng.conta = angular.copy(item);
		if(!$('#box-novo').is(':visible')){
			ng.showBoxNovo();
		}
		$('html,body').animate({scrollTop: 0},'slow');
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

	ng.loadOperacaoCombo = function() {
		ng.lista_operacao  = [{cod_operacao:null,dsc_operacao:'Selecione'}] ;
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

	ng.loadDepositosCaixa = function(id_caixa) {
		ng.conta.depositos = null ;
		aj.get(baseUrlApi()+"caixa_deposito?tcd->id_caixa="+id_caixa)
			.success(function(data, status, headers, config) {
				ng.conta.depositos = data ;
			})
			.error(function(data, status, headers, config) {
				ng.conta.depositos = [] ;
			});
	}


	ng.modalDepositos = function(){
		$('#modal-depositos').modal('show');
		ng.loadDepositos(0,10);
	}

	ng.depositos = {itens:null,paginacao:[]};
	ng.loadDepositos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.depositos = {itens:null,paginacao:[]};
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(!empty(ng.busca.depositos))
			query_string  += "&"+$.param({nme_deposito:{exp:"like '%"+ng.busca.depositos+"%'"}});

    	aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+query_string)
		.success(function(data, status, headers, config) {
			ng.depositos = {itens:data.depositos,paginacao:data.paginacao};
		})
		.error(function(data, status, headers, config) {
			ng.depositos = {itens:[],paginacao:[]};
		});
	}

	ng.addDeposito = function(item){
		if(!empty(item.ordem_saida)){
			if(ng.OrdemdepositoExists(item.ordem_saida)){
				ng.mensagens('alert-warning','Já existe um deposito nessa ordem','#alert-modal-deposito');
				return ;
			}
		}
		var itemAdd = {
			id_deposito: item.id,
			ordem_saida: ( empty(item.ordem_saida) ? null : item.ordem_saida ) ,
			nme_deposito: item.nme_deposito,
			error_ordem_saida : false 
		};
		ng.conta.depositos.push(itemAdd);
		item.ordem_saida = null ;
	}

	ng.delDeposito = function(index){
		ng.conta.depositos.splice(index,1);
	}
	
	ng.depositoSelected = function(item){
		if(typeof ng.conta.depositos != 'object')
			return ;
		var saida = false ;
		$.each(ng.conta.depositos,function(i,x){
			if(Number(item.id) == Number(x.id_deposito)){
				saida = true ;
				return ;
			}
		});
		return saida ;
	}

	ng.OrdemdepositoExists = function(ordem_saida,index){
		index = index == null ? -1 : Number(index) ;
		var saida = false ;
		$.each(ng.conta.depositos,function(i,x){
			if(i != index){
				if(Number(ordem_saida) == Number(x.ordem_saida)){
					saida = true ;
					return ;
				}
			}
		});
		return saida ;
	}

	ng.verificarOrdemSaida = function(item,index){
		if(ng.OrdemdepositoExists(item.ordem_saida,index) && !empty(item.ordem_saida)){
			delete item.tooltip ;
			item.tooltip = {init:true,show:true,placement:'top',trigger:'focus hover',title:'Já existe um deposito na ordem '+item.ordem_saida} ;
			item.ordem_saida = null ;
		}else{
			delete item.tooltip ;
		}
	}

	ng.tirarErrorTooltip = function(item){  
		if(typeof item.tooltip == 'object' && item.tooltip.init === true) delete item.tooltip  ; 
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().removeClass('alert-success alert-warning alert-danger').addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}


	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.loadContas(0,10);
	ng.loadOperacaoCombo();
	ng.loadBancos();
	ng.loadtipos();
	ng.loadDepositos();
});
