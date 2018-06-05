app.controller('LancamentosController', function($scope, $http, $window, $dialogs, UserService, ConfigService){

	var ng = $scope
		aj = $http;

	ng.detalhar = false ;
	ng.baseUrl 					= baseUrl();
	ng.userLogged 				= UserService.getUserLogado();
	ng.configuracoes	 		= ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.empreendimento 			= {};
    ng.empreendimentos 			= [];
    ng.bancos           		= [];
    ng.contas           		= [];
    ng.busca					= {dsc_conta_bancaria : "",clientes:"",fornecedores:"",op_valor:"" }
    ng.paginacao 				= {pagamentos:null}
    ng.vlr_total_periodo 		= 0;
    ng.dataGroups 				= [];
    ng.recorrencias			    = [
    								{periodo:"Semanal",dias:7},{periodo:"Quizenal",dias:15},{periodo:"Mensal",dias:30},
    								{periodo:"trimestral",dias:90},{periodo:"Semestral",dias:180},{periodo:"Anual",dias:365}
    							  ];
    ng.cheques					= [{id_banco:null,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0}];
    ng.recebidos 				= [] ;
    ng.boletos					= [{id_banco:null,num_conta_corrente:null,num_cheque:null,status_pagamento:0}];
    ng.roleList 				= [];
    ng.pagamento         		= {status:0};
    ng.status 					= 0 ;
    ng.flgTipoLancamento 		= 0 ;
    ng.formas_pagamento 		= [
		{nome:"Cheque",id:2},
		{nome:"Dinheiro",id:3},
		{nome:"Boleto Bancário",id:4},
		{nome:"Cartão de Débito",id:5},
		{nome:"Cartão de Crédito",id:6},
		{nome:"Transferência",id:8},
		{nome:"Promessa de Pagamento",id:9}
	  ]
    ng.editing 					= false;
    ng.cliente          		= {} ;
    ng.config_table     		= {
		cheque: false,
		boleto: false,
		transferencia: false,
		conta_bancaria: false,
		forma_pagamento: false,
		flg_considerar_pendente_saldo: false
	};

	ng.npAutocompleteFornecedorOptions = {
		url: baseUrlApi()+"fornecedores?(id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+")",
		nameAttr: 'nome_fornecedor',
		valueAttr: 'id',
		dataHolder: 'fornecedores',
		searchParam: 'frn->nome_fornecedor[exp]'
	};

	ng.npAutocompleteClienteOptions = {
		url: baseUrlApi()+"usuarios?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+")",
		nameAttr: 'nome',
		valueAttr: 'id',
		dataHolder: 'usuarios',
		searchParam: 'usu->nome[exp]'
	};

	ng.transferencia = {};
	ng.transferencia.dta_transferencia = "";
	ng.transferencia.id_fornecedor = ng.configuracoes.id_fornecedor_movimentacao_caixa;
	ng.transferencia.id_cliente = ng.configuracoes.id_cliente_movimentacao_caixa;
	ng.transferencia.id_empreendimento = ng.userLogged.id_empreendimento;
	ng.salvarTransferencia = function(){
		ng.transferencia.dta_transferencia = (!empty($("#dta_transferencia").val())) ? formatDate($("#dta_transferencia").val()) : "";
		if (empty(ng.transferencia.dta_transferencia)) {
			ng.mensagens('alert-warning','<strong>Selecione uma data</strong>','.alert-pagamento');
			return false;
		}
		if (empty(ng.transferencia.id_conta_bancaria_origem)) {
			ng.mensagens('alert-warning','<strong>Selecione uma conta de origem</strong>','.alert-pagamento');
			return false;
		}
		if (empty(ng.transferencia.id_conta_bancaria_destino)) {
			ng.mensagens('alert-warning','<strong>Selecione uma conta de destino</strong>','.alert-pagamento');
			return false;
		}
		if (ng.transferencia.id_conta_bancaria_destino == ng.transferencia.id_conta_bancaria_origem) {
			ng.mensagens('alert-warning','<strong>Não é possível fazer uma transferência para a mesma conta</strong>','.alert-pagamento');
			return false;
		}
		if (empty(ng.transferencia.vlr_transferencia)) {
			ng.mensagens('alert-warning','<strong>Preencha o campo Valor</strong>','.alert-pagamento');
			return false;
		}
		aj.post(baseUrlApi()+"lancamento/transferencia", ng.transferencia)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>Lançamento atualizado com sucesso</strong>','.alert-pagamento');

				ng.transferencia.dta_transferencia = "";
				ng.dta_pagamento = "";
				ng.transferencia.id_conta_bancaria_origem = "";
				ng.transferencia.id_conta_bancaria_destino = "";
				ng.transferencia.vlr_transferencia = "";
				ng.transferencia.obs_transferencia = "";
				ng.transferencia.id_fornecedor = ng.configuracoes.id_fornecedor_movimentacao_caixa;
				ng.transferencia.id_cliente = ng.configuracoes.id_cliente_movimentacao_caixa;
				ng.transferencia.id_empreendimento = ng.userLogged.id_empreendimento;
			})
			.error(function(data, status, headers, config){
				ng.msg_error = "Erro ao lançar transferência";	
			});
	}

	ng.showAnexo = function(item){
		if(!empty(item.pth_anexo)){
			item = {
				path: item.pth_anexo.substring(item.pth_anexo.indexOf('assets'), item.pth_anexo.length)
			};
			$window.open(baseUrl()+item.path)
		} else {
			return false;
		}
	}

    ng.pagamento_edit = {} ;
    ng.modalChangeStatusPagamento = function(item){
    	ng.pagamento_edit = angular.copy(item) ;
    	ng.pagamento_edit.flg_tipo_lancamento_anterior = ng.pagamento_edit.flg_tipo_lancamento;
    	ng.flg_tipo_lancamento_anterior = ng.pagamento_edit.flg_tipo_lancamento;
    	ng.pagamento_edit.id_conta_bancaria = Number(ng.pagamento_edit.id_conta_bancaria);
    	ng.pagamento_edit.valor_pagamento = Number(ng.pagamento_edit.valor_pagamento);
    	ng.pagamento_edit.vlr_final = ng.pagamento_edit.valor_pagamento;
    	ng.pagamento_edit.vlr_juros = Number(ng.pagamento_edit.vlr_juros);
    	ng.pagamento_edit.vlr_multa = Number(ng.pagamento_edit.vlr_multa);
    	ng.pagamento_edit.id_plano_conta = Number(ng.pagamento_edit.id_plano_conta);
    	$("#dta_change_pagamento").val(formatDateBR(item.data_pagamento));
    	if (!empty(item.dta_vencimento)) {
	    	$("#dta_vencimento").val(formatDateBR(item.dta_vencimento));
	    }
	    if (!empty(item.dta_competencia)) {
	    	$("#dta_competencia").val(formatDateBR(item.dta_competencia));
	    }
    	$("#modal_change_date_pagamento").modal('show');
    	setTimeout(function() {
    		switch(item.flg_tipo_lancamento){
    			case 'D':
    				$("#txtNomeFornecedor").val(item.nome_clienteORfornecedor);
    				break;
    			case 'C':
    				$("#txtNomeCliente").val(item.nome_clienteORfornecedor);
    				break;
    		}
    	}, 1);
    	ng.clique_tipo = 0;
    	if (ng.pagamento_edit.id_forma_pagamento == 5 || ng.pagamento_edit.id_forma_pagamento == 6) {
    		ng.loadMaquinetas();
    		ng.loadBandeiras(ng.pagamento_edit.id_forma_pagamento);	
    	}
    }

    ng.flg_valid_venda = 0;

    ng.clearFieldsByPaymentMethod = function(id){
		ng.pagamento_edit.doc_boleto = null;
		ng.pagamento_edit.num_boleto = null;
		ng.pagamento_edit.id_banco = null;
		ng.pagamento_edit.num_conta_corrente = null;
		ng.pagamento_edit.num_cheque = null;
		ng.pagamento_edit.id_maquineta = null;
		ng.pagamento_edit.id_bandeira = null;
		ng.pagamento_edit.agencia_transferencia = null;
		ng.pagamento_edit.conta_transferencia = null;
		if (ng.pagamento_edit.id_forma_pagamento == 5 ||ng.pagamento_edit.id_forma_pagamento == 6) {
    		ng.loadBandeiras(id);
    	}
    }

    ng.updateStatusLancamento = function(item) {
    	if(!empty(item.id_clienteORfornecedor)) {
	    	ng.pagamento_edit.data_pagamento = (!empty($("#dta_change_pagamento").val())) ? formatDate($("#dta_change_pagamento").val()) : null;
	    	ng.pagamento_edit.dta_vencimento = (!empty($("#dta_vencimento").val())) ? formatDate($("#dta_vencimento").val()) : null;
	    	ng.pagamento_edit.dta_competencia = (!empty($("#dta_competencia").val())) ? formatDate($("#dta_competencia").val()) : null;

	    	$("#modal_change_date_pagamento").modal('hide');

	    	dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja alterar o status deste lançamento?</strong>');

			dlg.result.then(function(btn){
				aj.post(baseUrlApi()+"lancamento/status/update", ng.pagamento_edit)
					.success(function(data, status, headers, config) {
						ng.mensagens('alert-success','<strong>Lançamento atualizado com sucesso</strong>','.alert-delete');
						ng.reset();
						ng.load(0,20);
					})
					.error(defaulErrorHandler);
			}, undefined);
		}
		else {
			ng.mensagens(
				'alert-danger',
				'<strong>Você precisa informar o '+ ((item.flg_tipo_lancamento == 'C') ? 'Cliente' : 'Fornecedor') +'</strong>',
				'.alert-edit'
			);
		}
	}

	ng.changeIdLancamento = function(flg_tipo_lancamento_novo){
		if(ng.flg_tipo_lancamento_anterior != flg_tipo_lancamento_novo){
			if(!empty(ng.pagamento_edit.id_ref)){
				var id_copy = angular.copy(ng.pagamento_edit.id);
				ng.pagamento_edit.id = angular.copy(ng.pagamento_edit.id_ref);
				ng.pagamento_edit.id_ref = id_copy;
			}
			else {
				ng.pagamento_edit.id_ref = angular.copy(ng.pagamento_edit.id);
				ng.pagamento_edit.id = null;
			}

			ng.flg_tipo_lancamento_anterior = flg_tipo_lancamento_novo;
		}
		ng.pagamento_edit.id_clienteORfornecedor = null;
		ng.pagamento_edit.nome_clienteORfornecedor = null;
		ng.pagamento_edit.nome = null;
		$("#txtNomeFornecedor").val('');
		$("#txtNomeCliente").val('');
	}

	ng.delete = function(item,tipo){
		if(tipo == 'cliente'){
			var url = 'fornecedor/pagamento/delete/'
		}else if(tipo == 'fornecedor'){
			var url = 'cliente/pagamento/delete/'
		}

		if (!empty(item.id_ref)) {
			var id_ref = item.id_ref;
		}

		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este lançamento?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+url+item.id+"/"+ id_ref)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Lançamento excluido com sucesso</strong>','.alert-delete');
					ng.reset();
					ng.load(0,20);
				})
				.error(defaulErrorHandler);
		}, undefined);
	}


    ng.showBoxNovo = function(onlyShow){
    	//ng.editing = !ng.editing;

    	if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show(400,function(){$("select").trigger("chosen:updated");});
		}
		else {
			$('#box-novo').toggle(400, function(){
				if($(this).is(':visible')){
					$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
				}else{
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
			});
			$("select").trigger("chosen:updated");
		}
		$("select").trigger("chosen:updated");
	}

	var nParcelasAnt = 1 ;

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
		var queryString = "?id_empreendimento="+ ng.userLogged.id_empreendimento;

		if ( dataInicial != "" &&  dataFinal != "" ) {
			var data_arr = dataInicial.split('/');
			dataInicial = data_arr[2]+"-"+data_arr[1]+"-"+data_arr[0];
			data_arr = dataFinal.split('/');
			dataFinal = data_arr[2]+"-"+data_arr[1]+"-"+data_arr[0];
			queryString += "&" + $.param({data_pagamento:{exp:"between '"+dataInicial+" 00:00:00' and '"+dataFinal+" 23:59:59'"}}) ;
		}
		else
			return false;

		if(ng.busca.dsc_conta_bancaria != ""){
			queryString += "&id_conta_bancaria="+ng.busca.dsc_conta_bancaria;
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

		ng.loadSaldoAnterior(dataInicial);

		aj.get(baseUrlApi()+"lancamentos/financeiros"+queryString)
			.success(function(data, status, headers, config) {
				ng.msg_error = null;
				ng.pagamentos           = data.pagamentos;
				ng.paginacao.pagamentos = data.paginacao;

				ng.vlr_total_periodo 	= 0;
				ng.vlr_total_credito 	= 0;
				ng.vlr_total_debito 	= 0;

				angular.forEach(ng.pagamentos, function(lancamento, index){
					if(lancamento.flg_tipo_lancamento == 'C' && (lancamento.status_pagamento == 1 || (lancamento.status_pagamento == 0 && ng.config_table.flg_considerar_pendente_saldo))) // CRÉDITO
						ng.vlr_total_credito += parseFloat(lancamento.valor_pagamento);
					else if(lancamento.flg_tipo_lancamento == 'D' && (lancamento.status_pagamento == 1 || (lancamento.status_pagamento == 0 && ng.config_table.flg_considerar_pendente_saldo))) // DÉBITO
						ng.vlr_total_debito += parseFloat(lancamento.valor_pagamento);
					lancamento.vlr_saldo = (ng.vlr_total_credito - ng.vlr_total_debito);
				});

				ng.dataGroups = _.groupBy(ng.pagamentos, "data_pagamento");
				$.each(ng.dataGroups, function(i, item) {
					var obj = { vlr_total_item: 0, items: item };
					var crd = 0;
					var deb = 0;
					var sld = 0;
					var a_receber = 0 ;
					var a_pagar   = 0 ;
					
					angular.forEach(item, function(xItem, x){
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
					ng.vlr_total_periodo += (obj.vlr_total_item);
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
	ng.loadSaldoAnterior = function(dataInicial){
		
		aj.get(baseUrlApi()+"lancamentos/saldo_anterior_despesa/"+ng.userLogged.id_empreendimento+"/"+dataInicial+"/"+ng.busca.dsc_conta_bancaria)
			.success(function(data, status, headers, config){
				ng.saldo_anterior_despesa = data;
			})
			.error(function(data, status, headers, config) {

			});
		aj.get(baseUrlApi()+"lancamentos/saldo_anterior_receita/"+ng.userLogged.id_empreendimento+"/"+dataInicial+"/"+ng.busca.dsc_conta_bancaria)
			.success(function(data, status, headers, config){
				ng.saldo_anterior_receita = data;
			})
			.error(function(data, status, headers, config) {

			});
	}

	
	ng.loadVendaByIdLancamento = function(item){
		aj.get(baseUrlApi()+"lancamentos/venda_by_lancamento_id/"+item.id)
			.success(function(data, status, headers, config){
				ng.flg_valid_venda = 0;
				if (item.flg_tipo_lancamento == 'C') {
					ng.flg_valid_venda = 1;
				}
				ng.modalChangeStatusPagamento(item);
			})
			.error(function(data, status, headers, config) {
				ng.flg_valid_venda = 0;
				ng.modalChangeStatusPagamento(item);
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
		// ng.refresh();
		// ng.showModalPrint();
		ng.editing 		= false;
		ng.recebidos 	= [];
		ng.cliente 		= {};
		ng.fornecedor 	= {};
		ng.pg_cheques 	= [];
		ng.pg_boletos 	= [];
		ng.total_pg 	= 0;
		ng.pagamento 	= null;
		ng.cheques	 	= [{id_banco:null,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0}];
		ng.boletos	 	= [{id_banco:null,num_conta_corrente:null,doc_boleto:null,num_boleto:null}];
		ng.msg_error = "Faça um filtro para obter resultados";
		ng.pagamentos = null;
		ng.loadPlanoContas();
		
		$("#pagamentoData").val('');
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.loadPlanoContas = function() {
		ng.plano_contas = [{id:"",dsc_completa:"--- Selecione ---"}];
		aj.get(baseUrlApi()+"planocontas?tpc->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				$.each(data, function(i, item){
					data[i].cod_plano 			= (!empty(item.cod_plano)) ? parseInt(item.cod_plano, 10) : null;
					data[i].cod_plano_pai 		= (!empty(item.cod_plano_pai)) ? parseInt(item.cod_plano_pai, 10) : null;
					data[i].id 					= (!empty(item.id)) ? parseInt(item.id, 10) : null;
					data[i].id_empreendimento 	= (!empty(item.id_empreendimento)) ? parseInt(item.id_empreendimento, 10) : null;
					data[i].id_plano_pai 		= (!empty(item.id_plano_pai)) ? parseInt(item.id_plano_pai, 10) : null;
				});
				ng.roleList = data;
				ng.plano_contas = ng.plano_contas.concat(data);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.roleList = [];
			});
	}

	ng.loadPerfil = function () {
		ng.perfisNewCliente = [];

		aj.get(baseUrlApi()+"perfis?tpue->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.perfisNewCliente = data;
			});
	}

	ng.salvarCliente = function(){
		$("#list_clientes .has-error")
			.removeClass('has-error')
			.removeAttr("data-toggle")
			.removeAttr("data-placement")
			.removeAttr("title")
			.removeAttr("data-original-title");

		ng.new_cliente.empreendimentos = [{ id: ng.userLogged.id_empreendimento }];
		ng.new_cliente.id_empreendimento = ng.userLogged.id_empreendimento;
		var btn = $('#btn-salvar-cliente');
		btn.button('loading');
		
		var postData = angular.copy(ng.new_cliente);

		if(!empty(postData.dta_nacimento))
			postData.dta_nacimento = moment(postData.dta_nacimento, 'DD-MM-YYYY').format('YYYY-MM-DD');
		
		aj.post(baseUrlApi() + "cliente/cadastro/rapido", postData)
			.success(function(data, status, headers, config) {
				ng.addCliente(data.dados);
				btn.button('reset');
				ng.new_cliente = {};
				ng.enableNewFormCliente = false;
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');

				if(status == 406) {
		 			var errors = data;
		 			var count = 0;

		 			$.each(errors, function(i, item) {
		 				$("#"+i).addClass("has-error");
		 				var formControl = $($("#"+i))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", item)
		 					.attr("data-original-title", item);
		 				formControl.tooltip('show');
		 				count ++ ;
		 			});

		 			if(count == 0)
		 				ng.mensagens('alert-warning','<strong>Informe ao menos o nome ou CPF do cliente</strong>','.alert-cadastro-rapido-error');
			 	} else
			 		ng.mensagens('alert-danger','<strong>Ocorreu um erro fatal</strong>','.alert-cadastro-rapido');
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
    	ng.pagamento.id_banco = ""+angular.copy(item.id_banco);
    	ng.pagamento.agencia_transferencia = angular.copy(item.agencia);
    	ng.pagamento.conta_transferencia = angular.copy(item.conta);
    	ng.pagamento.proprietario_conta_transferencia = angular.copy(item.nome);
    	ng.pagamento.id_plano_conta = angular.copy(item.id_plano_contas_padrao);
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
				if(status == 404) {

				}
			});
	}

	ng.loadCliente= function(offset,limit) {
		ng.clientes = [] ;
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+")"+"&usu->flg_excluido=0";

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

	ng.salvarFornecedor = function(){
		$("#list_fornecedores .has-error")
			.removeClass('has-error')
			.removeAttr("data-toggle")
			.removeAttr("data-placement")
			.removeAttr("title")
			.removeAttr("data-original-title");

		ng.new_fornecedor.empreendimentos = [{ id: ng.userLogged.id_empreendimento }];
		ng.new_fornecedor.id_empreendimento = ng.userLogged.id_empreendimento;
		var btn = $('#btn-salvar-fornecedor');
		btn.button('loading');
		
		var postData = angular.copy(ng.new_fornecedor);

		if(!empty(postData.dta_nacimento))
			postData.dta_nacimento = moment(postData.dta_nacimento, 'DD-MM-YYYY').format('YYYY-MM-DD');
		
		aj.post(baseUrlApi() + "fornecedor", postData)
			.success(function(data, status, headers, config) {
				ng.addFornecedor(data.dados);
				btn.button('reset');
				ng.new_fornecedor = {};
				ng.enableNewFormFornecedor = false;
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');

				if(status == 406) {
		 			var errors = data;
		 			var count = 0;

		 			$.each(errors, function(i, item) {
		 				$("#"+i).addClass("has-error");
		 				var formControl = $($("#"+i))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", item)
		 					.attr("data-original-title", item);
		 				formControl.tooltip('show');
		 				count ++ ;
		 			});

		 			if(count == 0)
		 				ng.mensagens('alert-warning','<strong>Informe ao menos o nome ou CPF do cliente</strong>','.alert-cadastro-rapido-error');
			 	} else
			 		ng.mensagens('alert-danger','<strong>Ocorreu um erro fatal</strong>','.alert-cadastro-rapido');
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
    	$("#list_fornecedores").modal("hide");
    	ng.fornecedor 				= item;
    	ng.pagamento.id_fornecedor  = item.id;
    	ng.pagamento.id_banco = ""+angular.copy(item.id_banco);
    	ng.pagamento.agencia_transferencia = angular.copy(item.num_agencia);
    	ng.pagamento.conta_transferencia = angular.copy(item.num_conta);
    	ng.pagamento.proprietario_conta_transferencia = angular.copy(item.nme_fantasia);
    	ng.pagamento.id_plano_conta = angular.copy(item.id_plano_contas_padrao);
	}

	ng.loadFornecedor = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.fornecedores = [];
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento+"&frn->id[exp]= NOT IN ("+ ng.configuracao.id_fornecedor_movimentacao_caixa +")";
		if(!empty(ng.busca.fornecedores)){
			var buscaCpf  = ng.busca.fornecedores.replace(/\./g, '').replace(/\-/g, '');
			var buscaCnpj = ng.busca.fornecedores.replace(/\./g, '').replace(/\-/g, '').replace(/\//g,'');
			var busca = ng.busca.fornecedores.replace(/\s/g, '%');
			query_string += "&"+$.param({"(frn->nome_fornecedor":{exp:"like'%"+busca+"%' OR frn.nme_fantasia like '%"+busca+"%' OR frn.num_cnpj like '%"+buscaCnpj+"%' OR frn.num_cpf like '%"+buscaCpf+"%')"}})+"";
		}

		query_string += "&cplSql= ORDER BY frn.nome_fornecedor ASC";


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
				
				ng.pagamento.id_conta_bancaria = maquineta.id_conta_bancaria ;
				ng.pagamento.taxa_maquineta = ng.pagamento.id_forma_pagamento == 5 ? maquineta.per_margem_debito : maquineta.per_margem_credito ;
			}
		}
	}

	ng.selectChange = function(id){
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

		setTimeout(function(){
			$scope.$apply();
		}, 10);
		ng.loadBandeiras(id);
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

	ng.loadBandeiras = function(id) {
		ng.bandeiras = [];

		aj.get(baseUrlApi()+"bandeiras/"+id)
			.success(function(data, status, headers, config) {
				ng.bandeiras = data;
			})
			.error(function(data, status, headers, config) {

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
			$("#bandeira").addClass("has-error");

			var formControl = $("#bandeira")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'O escolha da bandeira é obrigatório')
				.attr("data-original-title", 'O escolha da bandeira é obrigatório');
			formControl.tooltip();
		}

		if((ng.pagamento.id_bandeira ==  undefined || ng.pagamento.id_bandeira ==  '') && (ng.pagamento.id_forma_pagamento == 5 || ng.pagamento.id_forma_pagamento == 6 ) && (ng.flgTipoLancamento == 0) ){
			error ++ ;
			$("#pagamento_bandeira").addClass("has-error");

			var formControl = $("#pagamento_bandeira")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'O escolha da bandeira é obrigatório')
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
								id_bandeira		   				 : ng.pagamento.id_bandeira,
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
								id_bandeira	   					 : ng.pagamento.id_bandeira,
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

			item.detalhamento = angular.copy(ng.pagamento.detalhamento);
			ng.recebidos.push(item);
		}
		ng.totalPagamento();
		ng.pagamento = {} ;
		$('.data-cc').val('');
		
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

				ClassDet = new Detalhamento();

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

					item.detalhamento = ClassDet.distribuir(v.detalhamento,parcelas,count+1);

					itens_prc.push(item);
				}

				pagamentos.push({id_forma_pagamento : v.id_forma_pagamento ,id_tipo_movimentacao: 3, parcelas:itens_prc});
			}else if(Number(v.id_forma_pagamento) == 2){
				ClassDet = new Detalhamento();
				var total_pagamento_cheque = getIndex('id_forma_pagamento',2,ng.recebidos);
				total_pagamento_cheque = ng.recebidos[total_pagamento_cheque].valor ;
				$.each(ng.pg_cheques,function(i_cheque, v_cheque){
					v.id_banco 				= v_cheque.id_banco ;
					v.num_conta_corrente 	= v_cheque.num_conta_corrente ;
					v.num_cheque 			= v_cheque.num_cheque ;
					v.flg_cheque_predatado 	= v_cheque.flg_cheque_predatado ;
					v.data_pagamento 		= v_cheque.data_pagamento ;
					v.valor_pagamento 		= v_cheque.valor_pagamento ;
					v_push = angular.copy(v);
					v_push.detalhamento = ClassDet.distribuir(v.detalhamento,ng.pg_cheques.length,i_cheque+1,total_pagamento_cheque,v_cheque.valor_pagamento);
					pagamentos.push(v_push);
				});
			}else if(Number(v.id_forma_pagamento) == 4){
				ClassDet = new Detalhamento();
				var total_pagamento_boleto = getIndex('id_forma_pagamento',4,ng.recebidos);
				total_pagamento_boleto = ng.recebidos[total_pagamento_boleto].valor ;	
				$.each(ng.pg_boletos,function(i_boleto, v_boleto){
					v.id_banco 				= v_boleto.id_banco ;
					v.data_pagamento 		= v_boleto.data_pagamento ;
					v.valor_pagamento 		= v_boleto.valor_pagamento ;
					v.doc_boleto            = v_boleto.doc_boleto ;
					v.num_boleto            = v_boleto.num_boleto ;
					v.status                = v_boleto.status_pagamento ;
					v_push = angular.copy(v);
					v_push.detalhamento = ClassDet.distribuir(v.detalhamento,ng.pg_boletos.length,i_boleto+1,total_pagamento_boleto,v_boleto.valor_pagamento);
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
							id_empreendimento:ng.userLogged.id_empreendimento,
							anexo_comprovante:ng.anexo_comprovante
						}
		}else if(ng.flgTipoLancamento == 1){
			var url = "fornecedor_pagamento";
			var dados = {
							pagamentos	 :pagamentos,
							id_fornecedor:ng.fornecedor.id,
							anexo_comprovante:ng.anexo_comprovante
						}
		}

		$('button').button('reset');


		aj.post(baseUrlApi()+url, { data: JSON.stringify( dados ) })
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
				ng.reset();
				ng.load(0,20);
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
				
			});
	}

	var ant_cheque 		  = false ;
	var ant_boleto 		  = false ;
	var ant_transferencia = false ;
	var ant_conta_bancaria = false ;
	var ant_forma_pagamento = false ;
	var ant_observacao = false ;

	ng.calculaColspan = function(init){
		
		var qtd_cheque 			= 4 ;
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

		if(ng.config_table.conta_bancaria)
			init = init+1;
		else if(ant_conta_bancaria)
			init = init-1;

		if(ng.config_table.forma_pagamento)
			init = init+1;
		else if(ant_forma_pagamento)
			init = init-1;

		if(ng.config_table.observacao)
			init = init+1;
		else if(ant_observacao)
			init = init-1;

		ant_cheque 			= ng.config_table.cheque;
		ant_boleto 			= ng.config_table.boleto;
		ant_transferencia 	= ng.config_table.transferencia;
		ant_conta_bancaria 	= ng.config_table.conta_bancaria;
		ant_forma_pagamento = ng.config_table.forma_pagamento;
		ant_observacao 		= ng.config_table.observacao;

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
		
		$("#modal-print").modal("show");
		if(item.id_forma_pagamento == 6){
			aj.get(baseUrlApi()+"lancamentos/parcelas/"+ng.vendaPrint.id_parcelamento)
				.success(function(data, status, headers, config) {
					parcelas = data ;
					aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ng.userLogged.id_empreendimento+"/"+ng.vendaPrint.id_cliente)
						.success(function(data, status, headers, config) {
							ng.vendaPrint.vlr_saldo_devedor = Number(data.vlr_saldo_devedor);

							if(parcelas.length > 1){
								dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Este pagamento faz parte de um parcelamento em '+parcelas.length+'x. Deseja imprimir todas as parcelas ? </strong>');

								dlg.result.then(function(btn){
									
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

	ng.msg_error = "Faça um filtro para obter resultados";


	ng.distriuDetalhamento = function(detalhamento,n_parcelas,n_pacela_atual,vlr_total_pg,vlr_parcela){
		n_parcelas = Number(n_parcelas),n_pacela_atual = Number(n_pacela_atual),vlr_total_pg = Number(vlr_total_pg),vlr_parcela = Number(vlr_parcela) ;
		var round = function(total){
			return Math.round( total * 100) /100
		}
		var item_detalhado = [] ;

		if(!vlr_total_pg){
			$.each(detalhamento,function(i,v){
				vlr_total_item = Number(v.valor) ;
				vlr_item = round( vlr_total_item/n_parcelas );
				item = {
					id_plano_conta  : v.id_plano_conta,
					valor 			: vlr_item,
					total 			: vlr_total_item
				}
				item_detalhado.push(item);
			});

			if(n_pacela_atual == n_parcelas){
				$.each(item_detalhado,function(x,y){
					if(y.valor*n_parcelas > y.total){
						var r = round((y.valor*n_parcelas) - y.total) ;	
						item_detalhado[x].valor = round(item_detalhado[x].valor-r) ;
					}else if(y.valor*n_parcelas < y.total){
						var r = round(y.total - (y.valor*n_parcelas))  ;	
						item_detalhado[x].valor += round(item_detalhado[x].valor+r) ;
					}

				});
			}

			$.each(item_detalhado,function(x,y){  delete item_detalhado[x].total });

			return item_detalhado ;
		}else{

			var representa = round((vlr_parcela * 100) / vlr_total_pg) ;
			representa = round(representa/100);

			$.each(detalhamento,function(i,v){
				vlr_total_item = Number(v.valor) ;
				vlr_item = round( vlr_total_item*representa );
				item = {
					id_plano_conta  : v.id_plano_conta,
					valor 			: vlr_item,
					total 			: vlr_total_item
				}
				item_detalhado.push(item);
			});

			return item_detalhado ;
		}	
	}



	ng.showModalDetalhamento = function(){
		$('#modal_add_detalhamento').modal('show');
	}

	ng.novo_detalhamento = {} ;
	ng.pagamento.detalhamento = [] ;
	ng.mensagem_detalhamento = '' ;
	ng.addDetalhePagamento = function(stay){
		ng.mensagem_detalhamento = '' ;
		var item = angular.copy(ng.novo_detalhamento);
		if(empty(item.id_plano_conta)){
			ng.mensagem_detalhamento = 'Selecione um  plano de conta';
		}
		if(empty(item.valor)){
			if(ng.mensagem_detalhamento != '') ng.mensagem_detalhamento +='<br/>' ;
			ng.mensagem_detalhamento += 'Informe o valor';
		}

		var valor_detalhado = Number(item.valor) ;

		if( !angular.isArray(ng.pagamento.detalhamento) ) ng.pagamento.detalhamento = [] ;

		$.each(ng.pagamento.detalhamento,function(i,v){ valor_detalhado += Number(v.valor) });
		valor_detalhado = Math.round( valor_detalhado * 100) /100 

		if( !$.isNumeric(ng.pagamento.valor) || (Number(ng.pagamento.valor) < Number(valor_detalhado) ) ){
			if(ng.mensagem_detalhamento != '') ng.mensagem_detalhamento +='<br/>' ;
			ng.mensagem_detalhamento += 'O valores detalhados são maiores que o pagamento';
		}

		if(ng.mensagem_detalhamento != '') return ;
		ng.pagamento.detalhamento.push(item);
		$.each(ng.plano_contas,function(i,v){
			if(Number(item.id_plano_conta) == Number(v.id)) item.nome_plano_conta = v.dsc_completa ;
		});
		ng.novo_detalhamento = {} ;
		if(stay != true) $('#modal_add_detalhamento').modal('hide');

	}

	ng.delDetalhamento = function($index){
		ng.pagamento.detalhamento.splice($index,1);
	}

	setTimeout(function(){
	  $('.btn-upload input[type="file"]').on('change', function(){
		var file = this.files[0]; // get selected file
		var reader = new FileReader();

		ng.fileModel = $(this).data().model; // get attribute model name


		if(empty(ng.anexo_comprovante)){
			ng.anexo_comprovante = {};
		}

		if(empty(ng.anexo_comprovante[ng.fileModel])) // validate if is empty
			ng.anexo_comprovante[ng.fileModel] = {}; // create as object

		// detect file type
		var type = file.type.substring(file.type.indexOf('/')+1, file.type.length);
		if(empty(type)){
			type = file.name.substring((file.name.lastIndexOf('.')+1), file.name.length);
		}
		
		ng.anexo_comprovante[ng.fileModel].name = file.name; // file name
		ng.anexo_comprovante[ng.fileModel].type = type; // file type
		ng.anexo_comprovante[ng.fileModel].size = (file.size / 1024); // file size

		// adjust file size string name
		if(ng.anexo_comprovante[ng.fileModel].size < 1024)
			ng.anexo_comprovante[ng.fileModel].size = numberFormat(ng.anexo_comprovante[ng.fileModel].size, 2, ',', '.') + ' KB';
		else if(ng.anexo_comprovante[ng.fileModel].size > 1024)
			ng.anexo_comprovante[ng.fileModel].size = numberFormat(ng.anexo_comprovante[ng.fileModel].size, 2, ',', '.') + ' MB';

		// after loading file...
		reader.onload = function (e) {
			ng.anexo_comprovante[ng.fileModel].path = e.target.result; // get base64 string of file
			ng.anexo_comprovante[ng.fileModel].updated = true;
		  	setTimeout(function(){
				ng.$apply(); // apply changes in the screen
			},1);
		}

		if(!empty(file))
			reader.readAsDataURL(file);
		});
	}, 10);


	ng.loadPlanoContas();
	ng.loadContas();
	ng.loadBancos();
	ng.loadMaquinetas();
	ng.loadConfig();
	ng.loadPerfil();

});

var Detalhamento = function(){
	var arr_parc = [] ;
	this.getParcelas = function(){
		return arr_parc; 
	}
	this.distribuir = function(detalhamento,n_parcelas,n_pacela_atual,vlr_total_pg,vlr_parcela){
		detalhamento = angular.isArray(detalhamento) ? detalhamento : [] ;
		n_parcelas = Number(n_parcelas),n_pacela_atual = Number(n_pacela_atual),vlr_total_pg = Number(vlr_total_pg),vlr_parcela = Number(vlr_parcela) ;
		var round = function(total){
			return Math.round( total * 100) /100
		}
		var item_detalhado = [] ;

		if(!vlr_total_pg){
			$.each(detalhamento,function(i,v){
				vlr_total_item = Number(v.valor) ;
				vlr_item = round( vlr_total_item/n_parcelas );
				item = {
					id_plano_conta  : v.id_plano_conta,
					valor 			: vlr_item,
					total 			: vlr_total_item
				}
				item_detalhado.push(item);
			});

			if(n_pacela_atual == n_parcelas){
				$.each(item_detalhado,function(x,y){
					if(y.valor*n_parcelas > y.total){
						var r = round((y.valor*n_parcelas) - y.total) ;	
						item_detalhado[x].valor = round(item_detalhado[x].valor-r) ;
					}else if(y.valor*n_parcelas < y.total){
						var r = round(y.total - (y.valor*n_parcelas))  ;	
						item_detalhado[x].valor = round(item_detalhado[x].valor+r) ;
					}

				});
			}

			$.each(item_detalhado,function(x,y){
			  delete item_detalhado[x].total ;
			  item_detalhado[x].valor = round(item_detalhado[x].valor);
			});

			return item_detalhado ;
		}else{
			var representa = round((vlr_parcela * 100) / vlr_total_pg) ;
			representa = round(representa/100);

			$.each(detalhamento,function(i,v){
				vlr_total_item = Number(v.valor) ;
				vlr_item = round( vlr_total_item*representa );
				if(arr_parc[i] == undefined) arr_parc[i] = [] ;
				arr_parc[i].push(round(vlr_item));
				item = {
					id_plano_conta  : v.id_plano_conta,
					valor 			: round(vlr_item),
					total 			: vlr_total_item
				}
				item_detalhado.push(item);
			});

			if(n_pacela_atual == n_parcelas){
				$.each(item_detalhado,function(x,y){
					var valor_total_parcelas = round(arr_parc[x].reduce((a, b) => a + b)) ;
					if(valor_total_parcelas > y.total){
						var r = round((valor_total_parcelas) - y.total) ;	
						item_detalhado[x].valor = round(item_detalhado[x].valor-r) ;
						arr_parc[x][arr_parc[x].length-1] = round(item_detalhado[x].valor) ;
					}else if(valor_total_parcelas < y.total){
						var r = round(y.total - (valor_total_parcelas))  ;	
						item_detalhado[x].valor = round(item_detalhado[x].valor+r) ;
						arr_parc[x][arr_parc[x].length-1] = round(item_detalhado[x].valor) ;
					}

				});
			}

			$.each(item_detalhado,function(x,y){
			  delete item_detalhado[x].total ;
			  item_detalhado[x].valor = round(item_detalhado[x].valor);
			});

			return item_detalhado ;
		}	
	}
}