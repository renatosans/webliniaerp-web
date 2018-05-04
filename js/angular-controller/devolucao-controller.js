app.controller('DevolucaoController', function($scope, $http, $window,$dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 				= baseUrl();
	ng.userLogged 			= UserService.getUserLogado();
	ng.id_venda				= null ;
	ng.itens_venda          = null ;
	ng.busca_return_empty   = false ;
	ng.view					= { busca_return_empty : false,cadastar_cliente:false,cadastro_novo_cliente:false } ;
	ng.cliente              = {tipo_cadastro:'pf'};
	ng.busca                = {clientes:'',nome: "",id_venda: ""};
	ng.clientes             = [];
	ng.cliente_selecionado  = {} ;
	ng.paginacao = { itens: [] } ;

	ng.showBoxNovaDevolucao = function(onlyShow){
    	//ng.editing = !ng.editing;

    	if(onlyShow) {
			$('i','#btn-nova-devolucao').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo-pedido').show();
		}
		else {
			$('#box-novo-pedido').toggle(400, function(){
				if($(this).is(':visible')){
					$('i','#btn-nova-devolucao').removeClass("fa-plus-circle").addClass("fa-minus-circle");
				}else{
					$('i','#btn-nova-devolucao').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
			});
		}
	}

	ng.loadVenda = function(id_venda) {
		if(empty(id_venda))
			return ;

		ng.itens_venda = [];
		ng.venda       				  = null ;
		ng.view.cadastar_cliente 	  = false ;
		ng.view.busca_return_empty    = false ;
		ng.cliente_selecionado        = {} ;

		aj.get(baseUrlApi()+"devolucao/venda/"+id_venda+"/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
					if(ng.configuracoes.id_cliente_movimentacao_caixa == data.venda.id_cliente){
						ng.view.cadastar_cliente = true ;
					}

					ng.itens_venda 	= data.itens ;
					ng.venda    	= data.venda ;

			})
			.error(function(data, status, headers, config) {
				ng.view.busca_return_empty = true ;
				ng.view.busca_id_empty    = ng.id_venda ;
			});
	}

	ng.loadConfig = function(){
		var error = 0 ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracoes = data ;
				ng.loadPerfis();
			})
			.error(function(data, status, headers, config) {
					ng.caixa_configurado = false ;
			});
	}

	ng.showModalCastroCliente = function(){
		$('#modal-cadastro_cliente').modal({
		  backdrop: 'static',
		  keyboard: false
		});
		$('.modal-backdrop.in').css({opacity:1,'background-color':'#C7C7C7'});
	}

	ng.perfisCadastro = [] ;
	ng.loadPerfis = function() {
		aj.get(baseUrlApi()+"perfis?tpue->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				var aux = typeof parseJSON(ng.configuracoes.perfis_cadastro_rapido) == 'object' ?  parseJSON(ng.configuracoes.perfis_cadastro_rapido) : [] ;
				var perfis = [];
				$.each(data,function(i,x){
					index = getIndex('id',data[i].id,data);
					if($.isNumeric(index)){
						if(aux[index].value == 1)
							perfis.push(x);
					}
				});
				ng.perfisCadastro = perfis ;
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
				ng.perfis = [] ;
			});
	}

	ng.removeError = function(){
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").tooltip('destroy');
		$(".has-error").css({border:"none",background: 'none'}).addClass('has-error');
		$(".has-error").parent().css({'background':'none'});
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.salvarCliente = function(){
		ng.removeError();
		ng.cliente.empreendimentos = [{id:ng.userLogged.id_empreendimento}];
		ng.cliente.id_empreendimento = ng.userLogged.id_empreendimento;
		var btn = $('#btn-salvar-cliente');
		btn.button('loading');
		aj.post(baseUrlApi()+"cliente/cadastro/rapido",ng.cliente)
		.success(function(data, status, headers, config) {
			btn.button('reset');
			ng.view.cadastar_cliente = true ;
			ng.view.cadastro_novo_cliente = false ;
			ng.cliente_selecionado = {id:data.id,nome:ng.cliente.nome} ;
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			if(status == 406) {
		 			var errors = data;

		 			$.each(errors, function(i, item) {
		 				$("#"+i).addClass("has-error");

		 				var formControl = $($("#"+i))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", item)
		 					.attr("data-original-title", item);
		 				formControl.tooltip();

		 				if(i == "email_marketing" || i == "indicacao"){
		 					$("#"+i).parent().css({'background':'#E9D8D7'});
		 					$("#"+i).parent().attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", item)
		 					.attr("data-original-title", item);
		 					$("#"+i).parent().tooltip();
		 				}
		 			});
		 		}
		});
	}

	ng.loadClientes = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.clientes = [] ;
		var query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+")";

		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({"(usu->nome":{exp:"like'%"+ng.busca.clientes+"%' OR tpf.cpf LIKE '%"+ng.busca.clientes+"%' OR tpj.nome_fantasia LIKE '%"+ng.busca.clientes+"%' OR tpj.nome_fantasia LIKE '%"+ng.busca.clientes+"%' OR apelido like '%"+ng.busca.clientes+"%')"}})+"";
		}

		aj.get(baseUrlApi()+"usuarios/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.clientes = [];
				ng.paginacao.itens = data.paginacao;
				$.each(data.usuarios,function(i, item){
					item.id_como_encontrou = item.id_como_encontrou == null || item.id_como_encontrou == "" ? 'outros' : item.id_como_encontrou ;
					ng.clientes.push(item);
				});
				ng.loadSaldoDevedorClientes();
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.clientes = [];
			});
	}

	ng.selCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

			ng.loadCliente(offset,limit);
			$("#list_clientes").modal("show");
	}


	ng.addCliente = function(item){
    	ng.cliente_selecionado = item;
    	$("#list_clientes").modal("hide");
	}

	ng.loadCliente= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+")";

		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.clientes+"%' OR tpf.cpf LIKE '%"+ng.busca.clientes+"%' OR tpj.nome_fantasia LIKE '%"+ng.busca.clientes+"%' OR tpj.cnpj LIKE '%"+ng.busca.clientes+"%' OR usu.apelido LIKE '%"+ng.busca.clientes+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.clientes = data.usuarios;
				ng.paginacao_clientes = data.paginacao;

			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.atualizarRegistros = function(id_venda,id_cliente){
		id_venda   = Number(id_venda);
		id_cliente = Number(id_cliente);

		if(empty(ng.cliente_selecionado.id)){
			$("#busca_cliente").addClass("has-error");

		 				var formControl = $($("#busca_cliente"))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", 'Selecione um cliente')
		 					.attr("data-original-title", 'Selecione um cliente');
		 				formControl.tooltip();
		 	return;
		}

		var btn = $('#btn-salvar-vincular-cliente');
		btn.button('loading');

		aj.get(baseUrlApi()+"devolucao/atualizar/"+id_venda+"/"+id_cliente+"/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.itens_venda 	= data.itens ;
				ng.venda    	= data.venda ;
				ng.cliente_selecionado  = {} ;
				ng.view.cadastar_cliente = false ;
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
			});
	}

	ng.comparaQtd = function(item,index){
		
		var qtd 				= Number(item.qtd);
		var qtd_devolvida 		= Number(item.qtd_devolvida);
		var qtd_devolvida_real 	= Number(item.qtd_devolvida_real);

		if(qtd_devolvida > (qtd-qtd_devolvida_real)){
			var formControl = $($("#qtd-devolvida-"+index))
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'A quatidade da devolução não pode ser maior que a vendida')
				.attr("data-original-title", 'A quatidade da devolução não pode ser maior que a vendida');
			formControl.tooltip('show');
			$('#qtd-devolvida-'+index).attr('style','') ;
			setTimeout(function(){
				$("#qtd-devolvida-"+index).tooltip('destroy');
			}, 4000);
			item.qtd_devolvida = null ;
		}
	}

	ng.removeItemDevolucao = function(index){
		ng.itens_venda.splice(index,1);
	}

	ng.lancarDevolucao = function(){
		ng.removeError();
		var error = 0 ;
		var btn = $('#btn-lancar-devolucao');
		btn.button('loading');
		var msg_erro = '' ;

		var itens_validos = [] ;

		$.each(ng.itens_venda,function(i,v){
			if( ( !empty(v.dta_devolvida) || (empty(v.dta_devolvida) && v.flg_controlar_validade == 0)  ) && !empty(v.qtd_devolvida)){
				itens_validos.push(angular.copy(v));
			}else{
				error ++ ;
				if( empty(v.dta_devolvida) && v.flg_controlar_validade == 1 ){
					$("#dta_validade-devolvida-"+i)
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "top")
					.attr("title", 'informe a validade')
					.attr("data-original-title", 'informe a validade')
					.tooltip()
					.parent()
					.addClass("has-error");
				}
				if(empty(v.qtd_devolvida)){
					$("#qtd-devolvida-"+i)
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "top")
					.attr("title", 'informe a quantidade')
					.attr("data-original-title", 'informe a quantidade')
					.tooltip()
					.parent()
					.addClass("has-error");
				}
			}

		});

	
		if(error > 0){
			ng.mensagens('alert-warning','<strong>É necessario informar a quantidade e a data de validade para os produtos que serão devolvidos</strong>','.alert-validade');
			btn.button('reset');
			return false ;
		}

		$.each(itens_validos,function(i,v){
			if(empty(v.dta_devolvida)){
				itens_validos[i].dta_validade = '122099';
				itens_validos[i].dta_devolvida = '122099';

			}
		});


		var itens_devolucao = [] ;
		var id_deposito ;
		$.each(itens_validos,function(i,v){
			var ano       = parseInt(v.dta_devolvida.substring(2,6));
			var mes       = parseInt(v.dta_devolvida.substring(0,2)) -1;
			var objDate   = new Date(ano, mes , 1);
			if(empty(id_deposito)) id_deposito = v.id_deposito;
			itens_devolucao.push({
				id_produto           : v.id_produto,
				qtd 				 : v.qtd_devolvida,
				valor_real_devolucao : v.valor_real_item,
				dta_validade         : ano+'-'+(mes+1)+'-'+ultimoDiaDoMes(objDate),
				id_item_venda        : v.id_item,
				id_deposito          : v.id_deposito,
				vlr_custo             : v.vlr_custo,
				perc_imposto_compra  : v.perc_imposto_compra,
				perc_desconto_compra : v.perc_desconto_compra
			});

		});

		var devolucao = {
			id_empreendimento   : ng.userLogged.id_empreendimento,
			id_venda			: ng.venda.id,
			observacao			: ng.observacao,
			id_operador         : ng.userLogged.id,
			id_deposito         : id_deposito
		};

		var post = {
			devolucao 	    : devolucao,
			itens_devolucao : itens_devolucao
		}

		aj.post(baseUrlApi()+"devolucao",post)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.venda 		= null ;
				ng.itens_venda  = null ;
				ng.mensagens('alert-success','<strong>Devolução realizada com sucesso</strong>','.alert-devolucao-sucesso');
				ng.loadDevolucoes(0,10);
				ng.showBoxNovaDevolucao();
				$('html,body').animate({scrollTop: 0},'slow');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-danger','<strong>Ocorreu um erro ao efetuar a devolução</strong>','.alert-erro');
			});
	}

	ng.reset = function(){
		ng.Devolucoes = {itens:[]};
	}

	ng.resetFilter = function() {
		$("#data").val("");
		ng.busca.nome = "" ;
		ng.busca.id_venda = "" ;
		ng.reset();
		ng.loadDevolucoes(0,10);
	}

	ng.loadDevolucoes = function(offset,limit,divLoadingAjax) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		if(divLoadingAjax == null) ng.devolucoes = [];
		else $(divLoadingAjax).show();

		query_string = "?1=1";

		if(ng.busca.nome != ""){
			query_string += "&("+$.param({'tu->nome':{exp:"like'%"+ng.busca.nome+"%')"}});
		}

		if(ng.busca.id_venda != ""){
			query_string += "&("+$.param({'td->id_venda':{exp:"= "+ng.busca.id_venda+")"}});
		}

		if($("#data").val() != ""){
			var dta_devolucao = moment($("#data").val(), 'DD/MM/YYYY').format('YYYY-MM-DD');

			query_string += "&("+$.param({'2':{exp:"=2 AND cast(td.dta_devolucao as date) = '"+ dta_devolucao +"' )"}});
		}

		aj.get(baseUrlApi()+"devolucoes/"+ng.userLogged.id_empreendimento+"/"+offset+"/"+limit + query_string)
			.success(function(data, status, headers, config) {
					$(divLoadingAjax).hide();
					ng.devolucoes           = data.devolucoes; 
					ng.paginacao_devolucoes = data.paginacao ;

			})
			.error(function(data, status, headers, config) {
				$(divLoadingAjax).hide();
			});
	}
	
	ng.viewDetalhes = function(item){
		ng.viewDetalhes.item = item;
		ng.viewDetalhes.itens   = [] ;
		$("#list-itens-devolucao").modal('show');
		aj.get(baseUrlApi()+"devolucao/itens/"+item.id)
			.success(function(data, status, headers, config) {
				ng.viewDetalhes.itens = data;
			})
			.error(function(data, status, headers, config) {
				ng.viewDetalhes.itens = null ;
			});
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}


		ng.consultaCep = function(){
		aj.get("http://api.postmon.com.br/v1/cep/"+ng.cliente.cep)
		.success(function(data, status, headers, config) {

			ng.cliente.endereco = data.logradouro;
			ng.cliente.bairro = data.bairro;
			var estado = ng.getEstado(data.estado);
			ng.cliente.id_estado = estado.id;
			ng.loadCidadesByEstado(data.cidade);
			//ng.cliente.id_cidade = data.cidade_info.codigo_ibge.substr(0,6);
			$("#num_logradouro").focus();
			$('#busca-cep').modal('hide');
		})
		.error(function(data, status, headers, config) {
			$('#busca-cep').modal('hide');
			alert('CEP inválido');
		});
	}

	ng.getEstado = function(uf){
		var estado = null ;
		$.each(ng.estados,function(i,x){
			if(x.uf.toUpperCase() == uf.toUpperCase()){
			    estado = x;
				return false;
			}
		});

		return estado;
	}

	ng.getCidadeByIBGE = function(id){
		var cidade = null ;
		$.each(ng.cidades,function(i,x){
			if(Number(id) == Number(x.id)){
			    cidade = x;
				return false;
			}
		});

		return cidade;
	}

	ng.getEstadoByidIBGE = function(id){
		var estado = null ;
		$.each(ng.estados,function(i,x){
			if(Number(id) == Number(x.id) ){
			    estado = x;
				return false;
			}
		});

		return estado;
	}

	ng.loadEstados = function () {
		ng.estados = [];

		aj.get(baseUrlApi()+"estados")
		.success(function(data, status, headers, config) {
			ng.estados = data;
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.loadCidadesByEstado = function (nome_cidade) {
		ng.cidades = [];
		var id_cidade = angular.copy(ng.cliente.id_cidade);
		aj.get(baseUrlApi()+"cidades/"+ng.cliente.id_estado)
		.success(function(data, status, headers, config) {
			ng.cliente.id_cidade = angular.copy(id_cidade);
			
			ng.cidades = data;
			setTimeout(function(){$("select").trigger("chosen:updated");},300);
			if(nome_cidade != null){
				$.each(ng.cidades,function(i,x){
					if(removerAcentos(nome_cidade) == removerAcentos(x.nome)){
						ng.cliente.id_cidade = angular.copy(x.id);
						return false ;
					}
				});
			}
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.setEstadoCidade = function(){
		var id_empreendimento =  ng.userLogged.id_empreendimento ;
		var empreendimentos = ng.userLogged.empreendimento_usuario ;
		var empreendimento = {} ;

		$.each(empreendimentos,function(i,v){
			if(id_empreendimento == v.id){
				empreendimento = angular.copy(v) ;
				return
			}
		});
		console.log(empreendimento);
		ng.cliente.id_estado = empreendimento.cod_estado ;
		ng.loadCidadesByEstado(empreendimento.nme_municipio) ;
	}

	ng.loadEstados();
	ng.loadConfig();
	ng.loadDevolucoes(0,10);

});
