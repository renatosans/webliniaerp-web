app.controller('ControleMesasController', function(
	$scope, $http, $window, $dialogs, UserService, ConfigService, FuncionalidadeService, CozinhaService
) {
	var ng = $scope,
		aj = $http;
	ng.userLogged = UserService.getUserLogado();
	ng.cozinhasDisponiveis = CozinhaService.getCozinhasAtivas(ng.userLogged.id_empreendimento);
	ng.configuracao  = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.layout = { 
		mesas:true,
		detMesa:false,
		SelCliente:false,
		cadCliente:false,
		detComanda:false,
		detItemComanda:false,
		escTipoProduto:false,
		selTipoProduto:false,
		escProduto:false
	} ;
	ng.userLogged.flg_dispositivo=1;
	ng.telaAnterior = null ;
	ng.mesas = [];
	ng.mesaSelecionada = {mesa:{},comandas:[]} ;
	ng.comandaSelecionada = {};
	ng.busca = {} ;
	ng.buscaTipoProduto = {} ;
	ng.categoriasProduto = [];
	ng.loadingMoreProdutos = false ;
	ng.produtos 	= {itens:[],paginacao:[]};
	ng.produto 		= {};
	ng.EditProduto 	= false;
	ng.editComanda 	= false;
	ng.new_cliente 	= { id_empreendimento: ng.userLogged.id_empreendimento, id_perfil: 6 };
	ng.id_ws_dsk 	= ng.configuracao.id_ws_dsk_op;
	ng.status_websocket = 0 ;
	var TimeWaitingResponseTestConection = 10000;
	var timeOutSendTestConection = null ;
	var timeOutWaitingResponseTestConection = null ;

	$('#sizeToggle').trigger("click");

	ng.funcioalidadeAuthorized = function(cod_funcionalidade){
		return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
	}

	ng.changeTela = function(tela,changeValue){
		if(!empty(tela)){
			$.each(ng.layout,function(i,x){
				if(x) ng.telaAnterior = i ;
				ng.layout[i] = false ;
			});

			if(!empty(changeValue)){
				$.each(changeValue,function(i,v){
					ng[i] = v ;
				});

			}

			ng.layout[tela] = true ;
			$('html,body').animate({scrollTop: 0});
			if(tela=='mesas')
				ng.loadMesas();
			if(tela=='SelCliente'){
				ng.busca.cliente = '';
				ng.clientes = [];
			}
			if(tela=='cadCliente'){
				ng.new_cliente = {id_empreendimento:ng.userLogged.id_empreendimento,id_perfil:6};
			}
			else if(tela=='detMesa')
				ng.loadComandasByMesa();
		}
	}

	ng.baseUrl = function(){
		return baseUrl();
	}

	ng.loadMesas = function(offset,limit){
		offset = offset ==  null ? 0 : offset ; 
		limit  = limit  ==  null ? 10 : limit ;
		ng.mesas = null ;
		aj.get(baseUrlApi()+"mesas/resumo/?tm->id_empreendimento="+ng.userLogged.id_empreendimento)
		.success(function(data, status, headers, config) {
			ng.mesas = data;
		})
		.error(function(data, status, headers, config) {
			ng.mesas = [] ;
		}); 
	}

	ng.abrirMesa = function(mesa,index){
		ng.mesaSelecionada.mesa = angular.copy(mesa);
		ng.indeMesaSelecionada = index;
		ng.changeTela('detMesa');
	}

	var interval_busca_clientes = null ;
	ng.autoCompleteCliente = function(busca){
		 clearInterval(interval_busca_clientes);
		 if(!empty(busca)){
			 interval_busca_clientes = setTimeout(function(){
			 	ng.loadClientes(busca);
			 },500);
		}else
			ng.clientes = [] ;
	}

	ng.loadClientes = function(busca){
		ng.clientes = null ;
		busca = angular.copy(busca);
		var url = "usuarios?tue->id_empreendimento="+ng.userLogged.id_empreendimento;
		var query_string = "";
		if(!empty(busca)){
			var buscaCpf  = busca.replace(/\./g, '').replace(/\-/g, '');
			var buscaCnpj = busca.replace(/\./g, '').replace(/\-/g, '').replace(/\//g,'');
			busca = busca.replace(/\s/g, '%');
			query_string += "&"+$.param({"(usu->nome":{exp:"like'%"+busca+"%' OR usu.apelido like '%"+busca+"%' OR tpj.cnpj like '%"+buscaCnpj+"%' OR tpf.cpf like '%"+buscaCpf+"%')"}})+"";
		}
		aj.get(baseUrlApi()+url+query_string)
		.success(function(data, status, headers, config) {
			ng.clientes = data.usuarios ;
		})
		.error(function(data, status, headers, config) {
			ng.clientes = [];
		}); 
	}

	ng.abrirComanda = function(id_cliente,event){
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');
		var post = {
			id_usuario : ng.userLogged.id,
			id_cliente : id_cliente,
			id_empreendimento : ng.userLogged.id_empreendimento,
			dta_venda : moment().format('YYYY-MM-DD HH:mm:ss'),
			id_mesa : ng.mesaSelecionada.mesa.id_mesa 
		}

		aj.post(baseUrlApi()+'mesa',post)
		.success(function(data, status, headers, config) {
			var msg = {
					type : 'table_change',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
					message : JSON.stringify({index_mesa:ng.indeMesaSelecionada,mesa:data.mesa})
				}
			ng.sendMessageWebSocket(msg);
			btn.button('reset');
			ng.changeTela('detMesa');
		})
		.error(function(data, status, headers, config) {
			console.log('Não foi possivel abrir a comanda');
		}); 
	}

	ng.loadComandasByMesa = function(){
		ng.mesaSelecionada.comandas = null ;
		aj.get(baseUrlApi()+'mesa/comandas/'+ng.mesaSelecionada.mesa.id_mesa)
		.success(function(data, status, headers, config) {
			ng.mesaSelecionada.comandas = data ;
		})
		.error(function(data, status, headers, config) {
			ng.mesaSelecionada.comandas = [] ;
			if(status != 406)
			console.log('Não foi possivel buscar as comandas');
		}); 
	}

	ng.qtdTotalComandas = function(){
		var qtd_total = 0 ;
		if(ng.mesaSelecionada.comandas != null){
			$.each(ng.mesaSelecionada.comandas,function(i,x){
				qtd_total += x.qtd_total ;
			});
		}

		return qtd_total ;
	}

	ng.vlrTotalComanda = function(){
		var valor_total = 0 ;
		if(ng.mesaSelecionada.comandas != null){
			$.each(ng.mesaSelecionada.comandas,function(i,x){
				valor_total += x.valor_total ;
			});
		}

		return valor_total ;
	}

	ng.loadComanda = function(id_comanda){
		ng.comandaSelecionada = null ;
		aj.get(baseUrlApi()+'comanda/'+id_comanda)
		.success(function(data, status, headers, config) {
			ng.comandaSelecionada = data ;
		})
		.error(function(data, status, headers, config) {
			ng.comandaSelecionada = {} ;
			if(status != 406)
			console.log('Não foi possivel buscar a comanda');
		}); 
	}

	ng.abrirDetalhesComanda = function(id_comanda){
		ng.changeTela('detComanda');
		ng.loadComanda(id_comanda);
	}

	ng.bucaTipoProduto = function(tipo){
		ng.busca.produtosModal = '' ;
		ng.busca.produtos = '' ;
		if(tipo != null){
			ng.buscaTipoProduto = {};
			ng.buscaTipoProduto[tipo] = null ;
			ng.changeTela('selTipoProduto');
		}else{
			ng.buscaTipoProduto = {};
			ng.changeTela('escProduto');
			ng.loadProdutos();
		}
	}

	ng.getTipoBuscaProduto = function(){
		if(typeof ng.buscaTipoProduto.categoria != 'undefined' ){
			return 'categoria';
		}else if(typeof ng.buscaTipoProduto.fabricante != 'undefined' ){
			return 'fabricante';
		}else{
			return null ;
		}
	}

	ng.loadCategorias = function(){
		ng.categoriasProduto = null ;
		aj.get(baseUrlApi()+'categorias?tce->id_empreendimento='+ng.userLogged.id_empreendimento)
		.success(function(data, status, headers, config) {
			ng.categoriasProduto = data.categorias ;
		})
		.error(function(data, status, headers, config) {
			ng.categoriasProduto = [] ;
			if(status != 406)
			console.log('Não foi possivel buscar as categorias');
		}); 
	}

	ng.loadFabricantes = function(){
		ng.fabricantesProduto = null ;
		aj.get(baseUrlApi()+'fabricantes?tfe->id_empreendimento='+ng.userLogged.id_empreendimento)
		.success(function(data, status, headers, config) {
			ng.fabricantesProduto = data.fabricantes ;
		})
		.error(function(data, status, headers, config) {
			ng.fabricantesProduto = [] ;
			if(status != 406)
			console.log('Não foi possivel buscar os fabricantes');
		}); 
	}

	ng.setBuscaCategoria = function(categoria){
		ng.buscaTipoProduto.categoria = categoria ;
		ng.changeTela('escProduto');
		ng.loadProdutos();
	}

	ng.setBuscaFabricante = function(fabricante){
		ng.buscaTipoProduto.fabricante = fabricante ;
		ng.changeTela('escProduto');
		ng.loadProdutos();
	}

	var interval_busca_produtos = null ;
	ng.autoCompleteProdutos = function(busca){
		 clearInterval(interval_busca_produtos);
		 interval_busca_produtos = setTimeout(function(){
		 	ng.loadProdutos();
		 },500);
	}


	ng.loadProdutos = function(offset, limit,concat) {
		concat = concat == null ? false : true ;
		
		if(!concat) ng.produtos.itens = null;

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(!empty(ng.busca.produtos)){
			var busca = ng.busca.produtos.replace(/\s/g, '%');
			if(isNaN(Number(ng.busca.produtos)))
				query_string += "&("+$.param({nome:{exp:"like '%"+busca+"%' OR codigo_barra like '%"+busca+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+busca+"%' OR codigo_barra like '%"+busca+"%' OR pro.id = "+busca+""}})+")";
		}

		if(ng.getTipoBuscaProduto() == 'categoria'){
			query_string += '&pro->id_categoria='+ng.buscaTipoProduto.categoria.id; 
		}else if(ng.getTipoBuscaProduto() == 'fabricante'){
			query_string += '&pro->id_fabricante='+ng.buscaTipoProduto.fabricante.id; 
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				if(!concat){
					var aux = {itens:data.produtos,paginacao:ng.getNextPage(data.paginacao)}
					ng.produtos = aux;
				}else{
					ng.produtos.itens = ng.produtos.itens.concat(data.produtos);
					ng.produtos.paginacao = ng.getNextPage(data.paginacao);
					ng.loadingMoreProdutos = false ; 
				}
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.produtos.itens = [];
					ng.produtos.paginacao = [];
				}
			});
	}

	ng.openModalProdutos = function(){
		ng.busca.produtosModal = '' ;
		ng.loadProdutosModal();
		$('#list_produtos').modal('show')
	}

	ng.loadProdutosModal = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.produtosModal = null ;
		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(!empty(ng.busca.produtosModal)){
			var busca = ng.busca.produtosModal.replace(/\s/g, '%');
			if(isNaN(Number(ng.busca.produtosModal)))
				query_string += "&("+$.param({nome:{exp:"like '%"+busca+"%' OR codigo_barra like '%"+busca+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+busca+"%' OR codigo_barra like '%"+busca+"%' OR pro.id = "+busca+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
					$.each(data.produtos,function(i,v){
						data.produtos[i].qtd = null ;
					});
					var aux = {itens:data.produtos,paginacao:data.paginacao}
					ng.produtosModal = aux;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					var aux = {itens:[],paginacao:[]}
					ng.produtosModal = aux ;
				}
			});
	}

	ng.incluirItemComandaModal = function(item,event){
		dlg = $dialogs.confirm('Atenção!!!' ,'Este ítem é para entrega?');

		dlg.result.then(
			function(btn){
				incluirItemComandaModalAction(item, true);
			},
			function(){
				incluirItemComandaModalAction(item, false);
			}
		);
	}

	function incluirItemComandaModalAction(item, flg_delivery){
		var produto = angular.copy(item);
		produto.qtd = empty(produto.qtd) ? 1 : produto.qtd ; 
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');
		var post = {
			id_venda : ng.comandaSelecionada.comanda.id,
			id_usuario : ng.userLogged.id_usuario,
			id_produto : produto.id ,
			desconto_aplicado : 0 ,
			valor_desconto : 0 ,
			qtd : produto.qtd,
			observacoes: "",
			valor_real_item : round(produto.vlr_venda_varejo,2) ,
			vlr_custo : produto.vlr_custo_real,
			perc_imposto_compra : produto.perc_imposto_compra ,
			perc_desconto_compra : produto.perc_desconto_compra,
			perc_margem_aplicada : produto.perc_venda_varejo,
			id_empreendimento : ng.userLogged.id_empreendimento,
			id_deposito : ng.configuracao.id_deposito_padrao,
			flg_produto_composto : produto.flg_produto_composto,
			id_usuario : ng.userLogged.id,
			dta_create : moment().format('YYYY-MM-DD HH:mm:ss'),
			dta_lancamento : moment().format('YYYY-MM-DD HH:mm:ss'),
			id_mesa : ng.mesaSelecionada.mesa.id_mesa,
			flg_delivery: (flg_delivery) ? 1 : 0
		}

		aj.post(baseUrlApi()+"item_comanda/add",post)
		.success(function(data, status, headers, config) {
			if(Number(produto.flg_produto_composto) == 1){
				data.ordem_producao.nome_cliente = ((ng.configuracao.id_cliente_movimentacao_caixa == data.ordem_producao.id_cliente) ? '' : data.ordem_producao.nome_cliente.toUpperCase());
				
				if(!empty(ng.cozinhasDisponiveis) && ng.cozinhasDisponiveis.length > 0) {
					$.each(ng.cozinhasDisponiveis, function(i, cozinha){
						var msg = {
							from: ng.id_ws_web,
							to: cozinha.id_ws_dsk,
							type:'cop_print',
							message : JSON.stringify({ 
								numOrdemProducao: 		(!empty(data.ordem_producao.id_ordem_producao) 			? data.ordem_producao.id_ordem_producao 		: ""),
								numMesa: 				(!empty(data.ordem_producao.dsc_mesa) 					? data.ordem_producao.dsc_mesa 					: ""),
								numComanda: 			(!empty(data.ordem_producao.id_venda) 					? data.ordem_producao.id_venda 					: ""),
								nmeSolicitante: 		(!empty(data.ordem_producao.nome_usuario) 				? data.ordem_producao.nome_usuario 				: ""),
								nmeCliente: 			(!empty(data.ordem_producao.nome_cliente) 				? data.ordem_producao.nome_cliente 				: ""),
								nmeEndereco: 			(!empty(data.ordem_producao.nme_endereco) 				? data.ordem_producao.nme_endereco 				: ""),
								nmeComplementoEndereco: (!empty(data.ordem_producao.nme_complemento_endereco) 	? data.ordem_producao.nme_complemento_endereco 	: ""),
								numEndereco: 			(!empty(data.ordem_producao.num_endereco) 				? data.ordem_producao.num_endereco 				: ""),
								nmeBairro: 				(!empty(data.ordem_producao.nme_bairro) 				? data.ordem_producao.nme_bairro 				: ""),
								nmeUfEstado: 			(!empty(data.ordem_producao.nme_uf_estado) 				? data.ordem_producao.nme_uf_estado 			: ""),
								nmeMunicipio: 			(!empty(data.ordem_producao.nme_municipio) 				? data.ordem_producao.nme_municipio 			: ""),
								nmeProduto: 			(!empty(data.ordem_producao.nome_produto) 				? data.ordem_producao.nome_produto 				: ""),
								nmeCorSabor: 			(!empty(data.ordem_producao.sabor) 						? data.ordem_producao.sabor 					: ""),
								nmeTamanho: 			(!empty(data.ordem_producao.tamanho) 					? data.ordem_producao.tamanho 					: ""),
								nmeFabricante: 			(!empty(data.ordem_producao.nome_fabricante) 			? data.ordem_producao.nome_fabricante 			: ""),
								codCategoria: 			(!empty(data.ordem_producao.cod_categoria) 				? data.ordem_producao.cod_categoria 				: ""),
								nmeCategoria: 			(!empty(data.ordem_producao.descricao_categoria) 		? data.ordem_producao.descricao_categoria 		: ""),
								qtdItem: 				(!empty(data.ordem_producao.qtd) 						? data.ordem_producao.qtd 						: ""),
								nmePrinterModel: 		(!empty(ng.configuracao.printer_model_op) 	    		? ng.configuracao.printer_model_op 				: ""),
								flgDelivery: 			(!empty(data.ordem_producao.flg_delivery) 	    		? data.ordem_producao.flg_delivery 				: 0)
							})
						}

						ng.sendMessageWebSocket(msg);
					});
				}

				var msg = {
					type : 'op_new',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
					message : JSON.stringify(data.ordem_producao)
				}
				ng.sendMessageWebSocket(msg);
			}
			var msg = {
				type : 'table_change',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
				message : JSON.stringify({index_mesa:ng.indeMesaSelecionada,mesa:data.mesa,id_comanda:ng.comandaSelecionada.comanda.id})
			}
				
			ng.sendMessageWebSocket(msg);
			item.qtd = null ;
			btn.button('reset');
			ng.loadComanda(ng.comandaSelecionada.comanda.id);
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			if(status == 406){
				$dialogs.notify('Atenção!','<strong>Produto com estoque insuficiente</strong>');
			}else
				$dialogs.notify('Atenção!','<strong>Erro ao incluir produto</strong>');
		});
	}

	ng.getNextPage = function(paginacao){
		var indexNext ;
		var pages = [];
		$.each(paginacao,function(i,x){
			if(x.current)
				indexNext = (x.index + 1) ;
			pages[x.index] = x ;
		});
		
		if(typeof pages[indexNext] == 'undefined')
			return false ;
		else
			return pages[indexNext];
	}

	ng.paginacaoProdutos = function(){
		$(window).scroll(function() {
		    if( ( $(document).height() == ($(window).scrollTop() + $(window).height()) ) && ng.layout.escProduto ){
		    	if(ng.produtos.paginacao != false && !ng.loadingMoreProdutos){
		    		ng.loadingMoreProdutos = true ;
		    		ng.loadProdutos(ng.produtos.paginacao.offset,ng.produtos.paginacao.limit,true);
		    	}
		    }

		});
	}

	ng.selProduto = function(produto,edit){
		edit = edit == null ? false : edit ;
		ng.EditProduto = edit ;
		ng.produto = angular.copy(produto);
		if(!edit)
			ng.produto.qtd = 1 ;
		else
			ng.produto.qtd = ng.produto.qtd_total ; 
		ng.changeTela('detItemComanda');
	}

	ng.getPermission = function(str){
		return _in(ng.userLogged.id_perfil,str);
	}

	ng.cancelarProduto = function(){
		ng.produto = {} ;
		if(ng.EditProduto)
			ng.changeTela('detComanda');
		else
			ng.changeTela('escProduto');
	}


	ng.incluirItemComanda = function(event){
		dlg = $dialogs.confirm('Atenção!!!' ,'Este ítem é para entrega?');

		dlg.result.then(
			function(btn){
				incluirItemComandaAction(true);
			},
			function(){
				incluirItemComandaAction(false);
			}
		);
	}

	function incluirItemComandaAction(flg_delivery) {
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');
		var post = {
			id_venda : ng.comandaSelecionada.comanda.id,
			id_usuario : ng.userLogged.id,
			id_produto : ng.produto.id ,
			observacoes: ng.produto.observacoes,
			desconto_aplicado : 0 ,
			valor_desconto : 0 ,
			qtd : ng.produto.qtd,
			valor_real_item : round(ng.produto.vlr_venda_varejo,2) ,
			vlr_custo : ng.produto.vlr_custo_real,
			perc_imposto_compra : ng.produto.perc_imposto_compra ,
			perc_desconto_compra : ng.produto.perc_desconto_compra,
			perc_margem_aplicada : ng.produto.perc_venda_varejo,
			id_empreendimento : ng.userLogged.id_empreendimento,
			id_deposito : ng.configuracao.id_deposito_padrao,
			flg_produto_composto : ng.produto.flg_produto_composto,
			dta_create : moment().format('YYYY-MM-DD HH:mm:ss'),
			dta_lancamento : moment().format('YYYY-MM-DD HH:mm:ss'),
			id_mesa : ng.mesaSelecionada.mesa.id_mesa,
			flg_delivery: (flg_delivery) ? 1 : 0
		}

		aj.post(baseUrlApi()+"item_comanda/add",post)
		.success(function(data, status, headers, config) {
			if(Number(ng.produto.flg_produto_composto) == 1){
				data.ordem_producao.nome_cliente = ((ng.configuracao.id_cliente_movimentacao_caixa == data.ordem_producao.id_cliente) ? '' : data.ordem_producao.nome_cliente.toUpperCase());
				
				if(!empty(ng.cozinhasDisponiveis) && ng.cozinhasDisponiveis.length > 0) {
					$.each(ng.cozinhasDisponiveis, function(i, cozinha){
						var msg = {
							from:ng.id_ws_web,
							to: cozinha.id_ws_dsk,
							type:'cop_print',
							message : JSON.stringify({ 
								numOrdemProducao: 		(!empty(data.ordem_producao.id_ordem_producao) 			? data.ordem_producao.id_ordem_producao 		: ""),
								numMesa: 				(!empty(data.ordem_producao.dsc_mesa) 					? data.ordem_producao.dsc_mesa 					: ""),
								numComanda: 			(!empty(data.ordem_producao.id_venda) 					? data.ordem_producao.id_venda 					: ""),
								nmeSolicitante: 		(!empty(data.ordem_producao.nome_usuario) 				? data.ordem_producao.nome_usuario 				: ""),
								nmeCliente: 			(!empty(data.ordem_producao.nome_cliente) 				? data.ordem_producao.nome_cliente 				: ""),
								nmeEndereco: 			(!empty(data.ordem_producao.nme_endereco) 				? data.ordem_producao.nme_endereco 				: ""),
								nmeComplementoEndereco: (!empty(data.ordem_producao.nme_complemento_endereco) 	? data.ordem_producao.nme_complemento_endereco 	: ""),
								numEndereco: 			(!empty(data.ordem_producao.num_endereco) 				? data.ordem_producao.num_endereco 				: ""),
								nmeBairro: 				(!empty(data.ordem_producao.nme_bairro) 				? data.ordem_producao.nme_bairro 				: ""),
								nmeUfEstado: 			(!empty(data.ordem_producao.nme_uf_estado) 				? data.ordem_producao.nme_uf_estado 			: ""),
								nmeMunicipio: 			(!empty(data.ordem_producao.nme_municipio) 				? data.ordem_producao.nme_municipio 			: ""),
								nmeProduto: 			(!empty(data.ordem_producao.nome_produto) 				? data.ordem_producao.nome_produto 				: ""),
								nmeCorSabor: 			(!empty(data.ordem_producao.sabor) 						? data.ordem_producao.sabor 					: ""),
								nmeTamanho: 			(!empty(data.ordem_producao.tamanho) 					? data.ordem_producao.tamanho 					: ""),
								nmeFabricante: 			(!empty(data.ordem_producao.nome_fabricante) 			? data.ordem_producao.nome_fabricante 			: ""),
								codCategoria: 			(!empty(data.ordem_producao.cod_categoria) 				? data.ordem_producao.cod_categoria 				: ""),
								nmeCategoria: 			(!empty(data.ordem_producao.descricao_categoria) 		? data.ordem_producao.descricao_categoria 		: ""),
								dscObservacoes: 		(!empty(ng.produto.observacoes) 						? ng.produto.observacoes 						: ""),
								qtdItem: 				(!empty(data.ordem_producao.qtd) 						? data.ordem_producao.qtd 						: ""),
								nmePrinterModel: 		(!empty(ng.configuracao.printer_model_op) 	    		? ng.configuracao.printer_model_op 				: ""),
								flgDelivery: 			(!empty(data.ordem_producao.flg_delivery) 	    		? data.ordem_producao.flg_delivery 				: 0)
							})
						}
						ng.sendMessageWebSocket(msg);
					});
				}
				
				var msg = {
					type : 'op_new',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
					message : JSON.stringify(data.ordem_producao)
				}
				ng.sendMessageWebSocket(msg);

			}
			var msg = {
					type : 'table_change',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
					message : JSON.stringify({index_mesa:ng.indeMesaSelecionada,mesa:data.mesa,id_comanda:ng.comandaSelecionada.comanda.id})
				}
			ng.sendMessageWebSocket(msg);
			btn.button('reset');
			ng.produto = {} ;
			ng.abrirDetalhesComanda(ng.comandaSelecionada.comanda.id);
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			if(status == 406){
				$dialogs.notify('Atenção!','<strong>Produto com estoque insuficiente</strong>');
			}else
				$dialogs.notify('Atenção!','<strong>Erro ao incluir produto</strong>');
		});
	}

	ng.editItemComanda = function(event){
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');
		var post = {
			id_mesa : ng.mesaSelecionada.mesa.id_mesa,
			campos:{
				id_venda : ng.comandaSelecionada.comanda.id,
				id_produto : ng.produto.id ,
				desconto_aplicado : 0 ,
				valor_desconto : 0 ,
				qtd : ng.produto.qtd,
				valor_real_item : round(ng.produto.vlr_venda_varejo,2) ,
				vlr_custo : ng.produto.vlr_custo_real,
				perc_imposto_compra : ng.produto.perc_imposto_compra ,
				perc_desconto_compra : ng.produto.perc_desconto_compra,
				perc_margem_aplicada : ng.produto.perc_venda_varejo,
				id_empreendimento : ng.userLogged.id_empreendimento,
				id_deposito : ng.configuracao.id_deposito_padrao
			},
			where : 'id='+ng.produto.id_item_venda
		}

		aj.post(baseUrlApi()+"item_comanda/edit",post)
		.success(function(data, status, headers, config) {
			var msg = {
					type : 'table_change',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
					message : JSON.stringify({index_mesa:ng.indeMesaSelecionada,mesa:data.mesa,id_comanda:ng.comandaSelecionada.comanda.id})
				}
			ng.sendMessageWebSocket(msg);
			btn.button('reset');
			ng.produto = {} ;
			ng.abrirDetalhesComanda(ng.comandaSelecionada.comanda.id);
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			if(status == 406){
				$dialogs.notify('Atenção!','<strong>Produto com estoque insuficiente</strong>');
			}else
				$dialogs.notify('Atenção!','<strong>Erro ao incluir produto</strong>');
		});
	}

	ng.excluirItemComanda = function(event){
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');

		aj.get(baseUrlApi()+"item_comanda/delete/"+ng.produto.id_item_venda+"/"+ng.mesaSelecionada.mesa.id_mesa)
		.success(function(data, status, headers, config) {
			var msg = {
					type : 'table_change',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
					message : JSON.stringify({index_mesa:ng.indeMesaSelecionada,mesa:data.mesa,id_comanda:ng.comandaSelecionada.comanda.id})
				}
			ng.sendMessageWebSocket(msg);
			btn.button('reset');
			ng.produto = {} ;
			ng.abrirDetalhesComanda(ng.comandaSelecionada.comanda.id);
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			$dialogs.notify('Atenção!','<strong>Erro ao excluir produto</strong>');
		});
	}

	ng.vlrTotalItensComanda = function(){
		var total = 0 ;
		if(!empty(ng.comandaSelecionada) && typeof ng.comandaSelecionada.comanda == 'object'){
			$.each(ng.comandaSelecionada.comanda.itens,function(i,x){
				var vlr = $.isNumeric(Number(x.vlr_venda_varejo)) ? x.vlr_venda_varejo : 0 ;
				var qtd = $.isNumeric(Number(x.qtd_total)) ? x.qtd_total : 0 ;
				total += (qtd*vlr);
			});
		}
		return total ;
	}

	ng.totalItensComanda = function(){
		var total = 0 ;
		if(!empty(ng.comandaSelecionada) && typeof ng.comandaSelecionada.comanda == 'object'){
			$.each(ng.comandaSelecionada.comanda.itens,function(i,x){
				var qtd = $.isNumeric(Number(x.qtd_total)) ? x.qtd_total : 0 ;
				total += qtd;
			});
		}
		return total ;
	}

	ng.goChangeCliente = function(){
		ng.editComanda = true ;
		ng.changeTela('SelCliente');
	}

	ng.changeCliente = function(id_cliente,event){
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');
		var id_comanda = ng.comandaSelecionada.comanda.id
		var post = {
			campos:{
				id_cliente : id_cliente
			},
			where : 'id='+id_comanda
		}

		aj.post(baseUrlApi()+"comanda/edit",post)
		.success(function(data, status, headers, config) {
			btn.button('reset');
			ng.editComanda = false ;
			ng.changeTela('detComanda');
			ng.comandaSelecionada = {} ;
			ng.loadComanda(id_comanda);
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			$dialogs.notify('Atenção!','<strong>Erro ao trocar o cliente da comanda</strong>');
		});
	}

	ng.cadastrarCliente = function($event){
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');
		var cliente = angular.copy(ng.new_cliente);
		cliente.celular = empty(cliente.celular) ? null : cliente.celular ;
		aj.post(baseUrlApi()+"comanda/cliente/new",cliente)
		.success(function(data, status, headers, config) {
			if(ng.editComanda){
				ng.changeCliente(data.usuario.id,$event);
			}else
				ng.abrirComanda(data.usuario.id,$event);
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			if(status == 406){
				var str = "";
				$.each(data,function(i,x){
					str += x[0]+'<br/>';
				});
				$dialogs.notify('Atenção!','<strong>'+str+'</strong>');
			}
		});
	}

	ng.newConnWebSocket = function(){
		ng.conn = new WebSocket(ng.configuracao.patch_socket_sat);
		ng.conn.onopen = function(e) {
			console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - WebSocket conectado.');
		};

		ng.conn.onclose = function(e) {

		}

		ng.conn.onmessage = function(e) {
			console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - Mensagem Recebida : '+e.data);
			var data = JSON.parse(e.data);
			data.message = parseJSON(data.message);
			switch(data.type){
				case 'session_id':
					ng.id_ws_web = data.to ;
					var msg = {
						type : 'set_id_empreendimento',from : data.to,
						message : JSON.stringify({id_empreendimento:ng.userLogged.id_empreendimento,id_usuario:ng.userLogged.id,id_perfil:ng.userLogged.id_perfil})
					}
					ng.sendMessageWebSocket(msg);
					enviaTesteConexao();
					break;
				case 'op_finished':
					aj.get(baseUrlApi() +"mesa/ordem_producao/"+ data.message.id_ordem_producao)
					.success(function(data, status, headers, config) {
						noty({
							layout: 'topRight',
							type: 'success',
							theme: 'relax',
							text: '<i class="fa fa-check-circle"></i> <strong>PRODUTO PRONTO P/ ENTREGA</strong><br/>CLIENTE: '+ ((ng.configuracao.id_cliente_movimentacao_caixa == data.id_cliente) ? '' : data.nome_cliente.toUpperCase()) + '<br/>MESA: '+ data.dsc_mesa.toUpperCase(),
							animation : {
								open  : 'animated bounceInRight',
								close : 'animated bounceOutRight'
							}
						});
					})
					.error(function(data, status, headers, config) {
						
					});
				break;
				case 'table_change':
					$scope.$apply(function () { 
						if(empty(data.message.index_mesa))
							data.message.index_mesa = getIndex('id_mesa',data.message.mesa.id_mesa,ng.mesas);
						ng.mesas[data.message.index_mesa] = data.message.mesa ;
						if(!(empty(ng.mesaSelecionada.mesa)) && ng.mesaSelecionada.mesa.id_mesa == data.message.mesa.id_mesa)
							ng.loadComandasByMesa();
						if(( !empty(data.message.id_comanda) && !empty(ng.comandaSelecionada.comanda)) && ng.comandaSelecionada.comanda.id == data.message.id_comanda)
							ng.loadComanda(data.message.id_comanda);
						
					});
				break;
				case 'connection_test_response':
					clearTimeout(timeOutWaitingResponseTestConection);
					$scope.$apply(function () { ng.status_websocket = 2 ;});
					ng.id_ws_dsk = data.from;
					console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - Conexão com App client extabelecida');
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
			if(!empty(ng.cozinhasDisponiveis) && ng.cozinhasDisponiveis.length > 0) {
				$.each(ng.cozinhasDisponiveis, function(i, cozinha){
					var msg = {
						from: ng.id_ws_web,
						to: cozinha.id_ws_dsk,
						type: 'connection_test_request',
						message: 'Teste de conexão com client desktop'
					};

					ng.sendMessageWebSocket(msg);

					timeOutWaitingResponseTestConection = setTimeout(function() {
						$scope.$apply(function () { ng.status_websocket = 1 ;});
						console.log(moment().format("YYYY-MM-DD HH:mm:ss")+' - Não foi possível obter resposta do APP Client para o teste de conexão');
					}, TimeWaitingResponseTestConection);
				});
			}

			enviaTesteConexao();
		},60000);
	}

	if(!empty(ng.configuracao.patch_socket_sat))
		ng.newConnWebSocket();

	ng.loadMesas();
	ng.loadCategorias();
	ng.loadFabricantes();
	ng.paginacaoProdutos();
});