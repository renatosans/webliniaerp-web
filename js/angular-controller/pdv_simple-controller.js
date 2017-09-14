app.controller('PdvSimpleController', function($scope, $http, $window, $dialogs, UserService, ConfigService, AsyncAjaxSrvc){
	$scope.userLogged 			= UserService.getUserLogado();
	$scope.configuracoes 		= ConfigService.getConfig($scope.userLogged.id_empreendimento);
	$scope.busca 				= { clientes: "", 	servicos: "", 	produtos: "",  nome: "", cod_status_servico: null};
	$scope.paginacao			= { clientes: null, servicos: null, produtos: null, ordens_servico: null };
	$scope.margemAplicada       = {atacado:false,intermediario:false,varejo:true,parceiro:false} ;
	$scope.tipo_view 			= 'grade-categorias' ;
	$scope.carrinho 			= [] ;
	$scope.calcTotalCompra 		= 0 ;
	$scope.total_itens 			= 0 ;
	$scope.out_produtos 		= [];

	var isFullscreen = false;
	$scope.resizeScreen = function() {
		if(!isFullscreen){
			$("#map_canvas").css("height", 700);
			$("footer").addClass("hide");
			$("#wrapper").css("min-height", "0px");
			$("#main-container").css("min-height", "0px");
			$("#main-container").css("margin-left", 0).css("padding-top", 45);
			//$("#top-nav").toggle();
			$("aside").toggle();
			$("#breadcrumb").toggle();
			isFullscreen = !isFullscreen;
		}
		else {
			$("#map_canvas").css("height", 600);
			$("footer").removeClass("hide");
			$("#wrapper").css("min-height", "800px");
			$("#main-container").css("min-height", "800px");
			$("#main-container").css("margin-left", 194).css("padding-top", 45);
			//$("#top-nav").toggle();
			$("aside").toggle();
			$("#breadcrumb").toggle();
			isFullscreen = !isFullscreen;
		}
	}

	$scope.changeTipoView=function(tipo){
		$scope.tipo_view = tipo ;
	}

	$scope.addProduto = function(item){
		item.vlr_venda_atacado = round(item.vlr_venda_atacado,2) ;
		item.vlr_venda_intermediario = round(item.vlr_venda_intermediario,2) ;
		item.vlr_venda_varejo = round(item.vlr_venda_varejo,2) ;
		$scope.incluirCarrinho(angular.copy(item));
		item.qtd_total = "";
		$scope.calcTotalCompra();
	}

	$scope.removeProduto = function(produto){
		index = getIndex('id',produto.id,$scope.carrinho);
		$scope.carrinho.splice(index,1);
		$scope.calcTotalCompra();

		index = $scope.out_produtos.indexOf(produto.id);
		if(index >= 0){
			$scope.out_produtos.splice(index,1); 
		}			
	}

	$scope.incluirCarrinho = function(produto){
		produto = angular.copy(produto);
		if($scope.margemAplicada.atacado){
			produto.vlr_unitario    	 = produto.vlr_venda_atacado;
			produto.vlr_real        	 = produto.vlr_venda_atacado;
			produto.perc_margem_aplicada = produto.margem_atacado;
			$scope.margem_aplicada_venda 	 = 'atacado';

		}else if($scope.margemAplicada.varejo){

			produto.vlr_unitario		 = produto.vlr_venda_varejo;
			produto.vlr_real       		 = produto.vlr_venda_varejo;
			produto.perc_margem_aplicada = produto.margem_varejo;
			$scope.margem_aplicada_venda 	 = 'varejo';

		}else if($scope.margemAplicada.intermediario){

			produto.vlr_unitario		 = produto.vlr_venda_intermediario;
			produto.vlr_real       		 = produto.vlr_venda_intermediario;
			produto.perc_margem_aplicada = produto.margem_intermediario;
			$scope.margem_aplicada_venda 	 = 'intermediario';

		}else if($scope.margemAplicada.parceiro){

			produto.vlr_unitario    	 = produto.vlr_custo_real;
			produto.vlr_real       		 = produto.vlr_custo_real;
			produto.perc_margem_aplicada = 0 ;
			$scope.margem_aplicada_venda 	 = 'parceiro';

		}else{
			produto.vlr_unitario    	 = produto.vlr_venda_varejo;
			produto.vlr_real        	 = produto.vlr_venda_varejo;
			produto.perc_margem_aplicada = produto.margem_varejo;
			$scope.margemAplicada.varejo = true ;
			produto.margem_aplicada 	 = 'varejo';
		}
		produto.valor_desconto = empty(produto.valor_desconto) ?  0 : produto.valor_desconto ; 

		produto.qtd_total = !$.isNumeric(produto.qtd_total) || Number(produto.qtd_total) < 1 ? 1 : Number(produto.qtd_total) ;
		produto.sub_total = produto.qtd_total * produto.vlr_unitario;

		$scope.vezes_valor			    = produto.qtd_total+' x R$ '+numberFormat(produto.vlr_unitario,2,',','.');
		$scope.nome_ultimo_produto      = produto.nome_produto ;

		if(produto.img != null)
			$scope.imgProduto = 'assets/imagens/produtos/'+produto.img ;
		else
			$scope.imgProduto = 'img/imagem_padrao_produto.gif';

		$scope.carrinho.push(produto) ;
	}

	$scope.calcTotalCompra = function() {
		var total = 0 ;
		var total_itens = 0 ;
		var qtd_total = 0 ;
		$.each($scope.carrinho, function(i, item) {
			total += Number(item.sub_total);
			if(empty(item.qtd_total))
				qtd_total = 1 ;
			else
				qtd_total = item.qtd_total ;
			total_itens += Number(qtd_total);
		});
		$scope.vlrTotalCompra = Math.round( total * 100) /100  ;
		$scope.total_itens = total_itens ;
	}
	
	$scope.isSelect = function(produto){
		var s = false ;
		$.each($scope.carrinho,function(i,v){
			if(Number(produto.id) == Number(v.id)){
				s = true ;
				return 
			}
		});
		return s ;
	}

	$scope.modalProgressoVenda = function(acao){
		if(acao == 'show')
			$('#modal_progresso_venda').modal({ backdrop: 'static',keyboard: false});
		else if (acao == 'hide')
			$('#modal_progresso_venda').modal('hide');
	};

	$scope.orcamento = false ;
	$scope.pagamento_fulso = false ;
	$scope.efetivarCompra = function(){
		$scope.modalProgressoVenda('show');
		if($scope.orcamento){
			var btn = $('#btn-fazer-orcamento');
		}else{
			var btn = $('#btn-fazer-compra');
		}


		btn.button('loading');

		var pagamentos   = [] ;
	    var data_atual = NOW('en');

		

		var pagamentos = [
			{
				data_pagamento 			:  data_atual,
				id_abertura_caixa 		:  $scope.caixa.id ,
				id_plano_conta    		:  $scope.configuracoes.id_plano_caixa,
				id_tipo_movimentacao	:  3,
				id_cliente				:  $scope.configuracoes.id_cliente_movimentacao_caixa,
				id_forma_pagamento		:  3,
				valor_pagamento			:  $scope.vlrTotalCompra,
				valor					:  $scope.vlrTotalCompra,
				status_pagamento		:  1,
				id_empreendimento		:  $scope.userLogged.id_empreendimento,
				id_conta_bancaria       :  $scope.configuracoes.id_caixa,
				id_cliente_lancamento	:  $scope.caixa.id_cliente_movimentacao_caixa,
				id_conta_bancaria       :  $scope.caixa.id_caixa
			}
		];


		var produtos = angular.copy($scope.carrinho);
		var venda    = {
							id_usuario			: $scope.userLogged.id,
							id_cliente 			: $scope.configuracoes.id_cliente_movimentacao_caixa,
							venda_confirmada 	: 1,
							id_empreendimento	: $scope.userLogged.id_empreendimento,
							id_deposito 		: $scope.caixa.depositos,
							id_status_venda 	: 4,
							margem_aplicada     : $scope.margem_aplicada_venda,
							dta_venda           : moment().format("YYYY-MM-DD HH:mm:ss")
						};

		
		$.each(produtos,function(index,value){
			produtos[index].venda_confirmada 	= 1 ;
			produtos[index].valor_produto 		= value.vlr_unitario;
			produtos[index].qtd           		= value.qtd_total;

			if(value.flg_desconto != null && Number(value.valor_desconto) > 0 && !isNaN(Number(value.valor_desconto))){
				produtos[index].desconto_aplicado	= parseInt(value.flg_desconto) != 1 && isNaN(parseInt(value.flg_desconto)) ? 0 : 1 ;
				produtos[index].valor_desconto      = parseInt(value.flg_desconto) == 1 ? value.valor_desconto/100 : 0 ;
			} else {
				produtos[index].desconto_aplicado	= 0 ;
				produtos[index].valor_desconto      = 0 ;
			}
		});

		/*
		* agrupando os produtos de 10 em 10
		*/

		var index_current 	  = 0  ;
		var n_repeat 	  	  = 10 ;
		var repeat_count      = 0  ;
		var produtos_enviar   = [] ;




		$.each(produtos,function(index,obj){
			if(repeat_count >= n_repeat){
					index_current ++ ;
					repeat_count = 0 ;
			}

			if(!(produtos_enviar[index_current] instanceof Array)){
				produtos_enviar[index_current] = [];
			}

			produtos_enviar[index_current].push(obj);
			repeat_count ++ ;
		});

		$scope.venda 				  = venda ;
		$scope.produtos_enviar 		  = produtos_enviar ;
		$scope.pagamentos_enviar      = pagamentos;


		controla_error_estoque = 0 ;
		//$scope.clearOutProdutos();
		$scope.out_produtos = [] ;
		//$scope.out_descontos = [] ;
		$scope.verificaEstoque(produtos_enviar,0);
	}

	$scope.verificaEstoque = function(produtos_enviar,init,acao){
		$scope.out_descontos = [] ;
		var cont_itens  = produtos_enviar.length ;

		init = init == null ? 0 : init ;
		var item_enviar = produtos_enviar[init];


		$http.post(baseUrlApi()+"venda/verificaEstoque",{
														id_empreendimento:$scope.userLogged.id_empreendimento,
											        	id_deposito:$scope.caixa.depositos,
											        	produtos:item_enviar,
											        	id_vendedor      : Number($scope.userLogged.id),
											        	id_venda_ignore  : (empty($scope.id_venda_ignore) ? null : $scope.id_venda_ignore )
	        										 }
	        )
			.success(function(data, status, headers, config) {
				if (init+1 >= cont_itens){
					if($scope.out_produtos.length > 0){
						$scope.modalProgressoVenda('hide');
						if($scope.out_produtos.length > 0)
						$('html,body').animate({scrollTop: 0},'slow');
						var btn = $('#btn-fazer-compra');

						btn.button('reset');
						return ;
					}else{
						$('#text_status_venda').text('Salvando Venda');
					 	$scope.gravarVenda();
					}
					
				}else
	           		$scope.verificaEstoque(produtos_enviar,init+1,acao);
			})
			.error(function(data, status, headers, config) {
				if(status == 406){
					$.each(data.out_estoque,function(i, value){
							$scope.out_produtos.push(value[0]);
					});
					if (init+1 >= cont_itens){
		         		if($scope.out_produtos.length > 0){
		         			setTimeout(function(){
		         				$('html,body').animate({scrollTop: 0},'slow');
		         			},300);
			         		
		         		}
						$scope.modalProgressoVenda('hide');
						$('button').button('reset');
					}else{
		           		$scope.verificaEstoque(produtos_enviar,init+1,acao);
					}
				}
			});
	};

	$scope.gravarVenda = function(venda){
		if(!empty($("#dta_venda").val())) {
			$scope.venda.dta_venda = formatDate($("#dta_venda").val());
		}

		
		$http.post(baseUrlApi()+"venda/gravarVenda",{venda:$scope.venda})
			.success(function(data, status, headers, config) {
				$('#text_status_venda').text('Salvando Itens');
				if($.isNumeric(data.id_cliente)){
					$scope.cliente.id = $.isNumeric(data.id_cliente) ? Number(data.id_cliente) : $scope.cliente.id ; 
					$.each($scope.pagamentos_enviar,function(i,x){
						$scope.pagamentos_enviar[i].id_cliente = Number(data.id_cliente);
					});
				}
				$scope.id_venda = data.id_venda;
				$scope.salvarItensVenda(data.id_venda,$scope.produtos_enviar,0);
			})
			.error(function(data, status, headers, config) {
				alert('Erro fatal');
			});
	};

	$scope.salvarItensVenda = function(id_venda,produtos_enviar,init){
		var cont_itens  = produtos_enviar.length ;

		if (init >= cont_itens){
			$('#text_status_venda').text('Salvando Movimentações');
			$scope.gravarMovimentacoes();
			return ;
		}

		init = init == null ? 0 : init ;
		var item_enviar = produtos_enviar[init];

		$http.post(baseUrlApi()+"venda/gravarItensVenda",{	id_venda:id_venda ,
														id_vendedor :$scope.userLogged.id,
														produtos:item_enviar,
														venda_confirmada 	: $scope.orcamento ? 0 : 1,
														id_empreendimento:$scope.userLogged.id_empreendimento,
											        	id_deposito:$scope.caixa.depositos,
											        	id_caixa : $scope.caixa.id_caixa
											          }
			)
			.success(function(data, status, headers, config) {
				$scope.salvarItensVenda(id_venda,produtos_enviar,init+1);
			})
			.error(function(data, status, headers, config) {
				alert('Erro fatal');
			});
	};

	$scope.gravarMovimentacoes = function(){
			console.log($scope.pagamentos_enviar);
			var id_venda = $scope.finalizarOrcamento == true ? $scope.id_orcamento : $scope.id_venda;
			var id_mesa = null; 
			if(!empty($scope.dadosOrcamento)){
				if($scope.dadosOrcamento.flg_comanda == 1)
					id_mesa = $scope.dadosOrcamento.id_mesa ;
			}
			$http.post(baseUrlApi()+"venda/gravarMovimentacoes",{ 
															   id_venda:id_venda,
															   pagamentos:$scope.pagamentos_enviar,
															   id_cliente:$scope.configuracoes.id_cliente_movimentacao_caixa,
															   id_empreendimento:$scope.userLogged.id_empreendimento
															 }
			).success(function(data, status, headers) {
				var btn = $('#btn-fazer-compra');
				btn.button('reset');
				$scope.modalProgressoVenda('hide');
				$scope.out_produtos = [];
				$scope.carrinho = [] ;
				$scope.total_itens = 0 ;
				$scope.vlrTotalCompra = 0 ;
				$scope.voltarListaCategorias();
			})
			.error(function(data, status, headers, config) {
				alert('Erro fatal');
			});
	}

	$scope.isOutEstoque = function(id){
		return _in(id,$scope.out_produtos);
	}

	$scope.showBoxNovo = function(clearData){
    	$scope.editing = !$scope.editing;
		$('#box-novo').toggle(0,function(){$("select").trigger("chosen:updated");});
		if(clearData) {
			clearValidationFormStyle();
			clearObject();
		}
	}
 
	$scope.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?tpe->id_empreendimento="+$scope.userLogged.id_empreendimento+"&tp->flg_excluido=0";

    	if($scope.busca.produtos != ""){
    		query_string += "&"+$.param({'(tp->nome':{exp:"like'%"+$scope.busca.produtos+"%' OR tf.nome_fabricante like'%"+$scope.busca.produtos+"%')"}});
    	}

    	if(!empty($scope.categoria))
    		query_string += '&tp->id_categoria='+$scope.categoria.id; 

		$scope.produtos = [];
		$http.get(baseUrlApi()+"estoque_produtos/null/"+query_string+"&cplSql= ORDER BY tp.nome ASC, tt.nome_tamanho ASC, tcp.nome_cor ASC")
			.success(function(data, status, headers, config) {
				$scope.produtos = data ;

				angular.forEach($scope.produtos, function(produto){
					if(!empty(produto.img))
						produto.imgProduto = 'assets/imagens/produtos/' + produto.img;
					else
						produto.imgProduto = 'img/imagem_padrao_produto.gif';
				});
			})
			.error(function(data, status, headers, config) {
				$scope.produtos = [] ;
			});
	}

	$scope.selectProduto = function(item) {
		if(empty(item.qtd_pedido))
			item.qtd_pedido = 1;
		$scope.objectModel.produtos.push(item);
		$scope.recalculaTotais();
		$("#list_produtos").modal("hide");
	}

	$scope.removeItem = function(item, objectOwner) {
		$scope.objectModel[objectOwner] = _.without($scope.objectModel[objectOwner], item);
		$scope.recalculaTotais();
		updateView(100);
	}

	$scope.recalculaTotais = function() {
		$scope.objectModel.vlr_total_servicos = _.reduce($scope.objectModel.servicos, function(value, item){
			return value + (parseInt(item.qtd_pedido, 10) * item.vlr_procedimento);
		}, 0);

		$scope.objectModel.qtd_total_servicos = _.reduce($scope.objectModel.servicos, function(value, item){
			return value + parseInt(item.qtd_pedido, 10);
		}, 0);

		$scope.objectModel.vlr_total_produtos = _.reduce($scope.objectModel.produtos, function(value, item){
			return value + (parseInt(item.qtd_pedido, 10) * item.vlr_venda_varejo);
		}, 0);

		$scope.objectModel.qtd_total_produtos = _.reduce($scope.objectModel.produtos, function(value, item){
			return value + parseInt(item.qtd_pedido, 10);
		}, 0);

		$scope.objectModel.vlr_total_os = ($scope.objectModel.vlr_total_servicos + $scope.objectModel.vlr_total_produtos);
	}

	$scope.abrirCaixa = function() {
   		$http.get(baseUrlApi()+"pedido_venda/abrir_caixa/"+ $scope.configuracoes.id_caixa_padrao +"/"+ $scope.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				$scope.caixa = data;
				if(empty($scope.caixa.depositos) && !empty($scope.configuracoes.id_deposito_padrao)) {
					$scope.caixa.depositos = [$scope.configuracoes.id_deposito_padrao];

					if($.isNumeric($scope.configuracoes.id_conta_bancaria_padrao_fechamento_automatico) && $.isNumeric($scope.configuracoes.id_caixa_padrao))
						$scope.statusConfig = true;
					else
						$scope.statusConfig = false;
				}
				else
					$scope.statusConfig = false;
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	$scope.save = function() {
		clearValidationFormStyle();

		$('#btnCancelarOS').button('loading');
		$('#btnSalvarOS').button('loading');

		var postData = angular.copy($scope.objectModel);
			postData.id_abertura_caixa 	= $scope.caixa.id;
			postData.id_plano_conta 	= $scope.configuracoes.id_plano_caixa;
			postData.dta_ordem_servico 	= moment(postData.dta_ordem_servico, 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss');

		delete postData.criador.modulosAssociatePage;
		delete postData.criador.empreendimento_usuario;

		$http.post(baseUrlApi()+"ordem-servico", postData)
			.success(function(data, status, headers, config) {
				$('#btnCancelarOS').button('reset');
				$('#btnSalvarOS').button('reset');
				
				$scope.showBoxNovo(true);
				$scope.loadOrdensServicos(0,10);
			})
			.error(function(errors, status, headers, config) {
				$('#btnCancelarOS').button('reset');
				$('#btnSalvarOS').button('reset');

				if(status === 406) {
					$('.alert-form.alert-warning').text("Atenção! Alguns campos obrigatórios não foram preenchidos.").removeClass('hide');
					applyFormErrors(errors, 'objectModel');
				} else {
					$('.alert-form.alert-danger').text(errors).removeClass('hide');
				}
			});
	}

	function loadSaldoDevedorCliente() {
		$http.get(baseUrlApi()+"usuarios/saldodevedor/"+ $scope.userLogged.id_empreendimento +"?usu->id="+ $scope.objectModel.cliente.id)
			.success(function(data, status, headers, config) {
				$scope.objectModel.cliente.vlr_saldo_devedor = data.vlr_saldo_devedor;
			})
			.error(function(data, status, headers, config) {
				console.log('erro ao consultar saldo do cliente');
			});
	}

	$scope.reset = function(){
		$scope.OrdensServicos = {itens:[]};
	}

	$scope.resetFilter = function() {
		$("#dtaInicial").val("");
		$scope.busca.nome = "" ;
		$scope.busca.cod_status_servico = null ;
		$scope.reset();
		$scope.loadOrdensServicos(0,10);
	}

	$scope.loadOrdensServicos = function(offset,limit) {
		var query_string = "?atd->id_empreendimento="+ $scope.userLogged.id_empreendimento;

		if($scope.busca.nome != ""){
			query_string += "&("+$.param({'cli->nome':{exp:"like'%"+$scope.busca.nome+"%')"}});
		}

		if($scope.busca.cod_status_servico != null){
			query_string += "&atd->id_status="+ $scope.busca.cod_status_servico;
		}

		if($("#dtaInicial").val() != ""){
			var dta_ordem_servico = moment($("#dtaInicial").val(), 'DD/MM/YYYY').format('YYYY-MM-DD');

			query_string += "&("+$.param({'2':{exp:"=2 AND cast(ven.dta_venda as date) = '"+ dta_ordem_servico +"' )"}});
		}

		$http.get(baseUrlApi()+"ordens-servico/"+ offset +"/"+ limit + query_string)
			.success(function(data, status, headers, config) {
				$scope.ordens_servico = data.itens;
				$scope.paginacao.ordens_servico = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	function loadProdutosByIdOrdemServico() {
		$http.get(baseUrlApi()+"ordem-servico/"+ $scope.objectModel.id +"/produtos")
			.success(function(data, status, headers, config) {
				$scope.objectModel.produtos = data;
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	function loadServicosByIdOrdemServico() {
		$http.get(baseUrlApi()+"ordem-servico/"+ $scope.objectModel.id +"/servicos")
			.success(function(data, status, headers, config) {
				$scope.objectModel.servicos = data;
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	function clearObject() {
		$scope.objectModel = {
			criador: $scope.userLogged,
			id_empreendimento: $scope.userLogged.id_empreendimento,
			cod_status_servico: 4,
			servicos: [],
			produtos: [],
			vlr_total_servicos: 0,
			vlr_total_produtos: 0,
			vlr_total_os: 0,
			dta_ordem_servico: moment().format('DD/MM/YYYY HH:mm:ss')
		};
		$scope.loadOrdensServicos(0,10);
	}

	$scope.isSelected = function(produto) {
		if(!empty(_.findWhere($scope.carrinho, {id_produto: produto.id_produto})))
			return 'product-selected';
		else
			return '';
	}

	$scope.cancelarVenda = function() {
		$scope.carrinho = [];
		$scope.calcTotalCompra();
	}

	$scope.loadCategorias = function(){
		$scope.categoriasProduto = null ;
		$http.get(baseUrlApi()+'categorias?tce->id_empreendimento='+$scope.userLogged.id_empreendimento)
		.success(function(data, status, headers, config) {
			$scope.categoriasProduto = data.categorias ;
		})
		.error(function(data, status, headers, config) {
			$scope.categoriasProduto = [] ;
			if(status != 406)
			console.log('Não foi possivel buscar as categorias');
		}); 
	}

	$scope.setBuscaCategoria = function(categoria){
		$scope.categoria = categoria;
		$scope.loadProdutos();
		$scope.tipo_view = 'grade-produtos';
	}

	$scope.voltarListaCategorias = function(){
		$scope.produtos = [];
		$scope.tipo_view = 'grade-categorias';
	}

	clearObject();
	$scope.abrirCaixa();
	$scope.loadCategorias();
	// $scope.loadProdutos();
	$scope.resizeScreen();
});
