app.controller('PedidoTransferenciaRecebidoController', function($scope, $http, $window, $dialogs, UserService,ConfigService,PrestaShop, FuncionalidadeService){
	var ng = $scope
		aj = $http;
	ng.ctrl = $scope ;
	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.configuracao = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.configuracao.flg_controlar_validade_transferencia =  _in(ng.configuracao.flg_controlar_validade_transferencia,[1,0]) ? ng.configuracao.flg_controlar_validade_transferencia : 0 ;
	
	ng.busca 		= {empreendimento:'',produto:'',depositos:''} ;
	ng.paginacao    = {};
    ng.lista_emp    = [];
    var transferenciaTO = {
    	id : null,
		id_empreendimento_pedido : null ,
		id_empreendimento_transferencia : ng.userLogged.id_empreendimento,
		id_usuario_pedido : null,
		id_usuario_transferencia : ng.userLogged.id,
		dta_pedido : null,
		dta_transferencia : null,
		id_status_transferencia : 2,
   		produtos:[]
	};
    ng.transferencia = angular.copy(transferenciaTO);
    ng.editing = false;
    ng.enviarNovaTransferencia = false ;

    ng.testeDep = [
    	{ nome:'Deposito 1',validade:'2016-10-10',qtd:10 },
    	{ nome:'Deposito 2',validade:'2016-10-05',qtd:5 }
    ];
    ng.teste="jheizer";

    var flg_error = 0;

    ng.funcioalidadeAuthorized = function(cod_funcionalidade){
    	val = FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
    	
		return val;
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

	ng.cancelar = function(){
		ng.showBoxNovo();
		ng.transferencia = angular.copy(transferenciaTO);
		ng.enviarNovaTransferencia = false ;
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

    	var query_string = "?id_usuario="+ng.userLogged.id; // +"&emp->id[exp]=<>"+ng.userLogged.id_empreendimento
    	if(ng.busca.empreendimento != ""){
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
	ng.addEmpreendimento = function(item) {
		if( !(ng.transferencia.id_empreendimento_pedido == item.id) )
			ng.transferencia.produtos = [] ;
		ng.transferencia.id_empreendimento_pedido = item.id;
		ng.transferencia.nome_empreendimento = item.nome_empreendimento ;
		$('#list_empreendimentos').modal('hide');
	}


	ng.showProdutos = function(){
		$('#list_produtos').modal('show');
		ng.busca.produto = "";
		ng.loadProdutos(0,10);
	}

	ng.loadProdutos = function(offset, limit) {
		ng.produtos = null;

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?tpe->id_empreendimento="+ng.transferencia.id_empreendimento_pedido;
		query_string +="&pro->id[exp]= IN(SELECT tp.id FROM tbl_produtos AS tp INNER JOIN tbl_produto_empreendimento AS tpe ON tp.id = tpe.id_produto WHERE tpe.id_empreendimento IN ("+ng.userLogged.id_empreendimento+"))";

		if($.isNumeric(ng.id_deposito_principal)){
			query_string +="&id_deposito_estoque="+ng.id_deposito_principal;
		}
		if(ng.busca.produto != ""){
			if(isNaN(Number(ng.busca.produto)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produto+"%' OR codigo_barra like '%"+ng.busca.produto+"%' OR fab.nome_fabricante like '%"+ng.busca.produto+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produto+"%' OR codigo_barra like '%"+ng.busca.produto+"%' OR fab.nome_fabricante like '%"+ng.busca.produto+"%' OR pro.id = "+ng.busca.produto+""}})+")";
		}

		aj.get(baseUrlApi()+"produtos/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos = data.produtos;
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

	ng.addProduto = function(item){
		var produto = angular.copy(item) ;
		produto.id_produto = item.id ;
		produto.qtd_pedida = empty(produto.qtd_pedida) ? 0 : produto.qtd_pedida  ;
		produto.qtd_transferida = (!empty(produto.qtd_transferida)) ? produto.qtd_transferida : 0 ;
		produto.add 	   = item.add == 0 ? 0 : 1 ;

		produto.vlr_custo_real = item.vlr_custo_real ;
		produto.vlr_venda_atacado = item.vlr_venda_atacado ;
		produto.vlr_venda_intermediario = item.vlr_venda_intermediario ;
		produto.vlr_venda_varejo = item.vlr_venda_varejo ;

		if(empty(item.tipo_vlr_custo)){
			produto.vlr_custo = item.vlr_custo_real ;
			produto.tipo_vlr_custo = 'vlr_custo_real' ;
		}else{
			produto.vlr_custo = item[item.tipo_vlr_custo] ;
			produto.tipo_vlr_custo = item.tipo_vlr_custo ;
		}
	
		if(empty(ng.transferencia.id) && ng.enviarNovaTransferencia){
			produto.qtd_transferida = produto.qtd_pedida;
			produto.qtd_pedida = 0;
		}
		ng.transferencia.produtos.push(produto);
		item.qtd_pedida = null;
	}

	ng.deletarTransferencia = function(item){
		aj.get(baseUrlApi()+"transferencia/estoque/"+ item.id +"/excluir")
			.success(function(data, status, headers, config) {
				ng.loadtransferencias(0,10);
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.listaTransferencias = {} ;
	ng.loadtransferencias = function(offset, limit){
		ng.listaTransferencias.transferencias = null 
		aj.get(baseUrlApi()+"transferencias/estoque/"+offset+"/"+limit+"?cplSql=id_empreendimento_transferencia="+ng.userLogged.id_empreendimento+" AND id_status_transferencia <> 4 AND flg_excluido = 0 ORDER BY id  DESC")
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

	ng.selDeposito = function(){
		$('#list_depositos').modal('show');
		ng.loadDepositos(0,10);
	}
	ng.addDeposito = function(item){
		ng.nome_deposito_principal = item.nme_deposito;
		ng.id_deposito_principal = item.id;
		$('#list_depositos').modal('hide');
		$.each(ng.transferencia.produtos,function(i,x){
			ng.transferencia.produtos[i].id_deposito_saida = item.id;
		});
		ng.verificarEstoque();
		ng.loadestoque();
	}

	ng.loadestoque = function(item){
		if(item == null){
			$.each(ng.transferencia.produtos,function(i,x){
				ng.transferencia.produtos[i].load_estoque = true;
			});	
			var prd_in = ''; aux = [];
			$.each(ng.transferencia.produtos,function(i,x){
				prd_in += x.id+",";
			});
			prd_in = prd_in.substring(0,prd_in.length-1);	
			var id_deposito_principal = ng.id_deposito_principal ;
		}else{
			var id_deposito_principal = item.id_deposito_saida ;
			var prd_in = item.id;
			item.load_estoque = true ;
		}
		
		aj.get(baseUrlApi()+"produtos?pro->id[exp]=IN("+prd_in+")&tpe->id_empreendimento="+ng.userLogged.id_empreendimento+"&id_deposito_estoque="+id_deposito_principal)
		.success(function(prd, status, headers, config) {
			if(item == null){
				$.each(prd.produtos,function(i,x){
					$.each(ng.transferencia.produtos,function(y,z){
						if(Number(x.id) == Number(z.id)){
							ng.transferencia.produtos[y].qtd_item =  x.qtd_item;
							ng.transferencia.produtos[y].load_estoque = false ;
						}
					});
				});
			}else{
				item.qtd_item = prd.produtos[0].qtd_item ;
				item.load_estoque = false ;
			}
		})
		.error(function(data, status, headers, config) {
			
		});
		
	}

	ng.loadDepositos = function(offset, limit) {
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
			if(ng.depositos.length == 1){
				ng.estoqueSaida.nome_deposito = ng.depositos[0].nme_deposito;
				ng.estoqueSaida.id_deposito   = ng.depositos[0].id;
			}
			
		})
		.error(function(data, status, headers, config) {
			ng.depositos = [] ;	
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

	ng.verificarEstoque = function(){
		$('.tr-out-estoque').find('input').tooltip('destroy');
		$('.tr-out-estoque').removeClass('tr-out-estoque');
		$('.has-error').find('input').tooltip('destroy');

		$('.has-error').find('.chosen-single').attr('style','');
		$('.has-error').find('.chosen-single').find('span').attr('style','');
		$('.has-error').find('.chosen-single').tooltip('destroy');

		$('.has-error').removeClass('has-error');

		var post = angular.copy(ng.transferencia);
		var produtos = angular.copy(ng.transferencia.produtos);
		post.produtos = [];
		$.each(produtos,function(i,v){
			v.qtd_transferida = v.qtd_pedida ;
			post.produtos.push(v);
		});

		aj.post(baseUrlApi()+"estoque/pedido/transferencia/verificar_estoque/",post)
		.success(function(data, status, headers, config) {
			
		})
		.error(function(data, status, headers, config) {
			if(status == 406){
				$.each(data.out_estoque,function(i,x){	
					var msg = 'A quantidade solicitada ( '+x.qtd_transferida+' ) é maior que a em estoque ( '+x.qtd_estoque+' )';			
					$('#tr-prd-'+i).addClass('tr-out-estoque');
					$('#tr-prd-'+i).find('input').eq(0).attr("data-placement", "top").attr("title", msg).attr("data-original-title", msg); 
					$('#tr-prd-'+i).find('input').eq(0).tooltip();
					$.each(ng.transferencia.produtos,function(x,y){
						if(Number(i) == Number(y.id)){
							y.id_deposito_saida = null ;
						}
					});
				});
			}
		});
	}

	ng.verificaQtdMultiplo = function(){
		if (ng.transferencia.produtos.length != 0) {
			angular.forEach(ng.transferencia.produtos, function(item, index){
				if (item.qtd_multiplo_transferencia != "" || item.qtd_multiplo_transferencia != null) {
					if ((item.qtd_transferida % item.qtd_multiplo_transferencia) > 0) {
						$("#produtos").addClass("has-error");
						var formControl = $('#produtos .input-group')
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "top")
							.attr("title", 'Apenas envio em multiplo de: ' + item.qtd_multiplo_transferencia)
						formControl.tooltip();
						flg_error = 1;
					} else{
						$($(".has-error").find(".form-control")).tooltip('destroy');
						$(".has-error").removeClass("has-error");
						flg_error = 0;
					}
				}
			});
		}
	}

	ng.salvarTransferencia = function(){
		if (flg_error == 1) {
			return false;
		}
		var btn = $('#salvar-transferencia') ;
		btn.button('loading');
		$('.tr-out-estoque').find('input').tooltip('destroy');
		$('.tr-out-estoque').removeClass('tr-out-estoque');
		$('.has-error').find('input').tooltip('destroy');

		$('.has-error').find('.chosen-single').attr('style','');
		$('.has-error').find('.chosen-single').find('span').attr('style','');
		$('.has-error').find('.chosen-single').tooltip('destroy');

		$('.has-error').removeClass('has-error');
		var error = 0 ;
		/*if(!$.isNumeric(ng.id_deposito_principal)){
			$('#id_deposito_principal').addClass('has-error');
			$('#id_deposito_principal').find('.input-group').attr("data-placement", "top").attr("title", 'Informe o deposito').attr("data-original-title", 'Informe o deposito'); 
			$('#id_deposito_principal').find('.input-group').tooltip('show');	
			$('html,body').animate({scrollTop: 0},'slow');
			error ++ ;
		}*/
		var postPrestaShop = {produtos:[]}; 
		$.each(ng.transferencia.produtos,function(key,item){
			postPrestaShop.produtos.push(item.id_produto);
			if(!($.isNumeric(item.qtd_transferida))){
				$('#td-prd-'+item.id).addClass('has-error');
				$('#td-prd-'+item.id).find('input').attr("data-placement", "top").attr("title", 'A quantidade para transferência não poder ser vazio').attr("data-original-title", 'A quantidade para transferência não poder ser vazia'); 
				if(error == 0) {
					$('#td-prd-'+item.id).find('input').tooltip('show');
					$('html,body').animate({scrollTop: $('#td-prd-'+item.id).offset().top - 100 },'slow');
				}else {
					$('#td-prd-'+item.id).find('input').tooltip();
				}
				error ++ ;
			}
			if(!($.isNumeric(item.id_deposito_saida)) && ng.configuracao.flg_controlar_validade_transferencia == 0){
				$('#td-prd-deposito-saida-'+item.id).addClass('has-error');
				$('#td-prd-deposito-saida-'+item.id).find('.chosen-single').attr("data-placement", "top").attr("title", 'Informe o deposito de saida').attr("data-original-title", 'A quantidade para transferência não poder ser vazia'); 
				$('#td-prd-deposito-saida-'+item.id).find('.chosen-single').attr('style','border: 1px solid #A94442;');
				$('#td-prd-deposito-saida-'+item.id).find('.chosen-single').find('span').attr('style','color:#A94442;');
				if(error == 0) {
					$('#td-prd-deposito-saida-'+item.id).find('.chosen-single').tooltip('show');
					$('html,body').animate({scrollTop: $('#td-prd-deposito-saida-'+item.id).offset().top - 100 },'slow');
				}else {
					$('#td-prd-deposito-saida-'+item.id).find('.chosen-single').tooltip();
				}
				error ++ ;
			}

		});

		if(error > 0){
			btn.button('reset');
			return ;
		}
		ng.transferencia.dta_transferencia = moment().format('YYYY-MM-DD HH:mm:ss');
		var post = angular.copy(ng.transferencia);
		if(ng.configuracao.flg_controlar_validade_transferencia == 1){
			post.flg_controle_validade = 1 ;
			post.produtos = ng.formatPostValidades() ;
		}

		aj.post(baseUrlApi()+"estoque/pedido/transferencia/transferir/", { form_data: JSON.stringify(post) })
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

	var index_current_edit = null ;
	ng.editTransferencia = function(index,event){
		ng.enviarNovaTransferencia = ng.listaTransferencias.transferencias[index].id_status_transferencia == 5 ? true : false ;
		var id = ng.listaTransferencias.transferencias[index].id ;
		index_current_edit = index ;
		var btn = $(event.target) ;
		ng.transferencia = angular.copy(transferenciaTO);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');
		ng.view.transferencia  = null 
		aj.get(baseUrlApi()+"transferencia/estoque/"+id)
		.success(function(data, status, headers, config) {
			var prd_in = ''; aux = [];
			$.each(data.itens,function(i,x){
				prd_in += x.id_produto+",";
				if(ng.listaTransferencias.transferencias[index].flg_controle_validade == 1 || ng.configuracao.flg_controlar_validade_transferencia == 1){
					if(empty(aux[x.id_produto])){
						aux[x.id_produto] = { 
						qtd_pedida : x.qtd_pedida,
						qtd_transferida : x.qtd_transferida ,
						id_deposito_saida : x.id_deposito_saida,
						vlr_custo : x.vlr_custo,
						tipo_vlr_custo : x.tipo_vlr_custo,
						validades : [{ dta_validade : x.dta_validade, id_deposito : x.id_deposito_saida, qtd_transferida : x.qtd_transferida  }]
					};
					}else{
						aux[x.id_produto].validades.push({ dta_validade : x.dta_validade, id_deposito : x.id_deposito_saida, qtd_transferida : x.qtd_transferida  });
					}
				}else{
					aux[x.id_produto] = { 
						qtd_pedida : x.qtd_pedida,
						qtd_transferida : x.qtd_transferida ,
						id_deposito_saida : x.id_deposito_saida,
						vlr_custo : x.vlr_custo,
						tipo_vlr_custo : x.tipo_vlr_custo
					};
				}
			});
			prd_in = prd_in.substring(0,prd_in.length-1);	
			aj.get(baseUrlApi()+"produtos?pro->id[exp]=IN("+prd_in+")&tpe->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(prd, status, headers, config) {
				if(ng.enviarNovaTransferencia)
					ng.addEmpreendimento(data.empreendimento_pedido);
				ng.transferencia.id = data.id ;
				ng.transferencia.id_empreendimento_pedido = data.id_empreendimento_pedido ;
				ng.transferencia.id_empreendimento_transferencia = data.id_empreendimento_transferencia ;
				ng.transferencia.flg_controle_validade = ng.configuracao.flg_controlar_validade_transferencia ;
				//ng.transferencia.id_usuario_pedido = data.id_usuario_pedido ;
				ng.transferencia.id_usuario_transferencia = ng.userLogged.id ;
				//ng.transferencia.dta_pedido = data.dta_pedido ;
				//ng.transferencia.dta_transferencia = data.dta_transferencia ;
				ng.transferencia.id_status_transferencia = 2 ;
				ng.transferencia.produtos = [];
				ng.showBoxNovo(true);
				
				$.each(prd.produtos,function(x,i){ prd.produtos[x].id_item = aux[i.id_produto].id_item; });

				prd.produtos = _.sortBy(prd.produtos,'nome');
				$.each(prd.produtos,function(x,i){
					i.qtd_pedida = aux[i.id_produto].qtd_pedida;
					i.qtd_transferida = aux[i.id_produto].qtd_transferida;
					i.id_deposito_saida = ""+aux[i.id_produto].id_deposito_saida;
					i.vlr_custo =  Number(aux[i.id_produto].vlr_custo) ;
					i.tipo_vlr_custo = aux[i.id_produto].tipo_vlr_custo ;
					if(empty(i.tipo_vlr_custo)){
						i.vlr_custo =  i.vlr_custo_real ;
						i.tipo_vlr_custo = 'vlr_custo_real' ;
					}
					i.load_estoque = false;
					i.add = 0 ;
					if(ng.transferencia.flg_controle_validade == 1){
						i.validades = aux[i.id_produto].validades;
						i.id_produto  = i.id_produto ;
						i.nome_produto  = i.nome ;
						if(data.id_status_transferencia != 1)
							ng.addProdutoByValidade(i,true);
						else	
							ng.addProdutoByValidade(i);
					}
					else
						ng.addProduto(i);
				});
				btn.button('reset');
				$('html,body').animate({scrollTop: 0},'slow');

				setTimeout(function(){
					$scope.$apply();
				},500);
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

	ng.openNovaTransferencia = function(){
		ng.enviarNovaTransferencia = true;
		ng.showBoxNovo();
	}

	ng.salvarNovaTransferencia = function(id_status_transferencia,event){
		if (flg_error == 1) {
			return false;
		}
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		var btn = $(event.target);
		if(!btn.is(':button')) btn = $(event.target).parent();
		btn.button('loading');
		var error = 0 ;

		if(!$.isNumeric(ng.transferencia.id_empreendimento_pedido)){
			$("#id_empreendimento_transferencia").addClass("has-error");
			var formControl = $('#id_empreendimento_transferencia .input-group')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "top")
				.attr("title", 'Informe o empreendimento que deseja enviar a transferência')
				.attr("data-original-title", 'Informe o empreendimento que deseja enviar a transferência');
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
			if(!($.isNumeric(item.qtd_transferida))){
				$('#td-prd-'+item.id).addClass('has-error');
				$('#td-prd-'+item.id).find('input').attr("data-placement", "top").attr("title", 'A quantidade para transferência não poder ser vazio').attr("data-original-title", 'A quantidade para transferência não poder ser vazia'); 
				if(error == 0) {
					$('#td-prd-'+item.id).find('input').tooltip('show');
					$('html,body').animate({scrollTop: $('#td-prd-'+item.id).offset().top - 100 },'slow');
				}else {
					$('#td-prd-'+item.id).find('input').tooltip();
				}
				error ++ ;
			}
			if(!($.isNumeric(item.id_deposito_saida)) && ng.configuracao.flg_controlar_validade_transferencia == 0){
				$('#td-prd-deposito-saida-'+item.id).addClass('has-error');
				$('#td-prd-deposito-saida-'+item.id).find('.chosen-single').attr("data-placement", "top").attr("title", 'Informe o deposito de saida').attr("data-original-title", 'A quantidade para transferência não poder ser vazia'); 
				$('#td-prd-deposito-saida-'+item.id).find('.chosen-single').attr('style','border: 1px solid #A94442;');
				$('#td-prd-deposito-saida-'+item.id).find('.chosen-single').find('span').attr('style','color:#A94442;');
				if(error == 0) {
					$('#td-prd-deposito-saida-'+item.id).find('.chosen-single').tooltip('show');
					$('html,body').animate({scrollTop: $('#td-prd-deposito-saida-'+item.id).offset().top - 100 },'slow');
				}else {
					$('#td-prd-deposito-saida-'+item.id).find('.chosen-single').tooltip();
				}
				error ++ ;
			}

		});

		if(error > 0){
			btn.button('reset'); 
			return ;
		}
		if(id_status_transferencia == 2) ng.transferencia.dta_transferencia = moment().format('YYYY-MM-DD HH:mm:ss');
		ng.transferencia.id_status_transferencia = id_status_transferencia ;
		var post = angular.copy(ng.transferencia);

		var url ;
		if($.isNumeric(ng.transferencia.id))
			url = 'estoque/pedido/transferencia/edit';
		else
			url = 'estoque/pedido/transferencia';

		if(ng.configuracao.flg_controlar_validade_transferencia == 1){
			post.flg_controle_validade = 1 ;
			post.produtos = ng.formatPostValidades() ;
		}

		aj.post(baseUrlApi()+url,{ form_data: JSON.stringify(post)})
		.success(function(data, status, headers, config) {
			btn.button('reset'); 
			ng.transferencia = angular.copy(transferenciaTO);
			ng.showBoxNovo();
			ng.mensagens('alert-success','<b>Pedido de transferência realizado com sucesso</b>','.alert-transferencia-lista');
			$('html,body').animate({scrollTop: 0},'slow');
			ng.loadtransferencias(0,10);

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
			}else{
				ng.mensagens('alert-danger','<b>Ocorreu um erro ao realizar o pedido</b>','.alert-transferencia-form');
				$('html,body').animate({scrollTop: 0},'slow');
			}
		});
	}

	ng.formatPostValidades = function(){
		var produtos = [] ;
		$.each(ng.transferencia.produtos,function(y,z){
			if(!empty(z.qtd_transferida)){
				$.each(z.validades,function(i,x){
					if(!empty(x.qtd_transferida)){
						produtos.push({
							id : Number(x.id_produto),
							id_produto : Number(x.id_produto),
							dta_validade : x.dta_validade,
							id_deposito_saida : Number(x.id_deposito),
							qtd_transferida : Number(x.qtd_transferida),
							qtd_pedida:z.qtd_pedida,
							vlr_custo : z.vlr_custo,
							tipo_vlr_custo : z.tipo_vlr_custo,
							add : 1
						});
					}
				});	
			}else{
				produtos.push({
							id : Number(z.id_produto),
							id_produto : Number(z.id_produto),
							dta_validade :'2099-31-12',
							id_deposito_saida : null ,
							qtd_transferida : 0 ,
							qtd_pedida:z.qtd_pedida,
							vlr_custo : z.vlr_custo,
							tipo_vlr_custo : z.tipo_vlr_custo,
							add : 1
						});
			}
		});
		return produtos ;
	}

	ng.setarVlrCusto = function(item,tipo){
		if(!empty(item)){
			item.tipo_vlr_custo = tipo ;
			item.vlr_custo = item[tipo] ;
		}else if(empty(item)){
			$.each(ng.transferencia.produtos,function(i,x){
				ng.transferencia.produtos[i].tipo_vlr_custo = tipo ;
				ng.transferencia.produtos[i].vlr_custo = x[tipo] ;
			});
		}
	}


	ng.showProdutosByValidade = function(){
		$('#list_produtos').modal('show');
		ng.busca.produto = "";
		ng.loadProdutosByValidade(0,10);
	}


	ng.loadProdutosByValidade = function(offset, limit) {
		ng.produtos = null;

		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?emp->id_empreendimento="+ng.userLogged.id_empreendimento;
		query_string +="&prd->id[exp]= IN(SELECT tp.id FROM tbl_produtos AS tp INNER JOIN tbl_produto_empreendimento AS tpe ON tp.id = tpe.id_produto WHERE tpe.id_empreendimento IN ("+ng.transferencia.id_empreendimento_pedido+"))";

		if($.isNumeric(ng.id_deposito_principal)){
			query_string +="&id_deposito_estoque="+ng.id_deposito_principal;
		}
		if(ng.busca.produto != ""){
			if(isNaN(Number(ng.busca.produto)))
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produto+"%' OR codigo_barra like '%"+ng.busca.produto+"%' OR fab.nome_fabricante like '%"+ng.busca.produto+"%'"}})+")";
			else
				query_string += "&("+$.param({nome:{exp:"like '%"+ng.busca.produto+"%' OR codigo_barra like '%"+ng.busca.produto+"%' OR fab.nome_fabricante like '%"+ng.busca.produto+"%' OR prd.id = "+ng.busca.produto+""}})+")";
		}

		aj.get(baseUrlApi()+"estoque/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos = GroupBy(data.produtos, "id_produto");
				
				ng.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404) {
					ng.produtos = [];
					ng.paginacao.produtos = null;
				}
			});
	}

	ng.somarQtd = function(item){
		if(empty(item.validades)) return 0 ;
		var total = 0 ;
		$.each(item.validades,function(i,x){
			if($.isNumeric(x.qtd_transferida)){
				total += Number(x.qtd_transferida);
			}
		});
		item.qtd_transferida = total ;
		return total ;
	}

	ng.clearTooltip = function(item){
		delete item.tooltip ;
	}

	ng.vericarQtdByValidade = function(item,container){
		var qtd_item = $.isNumeric(item.qtd_item) ? Number(item.qtd_item) : 0 ;
		var qtd_transferida = $.isNumeric(item.qtd_transferida) ? Number(item.qtd_transferida) : 0 ;

		if(qtd_transferida > qtd_item){
			delete item.tooltip ;
			item.tooltip = {init:true,show:true,placement:'top',trigger:'focus hover',title:'Quantidade superior a em estoque ',container:(empty(container) ? null : container)} ;
			item.qtd_transferida = '' ;
		}else{
			delete item.tooltip ;
		}
	}

	ng.produtoSelectedByValidade = function(id){
		var r = false ;
		$.each(ng.transferencia.produtos,function(i,x){
			var index_validade = getIndex('id',id,x.validades);
			if( index_validade != null && !empty(x.validades[index_validade].qtd_transferida)){
				r = true ;
				return false ;
			}
		});
		return r ;
	}

	ng.excluirProdutoListaByValidade = function(index){
		ng.transferencia.produtos.splice(index,1);
	}

	ng.addProdutoByValidade = function(item,edit){
		edit = empty(edit) ? false : edit ;
		var index_produto = getIndex('id_produto',item.id_produto,ng.transferencia.produtos) ;
		if(index_produto != null ){
			var index_validade = getIndex({dta_validade:item.dta_validade,id_deposito:item.id_deposito},null,ng.transferencia.produtos[index_produto].validades);
			ng.transferencia.produtos[index_produto].validades[index_validade].qtd_transferida = item.qtd_transferida ;
			item.qtd_transferida = null ;
			return ;
		}

		aj.get(baseUrlApi()+"estoque/?prd->id="+item.id_produto+"&emp->id_empreendimento="+ng.userLogged.id_empreendimento)
		.success(function(data, status, headers, config) {
			var produto = angular.copy(item) ;

			if(!edit){
				var index_validade = getIndex({dta_validade:item.dta_validade,id_deposito:item.id_deposito},null,data.produtos);
				if($.isNumeric(index_validade))
					data.produtos[index_validade].qtd_transferida = item.qtd_transferida ;
				produto.validades = data.produtos ;
			}else{
				$.each(item.validades,function(a_validade,b_validade){
					var index_validade = getIndex({dta_validade:b_validade.dta_validade,id_deposito:b_validade.id_deposito},null,data.produtos);
					data.produtos[index_validade].qtd_transferida = b_validade.qtd_transferida ;
				});
				produto.validades = data.produtos ;	
			}

			item.qtd_transferida = null ;

			produto.id         = item.id_produto ;
			produto.id_produto = item.id_produto ;
			produto.nome       = item.nome_produto ;
			produto.qtd_pedida = empty(produto.qtd_pedida) ? 0 : produto.qtd_pedida  ;
			produto.add 	   = item.add == 0 ? 0 : 1 ;
	
			produto.vlr_custo_real = item.vlr_custo_real ;
			produto.vlr_venda_atacado = item.vlr_venda_atacado ;
			produto.vlr_venda_intermediario = item.vlr_venda_intermediario ;
			produto.vlr_venda_varejo = item.vlr_venda_varejo ;

			if(empty(item.tipo_vlr_custo)){
				produto.vlr_custo = item.vlr_custo_real ;
				produto.tipo_vlr_custo = 'vlr_custo_real' ;
			}else{
				produto.vlr_custo = item[item.tipo_vlr_custo] ;
				produto.tipo_vlr_custo = item.tipo_vlr_custo ;
			}
		
			if(ng.enviarNovaTransferencia){
				produto.qtd_transferida = produto.qtd_pedida;
				produto.qtd_pedida = 0 ;
			}
			ng.transferencia.produtos.push(produto);
			item.qtd_pedida = null ;
		})
		.error(function(data, status, headers, config) {
			if(status!=404)
				alert('Ocorreu um erro');
			var produto = angular.copy(item) ;
			produto.id = item.id_produto ;
			produto.id_produto = item.id_produto ;
			produto.nome = item.nome_produto ;
			produto.qtd_pedida = empty(produto.qtd_pedida) ? 0 : produto.qtd_pedida  ;
			produto.add 	   = item.add == 0 ? 0 : 1 ;
	
			produto.vlr_custo_real = item.vlr_custo_real ;
			produto.vlr_venda_atacado = item.vlr_venda_atacado ;
			produto.vlr_venda_intermediario = item.vlr_venda_intermediario ;
			produto.vlr_venda_varejo = item.vlr_venda_varejo ;

			if(empty(item.tipo_vlr_custo)){
				produto.vlr_custo = item.vlr_custo_real ;
				produto.tipo_vlr_custo = 'vlr_custo_real' ;
			}else{
				produto.vlr_custo = item[item.tipo_vlr_custo] ;
				produto.tipo_vlr_custo = item.tipo_vlr_custo ;
			}
		
			if(ng.enviarNovaTransferencia){
				produto.qtd_transferida = produto.qtd_pedida;
				produto.qtd_pedida = 0 ;
			}
			ng.transferencia.produtos.push(produto);
			item.qtd_pedida = null ;
		});
	}

	ng.isNumeric = function(vlr){
		return $.isNumeric(vlr);
	}


	ng.loadtransferencias(0,10);
	ng.loadDepositosSelect();

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

$("body").on("mouseenter","tr.tr-out-estoque",function() {
  $(this).find('input').eq(0).focus();
});

$("body").on("mouseleave","tr.tr-out-estoque",function() {
  $(this).find('input').eq(0).blur();
});
