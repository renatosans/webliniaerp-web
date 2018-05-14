app.controller('PedidoTransferenciaController', function($scope, $http, $window, $dialogs, UserService,ConfigService,PrestaShop, FuncionalidadeService){
	var ng = $scope
		aj = $http;
	ng.ctrl = $scope ;
	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.busca 		= {empreendimento:'',produto:''} ;
	ng.configuracoes = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.paginacao    = {};
    ng.lista_emp    = [];
    ng.busca        = {usuario_pedido:{},empreendimento_busca:{}} ;
    ng.filtro = {};

    var flg_error = 0;

    ng.etapas = [
    	{id: null , nme_etapa: "Selecione", flg_oculto_empreendimento: false},
    	{id: 1 , nme_etapa: "Solicitação", flg_oculto_empreendimento: false},
    	{id: 2 , nme_etapa: "Envio", flg_oculto_empreendimento: true},
    	{id: 3 , nme_etapa: "Recebimento", flg_oculto_empreendimento: false}
    ];

    ng.status = [
   		{
			id_status_transferencia_estoque: null,
			dsc_status_transferencia_estoque: "Selecione"
		},
		{
			id_status_transferencia_estoque: 1,
			dsc_status_transferencia_estoque: "Pedido enviado"
		},
		{
			id_status_transferencia_estoque: 2,
			dsc_status_transferencia_estoque: "Pedido em transito"
		},
		{
			id_status_transferencia_estoque: 3,
			dsc_status_transferencia_estoque: "Pedido recebido"
		},
		{
			id_status_transferencia_estoque: 4,
			dsc_status_transferencia_estoque: "Pedido a solicitar"
		},
	] ;

    var transferenciaTO = {
    	id : null,
		id_empreendimento_pedido : ng.userLogged.id_empreendimento,
		id_empreendimento_transferencia : null,
		id_usuario_pedido : ng.userLogged.id,
		id_usuario_transferencia : null,
		dta_pedido : null,
		dta_transferencia : null,
		id_status_transferencia : null,
   		produtos:[]
	};
    ng.transferencia = angular.copy(transferenciaTO);

    ng.editing = false;

    ng.funcioalidadeAuthorized = function(cod_funcionalidade){
    	return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
    }

    ng.showProductCost = function(transferencia) {
    	if(ng.funcioalidadeAuthorized('ver_custo_transferencia')) {
    		return (ng.isNumeric(transferencia.id) && transferencia.id_status_transferencia == 3);
    	}

    	return false;
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

	ng.resetFilter = function(){
		ng.busca  = {usuario_pedido:{},empreendimento_busca:{}} ;
		ng.loadtransferencias(0,10);
	}

	ng.cancelar = function(){
		ng.showBoxNovo();
		ng.transferencia = angular.copy(transferenciaTO);
	}

	ng.isNumeric = function(vlr){
		return $.isNumeric(vlr);
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.showEmpreendimentos = function() {
		$('#list_empreendimentos').modal('show');
		ng.loadAllEmpreendimentos(0,10);
	}

	ng.loadAllEmpreendimentos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?id_usuario="+ng.userLogged.id;
    	if(empty(!ng.busca.empreendimento)){
    		query_string += "&" +$.param({nome_empreendimento:{exp:"like'%"+ng.busca.empreendimento+"%'"}});
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
	ng.addEmpreendimento = function(item,event) {
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');
		ng.transferencia.produtos = [] ;
		ng.transferencia.id_empreendimento_transferencia = item.id;
		ng.transferencia.nome_empreendimento = item.nome_empreendimento ;
		aj.get(baseUrlApi()+"configuracoes/"+item.id+"/id_deposito_padrao")
		.success(function(data, status, headers, config) {
			ng.transferencia.id_deposito_padrao_pedido = data.id_deposito_padrao ;
			btn.button('reset');
			$('#list_empreendimentos').modal('hide');
		})
		.error(function(data, status, headers, config) {
			ng.transferencia.id_deposito_padrao_pedido = null ;
			btn.button('reset');
			$('#list_empreendimentos').modal('hide');
		});
		
	}


	ng.showProdutos = function(){
		$('#list_produtos').modal('show');
		ng.busca.produto = "";
		ng.loadProdutos(0,10);
		setTimeout(function(){
		
		},500);
	}

	ng.loadProdutos = function(offset, limit) {
	
		ng.produtos = null;

 		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.transferencia.id_empreendimento_transferencia;
		if(!empty(ng.transferencia.id_deposito_padrao_pedido)){
			query_string += "&id_deposito_estoque="+ng.transferencia.id_deposito_padrao_pedido;
			// query_string += "&getQtdProduto(tpe->id_empreendimento,pro->id,null,"+ng.transferencia.id_deposito_padrao_pedido+",null)[exp]=>0";
		}
		query_string +="&pro->id[exp]= IN(SELECT tp.id FROM tbl_produtos AS tp INNER JOIN tbl_produto_empreendimento AS tpe ON tp.id = tpe.id_produto WHERE tpe.id_empreendimento IN ("+ng.userLogged.id_empreendimento+"))";

		if(!empty(ng.busca.produto)){
			if(isNaN(Number(ng.busca.produto)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produto+"%' OR codigo_barra like '%"+ng.busca.produto+"%' OR fab.nome_fabricante like '%"+ng.busca.produto+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produto+"%' OR codigo_barra like '%"+ng.busca.produto+"%' OR fab.nome_fabricante like '%"+ng.busca.produto+"%' OR pro.id = "+ng.busca.produto+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				angular.forEach(data.produtos, function(produto){
					if(empty(ng.produtos))
						ng.produtos = [];
					if(produto.qtd_item > 0) {
						ng.produtos.push(produto);
					}
				});
				ng.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
				
					ng.produtos = [];
					ng.paginacao.produtos = null;
				}
			});
	}

	ng.produtoSelected = function(id){
		var r = false ;
		$.each(ng.transferencia.produtos,function(i,x){
			if(Number(x.id) == Number(id)){
				r = true ;
				return false ;
			}
		});
		return r ;
	}

	ng.excluirProdutoLista = function(index){
		ng.transferencia.produtos.splice(index,1);
	}

	ng.addProduto = function(item,incrementar){
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		if(typeof item.atualizar_custo == 'undefined'){
			delete item.vlr_custo ;
		}
		var produto = angular.copy(item) ;
		produto.id_produto = item.id ;

		if(ng.transferencia.flg_controle_validade==1){
			var qtd_transferida = 0 ;
			$.each(produto.validades,function(i,x){
				if($.isNumeric(x.qtd_transferida))
					qtd_transferida += Number(x.qtd_transferida);
			});
			produto.qtd_transferida = qtd_transferida ;
		}

		var index = null ;
		$.each(ng.transferencia.produtos,function(i,v){
			if(v.id_produto == produto.id){
				index = i ;
				return ;
			}
		});

		if(incrementar === true && $.isNumeric(index)){
			var qtd = $.isNumeric(ng.transferencia.produtos[index].qtd_pedida) ? Number(ng.transferencia.produtos[index].qtd_pedida)  : 1 ;
			ng.transferencia.produtos[index].qtd_pedida = (qtd + 1) ;
		}else{
			produto.qtd_pedida = empty(produto.qtd_pedida) ? 1 : produto.qtd_pedida ;
			ng.transferencia.produtos.push(produto);
			item.qtd_pedida = null ;
		}

		$('#foco').focus()
	}

	ng.verificaQtdMultiplo = function(container, index, item){
		if (item.qtd_multiplo_transferencia != "" || item.qtd_multiplo_transferencia != null) {
			if ((item.qtd_pedida % item.qtd_multiplo_transferencia) > 0) {
				var element = '#'+ container +' #produtos #txt-qtd-multiplo-'+ index;
				$(element).closest('.form-group').addClass('has-error');
				var formControl = $(element)
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "top")
					.attr("title", 'Apenas solicitação em multiplo de: ' + item.qtd_multiplo_transferencia)
				formControl.tooltip();
				item.flg_error = 1;
			} else{
				var element = '#'+ container +' #produtos #txt-qtd-multiplo-'+ index;
				$(element).tooltip('destroy');
				$(element).closest('.form-group').removeClass("has-error");
				item.flg_error = 0;
			}
		}
	}

	ng.salvarTransferencia = function(id_status_transferencia,event){
		var flg_error_save = false
		angular.forEach(ng.transferencia.produtos, function(produto){
			if (produto.flg_error == 1) {
				alert("Por favor, verifique se existe algum produto com quantidade multipla");
				flg_error_save = true
			}
		});
		if (flg_error_save) {
			return false;
		}

		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');
		var error = 0 ;

		if(!$.isNumeric(ng.transferencia.id_empreendimento_transferencia)){
			$("#id_empreendimento_transferencia").addClass("has-error");
			var formControl = $('#id_empreendimento_transferencia .input-group')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "top")
				.attr("title", 'Informe o empreendimento que deseja solicitar a transferência')
				.attr("data-original-title", 'Informe o empreendimento que está solicitando a transferência');
			formControl.tooltip();
			if(error == 0){
				$('html,body').animate({scrollTop: 0},'slow');
				formControl.tooltip('show');
			} 
			error ++ ;
		}

		if(ng.transferencia.produtos.length == 0){
			$("#produtos").addClass("has-error");
			var formControl = $('#produtos')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "top")
				.attr("title", 'Informe os produtos para transferência')
				.attr("data-original-title", 'Informe os produtos para transferência');
			formControl.tooltip();
			if(error == 0){
				$('html,body').animate({scrollTop: 0},'slow');
				formControl.tooltip('show');
			} 
			error ++ ;
		}

		$.each(ng.transferencia.produtos,function(key,item){
			if(!($.isNumeric(item.qtd_pedida))){
				$('#td-trasnferencia-qtd-pedida-'+item.id).addClass('has-error');
				$('#td-trasnferencia-qtd-pedida-'+item.id).find('input').attr("data-placement", "top").attr("title", 'A quantidade pedida não pode ser vazia').attr("data-original-title", 'A quantidade para pedida não pode ser vazia'); 
				if(error == 0) {
					$('#td-trasnferencia-qtd-pedida-'+item.id).find('input').tooltip('show');
					$('html,body').animate({scrollTop: $('#td-trasnferencia-qtd-pedida-'+item.id).offset().top - 100 },'slow');
				}else {
					$('#td-trasnferencia-qtd-pedida-'+item.id).find('input').tooltip();
				}
				error ++ ;
			}
		});

		if(error > 0){
			btn.button('reset'); 
			return ;
		}
		ng.transferencia.id_status_transferencia = id_status_transferencia ;
		var post = angular.copy(ng.transferencia);
		post.dta_pedido = id_status_transferencia == 4 ? null : moment().format('YYYY-MM-DD HH:mm:ss');

		var url ;
		if($.isNumeric(ng.transferencia.id)){
			url = 'estoque/pedido/transferencia/edit';
		}else{
			url = 'estoque/pedido/transferencia';
		}

		aj.post(baseUrlApi()+url, { form_data: JSON.stringify(post) })
		.success(function(data, status, headers, config) {
			btn.button('reset'); 
			ng.transferencia = angular.copy(transferenciaTO);
			ng.showBoxNovo();
			ng.mensagens('alert-success','<b>Pedido de transferência realizado com sucesso</b>','.alert-transferencia-lista');
			$('html,body').animate({scrollTop: 0},'slow');
			ng.loadtransferencias(0,10);

		})
		.error(function(data, status, headers, config) {
			ng.mensagens('alert-danger','<b>Ocorreu um erro ao realizar o pedido</b>','.alert-transferencia-form');
			$('html,body').animate({scrollTop: 0},'slow');
			btn.button('reset'); 
		});
	}

	ng.listaTransferencias = {} ;
	ng.loadtransferencias = function(offset, limit){
		ng.listaTransferencias.transferencias = null 
		var query_string = "?cplSql=id_empreendimento_pedido="+ng.userLogged.id_empreendimento+" AND id_status_transferencia <> 5 ";
		if(!empty(ng.busca.data) && !empty(ng.busca.id_etapa_data)){
			if(ng.busca.id_etapa_data == 1 )
				query_string += " AND date_format(tte.dta_pedido,'%Y-%m-%d') = '"+ng.busca.data+"' ";
			else if(ng.busca.id_etapa_data == 2)
				query_string += " AND date_format(tte.dta_transferencia,'%Y-%m-%d') = '"+ng.busca.data+"' ";
			else if(ng.busca.id_etapa_data == 3)
				query_string += " AND date_format(tte.dta_recebido,'%Y-%m-%d') = '"+ng.busca.data+"' ";
		}
		if(!empty(ng.busca.id_status))
			query_string += " AND id_status_transferencia = "+ng.busca.id_status+" ";
		if(!empty(ng.busca.usuario_pedido.id))
			query_string += " AND id_usuario_pedido = "+ng.busca.usuario_pedido.id+" ";
		if(!empty(ng.busca.empreendimento_busca.id))
			query_string += " AND id_empreendimento_transferencia = "+ng.busca.empreendimento_busca.id+" ";
		
		query_string += "ORDER BY (CASE WHEN dta_pedido IS NULL THEN tte.id ELSE 0 END) DESC ,dta_pedido DESC";
		aj.get(baseUrlApi()+"transferencias/estoque/"+offset+"/"+limit+query_string)
		.success(function(data, status, headers, config) {
			ng.listaTransferencias.transferencias = data.transferencias ;
			ng.listaTransferencias.paginacao = data.paginacao ;
		})
		.error(function(data, status, headers, config) {
 			ng.listaTransferencias.transferencias = [] ;
			ng.listaTransferencias.paginacao = [] ;
		});
	}
	ng.view = {};
	ng.viewTransferencia = function(id){
		ng.loadTransferencia(id);
		$('#modal-detalhes-transferencia').modal('show');
	}
	ng.loadTransferencia = function(id){
		ng.view.transferencia  = null 
		aj.get(baseUrlApi()+"transferencia/estoque/"+id)
		.success(function(data, status, headers, config) {
			ng.view.transferencia = data ;
		})
		.error(function(data, status, headers, config) {
 			ng.view.transferencia = []
		});
	}

	var index_current_edit = null ;
	ng.editTransferencia = function(index,event,id_status_transferencia){
		var id = ng.listaTransferencias.transferencias[index].id ;
		index_current_edit = index ;
		var btn = $(event.target) ;
		ng.transferencia = angular.copy(transferenciaTO);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');
		ng.view.transferencia  = null 
		ng.nome_deposito_principal = '';
		ng.id_deposito_principal = '';
		aj.get(baseUrlApi()+"transferencia/estoque/"+id)
		.success(function(data, status, headers, config) {
			var prd_in = ''; aux = [];
			ng.addEmpreendimento(data.empreendimento_transferencia,event);
			$.each(data.itens,function(i,x){
				prd_in += x.id_produto+",";
				if(ng.listaTransferencias.transferencias[index].flg_controle_validade == 1){
					if(empty(aux[x.id_produto])){
						aux[x.id_produto] = {
							vlr_custo : x.vlr_custo,
							qtd_pedida:x.qtd_pedida,
							qtd_transferida:x.qtd_transferida,
							validades:{}
						} ;
					}
					if(empty(aux[x.id_produto]['validades'][x.dta_validade])){
						aux[x.id_produto]['validades'][x.dta_validade] = {
							qtd_transferida:Number(x.qtd_transferida),
							dta_validade : x.dta_validade,
							qtd_recebida : null
						};
					}else{
						aux[x.id_produto]['validades'][x.dta_validade].qtd_transferida += Number(x.qtd_transferida) ;
					}
				}else{
					aux[x.id_produto] = {
						qtd_pedida:x.qtd_pedida,
						qtd_transferida:x.qtd_transferida,
						id_item:x.id,
						vlr_custo : x.vlr_custo
					} ;
				}
			});
			prd_in = prd_in.substring(0,prd_in.length-1);
			aj.get(baseUrlApi()+"produtos?tpe->id_empreendimento="+ng.userLogged.id_empreendimento+"&pro->id[exp]=IN("+prd_in+")")
			.success(function(prd, status, headers, config) {
				ng.transferencia.id = data.id ;
				ng.transferencia.flg_controle_validade = data.flg_controle_validade ;
				//ng.transferencia.id_empreendimento_pedido = data.id_empreendimento_pedido ;
				ng.transferencia.id_empreendimento_transferencia = data.id_empreendimento_transferencia ;
				//ng.transferencia.id_usuario_pedido = data.id_usuario_pedido ;
				ng.transferencia.id_usuario_transferencia = ng.userLogged.id ;
				//ng.transferencia.dta_pedido = data.dta_pedido ;
				//ng.transferencia.dta_transferencia = data.dta_transferencia ;
				ng.transferencia.id_status_transferencia = id_status_transferencia ;
				ng.transferencia.produtos = [];
				ng.showBoxNovo(true);
				$.each(prd.produtos,function(x,i){ prd.produtos[x].id_item = aux[i.id_produto].id_item; });
				prd.produtos = _.sortBy(prd.produtos,'id_item');
				$.each(prd.produtos,function(x,i){
					if(ng.listaTransferencias.transferencias[index].flg_controle_validade == 1){
							i.qtd_pedida = aux[i.id_produto].qtd_pedida;
							i.qtd_transferida = aux[i.id_produto].qtd_transferida;
							i.vlr_custo_sugerido = aux[i.id_produto].vlr_custo;
							i.atualizar_custo = 0 ;
							i.validades = aux[i.id_produto].validades ;
							i.qtd_recebida = 0 ;
							ng.addProduto(i);
					}else{
						i.qtd_pedida = aux[i.id_produto].qtd_pedida;
						i.qtd_transferida = aux[i.id_produto].qtd_transferida;
						i.vlr_custo_sugerido = aux[i.id_produto].vlr_custo;
						i.atualizar_custo = 0 ;
						ng.addProduto(i);
					}
				});
				ng.loadDepositos(0,10,true);
				btn.button('reset');
				$('html,body').animate({scrollTop: 0},'slow');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				alert('Ocorreu um erro ao buscar os dados');
			});

		})
		.error(function(data, status, headers, config) {
 			btn.button('reset');
 			alert('Ocorreu um erro ao buscar os dados');
		});
	}

	ng.loadDepositosSelect = function() {
		ng.depositos_chosen = [];
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
    	aj.get(baseUrlApi()+"depositos/"+query_string)
		.success(function(data, status, headers, config) {
			ng.depositos_chosen = ng.depositos_chosen.concat(data.depositos);	
			setTimeout(function(){$("select").trigger("chosen:updated");},300);
		})
		.error(function(data, status, headers, config) {
			ng.depositos = [] ;	
		});
	}

	ng.selDeposito = function(){
		$('#list_depositos').modal('show');
		ng.loadDepositos(0,10);
	}

	ng.addDeposito = function(item){
		ng.nome_deposito_principal = item.nme_deposito;
		ng.id_deposito_principal = item.id;
		$('#list_depositos').modal('hide');
		$.each(ng.transferencia.produtos,function(i,x){
			if(!$.isNumeric(ng.transferencia.produtos[i].id_deposito_entrada))
				ng.transferencia.produtos[i].id_deposito_entrada = item.id;
		});
	}

	ng.loadDepositos = function(offset, limit,preSel) {
		preSel = empty(preSel) ? false : preSel ;
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.depositos = null ;
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(!empty(ng.busca.depositos))
			query_string  += "&"+$.param({nme_deposito:{exp:"like '%"+ng.busca.depositos+"%'"}});

    	aj.get(baseUrlApi()+"depositos/"+offset+"/"+limit+query_string)
		.success(function(data, status, headers, config) {
			ng.depositos = data.depositos ;
			ng.paginacao_depositos = data.paginacao ;
			if(ng.depositos.length == 1 && preSel){
				ng.addDeposito(ng.depositos[0]);
			}
			
		})
		.error(function(data, status, headers, config) {
			ng.depositos = [] ;	
		});
	}

	ng.receberTransferencia = function(){
		var btn = $('#receber-transferencia') ;
		btn.button('loading');
		$('.has-error').find('input').tooltip('destroy');
		$('.has-error').find('.chosen-single').attr('style','');
		$('.has-error').find('.chosen-single').find('span').attr('style','');
		$('.has-error').find('.chosen-single').tooltip('destroy');

		$('.has-error').removeClass('has-error');
		var error = 0 ;
		var qtd_atualiza_custo = 0 ;
		var postPrestaShop = {produtos:[]};
		$.each(ng.transferencia.produtos,function(key,item){
			postPrestaShop.produtos.push(item.id_produto);
			if(!($.isNumeric(item.qtd_recebida))){
				var id_element = '#td-trasnferencia-qtd-recebida-'+item.id ;
				$(id_element).addClass('has-error');
				$(id_element).find('input').attr("data-placement", "top").attr("title", 'A quantidade recebida não pode ser vazia').attr("data-original-title", 'A quantidade para transferência não pode ser vazia'); 
				if(error == 0) {
					$(id_element).find('input').tooltip('show');
					$('html,body').animate({scrollTop: $(id_element).offset().top - 100 },'slow');
				}else {
					$(id_element).find('input').tooltip();
				}
				error ++ ;
			}else if( !(Number(item.qtd_recebida) == Number(item.qtd_transferida)) ){
				var id_element = '#td-trasnferencia-qtd-recebida-'+item.id ;
				$(id_element).addClass('has-error');
				$(id_element).find('input').attr("data-placement", "top").attr("title", 'A quantidade recebida não pode diferente da transferida').attr("data-original-title", 'A quantidade recebida não pode diferente da transferida'); 
				if(error == 0) {
					$(id_element).find('input').tooltip('show');
					$('html,body').animate({scrollTop: $(id_element).offset().top - 100 },'slow');
				}else {
					$(id_element).find('input').tooltip();
				}
				error ++ ;
			}
			if(!$.isNumeric(item.id_deposito_entrada) && ( $.isNumeric(item.qtd_recebida) && Number(item.qtd_recebida) > 0 ) ){
				var id_element =  '#td-trasnferencia-id-deposito-entrada-'+item.id ;
				$(id_element).addClass('has-error');
				$(id_element).find('.chosen-single').attr("data-placement", "top").attr("title", 'Informe o deposito de entrada').attr("data-original-title", 'A quantidade para transferência não pode ser vazia'); 
				$(id_element).find('.chosen-single').attr('style','border: 1px solid #A94442;');
				$(id_element).find('.chosen-single').find('span').attr('style','color:#A94442;');
				if(error == 0) {
					$(id_element).find('.chosen-single').tooltip('show');
					$('html,body').animate({scrollTop: $(id_element).offset().top - 100 },'slow');
				}else {
					$(id_element).find('.chosen-single').tooltip();
				}
				error ++ ;
			}
			if(Number(item.atualizar_custo == 1)){
				qtd_atualiza_custo ++ ;
			}

		});

		if(error > 0){
			btn.button('reset');
			return ;
		}

		if(ng.funcioalidadeAuthorized('ver_custo_transferencia') && qtd_atualiza_custo==0){
			dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que não deseja atualizar os valores de custo ?</strong>');
			$('#confirmModal').parent('.modal').show();
			dlg.result.then(
				function(){
					ng.transferencia.dta_recebido = moment().format('YYYY-MM-DD HH:mm:ss');
					ng.transferencia.id_usuario_recebeu = ng.userLogged.id ;
					var post = angular.copy(ng.transferencia);
					if(ng.transferencia.flg_controle_validade == 1){
						post.flg_controle_validade = 1 ;
						post.produtos = formatPostValidades() ;
					}
					aj.post(baseUrlApi()+"estoque/pedido/transferencia/receber/",{form_data: JSON.stringify(post)})
					.success(function(data, status, headers, config) {
						aj.get(baseUrlApi()+"transferencias/estoque/?id_empreendimento_transferencia="+ng.userLogged.id_empreendimento+"&tte->id="+ng.transferencia.id)
						.success(function(data, status, headers, config) {
							btn.button('reset');
							ng.transferencia = angular.copy(transferenciaTO);
							ng.showBoxNovo();
							ng.mensagens('alert-success','<b>transferência realizada com sucesso</b>','.alert-transferencia-lista');
							$('html,body').animate({scrollTop: 0},'slow');
							if(data.length == 1) 
								ng.listaTransferencias.transferencias[index_current_edit] = data[0];
							else
								ng.loadtransferencias(0,10);
							PrestaShop.send('post',baseUrlApi()+"prestashop/estoque",postPrestaShop);
						})
						.error(function(data, status, headers, config) {
							btn.button('reset');
							ng.transferencia = angular.copy(transferenciaTO);
							ng.showBoxNovo();
							ng.mensagens('alert-success','<b>transferência realizada com sucesso</b>','.alert-transferencia-lista');
							$('html,body').animate({scrollTop: 0},'slow');
				 			ng.loadtransferencias(0,10);
				 			PrestaShop.send('post',baseUrlApi()+"prestashop/estoque",postPrestaShop);
						});
					})
					.error(function(data, status, headers, config) {
						btn.button('reset');
						if(status == 406){
							$.each(data.out_estoque,function(i,x){	
								var msg = 'A quantidade a ser transferêncida ( '+x.qtd_transferida+' ) é maior que a em estoque ( '+x.qtd_estoque+' )';			
								$('#tr-prd-'+i).addClass('tr-out-estoque');
								$('#tr-prd-'+i).find('input').eq(0).attr("data-placement", "top").attr("title", msg).attr("data-original-title", msg); 
								$('#tr-prd-'+i).find('input').eq(0).tooltip();
							});
						}
					});	
				}, function(){
					btn.button('reset');
				}
			);
		}else{
			ng.transferencia.dta_recebido = moment().format('YYYY-MM-DD HH:mm:ss');
			ng.transferencia.id_usuario_recebeu = ng.userLogged.id ;
			var post = angular.copy(ng.transferencia);
			if(ng.transferencia.flg_controle_validade == 1){
				post.flg_controle_validade = 1 ;
				post.produtos = formatPostValidades() ;
			}

			aj.post(baseUrlApi()+"estoque/pedido/transferencia/receber/",{ form_data: JSON.stringify(post) })
			.success(function(data, status, headers, config) {
				aj.get(baseUrlApi()+"transferencias/estoque/?id_empreendimento_transferencia="+ng.userLogged.id_empreendimento+"&tte->id="+ng.transferencia.id)
				.success(function(data, status, headers, config) {
					btn.button('reset');
					ng.transferencia = angular.copy(transferenciaTO);
					ng.showBoxNovo();
					ng.mensagens('alert-success','<b>transferência realizada com sucesso</b>','.alert-transferencia-lista');
					$('html,body').animate({scrollTop: 0},'slow');
					if(data.length == 1) 
						ng.listaTransferencias.transferencias[index_current_edit] = data[0];
					else
						ng.loadtransferencias(0,10);
					PrestaShop.send('post',baseUrlApi()+"prestashop/estoque",postPrestaShop);
				})
				.error(function(data, status, headers, config) {
					btn.button('reset');
					ng.transferencia = angular.copy(transferenciaTO);
					ng.showBoxNovo();
					ng.mensagens('alert-success','<b>transferência realizada com sucesso</b>','.alert-transferencia-lista');
					$('html,body').animate({scrollTop: 0},'slow');
		 			ng.loadtransferencias(0,10);
		 			PrestaShop.send('post',baseUrlApi()+"prestashop/estoque",postPrestaShop);
				});
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406){
					$.each(data.out_estoque,function(i,x){	
						var msg = 'A quantidade a ser transferêncida ( '+x.qtd_transferida+' ) é maior que a em estoque ( '+x.qtd_estoque+' )';			
						$('#tr-prd-'+i).addClass('tr-out-estoque');
						$('#tr-prd-'+i).find('input').eq(0).attr("data-placement", "top").attr("title", msg).attr("data-original-title", msg); 
						$('#tr-prd-'+i).find('input').eq(0).tooltip();
					});
				}
			});
		}
	}

	ng.detalhesPedido = function(item){
		var caminho = baseUrlApi()+'relPDF?' + $.param({
			dados : {
				id_empreendimento : ng.userLogged.id_empreendimento,
				id_transferencia_estoque : item.id
			},
			template : 		'relatorio_transferencia_estoque'
		});
		
		eModal.setEModalOptions({ loadingHtml: '<div><div style="text-align: center;margin-top:5px;margin-bottom:3px"><span class="fa fa-circle-o-notch fa-spin fa-3x text-primary"></span></div><div style="text-align: center;"><span class="h4">Carregando, aguarde...</span></div></div>'});
		var title = 'Detalhes do Pedido';
        eModal
            .iframe({message:caminho, title:title,size:'lg'})
            .then(function () { t8.success('iFrame loaded!!!!', title) });
	}

	ng.showCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

		ng.loadCliente(offset,limit);
		$("#list_clientes").modal("show");
	}

	ng.loadCliente= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.usuarios = { itens:null, paginacao:[] };
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+"&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

		if(!empty(ng.busca.clientes)){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.clientes+"%' OR usu.apelido LIKE '%"+ng.busca.clientes+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.usuarios = { itens:data.usuarios, paginacao:data.paginacao }
			})
			.error(function(data, status, headers, config) {
				ng.usuarios = { itens:[], paginacao:[] }
			});
	}

	ng.addCliente = function(item){
		item = angular.copy(item);
		ng.busca.usuario_pedido = item;
		$("#list_clientes").modal("hide");
	}

	ng.showEmpreendimentosBusca = function() {
		ng.busca.empreendimento_busca ;
		$('#list_empreendimentos_busca').modal('show');
		ng.loadEmpreendimentosBusca(0,10);
	}

	ng.loadEmpreendimentosBusca = function(offset, limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?id_usuario="+ng.userLogged.id;
    	if(empty(!ng.busca.str_empreendimento_busca)){
    		query_string +="&" +$.param({nome_empreendimento:{exp:"like'%"+ng.busca.str_empreendimento_busca+"%'"}});
    	}

    	ng.empreendimentos_busca = {itens:null,paginacao:null};
		aj.get(baseUrlApi()+"empreendimentos/"+offset+"/"+limit+"/"+query_string)
		.success(function(data, status, headers, config) {
			ng.empreendimentos_busca = { itens:data.empreendimentos,paginacao:data.paginacao };
		})
		.error(function(data, status, headers, config) {
			ng.empreendimentos_busca = { itens:[],paginacao:[] };
		});
	}

	ng.addEmpreendimentoBusca = function(item){
		item = angular.copy(item);
		ng.busca.empreendimento_busca = item ;
		$('#list_empreendimentos_busca').modal('hide');
	}

	ng.somarQtdRecebida = function(item){
		var total = 0 ;
		$.each(item.validades,function(i,x){
			if($.isNumeric(x.qtd_recebida)){
				total += Number(x.qtd_recebida);
			}
		});
		item.qtd_recebida = total ;
		return total ;
	}

	ng.vericarQtdByValidade = function(item,container){
		var qtd_transferida = $.isNumeric(item.qtd_transferida) ? Number(item.qtd_transferida) : 0 ;
		var qtd_recebida    = $.isNumeric(item.qtd_recebida) ? Number(item.qtd_recebida) : 0 ;

		if(qtd_recebida > qtd_transferida){
			delete item.tooltip ;
			item.tooltip = {init:true,show:true,placement:'top',trigger:'focus hover',title:'Quantidade superior a transferida',container:(empty(container) ? null : container)} ;
			item.qtd_recebida = '' ;
		}else{
			delete item.tooltip ;
		}
	}

	function formatPostValidades(){
		var produtos = [];
		var transferencia_itens = angular.copy(ng.transferencia.produtos);
		$.each(transferencia_itens,function(i,x){
			var item = angular.copy(x)  ;
			delete item.validades ;
			$.each(x.validades,function(y,z){
				item.qtd_recebida = empty(z.qtd_recebida) ? 0 : z.qtd_recebida ;
				item.dta_validade = z.dta_validade ;
				produtos.push(angular.copy(item));
			});
		});
		return produtos ;
	}

	ng.clearTooltip = function(item){
		delete item.tooltip ;
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

   	ng.loadProdutosCodigoBarra = function(offset, limit) {
	
		ng.produtos = null;

 		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.transferencia.id_empreendimento_transferencia;
		if(!empty(ng.transferencia.id_deposito_padrao_pedido)){
			query_string += "&id_deposito_estoque="+ng.transferencia.id_deposito_padrao_pedido;
			// query_string += "&getQtdProduto(tpe->id_empreendimento,pro->id,null,"+ng.transferencia.id_deposito_padrao_pedido+",null)[exp]=>0";
		}
		query_string +="&pro->id[exp]= IN(SELECT tp.id FROM tbl_produtos AS tp INNER JOIN tbl_produto_empreendimento AS tpe ON tp.id = tpe.id_produto WHERE tpe.id_empreendimento IN ("+ng.userLogged.id_empreendimento+"))";

		query_string += "&(pro->codigo_barra[exp]=='"+ng.cod_barra_busca+"')";
		ng.cod_barra_busca = '' ;
		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				if(data.produtos.length == 1){
					var produto = angular.copy(data.produtos[0]);
					if(produto.qtd_item > 0) {
						ng.addProduto(produto,true);
						ng.addFocus();
					}else{
						$.noty.closeAll();
						var i = noty({
							timeout : 4000,
							layout: 'topRight',
							type: 'error',
							theme: 'relax',
							text: 'Produto sem estoque',
						});	
					}
				}
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					$.noty.closeAll();
					var i = noty({
						timeout : 4000,
						layout: 'topRight',
						type: 'error',
						theme: 'relax',
						text: 'Codigo de barra não corresponde a nenhum produto',
					});
				}else{
						$.noty.closeAll();
						var i = noty({
							timeout : 4000,
							layout: 'topRight',
							type: 'error',
							theme: 'relax',
							text: 'Ocorreu um erro ao buscar o produto',
						});
				}
			});
	}

	ng.loadtransferencias(0,10);
	ng.loadDepositosSelect();

	$('#list_produtos').on('shown.bs.modal', function () {
		$('#foco').focus()
	})

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
