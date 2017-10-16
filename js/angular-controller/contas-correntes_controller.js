app.controller('LancamentosController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 					= baseUrl();

	ng.userLogged 				= UserService.getUserLogado();
	ng.empreendimento 			= {};
    ng.empreendimentos 			= [];
    ng.bancos           		= [];
    ng.contas           		= [];
    ng.busca					= {dsc_conta_bancaria : "",clientes:"",fornecedores:"",op_valor:"" }
    ng.paginacao 				= {pagamentos:null}
    ng.vlrTotalPeriodo 			= 0;
    ng.dataGroups 				= [];
    ng.recorrencias			    = [
    								{periodo:"Semanal",dias:7},{periodo:"Quizenal",dias:15},{periodo:"Mensal",dias:30},
    								{periodo:"trimestral",dias:90},{periodo:"Semestral",dias:180},{periodo:"Anual",dias:365}
    							  ];
    ng.cheques					= [{id_banco:null,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0}];
    ng.recebidos 				= [] ;
    ng.boletos					= [{id_banco:null,num_conta_corrente:null,num_cheque:null,status_pagamento:0}];

    ng.roleList = [];
    ng.pagamento         = {status:0};
    ng.status = 0 ;
    ng.flgTipoLancamento = 0 ;
    ng.formas_pagamento = [
		{nome:"Cheque",id:2},
		{nome:"Dinheiro",id:3},
		{nome:"Boleto Bancário",id:4},
		{nome:"Cartão de Débito",id:5},
		{nome:"Cartão de Crédito",id:6},
		{nome:"Transferência",id:8}
	  ]

    ng.editing 			= false;
    ng.cliente          = {} ;
    ng.config_table     = {cheque:false,boleto:false,transferencia:false} ;

    ng.pagamento_edit = {} ;
    ng.modalChangeStatusPagamento = function(item){
    	ng.pagamento_edit = item ;
    	$("#dta_change_pagamento").val(formatDateBR(item.data_pagamento));
    	$("#modal_change_date_pagamento").modal('show');
    }
    ng.updateStatusLancamento = function(item) {
    	var obj = {
    		idLancamento 	: item.id,
    		newStatus 		: (item.status_pagamento == 1) ? 0 : 1,
    		flgTipo 		: item.flg_tipo_lancamento,
    		data_pagamento  : formatDate($("#dta_change_pagamento").val())
    	};

    	$("#modal_change_date_pagamento").modal('hide');

    	//console.log(obj);

    	dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja alterar o status deste lançamento?</strong>');

		dlg.result.then(function(btn){
			aj.post(baseUrlApi()+"lancamento/status/update", obj)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Lançamento atualizado com sucesso</strong>','.alert-delete');
					ng.reset();
					ng.load();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	ng.delete = function(item,tipo){
		if(tipo == 'cliente'){
			var url = 'fornecedor/pagamento/delete/'
		}else if(tipo == 'fornecedor'){
			var url = 'cliente/pagamento/delete/'
		}

		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este lançamento?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+url+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Lançamento excluido com sucesso</strong>','.alert-delete');
					ng.reset();
					ng.load();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

    ng.showBoxNovo = function(onlyShow){
    	ng.editing = !ng.editing;

    	if(onlyShow) {
			$('i','.btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show(0,function(){$("select").trigger("chosen:updated");});
		}
		else {
			$('#box-novo').toggle(0, function(){
				/*if($(this).is(':visible')){
					$(".btn-novo").removeClass("btn-info").addClass("btn-danger").text("Cancelar");
					$("i", ".btn-novo").removeClass("fa-plus-circle").addClass("fa-minus-circle");
				}else{
					$(".btn-novo").removeClass("btn-danger").addClass("btn-info").text("Novo Lançamento");
					$("i", ".btn-novo").removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}*/
			});
			$("select").trigger("chosen:updated");
		}
		$("select").trigger("chosen:updated");
	}

	var nParcelasAnt = 1 ;

	ng.reset = function() {
		ng.empreendimento = {};
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.limparBusca = function(){
		$("#dtaInicial").val('');
		$("#dtaFinal").val('');
		ng.busca.dsc_conta_bancaria = "" ;
		ng.load(0,20);
	}

	ng.limparErrorValor = function(){
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass('has-error');
	}

	ng.load = function(offset,limit) {
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass('has-error');

		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 0 : limit;

		var dataInicial = $("#dtaInicial").val();
		var dataFinal   = $("#dtaFinal").val();
		var queryString = "?(id_tipo_conta[exp]= <> 5 OR (id_tipo_conta =5 AND (flg_caixa_fechado = 0 OR flg_caixa_fechado IS NULL ) )  AND flg_tipo_lancamento = 'C' ) AND (flg_transferencia_conta = 0 OR flg_transferencia_conta IS NULL)&id_empreendimento="+ng.userLogged.id_empreendimento ;

		if(dataInicial != "" &&  dataFinal != "" ){
			var data_arr = dataInicial.split('/');
			dataInicial = data_arr[2]+"-"+data_arr[1]+"-"+data_arr[0];
			data_arr = dataFinal.split('/');
			dataFinal = data_arr[2]+"-"+data_arr[1]+"-"+data_arr[0];
			queryString += "&" + $.param({data_pagamento:{exp:"between '"+dataInicial+" 00:00:00' and '"+dataFinal+" 23:59:59'"}}) ;
		}

		if(ng.busca.dsc_conta_bancaria != ""){
			queryString += "&dsc_conta_bancaria="+ng.busca.dsc_conta_bancaria;
		}

		if(ng.busca_avancada){
			if(ng.busca.flg_tipo_lancamento == 'D' || ng.busca.flg_tipo_lancamento == 'C')
				queryString += "&flg_tipo_lancamento="+ng.busca.flg_tipo_lancamento;
			if(!empty(ng.busca.nome_clienteORfornecedor))
				queryString +="&" + $.param({nome_clienteORfornecedor:{exp:"like '%"+ng.busca.nome_clienteORfornecedor+"%'"}}) ;
			if(!empty(ng.busca.id_plano_conta))
				queryString += "&id_plano_conta="+ng.busca.id_plano_conta;
			if(!empty(ng.busca.id_forma_pagamento))
				queryString += "&id_forma_pagamento="+ng.busca.id_forma_pagamento ;
			if(ng.busca.status_pagamento == "0" || ng.busca.status_pagamento == "1")
				queryString += "&status_pagamento="+ng.busca.status_pagamento;
			if(ng.busca.op_valor == "between"){
				if(empty(ng.busca.valor_fim)){
						$(".valor_fim").addClass("has-error");

						var formControl = $(".valor_fim")
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", 'Escolha o valor final')
							.attr("data-original-title", 'Escolha o valor final');
						formControl.tooltip();
						return;
				}else if(empty(ng.busca.valor_inicio)){
					ng.busca.valor_inicio = 0;
					queryString +="&" + $.param({valor_pagamento:{exp:"between "+ng.busca.valor_inicio+" AND "+ng.busca.valor_fim+""}}) ;
				}else
					queryString +="&" + $.param({valor_pagamento:{exp:"between "+ng.busca.valor_inicio+" AND "+ng.busca.valor_fim+""}}) ;
			}else if(ng.busca.op_valor == "=")
				queryString += "&" + $.param({valor_pagamento:{literal_exp:"CAST(valor_pagamento AS CHAR) ='"+ng.busca.valor_fim+"'" }}) ;
			else if(ng.busca.op_valor == "<")
				queryString += "&" + $.param({valor_pagamento:{literal_exp:"valor_pagamento  <'"+ng.busca.valor_fim+"'" }})  ;
			else if(ng.busca.op_valor == ">")
				queryString += "&" + $.param({valor_pagamento:{literal_exp:"valor_pagamento  >'"+ng.busca.valor_fim+"'" }})  ;
			else if(ng.busca.op_valor == "<=")
				queryString += "&" + $.param({valor_pagamento:{literal_exp:"(valor_pagamento  < '"+ng.busca.valor_fim+"' OR CAST(valor_pagamento AS CHAR) = '"+ng.busca.valor_fim+"')" }})  ;
			else if(ng.busca.op_valor == ">=")
				queryString += "&" + $.param({valor_pagamento:{literal_exp:"(valor_pagamento  >='"+ng.busca.valor_fim+"'  OR CAST(valor_pagamento AS CHAR) = '"+ng.busca.valor_fim+"')" }})  ;

		}
		ng.dataGroups  = [] ;
		aj.get(baseUrlApi()+"lancamentos/"+queryString)
			.success(function(data, status, headers, config) {
				ng.vlrTotalPeriodo 		= 0;

				ng.pagamentos           = data.pagamentos;
				ng.paginacao.pagamentos = data.paginacao;
				ng.dataGroups 			= _.groupBy(ng.pagamentos, "data_pagamento");

				$.each(ng.dataGroups, function(i, item) {
					var obj = { vlr_total_item: 0, items: item };
					var crd = 0;
					var deb = 0;
					var sld = 0;
					var a_receber = 0 ;
					var a_pagar   = 0 ;
					$.each(item, function(x, xItem){
						if((xItem.flg_tipo_lancamento == 'C') && (parseInt(xItem.status_pagamento) == 1))
							crd += parseFloat(xItem.valor_pagamento);
						else if ((xItem.flg_tipo_lancamento == 'C') && (parseInt(xItem.status_pagamento) == 0))
							a_receber += parseFloat(xItem.valor_pagamento);
						

						if(xItem.flg_tipo_lancamento == 'D' && (parseInt(xItem.status_pagamento) == 1))
							deb += parseFloat(xItem.valor_pagamento);
						else if(xItem.flg_tipo_lancamento == 'D' && (parseInt(xItem.status_pagamento) == 0))
							a_pagar += parseFloat(xItem.valor_pagamento);
					});

					
					obj.recebido        = crd ;
					obj.pago            = deb ;
					obj.a_receber       = a_receber ;
					obj.a_pagar         = a_pagar ;
					obj.vlr_total_item += (crd - deb);
					ng.vlrTotalPeriodo += (obj.vlr_total_item);
					ng.dataGroups[i] = obj;
					
				});
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.pagamentos = [];
					ng.paginacao.pagamentos = [];
					ng.dataGroups = null;
			});
	}

	ng.loadContas = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.contas = [];

		aj.get(baseUrlApi()+"contas_bancarias?id_empreendimento="+ng.userLogged.id_empreendimento+"&id_tipo_conta[exp]=<>5")
			.success(function(data, status, headers, config) {
				ng.contas = data.contas;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.reset = function() {
		ng.pagamento = {} ;
		ng.cheques	 =[{id_banco:null,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0}];
		ng.loadPlanoContas();
		$("#pagamentoData").val('');
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		ng.editing = false;
	}

	ng.loadPlanoContas = function() {
		ng.plano_contas = [{id:"",dsc_completa:"--- Selecione ---"}];
		aj.get(baseUrlApi()+"planocontas?tpc->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.roleList = data;
				ng.plano_contas = ng.plano_contas.concat(data);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.roleList = [];
			});
	}

	ng.selCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

			ng.loadCliente(offset,limit);
			$("#list_clientes").modal("show");
	}


	ng.addCliente = function(item){
    	ng.cliente = item;
    	ng.loadSaldoDevedorCliente();
    	ng.pagamento.id_cliente = item.id;
    	$("#list_clientes").modal("hide");
	}

	ng.loadSaldoDevedorCliente= function() {
		aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ng.userLogged.id_empreendimento+"/"+ng.cliente.id)
			.success(function(data, status, headers, config) {
				ng.cliente.vlr_saldo_devedor = Number(data.vlr_saldo_devedor);
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.loadCliente= function(offset,limit) {
		ng.clientes = [] ;
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+")";

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

			});
	}

	ng.selFornecedor = function(){
		var offset = 0  ;
    	var limit  =  10;

			ng.loadFornecedor(offset,limit);
			$("#list_fornecedores").modal("show");
	}

	ng.fornecedor = {} ;
	ng.addFornecedor = function(item){
    	ng.fornecedor 				= item;
    	ng.pagamento.id_fornecedor  = item.id;
    	$("#list_fornecedores").modal("hide");
	}

	ng.loadFornecedor = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.fornecedores = [];
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(ng.busca.fornecedores != ""){
			query_string += "&"+$.param({'frn->nome_fornecedor':{exp:"like'%"+ng.busca.fornecedores+"%'"}});
		}

		aj.get(baseUrlApi()+"fornecedores/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.fornecedores 		  = data.fornecedores;
				ng.paginacao_fornecedores = data.paginacao ;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.configTable = function(){
		$('#modal_config_table').modal('show');
	}

	ng.cancelarModal = function(id){
		$('#'+id).modal('hide');
	}

	ng.modalPlanoContas = function(tipo){
		ng.loadPlanoContas();
		$('#modal-plano-contas').modal('show');
	}

	ng.escolherPlano = function(){

		ng.pagamento.nome_plano_conta = ng.currentNode.dsc_plano ;
		ng.pagamento.id_plano_conta   = ng.currentNode.id;

		console.log(ng.pagamento);

		$('#modal-plano-contas').modal('hide');
	}

	var nParcelasAntCheque = 1 ;
	var nParcelasAntBoleto = 1 ;
	ng.pagamento.parcelas  = 1 ;

	ng.pushCheques = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.parcelas = empty(ng.pagamento.parcelas) ? 1 : ng.pagamento.parcelas ;
			ng.pagamento.parcelas = ng.pagamento.parcelas == "" ?  1 : ng.pagamento.parcelas ;
			if(ng.pagamento.parcelas > nParcelasAntCheque){
				var repeat = parseInt(ng.pagamento.parcelas) - parseInt(nParcelasAntCheque) ;
				while(repeat > 0){
					var item = {id_banco:null,valor_pagamento:0,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0};
					ng.cheques.push(item);
					repeat -- ;
				}
			}else if(ng.pagamento.parcelas < nParcelasAntCheque){
				var repeat = parseInt(nParcelasAntCheque) - parseInt(ng.pagamento.parcelas) ;
				while(repeat > 0){
					var index = ng.cheques.length - 1;
					ng.cheques.splice(index,1);
					repeat -- ;
				}
			}
			nParcelasAntCheque = ng.pagamento.parcelas;
			ng.calTotalCheque();
			setTimeout(function(){ ng.loadDatapicker();}, 1000);
		}else if(ng.pagamento.id_forma_pagamento == 4){

			ng.pagamento.parcelas = empty(ng.pagamento.parcelas) ? 1 : ng.pagamento.parcelas ;
			ng.pagamento.parcelas = ng.pagamento.parcelas == "" ?  1 : ng.pagamento.parcelas ;
			if(ng.pagamento.parcelas > nParcelasAntBoleto){
				var repeat = parseInt(ng.pagamento.parcelas) - parseInt(nParcelasAntBoleto) ;
				while(repeat > 0){
					var item = {id_banco:null,valor_pagamento:0,num_conta_corrente:null,num_cheque:null,status_pagamento:0};
					ng.boletos.push(item);
					repeat -- ;
				}
			}else if(ng.pagamento.parcelas < nParcelasAntBoleto){
				var repeat = parseInt(nParcelasAntBoleto) - parseInt(ng.pagamento.parcelas) ;
				while(repeat > 0){
					var index = ng.cheques.length - 1;
					ng.boletos.splice(index,1);
					repeat -- ;
				}
			}
			nParcelasAntBoleto = ng.pagamento.parcelas;
			ng.calTotalBoleto();
			setTimeout(function(){ ng.loadDatapicker();}, 1000);
		}
	}

	ng.calTotalCheque = function(){
		var valor = 0 ;
		$.each(ng.cheques,function(i,v){
			valor += Number(v.valor_pagamento);
		});

		ng.pagamento.valor = valor;

	}

	ng.calTotalBoleto = function(){
		var valor = 0 ;
		$.each(ng.boletos,function(i,v){
			valor += Number(v.valor_pagamento);
		});

		ng.pagamento.valor = valor;

	}

	ng.getDadosMaquineta = function(){
		var dados = null;
		$.each(ng.maquinetas,function(i,v){

			if(Number(v.id_maquineta) == Number(ng.pagamento.id_maquineta)){
				dados = v ;
				return ;
			}

		});

		return dados ;

	}

	ng.selIdMaquineta = function(){
		if(ng.pagamento.id_forma_pagamento == 5 || ng.pagamento.id_forma_pagamento == 6 ){
			ng.pagamento.id_conta_bancaria = null ;
			if (ng.pagamento.id_maquineta != undefined && ng.pagamento.id_maquineta != ''){
				var maquineta = ng.getDadosMaquineta()
				console.log(maquineta);
				ng.pagamento.id_conta_bancaria = maquineta.id_conta_bancaria ;
				ng.pagamento.taxa_maquineta = ng.pagamento.id_forma_pagamento == 5 ? maquineta.per_margem_debito : maquineta.per_margem_credito ;
			}
		}
	}

	ng.selectChange = function(){
		ng.selIdMaquineta();
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.valor = 0 ;
			ng.pagamento.parcelas = ng.cheques.length  > 0 ? ng.cheques.length : 1 ;
			if(ng.cheques.length > 0)
				ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 4){
			ng.pagamento.valor = 0 ;
			ng.pagamento.parcelas = ng.boletos.length  > 0 ? ng.boletos.length : 1 ;
			if(ng.boletos.length > 0)
				ng.calTotalBoleto();
		}	
		else if(ng.pagamento.id_forma_pagamento == 6){
			ng.pagamento.parcelas = 1 ;
			$(".data-cc").val(getDate('+',30,'pt'), getDate());
		}
		if(ng.pagamento.id_forma_pagamento != 6)
			$(".data-cc").val(getDate('+',0,'pt'));

		if(ng.pagamento.id_forma_pagamento != 2 )
			ng.pagamento.status = 0 ;

		ng.loadDatapicker();
	}

	ng.delItemCheque = function($index){
		ng.cheques.splice($index,1);
		ng.pagamento.parcelas = ng.cheques.length ;
		nParcelasAntCheque  = ng.pagamento.parcelas
	}
	ng.delItemBoleto = function($index){
		ng.boletos.splice($index,1);
		ng.pagamento.parcelas = ng.boletos.length ;
		nParcelasAntBoleto    = ng.pagamento.parcelas
	}

	ng.qtdCheque = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.parcelas = ng.cheques.length  > 0 ? ng.cheques.length : 1 ;
		}

	}

	ng.bancos = [] ;
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


	ng.loadDatapicker = function(){
		$(".chequeData").datepicker();
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});

		$(".boletoData").datepicker();
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});


		$("#dtaInicialCC").datepicker();
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
	}

	ng.loadMaquinetas = function() {
		ng.maquinetas = [];

		aj.get(baseUrlApi()+"maquinetas/?maq->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.maquinetas 			= data.maquinetas;
				ng.paginacao.maquinetas = [] ;
			})
			.error(function(data, status, headers, config) {
				ng.paginacao.maquinetas = [] ;
			});
	}

	ng.pg_cheques = [] ;
	ng.aplicarRecebimento = function(){
		var error = 0 ;
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass('has-error');

		//if(ng.pagamento.id_forma_pagamento != 5 && ng.pagamento.id_forma_pagamento != 6){
			if((ng.pagamento.id_conta_bancaria ==  undefined || ng.pagamento.id_conta_bancaria ==  '') && (ng.pagamento.id_forma_pagamento != 8)){
				error ++ ;
				$("#id_conta_bancaria").addClass("has-error");

				var formControl = $("#id_conta_bancaria")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'A escolha da conta bancaria é obrigatória')
					.attr("data-original-title", 'A escolha da conta bancaria é obrigatória');
				formControl.tooltip();
			}
		//}

		if(ng.pagamento.id_plano_conta ==  undefined || ng.pagamento.id_plano_conta ==  ''){
			error ++ ;
			$("#id_plano_pagamento").addClass("has-error");

			var formControl = $("#id_plano_pagamento")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'A escolha do plano de pagamento é obrigatório')
				.attr("data-original-title", 'A escolha do plano de pagamento é obrigatório');
			formControl.tooltip();
		}

		if(ng.pagamento.id_forma_pagamento ==  undefined || ng.pagamento.id_forma_pagamento ==  ''){
			error ++ ;
			$("#pagamento_forma_pagamento").addClass("has-error");

			var formControl = $("#pagamento_forma_pagamento")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'A escolha da forma de pagamento é obrigatória')
				.attr("data-original-title", 'A escolha da forma de chequ é obrigatória');
			formControl.tooltip();
		}
		//console.log(ng.pagamento);
		if(ng.pagamento.valor ==  undefined || ng.pagamento.valor ==  ''){
			error ++ ;
			$("#pagamento_valor").addClass("has-error");

			var formControl = $("#pagamento_valor")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'O valor é obrigatório')
				.attr("data-original-title", 'O valor é obrigatório');
			formControl.tooltip();
		}

		if((ng.pagamento.id_maquineta ==  undefined || ng.pagamento.id_maquineta ==  '') && (ng.pagamento.id_forma_pagamento == 5 || ng.pagamento.id_forma_pagamento == 6 ) && (ng.flgTipoLancamento == 0) ){
			error ++ ;
			$("#pagamento_maquineta").addClass("has-error");

			var formControl = $("#pagamento_maquineta")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'O escolha da maquineta é obrigatório')
				.attr("data-original-title", 'O escolha da maquineta é obrigatório');
			formControl.tooltip();
		}

		if(ng.pagamento.id_forma_pagamento == 2){
			$.each(ng.cheques, function(i,v){
				if($('.cheque_data input').eq(i).val() == "" || $('.cheque_data input').eq(i).val() == undefined ){
					$('.cheque_data').eq(i).addClass("has-error");

					var formControl = $('.cheque_data').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'A data do cheque é obrigatória')
						.attr("data-original-title", 'A data do cheque é obrigatória');
					formControl.tooltip();
					error ++ ;
				}

				if(v.valor_pagamento == "" || v.valor_pagamento == 0 || v.valor_pagamento == undefined ){
					$('.cheque_valor').eq(i).addClass("has-error");

					var formControl = $('.cheque_valor').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O valor do cheque é obrigatório')
						.attr("data-original-title", 'O valor do cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.id_banco == "" || v.id_banco == 0 || v.id_banco == undefined ){
					$('.cheque_banco').eq(i).addClass("has-error");

					var formControl = $('.cheque_banco').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O banco é obrigatório')
						.attr("data-original-title", 'O banco é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_conta_corrente == "" || v.num_conta_corrente == 0 || v.num_conta_corrente == undefined ){
					$('.cheque_cc').eq(i).addClass("has-error");

					var formControl = $('.cheque_cc').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O número da C/C é obrigatório')
						.attr("data-original-title", 'O Num. C/C é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_cheque == "" || v.num_cheque == 0 || v.num_cheque == undefined ){
					$('.cheque_num').eq(i).addClass("has-error");

					var formControl = $('.cheque_num').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O Núm. Cheque é obrigatório')
						.attr("data-original-title", 'O Núm. Cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}
			});

			//ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 4){
			$.each(ng.boletos, function(i,v){
				if($('.boleto_data input').eq(i).val() == "" || $('.boleto_data input').eq(i).val() == undefined ){
					$('.boleto_data').eq(i).addClass("has-error");

					var formControl = $('.boleto_data').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'A data do boleto é obrigatória')
						.attr("data-original-title", 'A data do boleto é obrigatória');
					formControl.tooltip();
					error ++ ;
				}

				if(v.valor_pagamento == "" || v.valor_pagamento == 0 || v.valor_pagamento == undefined ){
					$('.boleto_valor').eq(i).addClass("has-error");

					var formControl = $('.boleto_valor').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O valor do boleto é obrigatório')
						.attr("data-original-title", 'O valor do boleto é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.id_banco == "" || v.id_banco == 0 || v.id_banco == undefined ){
					$('.boleto_banco').eq(i).addClass("has-error");

					var formControl = $('.boleto_banco').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O banco é obrigatório')
						.attr("data-original-title", 'O banco é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.doc_boleto == "" || v.doc_boleto == 0 || v.doc_boleto == undefined ){
					$('.boleto_doc').eq(i).addClass("has-error");

					var formControl = $('.boleto_doc').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O documento do boleto é obrigatório')
						.attr("data-original-title", 'O documento do boleto é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_boleto == "" || v.num_boleto == 0 || v.num_boleto == undefined ){
					$('.boleto_num').eq(i).addClass("has-error");

					var formControl = $('.boleto_num').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O Núm. Cheque é obrigatório')
						.attr("data-original-title", 'O Núm. Cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}
			});

			//ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 8){
			if(empty(ng.pagamento.id_banco)){
				$("#pagamento_id_banco").addClass("has-error");
				var formControl = $("#pagamento_id_banco")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Selecione o banco')
					.attr("data-original-title", 'Selecione o banco');
				formControl.tooltip();
				error ++ ;
			}
			if(empty(ng.pagamento.agencia_transferencia)){
				$("#pagamento_agencia_transferencia").addClass("has-error");
				var formControl = $("#pagamento_agencia_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o número da agência')
					.attr("data-original-title", 'Informe o número da agência');
				formControl.tooltip();
				error ++ ;
			}
			if(empty(ng.pagamento.conta_transferencia)){
				$("#pagamento_conta_transferencia").addClass("has-error");
				var formControl = $("#pagamento_conta_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o número da conta de origem')
					.attr("data-original-title", 'Informe o número da conta de origem');
				formControl.tooltip();
				error ++ ;
			}
			if(empty(ng.pagamento.proprietario_conta_transferencia)){
				$("#proprietario_conta_transferencia").addClass("has-error");
				var formControl = $("#proprietario_conta_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o Proprietário da conta')
					.attr("data-original-title", 'Informe o Proprietário da conta');
				formControl.tooltip();
				error ++ ;
			}
			if(empty(ng.pagamento.id_conta_transferencia_destino)){
				$("#pagamento_id_conta_transferencia_destino").addClass("has-error");
				var formControl = $("#pagamento_id_conta_transferencia_destino")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe a conta de destino')
					.attr("data-original-title", 'Informe a conta de origem');
				formControl.tooltip();
				error ++ ;
			}
		}

		if(error > 0){
			return;
		}

		if(ng.pagamento.id_forma_pagamento != 2 || ng.pagamento.id_forma_pagamento != 4 ){
			ng.pagamento.data_pagamento   = $(".data-cc").val();
			console.log(ng.pagamento.data_pagamento);
		}
				

		if(ng.pagamento.id_forma_pagamento == 6 || ng.pagamento.id_forma_pagamento == 2 || ng.pagamento.id_forma_pagamento == 4){
			ng.pagamento.status 		  = 0 ;
		}

		if(ng.pagamento.id_forma_pagamento == 5){
			ng.pagamento.status = 1 ;
		}

		if((ng.pagamento.id_forma_pagamento == 6 || ng.pagamento.id_forma_pagamento == 2 || ng.pagamento.id_forma_pagamento == 4) && (ng.pagamento.parcelas ==  undefined || ng.pagamento.parcelas ==  '') ){
			ng.pagamento.parcelas = 1 ;
		}

		var push = true ;

		if(ng.pagamento.id_forma_pagamento == 2){


			$.each(ng.recebidos,function(a,b){
				if(Number(b.id_forma_pagamento) == 2){
					ng.recebidos.splice(a,1);
				}
			});


			var valor_parcelas = ng.pagamento.valor / ng.pagamento.parcelas;
			var count = 0 ;
			ng.pg_cheques = [];
			$.each(ng.cheques,function(index,value){
				value.id_forma_pagamento  	= ng.pagamento.id_forma_pagamento;
				value.valor 				= Math.round(valor_parcelas * 100) / 100;
				value.id_maquineta 			= ng.pagamento.id_maquineta;
				value.parcelas 				= 1 ;
				value.data_pagamento		= formatDate($('.chequeData').eq(count).val());
				value.obs_pagamento         = empty(ng.pagamento.obs_pagamento) ? null : ng.pagamento.obs_pagamento ;
			//value.valor_pagamento		= valor_parcelas;
				ng.pg_cheques.push(value);
				count ++ ;
			});
		}else if(ng.pagamento.id_forma_pagamento == 4){


			$.each(ng.recebidos,function(a,b){
				if(Number(b.id_forma_pagamento) == 4){
					ng.recebidos.splice(a,1);
				}
			});


			var valor_parcelas = ng.pagamento.valor / ng.pagamento.parcelas;
			var count = 0 ;
			ng.pg_boletos = [];
			$.each(ng.boletos,function(index,value){
				value.id_forma_pagamento  	= ng.pagamento.id_forma_pagamento;
				value.valor 				= Math.round(valor_parcelas * 100) / 100;
				value.id_maquineta 			= ng.pagamento.id_maquineta;
				value.parcelas 				= 1 ;
				value.data_pagamento		= formatDate($('.boletoData').eq(count).val());
			//value.valor_pagamento		= valor_parcelas;
				value.obs_pagamento         = empty(ng.pagamento.obs_pagamento) ? null : ng.pagamento.obs_pagamento ;
				ng.pg_boletos.push(value);
				count ++ ;
			});
		}

		console.log(ng.pg_boletos);

		if(ng.pagamento.id_forma_pagamento == 3){
			$.each(ng.recebidos,function(x,y){
				if(Number(y.id_forma_pagamento) == 3){
					ng.recebidos[x].valor = ng.recebidos[x].valor + ng.pagamento.valor ;
					ng.recebidos[x].obs_pagamento         = empty(ng.pagamento.obs_pagamento) ? null : ng.pagamento.obs_pagamento ;
					push = false ;
				}
			});
		}

		if(push){
			if(ng.pagamento.id_forma_pagamento == 8){
				var item = {
								id_forma_pagamento 				 : ng.pagamento.id_forma_pagamento,
								valor              				 : ng.pagamento.valor,
								id_maquineta	   				 : ng.pagamento.id_maquineta,
								parcelas           				 : ng.pagamento.parcelas,
								id_vale_troca     				 : ng.pagamento.id_vale_troca,
								agencia_transferencia            : ng.pagamento.agencia_transferencia,
								conta_transferencia              : ng.pagamento.conta_transferencia,
								proprietario_conta_transferencia : ng.pagamento.proprietario_conta_transferencia,
								id_conta_transferencia_destino   : ng.pagamento.id_conta_transferencia_destino,
								id_conta_bancaria				 : ng.pagamento.id_conta_transferencia_destino,
								id_banco                         : ng.pagamento.id_banco,
								status                           : ng.pagamento.status,
								id_plano_conta                   : ng.pagamento.id_plano_conta
						   };
			}else{
				var item = {
								id_forma_pagamento 				 : ng.pagamento.id_forma_pagamento,
								valor              				 : ng.pagamento.valor,
								id_maquineta	   				 : ng.pagamento.id_maquineta,
								parcelas           				 : ng.pagamento.parcelas,
								id_vale_troca     				 : ng.pagamento.id_vale_troca,
								status                           : ng.pagamento.status,
								id_conta_bancaria				 : ng.pagamento.id_conta_bancaria,
								id_plano_conta                   : ng.pagamento.id_plano_conta

						   };
			}
			item.data_pagamento = ng.pagamento.data_pagamento ;
			item.obs_pagamento         = empty(ng.pagamento.obs_pagamento) ? null : ng.pagamento.obs_pagamento ;
			$.each(ng.formas_pagamento,function(i,v){
				if(v.id == ng.pagamento.id_forma_pagamento){
					item.forma_pagamento = v.nome ;
					return;
				}
			});
			ng.recebidos.push(item);
		}
		ng.totalPagamento();
		ng.pagamento = {} ;
		$('.data-cc').val('');
		console.log(ng.recebidos);
	}
	ng.fornecedor = {}
	ng.salvarPagamento = function(){
		ng.modalProgressoPagamento('show');
		var pagamentos   = [] ;
		var Today        = new Date();
		var data_atual   = Today.getDate()+"/"+(Today.getMonth()+1)+"/"+Today.getFullYear();
		var error 		 = 0 ;

		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass('has-error');

		if((ng.flgTipoLancamento == 0) && (ng.cliente.id ==  undefined || ng.cliente.id ==  '')){
			error ++ ;
			$("#id_cliente").addClass("has-error");

			var formControl = $("#id_cliente")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'A escolha do cliente é obrigatório')
				.attr("data-original-title", 'A escolha do cliente é obrigatório');
			formControl.tooltip();
		}

		if((ng.flgTipoLancamento == 1) && (ng.fornecedor.id ==  undefined || ng.fornecedor.id ==  '')){
			error ++ ;
			$("#id_fornecedor").addClass("has-error");

			var formControl = $("#id_fornecedor")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'A escolha do fornecedor é obrigatória')
				.attr("data-original-title", 'A escolha do cliente é obrigatório');
			formControl.tooltip();
		}

		if(error > 0){
			ng.modalProgressoPagamento('hide');
			return false;
		}

		var recebidos = angular.copy(ng.recebidos);

		$.each(recebidos, function(i,v){
			var parcelas = Number(v.parcelas);

			if(Number(ng.flgTipoLancamento) == 0)
				v.id_cliente			= ng.cliente.id;
			if(Number(ng.flgTipoLancamento) == 1)
				v.id_fornecedor			= ng.fornecedor.id;
			v.id_forma_pagamento		= v.id_forma_pagamento;
			v.valor_pagamento			= v.valor;
			v.status         			= v.status;
			v.id_empreendimento			= ng.userLogged.id_empreendimento;
			v.id_maquineta				= v.id_maquineta ;
			v.taxa_maquineta			= v.taxa_maquineta ;
			v.obs_pagamento             = empty(v.obs_pagamento) ? null : v.obs_pagamento ;

			if(Number(v.id_forma_pagamento) == 6){

				var valor_parcelas 	 	  = v.valor/parcelas ;
				var next_date		 	  = v.data_pagamento;
				var itens_prc        	  = [] ;
				var arr_date 		 	  = next_date.split('/');
				var next_date_dia_init    = parseInt(arr_date[0]) ;

				for(var count = 0 ; count < parcelas ; count ++){
					var item 			 = angular.copy(v);
					item.valor_pagamento = valor_parcelas ;
					item.data_pagamento  = formatDate(next_date) ;
					item.obs_pagamento   = v.obs_pagamento  ;

					var arr_date 		 = next_date.split('/');
					var objDate   		 = new Date(parseInt(arr_date[2]), parseInt(arr_date[1]) , 1);
					var Diasnext		 = ultimoDiaDoMes(objDate);

					var next_date_dia        = next_date_dia_init > Diasnext ? Diasnext : next_date_dia_init ;
					if(next_date_dia < 10 )
						next_date_dia = '0'+next_date_dia;
					else
						next_date_dia = next_date_dia;
					var next_date_ano    = parseInt(arr_date[2]) ;
					var next_date_mes    = parseInt(arr_date[1]) ;
					if(next_date_mes == 12){
						next_date_mes		 = '01';
						next_date_ano ++ ;
					}else{
						if(next_date_mes < 10 )
							next_date_mes = '0'+(next_date_mes+1);
						else
							next_date_mes = next_date_mes+1;
					}
					next_date = next_date_dia+"/"+next_date_mes+"/"+next_date_ano ;
					console.log(next_date);

					itens_prc.push(item);
				}

				pagamentos.push({id_forma_pagamento : v.id_forma_pagamento ,id_tipo_movimentacao: 3, parcelas:itens_prc});

			}else if(Number(v.id_forma_pagamento) == 2){
				$.each(ng.pg_cheques,function(i_cheque, v_cheque){
					v.id_banco 				= v_cheque.id_banco ;
					v.num_conta_corrente 	= v_cheque.num_conta_corrente ;
					v.num_cheque 			= v_cheque.num_cheque ;
					v.flg_cheque_predatado 	= v_cheque.flg_cheque_predatado ;
					v.data_pagamento 		= v_cheque.data_pagamento ;
					v.valor_pagamento 		= v_cheque.valor_pagamento ;
					v_push = angular.copy(v);
					pagamentos.push(v_push);
				});
			}else if(Number(v.id_forma_pagamento) == 4){
				$.each(ng.pg_boletos,function(i_boleto, v_boleto){
					v.id_banco 				= v_boleto.id_banco ;
					v.data_pagamento 		= v_boleto.data_pagamento ;
					v.valor_pagamento 		= v_boleto.valor_pagamento ;
					v.doc_boleto            = v_boleto.doc_boleto ;
					v.num_boleto            = v_boleto.num_boleto ;
					v.status                = v_boleto.status_pagamento ;
					v_push = angular.copy(v);
					pagamentos.push(v_push);
				});
			}else {
				v.data_pagamento  = formatDate(v.data_pagamento) ;
				pagamentos.push(v);
			}
		});

		

		if(ng.flgTipoLancamento == 0){
			var url   = "cliente/pagamento"
			var dados = {
							pagamentos:pagamentos,
							id_cliente:ng.cliente.id,
							id_empreendimento:ng.userLogged.id_empreendimento
						}
		}else if(ng.flgTipoLancamento == 1){
			var url = "fornecedor_pagamento";
			var dados = {
							pagamentos	 :pagamentos,
							id_fornecedor:ng.fornecedor.id
						}
		}

		console.log(dados);
		aj.post(baseUrlApi()+url, dados)
			.success(function(data, status, headers, config) {
				if(typeof data.msg_agenda == "object"){
					var dias_semanas    = {1:'Segunda-Feira',2:'Terça-Feira',3:'Quarta-Feira',4:'Quinta-Feira',5:'Sexta-Feira',6:'Sábado',7:'Domingo'};
					var out_dias_agenda = data.msg_agenda.out_dias_agenda ; 
					var out_valores     = data.msg_agenda.out_valores ;
					var msg             = "Os dias da semana para pagamentos são " ;
					if(out_dias_agenda != undefined){
						$.each(out_dias_agenda.dentro,function(i,x){
							msg += "&nbsp"+(dias_semanas[x])+",";
						});
						msg = msg.substr(0,msg.length-1);
						msg += "<br/>Os dias abaixo não são validos:";
						$.each(out_dias_agenda.fora,function(i,x){
							msg += "<br/>&nbsp&nbsp&nbsp"+formatDateBR(x.dta)+" ("+dias_semanas[x.dia]+")";
						});
						$dialogs.notify('Atenção!','<strong style="color:black">'+msg+'</strong>');
					}else if(out_valores != undefined){
						var msg  = "O pagamento foi realizado, mas informamos que o valor máximo para pagamento dos dias abaixo foi excedido:" ;
						$.each(out_valores,function(i,x){
							msg += "<br>&nbsp&nbsp&nbsp"+formatDateBR(x.dta)+" ("+dias_semanas[x.dia_semana]+")";
						});
						$dialogs.notify('Atenção!','<strong style="color:black">'+msg+'</strong>');
					}
				}
				ng.modalProgressoPagamento('hide');
				ng.vlr_saldo_devedor = data.vlr_saldo_devedor ;
				ng.id_controle_pagamento = data.id_controle_pagamento ;
				ng.refresh();
				//ng.showModalPrint();
			})
			.error(function(data, status, headers, config) {
				ng.modalProgressoPagamento('hide');
				if(status == 406){
					var dias_semanas    = {1:'Segunda-Feira',2:'Terça-Feira',3:'Quarta-Feira',4:'Quinta-Feira',5:'Sexta-Feira',6:'Sábado',7:'Domingo'};
					var out_dias_agenda = data.out_dias_agenda ; 
					var out_valores     = data.out_valores ;
					var msg             = "Os dias da semana para pagamentos são " ;
					if(out_dias_agenda != undefined){
						$.each(out_dias_agenda.dentro,function(i,x){
							msg += "&nbsp"+(dias_semanas[x])+",";
						});
						msg = msg.substr(0,msg.length-1);
						msg += "<br/>Os dias abaixo não são validos:";
						$.each(out_dias_agenda.fora,function(i,x){
							msg += "<br/>&nbsp&nbsp&nbsp"+formatDateBR(x.dta)+" ("+dias_semanas[x.dia]+")";
						});
						$dialogs.notify('Atenção!','<strong style="color:black">'+msg+'</strong>');
					}else if(out_valores != undefined){
						var msg  = "O valor máximo para pagamento dos dias abaixo foi excedido:" ;
						$.each(out_valores,function(i,x){
							msg += "<br>&nbsp&nbsp&nbsp"+formatDateBR(x.dta)+" ("+dias_semanas[x.dia_semana]+")";
						});
						$dialogs.notify('Atenção!','<strong style="color:black">'+msg+'</strong>');
					}else{
						alert('Ocorreu um erro');
					}
				}else{
					alert('Ocorreu um erro');
				}
			});

	}

	ng.deleteRecebidos = function(index){
		ng.recebidos.splice(index,1);
		ng.totalPagamento();
	}

	ng.totalPagamento = function(){
		var total = 0 ;
		$.each(ng.recebidos,function(i,v){
			total += Number(v.valor);
		});
		ng.total_pg = Math.round( total * 100) /100 ;
	}

	ng.modalProgressoPagamento = function(acao){
		if(acao == 'show')
			$('#modal_progresso_pagamento').modal({ backdrop: 'static',keyboard: false});
		else if (acao == 'hide')
			$('#modal_progresso_pagamento').modal('hide');
	};

	ng.loadConfig = function(){
		var error = 0 ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracoes = data ;
			})
			.error(function(data, status, headers, config) {
				console.log('Erro ao busca configuraçãoes so sistema');
			});
	}

	var ant_cheque 		  = false ;
	var ant_boleto 		  = false ;
	var ant_transferencia = false ;

	ng.calculaColspan = function(init){
		
		var qtd_cheque 			= 3 ;
		var qtd_boleto 			= 2 ;
		var qtd_transferencia 	= 3 ;

		if(ng.config_table.cheque)
			init = init+qtd_cheque ;
		else if(ant_cheque)
			init = init-qtd_cheque ;

		if(ng.config_table.boleto)
			init = init+qtd_boleto ;
		else if(ant_boleto)
			init = init-qtd_boleto ;

		if(ng.config_table.transferencia)
			init = init+qtd_transferencia ;
		
		else if(ant_transferencia)
			init = init-qtd_transferencia ;

		ant_cheque 			= ng.config_table.cheque ;
		ant_boleto 			= ng.config_table.boleto ;
		ant_transferencia	= ng.config_table.transferencia ;

		return init

	}

	ng.showModalPrint = function(){
		$('#modal-print-lancamento').modal({
		  backdrop: 'static',
		  keyboard: false
		});
		$('.modal-backdrop.in').css({opacity:1,'background-color':'#C7C7C7'});
	}
	ng.vendaPrint = {} ;
	ng.printPagamentos = function(item){
		ng.itensPrint = [] ;
		ng.vendaPrint.nome_cliente 			  = item.nome ;
		ng.vendaPrint.id_cliente   			  = item.id_clienteORfornecedor;
		ng.vendaPrint.id_controle_pagamento   = item.id_controle_pagamento;
		ng.vendaPrint.id_parcelamento   	  = item.id_parcelamento == null ? item.id : item.id_parcelamento ;
		ng.vendaPrint.id_lancamento           = item.id;
		console.log(item);
		$("#modal-print").modal("show");
		if(item.id_forma_pagamento == 6){
				aj.get(baseUrlApi()+"lancamentos/parcelas/"+ng.vendaPrint.id_parcelamento )
					.success(function(data, status, headers, config) {
						parcelas = data ;
						aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ng.userLogged.id_empreendimento+"/"+ng.vendaPrint.id_cliente)
							.success(function(data, status, headers, config) {
								ng.vendaPrint.vlr_saldo_devedor = Number(data.vlr_saldo_devedor);

								if(parcelas.length > 1){
									dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Este pagamento faz parte de um parcelamento em '+parcelas.length+'x. Deseja imprimir todas as parcelas ? </strong>');

									dlg.result.then(function(btn){
										console.log(parcelas);
										ng.itensPrint = parcelas;
										$("#modal-print").modal("show")
									}, function(){
										$.each(parcelas,function(i,v){
											if(v.id == ng.vendaPrint.id_lancamento){
												ng.itensPrint = [v];
											}
										});
										$("#modal-print").modal("show");
									});
								}else{
									$.each(parcelas,function(i,v){
										if(v.id == ng.vendaPrint.id_lancamento){
											ng.itensPrint = [v];
										}
									});
								}


							})
							.error(function(data, status, headers, config) {

							})
							.error(function(data, status, headers, config) {

						});
					})
					.error(function(data, status, headers, config) {

					});
		}else{
			aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ng.userLogged.id_empreendimento+"/"+ng.vendaPrint.id_cliente)
				.success(function(data, status, headers, config) {
					ng.vendaPrint.vlr_saldo_devedor = Number(data.vlr_saldo_devedor);
					ng.itensPrint = [item];
					$("#modal-print").modal("show")
				})
				.error(function(data, status, headers, config) {


					$("#modal-print").modal("show")
				})
				.error(function(data, status, headers, config) {

			});
		}
	}

	ng.printDiv = function(id,pg) {

		var contentToPrint, printWindow;

		contentToPrint = '<div class="col-sm-12" style="margin-bottom: 30px;">'+$('#topo_print').html()+'</div><br/><br/>';
		contentToPrint = contentToPrint+' '+$('#tbl_print').html() + '' + $('#tbl_print_pg').html() ;
		printWindow = window.open(pg);

	    printWindow.document.write("<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet'>");
		printWindow.document.write("<link href='css/font-awesome.min.css' rel='stylesheet'>");
		printWindow.document.write("<link href='css/pace.css' rel='stylesheet'>");
		printWindow.document.write("<link href='css/endless.min.css' rel='stylesheet'>");
		printWindow.document.write("<link href='css/endless-skin.css' rel='stylesheet'>");

		printWindow.document.write("<style type='text/css' media='print'>@page { size: landscape; padding: 10px; }</style>");
		printWindow.document.write("<style type='text/css'>body{  padding-top: 20px;padding-bottom: 20px; }</style>");


		printWindow.document.write(contentToPrint);

		printWindow.window.print();
		printWindow.document.close();
		printWindow.focus();
	}

	ng.cancelar = function(){
			$("#modal-print").modal('hide');
			$("#modal-print-lancamento").modal('hide');
	}

	ng.refresh = function(){
		window.location="lancamentos.php";
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
	ng.busca_avancada = false;
	ng.buscaAvancada = function(){
		$("select").trigger("chosen:updated");
		ng.busca_avancada = !ng.busca_avancada ;
	}

	var dtaAtual = new Date();

	var actualMonth = parseInt(dtaAtual.getMonth() + 1);
	if(actualMonth < 10)
		actualMonth = "0" + actualMonth;

	var actualYear = dtaAtual.getFullYear();

	var lastDay = parseInt(ultimoDiaDoMes(new Date()));
	if(lastDay < 10)
		lastDay = "0" + lastDay;

	$("#dtaInicial").val( "01/"+actualMonth+"/"+actualYear );
	$("#dtaFinal").val( lastDay+"/"+actualMonth+"/"+actualYear );
	$(".data-cc").val('11-10-2010');

	ng.configuracao = null ;
	ng.loadConfig = function(){
		var error = 0 ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracao = data ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.configuracao = false ;
				}
			});
	}

	ng.load(0,20);
	ng.loadPlanoContas();
	ng.loadContas();
	ng.loadBancos();
	ng.loadMaquinetas();
	ng.loadConfig();
	$('#sizeToggle').trigger("click");
});
