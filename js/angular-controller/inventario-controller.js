app.controller('InventarioController', function($scope, $http, $window, $dialogs, UserService, PrestaShop){

	var ng = $scope
		aj = $http;

	ng.baseUrl 			   = baseUrl();
	ng.userLogged 		   = UserService.getUserLogado();
	ng.entradaEstoque 	   = {};
	ng.utimosInventarios   = [] ;
	ng.detalhes            = [];
	ng.busca_cod_barra     = false ;

    ng.editing = false;

    ng.paginacao           = {} ;
    ng.busca               = {fornecedores:"",pedidos:""} ;

    ng.produto = {};
    ng.itemValidade = {} ;

     /* inicio - Ações de inventario */
     ng.inventario = {id_usuario_responsavel:ng.userLogged.id,nome_usuario:ng.userLogged.nme_usuario,qtd_total:0,itens:[]} ;

     $("#stock-file").change(function() {
		var filename = $(this).val().split('\\').pop();
		$(this).parent().find('span').attr('data-title',filename);
		$(this).parent().find('label').attr('data-title','Trocar CSV');
		$(this).parent().find('label').addClass('selected');
	});

     ng.loadDataFromCSV = function() {
		ng.inventario.itens = [] ; 
		$('#form-csv').ajaxForm({
		 	url: baseUrlApi()+"inventario/importar/csv",
		 	type: 'POST',
		 	data: {
		 		id_empreendimento: ng.userLogged.id_empreendimento
		 	},
		 	beforeSend: function() {
		 		$("#loadDataFromCSV").button('loading');
		 		$("#csv_estoque").removeClass("has-error");
		 		$("#csv_estoque").tooltip('destroy');
		 	},
		 	success:function(data){
		 		ng.inventario.itens = data;
		 		ng.atualizaQtdTotal();

				setTimeout(function() {
					$scope.$apply();
				}, 500);

		 		$("#loadDataFromCSV").button('reset');
		 	},
		 	error: function(data, status, headers, config){
		 		$("#loadXMLButton").button('reset');
		 		
		 		if(data.status == 406) {
		 			$("#csv_estoque").addClass("has-error");

					var formControl = $($("#csv_estoque"))
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", data.responseText)
						.attr("data-original-title", data.responseText);
					formControl.tooltip();
		 		}
		 		else {
		 			defaulErrorHandler(data, status, headers, config);
		 		}
		 	}
		}).submit();
	}

     ng.validadeAllValidades = function(){
     	var validade = true;
     	$.each(ng.inventario.itens,function(i,item){
			if(item.validades == null){
				validade = false;
			}
		});

		return validade;
     }

    ng.salvar  = function(){
    	$("#btCancelar").hide();
    	$("#btSalvar").button("loading");

     	ng.inventario.dta_contagem = $('#inventarioData').val();

     	if(ng.inventario.itens.length <= 0){
     		$("#btCancelar").show();
        	$("#btSalvar").button("reset");
     		$dialogs.notify('Atenção!','Nenhum produto foi adicionado');
   			return ;
     	}

     	if(!ng.validadeAllValidades()){
     		$("#btCancelar").show();
        	$("#btSalvar").button("reset");
     		$dialogs.notify('Atenção!','Não foi informado Quantidade para todos os produtos');
   			return ;
     	}

	    var itens = [] ;
	    var postPrestaShop = {produtos:[],id_empreendimento:ng.userLogged.id_empreendimento} ;
		$.each(ng.inventario.itens,function(i,item){
			$.each(item.validades,function(x,validade){
				var item_atual = {} ;

				item_atual = cloneArray(item,['$$hashKey','validades']);

				var ano       = parseInt(validade.validade.substring(2,6));
				var mes       = parseInt(validade.validade.substring(0,2)) -1;
				var objDate   = new Date(ano, mes , 1);

				item_atual.dta_validade = ano+'-'+(mes+1)+'-'+ultimoDiaDoMes(objDate);
				item_atual.qtd_ivn      = validade.qtd;

				itens.push(item_atual);

				postPrestaShop.produtos.push(item.id_produto);
			});
		});

	 	var inventario       = cloneArray(ng.inventario,['$$hashKey','validades','itens']);
	 	inventario['itens']  = itens ;
	 	inventario.id_empreendimento = ng.userLogged.id_empreendimento ;

	 	$http.post(baseUrlApi()+'inventario',inventario)
			.success(function(data, status, headers, config) {
				$("#btCancelar").show();
				$("#btSalvar").button("reset");
				ng.showBoxNovo();
	        	ng.mensagens('alert-success','<strong>Inventario cadastrada com sucesso</strong>');
	        	ng.loadUltimosInventarios(0,30);
	        	PrestaShop.send('post',baseUrlApi()+"prestashop/estoque",postPrestaShop);
	        }).error(function(data, status) {
	        	$("#btCancelar").show();
	        	$("#btSalvar").button("reset");

	        	if(status == 406) {
	            	$.each(data, function(i, item) {
	            		$("#"+i).addClass("has-error");
						var formControl = $($("#"+i))
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});
	           	}else{
	        		alert('Desculpe, ocorreu um erro inesperado!');
	        	}
	   	    });
     }

     ng.validarDataValidade = function(mes_ano,index) {
		var mes = parseInt(mes_ano.substr(0,2),10);
		var ano = parseInt(mes_ano.substr(2,4),10);

		var valid = true;

		var dtaAtual = new Date();
		var mesAtual = dtaAtual.getMonth() + 1;
		var anoAtual = dtaAtual.getFullYear();

		if(mes > 12)
			valid = false;

		if(ano < anoAtual)
			valid = false;

		if(ano < 1900 || ano > 2100)
			valid = false;

		if(!valid) {
			ng.mensagens('alert-danger','<strong>A data informada é inválida</strong>','.alert-itens');
			ng.itemValidade.validade = "";
		}
	}

	ng.showValidades = function(item) {
		ng.produto = item;

		if(ng.produto.validades == null || typeof(ng.produto.validades) == "undefined")
			ng.produto.validades = [];

		$("#list_validades").modal('show');
	}

	ng.addValidadeItem = function() {
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		var error = 0 ;
		var item = angular.copy(ng.itemValidade);
		if(empty(ng.itemValidade.validade))
			item.validade = '122099';

		if(typeof ng.produto.validades == 'object'){
			$.each(ng.produto.validades,function(i,x){
				if(x.validade == item.validade){
					$("#item-validade-add").addClass("has-error");
					var formControl = $("#item-validade-add")
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'Validade ja inserida')
						.attr("data-original-title", 'Validade ja inserida');
					formControl.tooltip();
					if(error == 0) formControl.tooltip('show');
					error ++ ;
					return ;
				}
			});
		}

		if(empty(ng.itemValidade.qtd) && Number(ng.itemValidade.qtd) != 0 ){
			$("#item-qtd-add").addClass("has-error");
			var formControl = $("#item-qtd-add")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'A Quantidade é Obrigatória')
				.attr("data-original-title", 'A Quantidade é Obrigatória');
			formControl.tooltip();
			if(error == 0) formControl.tooltip('show');
			error ++ ;
		}
		
		if(error > 0)
			return ;

		ng.produto.validades.push(item);
		ng.itemValidade = {};
		ng.atualizaQtdValidadeItens();
	}

	ng.atualizaQtdValidadeItens = function() {
		var qtdTotal = 0;

		$.each(ng.produto.validades, function(i, item) {
			qtdTotal += parseInt(item.qtd,10);
		});

		ng.produto.qtd_ivn = qtdTotal;
		ng.atualizaQtdTotal();
	}

	ng.deleteValidadeItem = function(index) {
		ng.produto.validades.splice(index,1);
		ng.atualizaQtdValidadeItens();
	}

	 ng.id_invetario_current = null ;

	 ng.reset = function(){
		ng.Notas = {itens:[]};
	}

	ng.busca = { text: "", responsavel: ""};
	ng.resetFilter = function() {
		$("#data_da_contagem").val("");
		ng.busca.text = "" ;
		ng.busca.responsavel = "" ;
		ng.reset();
		ng.loadUltimosInventarios(0,10);
	}

    ng.loadUltimosInventarios = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;

    	var query_string = "?tde->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.text != ""){
			query_string += "&("+$.param({nme_deposito:{exp:"like '%"+ng.busca.text+"%'"}})+")";
		}

		if(ng.busca.responsavel != ""){
			query_string += "&("+$.param({'usu->nome':{exp:"like '%"+ng.busca.responsavel+"%'"}})+")";
		}

		if($("#data_da_contagem").val() != ""){
			var data = moment($("#data_da_contagem").val(), "DD/MM/YYYY").format("YYYY-MM-DD");
			query_string += "&("+$.param({'1':{exp:"= 1 AND cast(dta_contagem as date) = '"+ data +"' "}})+")";
		}
		
		ng.utimosInventarios = [];
		aj.get(baseUrlApi()+"inventarios/"+offset+"/"+limit+ query_string)
			.success(function(data, status, headers, config) {
				ng.utimosInventarios = data.invetarios ;
				ng.paginacao.inventarios = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.utimosInventarios = [];
			});
	}

	ng.loadItensInventario = function(id_inventario,offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;

		ng.detalhes = [];
		aj.get(baseUrlApi()+"inventarios/itens/"+offset+"/"+limit+"?id_inventario="+id_inventario)
			.success(function(data, status, headers, config) {
				ng.detalhes                      = data.itens ;
				ng.paginacao.detalhes            = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.loadUltimosInventarios = [];
			});
	}

	ng.showDetalhes = function(item){
		ng.id_invetario_current = item.id;
		ng.loadItensInventario(item.id,0,10);
		$('#list_detalhes').modal('show');
	}

	ng.validarDataValidade = function(mes_ano,index) {
		if(!empty(mes_ano)){
			var mes = parseInt(mes_ano.substr(0,2),10);
			var ano = parseInt(mes_ano.substr(2,4),10);

			var valid = true;

			var dtaAtual = new Date();
			var mesAtual = dtaAtual.getMonth() + 1;
			var anoAtual = dtaAtual.getFullYear();

			if(mes > 12)
				valid = false;

			if(ano < anoAtual)
				valid = false;

			if(ano < 1900 || ano > 2100)
				valid = false;

			if(!valid) {
				ng.mensagens('alert-danger','<strong>A data informada é inválida</strong>','.alert-itens');
				ng.itemValidade.validade = "";
		}
		}
	}

    ng.atualizaQtdTotal = function(){
     	var qtd_total = 0;
     	$.each(ng.inventario.itens,function(i,item){
     		qtd_total += parseInt(item.qtd_ivn == null || item.qtd_ivn == "" ? 0 : item.qtd_ivn);
     	});
     	ng.inventario.qtd_total = qtd_total ;
     }

     ng.deleteItem = function(index){
     	ng.inventario.itens.splice(index,1);
     	ng.atualizaQtdTotal();
     }

    ng.clearInventario = function(){
     	$('#inventarioData').val();
     	$.each(ng.inventario,function(i,item){
     		if(i == 'itens'){
     			ng.inventario[i] = [] ;
     		}else if (i == 'id_usuario_responsavel' || i == 'nome_usuario'){

     		}else
     			ng.inventario[i] = "" ;
     	});
     }

     /* end */

     /* inicio - Ações preços*/

	ng.showModalPrecos = function(){
		ng.precoProduto = [];

			$.each(ng.inventario.itens, function(x, itemIventario) {
				itemIventario.id_produto = itemIventario.id;
				itemIventario.margem_intermediario = parseFloat(itemIventario.margem_intermediario) * 100;
				itemIventario.margem_atacado       = parseFloat(itemIventario.margem_atacado) * 100;
				itemIventario.margem_varejo        = parseFloat(itemIventario.margem_varejo) * 100;

				if(ng.precoProduto != null && ng.precoProduto.length > 0) {
					var b = true;

					$.each(ng.precoProduto, function(y, itemPreco){
						if(itemIventario.id === itemPreco.id)
							b = false;
					});

					if(b)
						ng.precoProduto.push(itemIventario);
				}
				else
					ng.precoProduto.push(itemIventario);
			});

		$('#list_precos').modal('show');return;
	 }

	 ng.salvarPrecoProduto = function(item){
			var itens = [] ;

			$.each(ng.precoProduto, function(x, itemIventario) {
				itens = [] ;
				itemIventario = cloneArray(itemIventario,['$$hashKey']);
				itemIventario.margem_intermediario  = parseFloat(itemIventario.margem_intermediario) / 100;
				itemIventario.margem_atacado        = parseFloat(itemIventario.margem_atacado)       / 100;
				itemIventario.margem_varejo         = parseFloat(itemIventario.margem_varejo)        / 100;

				itens.push(itemIventario);
			});

			$($(".has-error")).tooltip('destroy');
			$(".has-error").removeClass("has-error");

			$http.post(baseUrlApi()+'produtos/preco',{precos:itens})
			.success(function(data, status, headers, config) {
				$('#list_precos').modal('hide');
				ng.mensagens('alert-success','<strong>Preços do produto atualizados com sucesso</strong>');
				ng.precoProduto = [] ;
			})
			.error(function(data, status) {
				if(status == 406) {

					$.each(data, function(i, item) {
						$("#"+i).addClass("has-error");

						var formControl = $($("#"+i))
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();
					});

				}else{
					alert('Desculpe, ocorreu um erro inesperado!');
				}
			});
		}

     /*end*/

     /* inicio - Ações de depositos */

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
		ng.inventario.id_deposito      = item.id;
		ng.inventario.nome_deposito    = item.nme_deposito;
		$('#list_depositos').modal('hide');
	}

   	/* end */

   	 /* inicio - Ações de produtos */

   	ng.produtos = [] ;

   	ng.showProdutos = function(clear){
   		clear = clear == false ? false : true ;
   		if(clear)
   			ng.busca.produtos = "" ;
   		ng.loadProdutos(0,10);
   		$('#list_produtos').modal('show');
   	}

   	ng.addFocus = function(){
   		ng.cod_barra_busca = '';
   		$('#focus').focus();
   		ng.busca_cod_barra = true ;
   	}

   	ng.blurBuscaCodBarra = function(){
   		ng.busca_cod_barra = false ;
   		$.noty.closeAll();
		var i = noty({
			timeout : 4000,
			layout: 'topRight',
			type: 'warning',
			theme: 'relax',
			text: 'Busca por codigo de barra desativada',
		});
   	}

   	ng.buscaCodBarra = function(){
   		if(empty(ng.cod_barra_busca)){
   			$.noty.closeAll();
			var i = noty({
				timeout : 4000,
				layout: 'topRight',
				type: 'warning',
				theme: 'relax',
				text: 'O codigo de barra não pode ser vazio',
			});
   			return ;
   		}
    	var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

    	if(ng.busca.produtos != ""){
    		query_string += "&"+$.param({'codigo_barra':ng.cod_barra_busca});
    	}
		aj.get(baseUrlApi()+"produtos"+query_string)
		.success(function(data, status, headers, config) {
			if(data.produtos.length == 1){
				item = data.produtos[0];
				item.nome_produto = item.nome;
				ng.cod_barra_busca = '';
				ng.addProduto(item, false);
				ng.showValidades(item);
			}else{
				ng.busca.produtos = ng.cod_barra_busca ;
				ng.showProdutos(false);
			}
		})
		.error(function(data, status, headers, config) {
			$.noty.closeAll();
			var i = noty({
				timeout : 4000,
				layout: 'topRight',
				type: 'error',
				theme: 'relax',
				text: 'Codigo de barra não corresponde a nenhum produto',
			});
		});	
   	}

   	ng.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;

    	var query_string = "?tpe->id_empreendimento="+ng.userLogged.id_empreendimento;

    	if(ng.busca.produtos != ""){
    		query_string += "&"+$.param({'(nome':{exp:"like'%"+ng.busca.produtos+"%' OR nome_fabricante like'%"+ng.busca.produtos+"%' OR codigo_barra='"+ng.busca.produtos+"' )"}});
    	}

		ng.produtos = [];
		aj.get(baseUrlApi()+"produtos/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos        = data.produtos ;
				ng.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.produtos = [];
			});
	}

	ng.addProduto = function(item, autoAddQtd){
		autoAddQtd = autoAddQtd == false ? false : true;

		if(autoAddQtd && empty(item.qtd))
    		item.qtd = 1;
    	else
    		item.qtd = 0

		var x = false ;

		$.each(ng.inventario.itens,function(i,produto){
			if(produto.id == item.id){
				x = true ;
			}
		});

		if(x){
			ng.mensagens('alert-danger','<strong>Este produto já foi adicionado a lista</strong>','.alert-produtos');
			return;
		}

		ng.inventario.itens.push(item);
		if(autoAddQtd) {
	    	ng.itemValidade = { validade: '', qtd: item.qtd };
	    	ng.addValidadeItem();
    		ng.atualizaQtdTotal();
    	}
	}

	/* end */

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
					ng.clearInventario();
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
			});
		}
	}

	ng.formatDate = function(date){
		return date.substring(0,2)+'/'+date.substring(2,6) ;
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

	ng.loadUltimosInventarios(0,10);
});
