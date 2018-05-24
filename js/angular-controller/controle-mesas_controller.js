app.controller('ControleMesasController', function(
	$scope, $http, $window, $dialogs, UserService, ConfigService, FuncionalidadeService, CozinhaService
) {
	var ng = $scope,
		aj = $http;
	ng.userLogged = UserService.getUserLogado();
	ng.id_modulo = FuncionalidadeService.getIdModulo();
	ng.allCozinhas = CozinhaService.getAllCozinhas(ng.userLogged.id_empreendimento);
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
	ng.busca_avancada = {};
	ng.userLogged.flg_dispositivo = 1;
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
	ng.baseUrl = baseUrl;
	ng.vlr_total_pedido = 0;
	ng.isFullscreen = false;
	ng.resizeScreen = function() {
		if(!ng.isFullscreen){
			$("#map_canvas").css("height", 700);
			$("footer").addClass("hide");
			$(".main-header").addClass("hide");
			$("#wrapper").css("min-height", "0px");
			$("#main-container").css("min-height", "0px");
			$("#main-container").css("margin-left", 0).css("padding-top", 45);
			//$("#top-nav").toggle();
			$("aside").toggle();
			$("#breadcrumb").toggle();
			ng.isFullscreen = !ng.isFullscreen;
		}
		else {
			$("#map_canvas").css("height", 600);
			$("footer").removeClass("hide");
			$(".main-header").removeClass("hide");
			$("#wrapper").css("min-height", "800px");
			$("#main-container").css("min-height", "800px");
			$("#main-container").css("margin-left", 194).css("padding-top", 45);
			//$("#top-nav").toggle();
			$("aside").toggle();
			$("#breadcrumb").toggle();
			ng.isFullscreen = !ng.isFullscreen;
		}
	}

	ng.getValorTaxaServico = function(){
		if(!empty(ng.configuracao.prc_taxa_servico))
			return ((ng.vlrTotalItensComanda() * ng.configuracao.prc_taxa_servico) / 100);
		else
			return 0;
	}

	ng.funcioalidadeAuthorized = function(cod_funcionalidade){
		return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
	}

	ng.showPesquisaAvancada = function(){
		$('#pesquisa-avancada').modal('show');
		ng.clientes_comanda = null;
		ng.busca_avancada.nome = "";
	}

	ng.showAvaliableKitchens = function(){
		$('#avaliableKitchens').modal('show');
		$('[data-toggle="tooltip"]').tooltip();
	}

	ng.diminuirQuantidadeProduto = function() {
		if(empty(ng.produto.qtd))
			ng.produto.qtd = 0;
		
		if(parseInt(ng.produto.qtd, 10) > 0)
			ng.produto.qtd = (parseInt(ng.produto.qtd,10) - 1);
	}

	ng.aumentarQuantidadeProduto = function() {
		if(empty(ng.produto.qtd))
			ng.produto.qtd = 1;
		else
			ng.produto.qtd = (parseInt(ng.produto.qtd,10) + 1);
	}

	ng.loadComandasByCliente = function(){
		ng.clientes_comanda = [];
		var queryString = "?usu->id_empreendimento="+ ng.userLogged.id_empreendimento;
		if (ng.busca_avancada.nome == "" || ng.busca_avancada.nome == null) {
			return false;
		}
		else if (!empty(ng.busca_avancada.nome) || ng.busca_avancada.nome != null) {
			queryString += "&"+$.param({"(usu->nome":{exp:"like '%"+ng.busca_avancada.nome+"%' OR usu.id_externo = '"+ng.busca_avancada.nome +"')"}});
		}
		aj.get(baseUrlApi()+"mesa/comandas/cliente"+queryString)
		.success(function(data, status, headers, config) {
				ng.clientes_comanda = data;
			})
			.error(function(data, status, headers, config) {
				ng.clientes_comanda = [];
			});
	}

	ng.loadProdutoAdicionais = function() {
		ng.produto.adicionais = [];
		aj.get(baseUrlApi() +"produto/get/adicionais/"+ ng.produto.id)
			.success(function(data, status, headers, config) {
				ng.produto.adicionais = data;
				if(!empty(ng.produto.id_ordem_producao))
					ng.loadProdutoAdicionaisSelecionados();
			})
			.error(function(data, status, headers, config) {
				ng.produto.adicionais = [];
			});
	}

	ng.loadProdutoAdicionaisSelecionados = function() {
		ng.produto.adicionais_selecionados = [];
		aj.get(baseUrlApi() +"ordem-producao/"+ ng.produto.id_ordem_producao +"/adicionais/")
			.success(function(data, status, headers, config) {
				angular.forEach(data, function(item, i) {
					ng.produto.adicionais_selecionados.push(_.findWhere(ng.produto.adicionais, {id: parseInt(item.id, 10)}));
				});
			})
			.error(function(data, status, headers, config) {
				ng.produto.adicionais_selecionados = [];
			});
	}

	ng.selAdicional = function(index, adicional) {
		if(ng.produto.id_item_venda == null || (ng.produto.id_item_venda != null && ng.produto.id_ordem_producao == null)) {
			if(empty(ng.produto.adicionais_selecionados))
				ng.produto.adicionais_selecionados = [];
			
			if(empty(_.findWhere(ng.produto.adicionais_selecionados, { id: adicional.id })))
				ng.produto.adicionais_selecionados.push( angular.copy(adicional) );
			else {
				angular.forEach(ng.produto.adicionais_selecionados, function(item, i) {
					if(item.id == adicional.id){
						ng.produto.adicionais_selecionados = _.without(ng.produto.adicionais_selecionados, item);
					}
				});
			}
		}
	}

	ng.isAdicionalSelected = function(adicional){
		return (!empty(_.findWhere(ng.produto.adicionais_selecionados, { id: adicional.id })));
	}

	ng.openQRCodeCapture = function(){
		$('#modalCameraQRCode').modal('show');
		let scanner = new Instascan.Scanner({ 
			video: document.getElementById('qrcode-preview'),
			mirror: false
		});
			
		scanner.addListener('scan', function (content) {
			scanner.stop();
			$('#modalCameraQRCode').modal('hide');
			ng.abrirDetalhesComanda(content);
		});

		Instascan.Camera.getCameras().then(function (cameras) {
			if (cameras.length > 0) {
				if(cameras.length > 1) {
					var camera = _.findWhere(cameras, function(camera){if(camera.name.indexOf('front') != -1) return camera;})
					scanner.start(camera);
				}
				else
					scanner.start(cameras[0]);
			} else {
				alert('No cameras found.');
			}
		}).catch(function (e) {
			alert(e);
		});
	}

	ng.imprimirComandaEletronica = function(comanda){
		var post = {
			id : comanda.id,
			value : 0
		}

		aj.post(baseUrlApi()+'comanda/atualiza/impressao/',post)
		.success(function(data, status, headers, config) {
			ng.changeTela('detMesa');
		})
		.error(function(data, status, headers, config) {
			
		}); 
	}

	ng.cancelarComanda = function(id_comanda) {
		$('#autorizacao').modal('hide');
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir esta comanda?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"orcamento/delete/"+id_comanda+"/"+ng.userLogged.id_empreendimento+"/"+ng.userLogged.id)
			.success(function(data, status, headers, config) {
				ng.changeTela('detMesa');
				var msg = {
					type 				: 'table_change',
					from 				: ng.id_ws_web,
					to_empreendimento 	: ng.userLogged.id_empreendimento,
					message 			: ""
				};
				ng.sendMessageWebSocket(msg);
			})
			.error(function(data, status, headers, config) {
				
			});
		}, undefined);	
	}

	ng.changeTela = function(tela,changeValue,event){
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

			if((tela=='SelCliente') && (!empty(ng.configuracao.flg_controlar_comanda_cliente, true)) && (parseInt(ng.configuracao.flg_controlar_comanda_cliente,10) == 0)) {
				ng.abrirComanda(ng.configuracao.id_cliente_movimentacao_caixa, event);
			}

			ng.layout[tela] = true;
			
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

	ng.loadMesas = function(offset,limit){
		offset = offset ==  null ? 0 : offset ; 
		limit  = limit  ==  null ? 10 : limit ;
		ng.mesas = null ;
		aj.get(baseUrlApi()+"mesas/resumo/?tm->id_empreendimento="+ng.userLogged.id_empreendimento)
		.success(function(data, status, headers, config) {
			ng.mesas = data;
			if(ng.mesas.length == 1)
				ng.abrirMesa(ng.mesas[0], 0);
		})
		.error(function(data, status, headers, config) {
			ng.mesas = [] ;
		}); 
	}

	ng.abrirMesa = function(mesa,index){
		ng.mesaSelecionada.mesa = angular.copy(mesa);
		ng.indexMesaSelecionada = index;
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
			query_string += "&"+$.param({"(usu->nome":{exp:"like'%"+busca+"%' OR usu.apelido like '%"+busca+"%' OR usu.tel_fixo like '%"+busca+"%' OR usu.id_externo like '%"+busca+"%' OR usu.num_cartao like '%"+busca+"%' OR usu.celular like '%"+busca+"%' OR usu.endereco like '%"+busca+"%' OR tpj.cnpj like '%"+buscaCnpj+"%' OR tpf.cpf like '%"+buscaCpf+"%')"}})+"";
		}
		aj.get(baseUrlApi()+url+query_string)
		.success(function(data, status, headers, config) {
			ng.clientes = data.usuarios ;
		})
		.error(function(data, status, headers, config) {
			ng.clientes = [];
		}); 
	}

	ng.abrirComanda = function(id_cliente,event,item){
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');

		var post = {
			id_usuario : ng.userLogged.id,
			id_cliente : id_cliente,
			id_empreendimento : ng.userLogged.id_empreendimento,
			dta_venda : moment().format('YYYY-MM-DD HH:mm:ss'),
			id_mesa : ng.mesaSelecionada.mesa.id_mesa,
			num_cartao_fisico: (!empty(item) && !empty(item.num_cartao)) ? item.num_cartao : null
		};

		aj.post(baseUrlApi()+ 'mesa', post)
			.success(function(data, status, headers, config) {
				var msg = {
					type: 'table_change', 
					from: ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
					message: JSON.stringify({
						index_mesa: ng.indexMesaSelecionada, 
						mesa: data.mesa
					})
				};
				if (item != null) {
					if (!empty(item.num_cartao)) {
						ng.vincular_cartao_fisico_automaticamente = true;
						ng.num_cartao_fisico = item.num_cartao;
					}
				}
				
				ng.sendMessageWebSocket(msg);
				btn.button('reset');
				// ng.changeTela('detMesa');
				ng.abrirDetalhesComanda(data.id_venda);
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				alert(data);
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
			if(!empty(ng.vincular_cartao_fisico_automaticamente) && ng.vincular_cartao_fisico_automaticamente && !empty(ng.num_cartao_fisico)) {
				ng.loadCartoes();
			}
		})
		.error(function(data, status, headers, config) {
			ng.comandaSelecionada = {} ;
		}); 
	}

	ng.abrirDetalhesComanda = function(id_comanda){
		ng.changeTela('detComanda');
		ng.loadComanda(id_comanda);
		$('#pesquisa-avancada').modal('hide');
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
				query_string += "&("+$.param({nome:{exp:"LIKE '%"+busca+"%' OR pro.codigo_barra LIKE '%"+busca+"%' OR tcp.nome_cor LIKE '%"+busca+"%' OR tt.nome_tamanho LIKE '%"+busca+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"LIKE '%"+busca+"%' OR pro.codigo_barra LIKE '%"+busca+"%' OR tcp.nome_cor LIKE '%"+busca+"%' OR tt.nome_tamanho LIKE '%"+busca+"%' OR pro.id = "+busca+""}})+")";
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

	ng.openModalVincularCartao = function(){
		$('#modalVincularCartao').modal('show');
		ng.msg_erro_cartao = 0;
		ng.num_cartao_fisico = '';
		$('#modalVincularCartao').on('shown.bs.modal', function(){
			setTimeout(function(){
				$("#modalVincularCartao #buscaCartao").focus();
			}, 1);
		});
	}

	ng.enterComanda = function() {
		var comanda = _.findWhere(ng.mesaSelecionada.comandas, {id_comanda: parseInt(ng.busca.numero_comanda, 10)});

		if(!empty(comanda))
			ng.abrirDetalhesComanda(comanda.id_comanda);
		else {
			comanda = _.findWhere(ng.mesaSelecionada.comandas, {num_cartao_fisico: ng.busca.numero_comanda.toString()});
			if(!empty(comanda)) 
				ng.abrirDetalhesComanda(comanda.id_comanda);
		}
		ng.busca.numero_comanda = "";
	}

	ng.loadComandaByNumCartao = function(){
		ng.comandaSelecionada = null;
		aj.get(baseUrlApi()+'comanda/cartao-fisico/' + ng.busca.numero_comanda +'?tv->flg_excluido=0&tv->venda_confirmada=0&tv->id_empreendimento='+ ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.abrirDetalhesComanda(data.comanda.id);
			})
			.error(function(data, status, headers, config) {
				$dialogs.notify('Desculpe!','<strong>Não foi possível localizar uma comanda com o código informado!</strong>');			
			}); 
	}

	ng.loadComandaById = function(){
		ng.comandaSelecionada = null;
		aj.get(baseUrlApi()+'comanda/' + ng.busca.numero_comanda)
			.success(function(data, status, headers, config) {
				ng.abrirDetalhesComanda(data.comanda.id);
			})
			.error(function(data, status, headers, config) {
				$dialogs.notify('Desculpe!','<strong>Não foi possível localizar uma comanda com o código informado!</strong>');			
			}); 
	}

	ng.loadCartoes = function(){
		if(!empty(ng.num_cartao_fisico)){
			$('#modalVincularCartao button').button('loading');
			
			var query_string = "?id_empreendimento=" + ng.userLogged.id_empreendimento + "&num_comanda=" + ng.num_cartao_fisico;
			aj.get(baseUrlApi()+"cartoes-fisicos"+query_string)
				.success(function(data, status, headers, config) {
					aj.post(baseUrlApi()+"comanda/"+ ng.comandaSelecionada.comanda.id +"/vincular-cartao-fisico", {id_venda: ng.comandaSelecionada.comanda.id, id_cartao_fisico: data[0].id})
						.success(function(data, status, headers, config) {
							$('#modalVincularCartao button').button('reset');
							$('#modalVincularCartao').modal('hide');
							ng.abrirDetalhesComanda(ng.comandaSelecionada.comanda.id);
						})
						.error(function(data, status, headers, config) {
							ng.msg_erro_cartao = data;
							$('#modalVincularCartao button').button('reset');
						});
				})
				.error(function(data, status, headers, config) {
					ng.msg_erro_cartao = data;
					$('#modalVincularCartao button').button('reset');
				});
		}
		else {

		}
	}

	ng.openModalMesasTrocar = function(comanda) {
		ng.comanda_troca = comanda;
		$('#changeComandaMesa').modal('show');
	}

	ng.trocarComandaMesa = function(mesa) {
		aj.post(baseUrlApi() + "comandas/"+ ng.comanda_troca.id +"/trocar/mesa/"+ mesa.id_mesa)
			.success(function(data, status, headers, config) {
				ng.changeTela('mesas');
				$('#changeComandaMesa').modal('hide');
				var msg = {
					type 				: 'table_change',
					from 				: ng.id_ws_web,
					to_empreendimento 	: ng.userLogged.id_empreendimento,
					message 			: ""
				};
				ng.sendMessageWebSocket(msg);
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.openModalProdutos = function(){
		ng.busca.produtosModal = '' ;
		ng.loadProdutosModal();
		$('#list_produtos').modal('show');

		$('#list_produtos').on('shown.bs.modal', function () {
			$('input[ng-model="busca.produtosModal"]').focus();
		})
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

	ng.incluirItemComandaModal = function(item, event){
		dlg = $dialogs.confirm('Atenção!!!' ,'Confirma a inclusão deste item na comanda?');

		dlg.result.then(
			function(btn){
				if(item.flg_produto_composto === 1 && (!empty(ng.configuracao.flg_trabalha_delivery) && ng.configuracao.flg_trabalha_delivery == 1)){
					dlg = $dialogs.confirm('Atenção!!!' ,'Este ítem é para entrega?');

					dlg.result.then(
						function(btn){
							item.flg_delivery = true;
							incluirItemComandaModalAction(item);
						},
						function(){
							item.flg_delivery = false;
							incluirItemComandaModalAction(item);
						}
					);
				}
				else {
					item.flg_delivery = false;
					incluirItemComandaModalAction(item);
				}
			},
			function(){
				
			}
		);
	}

	function incluirItemComandaModalAction(item){
		var produto = angular.copy(item);
			produto.qtd = empty(produto.qtd) ? 1 : produto.qtd;
		
		if(ng.configuracao.flg_modo_selecao_produto == 'grade')
			ng.addItemPedido(produto);
		else {
			var btn = $(event.target);
			if(!btn.is(':button'))
				btn = $(event.target).parent();
			btn.button('loading');

			var post = {
				id_venda: 				ng.comandaSelecionada.comanda.id,
				id_usuario: 			ng.userLogged.id_usuario,
				id_produto: 			produto.id ,
				desconto_aplicado: 		0 ,
				valor_desconto: 		0 ,
				qtd: 					produto.qtd,
				observacoes: 			"",
				valor_real_item: 		round(produto.vlr_venda_varejo,2) ,
				vlr_custo: 				produto.vlr_custo_real,
				perc_imposto_compra: 	produto.perc_imposto_compra ,
				perc_desconto_compra: 	produto.perc_desconto_compra,
				perc_margem_aplicada: 	produto.perc_venda_varejo,
				id_empreendimento: 		ng.userLogged.id_empreendimento,
				id_deposito: 			ng.configuracao.id_deposito_padrao,
				flg_produto_composto: 	produto.flg_produto_composto,
				id_usuario: 			ng.userLogged.id,
				dta_create: 			moment().format('YYYY-MM-DD HH:mm:ss'),
				dta_lancamento: 		moment().format('YYYY-MM-DD HH:mm:ss'),
				id_mesa: 				ng.mesaSelecionada.mesa.id_mesa,
				flg_delivery: 			(produto.flg_delivery) ? 1 : 0,
				adicionais: 			ng.produto.adicionais_selecionados
			}

			aj.post(baseUrlApi()+"item_comanda/add/lista",post)
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
										flgDelivery: 			(!empty(data.ordem_producao.flg_delivery) 	    		? data.ordem_producao.flg_delivery 				: 0),
										adicionais: 			(!empty(ng.produto.adicionais) 							? ng.produto.adicionais 						: "")
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
						type: 'table_change',
						from: ng.id_ws_web,
						to_empreendimento: ng.userLogged.id_empreendimento,
						message : JSON.stringify(
							{
								index_mesa: ng.indexMesaSelecionada,
								mesa: data.mesa,
								id_comanda: ng.comandaSelecionada.comanda.id
							}
						)
					};
						
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
	}

	ng.addItemPedido = function(produto) {
		if(empty(ng.itens_pedido)) {
			ng.itens_pedido = [];
		}

		ng.itens_pedido.push(produto);

		ng.vlr_total_pedido += (produto.qtd * produto.vlr_venda_varejo);

		ng.bucaTipoProduto('categoria');
	}

	ng.limpaPedido = function() {
		ng.itens_pedido = null;
		ng.vlr_total_pedido = 0;
		ng.changeTela('detComanda', null, null);
	}

	ng.cancelarPedido = function() {
		if(empty(ng.itens_pedido) || ng.itens_pedido.length === 0)
			ng.limpaPedido();
		else {
			dlg = $dialogs.confirm('Atenção!!!' ,'Confirma o cancelamento do pedido?');

			dlg.result.then(
				function(btn){
					ng.limpaPedido();
				},
				function(){
					
				}
			);
		}
	}

	ng.confirmarPedido = function() {
		dlg = $dialogs.confirm('Atenção!!!' ,'Confirma o pedido?');

		dlg.result.then(
			function(btn){
				var itens = [];

				angular.forEach(ng.itens_pedido, function(produto, i) {
					itens.push({
						id_venda: 				ng.comandaSelecionada.comanda.id,
						id_usuario: 			ng.userLogged.id,
						id_produto: 			produto.id,
						observacoes: 			produto.observacoes,
						desconto_aplicado: 		0,
						valor_desconto: 		0,
						qtd: 					produto.qtd,
						valor_real_item: 		round(produto.vlr_venda_varejo, 2),
						vlr_custo: 				produto.vlr_custo_real,
						perc_imposto_compra: 	produto.perc_imposto_compra,
						perc_desconto_compra: 	produto.perc_desconto_compra,
						perc_margem_aplicada: 	produto.perc_venda_varejo,
						id_empreendimento: 		ng.userLogged.id_empreendimento,
						id_deposito: 			ng.configuracao.id_deposito_padrao,
						flg_produto_composto: 	produto.flg_produto_composto,
						dta_create: 			moment().format('YYYY-MM-DD HH:mm:ss'),
						dta_lancamento: 		moment().format('YYYY-MM-DD HH:mm:ss'),
						id_mesa: 				(!empty(ng.mesaSelecionada.mesa.id_mesa)) ? ng.mesaSelecionada.mesa.id_mesa : ng.comandaSelecionada.comanda.id_mesa,
						flg_delivery: 			(produto.flg_delivery) ? 1 : 0,
						adicionais: 			produto.adicionais_selecionados
					});
				});

				var categorias = _.groupBy(ng.itens_pedido, 'id_categoria');
				
				angular.forEach(categorias, function(itens, idx_cat) {
					angular.forEach(itens, function(item, idx_item){
						item.id_usuario 		= ng.userLogged.id;
						item.id_empreendimento 	= ng.userLogged.id_empreendimento;
						item.id_deposito 		= ng.configuracao.id_deposito_padrao;
						item.id_venda 			= ng.comandaSelecionada.comanda.id;
						item.id_mesa 			= ng.comandaSelecionada.comanda.id_mesa;
					});
				});

				aj.post(baseUrlApi()+"item_comanda/add/grade",{ itens: JSON.stringify(itens), categorias: JSON.stringify(categorias) })
					.success(function(data, status, headers, config) {
						var msg = {
							type: 'table_change',
							from: ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
							message: JSON.stringify({
								index_mesa: ng.indexMesaSelecionada,
								mesa: data.mesa,
								id_comanda: ng.comandaSelecionada.comanda.id
							})
						};
						ng.sendMessageWebSocket(msg);

						ng.abrirDetalhesComanda(ng.comandaSelecionada.comanda.id);
						ng.produto = {};
						ng.itens_pedido = null;
						ng.vlr_total_pedido = 0;
					})
					.error(function(data, status, headers, config) {
						if(status == 406)
							$dialogs.notify('Atenção!','<strong>Produto com estoque insuficiente</strong>');
						else
							$dialogs.notify('Atenção!','<strong>Erro ao incluir produto</strong>');
					});
			},
			function(){
				
			}
		);
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
		ng.loadProdutoAdicionais();
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
		dlg = $dialogs.confirm('Atenção!!!' ,'Confirma a inclusão deste item na comanda?');

		dlg.result.then(
			function(btn){
				if(ng.produto.flg_produto_composto === 1 && (!empty(ng.configuracao.flg_trabalha_delivery) && ng.configuracao.flg_trabalha_delivery == 1)){
					dlg = $dialogs.confirm('Atenção!!!' ,'Este ítem é para entrega?');

					dlg.result.then(
						function(btn){
							ng.produto.flg_delivery = true;
							incluirItemComandaAction();
						},
						function(){
							ng.produto.flg_delivery = false;
							incluirItemComandaAction();
						}
					);
				}
				else {
					ng.produto.flg_delivery = false;
					incluirItemComandaAction();
				}
			},
			function(){
				
			}
		);
	}

	function incluirItemComandaAction() {
		var produto = angular.copy(ng.produto);
			produto.qtd = (empty(produto.qtd)) ? 1 : produto.qtd;
		
		if(ng.configuracao.flg_modo_selecao_produto == 'grade')
			ng.addItemPedido(produto);
		else {
			var btn = $(event.target);
			if(!btn.is(':button'))
				btn = $(event.target).parent();
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
				flg_delivery: (ng.produto.flg_delivery) ? 1 : 0,
				adicionais: ng.produto.adicionais_selecionados
			};

			aj.post(baseUrlApi()+"item_comanda/add/lista",post)
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
										flgDelivery: 			(!empty(data.ordem_producao.flg_delivery) 	    		? data.ordem_producao.flg_delivery 				: 0),
										adicionais: 			(!empty(ng.produto.adicionais) 							? ng.produto.adicionais 						: "")
									})
								}
								ng.sendMessageWebSocket(msg);
							});
						}
						else {
							$dialogs.notify('Atenção!','<strong>Não foi possível enviar o pedido para impressão, pois não há nenhuma cozinha disponível!</strong>');
						}
						
						var msg = {
							type : 'op_new',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
							message : JSON.stringify(data.ordem_producao)
						}
						ng.sendMessageWebSocket(msg);

					}
					var msg = {
							type : 'table_change',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
							message : JSON.stringify({index_mesa:ng.indexMesaSelecionada,mesa:data.mesa,id_comanda:ng.comandaSelecionada.comanda.id})
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
					message : JSON.stringify({index_mesa:ng.indexMesaSelecionada,mesa:data.mesa,id_comanda:ng.comandaSelecionada.comanda.id})
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

	ng.verificaUsuario = function(){
		ng.usuario.id_empreendimento = ng.userLogged.id_empreendimento;
		ng.usuario.id_modulo = ng.id_modulo;
		ng.usuario.cod_funcionalidade = "excluir_item_comanda";
		aj.post(baseUrlApi()+"funcionalidade/autorizacao", { data: JSON.stringify( ng.usuario ) })
			.success(function(data, status, headers, config){
				if (ng.tipo_selecionado == 'item_comanda')
					ng.excluirItem(data.id_usuario);
				else if (ng.tipo_selecionado == 'comanda')
					ng.cancelarComanda(ng.id_comanda_selecionada);

			})
			.error(function(data, status, headers, config){
				ng.msg_autorizacao = data;
			})
	}

	ng.excluirItem = function(id_usuario){
		var post_data = {
			id_item 			: ng.produto.id_item_venda,
			id_mesa 			: (!empty(ng.mesaSelecionada.mesa.id_mesa)) ? ng.mesaSelecionada.mesa.id_mesa : ng.comandaSelecionada.comanda.id_mesa,
			id_usuario 			: id_usuario,
			id_empreendimento 	: ng.userLogged.id_empreendimento
		};

		aj.post(baseUrlApi()+"item_comanda/delete", {data: JSON.stringify(post_data)})
		.success(function(data, status, headers, config) {
			var msg = {
					type : 'table_change',from : ng.id_ws_web,to_empreendimento:ng.userLogged.id_empreendimento,
					message : JSON.stringify({index_mesa:ng.indexMesaSelecionada,mesa:data.mesa,id_comanda:ng.comandaSelecionada.comanda.id})
				}
			ng.sendMessageWebSocket(msg);
			ng.produto = {} ;
			ng.abrirDetalhesComanda(ng.comandaSelecionada.comanda.id);
			$('#autorizacao').modal('hide');
		})
		.error(function(data, status, headers, config) {
			$dialogs.notify('Atenção!','<strong>Erro ao excluir produto</strong>');
		});
	}

	ng.verificaChaveAutorizacao = function(id_comanda_selecionada, tipo){
		ng.msg_autorizacao = null;
		ng.tipo_selecionado = tipo;
		ng.id_comanda_selecionada = id_comanda_selecionada;
		if (ng.tipo_selecionado == 'item_comanda') {
			if (ng.configuracao.flg_autorizar_exclusao_sem_admin_controle_mesas == 0)
				$('#autorizacao').modal('show');
			else
				ng.excluirItem(ng.userLogged.id);

		} else if (ng.tipo_selecionado == 'comanda') {
			if (ng.configuracao.flg_autorizar_exclusao_sem_admin_controle_mesas == 0)
				$('#autorizacao').modal('show');
			else
				ng.cancelarComanda(ng.id_comanda_selecionada);
		}
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
		var patch_socket_sat = ng.configuracao.patch_socket_sat;

		if(location.protocol == "https:")
			patch_socket_sat = patch_socket_sat.replace('ws', 'wss');

		ng.conn = new WebSocket(patch_socket_sat);
		ng.conn.onopen = function(e) {
			
		};

		ng.conn.onclose = function(e) {

		}

		ng.conn.onmessage = function(e) {
			
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
						if(!empty(data.message)) {
							if(empty(data.message.index_mesa))
								data.message.index_mesa = getIndex('id_mesa',data.message.mesa.id_mesa,ng.mesas);
							ng.mesas[data.message.index_mesa] = data.message.mesa ;
							if(!(empty(ng.mesaSelecionada.mesa)) && ng.mesaSelecionada.mesa.id_mesa == data.message.mesa.id_mesa)
								ng.loadComandasByMesa();
							if(( !empty(data.message.id_comanda) && !empty(ng.comandaSelecionada.comanda)) && ng.comandaSelecionada.comanda.id == data.message.id_comanda) {
								if(data.message.closed)
									ng.changeTela('mesas');
								else
									ng.loadComanda(data.message.id_comanda);
							}
						}
						
					});
				break;
				case 'connection_test_response':
					clearTimeout(timeOutWaitingResponseTestConection);
					$scope.$apply(function () { ng.status_websocket = 2 ;});
					ng.id_ws_dsk = data.from;
					
				break;
			}			
		};
	}
	ng.sendMessageWebSocket = function(data){
		
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
						
					}, TimeWaitingResponseTestConection);
				});
			}

			enviaTesteConexao();
		},60000);
	}
	
	if(!empty(ng.configuracao.patch_socket_sat))
		ng.newConnWebSocket();

	ng.resizeScreen();
	ng.loadMesas();
	ng.loadCategorias();
	ng.loadFabricantes();
	ng.paginacaoProdutos();
});