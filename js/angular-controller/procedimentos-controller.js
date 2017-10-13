app.controller('ProcedimentosController', function($scope, $http, $window, $dialogs, UserService,ConfigService,$timeout){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		 = baseUrl();
	ng.userLogged 	 = UserService.getUserLogado();
	ng.configuracoes = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.nova_especialidade = {} ;
	ng.procedimento = {};
	ng.paginacao = {} ;

    ng.editing = false;
    ng.reset = function(){
    	ng.procedimento = {};
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
					ng.reset();
				}
			});
		}
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.cancelarModal = function(id){
		$('#'+id).modal('hide');
	}


	ng.loadEspecialidades= function(offset,limit) {
		aj.get(baseUrlApi()+"clinica/procedimento/especialidades/?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.especialidades = [{id:null,dsc_especialidade:'Selecione'}];
				ng.especialidades = ng.especialidades.concat(data);
				$timeout(function() { $("select").trigger("chosen:updated"); }, 300);
			})
			.error(function(data, status, headers, config) {
				ng.especialidades = [{id:null,dsc_especialidade:'Selecione'}];
			});
	}

	ng.salvarEspecialidade= function(offset,limit) {
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass("has-error");
		var btn = $('#btn-salvar-especialidade');
		btn.button('loading');
		ng.nova_especialidade.id_empreendimento = ng.userLogged.id_empreendimento ;
		aj.post(baseUrlApi()+"especialidade",ng.nova_especialidade)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.nova_especialidade = {} ;
				ng.loadEspecialidades();
				ng.procedimento.id_especialidade = data.id_especialidade;
				$('#modal-nova-especialidade').modal('hide');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406){
		 			var error = 0 ;
		 			$.each(data, function(i, item) {
		 				btn.button('reset');
						$("#"+i).addClass("has-error");
						var formControl = $($("#"+i))
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						if(error == 0) formControl.tooltip('show');
						else formControl.tooltip();

						error ++ ;
					});
		 		}
			});
	}

	ng.salvarProcedimento = function(){

	}

	ng.modalNovaEspecilidade = function(){
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass("has-error");
		$('#modal-nova-especialidade').modal('show');
	}
	
	ng.salvarProcedimento = function(){
		var url = 'procedimento';
		var msg = 'Procedimento cadastrado com sucesso';
		if($.isNumeric(ng.procedimento.id)){
			var url = 'procedimento/update';
			var msg = 'Procedimento atualizado com sucesso';
		}
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass("has-error");
		var btn = $('#salvar-procedimento');
		btn.button('loading');
		ng.procedimento.id_empreendimento = ng.userLogged.id_empreendimento ;
		aj.post(baseUrlApi()+url,ng.procedimento)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.procedimento = {} ;
				ng.mensagens('alert-success',msg,'.alert-procedimeto');
				ng.showBoxNovo();
				$('html,body').animate({scrollTop: 0},'slow');
				ng.loadProcedimentos(0,10);
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406){
		 			var error = 0 ;
		 			$.each(data, function(i, item) {
		 				btn.button('reset');
						$("#"+i).addClass("has-error");
						var formControl = $($("#"+i))
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						if(error == 0) formControl.tooltip('show');
						else formControl.tooltip();
						$('html,body').animate({scrollTop: 0},'slow');
						error ++ ;
					});
		 		}
			});
	};

	ng.loadProcedimentos= function(offset,limit) {
		ng.procedimentos = null ;
		aj.get(baseUrlApi()+"procedimentos/"+offset+"/"+limit+"?tp->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.procedimentos = data.procedimentos ;
				ng.paginacao.procedimentos = data.paginacao ;
			})
			.error(function(data, status, headers, config) {
				ng.procedimentos = [] ;
				ng.paginacao.procedimentos = [] ;
			});
	}

	ng.delete = function(id,$event){
		var btn =  $(event.target) ;
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');

		aj.get(baseUrlApi()+"procedimento/delete/"+id)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','Procedimento excluido com sucesso','.alert-procedimeto');
				$('html,body').animate({scrollTop: 0},'slow');
				ng.loadProcedimentos(0,10);
			})
			.error(function(data, status, headers, config) {
				btn.button('loading');
				alert('Ocorreu um erro ao excluir');
			});
	};

	ng.editar = function(item){
		ng.procedimento = angular.copy(item) ;
		ng.showBoxNovo(true);
		$('html,body').animate({scrollTop: 0},'slow');
	}

	ng.loadEspecialidades();
	ng.loadProcedimentos(0,10);

});
