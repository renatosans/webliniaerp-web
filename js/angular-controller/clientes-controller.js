app.controller('ClientesController', function($scope, $http, $window, $dialogs, UserService,PrestaShop, FuncionalidadeService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	var clienteTO 	= { 
						flg_ativo: 1, tipo_cadastro: "pf",empreendimentos:[],cod_regime_tributario:null,cod_regime_pis_cofins:null,
						cod_tipo_empresa:null,flg_contribuinte_icms:0,flg_isento_inscricao_estadual:0,flg_contribuinte_ipi:0,cod_zoneamento:null ,regime_especial:[],cliente_tipo_cadastro:'cliente_pf',flg_tipo:'cliente',modulos:[],
						empreendimentos: [{id:ng.userLogged.id_empreendimento,nome_empreendimento:ng.userLogged.nome_empreendimento}]
				  	};
	ng.cliente = angular.copy(clienteTO);
    ng.clientes	= [];
    ng.paginacao = {};
    ng.busca = {clientes:"", tipo_cliente: 'ambos'};
    ng.editing = false;
    ng.estadoSelecionado = {};
    ng.emptyBusca   = {};

    function getUrlVars() {
		if(window.location.href.indexOf('?') != -1) {
			var vars = {}, hash;
			var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < hashes.length; i++)
			{
				hash = hashes[i].split('=');
				vars[hash[0]] = hash[1];
			}

			return vars;
		}
		else
			return null;
	}

	var params = getUrlVars();

    ng.funcioalidadeAuthorized = function(cod_funcionalidade){
		return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
	}

    ng.showBoxNovo = function(onlyShow){
    	ng.editing = !ng.editing;

    	if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show(400,function(){$("select").trigger("chosen:updated");});
		}
		else {
			$('#box-novo').toggle(400, function(){
				if($(this).is(':visible')){
					$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
				}else{
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
					$('a',$('#tab-cliente .tab-bar li').eq(0)).tab('show');
					$scope.$apply(function () {
			           ng.cliente = angular.copy(clienteTO);
			        });
				}
				$("select").trigger("chosen:updated");
			});
		}
		$("select").trigger("chosen:updated");
	}

	ng.isNumeric = function(vlr_numeric){
		return $.isNumeric(vlr_numeric);
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

	ng.consultaLatLog = function() {
		/*
		var address = ng.cliente.endereco + ", " + ng.cliente.numero + ", " + ng.cliente.cep.substr(0,5) + "-" + ng.cliente.cep.substr(5,ng.cliente.cep.length);

		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({'address': address}, function(results, status) {
			if(results.length == 1) {
				ng.cliente.num_latitude = results[0].geometry.location.k;
				ng.cliente.num_longitude = results[0].geometry.location.B;
			} else {
				//alert("Endereço inválido!");
			}
		});
		*/
	}

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.cliente = angular.copy(clienteTO);
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
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
			ng.cliente.nme_vendedor_responsavel = item.nome ;
			ng.cliente.id_vendedor_responsavel   = item.id;
		}else if(ng.busca.tipo_usuario  == 'cliente'){
			ng.cliente.nme_vendedor_responsavel = item.nome ;
			ng.cliente.id_vendedor_responsavel   = item.id;
		}
		$("#list_usuarios").modal("hide");
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

	ng.loadComoEncontrou = function () {
		ng.comoencontrou = [];

		aj.get(baseUrlApi()+"comoencontrou")
		.success(function(data, status, headers, config) {
			data.push({id:"outros",nome:"Outros"});
			ng.comoencontrou = data;
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.loadPerfil = function () {
		ng.perfis = [];

		aj.get(baseUrlApi()+"perfis?tpue->id_empreendimento="+ng.userLogged.id_empreendimento)
		.success(function(data, status, headers, config) {
			ng.perfis = data;
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.loadFinalidades = function () {
		ng.finalidades = [];

		aj.get(baseUrlApi()+"finalidades")
		.success(function(data, status, headers, config) {
			ng.finalidades = data;
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.loadBancos = function () {
		ng.bancos = [];

		aj.get(baseUrlApi()+"bancos")
		.success(function(data, status, headers, config) {
			$.each(data.bancos,function(i,x){
				data.bancos[i].id = Number(data.bancos[i].id);
			});
			ng.bancos = data.bancos;
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.ccDetalhes = false ;

	ng.changeDetalhesCC = function(status){
		ng.ccDetalhes = status ;
		
	}


	ng.loadClientes = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.clientes = null ;
		var query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+"&usu->id[exp]= NOT IN("+ng.configuracao.id_cliente_movimentacao_caixa+","+ng.configuracao.id_usuario_venda_vitrine+"))";

		if(not_in(ng.userLogged.id,'222,498,1069,46')){
			query_string += " AND (usu.id NOT IN (222,498,1069,46) OR ( usu.id IN (222,498,1069,46) AND emp.id = 6 ) )";
		}
		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({"(usu->nome":{exp:"like'%"+ng.busca.clientes+"%' OR usu.apelido like '%"+ng.busca.clientes+"%' OR nome_fantasia like '%"+ng.busca.clientes+"%' OR cnpj like '%"+ng.busca.clientes+"%' )"}})+"";
		}
		if(ng.busca.tipo_cliente != 'ambos') {
			query_string += "&"+$.param({"(1":{exp:"=1 AND (CASE WHEN tpf.usuarios_id is NULL THEN 'pj' WHEN tpj.cnpj is NULL THEN 'pf' END) = '"+ ng.busca.tipo_cliente +"' AND (usu.nome like'%"+ng.busca.clientes+"%' OR apelido like '%"+ng.busca.clientes+"%' OR nome_fantasia like '%"+ng.busca.clientes+"%' OR cnpj like '%"+ng.busca.clientes+"%'))"}})+"";
		}
		if (params != null) {
			if(params.novos == 1){
				query_string += "&"+"usu->flg_cadastro_externo=1";	
			}
		}
		
		aj.get(baseUrlApi()+"usuarios/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.clientes = [];
				ng.paginacao.itens = data.paginacao;
				$.each(data.usuarios,function(i, item){
					
					item.id_como_encontrou = item.id_como_encontrou == null && (empty(item.id_como_encontrou) && !empty(item.como_entrou_outros)) ? 'outros' : item.id_como_encontrou ; 
					ng.clientes.push(item);
				});
				ng.loadSaldoDevedorClientes();
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.clientes = false;
			});
	}

	ng.loadEmpreendimentoCliente = function() {
		aj.get(baseUrlApi()+"empreendimentos?id_usuario="+ng.cliente.id)
			.success(function(data, status, headers, config) {
				ng.cliente.empreendimentos = data ;
				ng.load_empreendimentos = false ;
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.verificarEmpSelected = function(item){
		var x = false ;
		$.each(ng.cliente.empreendimentos,function(i,v){
			if(Number(item.id) == Number(v.id)){
				x = true;
				return false;
			}
		});
		return x ;
	}

	ng.loadSaldoDevedorClientes = function() {
		var id 			= "" ;
		var id_index 	= {} ;

		$.each(ng.clientes,function(i,v){
			id += v.id+", ";
			id_index[v.id] = i ; 
		});

		id = id.substr(0,id.lastIndexOf(','));

		
		

		aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ ng.userLogged.id_empreendimento+"?usu->id[exp]=IN("+id+")")
			.success(function(data, status, headers, config) {
				if(data.id != undefined){
					var index_cliente = id_index[data.id];
					ng.clientes[index_cliente].vlr_saldo_devedor = data.vlr_saldo_devedor;
				}else{
					$.each(data,function(i, item){
						var index_cliente = id_index[item.id];
						ng.clientes[index_cliente].vlr_saldo_devedor = item.vlr_saldo_devedor;
					});
				}	
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.salvar = function(event) {
		ng.removeError();
		
		var cliente = angular.copy(ng.cliente);
		var btn = $(event.target) ;
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');
		var msg = 'Cliente salvo com sucesso!';
		var url = 'cliente';

		if(cliente.id != null && cliente.id > 0) {
		 	url += '/update';
		 	msg = 'Cliente atualizado com sucesso!';
		 }

		 cliente.id_empreendimento = ng.userLogged.id_empreendimento;
		 cliente.status = 1 ;
		 cliente.flg_cadastro_externo = 0;

		  if(cliente.dta_nacimento != null && cliente.dta_nacimento.length == 8 ){
		 	var data = cliente.dta_nacimento;
		 	var dia  = cliente.dta_nacimento.substring(0,2); 
		 	var mes  = cliente.dta_nacimento.substring(2,4);
		 	var ano  = cliente.dta_nacimento.substring(4,8);

		 	cliente.dta_nacimento = ano+"-"+mes+"-"+dia ;

		 }

		var usuarioModulos = null 
		if(!empty($checkableTree)){
			var treeview = $checkableTree.treeview('getUnselected', null);
			var usuarioModulos = [] ;

			if(treeview.length > 0){
				$.each(treeview, function(i,v){
					if(v.id_modulo == 38){
						
					}
					if(v.state.checked){
						usuarioModulos.push({
							id_empreendimento : ng.userLogged.id_empreendimento ,
							id_modulo: v.id_modulo ,
							flg_permissao : 1 
						});
					}else{
						usuarioModulos.push({
							id_empreendimento : ng.userLogged.id_empreendimento ,
							id_modulo: v.id_modulo ,
							flg_permissao : 0 
						});
					}
				});
			}
		}
		cliente.modulos = null ;
		cliente.usuarioModulos = usuarioModulos ;
		if(cliente.tipo_cadastro=='pj'){
			cliente.nome = cliente.razao_social;
		}
		
		if((!empty(ng.cliente.id_estado) && !empty(ng.cliente.id_cidade) && !empty(ng.cliente.endereco) && !empty(ng.cliente.numero) && !empty(ng.cliente.bairro))){
		 	 var estado_selecionado = ng.getEstadoByidIBGE(ng.cliente.id_estado);
			 var cidade_selecionada = ng.getCidadeByIBGE(ng.cliente.id_cidade);
			 var address = ng.cliente.endereco + ", " + ng.cliente.numero + ", " + ng.cliente.bairro + "," + cidade_selecionada.nome +"," +estado_selecionado.nome ;
			
			 aj.get('https://maps.googleapis.com/maps/api/geocode/json?region=BR&address='+address)
				.success(function(data, status, headers, config) {
					if(data.status = 'OK' && !empty(data.results)){
						cliente.num_latitude  = data.results[0].geometry.location.lat ;
						cliente.num_longitude = data.results[0].geometry.location.lng ;
						ng.ajaxSalvar(cliente,url,msg,btn);
				    }else
				        ng.ajaxSalvar(cliente,url,msg,btn);

				})
				.error(function(data, status, headers, config) {
					ng.ajaxSalvar(cliente,url,msg,btn);

				});
		}else{
			ng.ajaxSalvar(cliente, url, msg,btn);
		}
	}

	ng.ajaxSalvar = function(cliente,url,msg,btn){
		aj.post(baseUrlApi()+url, cliente)
		 	.success(function(data, status, headers, config) {
		 		ng.mensagens('alert-success','<strong>'+msg+'</strong>');
		 		ng.showBoxNovo();
		 		ng.reset();
		 		$('html,body').animate({scrollTop: 0},'slow');
		 		btn.button('reset');
		 		ng.loadClientes(0,10);
		 		if(!empty(data.id_cliente))
		 			cliente.id = data.id_cliente ;
		 		PrestaShop.send('post',baseUrlApi()+"prestashop/usuario/",cliente);
		 	})
		 	.error(function(data, status, headers, config) {
		 		btn.button('reset');
		 		if(status == 406) {
		 			var errors = data;
		 			openAbaValidade(errors);
		 			if(Object.keys(errors).length == 1 && typeof errors.id_perfil != 'undefined'){
		 				$('a',$('#tab-cliente .tab-bar li').eq(4)).tab('show');
		 				setTimeout(function(){
		 					$('html,body').animate({scrollTop: 0 },'slow');
		 					$("#id_perfil").tooltip('show');
		 				},150);
		 			}
		 			var count = 0 ;
		 			$.each(errors, function(i, item) {
		 				$("#"+i).addClass("has-error");

		 				var formControl = $($("#"+i))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", "")
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

		 				if(count == 0){
		 					$('html,body').animate({scrollTop: $("#"+i).parents('.row').offset().top - 50},'slow');
		 					formControl.tooltip('show');
		 				}
		 				count ++ ;
		 			});
		 			
		 		}
		 	});
	}

	function openAbaValidade(errors){
		var campos_abas = {informacoes_basicas:'nome,tel_fixo,endereco,numero,bairro,cep,id_estado,id_cidade',dados_acesso:'id_perfil'};
		$.each(errors,function(i,x){
			if(_in(i,campos_abas.informacoes_basicas)){
				$('#tab-cliente').find('li.active').removeClass('active');
				$('#tab-cliente-body').find('.active').removeClass('active');
				$('#tab-cliente').find('[href="#informacoes_basicas"]').parent('li').addClass('active');
				$('#informacoes_basicas').addClass('active in');
				return ;
			}else if(_in(i,campos_abas.dados_acesso)){
				$('#tab-cliente').find('li.active').removeClass('active');
				$('#tab-cliente-body').find('.active').removeClass('active');
				$('#tab-cliente').find('[href="#dados_acesso"]').parent('li').addClass('active');
				$('#dados_acesso').addClass('active in');
				return ;
			}
		});
	}

	/*ng.buscarLatLog = function (){
		var address = ng.cliente.endereco + ", " + ng.cliente.numero + ", " + ng.cliente.cep.substr(0,5) + "-" + ng.cliente.cep.substr(5,ng.cliente.cep.length);
		aj.get('https://maps.googleapis.com/maps/api/geocode/json?address='+address)
			.success(function(data, status, headers, config) {
				ng.cliente.num_latitude  = data.results[0].geometry.location.lat ;
				ng.cliente.num_longitude = data.results[0].geometry.location.lng ;

			})
			.error(function(data, status, headers, config) {
				
			});
	}*/

	ng.removeError = function(){
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").tooltip('destroy');
		$(".has-error").css({border:"none",background: 'none'}).addClass('has-error');
		$(".has-error").parent().css({'background':'none'});
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}
	ng.load_empreendimentos = false ;
	ng.editar = function(item) {
		$('a',$('#tab-cliente .tab-bar li').eq(0)).tab('show');
		ng.load_empreendimentos = true ;
		ng.cliente = angular.copy(item);
		ng.cliente.id_como_encontrou = (!empty(ng.cliente.id_como_encontrou)) ? String(ng.cliente.id_como_encontrou) : null;

		if(ng.cliente.flg_tipo=='cliente' && ng.cliente.tipo_cadastro=='pf') ng.cliente.cliente_tipo_cadastro = 'cliente_pf';
		else if(ng.cliente.flg_tipo=='cliente' && ng.cliente.tipo_cadastro=='pj') ng.cliente.cliente_tipo_cadastro = 'cliente_pj';
		else if(ng.cliente.flg_tipo=='usuario' && ng.cliente.tipo_cadastro=='pf') ng.cliente.cliente_tipo_cadastro = 'usuario_pf';
		else if(ng.cliente.tipo_cadastro=='pf'){
			ng.cliente.cliente_tipo_cadastro = 'cliente_pf';
			ng.cliente.flg_tipo = 'cliente';
		}
		else if(ng.cliente.tipo_cadastro=='pj'){
			ng.cliente.cliente_tipo_cadastro = 'cliente_pj';
			ng.cliente.flg_tipo = 'cliente';
		}
		else{
			ng.cliente.cliente_tipo_cadastro = 'cliente_pf';
			ng.cliente.flg_tipo = 'cliente';
		}

		ng.cliente.regime_especial = [] ;
		ng.loadRegimeCliente(ng.cliente.id);
		if(ng.cliente.empreendimentos == false)
			ng.cliente.empreendimentos = [] ;
		ng.loadEmpreendimentoCliente();
		ng.loadCidadesByEstado();
		ng.showBoxNovo(true);
		if(!empty(item.id_perfil))
			ng.loadModulosByUser(angular.copy(item));
		$('html,body').animate({scrollTop: 0},'slow');
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este cliente?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"cliente/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Cliente excluido com sucesso</strong>');
					ng.reset();
					ng.loadClientes(0,10);
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	ng.loadEmpreendimentos = function() {
		//ng.reset();
		aj.get(baseUrlApi() + "empreendimentos?id_usuario="+ng.userLogged.id)
			.success(function(data, status, headers, config) {
				ng.empreendimentos = data;
				$('#list_emp').modal('show');
			})
			.error(function(data, status, headers, config) {
				ng.empreendimentos = [] ;
			});
		return false;
	}

	ng.addEmp = function(item){
		if(ng.cliente.empreendimentos == null)
			ng.cliente.empreendimentos = [];

		ng.cliente.empreendimentos.push(item);
	}

	ng.delEmpreendimento = function(index){
		ng.cliente.empreendimentos.splice(index,1);
	}

	ng.loadGrupoComissionamento = function() {
		aj.get(baseUrlApi()+"grupo/comissao/vendedores/?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.grupo_comissionamento = data.grupoComissaoVendedores;
				ng.grupo_comissionamento.unshift({nme_grupo_comissao:' ',id:null});
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.grupo_comissionamento = [];
			});
	}

	var cep_anterior = null;
	ng.validCep = function(cep){
		if(cep != cep_anterior){
			 var exp  = /^[0-9]{8}$/;
	         var cep = cep;
	         if(exp.test(cep)){
	         	cep_anterior = cep ;
	         	$('#busca-cep').modal({
				  backdrop: 'static',
				  keyboard: false
				});
				ng.consultaCep();
	         }
		}
	}

	ng.configuracao = null ;
	ng.loadConfig = function(){
		var error = 0 ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracao = data ;
				ng.loadClientes(0,10);
			})
			.error(function(data, status, headers, config) {
				ng.loadClientes(0,10);
				if(status == 404){
					ng.configuracao = false ;
				}
			});
	}

	ng.ClearChosenSelect = function(item){
		if(ng.cliente[item] == ''){
			ng.cliente[item] = null;
		}
	}

	ng.regimeTributario = [] ;
	ng.regimePisCofins  = [] ;
	ng.tipoEmpresa      = [] ;
	ng.zoneamentos      = [] ;

	ng.loadControleNfe = function(ctr,key) {
		ng[key] = [] ;
		aj.get(baseUrlApi()+"nfe/controles/null/"+ctr)
			.success(function(data, status, headers, config) {
				ng[key] = [{cod_controle_item_nfe:''}] ;
				ng[key] = ng[key].concat(data) ;
			})
			.error(function(data, status, headers, config) {
				
		});
	}

	ng.loadZoneamento = function() {
		aj.get(baseUrlApi()+"zoneamento/get?cod_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.zoneamentos = [{cod_zoneamento:''}] ;
				ng.zoneamentos = ng.zoneamentos.concat(data.zoneamentos);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.zoneamentos = [];
			});
	}


	ng.showModalRegimeEspecial = function(){
		$('#list-regime-especial').modal('show');
		ng.loadRegimeEspecial();

	}

	ng.selectedRegimeEspecial = function(item){
		var saida = false ;
		$.each(ng.cliente.regime_especial,function(i,x){
			if(Number(x.cod_regime_especial) == Number(item.cod_regime_especial)){
				saida = true ;
			}
		});
		return saida ;
	}

	ng.selRegimeEspecial = function(item){
		item = angular.copy(item);
		item.cod_cliente = ng.cliente.id;
		ng.cliente.regime_especial.push(item);
	}

	ng.delRegimeEspecial = function(index){
		ng.cliente.regime_especial.splice(index,1);
	}	

	ng.loadRegimeEspecial = function(offset,limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.regimes = null ;
		aj.get(baseUrlApi()+"regime_especial/get/"+offset+"/"+limit+"?cod_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.regimes = data.regimes;
				ng.paginacao.regimes = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.regimes = [];
					ng.paginacao.regimes = [];
				}
			});
	}
	ng.atendimentos = [] ;
    ng.getAtendimentos = function(){
    	ng.atendimentos = null ;
   		aj.get(baseUrlApi()+"clinica/paciente/"+ ng.cliente.id +"/procedimentos")
			.success(function(data, status, headers, config) {
				$.each(data,function(i,x){
					x.dta_inicio_procedimento = ""+x.dta_inicio_procedimento;
            		data[i].dta_inicio_procedimento = x.dta_inicio_procedimento.substring(0,2)+'/'+x.dta_inicio_procedimento.substring(2,4)+'/'+x.dta_inicio_procedimento.substring(4,8)+' '+x.dta_inicio_procedimento.substring(8,10)+':'+x.dta_inicio_procedimento.substring(10,12)
            	});
				ng.atendimentos = data ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.atendimentos = [];
		});
	}

	ng.totalAtendimentos = function(){
		var vlr_total = 0 ;
		$.each(ng.atendimentos,function(i,x){
			vlr_total += x.valor_real_item;
		});
		//ng.vlrTotalCompra = numberFormat(vlr_total,2,'.','') ;
		return vlr_total ;
	}
	ng.pagamentosCliente = {} ;
	ng.loadPagamentosPaciente = function(){
		ng.pagamentosCliente.pagamentos = null ;
		 aj.get(baseUrlApi()+"pagamentos/cliente/"+ng.cliente.id)
            .success(function(data, status, headers, config) {
				ng.pagamentosCliente.pagamentos = data.pagamentos ;
				ng.pagamentosCliente.total = 0 ;
				$.each(data.pagamentos,function(i,v){
					ng.pagamentosCliente.total += v.valor_pagamento ;
				});
         }).error(function(data, status, headers, config) {
   					ng.pagamentosCliente.pagamentos = [] ;
            });

          
	}
	ng.loadRegimeCliente = function (cod_cliente) {
		aj.get(baseUrlApi()+"regime_especial/cliente/get/"+cod_cliente)
		.success(function(data, status, headers, config) {
			ng.cliente.regime_especial = data;
		})
		.error(function(data, status, headers, config) {
			ng.cliente.regime_especial = [];
		});
	}

	// Bloco de modulos

	var $checkableTree  ;
	ng.cliente.modulos = [] ;
	function treeviewCheckChildren(node){
		if(!empty(node.nodes && node.nodes.length > 0)){
			treeviewExpanded(node);
			$.each(node.nodes,function(i,v){
		        if(!v.state.checked){
			        $scope.$apply(function () {
			           ng.cliente.modulos.push(v.id_modulo);
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
		        	var index = ng.cliente.modulos.indexOf(v.id_modulo);
		            $scope.$apply(function () {
		           	   ng.cliente.modulos.splice(index,1);
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
		           ng.cliente.modulos.push(parent.id_modulo);
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
	           ng.cliente.modulos.push(node.id_modulo);
	        });	
	        checkPai(node);
	        treeviewCheckChildren(node);
          },
          onNodeUnchecked: function (event, node) {
            var index = ng.cliente.modulos.indexOf(node.id_modulo);
            $scope.$apply(function () {
           	   ng.cliente.modulos.splice(index,1);
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

	ng.subMenuConstruct = function(arrpai,arr){
		var menu = [] ;
		$.each(arr,function(key,value){
			if(arrpai.id_modulo == value.id_modulo_pai){
				var item = {
					id_modulo : value.id_modulo,
					id_pai : value.id_modulo_pai,
					data : {id_modulo:value.id_modulo.toString()},
					text : value.nme_modulo,
					nodes : ng.subMenuConstruct(value,arr),
					icone : value.icn_modulo
				};	
				if(item.nodes.length == 0) delete item.nodes ;
				menu.push(item);	
			}
		});

		return menu ;
	}

	ng.menuConstruct = function(Modulos){
		var menu = [] ;
		$.each(Modulos,function(key,value){
			if(empty(value.id_modulo_pai)){
				var itens = ng.subMenuConstruct(value,Modulos)
				if(itens.length > 0){
					menu.push({
						id_modulo : value.id_modulo,
						data : {id_modulo:value.id_modulo.toString()},
						text : value.nme_modulo,
						nodes : ng.subMenuConstruct(value,Modulos),
						icone : "fa-signal",
						selectable:false
					});	
				}else{
					menu.push({
						id_modulo : value.id_modulo,
						data : {id_modulo:value.id_modulo.toString()},
						text : value.nme_modulo,
						icone : "fa-signal",
						selectable:false
					});			
				}
				
			}
		});

		return menu ;
	}

	ng.loadModulos = function() {
		aj.get(baseUrlApi()+"modulos/"+ng.userLogged.id_empreendimento+"?cplSql= ORDER BY tm.psc_menu_modulo ASC")
		.success(function(data, status, headers, config) {
			var menu = ng.menuConstruct(data);
			ng.treeviewConstruct(menu);
			
		})
		.error(function(data, status, headers, config) {
			if(status == 404){

			}
			
		});
	}
	ng.loadingModulos = false ;
	ng.loadModulosByPerfil = function(id_perfil){
		ng.loadingModulos = true ;
		if(!empty($checkableTree))
			$checkableTree.treeview('remove');
		ng.cliente.modulos = [] ;
		aj.get(baseUrlApi()+"modulos/"+ng.userLogged.id_empreendimento+"?cplSql= ORDER BY tm.psc_menu_modulo ASC")
		.success(function(empreendimentoModulos, status, headers, config) {
			var menu = ng.menuConstruct(empreendimentoModulos);
			ng.treeviewConstruct(menu);

			aj.get(baseUrlApi()+"modulos/"+ng.userLogged.id_empreendimento+"/"+id_perfil+"?cplSql= ORDER BY tm.psc_menu_modulo ASC")
			.success(function(usuarioModulos, status, headers, config) {

				var treeview = $checkableTree.treeview('getUnselected', null);;
				$.each(treeview,function(i,v){
					if($.isNumeric(getIndex('id_modulo',v.id_modulo,usuarioModulos))){
						$checkableTree.treeview('checkNode', [v.nodeId, {silent: true}]);
						treeviewExpanded(v);
				        ng.cliente.modulos.push(v.id_modulo);
					}else{
						$checkableTree.treeview('uncheckNode', [v.nodeId, {silent: true}]);
						treeviewCollapsing(v);
					}
				});
				ng.loadingModulos = false ;

			})
			.error(function(data, status, headers, config) {
				ng.loadingModulos = false ;
			});
			
		})
		.error(function(data, status, headers, config) {
			ng.loadingModulos = false ;
		});
	}

	ng.loadModulosByUser = function(user){
		ng.loadingModulos = true ;
		if(!empty($checkableTree))
			$checkableTree.treeview('remove');
		ng.cliente.modulos = [] ;
		aj.get(baseUrlApi()+"modulos/"+ng.userLogged.id_empreendimento+"?cplSql= ORDER BY tm.psc_menu_modulo ASC")
		.success(function(empreendimentoModulos, status, headers, config) {
			var menu = ng.menuConstruct(empreendimentoModulos);
			ng.treeviewConstruct(menu);
			aj.get(baseUrlApi()+"modulos/"+ng.userLogged.id_empreendimento+"/null/"+user.id+"?cplSql= WHERE flg_permissao = 1  ORDER BY psc_menu_modulo ASC")
			.success(function(usuarioModulos, status, headers, config) {
				var treeview = $checkableTree.treeview('getUnselected', null);;
				$.each(treeview,function(i,v){
					if($.isNumeric(getIndex('id_modulo',v.id_modulo,usuarioModulos))){
						$checkableTree.treeview('checkNode', [v.nodeId, {silent: true}]);
						treeviewExpanded(v);
				        ng.cliente.modulos.push(v.id_modulo);
					}else{
						$checkableTree.treeview('uncheckNode', [v.nodeId, {silent: true}]);
						treeviewCollapsing(v);
					}
				});
				ng.loadingModulos = false ;
			})
			.error(function(data, status, headers, config) {
				ng.loadingModulos = false ;
			});
		})
		.error(function(data, status, headers, config) {
			ng.loadingModulos = false ;
		});
	}

	ng.setTipoCadastro = function(tipo1,tipo2){
		ng.cliente.flg_tipo= tipo1 ;
		ng.cliente.tipo_cadastro = tipo2;
		if 		(tipo1 == 'cliente' && tipo2 == 'pf') ng.cliente.cliente_tipo_cadastro = 'cliente_pf';
		else if (tipo1 == 'cliente' && tipo2 == 'pj') ng.cliente.cliente_tipo_cadastro = 'cliente_pj';
		else if (tipo1 == 'usuario' && tipo2 == 'pf') ng.cliente.cliente_tipo_cadastro = 'usuario_pf';
	}

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.loadPlanoContas = function() {
		aj.get(baseUrlApi() + "planocontas?tpc->id_empreendimento=" + ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				$.each(data, function(i, item){
					data[i].cod_plano 			= (!empty(item.cod_plano)) ? parseInt(item.cod_plano, 10) : null;
					data[i].cod_plano_pai 		= (!empty(item.cod_plano_pai)) ? parseInt(item.cod_plano_pai, 10) : null;
					data[i].id 					= (!empty(item.id)) ? parseInt(item.id, 10) : null;
					data[i].id_empreendimento 	= (!empty(item.id_empreendimento)) ? parseInt(item.id_empreendimento, 10) : null;
					data[i].id_plano_pai 		= (!empty(item.id_plano_pai)) ? parseInt(item.id_plano_pai, 10) : null;
				});
				ng.plano_contas = data;
				ng.plano_contas.unshift({id: null, dsc_completa: ' '});
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.plano_contas = [];
			});
	}

	ng.loadEstados();
	ng.loadComoEncontrou();
	ng.loadPerfil();
	ng.loadFinalidades();
	ng.loadBancos();
	ng.loadConfig();
	ng.loadGrupoComissionamento();
	ng.loadZoneamento();
	ng.loadPlanoContas();
	ng.loadControleNfe('regime_tributario','regimeTributario');
	ng.loadControleNfe('regime_tributario_pis_cofins','regimePisCofins');
	ng.loadControleNfe('tipo_empresa','tipoEmpresa');
});
