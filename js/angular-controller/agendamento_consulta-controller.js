app.controller('AgendamentoConsultaController', function($scope, $http, $window, $dialogs, UserService, ConfigService,$timeout){

	var ng = $scope ,
		aj = $http;
	$scope.userLogged = UserService.getUserLogado();
	ng.configuracoes = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.pacientes     = null;
	ng.profissionais = null ;
	ng.atendimento   = {} ;
	ng.busca         = {'pacientes':"",'profissionais':"",procedimentos:"",id_profissional_atendimento:null};
	var atendimento = {
		id_empreendimento : null ,
		id_paciente : null ,
		id_atendimento_origem : null ,
		dta_entrada : null ,
		dta_inicio_atendimento : null ,
		dta_fim_atendimento : null ,
		id_usuario_entrada : null ,
		id_status : null ,
		id_profissional_atendimento : null,
		procedimentos : [] 
	}
	ng.fecharModal = function(id){
		$(id).modal('hide');
	}
	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().removeClass('alert-sucess alert-warning alert-danger').addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.atendimento = angular.copy(atendimento) ;

	function loadCalendar(id_empreendimento,id_profissional_atendimento) {
		$('#calendar').fullCalendar({
			height: 500,
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay',
			},
			events: {
				url: baseUrlApi()+'clinica/atendimentos/agenda',
				type: 'GET',
      			data: {
            		id_empreendimento:id_empreendimento,
            		id_profissional_atendimento: id_profissional_atendimento
       			},
				error: function() {

				},
				beforeSend:function(){

				},
				complete:function(){

				}
			},
			eventAfterRender: function(event, element) {
       			element.attr('data-placement','top').attr('title',event.title);
       			$(element).tooltip({ container: "body"});
   			},
			eventRender: function(event, element, view) {
				/*var tempalte = '<br>'+event.nome_paciente+
								( empty(event.dsc_especialidade) ? '' : '<br>'+event.dsc_especialidade)+
								'<br>'+event.nome_profissional;
				$('.fc-content .fc-title',element).html('').append(tempalte);*/
				element.attr("data-toggle", "tooltip")
						   .attr("data-placement", "top")
						   .attr("data-original-title", event.dsc_procedimento);
				element.tooltip({ container: "body"});
	     		
	     	},
			defaultDate: NOW('en'),
			lang: 'pt-br',
			editable: false,
			eventLimit: true // allow "more" link when too many events,
		});
	}

	ng.loadPacientes= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.pacientes = null;
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+" AND usu.id_perfil=10&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

		if(ng.busca.pacientes != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.pacientes+"%' OR usu.apelido LIKE '%"+ng.busca.pacientes+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.pacientes = data.usuarios;
				ng.paginacao_pacientes = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.pacientes = [] ;
			});
	}

	ng.selPaciente = function(){
		ng.loadPacientes();
		$("#modalNovoAgendamento").modal('hide');
		$("#list_pacientes").modal('show');
	}

	ng.addPaciente = function(item){
		ng.atendimento.nome_paciente = item.nome ;
		ng.atendimento.id_paciente   = item.id ;
		$('#ui-paciente-atendimento').val(item.nome);
		$("#list_pacientes").modal('hide');
		$("#modalNovoAgendamento").modal('show');
	}

	ng.loadProfissionais= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.profissionais = null;
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+" AND usu.id_perfil=9&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

		if(ng.busca.profissionais != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.profissionais+"%' OR usu.apelido LIKE '%"+ng.busca.profissionais+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.profissionais = data.usuarios;
				ng.paginacao_profissionais = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.profissionais = false ;
			});
	}
	ng.selProfissionais = function(){
		ng.loadProfissionais();
		$("#modalNovoAgendamento").modal('hide');
		$("#list_profissioanais").modal('show');
	}

	ng.addProfissional = function(item){
		ng.atendimento.nome_profissional = item.nome ;
		ng.atendimento.id_profissional_atendimento   = item.id ;
		$('#ui-profissional-atendimento').val(item.nome);
		$("#list_profissioanais").modal('hide');
		$("#modalNovoAgendamento").modal('show');
	}
	ng.selProfissionaisBuscaAgenda = function(){
		ng.loadProfissionais();
		$("#list_profissioanais_busca_agenda").modal('show');
	}
	ng.limparProfissionaisBuscaAgenda = function(){
		ng.busca.id_profissional_atendimento = null ;
		ng.busca.nome_profissional_atendimento = '';
		$('ui-busca-profissional-atendimento').val('');
		$('#calendar').fullCalendar( 'destroy' )
		loadCalendar(ng.userLogged.id_empreendimento,ng.busca.id_profissional_atendimento);
	}
	ng.addProfissionalBuscaAgenda = function(item){
		ng.busca.id_profissional_atendimento = item.id ;
		ng.busca.nome_profissional_atendimento = item.nome ;
		$('#ui-busca-profissional-atendimento').val(item.nome);
		$("#list_profissioanais_busca_agenda").modal('hide');
		$('#calendar').fullCalendar( 'destroy' );
		loadCalendar(ng.userLogged.id_empreendimento,ng.busca.id_profissional_atendimento);
	}

	ng.selProcedimento = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

			ng.loadProcedimentos(offset,limit);
			$("#modalNovoAgendamento").modal('hide');
			$("#list_procedimentos").modal("show");
	}
	ng.procedimento = {} ;
	ng.addProcedimento = function(item){
		ng.atendimento.id_procedimento = item.id;
		ng.atendimento.dsc_procedimento = item.cod_procedimento+" - "+item.dsc_procedimento;
		$("#ui-auto-complete-procedimento").val(item.cod_procedimento+" - "+item.dsc_procedimento);
		ng.atendimento.procedimentos = [];
		ng.atendimento.procedimentos.push(item);
		$("#list_procedimentos").modal("hide");
		$("#modalNovoAgendamento").modal('show');
		
	}

	ng.procedimentos = null ;
	ng.loadProcedimentos= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.procedimentos = [];
		query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento+"&id_especialidade="+ng.atendimento.id_especialidade;

		if(ng.busca.procedimentos != ""){
			query_string += "&"+$.param({'dsc_procedimento':{exp:"like'%"+ng.busca.procedimentos+"%'"}});
		}

		aj.get(baseUrlApi()+"clinica/procedimentos/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.procedimentos,function(i,item){
					ng.procedimentos.push(item);
				});
				ng.paginacao_procedimentos = [];
				$.each(data.paginacao,function(i,item){
					ng.paginacao_procedimentos.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				ng.procedimentos = false ;
			});
	}

	ng.modalNovoAgendamento = function(){
		ng.atendimento = angular.copy(atendimento) ;
		$('#modalNovoAgendamento').modal({
		  backdrop: 'static',
		  keyboard: false
		});
		ng.loadEspecialidades();
		$('#ui-paciente-atendimento').val('');
		$('#ui-profissional-atendimento').val('');
		$('#ui-auto-complete-procedimento').val('');
	}

	ng.loadEspecialidades = function() {
		ng.especialidades = [{id:null,dsc_especialidade:"Selecione"}];
		aj.get(baseUrlApi()+"clinica/procedimento/especialidades?id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.especialidades = ng.especialidades.concat(data) ;
				$timeout(function() {$("select").trigger("chosen:updated");}, 300);
			})
			.error(function(data, status, headers, config) {
				ng.especialidades = [] ;
			});
	}

	ng.incluirAtendimento = function(){
		var btn = $('#incluir-atendimento');
		btn.button('loading');
		var moment_dta_entrada = moment($('#data-atendimento').val()+" "+$('#hora-atendimento').val(),"DD/MM/YYYY HH:mm") ;

		$('.has-error').removeClass("has-error");
		$('.has-error').tooltip('destroy');
		var error = 0 ;

		if(!$.isNumeric(ng.atendimento.id_paciente)){
			var formControl = $("#id_paciente") ;
			formControl.addClass("has-error");
			formControl.attr("data-toggle", "tooltip").attr("data-placement", "bottom").attr("title", 'Selecione o paciente').attr("data-original-title", 'Selecione o paciente');
			formControl.tooltip('show');
			error ++ ;
		}
		if(!$.isNumeric(ng.atendimento.id_profissional_atendimento)){
			var formControl = $("#id_profissional_atendimento") ;
			formControl.addClass("has-error");
			formControl.attr("data-toggle", "tooltip").attr("data-placement", "bottom").attr("title", 'Selecione o profissional').attr("data-original-title", 'Selecione o profissional');
			if(error == 0) formControl.tooltip('show');
			else  formControl.tooltip() ;
			error ++ ;
		}
		if(!moment_dta_entrada.isValid() || (empty($('#data-atendimento').val()) || empty($('#hora-atendimento').val())) ){
			var formControl = $("#data-hora-atendimento") ;
			formControl.addClass("has-error");
			formControl.attr("data-toggle", "tooltip").attr("data-placement", "bottom").attr("title", 'Informe uma data valida').attr("data-original-title", 'Informe uma data valida');
			if(error == 0) formControl.tooltip('show');
			else  formControl.tooltip() ;
			error ++ ;	
		}	
		if(!$.isNumeric(ng.atendimento.id_especialidade)){
			var formControl = $("#id_especialidade") ;
			formControl.addClass("has-error");
			formControl.attr("data-toggle", "tooltip").attr("data-placement", "bottom").attr("title", 'Selecione uma especialidade').attr("data-original-title", 'Informe uma data valida');
			if(error == 0) formControl.tooltip('show');
			else  formControl.tooltip() ;
			error ++ ;	
		}	

		if(error > 0){
			btn.button('reset');
			return false ;
		}

		var post = {
			id_empreendimento : ng.userLogged.id_empreendimento ,
			id_paciente : ng.atendimento.id_paciente ,
			dta_entrada : moment_dta_entrada.format('YYYY-MM-DD HH:mm:ss') ,
			id_usuario_entrada : ng.userLogged.id ,
			id_status : 1 ,
			id_profissional_atendimento : ng.atendimento.id_profissional_atendimento,
			procedimentos : ( ng.atendimento.procedimentos.length > 0 ? ng.atendimento.procedimentos : null )
		}

		aj.post(baseUrlApi()+"clinica/atendimento/save",post)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>atendimento inserido com sucesso</strong>','.alert-novo-atendimento');
				ng.atendimento = atendimento ;
				$('#data-atendimento').val('');
				$('#hora-atendimento').val('');
				$('#ui-paciente-atendimento').val('');
				$('#ui-profissional-atendimento').val('');
				$('#ui-auto-complete-procedimento').val('');
				$('#modalNovoAgendamento').modal('hide');
				btn.button('reset');
				$('#calendar').fullCalendar( 'destroy' )
				loadCalendar(ng.userLogged.id_empreendimento,ng.busca.id_profissional_atendimento);
			})
			.error(function(data, status, headers, config) {
				alert('Error inserir novo atendimento');
				btn.button('reset');
		});	
			
	}

	ng.buscaProcedimentoByCod = function(){
		aj.get(baseUrlApi()+"clinica/get_procedimento_by_cod/"+ng.atendimento.dsc_procedimento )
			.success(function(data, status, headers, config) {
				ng.atendimento.dsc_procedimento = data.cod_procedimento+" - "+data.dsc_procedimento;
				ng.atendimento.id_procedimento = data.id;
				ng.atendimento.procedimentos = [];
			ng.atendimento.procedimentos.push(data);
			})
			.error(function(data, status, headers, config) {
				ng.atendimento.dsc_procedimento = '';
				ng.atendimento.id_procedimento = null;
		});	
	}

	var procedimentos_auto_complete = [] ;
    $( "#ui-auto-complete-procedimento" ).autocomplete({
      source: function (request, response) {
      	var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento+"&id_especialidade="+ng.atendimento.id_especialidade+"&"+$.param({'dsc_procedimento':{exp:"like '%"+request.term.replace(/^\s+|\s+$/g,"")+"%' OR cod_procedimento='"+request.term.replace(/^\s+|\s+$/g,"")+"'"}});
      	aj.get(baseUrlApi()+"clinica/procedimentos"+query_string)
			.success(function(data, status, headers, config) {
				procedimentos_auto_complete = []
				$.each(data,function(i,v){
					procedimentos_auto_complete.push({
						label : v.dsc_procedimento,
						valor : v.dsc_procedimento,
						id    : v.id
					})
				});
				response(procedimentos_auto_complete);
			})
			.error(function(data, status, headers, config) {
				response([]);
		});	
      },
      select:function(event, ui){
      	$scope.$apply(function () {
            ng.atendimento.id_procedimento = ui.item.id ;
        });
      }
    }).keydown(function(e){
    	 var tecla = (e.keyCode?e.keyCode:e.which);
    	 if(tecla == 9 && procedimentos_auto_complete.length == 1){
    	 	$(this).val(procedimentos_auto_complete[0].label);
    	 	$('#data-atendimento').focus();
    	 	$scope.$apply(function () {
            	ng.atendimento.id_procedimento = procedimentos_auto_complete[0].id ;
        	});
    	 	return false ;

    	 }else if(tecla == 9){
    	 	$('#data-atendimento').focus();
    	 	return false ;
    	 }
    }).focus(function(e){
    	$(this).val('');
      	$scope.$apply(function () {
            ng.atendimento.id_procedimento = null ;
        });
    }).blur(function(e){
    	var self = this ;
    	setTimeout(function(){
    		if(!$.isNumeric(ng.atendimento.id_procedimento)){
    			$(self).val('');
    		}
    	},300);
    });

    var profissionais_busca_auto_complete = [] ;
    $( "#ui-busca-profissional-atendimento" ).autocomplete({
      source: function (request, response) {
      	var query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+" AND usu.id_perfil=9&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";
        query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+request.term.replace(/^\s+|\s+$/g,"")+"%' OR usu.apelido LIKE '%"+request.term.replace(/^\s+|\s+$/g,"")+"%')"}});
        aj.get(baseUrlApi()+"usuarios"+query_string)
			.success(function(data, status, headers, config) {
				profissionais_busca_auto_complete = []
				$.each(data.usuarios,function(i,v){
					profissionais_busca_auto_complete.push({
						label : v.nome,
						valor : v.nome,
						id    : v.id
					})
				});
				response(profissionais_busca_auto_complete);
			})
			.error(function(data, status, headers, config) {
				response([]);
		});	
      },
      select:function(event, ui){
        $scope.$apply(function () {
              ng.busca.id_profissional_atendimento = ui.item.id ;
        });
        $('#calendar').fullCalendar( 'destroy' );
        loadCalendar(ng.userLogged.id_empreendimento,ng.busca.id_profissional_atendimento);
      }
    }).keydown(function(e){
    	var tecla = (e.keyCode?e.keyCode:e.which);
    	if(tecla == 9 && profissionais_busca_auto_complete.length == 1){
    	 	$(this).val(profissionais_busca_auto_complete[0].label);
    	 	$scope.$apply(function () {
            	ng.busca.id_profissional_atendimento = profissionais_busca_auto_complete[0].id ;
        });
    	$(this).autocomplete( "close" )
        $('#calendar').fullCalendar( 'destroy' );
        loadCalendar(ng.userLogged.id_empreendimento,ng.busca.id_profissional_atendimento);
    	 	return false ;

    	 }else if(tecla == 9){
    	 	$('#data-atendimento').focus();
    	 	return false ;
    	 }
    }).focus(function(e){
    	$(this).val('');
      	$scope.$apply(function () {
            ng.atendimento.id_procedimento = null ;
        });
    }).blur(function(e){
    	var self = this ;
    	setTimeout(function(){
    		if(!$.isNumeric(ng.atendimento.id_procedimento)){
    			$(self).val('');
    		}
    	},300);
    });

    var profissionais_auto_complete = [] ;
    $( "#ui-profissional-atendimento" ).autocomplete({
      source: function (request, response) {
        var query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+" AND usu.id_perfil=9&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";
        query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+request.term.replace(/^\s+|\s+$/g,"")+"%' OR usu.apelido LIKE '%"+request.term.replace(/^\s+|\s+$/g,"")+"%')"}});
        aj.get(baseUrlApi()+"usuarios"+query_string)
      .success(function(data, status, headers, config) {
        profissionais_auto_complete = []
        $.each(data.usuarios,function(i,v){
          profissionais_auto_complete.push({
            label : v.nome,
            valor : v.nome,
            id    : v.id
          })
        });
        response(profissionais_auto_complete);
      })
      .error(function(data, status, headers, config) {
        response([]);
    }); 
      },
      select:function(event, ui){
        $scope.$apply(function () {
              ng.atendimento.id_profissional_atendimento = ui.item.id ;
        });
        $('#calendar').fullCalendar( 'destroy' );
        loadCalendar(ng.userLogged.id_empreendimento,ng.atendimento.id_profissional_atendimento);
      }
    }).keydown(function(e){
       var tecla = (e.keyCode?e.keyCode:e.which);
       if(tecla == 9 && profissionais_auto_complete.length == 1){
	        $(this).val(profissionais_auto_complete[0].label);
	        $scope.$apply(function () {
	              ng.atendimento.id_profissional_atendimento = profissionais_auto_complete[0].id ;
	        });
     	}
    }).focus(function(e){
      $(this).val('');
        $scope.$apply(function () {
            ng.atendimento.id_profissional_atendimento = null ;
        });
    }).blur(function(e){
      var self = this ;
      setTimeout(function(){
        if(!$.isNumeric(ng.atendimento.id_profissional_atendimento)){
          $(self).val('');
        }
      },300);
    });

    var paciente_auto_complete = [] ;
    $( "#ui-paciente-atendimento" ).autocomplete({
      source: function (request, response) {
      	var query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+" AND usu.id_perfil=10&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";
        query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+request.term.replace(/^\s+|\s+$/g,"")+"%' OR usu.apelido LIKE '%"+request.term.replace(/^\s+|\s+$/g,"")+"%')"}});
        aj.get(baseUrlApi()+"usuarios"+query_string)
			.success(function(data, status, headers, config) {
				paciente_auto_complete = []
				$.each(data.usuarios,function(i,v){
					paciente_auto_complete.push({
						label : v.nome,
						valor : v.nome,
						id    : v.id
					})
				});
				response(paciente_auto_complete);
			})
			.error(function(data, status, headers, config) {
				response([]);
		});	
      },
      select:function(event, ui){
        $scope.$apply(function () {
              ng.atendimento.id_paciente = ui.item.id ;
        });
      }
    }).keydown(function(e){
    	var tecla = (e.keyCode?e.keyCode:e.which);
    	if(tecla == 9 && paciente_auto_complete.length == 1){
    	 	$(this).val(paciente_auto_complete[0].label);
    	 	$scope.$apply(function () {
            	ng.atendimento.id_paciente = paciente_auto_complete[0].id ;
        });
        $('#ui-profissional-atendimento').focus();
    	 	return false ;

    	 }else if(tecla == 9){
    	 	$('#ui-profissional-atendimento').focus();
    	 	return false ;
    	 }
    }).focus(function(e){
    	$(this).val('');
      	$scope.$apply(function () {
            ng.atendimento.id_paciente = null ;
        });
    }).blur(function(e){
    	var self = this ;
    	setTimeout(function(){
    		if(!$.isNumeric(ng.atendimento.id_paciente)){
    			$(self).val('');
    		}
    	},300);
    });

	loadCalendar(ng.userLogged.id_empreendimento,ng.busca.id_profissional_atendimento);

});