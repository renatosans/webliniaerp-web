app.controller('CadastroController', function($scope, $http, $window, $dialogs, UserService,ConfigService){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	//ng.userLogged 	= UserService.getUserLogado();
	ng.cliente 	= { tipo_cadastro: "pf",id_perfil:7 };
    ng.clientes	= [];
    ng.editing = false;
    ng.estadoSelecionado = {};

	ng.baseUrl 		   = baseUrl();
	ng.busca		   = {nome:"" , id_categoria:null, id_fabricante:null};
	ng.paginacao       = {grade:null}; 


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


	ng.consultaLatLog = function() {
		var address = ng.cliente.endereco + ", " + ng.cliente.numero + ", " + ng.cliente.cep.substr(0,5) + "-" + ng.cliente.cep.substr(5,ng.cliente.cep.length);

		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({'address': address}, function(results, status) {
			if(results.length == 1) {

				ng.cliente.num_latitude  = results[0].geometry.location.k;
				ng.cliente.num_longitude = results[0].geometry.location.B;
				
			} else {
				alert("Endereço inválido!");
			}
		});
	}

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.cliente = {};
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
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

		aj.get(baseUrlApi()+"perfis")
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
			ng.bancos = data.bancos;
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.loadClientes = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;

		var query_string = "?emp->id="+ng.userLogged.id_empreendimento;

		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({"usu->nome":{exp:"like'%"+ng.busca.clientes+"%' OR apelido like '%"+ng.busca.clientes+"%' OR per.nome like '%"+ng.busca.clientes+"%'"}})+"";
		}

		aj.get(baseUrlApi()+"usuarios/"+ offset +"/"+ limit +"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.clientes = [];
				ng.paginacao.itens = data.paginacao;
				$.each(data.usuarios,function(i, item){
					item.id_como_encontrou = item.id_como_encontrou == null || item.id_como_encontrou == "" ? 'outros' : item.id_como_encontrou ; 
					ng.clientes.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.clientes = [];
			});
	}

	ng.isCPF = function(strCPF) {
	    strCPF = ""+strCPF;
	    strCPF = strCPF.replace(/\./g, '').replace(/\-/g, ''); ;
	    var Soma; 
	    var Resto; 
	    	Soma = 0; 
	    	if (strCPF == "00000000000") 
	    		return false; 
	    		for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i); 
	    			Resto = (Soma * 10) % 11; if ((Resto == 10) || (Resto == 11)) Resto = 0; 
	    		if (Resto != parseInt(strCPF.substring(9, 10)) ) 
	    			return false; 
	    			Soma = 0; 
	    			for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i); 
	    				Resto = (Soma * 10) % 11; if ((Resto == 10) || (Resto == 11)) Resto = 0; 
	    			if (Resto != parseInt(strCPF.substring(10, 11) ) ) 
	    				return false; 

	    	return true; 
	}

	ng.salvar = function() {
		ng.isCPF();

		ng.cliente.empreendimentos 		= [{id:ng.id_empreendimento}];
		ng.cliente.id_empreendimento 	= ng.id_empreendimento ;
		/*if(empty(ng.configuracoes)){
			ng.configuracoes = ConfigService.getConfig(ng.id_empreendimento);

			var emails_notificacoes = !empty(ng.configuracoes.emails_notificacoes) ? JSON.parse(ng.configuracoes.emails_notificacoes) : false ;
			$.each(emails_notificacoes,function(i,v){
				emails_notificacoes[i] = {
					nome : '',
					email: v
				};
			});
		}
		console.log(emails_notificacoes);*/
		var btn = $('#btn-salvar');
   		btn.button('loading');
		ng.removeError();
		var cliente = angular.copy(ng.cliente);
		var msg = 'Seu Cadastro foi efetuado com sucesso! Em breve você receberá um e-mail com seu login e senha.';
		var url = 'cliente';

		if(cliente.id != null && cliente.id > 0) {
		 	url += '/update';
		 	msg = 'Cliente atualizado com sucesso!';
		 }

		 cliente.status = 1 ;
		 
		 if(cliente.dta_nacimento != null && cliente.dta_nacimento.length == 8 ){
		 	var data = cliente.dta_nacimento;
		 	var dia  = cliente.dta_nacimento.substring(0,2); 
		 	var mes  = cliente.dta_nacimento.substring(2,4);
		 	var ano  = cliente.dta_nacimento.substring(4,8);

		 	cliente.dta_nacimento = ano+"-"+mes+"-"+dia ;

		 }

		 if((!empty(ng.cliente.id_estado) && !empty(ng.cliente.id_cidade) && !empty(ng.cliente.endereco) && !empty(ng.cliente.numero) && !empty(ng.cliente.bairro))){
		 	 var estado_selecionado = ng.getEstadoByidIBGE(ng.cliente.id_estado);
			 var cidade_selecionada = ng.getCidadeByIBGE(ng.cliente.id_cidade);
			 var address = ng.cliente.endereco + ", " + ng.cliente.numero + ", " + ng.cliente.bairro + "," + cidade_selecionada.nome +"," +estado_selecionado.nome ;
			
			 aj.get('https://maps.googleapis.com/maps/api/geocode/json?region=BR&address='+address)
				.success(function(data, status, headers, config) {
					if(data.status = 'OK'){
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
				ng.ajaxSalvar(cliente,url,msg,btn);
			}
	}

	ng.ajaxSalvar = function(cliente,url,msg,btn){
		 aj.post(baseUrlApi()+url, cliente)
		 	.success(function(data, status, headers, config) {
		 		var form_data = data ;

		 		ng.mensagens('alert-success','<strong>'+msg+'</strong>');
		 		$('form').hide();
   				btn.button('reset');
		 		ng.reset();

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

	ng.removeError = function(){
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").tooltip('destroy');
		$(".has-error").css({border:"none",background: 'none'}).addClass('has-error');
		$(".has-error").parent().css({'background':'none'});
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.editar = function(item) {
		ng.cliente = angular.copy(item);
		ng.showBoxNovo(true);
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

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

	}

	ng.loadEmpreendimento = function(id_empreendimento) {
		aj.get(baseUrlApi()+"empreendimento/"+id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.empreendimento = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.empreendimento = [];
			});
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

	ng.loadCidadesByEstado = function (nome_cidade) {
		ng.cidades = [];
		var id_cidade = angular.copy(ng.cliente.id_cidade);
		aj.get(baseUrlApi()+"cidades/"+ng.cliente.id_estado)
		.success(function(data, status, headers, config) {
			ng.cliente.id_cidade = angular.copy(id_cidade);
			console.log(ng.cliente.id_cidade);
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

	ng.loadEstados();
	ng.loadComoEncontrou();
	ng.loadPerfil();
	ng.loadFinalidades();
	ng.loadBancos();
	//ng.loadClientes(0,10);
	//ng.loadEmpreendimento(ng.userLogged.id_empreendimento);
});
