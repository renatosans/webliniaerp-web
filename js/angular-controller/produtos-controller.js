app.controller('ProdutosController', function($scope, $timeout, $http, $window, $dialogs, ConfigService, UserService, FuncionalidadeService, PrestaShop, TabelaPrecoService,Ezcommerce){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.configuracoes 		= ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.ids_empreendimento_usuario = [] ;
	
	var $checkableTree ;
	var produtoTO = {
		id_tamanho : null,
		id_cor     : null,
		flg_produto_composto : 0,
		flg_controlar_lote: 0,
		flg_controlar_validade: 0,
		flg_materia_prima: 0,
		flg_venda_obrigatoria: 0,
		flg_unidade_fracao: 0,
		estoque:[],
		peso_frete : 0 ,
		largura_pacote : 0 ,
		altura_pacote : 0 ,
		profundidade_pacote : 0 ,
		preco:{
					perc_venda_atacado:0,
					valor_venda_atacado:0,
					perc_venda_varejo:0,
					valor_venda_varejo:0,
					perc_venda_intermediario:0,
					valor_venda_intermediario:0,
					perc_venda_intermediario_ii:0,
					valor_venda_intermediario_ii:0,
					vlr_custo:0
		},
		precos : [{
				 	nome_empreendimento: ng.userLogged.nome_empreendimento ,
					id_empreendimento: ng.userLogged.id_empreendimento,
					vlr_custo: 0,
					perc_imposto_compra: 0,
					perc_desconto_compra: 0,
					perc_venda_atacado: 0,
					perc_venda_intermediario: 0,
					perc_venda_intermediario_ii: 0,
					perc_venda_varejo: 0
		}],
		combinacoes : [],
		categorias : []
	};

	ng.produto 			= angular.copy(produtoTO)  ;
	ng.combinacoes 		= angular.copy(produtoTO)  ;
	ng.sublist_name = null;
	ng.campos_extras_produto  = [] ;
    ng.produtos		= null;
    ng.importadores	= [];
    ng.categorias	= [];
    ng.valor_tabela = "";
    ng.produto.fornecedores = [];
    ng.busca = {produtos:"",depositos:"",empreendimento:"",insumos:"",ncm:""};

    ng.editing = false;
    ng.paginacao = {};
    ng.depositos = [] ;
    ng.empreendimentosAssociados = [{ id_empreendimento : ng.userLogged.id_empreendimento, nome_empreendimento : ng.userLogged.nome_empreendimento }];

    ng.chosen_forma_aquisicao     = [{ cod_controle_item_nfe: null, nme_item : 'Selecione' }];
    ng.chosen_origem_mercadoria   = [{ cod_controle_item_nfe: null, nme_item : 'Selecione' }];
    ng.chosen_tipo_tributacao_ipi = [{ cod_controle_item_nfe: null, nme_item : 'Selecione' }];
    ng.chosen_especializacao_ncm  = [{ cod_especializacao_ncm: null, dsc_especializacao_ncm : 'Selecione' }];

    ng.existeTabelaPreco = function(nome_tabela){
			return TabelaPrecoService.existeTabelaPreco(ng.userLogged.id_empreendimento, nome_tabela);
		};

    ng.replicarCusto = function(preco){
    	var vlr_custo_linha = preco.vlr_custo;
    	$.each(ng.produto.precos, function(index,item){
    		ng.produto.precos[index].vlr_custo = vlr_custo_linha;
    		ng.calcularAllMargens(item);
    	});
    }

    ng.doExportExcel = function(){
    	window.location.href = baseUrlApi()+"produtos/export/"+ ng.userLogged.id_empreendimento;
    }

    ng.funcioalidadeAuthorized = function(cod_funcionalidade){
    	return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
    }

    ng.showBoxNovo = function(onlyShow){
    	if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show(400,function(){$("select").trigger("chosen:updated");});
		}
		else {
			ng.reset();
			$('#box-novo').toggle(400, function(){
				if($(this).is(':visible')){
					$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
					if(ng.depositos.length == 1) 
						ng.addDeposito(ng.depositos[0]);
				}else{
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
				$("select").trigger("chosen:updated");
			});
		}
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

	ng.isNumeric = function(n){
		return $.isNumeric(n) ;
	}

	ng.ClearChosenSelect = function(item){
		if(item == 'produto'){
			if(ng.produto.id_tamanho == '')
				ng.produto.id_tamanho = null;
		}
		else if(item == 'cor'){
			if(ng.produto.id_cor == '')
				ng.produto.id_cor = null;
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
		//ng.busca.produtos = '';
		$('#descricao_html').trumbowyg('html','');
		//$('#descricao_html_curta').trumbowyg('html','');
		ng.insumos = [] ;
		ng.adicionais = [] ;
		ng.produto 		= angular.copy(produtoTO)  ;
		ng.combinacoes 		= angular.copy(produtoTO)  ;
		ng.editing = false;
		ng.empreendimentosAssociados = [{ id_empreendimento : ng.userLogged.id_empreendimento, nome_empreendimento : ng.userLogged.nome_empreendimento }];
		valor_campo_extra = angular.copy(ng.valor_campo_extra);
		ng.produto.valor_campo_extra = valor_campo_extra ;
		ng.produto.flg_produto_composto = 0 ;
		ng.produto_normal = 0 ;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		var treeview = $('#treeview-modulos').treeview('getUnselected', null);
		$checkableTree.treeview('collapseAll', { silent: true });
		$.each(treeview,function(i,v){
				$checkableTree.treeview('uncheckNode', [v.nodeId, {silent: true}]);
		});
	}

	ng.resetFilter = function() {
		ng.busca.produtos = "" ;
		ng.reset();
		ng.loadProdutos(0,10);
	}

	var currentPaginacao = {} ;
	ng.loadProdutos = function(offset, limit) {
		ng.produtos = [];

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		currentPaginacao.offset = offset ;
		currentPaginacao.limit  = limit  ;

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;
		query_string += "&(tpc->id_combinacao[exp]=IS NULL OR (tpc.id_combinacao IS NOT NULL AND pro.id = tpc.id_produto ) )" ;

		if(ng.busca.produtos != ""){
			if(isNaN(Number(ng.busca.produtos)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produtos+"%' OR codigo_barra like '%"+ng.busca.produtos+"%' OR cat.descricao_categoria like '%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produtos+"%' OR codigo_barra like '%"+ng.busca.produtos+"%' OR cat.descricao_categoria like '%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%' OR pro.id = "+ng.busca.produtos+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos = data.produtos;
				ng.paginacao.itens = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.produtos = null;
					ng.paginacao.itens = null;
				}
			});
	}
	
	ng.fixBarcodeScanner = function(){}

	ng.loadInsumos = function(offset, limit) {
		ng.modal_insumos = [];

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

		query_string += ng.editing ? '&pro->id[exp]=<> '+ng.produto.id : '' ;

		if(ng.busca.insumos != ""){
			if(isNaN(Number(ng.busca.insumos)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.insumos+"%' OR codigo_barra like '%"+ng.busca.insumos+"%' OR fab.nome_fabricante like '%"+ng.busca.insumos+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.insumos+"%' OR codigo_barra like '%"+ng.busca.insumos+"%' OR fab.nome_fabricante like '%"+ng.busca.insumos+"%' OR pro.id = "+ng.busca.insumos+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+ query_string)
			.success(function(data, status, headers, config) {
				ng.modal_insumos = data.produtos;
				ng.paginacao.modal_insumos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.modal_insumos = null;
					ng.paginacao.modal_insumos = null;
				}
			});
	}
	ng.insumos = [] ;
	ng.showInsumos = function(sublist_name){
		$('#list_insumos').modal('show');
		ng.sublist_name = sublist_name;
		ng.busca.insumos = "";
		ng.loadInsumos(0,10);
	}

	ng.addInsumo = function(item){
		switch(ng.sublist_name) {
			case 'insumos':
				ng.insumos.push(_.extend(angular.copy(item), {qtd: (empty(item.qtd)) ? 1 : item.qtd}));
				ng.calVlrCustoInsumos();
				item.qtd = null;
				break;
			case 'adicionais':
				ng.adicionais.push(angular.copy(item));
				break;
		}
	}

	ng.calVlrCustoInsumos = function(){
		var insumos = angular.copy(ng.insumos); 
		var vlrCustoTotal = 0 ;
		var vlrCusto = 0;
		var qtd = 0 ;
		var totalVlrCustoInsumos = 0 ;
		$.each(insumos,function(i,v){
			vlrCusto = empty(v.vlr_custo_real) ? 0 : Number(v.vlr_custo_real) ;
			qtd      = empty(v.qtd) ? 1 : Number(v.qtd) ;
			totalVlrCustoInsumos += vlrCusto * qtd ;
		});
		totalVlrCustoInsumos ;
		$.each(ng.produto.precos,function(i,preco){
			ng.produto.precos[i].vlr_custo = totalVlrCustoInsumos ;
		});
	}

	ng.existsInsumo = function(id_produto){
		var r = false;
		$.each(ng[ng.sublist_name],function(i,x){
			if(Number(x.id) == Number(id_produto)){
				r = true ;
				return;
			}
		});
		return r;
	}

	ng.delInsumo = function(index){
		ng.insumos.splice(index,1);
		ng.calVlrCustoInsumos();
	}

	ng.delAdicional = function(index){
		ng.adicionais.splice(index,1);
	}
	
	ng.loadImportadores = function(nome_importador) {
		ng.importadores = [{id:"",nome_importador:"--- Selecione ---"}];
		aj.get(baseUrlApi()+"importadores?tie->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.importadores = ng.importadores.concat(data.importadores);
				if(nome_importador != null)
					ng.produto.id_importador = ng.getImportadorByName(nome_importador);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.importadores = [];
			});
	}

	ng.loadFabricantes = function(nome_fabricante) {
		ng.fabricantes = [{id:"",nome_fabricante:"--- Selecione ---"}];
		aj.get(baseUrlApi()+"fabricantes?tfe->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.fabricantes = ng.fabricantes.concat(data.fabricantes);
				if(nome_fabricante != null)
					ng.produto.id_fabricante = ng.getFabricanteByName(nome_fabricante);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.fabricantes = [];
			});
	}

	ng.loadCategorias = function(descricao_categoria) {
		ng.categorias = [{id:"",descricao_categoria:"--- Selecione ---"}];
		aj.get(baseUrlApi()+"categorias?tce->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.categorias = ng.categorias.concat(data.categorias);
				if(descricao_categoria != null)
					ng.produto.id_categoria = ng.getCategoriaByName(descricao_categoria);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.categorias = [];
			});
	}

	ng.tamanhos = [{id:'',nome_tamanho:'--- Selecione ---'}] ;

	ng.loadTamanhos = function(nome_tamanho) {
		ng.tamanhos = [{id:'',nome_tamanho:'--- Selecione ---'}] ;
		aj.get(baseUrlApi()+"tamanhos?tte->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.tamanhos = ng.tamanhos.concat(data);
				if(nome_tamanho != null)
					ng.produto.id_tamanho = ng.getTamanhoByName(nome_tamanho);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.tamanhos = [];
			});
	}

	ng.cores = [{id:'',nome_cor:'--- Selecione ---'}] ;

	ng.loadCores = function(nome_cor) {
		ng.cores = [{id:'',nome_cor:'--- Selecione ---'}] ;
		aj.get(baseUrlApi()+"cores_produto?tcpe->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.cores = ng.cores.concat(data);
				if(nome_cor != null)
					ng.produto.id_cor = ng.getCorByName(nome_cor);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.cores = [];
			});
	}

	ng.salvar = function(id_btn) {
		var btn = $('#'+id_btn);
   			btn.button('loading');

		var url = ng.editing ? 'produto/update' : 'produto';
		var msg = ng.editing ? 'Produto Atualizado com sucesso' : 'Produto salvo com sucesso!';

		$($(".has-error").find(".form-control")).tooltip('destroy');
		$($(".has-error").find("button")).tooltip('destroy');
		$(".has-error").removeClass("has-error");

		ng.produto.id_empreendimento = ng.userLogged.id_empreendimento;
		
		var produto = angular.copy(ng.produto);

		produto.descricao = $('#descricao_html').trumbowyg('html');
		//produto.descricao_curta = $('#descricao_html_curta').trumbowyg('html');	
		//return;

		/*if(produto.preco != undefined){
			produto.valor_desconto_cliente         = (produto.valor_desconto_cliente / 100);
		    
		    produto.preco = cloneArray(ng.produto.preco,['$$hashKey']);
			produto.preco.perc_desconto_compra     = (produto.preco.perc_desconto_compra / 100);
			produto.preco.perc_imposto_compra      = (produto.preco.perc_imposto_compra / 100);
			produto.preco.perc_venda_atacado       = (produto.preco.perc_venda_atacado / 100);
			produto.preco.perc_venda_intermediario = (produto.preco.perc_venda_intermediario / 100);
			produto.preco.perc_venda_varejo        = (produto.preco.perc_venda_varejo / 100);
		}*/

		$.each(produto.precos,function(i,prc){
			produto.precos[i].valor_desconto_cliente    = (prc.valor_desconto_cliente / 100);
			produto.precos[i].perc_venda_atacado       	= (prc.perc_venda_atacado / 100);
			produto.precos[i].perc_venda_intermediario 	= (prc.perc_venda_intermediario / 100);
			produto.precos[i].perc_venda_intermediario_ii 	= (prc.perc_venda_intermediario_ii / 100);
			produto.precos[i].perc_venda_varejo        	= (prc.perc_venda_varejo / 100);
		});

		//if(ng.editing){
		data = new Date();
		dia      = data.getDate();
		mes 	 = data.getMonth()+1;
		ano 	 = data.getFullYear();
		hora 	 = data.getHours();
		minutos  = data.getMinutes() < 10 ? "0"+data.getMinutes() : data.getMinutes() ;
		segundos = data.getSeconds() < 10 ? "0"+data.getSeconds() : data.getSeconds() ; 


		var inventario   = [] ;
		var inventarios  = [] ;
		var estoques     = _.groupBy(ng.produto.estoque, "nome_deposito");
		var dta_contagem = dia + "-" + mes + "-" + ano +" "+ hora +":"+ minutos +":"+ segundos;

		$.each(estoques, function(i,itens){
			inventario = {
				tipo: 					 	'entrada',
				id_deposito: 			 	null,
				id_usuario_responsavel: 	ng.userLogged.id,
				dta_contagem: 			 	dta_contagem,
				itens: 				 		[]
			};

			$.each(itens,function(y,item){
				if(!(Number(item.qtd_item) == Number(item.qtd_ivn))){
					var qtd_ivn = Number(item.qtd_ivn);
					inventario.id_deposito = item.id_deposito;
					inventario.itens.push({
						id           : item.id,
						id_produto   : item.id_produto,
						dta_validade : item.dta_validade,
						qtd_ivn      : qtd_ivn,
						lote         : item.lote
					});
				}
			});
			if(inventario.itens.length > 0)
				inventarios.push(inventario);
		});

		produto.inventario = inventarios;
		//}

		if(!(ng.empreendimentosAssociados == null || ng.empreendimentosAssociados.length == undefined || ng.empreendimentosAssociados.length == 0)){
			produto.empreendimentos = angular.copy(ng.empreendimentosAssociados) ;
		}

		if(ng.editing){
			produto.del_empreendimentos = ng.del_empreendimentos ;
		}

		if(Number(produto.flg_produto_composto) == 1){
			produto.insumos 	= ng.insumos;
			produto.adicionais 	= ng.adicionais;
		}

		$('#formProdutos').ajaxForm({
		 	url: baseUrlApi()+url,
		 	type: 'post',
		 	data: produto,
		 	success:function(data){
		 		produto.id= data.id ;
		 		produto.local_new_image = !empty(data.local_new_image) ?  data.local_new_image : null ; 

		 		if (empty(produto.fotos)) {
		 			produto.fotos = ng.produto.fotos;
		 		}

		 		angular.forEach(produto.fotos, function(item, index){
					item.id_produto = produto.id;
				});
		 		if (produto.fotos != "" || produto.fotos != null) {
			 		aj.post(baseUrlApi()+"produto/save/foto",{data: JSON.stringify(produto.fotos)})
						.success(function(data, status, headers, config) {
							$('#formProdutos')[0].reset();
					 		btn.button('reset');
					 		$('.upload-file label span').eq(0).attr('data-title','');
					 		$('.upload-file label span').eq(1).attr('data-title','');
					 		if(ng.editing)
					 			ng.loadProdutos(currentPaginacao.offset,currentPaginacao.limit);
					 		else
					 			ng.loadProdutos(0,10);
					 		ng.showBoxNovo();
					 		ng.mensagens('alert-success','<strong>'+msg+'</strong>');
					 		ng.produto = {fornecedores:[]} ;
					 		ng.insumos = [] ;
					 		
					 		ng.editing = false;
					 		btn.button('reset');
					 		ng.reset();
					 		$('html,body').animate({scrollTop: 0},'slow');
					 		
					 		PrestaShop.send('post',baseUrlApi()+"prestashop/produto/",produto);
					 		Ezcommerce.send('post',baseUrlApi()+"ezcommerce/catalogows/produto/"+ng.userLogged.id_empreendimento,produto);
						})
						.error(function(data, status, headers, config) {
							console.log('Erro ao subir fotos');
						});
		 		}
		 	},
		 	error:function(data){
		 		 btn.button('reset');
		 		if(data.status == 406){
		 			var count = 0 ;
		 			$.each(data.responseJSON, function(i, item) {
		 				if(count == 0){
		 					$('html,body').animate({scrollTop: $("#"+i).parents('.row').offset().top - 70},'slow');
		 				}
		 				count ++ ;
						$("#"+i).addClass("has-error");

						var formControl = $($("#"+i))
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});
		 		}
		 	}
		}).submit();
	}

	ng.editar = function(item) {
		ng.checando_categorias = true ;
		ng.editing = true ;
		ng.produto = angular.copy(item);

		ng.loadFotosProduto(ng.produto.id);

		var indexCombinacao = getIndex('id_combinacao',ng.produto.id,ng.produto.combinacoes);
		if(indexCombinacao==null){
			aj.post(baseUrlApi()+"combinacao/save_default",{id_produto:ng.produto.id})
			.success(function(data, status, headers, config) {
				aj.get(baseUrlApi()+"combinacoes/"+ng.produto.id_produto)
				.success(function(data, status, headers, config) {
					ng.produto.combinacoes = data ;
				})
				.error(function(data, status, headers, config) {
					ng.combinacoes = [] ;	
				});
			})
			.error(function(data, status, headers, config) {
				
			});
		}

		ng.produto.id_tamanho = ng.produto.id_tamanho === null ? 0 : Number(ng.produto.id_tamanho)  ;
		ng.produto.id_cor = ng.produto.id_cor === null ? 0 : Number(ng.produto.id_cor)  ;
		ng.produto.cod_especializacao_ncm = ng.produto.cod_especializacao_ncm === null ? "" : Number(ng.produto.cod_especializacao_ncm)  ; 
		ng.produto.ncm_view = item.cod_ncm+" - "+item.dsc_ncm ;
		if(!$.isNumeric(ng.produto.peso_frete)){
			ng.produto.peso_frete = 0 ;
		}

		if(!$.isNumeric(ng.produto.largura_pacote)){
			ng.produto.largura_pacote = 0 ;
		}

		if(!$.isNumeric(ng.produto.altura_pacote)){
			ng.produto.altura_pacote = 0 ;
		}

		if(!$.isNumeric(ng.produto.profundidade_pacote)){
			ng.produto.profundidade_pacote = 0 ;
		}

		if(typeof ng.produto.categorias != 'object'){
			 ng.produto.categorias = [] ;
		}else{
			//ng.checkedTreeview(ng.produto.categorias);
		}
		$('#descricao_html').trumbowyg('html',ng.produto.descricao);
		//$('#descricao_html_curta').trumbowyg('html',ng.produto.descricao_curta);

		/*if((typeof ng.produto.combinacoes == 'object') && ng.produto.combinacoes.length == 0){
			var combinacao = angular.copy(ng.produto);
			combinacao.id_combinacao = combinacao.id ;
			ng.produto.combinacoes.push(combinacao);
		}*/
	
		ng.removeErrorEstoque();
		ng.del_empreendimentos = [] ;

		ng.produto.precos = [] ;

		ng.empreendimentosByProduto(item.id_produto);
		ng.getEstoque(item.id_produto);
		//ng.calcularAllMargens();
		ng.loadProdutoInsumos();
		ng.loadProdutoAdicionais();

		valor_campo_extra = angular.copy(ng.valor_campo_extra);
		ng.produto.valor_campo_extra = valor_campo_extra ;
		ng.getValorCamposExtras(ng.produto);
		$('html,body').animate({scrollTop: 0 },'slow');




	ng.showBoxNovo(true);
}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este produto?</strong>');
		$('#confirmModal').parent('.modal').show();
		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"produto/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Produto excluido com sucesso</strong>');
					ng.reset();
					ng.loadProdutos();
					PrestaShop.send('delete',baseUrlApi()+"prestashop/produto/"+item.id+'/'+ng.userLogged.id_empreendimento);
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	/* inicio - Ações de Fornecedores */

	ng.fornecedores = [] ;

	ng.showFornecedores = function(){
		$('#list_fornecedores').modal('show');
		ng.busca.fornecedores = "";
		ng.loadFornecedores(0,10);
	}

	ng.loadFornecedores = function(offset,limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		var query_string = "?frn->id_empreendimento="+ng.userLogged.id_empreendimento+"&frn->id[exp]=!="+ng.configuracao.id_fornecedor_movimentacao_caixa ;
		if(ng.busca.fornecedores != ""){
			query_string += "&"+$.param({nome_fornecedor:{exp:"like'%"+ng.busca.fornecedores+"%'"}})+"";
		}

		ng.fornecedores = [];
		aj.get(baseUrlApi()+"fornecedores/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.fornecedores        = data.fornecedores ;
				ng.paginacao.fornecedores = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.fornecedores = [];
			});
	}

	ng.loadProdutoInsumos = function() {
		ng.insumos = [];
		aj.get(baseUrlApi()+"produto/get/insumos/"+ng.produto.id)
			.success(function(data, status, headers, config) {
				ng.insumos        = data ;
			})
			.error(function(data, status, headers, config) {
				ng.insumos = [];
			});
	}

	ng.loadProdutoAdicionais = function() {
		ng.adicionais = [];
		aj.get(baseUrlApi() +"produto/get/adicionais/"+ ng.produto.id)
			.success(function(data, status, headers, config) {
				ng.adicionais = data;
			})
			.error(function(data, status, headers, config) {
				ng.adicionais = [];
			});
	}

	ng.addFornecedor = function(item){
		var fornecedor = {id_fornecedor:item.id,nome_fornecedor:item.nome_fornecedor};
		if(ng.produto.fornecedores == null || ng.produto.fornecedores == false)
			ng.produto.fornecedores = [] ;
		ng.produto.fornecedores.push(fornecedor);
		//$('#list_fornecedores').modal('hide');
	}

	ng.delFornecedor = function(index){
		ng.produto.fornecedores.splice(index,1);
	}

	ng.calcularAllMargens = function(preco){
		if(preco.vlr_custo == 0){
			preco.perc_venda_atacado = 0 ;
			preco.perc_venda_varejo = 0 ;
			preco.perc_venda_intermediario = 0 ;
			preco.perc_venda_intermediario_ii = 0 ;
		}
		ng.calculaMargens('atacado','margem',preco);
		ng.calculaMargens('varejo','margem',preco);
		ng.calculaMargens('intermediario','margem',preco);
		ng.calculaMargens('intermediario_ii','margem',preco);

	}
	
	ng.calculaMargens = function(tipo_perfil,tipo_valor,preco){
		var vlr_custo 			= preco.vlr_custo;
		var imposto_compra 		= preco.perc_imposto_compra;
		var desconto_compra  	= preco.perc_desconto_compra;

		vlr_custo       = isNaN(Number(vlr_custo))	 ? 0 : vlr_custo;
		imposto_compra 	= isNaN(Number(imposto_compra))	 ? 0 : imposto_compra/100 ;
		desconto_compra = isNaN(Number(desconto_compra)) ? 0 : desconto_compra/100 ;

		valor_custo_real = (vlr_custo + (vlr_custo * imposto_compra));
		valor_custo_real = valor_custo_real - (valor_custo_real * desconto_compra);

		preco.valor_custo_real = valor_custo_real;

		if(tipo_perfil == "atacado" && tipo_valor == "margem"){
			var perc_venda_atacado = preco.perc_venda_atacado / 100;
			if(isNaN(Number(perc_venda_atacado)) || perc_venda_atacado == 0)
				preco.valor_venda_atacado = 0;
			else
				preco.valor_venda_atacado = valor_custo_real + (valor_custo_real*perc_venda_atacado) ;
		}else if(tipo_perfil == "atacado" && tipo_valor == "valor"){
			var valor_atacado = preco.valor_venda_atacado ;
			if(valor_atacado > valor_custo_real){
				var ex = (valor_custo_real - valor_atacado) * (-1);
				preco.perc_venda_atacado =(ex * 100)/valor_custo_real;
			}else
				preco.perc_venda_atacado = 0;
		}else if(tipo_perfil == "varejo" && tipo_valor == "margem"){
			var perc_venda_varejo = preco.perc_venda_varejo / 100;
			if(isNaN(Number(perc_venda_varejo)) || perc_venda_varejo == 0)
				preco.valor_venda_varejo = 0;
			else
				preco.valor_venda_varejo = valor_custo_real + (valor_custo_real*perc_venda_varejo) ;
		}else if(tipo_perfil == "varejo" && tipo_valor == "valor"){
			var valor_varejo = preco.valor_venda_varejo ;
			if(valor_varejo > valor_custo_real){
				var ex = (valor_custo_real - valor_varejo) * (-1);
				preco.perc_venda_varejo = (ex * 100)/valor_custo_real;
			}else
				preco.perc_venda_varejo = 0;
		}else if(tipo_perfil == "intermediario" && tipo_valor == "margem"){
			var perc_venda_intermediario = preco.perc_venda_intermediario / 100;
			if(isNaN(Number(perc_venda_intermediario)) || perc_venda_intermediario == 0)
				preco.valor_venda_intermediario = 0;
			else
				preco.valor_venda_intermediario = valor_custo_real + (valor_custo_real*perc_venda_intermediario) ;
		}else if(tipo_perfil == "intermediario" && tipo_valor == "valor"){
			var valor_intermediario = preco.valor_venda_intermediario ;
			if(valor_intermediario > valor_custo_real){
				var ex = (valor_custo_real - valor_intermediario) * (-1);
				preco.perc_venda_intermediario = (ex * 100)/valor_custo_real;
			}else
				preco.perc_venda_intermediario = 0;
		}else if(tipo_perfil == "intermediario_ii" && tipo_valor == "margem"){
			var perc_venda_intermediario_ii = preco.perc_venda_intermediario_ii / 100;
			if(isNaN(Number(perc_venda_intermediario_ii)) || perc_venda_intermediario_ii == 0)
				preco.valor_venda_intermediario_ii = 0;
			else
				preco.valor_venda_intermediario_ii = valor_custo_real + (valor_custo_real*perc_venda_intermediario_ii) ;
		}else if(tipo_perfil == "intermediario_ii" && tipo_valor == "valor"){
			var valor_intermediario_ii = preco.valor_venda_intermediario_ii ;
			if(valor_intermediario_ii > valor_custo_real){
				var ex = (valor_custo_real - valor_intermediario_ii) * (-1);
				preco.perc_venda_intermediario_ii = (ex * 100)/valor_custo_real;
			}else
				preco.perc_venda_intermediario_ii = 0;
		}
	}

	ng.getEstoque = function(id_produto) {
			var id_deposito_exists = ""  ;
			var depositos          = {} ;
			$http.get(baseUrlApi()+"estoque/?prd->id="+id_produto+"&emp->id_empreendimento="+ng.userLogged.id_empreendimento+"&get_combinacao=true")
			.success(function(data, status, headers, config) {
					depositos = data.produtos ;
					$.each(depositos,function(i,v){
						depositos[i].qtd_ivn   = v.qtd_item ;
						depositos[i].id = v.id_produto ;
					});

					ng.produto.estoque = depositos ;
					/*depositos  = _.groupBy(data.produtos, "nome_deposito");
					$.each(depositos,function(deposito,obj){
						var total_itens = 0 ;
						$.each(obj,function(i,x){
							total_itens += Number(x.qtd_item) ;
							if(i == 0){
								id_deposito_exists += ""+x.id_deposito+"," ;
								depositos[deposito].id_deposito = x.id_deposito ;	
							}
						});
						depositos[deposito].qtd_total = total_itens ;
						depositos[deposito].qtd_ivn   = total_itens ;
					});
					id_deposito_exists = id_deposito_exists.substring(0,(id_deposito_exists.length-1)) ;
					*/

					/*aj.get(baseUrlApi()+"depositos?id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+"&dep->id[exp]= NOT IN ("+id_deposito_exists+")")
						.success(function(data, status, headers, config) {
							$.each(data.depositos,function(i,x){
								depositos[x.nme_deposito] = []; 
								depositos[x.nme_deposito].id_deposito = x.id ;
								depositos[x.nme_deposito].qtd_total   = 0 ;
								depositos[x.nme_deposito].qtd_ivn     = 0 ;						 
							});	
							ng.produto.estoque = depositos ;
						})
						.error(function(data, status, headers, config) {
							if(status != 404)
								alert("ocorreu um erro");
							else{
								ng.produto.estoque = depositos ;
							}
								
						});*/
	        }).error(function(data, status) {
	        	if(status != 404)
	        		alert('Ocorreu um erro inesperado !');
	        	else{
	        		ng.produto.estoque = [] ;
	        		/*aj.get(baseUrlApi()+"depositos?id_empreendimento[exp]=="+ng.userLogged.id_empreendimento)
						.success(function(data, status, headers, config) {
							$.each(data.depositos,function(i,x){
								depositos[x.nme_deposito] = []; 
								depositos[x.nme_deposito].id_deposito = x.id ;
								depositos[x.nme_deposito].qtd_total   = 0 ;
								depositos[x.nme_deposito].qtd_ivn     = 0 ;						 
							});
							ng.produto.estoque = depositos ;	
						})
						.error(function(data, status, headers, config) {
							if(status != 404)
								alert("ocorreu um erro");
							else{
								ng.produto.estoque = depositos ;
							}
								
						});*/
	        	}
	   	    });
	}

	ng.modalCombinacao = function(){
		$('#modal-combinacao').modal('show');
		ng.loadCombinacoes(0,10);
	}

	ng.loadCombinacoes = function() {
    	aj.get(baseUrlApi()+"combinacoes/"+ng.produto.id_produto)
		.success(function(data, status, headers, config) {
			ng.combinacoes = data ;
			
		})
		.error(function(data, status, headers, config) {
			ng.combinacoes = [] ;	
		});
	}

	ng.modalDepositos = function(){
		$('#modal-depositos').modal('show');
		ng.loadDepositos(0,10);
	}

	ng.inventario_novo = {} ;
	
	ng.addDeposito = function(item){
		ng.inventario_novo.nome_deposito = item.nme_deposito;
		ng.inventario_novo.id_deposito   = item.id;
		$('#modal-depositos').modal('hide');
	}
	ng.existsDateEstoque = function(dta_validade,id_deposito,id){
		var exists = false ;
		if(empty(ng.produto.estoque)){
			ng.produto.estoque = [] ;
		}
		$.each(ng.produto.estoque,function(i,x){
			if( (dta_validade == x.dta_validade) && (id_deposito == x.id_deposito) && (id == x.id) ){
				exists = true ;
				return;
			}
		});
		return exists ;
	}
	ng.removeErrorEstoque = function(){
		$($(".painel-estoque").find('.has-error')).tooltip('destroy');
		$(".painel-estoque").find('.has-error').removeClass("has-error");
	}
	ng.addNovoInventario = function(){
		var error = 0 ;
		ng.removeErrorEstoque();
		if(empty(ng.inventario_novo.id_deposito)){
			error ++ ;
			$("#inventario_novo_deposito").addClass("has-error");
			var formControl = $('#inventario_novo_deposito')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'Informe o deposito')
				.attr("data-original-title", 'Informe o deposito');
			formControl.tooltip();
		}else{
			var dta_validade = empty(ng.inventario_novo.dta_validade) ? '2099-12-31' : formatDate(uiDateFormat(ng.inventario_novo.dta_validade,'99/99/999')) ;
			if(ng.existsDateEstoque(dta_validade,ng.inventario_novo.id_deposito,ng.inventario_novo.id)){
				 error ++ ;
				$("#inventario_novo_validade").addClass("has-error");
				var formControl = $('#inventario_novo_validade')
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Já existe está data de validade para o deposito selecionado')
					.attr("data-original-title", 'Já existe está data de validade para o deposito selecionado');
				formControl.tooltip();
			}
		}
		if(empty(ng.inventario_novo.qtd_ivn)){
			error ++ ;
			if(!ng.existsDateEstoque(dta_validade,ng.inventario_novo.id_deposito,ng.inventario_novo.id)){
				$("#inventario_novo_qtd").addClass("has-error");
				var formControl = $('#inventario_novo_qtd')
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informa quantidade desejada')
					.attr("data-original-title", 'Informa quantidade desejada');
				formControl.tooltip();
			}
		}

		if(empty(ng.inventario_novo.lote) && Number(ng.produto.flg_controlar_lote) == 1){
			error ++ ;
			if(!ng.existsDateEstoque(dta_validade,ng.inventario_novo.id_deposito,ng.inventario_novo.id)){
				$("#inventario_novo_lote").addClass("has-error");
				var formControl = $('#inventario_novo_lote')
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o lote')
					.attr("data-original-title", 'Informa o lote');
				formControl.tooltip();
			}
		}
		

		if(error > 0)
			return false;

		var id   = empty(ng.inventario_novo.id) ? ng.produto.id : ng.inventario_novo.id ;
		var sabor   = empty(ng.inventario_novo.id) ? ng.produto.sabor : ng.inventario_novo.sabor ;
		var peso = empty(ng.inventario_novo.id) ? ng.produto.peso : ng.inventario_novo.peso ;

		var item = {
			id   		  : ( empty(ng.inventario_novo.id) ? ng.produto.id :  ng.inventario_novo.id ),
			id_produto    : ( empty(ng.inventario_novo.id) ? ng.produto.id :  ng.inventario_novo.id ),
			peso       	  : peso,
			sabor         : sabor,
			id_deposito   : ng.inventario_novo.id_deposito,
			nme_deposito  : ng.inventario_novo.nome_deposito,
			nome_deposito : ng.inventario_novo.nome_deposito,
			qtd_item      : 0,
			dta_validade  : dta_validade,
			qtd_ivn       : ng.inventario_novo.qtd_ivn,
			flg_visivel   : 1 ,
			lote   		  : ( empty(ng.inventario_novo.lote) ? null :  ng.inventario_novo.lote ),
		}

		ng.produto.estoque.push(item);
		ng.inventario_novo = [] ;
	}

	ng.busca_vazia = {} ;
	ng.loadDepositos = function(offset, limit,loadPag) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.busca_vazia.depositos = false ;
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(!empty(ng.busca.depositos))
			query_string  += "&"+$.param({nme_deposito:{exp:"like '%"+ng.busca.depositos+"%'"}});

    	aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+query_string)
		.success(function(data, status, headers, config) {
			ng.depositos = data.depositos ;
			if(loadPag == true){
				if(ng.depositos.length == 1)
					ng.addDeposito(ng.depositos[0]);
			}
			ng.paginacao.depositos = data.paginacao ;
		})
		.error(function(data, status, headers, config) {
			if(status != 404)
				alert("ocorreu um erro");
			else{
				ng.paginacao.depositos = [] ;
				ng.depositos = [] ;	
				ng.busca_vazia.depositos = true ;
			}
				
		});
	}
	ng.configuracao = ConfigService.getConfig(ng.userLogged.id_empreendimento);

	ng.empreendimentosByProduto = function(id_produto){
		var error = 0 ;
		ids_empreendimento_usuario = [];
		$.each(ng.userLogged.empreendimento_usuario,function(i,v){ ids_empreendimento_usuario.push(v.id); });
		ids_empreendimento_usuario = ids_empreendimento_usuario.join();
		aj.get(baseUrlApi()+"empreendimentos/"+id_produto)
			.success(function(data, status, headers, config) {
				ng.empreendimentosAssociados = data ;
				var in_where = "";
				$.each(data,function(i,x){
					in_where = x.id_empreendimento+",";
				});
				in_where = in_where.substring(0,in_where.length-1);
				aj.get(baseUrlApi()+"produto/precos?cplSql=tp.id="+id_produto+" AND te.id IN("+ids_empreendimento_usuario+")")
				.success(function(dataPrc, statusPrc) {
					$.each(dataPrc,function(i,x){
						dataPrc[i].vlr_custo =  (empty(x.vlr_custo) ? 0  : x.vlr_custo);
						dataPrc[i].perc_imposto_compra =  0 ;
						dataPrc[i].perc_desconto_compra =  0 ;
						dataPrc[i].perc_venda_atacado = (empty(x.perc_venda_atacado) ? 0  : x.perc_venda_atacado) * 100;
						dataPrc[i].perc_venda_varejo = (empty(x.perc_venda_varejo) ? 0  : x.perc_venda_varejo) * 100;
						dataPrc[i].perc_venda_intermediario = (empty(x.perc_venda_intermediario) ? 0  : x.perc_venda_intermediario) * 100;
						dataPrc[i].perc_venda_intermediario_ii = (empty(x.perc_venda_intermediario_ii) ? 0  : x.perc_venda_intermediario_ii) * 100;
						dataPrc[i].valor_desconto_cliente = (empty(x.valor_desconto_cliente) ? 0  : x.valor_desconto_cliente) * 100;
					});
					ng.produto.precos = dataPrc ;
					$.each(ng.produto.precos,function(i,x){
						ng.calcularAllMargens(x);
					});
				})
				.error(function(dataPrc, statusPrc) {
					
				});
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.showEmpreendimentos = function() {
		$('#list_empreendimentos').modal('show');
		ng.loadAllEmpreendimentos(0,10);
	}

	ng.loadAllEmpreendimentos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?id_usuario="+ng.userLogged.id;
    	if(ng.busca.empreendimento != ""){
    		query_string = "&" +$.param({nome_empreendimento:{exp:"like'%"+ng.busca.empreendimento+"%'"}});
    	}

    	ng.empreendimentos = [];
		aj.get(baseUrlApi()+"empreendimentos/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.empreendimentos = data.empreendimentos;
				ng.paginacao.empreendimentos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.empreendimentos = [];
			});
	}
	ng.addEmpreendimento = function(item) {
		if(ng.empreendimentosAssociados == null)
			ng.empreendimentosAssociados = [];

		 var empreendimento = {
		 	id : null,
		 	id_empreendimento : item.id,
		 	nome_empreendimento : item.nome_empreendimento 
		 }

		ng.produto.precos.push({
		 	nome_empreendimento: item.nome_empreendimento,
			id_empreendimento: item.id,
			vlr_custo: 0,
			perc_imposto_compra: 0,
			perc_desconto_compra: 0,
			perc_venda_atacado: 0,
			perc_venda_intermediario: 0,
			perc_venda_intermediario_ii: 0,
			perc_venda_varejo: 0
		});
		ng.empreendimentosAssociados.push(empreendimento);
		if(Number(ng.produto.flg_produto_composto) == 1){
			ng.calVlrCustoInsumos();
		}
	}

	ng.addAllEmpreendimento = function(item) {
		var btn = $('#addAllEmpreendimentos');
   		btn.button('loading');
		aj.get(baseUrlApi()+"empreendimentos?id_usuario="+ng.userLogged.id)
			.success(function(data, status, headers, config) {
				if(empty(ng.empreendimentosAssociados))
					ng.empreendimentosAssociados = [];
				
				if(empty(ng.produto.precos))
					ng.produto.precos = [];
				
				$.each(data,function(i,item){
					if(empty(_.findWhere(ng.empreendimentosAssociados, {id_empreendimento: item.id}))) {
						var empreendimento = {
					 		id : null,
					 		id_empreendimento : item.id,
					 		nome_empreendimento : item.nome_empreendimento 
						};
						ng.empreendimentosAssociados.push(empreendimento);
						ng.produto.precos.push({
						 	nome_empreendimento: item.nome_empreendimento,
							id_empreendimento: item.id,
							vlr_custo: 0,
							perc_imposto_compra: 0,
							perc_desconto_compra: 0,
							perc_venda_atacado: 0,
							perc_venda_intermediario_ii: 0,
							perc_venda_varejo: 0
						});
						if(Number(ng.produto.flg_produto_composto) == 1){
							ng.calVlrCustoInsumos();
						}
					}
				});
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
			});
	}

	ng.empreendimentoSelected = function(item){
		var saida = false ;
		if(typeof item != 'object')
			return false ;

		$.each(ng.empreendimentosAssociados,function(i,v){
			if(Number(item.id) == Number(v.id_empreendimento)){
				saida = true ;
				return false ;
			}
		});
		return saida ;
	}
	ng.del_empreendimentos = [] ;
	ng.delEmpreendimento = function(index,item) {
		if(!isNaN(Number(item.id))){
			ng.del_empreendimentos.push(item);
		}
		ng.empreendimentosAssociados.splice(index,1);
	}

	ng.montaPopover = function(element,content){

	}

	ng.qtdDepostito = function(produto,event){
		if(Number(produto.qtd_item) < 1)
			return ;
		 $(event.target).popover({
                    title: 'Depositos',
                    placement: 'top',
                    content: '<strong>Aguarde, carregando...</strong>',
                    html: true,
                    container: 'body',
                    trigger  :'focus',
                }).popover('show');

		 aj.get(baseUrlApi()+"estoque/?prd->id="+produto.id_produto+"&emp->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				var depositos = {} ;
				$.each(data.produtos,function(i,v){
					if(depositos[v.nome_deposito] == undefined)
						depositos[v.nome_deposito] = {nome_deposito:v.nome_deposito,qtd:0};
					depositos[v.nome_deposito].qtd += Number(v.qtd_item); 
				});
				
				var tbl = '<table class="table table-bordered table-condensed table-striped table-hover">' ;
				$.each(depositos,function(i,v){
					tbl += '<tr>'+'<td>'+i+'</td>'+'<td class"text-center">'+v.qtd+'</td>'+'</tr>';
				});
				tbl += '</table>';
				 $(event.target).popover('destroy').popover({
	                    title: 'Depositos',
	                    placement: 'top',
	                    content: tbl,
	                    html: true,
	                    container: 'body',
	                    trigger  :'focus',
	                }).popover('show');
						
			})
			.error(function(data, status, headers, config) {
			
					
			});
	}
	ng.produto.flg_produto_composto = 0 ;
	ng.produto_normal = 0 ;
	ng.valor_campo_extra = {};
	ng.chosen_campo_extra = [{nome_campo:'',label:'--- Selecione ---'}];
	ng.getCamposExtras = function(){
		aj.get(baseUrlApi()+"campo_extra_prododuto_empreendimento?tcep->id_empreendimento="+ng.userLogged.id_empreendimento+"&cplSql=ORDER BY label")
		.success(function(data, status, headers, config) {
			ng.chosen_campo_extra = ng.chosen_campo_extra.concat(data);
			$.each(data,function(i,v){
				ng.campos_extras_produto.push(v);
				ng.valor_campo_extra[v.nome_campo] = 0 ;
			});
		})
		.error(function(data, status, headers, config) {
		
				
		});
	}

	ng.getValorCamposExtras = function(produto){
		aj.get(baseUrlApi()+"valor_campo_extra_produto?tcep->id_empreendimento="+ng.userLogged.id_empreendimento+"&tvcep->id_produto="+produto.id_produto)
		.success(function(data, status, headers, config) {
			$.each(data,function(i,v){
				produto.valor_campo_extra[i] = v.valor_campo;
				if(v.valor_campo == 1)
					produto.campo_extra_selected = i ;
			});
		})
		.error(function(data, status, headers, config) {
	
		});
	}

	ng.changeTipoProduto = function(campo,aux){
		if(aux == 'tipo'){
				ng.produto.flg_produto_composto = Number(ng.produto.flg_produto_composto) ;
		}
		
		if(aux == 'sub_tipo'){
			$.each(ng.produto.valor_campo_extra,function(i,v){
				if(i != campo)
					ng.produto.valor_campo_extra[i] = 0 ;
				else
					ng.produto.valor_campo_extra[i] = 1 ;
			});
		}
	}

	ng.showModalNovoTamanho = function(){
		$('#modal-novo-tamanho').modal('show');
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");
		ng.tamanho = {nome_tamanho:"",empreendimentos:[]} ;
	}
	
	ng.salvarTamanho = function(produto){
		var btn = $('#btn-salvar-tamanho');
   		btn.button('loading');
		ng.tamanho.empreendimentos = [] ;
		$.each(ng.empreendimentosAssociados,function(i,v){
			ng.tamanho.empreendimentos.push(v.id_empreendimento);
		});
		//return;
		aj.post(baseUrlApi()+"tamanho",ng.tamanho)
		.success(function(data, status, headers, config) {
			btn.button('reset');
			ng.loadTamanhos(ng.tamanho.nome_tamanho);
			$('#modal-novo-tamanho').modal('hide');
			post = angular.copy(ng.tamanho);
			post.id = data.id ;
			PrestaShop.send('post',baseUrlApi()+"prestashop/tamanho/",post);
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			if(status == 406){
				$.each(data, function(i, item) {
					$("#"+i).addClass("has-error");
					var formControl = $($("#"+i))
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", item)
						.attr("data-original-title", item);
					formControl.tooltip();
				});
			}
		});
	}

	ng.fabricante = {nome_fabricante:"",id_empreendimento:""} ;
	ng.salvarFabricante = function() {
		var url = 'fabricante';
		var btn = $('#btn-salvar-fabricante');
   		btn.button('loading');

		ng.fabricante.id_empreendimento = ng.userLogged.id_empreendimento;
		ng.fabricante.empreendimentos = [];

		$.each(ng.empreendimentosAssociados, function(i, item) {
			ng.fabricante.empreendimentos.push({id: item.id_empreendimento, nome_empreendimento: item.nome_empreendimento});
		});

		aj.post(baseUrlApi()+url, ng.fabricante)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.loadFabricantes(ng.fabricante.nome_fabricante);
				$('#modal-novo-fabricante').modal('hide');
				var itemPost = angular.copy(ng.fabricante);
				if(!empty(data.fabricante) && !empty(data.fabricante.id))
					itemPost.id = data.fabricante.id ;	
				PrestaShop.send('post',baseUrlApi()+"prestashop/fabricante",itemPost);
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
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
	ng.importador = {nome_importador:"",id_empreendimento:""} ;
	ng.salvarImportador = function() {
		var url = 'importador';
		var btn = $('#btn-salvar-importador');
   		btn.button('loading');
		ng.importador.id_empreendimento 	= ng.userLogged.id_empreendimento;
		ng.importador.empreendimentos = [];

		$.each(ng.empreendimentosAssociados, function(i, item) {
			ng.importador.empreendimentos.push({id: item.id_empreendimento, nome_empreendimento: item.nome_empreendimento});
		});

		aj.post(baseUrlApi()+url, ng.importador)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.loadImportadores(ng.importador.nome_importador);
				$('#modal-novo-importador').modal('hide');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
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

	ng.categoria = {descricao_categoria:"",id_empreendimento:""} ;
	ng.salvarCategoria = function() {
		var url = 'categoria';
		var btn = $('#btn-salvar-categoria');
   		btn.button('loading');
		ng.categoria.id_empreendimento 	= ng.userLogged.id_empreendimento;
		
		ng.categoria.empreendimentos = [];

		$.each(ng.empreendimentosAssociados, function(i, item) {
			ng.categoria.empreendimentos.push({id: item.id_empreendimento, nome_empreendimento: item.nome_empreendimento});
		});
		
		aj.post(baseUrlApi()+url, ng.categoria)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.loadCategorias(ng.categoria.descricao_categoria);
				$('#modal-nova-categoria').modal('hide');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
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

	ng.getTamanhoByName = function(nome_tamanho){
		var id_tamanho = {} ;
		$.each(ng.tamanhos,function(i,v){
			if(v.nome_tamanho == nome_tamanho){
				id_tamanho = v.id ;
			}
		});

		return id_tamanho ;
	}

	ng.getFabricanteByName = function(nome_fabricante){
		var id_fabricante = {} ;
		$.each(ng.fabricantes,function(i,v){
			if(v.nome_fabricante == nome_fabricante){
				id_fabricante = v.id ;
			}
		});

		return id_fabricante ;
	}

	ng.getImportadorByName = function(nome_importador){
		var id_importador = {} ;
		$.each(ng.importadores,function(i,v){
			if(v.nome_importador == nome_importador){
				id_importador = v.id ;
			}
		});

		return id_importador ;
	}

	ng.getCategoriaByName = function(descricao_categoria){
		var id_categoria = {} ;
		$.each(ng.categorias,function(i,v){
			if(v.descricao_categoria == descricao_categoria){
				id_categoria = v.id ;
			}
		});

		return id_categoria ;
	}

	ng.showModalNovaCor = function(){
		$('#modal-nova-cor').modal('show');
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");
		ng.cor_produto = {nome_cor:"",empreendimentos:[]} ;
	}

	ng.salvarCorProduto = function(produto){
		var btn = $('#btn-salvar-cor');
   		btn.button('loading');
		ng.cor_produto.empreendimentos = [] ;
		$.each(ng.empreendimentosAssociados,function(i,v){
			ng.cor_produto.empreendimentos.push(v.id_empreendimento);
		});
		//return;
		aj.post(baseUrlApi()+"cor_produto",ng.cor_produto)
		.success(function(data, status, headers, config) {
			btn.button('reset');
			ng.loadCores(ng.cor_produto.nome_cor);
			$('#modal-nova-cor').modal('hide');
			post = angular.copy(ng.cor_produto);
			post.id = data.id ;
			PrestaShop.send('post',baseUrlApi()+"prestashop/cor/",post);
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			if(status == 406){
				$.each(data, function(i, item) {
					$("#"+i).addClass("has-error");
					var formControl = $($("#"+i))
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", item)
						.attr("data-original-title", item);
					formControl.tooltip();
				});
			}
		});
	}

	ng.getCorByName = function(nome_cor){
		var id_cor = {} ;
		$.each(ng.cores,function(i,v){
			if(v.nome_cor == nome_cor){
				id_cor = v.id ;
			}
		});

		return id_cor ;
	}

	ng.selNcm = function(){
		$('#list-ncm').modal('show');
		ng.loadNcm(0,10);
	}

	ng.changeNcm = function(item){
		ng.produto.cod_ncm      = item.cod_ncm ;
		ng.produto.ncm_view 	= item.cod_ncm +" - "+item.dsc_ncm ;
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

	ng.cancelarModal = function(id){
		$('#'+id).modal('hide');
	}

	ng.loadControleNfe = function(ctr,key) {
		aj.get(baseUrlApi()+"nfe/controles/null/"+ctr)
			.success(function(data, status, headers, config) {
				ng[key] = ng[key].concat(data) ;
			})
			.error(function(data, status, headers, config) {
				
		});
	}

	ng.loadEspecialazacaoNcm = function() {
		aj.get(baseUrlApi()+"especializacao_ncm/get?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.chosen_especializacao_ncm = ng.chosen_especializacao_ncm.concat(data.especializacao_ncm) ;
			})
			.error(function(data, status, headers, config) {
				
		});
	}

	ng.loadRegraTributos = function() {
		ng.chosen_regra_tributos = [] ;
		aj.get(baseUrlApi()+"regra_tributos/?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.chosen_regra_tributos = [{cod_regra_tributos:null,dsc_regra_tributos:'Selecione'}] ;
				ng.chosen_regra_tributos = ng.chosen_regra_tributos.concat(data.regras) ;
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
				
		});
	}


	ng.loadModalCombinacoes = function(offset, limit) {
		ng.modal_combinacoes = [];

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento+"&pro->flg_produto_composto=0&(tt->id[exp]= IS NOT NULL OR tcp.id IS NOT NULL)";

		query_string += ng.editing ? '&pro->id[exp]=<> '+ng.produto.id : '' ;

		if(!empty(ng.busca.combinacoes)){
			if(isNaN(Number(ng.combinacoes)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.combinacoes+"%' OR codigo_barra like '%"+ng.combinacoes+"%' OR fab.nome_fabricante like '%"+ng.combinacoes+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.combinacoes+"%' OR codigo_barra like '%"+ng.combinacoes+"%' OR fab.nome_fabricante like '%"+ng.combinacoes+"%' OR pro.id = "+ng.combinacoes+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.modal_combinacoes = data.produtos;
				ng.paginacao.modal_combinacoes = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.modal_combinacoes = [];
					ng.paginacao.modal_combinacoes = null;
				}
			});
	}
	ng.insumos = [] ;
	ng.showModalCombinacoes = function(){
		$('#modal_combinacoes').modal('show');
		ng.busca.combinacoes = "";
		ng.loadModalCombinacoes(0,10);
	}

	ng.showModalAddCombinacoes = function(){
		indexEditCombinacao = null ;
		$('#modal-add-combinacao').modal('show');
		$('#modal-add-combinacao').on('shown.bs.modal', function (e) {
			$("select").trigger("chosen:updated");
		});
		ng.combinacao 	= angular.copy(produtoTO)  ;
		ng.combinacao.nome = ng.produto.nome ;
		ng.combinacao.precos = [] ;
		$.each(ng.produto.precos,function(i,item){
			ng.combinacao.precos.push({
			 	nome_empreendimento: item.nome_empreendimento,
				id_empreendimento: item.id_empreendimento,
				vlr_custo: 0,
				perc_imposto_compra: 0,
				perc_desconto_compra: 0,
				perc_venda_atacado: 0,
				perc_venda_intermediario_ii: 0,
				perc_venda_varejo: 0
			});
		});
	}

	ng.addCombinacao = function(item){
		if(typeof ng.produto.combinacoes != 'object' )
			ng.produto.combinacoes = [] ;
		if(ng.produto.combinacoes.length == 0){
			var itemProdutoDefault = angular.copy(ng.produto);
			itemProdutoDefault.id_combinacao = itemProdutoDefault.id_produto ;
			ng.produto.combinacoes.push(angular.copy(itemProdutoDefault));
		}
		item.id_combinacao = item.id_produto ;
		ng.produto.combinacoes.push(item);
	}

	ng.addCombinacaoDefault = function(item){
		if(typeof ng.produto.combinacoes != 'object' )
			ng.produto.combinacoes = [] ;
		if(ng.produto.combinacoes.length == 0){
			var itemProdutoDefault = angular.copy(ng.produto);
			itemProdutoDefault.id_combinacao = itemProdutoDefault.id_produto ;
			ng.produto.combinacoes.push(angular.copy(itemProdutoDefault));
		}
	}

	var indexEditCombinacao = null ;
	ng.ModalEditarCombinacao = function(item,$index){
		indexEditCombinacao = $index ;
		$('#modal-add-combinacao').modal('show');
		$('#modal-add-combinacao').on('shown.bs.modal', function (e) {
			$("select").trigger("chosen:updated");
		});
		ng.combinacao = item ;
		if($.isNumeric(item.id_combinacao) && typeof item.precos != 'object'){
			aj.get(baseUrlApi()+"produto/precos?cplSql=tp.id="+item.id_combinacao)
			.success(function(dataPrc, statusPrc) {
				$.each(dataPrc,function(i,x){
					dataPrc[i].vlr_custo =  numberFormat( ( empty(x.vlr_custo) ? 0  : x.vlr_custo  )					  ,2,'.','');
					dataPrc[i].perc_imposto_compra =  0 ;
					dataPrc[i].perc_desconto_compra =  0 ;
					dataPrc[i].perc_venda_atacado =  numberFormat( ( empty(x.perc_venda_atacado) ? 0  : x.perc_venda_atacado  )       * 100 ,2,'.','');
					dataPrc[i].perc_venda_varejo =  numberFormat( ( empty(x.perc_venda_varejo) ? 0  : x.perc_venda_varejo  )        * 100 ,2,'.','');
					dataPrc[i].perc_venda_intermediario =  numberFormat( ( empty(x.perc_venda_intermediario) ? 0  : x.perc_venda_intermediario  ) * 100 ,2,'.','');
					dataPrc[i].perc_venda_intermediario_ii =  numberFormat( ( empty(x.perc_venda_intermediario_ii) ? 0  : x.perc_venda_intermediario_ii  ) * 100 ,2,'.','');
					dataPrc[i].valor_desconto_cliente =  numberFormat( ( empty(x.valor_desconto_cliente) ? 0  : x.valor_desconto_cliente  )   * 100 ,2,'.','');
				});
				ng.combinacao.precos = dataPrc ;
				$.each(ng.combinacao.precos,function(i,x){
					ng.calcularAllMargens(x);
				});
			})
			.error(function(dataPrc, statusPrc) {
				
			});
		}
	}

	ng.existsCombinacao = function(id_produto){
		var r = false ;
		if(typeof ng.produto.combinacoes == 'object' ){
			$.each(ng.produto.combinacoes,function(i,x){
				if(Number(x.id) == Number(id_produto)){
					r = true ;
					return;
				}
			});
		}
		return r ;
	}

	ng.delCombinacao = function(index){
		ng.produto.combinacoes.splice(index,1);
	}

	ng.limpa_fp = function(){
		ng.produto.img = null;
	}

	ng.limpa_an = function(){
		ng.produto.nme_arquivo_nutricional = null;
	}

	ng.modal = function(acao,id){
		ng.fabricante.nome_fabricante = "";
		ng.importador.nome_importador = "";
		ng.categoria.descricao_categoria = "" ;
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");
		$("#"+id).modal(acao);
	}


	ng.subMenuConstruct = function(arrpai,arr){
		var menu = [] ;
		$.each(arr,function(key,value){
			if(arrpai.id == value.id_pai){
				var item = {
					id : value.id,
					id_pai : value.id_pai,
					data : {id:value.id.toString()},
					text : value.descricao_categoria,
					nodes : ng.subMenuConstruct(value,arr),
					icone : 'fa-tags'
				};	
				if(item.nodes.length == 0) delete item.nodes ;
				menu.push(item);	
			}
		});

		return menu ;
	}
	
	ng.menuConstruct = function(categorias){
		var menu = [] ;
		$.each(categorias,function(key,value){
			if(empty(value.id_pai)){
				var itens = ng.subMenuConstruct(value,categorias)
				if(itens.length > 0){
					menu.push({
						id : value.id,
						data : {id:value.id.toString()},
						text : value.descricao_categoria,
						nodes : ng.subMenuConstruct(value,categorias),
						icone : "fa-signal",
						selectable:false
					});	
				}else{
					menu.push({
						id : value.id,
						data : {id:value.id.toString()},
						text : value.descricao_categoria,
						icone : "fa-signal",
						selectable:false
					});			
				}
				
			}
		});

		return menu ;
	}


	ng.loadCategoriasTreeview = function() {
		aj.get(baseUrlApi()+"categorias?tce->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				menu = ng.menuConstruct(data.categorias);	
				ng.treeviewConstruct(menu);
			})
			.error(function(data, status, headers, config) {
				ng.treeviewConstruct([]);
			});
	}

	function treeviewCheckChildren(node){
		if(!empty(node.nodes && node.nodes.length > 0)){
			treeviewExpanded(node);
			$.each(node.nodes,function(i,v){
		        if(!v.state.checked){
			        $scope.$apply(function () {
			           ng.produto.categorias.push(v.id);
			        });
					$checkableTree.treeview('checkNode', [v.nodeId, {silent: true}]);
				}
				treeviewCheckChildren(v);
			});
		}
	}

	function treeviewExpanded(node){
		if(!node.state.expanded)
			$('#treeview-modulos').treeview('toggleNodeExpanded', [ node.nodeId, { silent: true } ]);
	}

	function treeviewCollapsing (node){
		if(node.state.expanded)
			$('#treeview-modulos').treeview('toggleNodeExpanded', [ node.nodeId, { silent: true } ]);
	}

	function treeviewUnCheckChildren(node){
		if(!empty(node.nodes && node.nodes.length > 0)){
			$.each(node.nodes,function(i,v){
		        if(v.state.checked){
		        	var index = ng.produto.categorias.indexOf(v.id);
		            $scope.$apply(function () {
		           	   ng.produto.categorias.splice(index,1);
		        	});
					$checkableTree.treeview('uncheckNode', [v.nodeId, {silent: true}]);
				}
				treeviewUnCheckChildren(v);
			});
		}
	}

	function checkPai(node){
		var parent = $checkableTree.treeview('getParent', node);
		if(!empty(parent.state)){
			if(!parent.state.checked){
				 $scope.$apply(function () {
		           ng.produto.categorias.push(parent.id);
		        });
				$checkableTree.treeview('checkNode', [parent.nodeId, {silent: true}]);
			}
		}
	}
	ng.treeviewConstruct = function(data){
			$checkableTree = $('#treeview-modulos').treeview({
	          data: data,
	          showIcon: false,
	          expandIcon: 'glyphicon glyphicon-chevron-right',
	          collapseIcon: 'glyphicon glyphicon-chevron-down',
	          showCheckbox: true,
	          showBorder: false,
	          selectedBackColor: "white",
	          selectedColor: "#777",
	          onhoverColor:false,
	          onNodeChecked: function(event, node) {
	          	$scope.$apply(function () {
		           ng.produto.categorias.push(node.id);
		        });	
		        checkPai(node);
		        treeviewCheckChildren(node);
	          },
	          onNodeUnchecked: function (event, node) {
	            var index = ng.produto.categorias.indexOf(node.id);
	            $scope.$apply(function () {
	           	   ng.produto.categorias.splice(index,1);
	        	});
	        	treeviewUnCheckChildren(node);
	          },
	        }).treeview('collapseAll', { silent: true });
	        var a =$checkableTree.treeview('search',
            [
              4,
              'data.cod_modulo',
              {
                ignoreCase: true,
                exactMatch: true,
                revealResults: false
              }
            ]
          );
	}

	ng.checkedTreeview = function(categorias){
		var treeview = $('#treeview-modulos').treeview('getUnselected', null);
		$.each(treeview,function(i,v){
			if(_in(v.id,categorias)){
				$checkableTree.treeview('checkNode', [v.nodeId, {silent: true}]);
				treeviewExpanded(v);
		        ng.showBoxNovo(true);
			}else{
				$checkableTree.treeview('uncheckNode', [v.nodeId, {silent: true}]);
				treeviewCollapsing(v);
			}
		});
	}

	ng.incluirCombinacao = function(){
		$('#modal-add-combinacao').modal('hide');
		if(!empty(ng.combinacao.id_tamanho)){
			var indexTamanho = getIndex('id',ng.combinacao.id_tamanho,ng.tamanhos);
			if(!empty(indexTamanho)){
				ng.combinacao.peso = ng.tamanhos[indexTamanho].nome_tamanho ;
			}
		}

		if(!empty(ng.combinacao.id_cor)){
			var indexCor = getIndex('id',ng.combinacao.id_cor,ng.cores);
			if(!empty(indexCor)){
				ng.combinacao.sabor = ng.cores[indexCor].nome_cor ;
			}
		}

		if(ng.produto.combinacoes.length == 0){
			var itemProdutoDefault = angular.copy(ng.produto);
			itemProdutoDefault.id_combinacao = itemProdutoDefault.id_produto ;
			ng.produto.combinacoes.push(angular.copy(itemProdutoDefault));
		}
		
		if($.isNumeric(indexEditCombinacao)){
			var post = {
				produto_combinacao : angular.copy(ng.combinacao),
				id_empreendimento : ng.userLogged.id_empreendimento,
				id_produto : ng.produto.id 
			};
			$.noty.closeAll();
			var i = exibirNoty("<i class='fa fa-refresh fa-spin'></i> Atualizando combinação",'information');
			aj.post(baseUrlApi()+"combinacao/update",post)
			.success(function(data, status, headers, config) {
				$.noty.close(i.options.id) ;
				var x = exibirNoty("<i class='fa fa-check-circle-o' aria-hidden='true'></i> Conbinação atualizada com sucesso",'success');
				$('#modal-add-combinacao').modal('hide');
				aj.get(baseUrlApi()+"combinacoes/"+ng.produto.id_produto)
				.success(function(data, status, headers, config) {
					ng.produto.combinacoes = data ;
				})
				.error(function(data, status, headers, config) {
					ng.combinacoes = [] ;	
				});
			})
			.error(function(data, status, headers, config) {
				$.noty.close(i.options.id) ;
				var x = exibirNoty('Erro ao atualizar combinação','error');
			});
			//ng.produto.combinacoes[indexEditCombinacao] = angular.copy(ng.combinacao) ;
		}
		else{
			var post = {
				produto_combinacao : angular.copy(ng.combinacao),
				id_empreendimento : ng.userLogged.id_empreendimento,
				id_produto : ng.produto.id 
			};
			$.noty.closeAll();
			var i = exibirNoty("<i class='fa fa-refresh fa-spin'></i> Salvando combinação",'information');
			aj.post(baseUrlApi()+"combinacao/save",post)
			.success(function(data, status, headers, config) {
				$.noty.close(i.options.id) ;
				var x = exibirNoty("<i class='fa fa-check-circle-o' aria-hidden='true'></i> Conbinação adicionada com sucesso",'success');
				$('#modal-add-combinacao').modal('hide');
				aj.get(baseUrlApi()+"combinacoes/"+ng.produto.id_produto)
				.success(function(data, status, headers, config) {
					ng.produto.combinacoes = data ;
				})
				.error(function(data, status, headers, config) {
					ng.combinacoes = [] ;	
				});
			})
			.error(function(data, status, headers, config) {
				$.noty.close(i.options.id) ;
				var x = exibirNoty('Erro ao adicionar combinação','error');
			});
			//;
			//ng.produto.combinacoes.push(angular.copy(ng.combinacao)) ;
		}
		ng.combinacao = angular.copy(produtoTO);
		indexEditCombinacao = null ;
	}

	ng.addCombinacaoEstoque = function(item){
		ng.inventario_novo.id = item.id ;
		ng.inventario_novo.peso = item.peso ;
		ng.inventario_novo.sabor = item.sabor ;
		ng.inventario_novo.dsc_combinacao = '#'+item.id+" - "+item.peso+" "+item.sabor ;
		$('#modal-combinacao').modal('hide');
	}

	ng.incluirCombinacaoDefault = function(){

	}

	ng.loadCategoriasTreeview();

	ng.timeCheckedTreeview = function(){
		$timeout( function(){ 
			ng.checkedTreeview(ng.produto.categorias);
			 $scope.$apply(function () {
	          ng.checando_categorias = false ;
	        });
		 }, 3000);
	}

	setTimeout(function(){
	  $('.btn-upload input[type="file"]').on('change', function(){
		var file = this.files[0]; // get selected file
		var reader = new FileReader();

		ng.fileModel = $(this).data().model; // get attribute model name

		if(empty(ng.produto)){
			ng.produto = {};
		}
		
		if(empty(ng.produto.fotos)){
			ng.produto.fotos = [];		
		}

		if(empty(ng.produto[ng.fileModel])) // validate if is empty
			ng.produto[ng.fileModel] = {}; // create as object

		// detect file type
		var type = file.type.substring(file.type.indexOf('/')+1, file.type.length);
		if(empty(type)){
			type = file.name.substring((file.name.lastIndexOf('.')+1), file.name.length);
		}
		
		ng.produto[ng.fileModel].name = file.name; // file name
		ng.produto[ng.fileModel].type = type; // file type
		ng.produto[ng.fileModel].size = (file.size / 1024); // file size

		// adjust file size string name
		if(ng.produto[ng.fileModel].size < 1024)
			ng.produto[ng.fileModel].size = numberFormat(ng.produto[ng.fileModel].size, 2, ',', '.') + ' KB';
		else if(ng.produto[ng.fileModel].size > 1024)
			ng.produto[ng.fileModel].size = numberFormat(ng.produto[ng.fileModel].size, 2, ',', '.') + ' MB';

		// after loading file...
		reader.onload = function (e) {
			ng.produto[ng.fileModel].path = e.target.result; // get base64 string of file
			ng.produto[ng.fileModel].flg_excluir = false;
			ng.produto.fotos.push(ng.produto[ng.fileModel]);
			ng.produto[ng.fileModel] = null;
		  	setTimeout(function(){
				ng.$apply(); // apply changes in the screen
			},1);
		}

		if(!empty(file))
			reader.readAsDataURL(file);
		});
	}, 10);

	ng.loadFotosProduto = function(id_produto){
		aj.get(baseUrlApi()+"produto/fotos/"+id_produto)
			.success(function(data, status, headers, config) {
				ng.produto.fotos = data;
				angular.forEach(ng.produto.fotos, function(item, index){
					item.path_nova = item.path.substring(item.path.indexOf('assets'), item.path.length);
					item.flg_excluir = false;
				});
			})
			.error(function(data, status, headers, config) {
				console.log('erro ao carregar fotos');
			});
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}
	ng.loadDepositos(0,10,true);
	ng.loadImportadores();
	ng.loadFabricantes();
	ng.loadCategorias();
	ng.loadTamanhos();
	ng.loadCores();
	ng.loadProdutos(0,10);
	ng.getCamposExtras();
	ng.loadControleNfe('forma_aquisicao','chosen_forma_aquisicao');
	ng.loadControleNfe('origem_mercadoria','chosen_origem_mercadoria');
	ng.loadControleNfe('tipo_tributacao_ipi','chosen_tipo_tributacao_ipi');
	ng.loadEspecialazacaoNcm();
	ng.loadRegraTributos();
	ng.loadPlanoContas();
});
