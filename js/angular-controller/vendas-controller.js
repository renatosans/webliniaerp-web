app.controller('VendasController', function($scope, $http, $window, $dialogs, UserService, FuncionalidadeService,PrestaShop){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.venda 		= {};
	ng.vendas 		= [];
	ng.paginacao 	= {vendas:null}
	ng.detalhes     = [];
	ng.busca        = {produtos:"",usuarios:"",vendedor:""};
	ng.emptyBusca   = {};
	$scope.popover = {content: ''};
	var params      = getUrlVars();

	ng.funcioalidadeAuthorized = function(cod_funcionalidade){
		return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
	}

	ng.sucessoSeparacao = function(){
		if(params.alert != null){
			ng.mensagens('alert-success','A Venda foi separada com sucesso');
		}
	}

	ng.confirmarVenda = function(item) {
		aj.get(baseUrlApi()+"venda/confirmar/"+item.id)
			.success(function(data, status, headers, config) {
				ng.loadVendas(0,10);
			})
			.error(function(data, status, headers, config) {
				console.log(data);
			});
	}

	ng.limparBusca = function(){
		ng.busca.ven_id_vendedor = null;
		ng.busca.ven_nome_vendedor = null;
		ng.busca.ven_id_cliente = null ;
		ng.busca.ven_nome_cliente = null ;
		ng.busca.ven_id_venda     = null ;
		$("#dtaInicial").val(''); 
		ng.loadVendas(0,10);
	}

	ng.loadVendas = function(offset,limit) {
		var query_string = "?ven->id_empreendimento="+ ng.userLogged.id_empreendimento+"&ven->flg_excluido=0";
		if(params.status == 'orcamento')
			query_string += "&ven->venda_confirmada=0";

		var date =  empty($("#dtaInicial").val()) ? null : formatDate($("#dtaInicial").val()) ;
		query_string += empty(date)                        ? "" : "&date_format(ven->dta_venda,'%Y-%m-%d')="+date ;
		query_string += empty(ng.busca.ven_id_vendedor)    ? "" : "&ven->id_usuario="+ng.busca.ven_id_vendedor  ;
		query_string += empty(ng.busca.ven_id_cliente)     ? "" : "&ven->id_cliente="+ng.busca.ven_id_cliente  ;
		query_string += empty(ng.busca.ven_id_venda)       ? "" : "&ven->id="+ng.busca.ven_id_venda  ;



		aj.get(baseUrlApi()+"vendas/"+ offset +"/"+ limit + query_string)
			.success(function(data, status, headers, config) {
				//ng.vendas 			= _.groupBy(data.vendas, "dta_group");
				ng.vendas 			= data.vendas;
				ng.paginacao.vendas = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.vendas = [];
				else
					alert('erro ao buscar vendas');
			});
	}

	ng.getUrlPDFVenda = function(venda) {
		var url_pdf = baseUrlApi()+'relPDF?template=comprovante_venda&'+(
			$.param({
				dados: {
					json: JSON.stringify({
						id_venda 				: venda.id,
						id_empreendimento 		: ng.userLogged.id_empreendimento,
						pagamento_fulso 		: false,
						id_controle_pagamento 	: null,
						id_cliente 				: venda.id_cliente
					})
				}
			})
		);
		return url_pdf;
	}

	ng.loadDetalhesVenda = function(venda) {
		ng.venda = venda;
		$('#modal-print-venda').modal('show');
		ng.url_pdf = baseUrlApi()+'relPDF?template=comprovante_venda&'+($.param({dados:{json:JSON.stringify({
                    id_venda : ng.venda.id,
                    id_empreendimento : ng.venda.id_empreendimento,
                    pagamento_fulso : false,
                    id_controle_pagamento : null,
                    id_cliente : ng.venda.id_cliente
				})}}));
		$('#load-pdf-venda').show();
		$('#pdf-venda').hide();
		$('#pdf-venda').html('<iframe style="height:500px" width="100%"  src="'+ng.url_pdf+'" frameborder=0 allowTransparency="true"  style=" width: 100%;height: 900px;background: #fff;border: none;overflow: hidden; display:none"></iframe>')
		$('#pdf-venda iframe').load(function(){
			$('#pdf-venda').show();
		    $(this).show();
		   	$('#load-pdf-venda').hide();
		});

		/*$('#list_clientes').modal('show');

		aj.get(baseUrlApi()+"venda/itens/"+ venda.id)
			.success(function(data, status, headers, config) {
				ng.detalhes = data;
			})
			.error(function(data, status, headers, config) {
				console.log(data);
			});*/
	}

	ng.loadEditVenda = function(venda) {
		ng.delItensEdit = [];
		ng.venda = venda;

		$('#modal-edit-venda').modal({
		  backdrop: 'static',
		  keyboard: false
		});

		aj.get(baseUrlApi()+"venda/itens/"+ venda.id)
			.success(function(data, status, headers, config) {

				$.each(data,function(i,val){
					data[i].valor_desconto      = data[i].valor_desconto * 100 ;
					data[i].add_produto         = false ;
					data[i].flg_desconto        = data[i].desconto_aplicado;
				});

				ng.edit = data;
				ng.calTotalVendaEdit();
			})
			.error(function(data, status, headers, config) {
				alert('error fatal');
			});
	}

	ng.produtoEditExistis = function(id_produto){
		exists = false ;

		$.each(ng.edit,function(i,v){
			if(Number(id_produto) == Number(v.id_produto)){
				exists = true ;
				return;
			}
		});

		return exists ;

	}

	ng.calTotalVendaEdit = function() {
		var total = 0 ;
		$.each(ng.edit,function(i,v){
			qtd 		=  Number(v.qtd) == 0 || v.qtd == ""  ? 1 : Number(v.qtd) ;
			v.sub_total =  qtd * v.valor_real_item ;
			total 		+= qtd * v.valor_real_item ;
		});

		ng.total_venda_edit = total ;
	}

	ng.delItensEdit = [];
	ng.excluirItemEdit = function(index,item){
		if(item.add_produto == undefined || item.add_produto == false )
			ng.delItensEdit.push(item.id);
		ng.edit.splice(index,1);
		ng.calTotalVendaEdit();
		console.log(ng.delItensEdit);
	}

	ng.calDescVendaEdit = function(item,tipo) {
		if(tipo == 'vlr'){
			var valor_desconto  =(item.valor_desconto_real * 100)/item.vlr_produto;
			item.valor_desconto = Math.round( valor_desconto * 100) /100 ;
			valor_desconto = (valor_desconto / 100) * item.vlr_produto ;

		}else if(Number(item.flg_desconto) == 1){
			var valor_desconto     = isNaN(Number(item.valor_desconto)) ? 0 : Number(item.valor_desconto) ;
			valor_desconto = (valor_desconto / 100) * item.vlr_produto ;
			item.valor_desconto_real = valor_desconto ;
		}else{
			var valor_desconto = 0 ;
			item.valor_desconto_real = 0 ;
		}	
		
		item.valor_real_item = item.vlr_produto - valor_desconto ;
		ng.calTotalVendaEdit() ;
	}

	ng.showProdutos = function(){
   		ng.busca.produtos = "" ;
   		ng.loadProdutos(0,10);

   		$('#modal-edit-venda').modal('hide');
   		$('#list_produtos').modal({
		  backdrop: 'static',
		  keyboard: false
		});
   	}

   	ng.voltarVenda = function(){
   		$('#list_produtos').modal('hide');
   		$('#modal-edit-venda').modal('show');
   	}

   	ng.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

 
    	var query_string = "?group=&emp->id_empreendimento="+ng.userLogged.id_empreendimento;
    	

    	if(ng.busca.produtos != ""){
    		query_string += "&("+$.param({'prd->nome':{exp:"like'%"+ng.busca.produtos+"%' OR fab.nome_fabricante like'%"+ng.busca.produtos+"%'"}})+")";
    	}

		ng.produtos = [];
		aj.get(baseUrlApi()+"estoque/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos        = data.produtos ;
				ng.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.produtos = [];
			});
	}

	ng.addProduto = function(item){
		var produto = angular.copy(item);
		if(ng.venda.perfil_cliente == "atacado"){
			produto.vlr_produto    	 	 = produto.vlr_venda_atacado;
			produto.valor_real_item      = produto.vlr_venda_atacado;
			produto.perc_margem_aplicada = produto.margem_atacado;
		}else if(ng.venda.perfil_cliente == "varejo"){
			produto.vlr_produto		     = produto.vlr_venda_varejo;
			produto.valor_real_item      = produto.vlr_venda_varejo;
			produto.perc_margem_aplicada = produto.margem_varejo;
		}else if(ng.venda.perfil_cliente == "vendedor externo"){
			produto.vlr_produto		 	 = produto.vlr_venda_intermediario;
			produto.valor_real_item      = produto.vlr_venda_intermediario;
			produto.perc_margem_aplicada = produto.margem_intermediario;
		}else if(ng.venda.perfil_cliente == 'parceiro'){
			produto.vlr_produto    	 	 = produto.vlr_custo_real;
			produto.valor_real_item  	 = produto.vlr_custo_real;
			produto.perc_margem_aplicada = 0 ;
		}else{
			produto.vlr_produto		     = produto.vlr_venda_varejo;
			produto.valor_real_item      = produto.vlr_venda_varejo;
			produto.perc_margem_aplicada = produto.margem_varejo;
		}
		produto.valor_desconto = 0;
		produto.valor_desconto_real = 0 ;
		produto.qtd = 1 ;
		produto.add_produto = true ;
		produto.sub_total = produto.qtd_total * produto.vlr_unitario;

		
		ng.edit.push(produto);
		ng.calTotalVendaEdit();
	}

	ng.editarOrcamento = function(){
		var btn = $('#salvar-orcamento');
   		btn.button('loading');
   		var itens = [] ;
   		$.each(ng.edit,function(i,v){
   			var item = angular.copy(v);
   			if( (empty(item.valor_desconto) || Number(item.valor_desconto) <= 0 || isNaN(Number(item.valor_desconto))) || ( empty(item.flg_desconto)) ){
   				item.desconto_aplicado = 0 ;
   				item.valor_desconto    = 0 ;
   			}else{
   				item.desconto_aplicado = 1 ;
   				item.valor_desconto    = Number(item.valor_desconto)/100;
   			}
   			itens.push(item);
   		});

   		

		aj.post(baseUrlApi()+"orcamento/edit",{venda:ng.venda,itens:itens,itensDel:ng.delItensEdit})
		.success(function(data, status, headers, config) {
			ng.mensagens('alert-success','<b>Orçamento alterado com sucesso</b>','.alert-orcamento-edit');
			ng.loadVendas(0,10);
			btn.button('reset');
		})
		.error(function(data, status, headers, config) {
			ng.mensagens('alert-danger','<b>Ocorreu um erro ao alterar o orçamento</b>','.alert-orcamento-edit');
			btn.button('reset');
		});
	}

	ng.changeStatus = function(id_status,id_venda){
		aj.post(baseUrlApi()+"venda/change/status",{id_venda:id_venda,id_status:id_status,id_empreendimento:ng.userLogged.id_empreendimento})
			.success(function(data, status, headers, config) {
				ng.loadVendas(0,10);
			})
			.error(function(data, status, headers, config) {
				alert('Ocorreu um erro inesperado');
			});
	}

	ng.cancelarModal = function(id){
		$('#'+id).modal('hide');
	} 

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}
	ng.loadPopover = true ;
	ng.getLastVendaProdutoByCliente = function(item){
		last_venda = { dta_venda:"00-00-00", vlr_custo:0,vlr_produto:0,valor_desconto:0,vlr_real_desconto:0,valor_real_item:0 } ;
		console.log(ng.venda);
		aj.get(baseUrlApi()+"venda/lastProduto/"+ng.userLogged.id_empreendimento+"/"+ng.venda.id_cliente+"/"+item.id_produto+"")
			.success(function(data, status, headers, config) {
				ng.last_venda = data ;
				ng.loadPopover = false ;
				
			})
			.error(function(data, status, headers, config) {
				alert('Ocorreu um erro inesperado');
				ng.loadPopover = null ;
		});
	}

	ng.cancelarVenda = function(item) {
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja cancelar esta venda? Os produtos serão retornados ao estoque e os pagamentos vinculados a ela serão cancelados</strong>');
		dlg.result.then(function(btn){
			var post = {
				id_venda : item.id,
				id_empreendimento : ng.userLogged.id_empreendimento,
				id_usuario : ng.userLogged.id
			};
			aj.post(baseUrlApi()+"venda/cancelar",post)
			.success(function(data, status, headers, config) {
				ng.loadVendas(0,10);
				ng.mensagens('alert-success','venda  cancelada com sucesso','#alert-list-vendas');
				var postPrestaShop = {produtos:[item.id],id_empreendimento:ng.userLogged.id_empreendimento};
				PrestaShop.send('post',baseUrlApi()+"prestashop/estoque",postPrestaShop);
			})
			.error(function(data, status, headers, config) {
				ng.mensagens('alert-danger','Erro ao cancelar venda','#alert-list-vendas');
			});
		}, undefined);	
	}

	ng.excluirOrcamento = function(item) {
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este orçamento?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"orcamento/delete/"+item.id+"/"+ng.userLogged.id_empreendimento+"/"+ng.userLogged.id)
			.success(function(data, status, headers, config) {
				ng.loadVendas(0,10);
				ng.mensagens('alert-success','O orçamento foi excluido com sucesso','#alert-list-vendas');
			})
			.error(function(data, status, headers, config) {
				ng.mensagens('alert-danger','Erro ao excluir orçamento','#alert-list-vendas');
			});
		}, undefined);	
	}
	ng.emptyBusca.usuarios = false ;
	ng.busca.tipo_usuario  = null ;
	ng.loadUsuarios = function(offset,limit,tipo) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;

		ng.emptyBusca.usuarios = false ;
		ng.paginacao_usuarios  = [];
		ng.usuarios = [];
		ng.busca.tipo_usuario = tipo ;
		if(tipo == 'vendedor')
			query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+") AND (usu.flg_tipo='usuario')";
		else
			query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+")";

		if(ng.busca.usuarios != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.usuarios+"%' OR usu.apelido LIKE '%"+ng.busca.usuarios+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.usuarios = data.usuarios;
				ng.paginacao_usuarios = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.usuarios = [] ;
					ng.emptyBusca.usuarios = true ;
				}else{
					alert('Ocorreu um erro ao carregar os usuários');
				}
			});
	}

	ng.selUsuario = function(tipo){
		var offset = 0  ;
    	var limit  =  10 ;;
		ng.busca.usuarios = "";
		ng.loadUsuarios(offset,limit,tipo);
		$("#list_usuarios").modal("show");
	}

	ng.addUsuario = function(item){
		if(ng.busca.tipo_usuario  == 'vendedor'){
			ng.busca.ven_nome_vendedor = item.nome ;
			ng.busca.ven_id_vendedor   = item.id;
		}else if(ng.busca.tipo_usuario  == 'cliente'){
			ng.busca.ven_nome_cliente = item.nome ;
			ng.busca.ven_id_cliente   = item.id;
		}
		$("#list_usuarios").modal("hide");
	}

	if( !(typeof params.id_venda == "undefined") ){
		aj.get(baseUrlApi()+"vendas/?ven->id="+params.id_venda)
			.success(function(data, status, headers, config) {
				var venda = data.vendas[0];
				ng.loadDetalhesVenda(venda);
			})
			.error(function(data, status, headers, config) {

		});
	}

	ng.changeVendedor = function(id_vendedor){
		$("#list-vendedor").modal("hide");
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem Certeza que Deseja Alterar o Vendedor Desta Venda ?</strong>');
		dlg.result.then(function(btn){
			var post = {
				id_vendedor: id_vendedor,
				id_venda   : ng.venda_change.id,
				id_empreendimento: ng.userLogged.id_empreendimento
			}
			aj.post(baseUrlApi()+"venda/change/vendedor/",post)
			.success(function(data, status, headers, config) {
				ng.venda_change.nme_vendedor = data.nme_vendedor;
				ng.id_vendedor               = data.id_vendedor ;
				ng.mensagens('alert-success','O Vendedor Foi Alterado com Sucesso','#alert-list-vendas');
			})
			.error(function(data, status, headers, config) {
				ng.mensagens('alert-danger','Ocorreu um Erro ao Alterar o Vendedor','#alert-list-vendas');
			});
		}, function(){
			$("#list-vendedor").modal("show");
		} );
	}
	ng.venda_change = false; 
	ng.selVendedor = function(item){
		var offset = 0  ;
    	var limit  =  10 ;
    	ng.venda_change = item ;
		ng.loadVendedor(offset,limit);
		$("#list-vendedor").modal("show");
	}

	ng.loadVendedor= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+" AND (usu.flg_tipo='usuario'))";

		if(ng.busca.vendedor != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.vendedor+"%' OR usu.apelido LIKE '%"+ng.busca.vendedor+"%')"}});
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
				ng.clientes = false ;
			});
	}

	ng.printModal = function(modal){
		printDiv(modal, "");
	} 


	ng.loadVendas(0,10);
	ng.sucessoSeparacao();
});

app.directive('bsPopover', function () {
        return function (scope, element, attrs) {
            element.find("a[rel=popover]").popover({
                placement: 'bottom',
                html: 'true'
            });
        };
 });