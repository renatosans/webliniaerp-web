app.controller('LoginController', function($scope, $http, $window,$dialogs,UserService){
	UserService.clearSessionData();
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.dados        = {senha:null,login:null};

	var id_perfil_user = null ;
	var id_usuario     = null ;
	var email_user     = null ;
	var nome_user     = null ;

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.logar = function() {
		ng.reset();
		var btn = $('#btn-logar');
   		btn.button('loading');
		aj.post("util/login/login.php",ng.dados)
			.success(function(data, status, headers, config) {
				//window.location.href = "selecionar_emp.php";
				id_perfil_user = data.id_perfil ;
				id_usuario     = Number(data.id) ;
				email_user     = data.email ;
				nome_user     = data.nome ;
				ng.loadEmpreendimentos(data.id);

			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 404){
					ng.mensagens('alert-danger','E-mail ou senha inválidos.','.alert');
				}
				else if(status == 406){
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
				} else if(status == 500){
					ng.mensagens('alert-danger','<strong>Ocorreu um erro:</strong><br>'+data,'.alert');
				}
			});
		return false;
	}

	ng.loadEmpreendimentos = function(id_usuario) {
		ng.reset();
		var btn = $('#btn-logar');
		$('#alert-acesso-negado').hide();
		aj.get(baseUrlApi() + "empreendimentos?id_usuario="+id_usuario)
			.success(function(data, status, headers, config) {
				if(data.length == 1){
					ng.addEmp(data[0]);
				}else{
					ng.empreendimentos = data;
					$('#list_emp').modal('show');
				}
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				ng.empreendimentos = [] ;
			});
		return false;
	}

	ng.addEmp = function(item) {
		$('.btnSelEmp-'+item.id).button('loading');
		if(Number(item.flg_debito) == 1 && (id_usuario != 222 && id_usuario != 498) ){
			$('#list_emp').modal('hide');
			$('#modal_debito').modal('show');
			return ;
		}

		ng.reset();

		var url = "util/login/login.php?";
		url += "id_empreendimento=" + item.id;
		url += "&nome_empreendimento=" + item.nome_empreendimento;
		url += "&nickname=" + item.nickname;
		url += "&nme_logo=" + item.nme_logo;
		url += "&id=" + id_usuario;
		url += "&nme_usuario=" + nome_user;
		url += "&end_email=" + email_user;
		aj.get(url)
			.success(function(data, status, headers, config) {
				window.location.href = data.pagina_principal;
			})
			.error(function(data, status, headers, config) {
				$('#btnSelEmp').button('reset');
				if(status == 404)
					$('#alert-acesso-negado').show();
				else
					alert('Desculpe, ocorreu um erro inesperado !!!');
			});

	}

});
