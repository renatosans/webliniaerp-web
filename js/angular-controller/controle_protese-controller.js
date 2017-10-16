app.controller('ControleProteseController', function($scope, $http, $window, $dialogs, UserService, ConfigService){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.configuracoes  = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.busca 		= {clientes:'',profissionais:'',fornecedores:''} ;
	ng.paginacao    = {};
    ng.lista_emp    = [];
    var proteseControleTO = {  	
		id: null,
		id_empreendimento: ng.userLogged.id_empreendimento,
		id_usuario: ng.userLogged.id,
		id_paciente: null,
		id_profissional_solicitante: null,
		id_laboratorio: null,
		dta_envio: null,
		dta_previsao_entrega: null,
		dta_entrega: null,
		id_status: null
	};
    ng.controle_protese = angular.copy(proteseControleTO);

    ng.editing = false;

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

	ng.selCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

		ng.loadCliente(offset,limit);
		$("#list_clientes").modal("show");
	}

	ng.addCliente = function(item){
		ng.controle_protese.id_paciente = item.id;
		ng.controle_protese.nome_paciente = item.nome;
		$("#list_clientes").modal("hide");
	}

	ng.loadCliente= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+" AND usu.id_perfil=10&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.clientes+"%' OR usu.apelido LIKE '%"+ng.busca.clientes+"%')"}});
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

	ng.selProfissionais = function(){
		var offset = 0  ;
    	var limit  =  10 ;;
		ng.loadProfissionais(offset,limit);
		$("#list_profissionais").modal("show");
	}

	ng.id_profissional_atendimento = null ;
	ng.addProfissional = function(item){
		ng.controle_protese.id_profissional_solicitante = item.id ;
		ng.controle_protese.nome_profissinal = item.nome ;
		$("#list_profissionais").modal("hide");
	}

	ng.loadProfissionais= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.profissionais = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+" AND usu.id_perfil=9&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

		if(ng.busca.profissionais != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.profissionais+"%' OR usu.apelido LIKE '%"+ng.busca.profissionais+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.usuarios,function(i,item){
					ng.profissionais.push(item);
				});
				ng.paginacao_profissionais = [];
				$.each(data.paginacao,function(i,item){
					ng.paginacao_profissionais.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				ng.profissionais = false ;
			});
	}

	ng.selFornecedor = function(){
		var offset = 0  ;
    	var limit  =  10;

			ng.loadFornecedor(offset,limit);
			$("#list_fornecedores").modal("show");
	}

	ng.fornecedor = {} ;
	ng.addFornecedor = function(item){
    	ng.controle_protese.nome_laboratorio = item.nome_fornecedor;
    	ng.controle_protese.id_laboratorio  = item.id;
    	$("#list_fornecedores").modal("hide");
	}

	ng.loadFornecedor = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.fornecedores = [];
		var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento+"&id[exp]=<>"+ng.configuracoes.id_fornecedor_movimentacao_caixa ;
		if(ng.busca.fornecedores != ""){
			query_string += "&"+$.param({'frn->nome_fornecedor':{exp:"like'%"+ng.busca.fornecedores+"%'"}});
		}

		aj.get(baseUrlApi()+"fornecedores/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.fornecedores 		  = data.fornecedores;
				ng.paginacao_fornecedores = data.paginacao ;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.salvarControle = function(){
		$($(".has-error").find('.input-group')).tooltip('destroy');
		$(".has-error").removeClass("has-error");
		var btn = $('#salvar-controle') ;
		btn.button('loading');
		var error = 0 ;
		var dta_envio =  moment($('#input-dta-envio').val(),'DD/MM/YYYY');
		var dta_previsao_entrega =  moment($('#input-dta-previsao-entrega').val(),'DD/MM/YYYY');
		var dta_entrega =  moment($('#input-dta-entrega').val(),'DD/MM/YYYY');
		if(!$.isNumeric(ng.controle_protese.id_profissional_solicitante)){
			$("#id_profissional_solicitante").addClass("has-error");
			var formControl = $('#id_profissional_solicitante').find('.input-group')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "top")
				.attr("title", 'Campo Obrigatório')
				.attr("data-original-title", 'Campo Obrigatório');
			formControl.tooltip();
			if(error == 0) formControl.tooltip('show');

			error ++ ;
		}

		if(!$.isNumeric(ng.controle_protese.id_laboratorio)){
			$("#id_laboratorio").addClass("has-error");
			var formControl = $('#id_laboratorio').find('.input-group')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "top")
				.attr("title", 'Campo Obrigatório')
				.attr("data-original-title", 'Campo Obrigatório');
			formControl.tooltip();
			if(error == 0) formControl.tooltip('show');

			error ++ ;
		}

		if(!$.isNumeric(ng.controle_protese.id_paciente)){
			$("#id_paciente").addClass("has-error");
			var formControl = $('#id_paciente').find('.input-group')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "top")
				.attr("title", 'Campo Obrigatório')
				.attr("data-original-title", 'Campo Obrigatório');
			formControl.tooltip();
			if(error == 0) formControl.tooltip('show');

			error ++ ;
		}
		ng.controle_protese.id_status = 1 ;
		if(dta_envio.isValid()){
			ng.controle_protese.dta_envio = dta_envio.format('YYYY-MM-DD');
			ng.controle_protese.id_status = 2 ;
		}

		if(!dta_previsao_entrega.isValid()){
			$("#dta_previsao_entrega").addClass("has-error");
			var formControl = $('#dta_previsao_entrega').find('.input-group')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "top")
				.attr("title", 'Campo Obrigatório')
				.attr("data-original-title", 'Campo Obrigatório');
			formControl.tooltip();
			if(error == 0) formControl.tooltip('show');

			error ++ ;
		}

		if(dta_entrega.isValid()){
			ng.controle_protese.dta_entrega = dta_entrega.format('YYYY-MM-DD');
			ng.controle_protese.id_status = 3 ;
			if(!dta_envio.isValid()){
				$("#dta_envio").addClass("has-error");
				var formControl = $('#dta_envio').find('.input-group')
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "top")
					.attr("title", 'Campo Obrigatório')
					.attr("data-original-title", 'Campo Obrigatório');
				formControl.tooltip();
				if(error == 0) formControl.tooltip('show');

				error ++ ;
			} 
		}

		if(error > 0){
			btn.button('reset');
			return false ;
		}

		ng.controle_protese.dta_previsao_entrega = dta_previsao_entrega.format('YYYY-MM-DD');

		aj.post(baseUrlApi()+"controle_protese",ng.controle_protese)
		.success(function(data, status, headers, config) {
			ng.mensagens('alert-success','<b>Controle salvo com sucesso</b>','.alert-controle');
			btn.button('reset');
			ng.controle_protese = angular.copy(proteseControleTO);
			ng.showBoxNovo();
			$('html,body').animate({scrollTop: 0},'slow');
			ng.loadControles();
		})
		.error(function(data, status, headers, config) {
			ng.mensagens('alert-danger','<b>Ocorreu um erro ao salvar o controle</b>','.alert-controle-form');
			btn.button('reset');
			$('html,body').animate({scrollTop: 0},'slow');
		});
	
	}
	ng.loadControles= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.controles = null;
		query_string = "?cplSql=tcp.id_empreendimento="+ng.userLogged.id_empreendimento+" ORDER BY tcp.id DESC" ;

		//if(ng.busca.controles != ""){
			//query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.profissionais+"%' OR usu.apelido LIKE '%"+ng.busca.profissionais+"%')"}});
		//}

		aj.get(baseUrlApi()+"controles_protese/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.controles = data.controles ;
				ng.paginacao.controles = data.paginacao ;
			})
			.error(function(data, status, headers, config) {
				ng.controles = [];
				ng.paginacao.controles = [] ;
			});
	}

	ng.changeStatus = function(status,index,event){
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');
		var post = {
			where : 'id='+ng.controles[index].id,
			campos : {
				id_status : status,
				dta_envio : ( Number(status) == 2 ? moment().format('YYYY-MM-DD HH:mm')  : null ),
				dta_entrega : ( Number(status) == 3 ? moment().format('YYYY-MM-DD HH:mm')  : null )
			}
		}

		aj.post(baseUrlApi()+"controle_protese/update",post)
		.success(function(data, status, headers, config) {
			btn.button('reset');
			ng.loadControles();
		})
		.error(function(data, status, headers, config) {
			alert('Ocorreu um erro ao atualizar o status')
			btn.button('reset');
		});
	}

	ng.loadControles();

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
