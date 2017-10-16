app.controller('OrdemProducaoController', function($scope, $http, $window, $dialogs, UserService, ConfigService, AsyncAjaxSrvc){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		 = baseUrl();
	ng.userLogged 	 = UserService.getUserLogado();
	ng.configuracao  = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.ordemProducao = {itens:[]};
	ng.busca         = {produtos:"",depositos:"", show_concluidos: 0, id_categoria_op: null};
	ng.paginacao     = {produtos:[]} ;
	ng.status_op 	 = AsyncAjaxSrvc.getListOfItens(baseUrlApi()+'status/ordem-producao');
	ng.categorias 	 = AsyncAjaxSrvc.getListOfItens(baseUrlApi()+'categorias?tce->id_empreendimento='+ng.userLogged.id_empreendimento)['categorias'];

    ng.editing = false;

    $('#sizeToggle').trigger("click");

    ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

    ng.showProdutos = function(busca_cdb){
   	 	ng.busca.produtos = "" ;
   		ng.loadProdutos(0,10);
   		$('#list_produtos').modal('show');
   	}

   	ng.loadProdutos = function(offset, limit) {
		ng.produtos = [];

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento+"&pro->flg_produto_composto=1";

		if(ng.busca.produtos != ""){
			if(isNaN(Number(ng.busca.produtos)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produtos+"%' OR codigo_barra like '%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produtos+"%' OR codigo_barra like '%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%' OR pro.id = "+ng.busca.produtos+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos = data.produtos;
				ng.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.produtos = null;
					ng.paginacao.produtos = null;
				}
			});
	}

	ng.addProduto = function(item){
		item = angular.copy(item) ;
		if(empty(item.qtd)){
			item.qtd =  1 ;
		}
		ng.ordemProducao.itens.push(item);
	}

	ng.delProduto = function(index){
		ng.ordemProducao.itens.splice(index,1);
	}

	ng.verificaProduto = function(item){
		var exists = false ;
		$.each(ng.ordemProducao.itens,function(i,produto){
			if(Number(produto.id) == Number(item.id)){
				exists = true ;
				return ;
			}
		});
		return exists ;
	}

	ng.salvar = function(){
		var btn = $("#btn-salvar");
		btn.button('loading');
		var error = 0
		$(".has-error").find("input").tooltip('destroy');
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");
		$.each(ng.ordemProducao.itens,function(i,produto){
			if(empty(produto.qtd)){
				error ++ ;
				$("#produto-qtd-"+i).parent().addClass("has-error");

				var formControl = $("#produto-qtd-"+i)
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe a Quatidade a ser Produzida')
					.attr("data-original-title", 'Informe a Quatidade a ser Produzida');
				formControl.tooltip();
			}
			
		});

		if(error > 0){
			btn.button('reset');
			return
		}



		var post = angular.copy(ng.ordemProducao);

		post.id_responsavel = ng.userLogged.id ;
		post.id_status     = 1 ;
		post.id_empreendimento = ng.userLogged.id_empreendimento ;

		aj.post(baseUrlApi()+"ordem_producao",post)
			.success(function(data, status, headers, config) {
				ng.showBoxNovo(); ng.reset();
				$('html,body').animate({scrollTop: 0},'slow');
				ng.mensagens('alert-success','Ordem de Produção Cadastrada com Sucesso','.alert-list-pedidos');
				ng.loadOrdemProducao();
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				if(status == 406){
					$.each(data, function(i, item) {
						$("#"+i).addClass("has-error");
						var formControl = $("#"+i)
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});
				}else{
					alert('error ao cadastrar');
				}
				btn.button('reset');
		});
	}

	ng.busca = { nme_cliente: "", id_status_op: null, id_categoria_op: null, show_concluidos: 0};
	ng.resetFilter = function() {
		$("#data").val("");
		ng.busca.nme_cliente = "" ;
		ng.busca.id_status_op = null;
		ng.busca.id_categoria_op = null;
		ng.busca.show_concluidos = 0;
		ng.reset();
		ng.loadOrdemProducao(0,10);
	}

	ng.loadOrdemProducao = function(offset, limit) {
		ng.ordem_producao = [];

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?top->id_empreendimento="+ng.userLogged.id_empreendimento+"&top->flg_excluido=0";

		if(ng.busca.nme_cliente != "")
			query_string += "&("+$.param({'tu->nome':{exp:"like '%"+ng.busca.nme_cliente+"%' "}})+")";
		
		if(ng.busca.id_status_op != null)
			query_string += "&top->id_status="+ ng.busca.id_status_op;
		else
			query_string += "&"+$.param({'top->id_status':{exp:"<> 3"}});
		
		if(ng.busca.id_categoria_op != null)
			query_string += "&prd->id_categoria="+ ng.busca.id_categoria_op;
		
		//if(parseInt(ng.busca.show_concluidos, 10) === 0)
			//query_string += "&"+$.param({'top->id_status':{exp:"<> 3"}});

		if($("#data").val() != ""){
			var data = moment($("#data").val(), "DD/MM/YYYY").format("YYYY-MM-DD");
			query_string += "&("+$.param({'1':{exp:"= 1 AND cast(dta_create as date) = '"+ data +"' "}})+")";
		}

		aj.get(baseUrlApi()+"ordem_producao/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.ordem_producao = data.ordem_producao;
				ng.paginacao.ordem_producao = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.ordem_producao = [];
					ng.paginacao.ordem_producao = [];
				}
			});
	}

	ng.viewOrdemProducao = {} ;

	ng.showView = function(item,out_estoque){
		if(out_estoque != true)
			ng.pro_out_estoque = [] ;
		ng.viewOrdemProducao = item ;
		ng.loadItensOrdemProducao(0,10,item.id);
		$('#list_detalhes').modal('show');
	}

	ng.loadItensOrdemProducao = function(offset, limit,id) {
		ng.viewOrdemProducao.itens = [];
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10  : limit;
		var query_string = "?tiop->id_ordem_producao="+id;
		aj.get(baseUrlApi()+"itens_ordem_producao/"+offset+"/"+limit+query_string)
			.success(function(data, status, headers, config) {
				ng.viewOrdemProducao.itens = data.itens_ordem_producao;
				ng.paginacao.itens_ordem_producao = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.viewOrdemProducao.itens = [];
					ng.paginacao.itens_ordem_producao = [];
				}
			});
	}
 	ng.pro_out_estoque = [] ;
	ng.outEstoque = function(item){
		var exists = false ;
		$.each(ng.pro_out_estoque,function(i,x){
			if(Number(x[0]) == Number(item.id)){
				exists = true;
				return;
			}
		});

		return exists ;
	}

	ng.depositos = [] ;

	ng.showDepositos = function(){
		ng.busca.depositos = "" ;
		ng.loadDepositos(0,10);
		$('#list_depositos').modal('show');
	}

	ng.loadDepositos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.depositos != ""){
			query_string += "&"+$.param({'nme_deposito':{exp:"like'%"+ng.busca.depositos+"%'"}});
		}



		ng.depositos = [];
		aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.depositos        = data.depositos ;
				ng.paginacao.depositos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.depositos = [];
			});
	}

	ng.addDeposito = function(item){
		ng.ordemProducao.id_deposito     = item.id;
		ng.ordemProducao.nme_deposito    = item.nme_deposito;
		$('#list_depositos').modal('hide');
	}

	ng.changeStatus = function(item,id_status,event){
		var but = $(event.target);
		if(!but.is(':button'))
			but = $(event.target).parent();
		
		
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Alterar o Status Desta Ordem de Produção?</strong>');

		dlg.result.then(function(btn){
			but.button('loading');
			aj.get(baseUrlApi()+"ordem_producao/chage_status/"+item.id+"/"+id_status+"/"+ng.userLogged.id_empreendimento+"/"+ng.userLogged.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','Status Alterado com Sucesso','.alert-list-pedidos');
					ng.loadOrdemProducao();
					if(id_status == 3){
						var msg = {
							type : 'op_finished',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento, message : JSON.stringify({id_ordem_producao:item.id})
						}
						ng.sendMessageWebSocket(msg);
					}
				})
				.error(function(data, status, headers, config) {
				 but.button('reset');
				 if(status == 406){
				 	$('#list_out_estoque').modal('show')
				 	ng.pro_out_estoque  = data.out_estoque ;
				 	ng.list_out_estoque = data.lista;
				 	//ng.showView(item,true);
				 }else{
				 	alert('Erro ao Mudar Status')
				}
			});
		}, function(){ but.button('reset') } );
	}

	ng.reset = function(){
		ng.ordemProducao = {itens:[]};
	}

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

	ng.newConnWebSocket = function(){
		ng.conn = new WebSocket(ng.configuracao.patch_socket_sat);
		ng.conn.onopen = function(e) {
			console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - WebSocket conectado.');
			$scope.$apply(function () { ng.status_websocket = 1 ;});
		};

		ng.conn.onclose = function(e) {

		}

		ng.conn.onmessage = function(e) {
			console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - Mensagem Recebida : '+e.data);
			var data = JSON.parse(e.data);
			data.message = parseJSON(data.message);
			switch(data.type){
				case 'session_id':
					var msg = {
						type : 'set_id_empreendimento',from : data.to,
						message : JSON.stringify({id_empreendimento:ng.userLogged.id_empreendimento,id_usuario:ng.userLogged.id,id_perfil:ng.userLogged.id_perfil})
					}
					ng.sendMessageWebSocket(msg);
					ng.id_ws_web = data.to ;
					break;
				case 'table_change':
					ng.loadOrdemProducao();
				case 'op_new':
					noty({
						timeout: 2500,
						layout: 'topRight',
						type: 'warning',
						theme: 'relax',
						text: '<i class="fa fa-wrench"></i><strong> NOVO PEDIDO (#'+ data.message.id_ordem_producao +')</strong><br/>SOLICITANTE: '+ data.message.nome_usuario.toUpperCase() + '<br/>CLIENTE: '+ data.message.nome_cliente.toUpperCase() + '<br/>MESA: '+ data.message.dsc_mesa.toUpperCase(),
						animation : {
							open  : 'animated bounceInRight',
							close : 'animated bounceOutRight'
						}
					});
					ng.loadOrdemProducao();
				break;
			}			
		};
	}

	ng.sendMessageWebSocket = function(data){
		console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - mensagem Enviada: '+JSON.stringify(data));
		ng.conn.send(JSON.stringify(data));
	}

	function enviaTesteConexao(){	
		timeOutSendTestConection = setTimeout(function(){
			console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - Buscando id_ws_dsk ...');
			aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento+"/id_ws_dsk_op")
				.success(function(data, status, headers, config) {
					if(!empty(data.id_ws_dsk_op)){
						ng.id_ws_dsk = data.id_ws_dsk_op ;
						var mg = {
							from:ng.id_ws_web,
							to:ng.id_ws_dsk,
							type:'connection_test_request',
							message:"Teste de conexão com client desktop"
						};
						ng.sendMessageWebSocket(mg);
						 timeOutWaitingResponseTestConection = setTimeout(function() {
						 	$scope.$apply(function () { ng.status_websocket = 1 ;});
						 	console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - Não foi possível obter resposta do APP Client para o teste de conexão');
						 }, TimeWaitingResponseTestConection);
					}else
						console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - Não foi possível obter o id_ws_dsk');
					enviaTesteConexao();
				})
				.error(function(data, status, headers, config) {
					console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - Não foi possível obter o id_ws_dsk');
					enviaTesteConexao();
				});
		},60000);
	}

	if(!empty(ng.configuracao.patch_socket_sat))
		ng.newConnWebSocket();

	ng.loadOrdemProducao();
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