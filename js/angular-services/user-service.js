app.service('UserService', function($http, $window) {
	var aj = $http;

	this.KEY_USER_LOGGED 			= "KEY_USER_LOGGED";
	this.KEY_MEUS_EMPREENDIMENTOS 	= "KEY_MEUS_EMPREENDIMENTOS";

	this.getUserLogado = function() {
		var user = {};
		if(empty(sessionStorage.user)){
			 $.ajax({
			 	url: baseUrl() + "get_session.php",
			 	async: false,
			 	success: function(usuario) {
			 		user.id 					= usuario.id;
			 		user.id_empreendimento 		= usuario.id_empreendimento;
			 		user.nme_usuario 			= usuario.nome;
			 		user.end_email 				= usuario.email;
			 		user.id_perfil 				= usuario.id_perfil;
			 		user.nome_perfil 			= usuario.nome_perfil;
			 		user.nome_empreendimento 	= usuario.nome_empreendimento;
			 		user.nme_logo 				= usuario.nme_logo;
			 		user.modulosAssociatePage 	= usuario.modulosAssociatePage;
			 		user.perc_venda 			= usuario.perc_venda;
			 		user.empreendimento_usuario = usuario.empreendimento_usuario;
			 		user.flg_dispositivo 		= (!empty(usuario.flg_dispositivo) ? usuario.flg_dispositivo : null );

			 	},
			 	error: function(error) {
			 		console.log(error);
			 	}
			 });
			sessionStorage.user = angular.toJson(user);
		}else
			user = parseJSON(sessionStorage.user) ;
		return user;
	}

	this.getMeusEmpreendimentos = function(id_usuario) {
		var empreendimentos = this.getSessionData(this.KEY_MEUS_EMPREENDIMENTOS);
		var serviceScope = this;
		if(empreendimentos == null) {
			$.ajax({
				url: baseUrlApi() + "empreendimentos?id_usuario="+id_usuario,
				async: false,
				success: function(items) {
					empreendimentos = items;
					
					serviceScope.setSessionData(serviceScope.KEY_MEUS_EMPREENDIMENTOS, items);
				},
				error: function(error) {
					console.log(error);
				}
			});
		}

		return empreendimentos;
	};

	this.getSessionData = function(key) {
		var value = $window.sessionStorage.getItem(key);
		
		if(!value)
			return null;
		
		return JSON.parse(value);
	};

	this.setSessionData = function(key, value) {
		$window.sessionStorage.setItem(key, JSON.stringify(value));
	};

	this.clearSessionData = function() {
		$window.sessionStorage.removeItem(this.KEY_USER_LOGGED);
		$window.sessionStorage.removeItem(this.KEY_MEUS_EMPREENDIMENTOS);
		$window.sessionStorage.removeItem('user');
		$window.sessionStorage.removeItem('funcionalidades');
	}
});

app.service('FuncionalidadeService', function($http) {
	this.getIdModulo = function(){
		var aux = parseJSON(sessionStorage.user) ;
		modulos = aux.modulosAssociatePage ;
		var page = location.pathname.substring(location.pathname.lastIndexOf("/") + 1) ;
		if(!empty(modulos[page])){
			return modulos[page].id_modulo;
		}
		else return null
	}
	this.getIdsPerfisAuthorizedByModulo = function(id_empreendimento,associativo) {
		associativo = empty(associativo) ? 'false' : (associativo === true ? 'true' : 'false' ) ; 
		id_modulo = this.getIdModulo();
		var modulosFuncionalidades = (empty(sessionStorage.funcionalidades) ? [] : parseJSON(sessionStorage.funcionalidades)) ;
		var funcionalidades = empty(modulosFuncionalidades[id_modulo]) ? null : modulosFuncionalidades[id_modulo] ; 
		if(funcionalidades == null) {
			$.ajax({
				url: baseUrlApi() + "modulo/funcionalidades/id_perfis/"+id_modulo+"/"+id_empreendimento+"/"+associativo,
				async: false,
				success: function(items) {
					funcionalidades = items ;
				},
				error: function(error) {
					funcionalidades = [-1] ;
					console.log(error);
				}
			});
			modulosFuncionalidades[id_modulo] = funcionalidades ;
			sessionStorage.funcionalidades = angular.toJson(modulosFuncionalidades) ;
		}
		return funcionalidades;
	};

	this.Authorized = function(cod_funcionalidade,id_perfil,id_empreendimento){
		var funcionalidades = this.getIdsPerfisAuthorizedByModulo(id_empreendimento,true);
		funcionalidades = empty(funcionalidades) ? [] : funcionalidades ;
		var funcionalidade = empty(funcionalidades[cod_funcionalidade]) ? [] : funcionalidades[cod_funcionalidade] ;
		return _in(id_perfil,funcionalidade) ;
	}
});

app.service('ConfigService', function($http) {
	var configuracoes = null ;
	this.getConfig = function(id_empreendimento) {
		if(configuracoes != null)
			return configuracoes ;
		 $.ajax({
		 	url: baseUrlApi()+"configuracoes/"+id_empreendimento,
		 	async: false,
		 	success: function(config) {
		 		configuracoes = config ;
		 	},
		 	error: function(error) {
		 		console.log(error);
		 	}
		 });
		return configuracoes ;
	}
});

app.service('EmpreendimentoService', function($http) {
	var empreendimento = null ;
	this.getDadosEmpreendimento = function(id_empreendimento) {
		if(empreendimento != null)
			return empreendimento ;
		 $.ajax({
		 	url: baseUrlApi()+"empreendimento/"+id_empreendimento,
		 	async: false,
		 	success: function(config) {
		 		empreendimento = config ;
		 	},
		 	error: function(error) {
		 		console.log(error);
		 	}
		 });
		return empreendimento ;
	}
});

app.service('NFService', function($http) {
	this.getNota = function(id_empreendimento,id_venda) {
		var nota  = false ;;
		 $.ajax({
		 	url: baseUrlApi()+"nota_fiscal?cod_empreendimento="+id_empreendimento+"&cod_venda="+id_venda,
		 	async: false,
		 	success: function(dados) {
		 		nota = dados ;
		 	},
		 	error: function(error) {
		 		if(error.status != 404)
		 			console.log(error);
		 	}
		 });
		return nota ;
	}
});

app.service('CaixaService', function($http) {
	this.getCaixaAberto = function(id_empreendimento,pth_local,id_usuario) {
		var caixa ;
		 $.ajax({
		 	url: baseUrlApi()+"caixa/aberto/"+id_empreendimento+"/"+pth_local+"/"+id_usuario,
		 	async: false,
		 	success: function(data) {
		 		caixa = data ;
		 	},
		 	error: function(error) {
		 		console.log(error);
		 	}
		 });
		return caixa ;
	}
});

app.service('CozinhaService', function($http){
	this.cozinhas = null,
	this.getCozinhasAPI = function(cod_empreendimento) {
		var cozinhas = null;

		$.ajax({
			url: baseUrlApi() +"cozinhas?cod_empreendimento="+cod_empreendimento,
			async: false,
			success: function(data){
				$.each(data.cozinhas, function(i, cozinha){
					if(empty(cozinhas))
						cozinhas = [];
					cozinhas.push(cozinha);
				});
			},
			error: function(error){
				console.log(error);
			}
		});

		this.cozinhas = cozinhas;

		return this.cozinhas;
	},
	this.getAllCozinhas = function(cod_empreendimento) {
		if(!empty(this.cozinhas))
			return this.cozinhas;
		else
			return this.getCozinhasAPI(cod_empreendimento);
	},
	this.getCozinhasAtivas = function(cod_empreendimento) {
		var cozinhasAtivas = null;
		
		if(empty(this.cozinhas))
			this.getCozinhasAPI(cod_empreendimento);

		if(!empty(this.cozinhas)) {
			$.each(this.cozinhas, function(i, cozinha){
				if(empty(cozinhasAtivas))
					cozinhasAtivas = [];

				if(!empty(cozinha.id_ws_dsk))
					cozinhasAtivas.push(cozinha);
			});
		}

		return cozinhasAtivas;
	}
});

app.service('AsyncAjaxSrvc', function() {
	this.getListOfItens = function(route) {
		var list = [];
		$.ajax({
			url: route,
			async: false,
			success: function(responseData){
				if(Array.isArray(responseData.rows))
					list = responseData.rows;
				else
					list = responseData;
			}
		});
		return list;
	};
});

app.service('PrestaShop', function($http,ConfigService,UserService) {
	var aj = $http ;
	var user = UserService.getUserLogado() ;
	conf = ConfigService.getConfig(user.id_empreendimento);
	var PrestaShop = this ;
	var sendPrestaShop = (!empty(conf['sistemas_integrados']) && typeof parseJSON(conf['sistemas_integrados']) == 'object' && _in('prestashop',parseJSON(conf['sistemas_integrados']))) ;
	this.send = function(method,url,dados,id_empreendimento) {
		if(!sendPrestaShop)
			return ;
		$.noty.closeAll();
		var i = notifcacaoPrestaShop('informacao');
		if(!empty(dados)){
			aj[method](url,dados)
			.success(function(data, status, headers, config) {
				$.noty.close(i.options.id) ;
				if(data.status){
					notifcacaoPrestaShop('sucesso');
				}else{
					notifcacaoPrestaShop('erro');
				}
			})
			.error(function(data, status, headers, config) {
				if(status == 406){
					$.noty.closeAll();
					PrestaShop.modal406(data);
				}else{
					$.noty.close(i.options.id) ;
					 notifcacaoPrestaShop('erro');
				}
			});
		}else{
			aj[method](url)
			.success(function(data, status, headers, config) {
				$.noty.close(i.options.id) ;
				if(data.status){
					notifcacaoPrestaShop('sucesso');
				}else{
					notifcacaoPrestaShop('erro');
				}
			})
			.error(function(data, status, headers, config) {
				if(status == 406){
					PrestaShop.modal406(data);
				}else{
					$.noty.close(i.options.id) ;
					 notifcacaoPrestaShop('erro');
				}
			});
		}	
	}

	this.modal406 = function(errors){
		$('#modal-pretashop406').modal('hide');
		var linhas = "";
		$.each(errors,function(i,v){
			linhas += '<p style="color: red;">* '+v+' </p>' ;
		});
		$('#modal-pretashop406').remove();
		var htmlModal = '<div class="modal fade" id="modal-pretashop406" style="display:none">'+
  			'<div class="modal-dialog">'+
    			'<div class="modal-content">'+
      				'<div class="modal-header">'+
        				'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
						'<h4>Validações PrestaShop</span></h4>'+
						'<p>'+
							'Os dados foram salvos no WebliniaERP, porem as seguintes validações são obrigatórios para a integração com o PrestaShop'+
						'</p>'+
      				'</div>'+
				    '<div class="modal-body">'+
				    	'<div class="row">'+
				    		'<div class="col-sm-12" class="has-error">'+
				    	   		 linhas +	
				    		'</div>'+
				    	'</div>'+
				   ' </div>'+
			  	'</div>'+
			'</div>'+
		'</div>';
		$('body').append(htmlModal);
		$('#modal-pretashop406').modal('show');
	}
});
