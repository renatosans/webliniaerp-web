app.controller('ProdutosController', function($scope, $http, $window, $dialogs, UserService,FuncionalidadeService){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.produto 		= {
							id_tamanho : null,
							id_cor     : null,
							flg_produto_composto : 0,
							estoque:[],
							preco:{
										perc_venda_atacado:0,
										valor_venda_atacado:0,
										perc_venda_varejo:0,
										valor_venda_varejo:0,
										perc_venda_intermediario:0,
										valor_venda_intermediario:0,
										vlr_custo:0
									},
							precos:[]
						};

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

    ng.chosen_forma_aquisicao     = [{cod_controle_item_nfe:null,nme_item:'Selecione'}] ;
    ng.chosen_origem_mercadoria   = [{cod_controle_item_nfe:null,nme_item:'Selecione'}] ;
    ng.chosen_tipo_tributacao_ipi = [{cod_controle_item_nfe:null,nme_item:'Selecione'}] ;
    ng.chosen_especializacao_ncm  = [{cod_especializacao_ncm:null,dsc_especializacao_ncm:'Selecione'}] ;

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
				}else{
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
				$("select").trigger("chosen:updated");
			});
		}
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
		ng.insumos = []
		ng.produto 		= {
							id_tamanho : null,
							id_cor     : null,
							estoque:[],
							preco:{
										perc_venda_atacado:0,
										valor_venda_atacado:0,
										perc_venda_varejo:0,
										valor_venda_varejo:0,
										perc_venda_intermediario:0,
										valor_venda_intermediario:0,
										vlr_custo:0
									},
							fornecedores : [],
							precos:[{
									 	nome_empreendimento: ng.userLogged.nome_empreendimento,
										id_empreendimento: ng.userLogged.id_empreendimento,
										vlr_custo: 0,
										perc_imposto_compra: 0,
										perc_desconto_compra: 0,
										perc_venda_atacado: 0,
										perc_venda_intermediario: 0,
										perc_venda_varejo: 0
									}]
						};
		ng.editing = false;
		ng.empreendimentosAssociados = [{ id_empreendimento : ng.userLogged.id_empreendimento, nome_empreendimento : ng.userLogged.nome_empreendimento }];
		valor_campo_extra = angular.copy(ng.valor_campo_extra);
		ng.produto.valor_campo_extra = valor_campo_extra ;
		ng.produto.flg_produto_composto = 0 ;
		ng.produto_normal = 0 ;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
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

		if(ng.busca.produtos != ""){
			if(isNaN(Number(ng.busca.produtos)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produtos+"%' OR codigo_barra like '%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produtos+"%' OR codigo_barra like '%"+ng.busca.produtos+"%' OR fab.nome_fabricante like '%"+ng.busca.produtos+"%' OR pro.id = "+ng.busca.produtos+""}})+")";
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

	ng.loadInsumos = function(offset, limit) {
		ng.modal_insumos = [];

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento+"&pro->flg_produto_composto=0";

		query_string += ng.editing ? '&pro->id[exp]=<> '+ng.produto.id : '' ;

		if(ng.busca.insumos != ""){
			if(isNaN(Number(ng.busca.insumos)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.insumos+"%' OR codigo_barra like '%"+ng.busca.insumos+"%' OR fab.nome_fabricante like '%"+ng.busca.insumos+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.insumos+"%' OR codigo_barra like '%"+ng.busca.insumos+"%' OR fab.nome_fabricante like '%"+ng.busca.insumos+"%' OR pro.id = "+ng.busca.insumos+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.modal_insumos = data.produtos;
				ng.paginacao.modal_insumos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.modal_insumos = [];
					ng.paginacao.modal_insumos = null;
				}
			});
	}
	ng.insumos = [] ;
	ng.showInsumos = function(){
		$('#list_insumos').modal('show');
		ng.busca.insumos = "";
		ng.loadInsumos(0,10);
	}

	ng.addInsumo = function(item){
		var insumo = {id:item.id,nome:item.nome,qtd:(empty(item.qtd)? 1 : item.qtd ),vlr_custo_real:item.vlr_custo_real};
		ng.insumos.push(insumo);
		ng.calVlrCustoInsumos();
		item.qtd = null ;
		//$('#list_fornecedores').modal('hide');
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
		var r = false ;
		$.each(ng.insumos,function(i,x){
			if(Number(x.id) == Number(id_produto)){
				r = true ;
				return;
			}
		});
		return r ;
	}

	ng.delInsumo = function(index){
		console.log(index);
		ng.insumos.splice(index,1);
		ng.calVlrCustoInsumos();
	}

	
	ng.loadFabricantes = function(nome_fabricante) {
		ng.fabricantes = [{id:"",nome_fabricante:"--- Selecione ---"}];
		aj.get(baseUrlApi()+"fabricantes?id_empreendimento="+ng.userLogged.id_empreendimento)
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

	
	ng.loadImportadores = function(nome_importador) {
		ng.importadores = [{id:"",nome_importador:"--- Selecione ---"}];
		aj.get(baseUrlApi()+"importadores?id_empreendimento="+ng.userLogged.id_empreendimento)
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

	ng.loadCategorias = function(descricao_categoria) {
		ng.categorias = [{id:"",descricao_categoria:"--- Selecione ---"}];
		aj.get(baseUrlApi()+"categorias?id_empreendimento="+ng.userLogged.id_empreendimento)
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

		var produto = angular.copy(ng.produto) ;

		//console.log(produto);
		//return;

		/*if(produto.preco != undefined){
		    produto.preco = cloneArray(ng.produto.preco,['$$hashKey']) ;

			produto.valor_desconto_cliente         = produto.valor_desconto_cliente         /100 ;
			produto.preco.perc_desconto_compra     = produto.preco.perc_desconto_compra     / 100;
			produto.preco.perc_imposto_compra      = produto.preco.perc_imposto_compra      / 100;
			produto.preco.perc_venda_atacado       = produto.preco.perc_venda_atacado       / 100;
			produto.preco.perc_venda_intermediario = produto.preco.perc_venda_intermediario / 100;
			produto.preco.perc_venda_varejo        = produto.preco.perc_venda_varejo        / 100;
		}*/

		$.each(produto.precos,function(i,prc){
				produto.precos[i].valor_desconto_cliente     = prc.valor_desconto_cliente   /100;
				produto.precos[i].perc_venda_atacado       	= prc.perc_venda_atacado       / 100;
				produto.precos[i].perc_venda_intermediario 	= prc.perc_venda_intermediario / 100;
				produto.precos[i].perc_venda_varejo        	= prc.perc_venda_varejo        / 100;
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
		var dta_contagem = dia+"-"+mes+"-"+ano+" "+hora+":"+minutos+":"+segundos;
		$.each(estoques,function(i,itens){
			inventario={
					tipo                    : 'entrada',
					id_deposito 			: null,
					id_usuario_responsavel 	: ng.userLogged.id,
					dta_contagem 			: dta_contagem,
					itens                   : []               
				}

			$.each(itens,function(y,item){
				if(!(Number(item.qtd_item) == Number(item.qtd_ivn))){
					var qtd_ivn = Number(item.qtd_ivn);
					inventario.id_deposito = item.id_deposito;
					inventario.itens.push({
						id           : ng.produto.id_produto,
						dta_validade : item.dta_validade,
						qtd_ivn      : qtd_ivn
					});
				}
			});
			if(inventario.itens.length > 0)
				inventarios.push(inventario);
		});

		produto.inventario = inventarios ;
		//}

		if(!(ng.empreendimentosAssociados == null || ng.empreendimentosAssociados.length == undefined || ng.empreendimentosAssociados.length == 0)){
			produto.empreendimentos = angular.copy(ng.empreendimentosAssociados) ;
		}

		if(ng.editing){
			produto.del_empreendimentos = ng.del_empreendimentos ;
		}

		if(Number(produto.flg_produto_composto) == 1){
			produto.insumos = ng.insumos ;
		}
		$('#formProdutos').ajaxForm({
		 	url: baseUrlApi()+url,
		 	type: 'post',
		 	data:produto,
		 	success:function(data){
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
		ng.editing = true ;
		ng.produto = angular.copy(item);
		ng.produto.id_tamanho = ng.produto.id_tamanho === null ? 0 : Number(ng.produto.id_tamanho)  ;
		ng.produto.id_cor = ng.produto.id_cor === null ? 0 : Number(ng.produto.id_cor)  ;
		ng.produto.cod_especializacao_ncm = ng.produto.cod_especializacao_ncm === null ? "" : Number(ng.produto.cod_especializacao_ncm)  ; 
		ng.produto.ncm_view = item.cod_ncm+" - "+item.dsc_ncm ;
	
		ng.removeErrorEstoque();
		ng.del_empreendimentos = [] ;

		/*$('[ng-model="produto.preco.vlr_custo"]')			  	 .val(numberFormat(item.vlr_custo					   ,2,',','.'));
		$('[ng-model="produto.preco.perc_imposto_compra"]') 	 .val(numberFormat(item.perc_imposto_compra      * 100 ,2,',','.'));
		$('[ng-model="produto.preco.perc_desconto_compra"]')	 .val(numberFormat(item.perc_desconto_compra     * 100 ,2,',','.'));
		$('[ng-model="produto.preco.perc_venda_atacado"]')  	 .val(numberFormat(item.perc_venda_atacado       * 100 ,2,',','.'));
		$('[ng-model="produto.preco.perc_venda_varejo"]')        .val(numberFormat(item.perc_venda_varejo        * 100 ,2,',','.'));
		$('[ng-model="produto.preco.perc_venda_intermediario"]') .val(numberFormat(item.perc_venda_intermediario * 100 ,2,',','.'));
		$('[ng-model="produto.valor_desconto_cliente"]')         .val(numberFormat(item.valor_desconto_cliente   * 100 ,2,',','.'));*/

		ng.produto.precos = [] ;


		ng.empreendimentosByProduto(item.id_produto);
		ng.getEstoque(item.id_produto);
		//ng.calcularAllMargens();
		ng.loadProdutoInsumos();

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
		aj.get(baseUrlApi()+"produto/insumos/"+ng.produto.id)
			.success(function(data, status, headers, config) {
				ng.insumos        = data ;
			})
			.error(function(data, status, headers, config) {
				ng.insumos = [];
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
		console.log(index);
		ng.produto.fornecedores.splice(index,1);
	}

	ng.calcularAllMargens = function(preco){
		if(preco.vlr_custo == 0){
			preco.perc_venda_atacado = 0 ;
			preco.perc_venda_varejo = 0 ;
			preco.perc_venda_intermediario = 0 ;
		}
		ng.calculaMargens('atacado','margem',preco);
		ng.calculaMargens('varejo','margem',preco);
		ng.calculaMargens('intermediario','margem',preco);

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
		}if(tipo_perfil == "intermediario" && tipo_valor == "margem"){
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
		}
	}

	ng.getEstoque = function(id_produto) {
			var id_deposito_exists = ""  ;
			var depositos          = {} ;
			$http.get(baseUrlApi()+"estoque/?prd->id="+id_produto+"&emp->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
					depositos = data.produtos ;
					$.each(depositos,function(i,v){
						depositos[i].qtd_ivn   = v.qtd_item ;
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
					console.log(depositos);*/

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
	ng.existsDateEstoque = function(dta_validade,id_deposito){
		var exists = false ;
		$.each(ng.produto.estoque,function(i,x){
			if((dta_validade == x.dta_validade) && (id_deposito == x.id_deposito)){
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
			if(ng.existsDateEstoque(dta_validade,ng.inventario_novo.id_deposito)){
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
			if(!ng.existsDateEstoque(dta_validade,ng.inventario_novo.id_deposito)){
				$("#inventario_novo_qtd").addClass("has-error");
				var formControl = $('#inventario_novo_qtd')
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informa quantidade desejada')
					.attr("data-original-title", 'Informa quantidade desejada');
				formControl.tooltip();
			}
		}

		if(error > 0)
			return false;

		var item = {
			id_deposito   : ng.inventario_novo.id_deposito,
			nme_deposito  : ng.inventario_novo.nome_deposito,
			nome_deposito : ng.inventario_novo.nome_deposito,
			qtd_item      : 0,
			dta_validade  : dta_validade,
			qtd_ivn       : ng.inventario_novo.qtd_ivn
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

	ng.empreendimentosByProduto = function(id_produto){
		var error = 0 ;
		aj.get(baseUrlApi()+"empreendimentos/"+id_produto)
			.success(function(data, status, headers, config) {
				ng.empreendimentosAssociados = data ;
				var in_where = "";
				$.each(data,function(i,x){
					in_where = x.id_empreendimento+",";
				});
				in_where = in_where.substring(0,in_where.length-1);
				aj.get(baseUrlApi()+"produto/precos?cplSql=tp.id="+id_produto)
				.success(function(dataPrc, statusPrc) {
					$.each(dataPrc,function(i,x){
						dataPrc[i].vlr_custo =  numberFormat( ( empty(x.vlr_custo) ? 0  : x.vlr_custo  )					  ,2,'.','');
						dataPrc[i].perc_imposto_compra =  0 ;
						dataPrc[i].perc_desconto_compra =  0 ;
						dataPrc[i].perc_venda_atacado =  numberFormat( ( empty(x.perc_venda_atacado) ? 0  : x.perc_venda_atacado  )       * 100 ,2,'.','');
						dataPrc[i].perc_venda_varejo =  numberFormat( ( empty(x.perc_venda_varejo) ? 0  : x.perc_venda_varejo  )        * 100 ,2,'.','');
						dataPrc[i].perc_venda_intermediario =  numberFormat( ( empty(x.perc_venda_intermediario) ? 0  : x.perc_venda_intermediario  ) * 100 ,2,'.','');
						dataPrc[i].valor_desconto_cliente =  numberFormat( ( empty(x.valor_desconto_cliente) ? 0  : x.valor_desconto_cliente  )   * 100 ,2,'.','');
					});
					ng.produto.precos = dataPrc ;
					$.each(ng.produto.precos,function(i,x){
						ng.calcularAllMargens(x);
					});
					console.log(ng.produto);
				})
				.error(function(dataPrc, statusPrc) {
					console.log('Erro ao buscar os preços');
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
			perc_venda_varejo: 0
		});
		ng.empreendimentosAssociados.push(empreendimento);
		if(Number(ng.produto.flg_produto_composto) == 1){
			ng.calVlrCustoInsumos();
		}
	}

	ng.empreendimentoSelected = function(item){
		var saida = false ;
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
                    content: '<strong>loading ... </strong>',
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
			console.log(produto);
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
		//console.log(ng.tamanho);return;
		aj.post(baseUrlApi()+"tamanho",ng.tamanho)
		.success(function(data, status, headers, config) {
			btn.button('reset');
			ng.loadTamanhos(ng.tamanho.nome_tamanho);
			$('#modal-novo-tamanho').modal('hide');
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

		aj.post(baseUrlApi()+url, ng.fabricante)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.loadFabricantes(ng.fabricante.nome_fabricante);
				$('#modal-novo-fabricante').modal('hide');	
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
		//console.log(ng.cor_produto);return;
		aj.post(baseUrlApi()+"cor_produto",ng.cor_produto)
		.success(function(data, status, headers, config) {
			btn.button('reset');
			ng.loadCores(ng.cor_produto.nome_cor);
			$('#modal-nova-cor').modal('hide');
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

	ng.modal = function(acao,id){
		ng.fabricante.nome_fabricante = "";
		ng.importador.nome_importador = "";
		ng.categoria.descricao_categoria = "" ;
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");
		$("#"+id).modal(acao);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}
	ng.loadDepositos(0,10,true);
	ng.loadConfig();
	ng.loadFabricantes();
	ng.loadImportadores();
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
	
});
