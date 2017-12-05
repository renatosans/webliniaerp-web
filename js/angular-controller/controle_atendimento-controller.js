app.controller('ControleAtendimentoController', function($scope, $http, $window, $dialogs, UserService,ConfigService,FuncionalidadeService,$timeout){

	var ng = $scope ,
		aj = $http;
	$scope.userLogged = UserService.getUserLogado();
	ng.configuracoes = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.busca 		 = {clientes:"",profissionais:"",procedimentos:"",odontogramas:"",faces:"",dta:null};
	ng.cliente       = {acao_cliente:'insert'} ;
	ng.paginacao     = {} ;
	var atividadeTO    = {id_procedimento_principal:null,id_procedimento:null,data:'',flg_concluido:0};
	ng.atividade     = angular.copy(atividadeTO);

	 ng.funcioalidadeAuthorized = function(cod_funcionalidade){
    	return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
    }

	$scope.openModal = function(aba){
		$("#modalFichaPaciente").modal('show');
		if(empty(aba)){
			$('.tab-pane').removeClass('in').removeClass('active');
			$('#'+aba).addClass('in').addClass('active');
			$('.tab-bar li').removeClass('active');
			$('[href="#'+aba+'"]').parent().addClass('active');
		}
	}

	ng.showBoxNovo = function(onlyShow){
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

    ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.selProfissionais = function(paciente){
		ng.paciente_atendimento = paciente ;
		var offset = 0  ;
    	var limit  =  10 ;;
		ng.loadProfissionais(offset,limit);
		$("#list_profissionais").modal("show");
	}

	ng.id_profissional_atendimento = null ;
	ng.addProfissional = function(item){
		ng.id_profissional_atendimento = item.id
	}

	ng.loadProfissionais= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.profissionais = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+" AND usu.id_perfil=1547&usu->flg_ativo=1&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

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

	ng.selCliente = function(acao){
		ng.acao_modal_pacientes = acao == null ? 'novo_atendimento' : acao ;
		var offset = 0  ;
    	var limit  =  10 ;;
		ng.loadCliente(offset,limit);
		$("#list_clientes").modal("show");
	}

	ng.addCliente = function(item){
		ng.cliente = item;
		console.log(ng.cliente);
		ng.cliente.acao_cliente = "update"
		$("#list_clientes").modal("hide");
		aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ ng.userLogged.id_empreendimento+"?usu->id="+item.id)
			.success(function(data, status, headers, config) {
				ng.cliente.vlr_saldo_devedor = data.vlr_saldo_devedor;
			})
			.error(function(data, status, headers, config) {
				console.log('erro ao consultar saldo do cliente');
			});
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

	ng.btnInsertCliente = function(){
		ng.cliente = {acao_cliente:'insert',indicacao:0} ;
	}

	ng.cancelarCadastroCliente = function(){
		ng.cliente = {acao_cliente:'insert',indicacao:0} ;
		ng.showBoxNovo();
	}

	ng.novoAtendimento = function(){
		var btn = $("#btn-incluir-fila");
		btn.button('loading');
		$('.has-error').tooltip('destroy');
    	$('.has-error').removeClass('has-error');

    	var url = "clinica/atendimento/cliente/save" ;
		if(ng.cliente.acao_cliente == 'update'){
			url = "clinica/atendimento/cliente/update" ;
			var post = {
				campos : {
					id : ng.cliente.id,
					nome : ng.cliente.nome,
					email : ng.cliente.email ,
					tel_fixo : ng.cliente.tel_fixo ,
					celular : ng.cliente.celular ,
					num_ficha_paciente : ( empty(ng.cliente.num_ficha_paciente) ? null : ng.cliente.num_ficha_paciente )
				},
				empreendimentos : [{id:ng.userLogged.id_empreendimento}],
				where : 'id ='+ng.cliente.id
			};
		}else{
			ng.cliente.empreendimentos = [{id:ng.userLogged.id_empreendimento}] ;
			ng.cliente.id_perfil = 10 ;
			var post = ng.cliente ;
		}
		ng.cliente.empreendimentos = [{id:ng.userLogged.id_empreendimento}] ;
		ng.cliente.id_perfil = 10 ;

    	aj.post(baseUrlApi()+url,post)
			.success(function(data, status, headers, config) {
				ng.cliente.id = data.id ;
				var atendimento = {
					id_empreendimento : ng.userLogged.id_empreendimento ,
					id_paciente : ng.cliente.id,
					dta_entrada : moment().format('YYYY-MM-DD HH:mm:ss'),
					id_usuario_entrada : ng.userLogged.id,
					id_status  : 1 
				}
				aj.post(baseUrlApi()+"clinica/atendimento/save",atendimento)
					.success(function(data, status, headers, config) {
						ng.cancelarCadastroCliente();
						ng.getListaAtendimento();
						btn.button('reset');
					})
					.error(function(data, status, headers, config) {
						btn.button('reset');
				});	
				
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406) {
		 			var errors = data;
		 			var first  = null ;
		 			$.each(data, function(i, item) {
		 				$("#"+i).addClass("has-error");
		 				first = first == null ? $("#"+i) : first ;
		 				var formControl = $($("#"+i))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", item)
		 					.attr("data-original-title", item);
		 				formControl.tooltip();
		 			});
		 			$('html,body').animate({scrollTop: 0},100,function(){
		 				first.focus();
		 				first.tooltip('show');
		 			});
			 	}else{
			 		alert('Erro ao Atualizar Cliente')
			 	}
		});
	}

	ng.saveAtendimento = function(){
		var atendimento = {
			id_empreendimento : ng.userLogged.id_empreendimento ,
			id_paciente : ng.cliente.id,
			dta_entrada : moment().format('YYYY-MM-DD HH:mm:ss'),
			id_usuario_entrada : ng.userLogged.id,
			id_status  : 1 
		}

		$.ajax({
		 	url: baseUrlApi()+'clinica/atendimento/save',
		 	async: false,
		 	method: 'POST',
		 	data: atendimento,
		 	success: function(data) {
		 		
		 	},
		 	error: function(data,x) {
		 		console.log(data);
		 		alert('erro ao iniciar atendimento');
		 	}
		});
	}

	ng.saveCliente = function(){
		var url = "clinica/atendimento/cliente/save" ;
		if(!empty(ng.cliente.acao_cliente == 'update'))
			url = "clinica/atendimento/cliente/update" ;
		ng.cliente.empreendimentos = [{id:ng.userLogged.id_empreendimento}] ;
		var saida = true ;
		$.ajax({
		 	url: baseUrlApi()+url,
		 	async: false,
		 	method: 'POST',
		 	data: ng.cliente,
		 	success: function(data) {
		 		ng.cliente.id = data.id ;
		 	},
		 	error: function(data,x) {
		 		console.log(data);
		 		console.log(x);
		 		if(data.status == 406) {
		 			var errors = data;
		 			var first  = null ;
		 			$.each(data.responseJSON, function(i, item) {
		 				$("#"+i).addClass("has-error");
		 				first = first == null ? $("#"+i) : first ;
		 				var formControl = $($("#"+i))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", item)
		 					.attr("data-original-title", item);
		 				formControl.tooltip();
		 			});
		 			$('html,body').animate({scrollTop: 0},100,function(){
		 				first.focus();
		 				first.tooltip('show');
		 			});
			 	}else{
			 		alert('Erro ao Atualizar Cliente')
			 	}
			 	saida = false ;
		 	}
		});
		return saida ;
	}

	ng.id_paciente_atendimento = null ;
	ng.FinalizarAtendimento = function(paciente){
		ng.id_profissional_atendimento = null ;
		ng.selProfissionais();
		ng.paciente_atendimento = paciente;
	}

	ng.setInitAtendimento = function(paciente,$event){
		var btn =  $(event.target) ;
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');
		var post = {	
			dta_inicio_atendimento:moment().format('YYYY-MM-DD HH:mm:ss'),
			//id_profissional_atendimento : ng.id_profissional_atendimento,
			id_status:2,
			where:'id = '+paciente.id
		};
		aj.post(baseUrlApi()+"clinica/atendimento/update",post)
			.success(function(data, status, headers, config) {
				ng.getListaAtendimento();
				btn.button('loading');
				//$('#list_profissionais').modal('hide');
			})
			.error(function(data, status, headers, config) {
				btn.button('loading');
		});
	}

	ng.salvarHistoricoAtendimento = function(post){	
		aj.post(baseUrlApi()+"historico/paciente",post)
			.success(function(data, status, headers, config) {
				
			})
			.error(function(data, status, headers, config) {
				console.log('Erro ao salvar historico') ;
		});
	}

	ng.atendimento_selecionado = {} ;
	ng.setFimAtendimento = function(){
		setTime = Number(ng.configuracoes.flg_controlar_tempo_atendimento) == 1 ? true : false ;
		var btn =  $("#btn-fim-atendimento") ;
		btn.button('loading');
		ng.atendimento_selecionado = ng.paciente_atendimento ;
		ng.getItensVenda();
		var id_status = 4;
		var id_status_procedimento = 3;
		if(ng.paciente_atendimento.flg_procedimento == 1)
			id_status = 6 ;
		
		var post = {	
			dta_fim_atendimento: ( setTime ? moment().format('YYYY-MM-DD HH:mm:ss') : null ),
			id_status:id_status,
			where:'id = '+ng.paciente_atendimento.id,
			id_item_venda : ng.paciente_atendimento.id_item_venda,
			id_status_procedimento : id_status_procedimento,
			id_profissional_atendimento : ng.id_profissional_atendimento
		};
		aj.post(baseUrlApi()+"clinica/atendimento/update",post)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.getListaAtendimento();
				$('#list_profissionais').modal('hide');
				ng.openModal('procedimentos');
				ng.loadProcedimentos(ng.busca.dta);
				ng.loadPaciente();
				ng.getItensVenda();
				if(ng.paciente_atendimento.flg_procedimento == 1){
					aj.get(baseUrlApi()+"clinica/paciente/"+ng.paciente_atendimento.id_paciente+"/procedimentos?id_status_procedimento[exp]=IN(1,2) AND tiv.id_especialidade_procedimento = "+(!empty(ng.id_especialidade_procedimento)? "'"+ng.id_especialidade_procedimento+"'"  : 'NULL' )+" ")
					.success(function(data, status, headers, config) {
					})
					.error(function(data, status, headers, config) {
						if (status == 404) {
							var post = {
								id_paciente 		: ng.paciente_atendimento.id_paciente ,
								id_usuario 			: ng.userLogged.id, 
								id_profissional 	: null,
								id_acao 			: 1,
								descricao 			: null,
								dta 				: moment().format('YYYY-MM-DD HH:mm:ss'),
								id_empreendimento   : ng.userLogged.id_empreendimento,
								id_profissional     : ng.paciente_atendimento.id_profissional

							}
							ng.salvarHistoricoAtendimento(post);
						}
					});
				}
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
		});
	}	

	ng.cancelarModal = function(id){
		$(id).modal('hide');
	}

	ng.lista_atendimento = [] ;
	ng.disabilitarNovoAtendimento = false ;
	ng.getListaAtendimento = function(dta){
		if(empty(dta)){
			dta = moment().format('YYYY-MM-DD')  ;
			ng.disabilitarNovoAtendimento = false ;
		}else{
			ng.disabilitarNovoAtendimento = true ;
		}
		ng.lista_atendimento = null ;
		aj.get(baseUrlApi()+"clinica/atendimentos?cplSql=ta.id_empreendimento="+ng.userLogged.id_empreendimento+" AND ta.flg_atendimento_fake<>1 AND date_format(ta.dta_entrada,'%Y-%m-%d') = '"+dta+"' ORDER BY ta.dta_entrada ASC")
			.success(function(data, status, headers, config) {
				ng.lista_atendimento = data ;
			})
			.error(function(data, status, headers, config) {
				ng.lista_atendimento = [] ;
		});
	}

	ng.selProcedimento = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

			ng.loadProcedimentos(offset,limit);
			$("#list_procedimentos").modal("show");
	}
	ng.procedimento = {} ;
	ng.addProcedimento = function(item){
		ng.procedimento.id_procedimento = item.id;
		ng.procedimento.dsc_procedimento = item.cod_procedimento+" - "+item.dsc_procedimento;
		$('#ui-auto-complete-procedimento').val(item.cod_procedimento+" - "+item.dsc_procedimento)
		$("#list_procedimentos").modal("hide");
		
	}

	ng.procedimentos = null ;
	ng.loadProcedimentos= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.procedimentos = [];
		query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento+"";

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


	ng.selOdontograma = function(){
        var offset = 0  ;
        var limit  =  10 ;;

            ng.loadOdontogramas(offset,limit);
            $("#list_odontogramas").modal("show");
    }

    ng.addOdontograma = function(item){
        ng.procedimento.id_dente = item.id;
        ng.procedimento.nme_dente = item.cod_dente+" - "+item.nme_dente;
        $('#ui-auto-complete-odontograma').val(item.cod_dente+" - "+item.nme_dente);
        $("#list_odontogramas").modal("hide");
        
    }

    ng.odontogramas = null ;
    ng.loadOdontogramas= function(offset,limit) {
        offset = offset == null ? 0  : offset;
        limit  = limit  == null ? 10 : limit;
        ng.odontogramas = [];
        query_string = "";

        if(ng.busca.odontogramas != ""){
            query_string += "?"+$.param({'nme_dente':{exp:"like'%"+ng.busca.odontogramas+"%' OR cod_dente=like'%"+ng.busca.odontogramas+"%'"}});
        }

        aj.get(baseUrlApi()+"clinica/odontogramas/"+offset+"/"+limit+"/"+query_string)
            .success(function(data, status, headers, config) {
                $.each(data.odontogramas,function(i,item){
                    ng.odontogramas.push(item);
                });
                ng.paginacao_odontogramas = [];
                $.each(data.paginacao,function(i,item){
                    ng.paginacao_odontogramas.push(item);
                });
            })
            .error(function(data, status, headers, config) {
                ng.odontogramas = false ;
            });
    }

    ng.selFaceDente = function(){
        var offset = 0  ;
        var limit  =  10 ;;

            ng.loadFaceDente(offset,limit);
            $("#list_face_dente").modal("show");
    }

    ng.addFaceDente = function(item){
        ng.procedimento.dsc_face = item.cod_face+" - "+item.dsc_face;
		ng.procedimento.id_regiao = item.id;
		$('#ui-auto-complete-face').val(item.cod_face+" - "+item.dsc_face)
        $("#list_face_dente").modal("hide");
        
    }

    ng.faces = null ;
    ng.loadFaceDente= function(offset,limit) {
        offset = offset == null ? 0  : offset;
        limit  = limit  == null ? 10 : limit;
        ng.faces = [];
        query_string = "";

        if(ng.busca.faces != ""){
            query_string += "?"+$.param({'dsc_face':{exp:"like'%"+ng.busca.faces+"%' OR cod_face=like'%"+ng.busca.faces+"%'"}});
        }

        aj.get(baseUrlApi()+"clinica/faces/"+offset+"/"+limit+"/"+query_string)
            .success(function(data, status, headers, config) {
                $.each(data.faces,function(i,item){
                    ng.faces.push(item);
                });
                ng.paginacao_faces = [];
                $.each(data.paginacao,function(i,item){
                    ng.paginacao_faces.push(item);
                });
            })
            .error(function(data, status, headers, config) {
                ng.faces = false ;
            });
    }

    ng.salvarProcedimento = function(){

    	if(empty(ng.id_especialidade_procedimento)){
			$dialogs.notify('Atenção!','<strong>Selecione uma especialidade antes de executar esta ação.</strong>');
			return ;
		}

    	var error = 0 ;
    	$('.has-error').tooltip('destroy');
    	$('.has-error').removeClass('has-error');
    	var btn = $('#incluir-procedimento');
    	btn.button('loading');

		if(empty(ng.procedimento.id_procedimento)){
			error ++ ;
			 $("#id_procedimento").addClass("has-error");
				var formControl = $("#id_procedimento")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Campo obrigatório')
					.attr("data-original-title", 'Campo obrigatório');
			if(error == 1)
				formControl.tooltip('show');
			else
				formControl.tooltip();
		}

		/*if(empty(ng.procedimento.id_dente)){
			error ++ ;
			 $("#id_dente").addClass("has-error");
				var formControl = $("#id_dente")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Campo obrigatório')
					.attr("data-original-title", 'Campo obrigatório');
			if(error == 1)
				formControl.tooltip('show');
			else
				formControl.tooltip();
		}*/

		/*if(empty(ng.procedimento.id_regiao)){
			error ++ ;
			 $("#id_regiao").addClass("has-error");
				var formControl = $("#id_regiao")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Campo obrigatório')
					.attr("data-original-title", 'Campo obrigatório');
			if(error == 1)
				formControl.tooltip('show');
			else
				formControl.tooltip();
		}*/

		if(empty(ng.procedimento.valor)){
			/*error ++ ;
			 $("#valor").addClass("has-error");
				var formControl = $("#valor")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Campo obrigatório')
					.attr("data-original-title", 'Campo obrigatório');
			if(error == 1)
				formControl.tooltip('show');
			else
				formControl.tooltip();*/
			ng.procedimento.valor = 0 ;
		}

		if(error > 0){
			btn.button('reset');
			return ;
		}

    	ng.venda = {
    			id_usuario : ng.userLogged.id ,
				id_cliente : ng.atendimento_selecionado.id_paciente ,
				venda_confirmada : 0 ,
				dta_venda : moment().format('YYYY-MM-DD HH:mm:ss'),
				id_empreendimento : ng.userLogged.id_empreendimento ,
				id_status_venda : 5 ,
				id_atendimento : ng.atendimento_selecionado.id,
				gerar_atendimento_fake : ( ng.hide_add_procedimentos === true ? 1 : 0 )
    		}

    	aj.post(baseUrlApi()+"clinica/gravarVenda",{venda:ng.venda})
			.success(function(data, status, headers, config) {
				ng.atendimento_selecionado.id_venda = data.id_venda;
				var item = {
					desconto_aplicado : 0 ,
					valor_desconto : 0 ,
					id_procedimento : ng.procedimento.id_procedimento ,
					id_dente : ng.procedimento.id_dente ,
					id_regiao : ng.procedimento.id_regiao,
					qtd : 1 ,
					valor_real_item : ng.procedimento.valor,
					vlr_custo: 0 ,
					perc_imposto_compra: 0,
					perc_desconto_compra: 0,
					perc_margem_aplicada: 0,
					id_status_procedimento : 1,
					id_especialidade_procedimento : ng.id_especialidade_procedimento
				};
			 	var post = {
			 		produtos : [item],
					id_empreendimento : ng.userLogged.id_empreendimento,
					id_deposito : ng.caixa_aberto.id_deposito,
					id_venda : ng.atendimento_selecionado.id_venda
			 	}
			 	aj.post(baseUrlApi()+"clinica/gravarItensVenda",post)
					.success(function(data, status, headers, config) {
						btn.button('reset');
						ng.getItensVenda();	
						ng.procedimento.id_procedimento = null ;
						ng.procedimento.id_dente = null ;
						ng.procedimento.id_regiao = null ;
						ng.procedimento.valor = null ;
						ng.procedimento.dsc_procedimento = '';
						ng.procedimento.nme_dente = '';
						ng.procedimento.dsc_face = '';
						ng.procedimento.valor = '';
						$('#ui-auto-complete-face').val('');
						$('#ui-auto-complete-odontograma').val('');
						$('#ui-auto-complete-procedimento').val('');
					})
					.error(function(data, status, headers, config) {
						btn.button('reset');
						alert('Erro ao cadastrar venda');
					});
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				alert('Erro ao cadastrar venda');
			});
    }
    ng.itens_venda = [] ;
    ng.getItensVenda = function(){
    	ng.itens_venda = null ;
   		aj.get(baseUrlApi()+"clinica/paciente/"+ ng.atendimento_selecionado.id_paciente +"/procedimentos?cplSql=( (ta.id_atendimento_origem IS NULL) OR (ta.id_atendimento_origem IS NOT NULL AND tiv.id_procedimento_principal IS NOT NULL )) AND tiv.id_especialidade_procedimento = "+(!empty(ng.id_especialidade_procedimento)? "'"+ng.id_especialidade_procedimento+"'"  : 'NULL' )+" ")
			.success(function(data, status, headers, config) {
				ng.itens_venda = data ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.itens_venda = [];
		});
	}

	ng.totalItensVenda = function(){
		var vlr_total = 0 ;
		$.each(ng.itens_venda,function(i,x){
			vlr_total += x.valor_real_item;
		});
		//ng.vlrTotalCompra = numberFormat(vlr_total,2,'.','') ;
		return vlr_total ;
	}

	ng.totalItensVendaAgendados = function(){
		var vlr_total = 0 ;
		$.each(ng.itens_venda,function(i,x){
			if(Number(x.id_status_procedimento == 2) || Number(x.id_status_procedimento == 3))
				vlr_total += x.valor_real_item;
		});
		//ng.vlrTotalCompra = numberFormat(vlr_total,2,'.','') ;
		//console.log(vlr_total);
		return vlr_total ;
	}

	ng.totalItensVendaNaoAgendados = function(){
		var vlr_total = 0 ;
		$.each(ng.itens_venda,function(i,x){
			if(Number(x.id_status_procedimento == 1))
				vlr_total += x.valor_real_item;
		});
		//ng.vlrTotalCompra = numberFormat(vlr_total,2,'.','') ;
		//console.log(vlr_total);
		return vlr_total ;
	}

    ng.abrirCaixa = function(){
   		aj.get(baseUrlApi()+"pedido_venda/abrir_caixa/"+ ng.configuracoes.id_caixa_atendimento_clinica +"/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.caixa_aberto = data ;
				ng.caixa = data ;
			})
			.error(function(data, status, headers, config) {
				alert(data);
		});
	}

	// funçãoes para pagamentos
		ng.loadMaquinetas = function() {
		ng.maquinetas = [];

		aj.get(baseUrlApi()+"maquinetas/?maq->id_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.maquinetas 			= data.maquinetas;
				ng.paginacao.maquinetas = [] ;
			})
			.error(function(data, status, headers, config) {
				ng.paginacao.maquinetas = [] ;
			});
	}

	ng.bancos = [] ;
	ng.loadBancos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.bancos = [];

		aj.get(baseUrlApi()+"bancos")
			.success(function(data, status, headers, config) {
				ng.bancos = data.bancos;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.loadContas = function() {
		aj.get(baseUrlApi()+"contas_bancarias?cnt->id_tipo_conta[exp]=!=5&id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.contas = data.contas;
			})
			.error(function(data, status, headers, config) {
				ng.contas = [] ;
			});
	}
	ng.initVarPag = function(){
		ng.total_pg             = 0 ;
		ng.troco				= 0;
		ng.pagamentos           = [];
		ng.vlrTotalCompra	    = 0;
		ng.formas_pagamento = [
			{nome:"Dinheiro",id:3},
			{nome:"Cheque",id:2},
			{nome:"Boleto Bancário",id:4},
			{nome:"Cartão de Débito",id:5},
			{nome:"Cartão de Crédito",id:6},
			{nome:"Transferência",id:8}
		  ];
		ng.cheques					=[{id_banco:null,valor:0,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0}];
		ng.boletos					= [{id_banco:null,num_conta_corrente:null,num_cheque:null,status_pagamento:0}];
		ng.recebidos = [] ;
		ng.tab_pagamentos = false ;
	}

	ng.initVarPag();

	ng.telaPagamento = function () {
		var error = 0 ;
    	$('.has-error').tooltip('destroy');
    	$('.has-error').removeClass('has-error');

		dtaVenda	= $("#dtaVenda").val();
		dtaEntrega 	= $("#dtaEntrega").val();

		if(empty(ng.cliente.acao_cliente)){
			$dialogs.notify('Atenção!','<strong>Informe um Cliente Para o Pedido.</strong>');
			return ;
		}

		if(ng.length(ng.carrinhoPedido) == 0){
			$dialogs.notify('Atenção!','<strong>Nunhum Produto Foi Montado.</strong>');
			return ;
		}

		if(empty(dtaVenda)){
			error ++ ;
			$("#label-dta-venda").addClass("has-error");
			 $("#form-dta-venda").addClass("has-error");
				var formControl = $("#form-dta-venda")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Campo obrigatório')
					.attr("data-original-title", 'Campo obrigatório');
				formControl.tooltip('show');
		}

		if(empty(dtaEntrega)){
			error ++ ;
			$("#label-dta-entrega").addClass("has-error");
			$("#form-dta-entrega").addClass("has-error");
				var formControl = $("#form-dta-entrega")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Campo obrigatório')
					.attr("data-original-title", 'Campo obrigatório');
				formControl.tooltip('show');
		}

		if(error > 0)
			return ;
			ng.tela = 'receber_pagamento';
			//ng.receber_pagamento = true ;
			$('html,body').animate({scrollTop: 0},100);
	}

	ng.receberPagamento = function(){
		var produtos = angular.copy(ng.carrinho);
		var venda    = {
							id_usuario:ng.userLogged.id,
							id_cliente:parseInt(ng.cliente.id),
							venda_confirmada:1,
							id_empreendimento:ng.userLogged.id_empreendimento,
							id_deposito : ng.caixa.id_deposito
						};

		venda.id_cliente = isNaN(venda.id_cliente) ? "" : venda.id_cliente;

		$.each(produtos,function(index,value){
			produtos[index].venda_confirmada 	= 1 ;
			produtos[index].valor_produto 		= value.vlr_unitario;
			produtos[index].qtd           		= value.qtd_total;

			if(value.flg_desconto != null && Number(value.valor_desconto) > 0 && !isNaN(Number(value.valor_desconto))){
				produtos[index].desconto_aplicado	= parseInt(value.flg_desconto) != 1 && isNaN(parseInt(value.flg_desconto)) ? 0 : 1 ;
				produtos[index].valor_desconto      = parseInt(value.flg_desconto) == 1 ? value.valor_desconto/100 : 0 ;
			} else {
				produtos[index].desconto_aplicado	= 0 ;
				produtos[index].valor_desconto      = 0 ;
			}
		});

		/*
		* agrupando os produtos de 10 em 10
		*/

		var index_current 	  = 0  ;
		var n_repeat 	  	  = 10 ;
		var repeat_count      = 0  ;
		var produtos_enviar   = [] ;


		$.each(produtos,function(index,obj){
			if(repeat_count >= n_repeat){
					index_current ++ ;
					repeat_count = 0 ;
			}

			if(!(produtos_enviar[index_current] instanceof Array)){
				produtos_enviar[index_current] = [];
			}

			produtos_enviar[index_current].push(obj);
			repeat_count ++ ;
		});
		ng.out_produtos = [] ;
		ng.out_descontos = [] ;
		ng.verificaEstoque(produtos_enviar,0,'receber');
	}

	ng.receber = function(){
		if(!ng.vlrTotalCompra > 0){
			$dialogs.notify('Atenção!','<strong>Não há nenhum valor à receber</strong>');
			return;
		}
		$('#modal-receber').modal('show');
	}

	ng.totalPagamento = function(){
		var total = 0 ;
		$.each(ng.recebidos,function(i,v){
			total += Number(v.valor);
		});
		ng.total_pg = Math.round( total * 100) /100 ;
		console.log(ng.total_pg);
		console.log(ng.vlrTotalCompra);
	}

	ng.calculaTroco = function(){
		var troco = 0 ;
		troco = ng.total_pg - ng.vlrTotalCompra;
		if(troco > 0)
			ng.troco = troco;
		else
			ng.troco = 0 ;
	}

	ng.pagamento = {};
	ng.pg_cheques = [] ;
	ng.aplicarRecebimento = function(){
		var restante  = Math.round((ng.vlrTotalCompra - ng.total_pg) * 100) /100 ;
		if((ng.pagamento.valor > restante) && (ng.pagamento.id_forma_pagamento != 3) && (ng.modo_venda == 'pdv')){
			ng.mensagens('alert-warning','<strong>o valor do pagamento utrapassa o valor restante à receber</strong>','.alert-pagamento');
			return;
		}

		if(ng.pagamento.id_forma_pagamento == 7 && ng.pagamento.valor > restante && (ng.modo_venda == 'pdv')){ 
			ng.mensagens('alert-warning','<strong>o valor do pagamento utrapassa o valor restante à receber</strong>','.alert-pagamento');
			return;
		}

		var error = 0 ;
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass('has-error');
		if(ng.pagamento.id_forma_pagamento ==  undefined || ng.pagamento.id_forma_pagamento ==  ''){
			error ++ ;
			$("#pagamento_forma_pagamento").addClass("has-error");

			var formControl = $("#pagamento_forma_pagamento")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'A escolha da forma de pagamento é obrigatória')
				.attr("data-original-title", 'A escolha da forma de chequ é obrigatória');
			formControl.tooltip();
		}
		if(ng.pagamento.valor ==  undefined || ng.pagamento.valor ==  ''){
			error ++ ;
			$("#pagamento_valor").addClass("has-error");

			var formControl = $("#pagamento_valor")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'O valor é obrigatório')
				.attr("data-original-title", 'O valor é obrigatório');
			formControl.tooltip();
		}

		if((ng.pagamento.id_maquineta ==  undefined || ng.pagamento.id_maquineta ==  '') && (ng.pagamento.id_forma_pagamento == 5 || ng.pagamento.id_forma_pagamento == 6 ) ){
			error ++ ;
			$("#pagamento_maquineta").addClass("has-error");

			var formControl = $("#pagamento_maquineta")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'O escolha da maquineta é obrigatório')
				.attr("data-original-title", 'O escolha da maquineta é obrigatório');
			formControl.tooltip();
		}

		if(ng.pagamento.id_forma_pagamento == 2){
			$.each(ng.cheques, function(i,v){
				if($('.cheque_data input').eq(i).val() == "" || $('.cheque_data input').eq(i).val() == undefined ){
					$('.cheque_data').eq(i).addClass("has-error");

					var formControl = $('.cheque_data').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'A data do cheque é obrigatória')
						.attr("data-original-title", 'A data do cheque é obrigatória');
					formControl.tooltip();
					error ++ ;
				}

				if(v.valor_pagamento == "" || v.valor_pagamento == 0 || v.valor_pagamento == undefined ){
					$('.cheque_valor').eq(i).addClass("has-error");

					var formControl = $('.cheque_valor').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O valor do cheque é obrigatório')
						.attr("data-original-title", 'O valor do cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.id_banco == "" || v.id_banco == 0 || v.id_banco == undefined ){
					$('.cheque_banco').eq(i).addClass("has-error");

					var formControl = $('.cheque_banco').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O banco é obrigatório')
						.attr("data-original-title", 'O banco é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_conta_corrente == "" || v.num_conta_corrente == 0 || v.num_conta_corrente == undefined ){
					$('.cheque_cc').eq(i).addClass("has-error");

					var formControl = $('.cheque_cc').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O número da C/C é obrigatório')
						.attr("data-original-title", 'O Num. C/C é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_cheque == "" || v.num_cheque == 0 || v.num_cheque == undefined ){
					$('.cheque_num').eq(i).addClass("has-error");

					var formControl = $('.cheque_num').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O Núm. Cheque é obrigatório')
						.attr("data-original-title", 'O Núm. Cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}
			});

			//ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 4){
			$.each(ng.boletos, function(i,v){
				if($('.boleto_data input').eq(i).val() == "" || $('.boleto_data input').eq(i).val() == undefined ){
					$('.boleto_data').eq(i).addClass("has-error");

					var formControl = $('.boleto_data').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'A data do boleto é obrigatória')
						.attr("data-original-title", 'A data do boleto é obrigatória');
					formControl.tooltip();
					error ++ ;
				}

				if(v.valor_pagamento == "" || v.valor_pagamento == 0 || v.valor_pagamento == undefined ){
					$('.boleto_valor').eq(i).addClass("has-error");

					var formControl = $('.boleto_valor').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O valor do boleto é obrigatório')
						.attr("data-original-title", 'O valor do boleto é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.id_banco == "" || v.id_banco == 0 || v.id_banco == undefined ){
					$('.boleto_banco').eq(i).addClass("has-error");

					var formControl = $('.boleto_banco').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O banco é obrigatório')
						.attr("data-original-title", 'O banco é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.doc_boleto == "" || v.doc_boleto == 0 || v.doc_boleto == undefined ){
					$('.boleto_doc').eq(i).addClass("has-error");

					var formControl = $('.boleto_doc').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O documento do boleto é obrigatório')
						.attr("data-original-title", 'O documento do boleto é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_boleto == "" || v.num_boleto == 0 || v.num_boleto == undefined ){
					$('.boleto_num').eq(i).addClass("has-error");

					var formControl = $('.boleto_num').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O Núm. Cheque é obrigatório')
						.attr("data-original-title", 'O Núm. Cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}
			});

			//ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 8){
			if(empty(ng.pagamento.id_banco)){
				$("#pagamento_id_banco").addClass("has-error");
				var formControl = $("#pagamento_id_banco")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Selecione o banco')
					.attr("data-original-title", 'Selecione o banco');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.agencia_transferencia)){
				$("#pagamento_agencia_transferencia").addClass("has-error");
				var formControl = $("#pagamento_agencia_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o número da agência')
					.attr("data-original-title", 'Informe o número da agência');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.conta_transferencia)){
				$("#pagamento_conta_transferencia").addClass("has-error");
				var formControl = $("#pagamento_conta_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o número da conta')
					.attr("data-original-title", 'Informe o número da conta');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.proprietario_conta_transferencia)){
				$("#proprietario_conta_transferencia").addClass("has-error");
				var formControl = $("#proprietario_conta_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o Proprietário da conta')
					.attr("data-original-title", 'Informe o Proprietário da conta');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.id_conta_bancaria)){
				$("#pagamento_id_conta_transferencia_destino").addClass("has-error");
				var formControl = $("#pagamento_id_conta_transferencia_destino")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe a conta de origem')
					.attr("data-original-title", 'Informe a conta de origem');
				formControl.tooltip();
			}
		}

		if(error > 0){
			return;
		}

		if((ng.pagamento.id_forma_pagamento == 6 || ng.pagamento.id_forma_pagamento == 2 || ng.pagamento.id_forma_pagamento == 4 ) && (ng.pagamento.parcelas ==  undefined || ng.pagamento.parcelas ==  '') ){
			ng.pagamento.parcelas = 1 ;
		}

		var push = true ;

		if(ng.pagamento.id_forma_pagamento == 2){


			$.each(ng.recebidos,function(a,b){
				if(Number(b.id_forma_pagamento) == 2){
					ng.recebidos.splice(a,1);
				}
			});


			var valor_parcelas = ng.pagamento.valor / ng.pagamento.parcelas;
			var count = 0 ;
			ng.pg_cheques = [];
			$.each(ng.cheques,function(index,value){
				value.id_forma_pagamento  	= ng.pagamento.id_forma_pagamento;
				value.valor 				= Math.round(valor_parcelas * 100) / 100;
				value.id_maquineta 			= ng.pagamento.id_maquineta;
				value.parcelas 				= 1 ;
				value.data_pagamento		= formatDate($('.chequeData').eq(count).val());
				//value.valor_pagamento		= valor_parcelas;
				ng.pg_cheques.push(value);
				count ++ ;
			});
		}else if(ng.pagamento.id_forma_pagamento == 4){


			$.each(ng.recebidos,function(a,b){
				if(Number(b.id_forma_pagamento) == 4){
					ng.recebidos.splice(a,1);
				}
			});


			var valor_parcelas = ng.pagamento.valor / ng.pagamento.parcelas;
			var count = 0 ;
			ng.pg_boletos = [];
			$.each(ng.boletos,function(index,value){
				value.id_forma_pagamento  	= ng.pagamento.id_forma_pagamento;
				//value.valor 				= Math.round(valor_parcelas * 100) / 100;
				value.id_maquineta 			= ng.pagamento.id_maquineta;
				value.parcelas 				= 1 ;
				value.data_pagamento		= formatDate($('.boletoData').eq(count).val());
			//value.valor_pagamento		= valor_parcelas;
				ng.pg_boletos.push(value);
				count ++ ;
			});
		}

		if(ng.pagamento.id_forma_pagamento == 3){
			$.each(ng.recebidos,function(x,y){
				if(Number(y.id_forma_pagamento) == 3){
					ng.recebidos[x].valor = ng.recebidos[x].valor + ng.pagamento.valor ;
					push = false ;
				}
			});
		}

		if(push){
			if(ng.pagamento.id_forma_pagamento == 8){
				var item = {
								id_forma_pagamento 				 : ng.pagamento.id_forma_pagamento,
								valor              				 : ng.pagamento.valor,
								id_maquineta	   				 : ng.pagamento.id_maquineta,
								parcelas           				 : ng.pagamento.parcelas,
								id_vale_troca     				 : ng.pagamento.id_vale_troca,
								agencia_transferencia            : ng.pagamento.agencia_transferencia,
								conta_transferencia              : ng.pagamento.conta_transferencia,
								proprietario_conta_transferencia : ng.pagamento.proprietario_conta_transferencia,
								id_conta_transferencia_destino   : ng.pagamento.id_conta_transferencia_destino,
								id_banco                         : ng.pagamento.id_banco
						   };
			}else{
				var item = {
								id_forma_pagamento 				 : ng.pagamento.id_forma_pagamento,
								valor              				 : ng.pagamento.valor,
								id_maquineta	   				 : ng.pagamento.id_maquineta,
								parcelas           				 : ng.pagamento.parcelas,
								id_vale_troca     				 : ng.pagamento.id_vale_troca
						   };
			}

			$.each(ng.formas_pagamento,function(i,v){
				if(v.id == ng.pagamento.id_forma_pagamento){
					item.forma_pagamento = v.nome ;
					return;
				}
			});
			ng.recebidos.push(item);
		}
		ng.totalPagamento();
		ng.calculaTroco();
		ng.pagamento = {} ;
	}

	ng.deleteRecebidos = function(index){
		ng.recebidos.splice(index,1);
		ng.totalPagamento();
		ng.calculaTroco();
	}

	ng.cancelarPagamento = function(){
		ng.tela = 'pedido';
		ng.recebidos = [];
		ng.totalPagamento();
		ng.calculaTroco();
	}



	var nParcelasAntCheque = 1 ;
	var nParcelasAntBoleto = 1 ;
	ng.pagamento.parcelas  = 1 ;

	ng.pushCheques = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.parcelas = empty(ng.pagamento.parcelas) ? 1 : ng.pagamento.parcelas ;
			ng.pagamento.parcelas = ng.pagamento.parcelas == "" ?  1 : ng.pagamento.parcelas ;
			if(ng.pagamento.parcelas > nParcelasAntCheque){
				var repeat = parseInt(ng.pagamento.parcelas) - parseInt(nParcelasAntCheque) ;
				while(repeat > 0){
					var item = {id_banco:null,valor_pagamento:0,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0};
					ng.cheques.push(item);
					repeat -- ;
				}
			}else if(ng.pagamento.parcelas < nParcelasAntCheque){
				var repeat = parseInt(nParcelasAntCheque) - parseInt(ng.pagamento.parcelas) ;
				while(repeat > 0){
					var index = ng.cheques.length - 1;
					ng.cheques.splice(index,1);
					repeat -- ;
				}
			}
			nParcelasAntCheque = ng.pagamento.parcelas;
			ng.calTotalCheque();
			setTimeout(function(){ ng.loadDatapicker();}, 1000);
		}else if(ng.pagamento.id_forma_pagamento == 4){
			ng.pagamento.parcelas = empty(ng.pagamento.parcelas) ? 1 : ng.pagamento.parcelas ;
			ng.pagamento.parcelas = ng.pagamento.parcelas == "" ?  1 : ng.pagamento.parcelas ;
			if(ng.pagamento.parcelas > nParcelasAntBoleto){
				var repeat = parseInt(ng.pagamento.parcelas) - parseInt(nParcelasAntBoleto) ;
				while(repeat > 0){
					var item = {id_banco:null,valor_pagamento:0,num_conta_corrente:null,num_cheque:null,status_pagamento:0};
					ng.boletos.push(item);
					repeat -- ;
				}
			}else if(ng.pagamento.parcelas < nParcelasAntBoleto){
				var repeat = parseInt(nParcelasAntBoleto) - parseInt(ng.pagamento.parcelas) ;
				while(repeat > 0){
					var index = ng.cheques.length - 1;
					ng.boletos.splice(index,1);
					repeat -- ;
				}
			}
			nParcelasAntBoleto = ng.pagamento.parcelas;
			ng.calTotalBoleto();
			setTimeout(function(){ ng.loadDatapicker();}, 1000);
		}
	}


	ng.loadDatapicker = function(){
		$(".chequeData").datepicker();
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});

		$(".boletoData").datepicker();
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
	}

	ng.selectChange = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.valor = 0 ;
			ng.pagamento.parcelas = ng.cheques.length  > 0 ? ng.cheques.length : 1 ;
			if(ng.cheques.length > 0)
				ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 6){
			ng.pagamento.parcelas = 1 ;
		}else if(ng.pagamento.id_forma_pagamento == 4){
			ng.pagamento.valor = 0 ;
			ng.pagamento.parcelas = ng.boletos.length  > 0 ? ng.boletos.length : 1 ;
			if(ng.boletos.length > 0)
				ng.calTotalBoleto();
		}	

		ng.loadDatapicker();
	}

	ng.delItemCheque = function($index){
		ng.cheques.splice($index,1);
		ng.pagamento.parcelas = ng.cheques.length ;
		nParcelasAnt  = ng.pagamento.parcelas
	}

	ng.focusData  = function($index){
		if(ng.pagamento.id_forma_pagamento == 2)
			$(".chequeData").eq($index).trigger("focus");
		if(ng.pagamento.id_forma_pagamento == 4)
			$(".boletoData").eq($index).trigger("focus");
	}


	ng.calTotalCheque = function(){
		var valor = 0 ;
		$.each(ng.cheques,function(i,v){
			valor += Number(v.valor_pagamento);
		});

		ng.pagamento.valor = valor;

	}

	ng.calTotalBoleto = function(){
		var valor = 0 ;
		$.each(ng.boletos,function(i,v){
			valor += Number(v.valor_pagamento);
		});

		ng.pagamento.valor = valor;

	}

	ng.qtdCheque = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.parcelas = ng.cheques.length  > 0 ? ng.cheques.length : 1 ;
		}

	}

	ng.pagamentoFulso = function (){
		ng.receber_pagamento = true ;
		ng.venda_aberta 	 = true ;
		ng.pagamento_fulso   = true ;
	}
	ng.modo_venda = 'est';
	ng.salvarPagamento = function(){

	if(empty(ng.id_especialidade_procedimento)){
		$dialogs.notify('Atenção!','<strong>Selecione uma especialidade antes de executar esta ação.</strong>');
		return ;
	}

	var btn = $('#btn-pagamneto');
	btn.button('loading');
    var pagamentos   = [] ;
    //pagamentos
    var Today        = new Date();
    var data_atual   = Today.getDate()+"/"+(Today.getMonth()+1)+"/"+Today.getFullYear();

    $.each(ng.recebidos, function(i,v){
        var parcelas = Number(v.parcelas);

        v.data_pagamento            = formatDate(data_atual);
        v.id_abertura_caixa         = ng.caixa_aberto.id ;
        v.id_plano_conta            = ng.caixa.id_plano_caixa;
        v.id_tipo_movimentacao      = 3;
        v.id_cliente                = ng.atendimento_selecionado.id_paciente;
        v.id_forma_pagamento        = v.id_forma_pagamento;
        v.valor_pagamento           = v.valor;
        v.status_pagamento          = 1;
        v.id_empreendimento         = ng.userLogged.id_empreendimento;
        v.id_conta_bancaria         = ng.caixa.id_caixa;
        v.id_cliente_lancamento     = ng.caixa.id_cliente_movimentacao_caixa;
        v.id_venda        = ng.atendimento_selecionado.id_venda;
        v.id_item_venda   = ng.id_item_venda;

        if(Number(v.id_forma_pagamento) == 6){

            var valor_parcelas   = v.valor/parcelas ;
            var next_date        = somadias(data_atual,30);
            var itens_prc        = [] ;

            for(var count = 0 ; count < parcelas ; count ++){
                var item             = angular.copy(v);
                item.valor_pagamento = valor_parcelas ;
                item.data_pagamento  = formatDate(next_date) ;
                next_date            = somadias(next_date,30);
                item.id_venda        = ng.atendimento_selecionado.id_venda;
                item.id_item_venda   = ng.id_item_venda;
                item.id_especialidade_procedimento = ng.id_especialidade_procedimento;
                itens_prc.push(item);
            }

            pagamentos.push({id_forma_pagamento : v.id_forma_pagamento ,id_tipo_movimentacao: 3, parcelas:itens_prc});

        }else if(Number(v.id_forma_pagamento) == 2){
            $.each(ng.pg_cheques,function(i_cheque, v_cheque){
                v.id_banco              = v_cheque.id_banco ;
                v.num_conta_corrente    = v_cheque.num_conta_corrente ;
                v.num_cheque            = v_cheque.num_cheque ;
                v.flg_cheque_predatado  = v_cheque.flg_cheque_predatado ;
                v.data_pagamento        = v_cheque.data_pagamento ;
                v.valor_pagamento       = v_cheque.valor_pagamento ;
                v.id_venda        = ng.atendimento_selecionado.id_venda;
                v.id_item_venda   = ng.id_item_venda;
                v.id_especialidade_procedimento = ng.id_especialidade_procedimento ;
                v_push = angular.copy(v);
                pagamentos.push(v_push);
            });
        }else if(Number(v.id_forma_pagamento) == 4){
            $.each(ng.pg_boletos,function(i_boleto, v_boleto){
                v.id_banco              = v_boleto.id_banco ;
                v.data_pagamento        = v_boleto.data_pagamento ;
                v.valor_pagamento       = v_boleto.valor_pagamento ;
                v.doc_boleto            = v_boleto.doc_boleto ;
                v.num_boleto            = v_boleto.num_boleto ;
                v.status_pagamento      = v_boleto.status_pagamento ;
                v.id_venda        = ng.atendimento_selecionado.id_venda;
                v.id_item_venda   = ng.id_item_venda;
                 v.id_especialidade_procedimento = ng.id_especialidade_procedimento ;
                v_push = angular.copy(v);
                pagamentos.push(v_push);
            });
        }else{
        	v.id_especialidade_procedimento = ng.id_especialidade_procedimento ;
            pagamentos.push(v);
        }
    });

    if(ng.troco > 0 && ng.modo_venda == 'pdv'){
        $.each(pagamentos,function(key,value){
            if(Number(value.id_forma_pagamento) == 3){
                pagamentos[key].valor           = pagamentos[key].valor_pagamento - ng.troco ;
                pagamentos[key].valor_pagamento = pagamentos[key].valor_pagamento - ng.troco ;
            }
        });
    }

    var vlr_restante = ng.vlrTotalCompra - ng.total_pg;

    if(vlr_restante > 0){
        item = {
        id_abertura_caixa       :ng.caixa_aberto.id,
        id_plano_conta          :ng.caixa.id_plano_caixa,
        id_tipo_movimentacao    : 5,
        valor                   :vlr_restante
        }
        pagamentos.push(item);
    }

    var url = "clinica/gravarMovimentacoes" ;
    var msg = "Pagamento lançado com sucesso" ;

    var post = {
    	id_atendimento : ng.atendimento_selecionado.id,
    	id_venda : ng.atendimento_selecionado.id_venda,
        id_cliente : ng.atendimento_selecionado.id_paciente,
        id_empreendimento : ng.userLogged.id_empreendimento,
        pagamentos:pagamentos
    }
    console.log(post);

    aj.post(baseUrlApi()+url,post)
        .success(function(data, status, headers, config) {
        	//ng.tab_pagamentos = false ;
        	btn.button('reset');
            /*$('.tab-pane').removeClass('in').removeClass('active');
			$('#procedimentos').addClass('in').addClass('active');
			$('.tab-bar li').removeClass('active');
			$('[href="#procedimentos"]').parent().addClass('active');*/
			ng.mensagens('alert-success','<strong>Pagamento realizado com sucesso</strong>','.alert-pagamento');
			ng.getItensVenda();
			ng.initVarPag();
			ng.getListaAtendimento();
			ng.loadPagamentosPaciente();
        })
        .error(function(data, status, headers, config) {
            $btn.button('reset');
            alert("Ocorreu um erro ao efetuar o pagamento");
        });
	}
	ng.loadPaciente = function(){
		 aj.get(baseUrlApi()+"usuario/"+ng.userLogged.id_empreendimento+"/"+ng.atendimento_selecionado.id_paciente)
        .success(function(data, status, headers, config) {
        	ng.dados_paciente	= data ;
        	ng.loadCidadesByEstado();
        })
        .error(function(data, status, headers, config) {
            
        });
	}

	ng.efetuarPagamento = function(item) {
		ng.initVarPag();
		ng.tab_pagamentos = true ; 
		ng.id_item_venda  = item.id ;
		ng.vlrTotalCompra = numberFormat(item.valor_real_item,2,'.','') ;
		$('.tab-pane').removeClass('in').removeClass('active');
		$('#pagamentos').addClass('in').addClass('active');
		$('.tab-bar li').removeClass('active');
		$('[href="#pagamentos"]').parent().addClass('active');
	}

	ng.cancelarPagamento = function(){
		ng.tab_pagamentos = false ;
        $('.tab-pane').removeClass('in').removeClass('active');
		$('#procedimentos').addClass('in').addClass('active');
		$('.tab-bar li').removeClass('active');
		$('[href="#procedimentos"]').parent().addClass('active');
	}
	ng.pagamentosCliente = {} ;
	ng.loadPagamentosPaciente = function(){
		ng.pagamentosCliente.pagamentos = null ;
		 aj.get(baseUrlApi()+"pagamentos/cliente/"+ng.atendimento_selecionado.id_paciente+"?pag->id_especialidade_procedimento="+ng.id_especialidade_procedimento)
            .success(function(data, status, headers, config) {
				ng.pagamentosCliente.pagamentos = data.pagamentos ;
				ng.pagamentosCliente.total = 0 ;
				$.each(data.pagamentos,function(i,v){
					ng.pagamentosCliente.total += v.valor_pagamento ;
				});
         }).error(function(data, status, headers, config) {
   					ng.pagamentosCliente.pagamentos = [] ;
            });

          console.log(ng.pagamentosCliente);
	}
	ng.abrirFichaPaciente = function(paciente,acao){
		ng.hide_add_procedimentos = false ;
		if(acao == 'ver_ficha'){
			ng.hide_add_procedimentos = true ;
			$('#list_clientes').modal('hide');
			ng.atendimento_selecionado = {id_paciente : paciente.id};
			ng.getItensVenda();
			ng.loadPagamentosPaciente();
			ng.loadPaciente();
			ng.loadPagamentosPaciente();
			ng.openModal('dados');
			ng.loadProcedimentos();
		}else if(empty(paciente.id_atendimento_origem)){
			ng.atendimento_selecionado = paciente ;
			ng.getItensVenda();
			ng.loadPagamentosPaciente();
			ng.loadPaciente();
			ng.loadPagamentosPaciente();
			ng.openModal('dados');
			ng.loadProcedimentos();

		}else{
			 aj.get(baseUrlApi()+"clinica/atendimento/"+paciente.id_atendimento_origem)
            .success(function(data, status, headers, config) {
				ng.atendimento_selecionado = data ;
				ng.getItensVenda();
				ng.loadPagamentosPaciente();
				ng.loadPaciente();
				ng.openModal('dados');
				ng.loadProcedimentos();
            })
            .error(function(data, status, headers, config) {
   
            });
		
		}
		
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

		aj.get(baseUrlApi()+"cidades/"+ng.dados_paciente.id_estado)
		.success(function(data, status, headers, config) {
			ng.cidades = data;
			if(nome_cidade != null){
				$.each(ng.cidades,function(i,x){
					if(removerAcentos(nome_cidade) == removerAcentos(x.nome)){
						ng.cliente.id_cidade = x.id;
						return false ;
					}
				});
			}
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.salvarDadosPaciente = function(){
		var btn = $('#salvar-dados-paciente');
		btn.button('loading');
		ng.dados_paciente.empreendimentos = [{id:ng.userLogged.id_empreendimento}] ;
		ng.dados_paciente.where = 'id = '+ng.dados_paciente.id ;
		var post = {
			campos : {
				id : ng.dados_paciente.id,
				nome : ng.dados_paciente.nome,
				email : ng.dados_paciente.email ,
				tel_fixo : ng.dados_paciente.tel_fixo ,
				celular : ng.dados_paciente.celular ,
				cep : ng.dados_paciente.cep ,
				endereco : ng.dados_paciente.endereco ,
				numero : ng.dados_paciente.numero ,
				bairro : ng.dados_paciente.bairro ,
				id_estado : ng.dados_paciente.id_estado ,
				id_cidade : ng.dados_paciente.id_cidade,
				cpf : ng.dados_paciente.cpf,
				rg : ng.dados_paciente.rg,
				num_ficha_paciente : ( empty(ng.dados_paciente.num_ficha_paciente) ? null : ng.dados_paciente.num_ficha_paciente )
			},
			empreendimentos : [{id:ng.userLogged.id_empreendimento}],
			where : 'id ='+ng.dados_paciente.id
		};
		aj.post(baseUrlApi()+"clinica/atendimento/cliente/update",post)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.mensagens('alert-success','<strong>Dados atualizados com sucesso</strong>','.alerta-dados-paciente');		
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406) {
		 			var errors = data;
		 			var first  = null ;
		 			$.each(data, function(i, item) {
		 				$("#dados-paciente-"+i).addClass("has-error");
		 				first = first == null ? $("#"+i) : first ;
		 				var formControl = $($("#dados-paciente-"+i))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", item)
		 					.attr("data-original-title", item);
		 				formControl.tooltip();
		 			});
		 			$('html,body').animate({scrollTop: 0},100,function(){
		 				first.focus();
		 				first.tooltip('show');
		 			});
			 	}else{
			 		alert('Erro ao Atualizar Cliente')
			 	}
		});
	}

	ng.agendarProcedimento = function(item,event){
    	var url = "clinica/atendimento/save";
		if(empty(item.dta_inicio_procedimento))
			return false ;
		if(!moment(data).isValid())
			return false ;
		item.dta_inicio_procedimento = ""+item.dta_inicio_procedimento;
		var data = item.dta_inicio_procedimento.substring(4,8)+'-'+item.dta_inicio_procedimento.substring(2,4)+'-'+item.dta_inicio_procedimento.substring(0,2)+' '+item.dta_inicio_procedimento.substring(8,10)+':'+item.dta_inicio_procedimento.substring(10,12)+":00";	

		var btn =  $(event.target) ;
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');

		if(empty(item.id_atendimento_gerado)){
			var atendimento = {
				id_empreendimento : ng.userLogged.id_empreendimento ,
				id_paciente : item.id_cliente,
				id_usuario_entrada : ng.userLogged.id,
				id_status  : 1 ,
				dta_entrada : data,
				id_atendimento_origem : item.id_atendimento,
				id_venda : item.id_venda,
				id_item_venda : item.id,
				id_status_procedimento : 2
			}
		}else{
			var url = "clinica/atendimento/update";
			var atendimento = {
				dta_entrada : data,
				where : "id = "+item.id_atendimento_gerado,
				id_item_venda : item.id,
				id_status_procedimento : 2
			}
		}
	

		aj.post(baseUrlApi()+url,atendimento)
			.success(function(data, status, headers, config) {
				ng.getListaAtendimento();
				ng.getItensVenda();
				ng.loadPagamentosPaciente();
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
		});	

		
	}

	ng.buscaProcedimentoByCod = function(){
		aj.get(baseUrlApi()+"clinica/get_procedimento_by_cod/"+ng.procedimento.dsc_procedimento )
			.success(function(data, status, headers, config) {
				ng.procedimento.dsc_procedimento = data.cod_procedimento+" - "+data.dsc_procedimento;
				ng.procedimento.id_procedimento = data.id;
			})
			.error(function(data, status, headers, config) {
				ng.procedimento.dsc_procedimento = '';
				ng.procedimento.id_procedimento = null;
		});	
	}

	ng.buscaDenteByCod = function(){
		aj.get(baseUrlApi()+"clinica/get_odontograma_by_cod/"+ng.procedimento.nme_dente )
			.success(function(data, status, headers, config) {
				ng.procedimento.nme_dente = data.cod_dente+" - "+data.nme_dente;
				ng.procedimento.id_dente = data.id;
			})
			.error(function(data, status, headers, config) {
				ng.procedimento.nme_dente = '';
				ng.procedimento.id_dente = null;
		});	
	}

	ng.buscaFaceDenteByCod = function(){
		aj.get(baseUrlApi()+"clinica/get_face_dente_by_cod/"+ng.procedimento.dsc_face )
			.success(function(data, status, headers, config) {
				ng.procedimento.dsc_face = data.cod_face+" - "+data.dsc_face;
				ng.procedimento.id_regiao = data.id;
			})
			.error(function(data, status, headers, config) {
				ng.procedimento.dsc_face = '';
				ng.procedimento.id_regiao = null;
		});	
	}


    ng.selAtividadeProcedimento = function(tipo){
    	ng.busca.procedimentos = ''; 
    	ng.atividade_tipo_procedimento = tipo;
		var offset = 0  ;
    	var limit  =  10 ;
		ng.loadAtividadeProcedimentos(offset,limit,tipo);
		$("#list_atividade_procedimentos").modal("show");

	}

	ng.addAtividadeProcedimento = function(item,tipo){
		if(tipo == 'principal'){
			ng.atividade.id_procedimento_principal = item.id_procedimento;
			ng.atividade.id_atendimento_origem 	   = item.id_atendimento;
			ng.atividade.id_item_venda_principal   = item.id;
			//ng.atividade.id_procedimento_principal	 = item.cod_procedimento+" - "+item.dsc_procedimento;
			$('#ui-auto-complete-procedimento-principal-atividade').val(item.cod_procedimento+" - "+item.dsc_procedimento)
			$("#list_atividade_procedimentos").modal("hide");
		}else{
			ng.atividade.id_procedimento = item.id;
			//ng.atividade.id_procedimento	 = item.cod_procedimento+" - "+item.dsc_procedimento;
			$('#ui-auto-complete-procedimento-atividade').val(item.cod_procedimento+" - "+item.dsc_procedimento)
			$("#list_atividade_procedimentos").modal("hide");
		}
		
	}

	ng.atividade_procedimentos = null ;
	ng.loadAtividadeProcedimentos= function(offset,limit,tipo) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.procedimentos = [];
		if(tipo=="principal"){
			var query_string = "clinica/paciente/"+ng.atendimento_selecionado.id_paciente+"/procedimentos/"+offset+"/"+limit+"?tiv->id_procedimento_principal[exp]=IS NULL&tav->id_item_venda[exp]= IS NULL&tiv->id_status_procedimento[exp]= IN(1,2) AND tiv.id_especialidade_procedimento = "+(!empty(ng.id_especialidade_procedimento)? "'"+ng.id_especialidade_procedimento+"'"  : 'NULL' )+" ";
			if(ng.busca.procedimentos != ""){
				query_string += "&"+$.param({'tp->dsc_procedimento':{exp:"like'%"+ng.busca.procedimentos+"%'"}});
			}
		}else{
			query_string = "clinica/procedimentos/"+offset+"/"+limit+"/?id_empreendimento="+ng.userLogged.id_empreendimento+"";
			if(ng.busca.procedimentos != ""){
				query_string += "&"+$.param({'dsc_procedimento':{exp:"like'%"+ng.busca.procedimentos+"%'"}});
			}
		}
		
		aj.get(baseUrlApi()+query_string)
			.success(function(data, status, headers, config) {		
					ng.atividade_procedimentos = tipo == 'principal' ? data.atendimentos : data.procedimentos;
					ng.atividade_paginacao_procedimentos = data.paginacao ;
			})
			.error(function(data, status, headers, config) {
				ng.procedimentos = false ;
				ng.atividade_procedimentos = false ;
			});
	}

	ng.selAtividadeProfissionais = function(paciente){
		var offset = 0  ;
    	var limit  =  10 ;;
		ng.loadAtividadeProfissionais(offset,limit);
		$("#list_atividade_profissionais").modal("show");
	}

	ng.addAtividadeProfissional = function(item){
		ng.atividade.id_profissional = item.id;
		ng.atividade.nome_profissional = item.nome ;
		$('#ui-auto-complete-profissioal-atividade').val(item.nome);
		$("#list_atividade_profissionais").modal("hide");
	}

	ng.loadAtividadeProfissionais= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.atividade_profissionais = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+" AND usu.id_perfil=1547&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

		if(ng.busca.profissionais != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.profissionais+"%' OR usu.apelido LIKE '%"+ng.busca.profissionais+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.usuarios,function(i,item){
					ng.atividade_profissionais.push(item);
				});
				ng.paginacao_atividade_profissionais = [];
				$.each(data.paginacao,function(i,item){
					ng.paginacao_atividade_profissionais.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				ng.profissionais = false ;
			});
	}

	ng.salvarAtividadeProcedimento = function(){

		if(empty(ng.id_especialidade_procedimento)){
			$dialogs.notify('Atenção!','<strong>Selecione uma especialidade antes de executar esta ação</strong>');
			return ;
		}

		var btn = $('#salvar-atividade-procedimento');
		btn.button('loading');
		var error = 0 ;
		$('.has-error input').tooltip('destroy');
    	$('.has-error').removeClass('has-error');

    	if(empty(ng.atividade.id_profissional)){
			$("#ui-auto-complete-profissioal-atividade").parents('.form-group').addClass("has-error");
			var formControl = $("#ui-auto-complete-profissioal-atividade")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'Campo obrigatório')
				.attr("data-original-title", 'Campo obrigatório');
			if(error == 0) formControl.tooltip('show');
			else formControl.tooltip();
			error ++ ;
		}		
		if(empty(ng.atividade.id_procedimento_principal)){
			$("#ui-auto-complete-procedimento-principal-atividade").parents('.form-group').addClass("has-error");
			var formControl = $("#ui-auto-complete-procedimento-principal-atividade")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'Campo obrigatório')
				.attr("data-original-title", 'Campo obrigatório');
			if(error == 0) formControl.tooltip('show');
			else formControl.tooltip();
			error ++ ;
		}
		if(empty(ng.atividade.id_procedimento)){
			$("#ui-auto-complete-procedimento-atividade").parents('.form-group').addClass("has-error");
			var formControl = $("#ui-auto-complete-procedimento-atividade")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'Campo obrigatório')
				.attr("data-original-title", 'Campo obrigatório');
			if(error == 0) formControl.tooltip('show');
			else formControl.tooltip();
			error ++ ;
		}
		if(ng.atividade.data.length !=12){
			$("#atividade-data").parents('.form-group').addClass("has-error");
			var formControl = $("#atividade-data")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'Campo obrigatório')
				.attr("data-original-title", 'Campo obrigatório');
			if(error == 0) formControl.tooltip('show');
			else formControl.tooltip();
			error ++ ;
		}
		

		if(error > 0){
			btn.button('reset');
			return;
		}

		var atividade = angular.copy(ng.atividade);
		atividade.data = atividade.data.substring(4,8)+'-'+atividade.data.substring(2,4)+'-'+atividade.data.substring(0,2)+' '+atividade.data.substring(8,10)+':'+atividade.data.substring(10,12)+":00";	
	    venda = {
			id_usuario : ng.userLogged.id ,
			id_cliente : ng.atendimento_selecionado.id_paciente ,
			venda_confirmada : 1 ,
			dta_venda : moment().format('YYYY-MM-DD HH:mm:ss'),
			id_empreendimento : ng.userLogged.id_empreendimento ,
			id_status_venda : 6 ,
			id_atendimento  : null
		}
		aj.post(baseUrlApi()+"clinica/gravarVenda",{venda:venda})
		.success(function(data, status, headers, config) {
			atividade.id_venda = data.id_venda;
			var item = {
				desconto_aplicado : 0 ,
				valor_desconto : 0 ,
				id_procedimento : ng.atividade.id_procedimento ,
				id_procedimento_principal : ng.atividade.id_procedimento_principal ,
				qtd : 1 ,
				valor_real_item : 0,
				vlr_custo: 0 ,
				perc_imposto_compra: 0,
				perc_desconto_compra: 0,
				perc_margem_aplicada: 0,
				id_status_procedimento : 3
			};
		 	var post = {
		 		produtos : [item],
				id_empreendimento : ng.userLogged.id_empreendimento,
				id_deposito : ng.caixa_aberto.id_deposito,
				id_venda : atividade.id_venda
		 	}

		 	aj.post(baseUrlApi()+"clinica/gravarItensVenda",post)
				.success(function(data, status, headers, config) {
					atividade.id_item_venda = data.id_item_venda ;
					var atendimento = {
						id_empreendimento : ng.userLogged.id_empreendimento ,
						id_paciente : ng.atendimento_selecionado.id_paciente,
						id_usuario_entrada : ng.userLogged.id,
						id_status  : 1 ,
						dta_entrada : atividade.data,
						id_atendimento_origem : ng.atividade.id_atendimento_origem,
						id_venda : atividade.id_venda,
						id_item_venda : atividade.id_item_venda,
						id_status : 6,
						id_profissional_atendimento : ng.atividade.id_profissional

					}
					aj.post(baseUrlApi()+"clinica/atendimento/save",atendimento)
						.success(function(data, status,headers, config) {
							if(Number(atividade.flg_concluido) == 0){
								btn.button('reset');
								ng.atividade = angular.copy(atividadeTO);
								$('#ui-auto-complete-profissioal-atividade').val('');
								$('#ui-auto-complete-procedimento-principal-atividade').val('');
								$('#ui-auto-complete-procedimento-atividade').val('');
								ng.mensagens('alert-success','<b>procedimento agendado com sucesso.</b>','.alert-atividades');
								ng.getItensVenda();
								ng.getListaAtendimento();
							}else{
								var post = {
									campos : {
										id_status_procedimento : 3
									},
									where : 'id = '+atividade.id_item_venda_principal
								}
								aj.post(baseUrlApi()+"clinica/item_venda/update",post)
									.success(function(data, status, headers, config) {
										btn.button('reset');
										ng.atividade = angular.copy(atividadeTO);
										$('#ui-auto-complete-profissioal-atividade').val('');
										$('#ui-auto-complete-procedimento-principal-atividade').val('');
										$('#ui-auto-complete-procedimento-atividade').val('');
										ng.mensagens('alert-success','<b>procedimento agendado com sucesso.</b>','.alert-atividades');
										ng.getItensVenda();
										ng.getListaAtendimento();
									})
									.error(function(data, status, headers, config) {
										btn.button('reset');
								});
							}
						})
						.error(function(data, status, headers, config) {
							btn.button('reset');
					});	
				})
				.error(function(data, status, headers, config) {
					btn.button('reset');
					alert('Erro ao cadastrar venda');
				});
		})
		.error(function(data, status, headers, config) {
			btn.button('reset');
			alert('Erro ao cadastrar venda');
		});
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

	ng.empty = function(vlr){
		return empty(vlr);
	}

	//$scope.openModal();
	ng.abrirCaixa();
	ng.getListaAtendimento();
	ng.loadMaquinetas();
	ng.loadBancos();
	ng.loadContas();
	ng.loadEstados();
	ng.loadEspecialidades();

    var procedimentos_auto_complete = [] ;
    $( "#ui-auto-complete-procedimento" ).autocomplete({
      source: function (request, response) {
      	var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento+"&"+$.param({'dsc_procedimento':{exp:"like '%"+request.term.replace(/^\s+|\s+$/g,"")+"%' OR cod_procedimento='"+request.term.replace(/^\s+|\s+$/g,"")+"'"}});
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
      	//$(this).attr('data-id-procedimento',ui.item.id);
      	$scope.$apply(function () {
            ng.procedimento.id_procedimento = ui.item.id ;
        });
      }
    }).keydown(function(e){
    	 var tecla = (e.keyCode?e.keyCode:e.which);
    	 if(tecla == 9 && procedimentos_auto_complete.length == 1){
    	 	//$(this).attr('data-id-procedimento',procedimentos_auto_complete[0].id) ;
    	 	$(this).val(procedimentos_auto_complete[0].label);
    	 	$('#ui-auto-complete-odontograma').focus();
    	 	$scope.$apply(function () {
            	ng.procedimento.id_procedimento = procedimentos_auto_complete[0].id ;
        	});
    	 	return false ;

    	 }else if(tecla == 9){
    	 	$('#ui-auto-complete-odontograma').focus();
    	 	return false ;
    	 }
    }).focus(function(e){
    	$(this).val('');
      	//$(this).attr('data-id-procedimento','') ;
      	$scope.$apply(function () {
            ng.procedimento.id_procedimento = null ;
        });
    }).blur(function(e){
    	var self = this ;
    	setTimeout(function(){
    		if(!$.isNumeric(ng.procedimento.id_procedimento)){
    			$(self).val('');
    		}
    	},300);
    });

    var odontogramas_auto_complete = [] ;
    $( "#ui-auto-complete-odontograma" ).autocomplete({
      source: function (request, response) {
      	 query_string = "?"+$.param({'nme_dente':{exp:"like'%"+request.term.replace(/^\s+|\s+$/g,"")+"%' OR cod_dente='"+request.term.replace(/^\s+|\s+$/g,"")+"'"}});
         aj.get(baseUrlApi()+"clinica/odontogramas"+query_string)
			.success(function(data, status, headers, config) {
				odontogramas_auto_complete = []
				$.each(data,function(i,v){
					odontogramas_auto_complete.push({
						label : v.cod_dente+' - '+v.nme_dente,
						valor : v.cod_dente+' - '+v.nme_dente,
						id    : v.id
					})
				});
				response(odontogramas_auto_complete);
			})
			.error(function(data, status, headers, config) {
				response([]);
		});	
      },
      select:function(event, ui){
      	//$(this).attr('data-id-procedimento',ui.item.id);
      	$scope.$apply(function () {
            ng.procedimento.id_dente = ui.item.id ;
        });
      }
    }).keydown(function(e){
    	 var tecla = (e.keyCode?e.keyCode:e.which);
    	 if(tecla == 9 && odontogramas_auto_complete.length == 1){
    	 	//$(this).attr('data-id-procedimento',odontogramas_auto_complete[0].id) ;
    	 	$(this).val(odontogramas_auto_complete[0].label);
    	 	$('#ui-auto-complete-face').focus();

    	 	$scope.$apply(function () {
            	ng.procedimento.id_dente = odontogramas_auto_complete[0].id ;
        	});
    	 	return false ;

    	 }else if(tecla == 9){
    	 	$('#ui-auto-complete-face').focus();
    	 	return false ;
    	 }
    }).focus(function(e){
    	$(this).val('');
      	//$(this).attr('data-id-procedimento','') ;
      	$scope.$apply(function () {
            ng.procedimento.id_dente = null ;
        });
    }).blur(function(e){
    	var self = this ;
    	setTimeout(function(){
    		if(!$.isNumeric(ng.procedimento.id_dente)){
    			$(self).val('');
    		}
    	},300);
    });

    var regiao_auto_complete = [] ;
    $( "#ui-auto-complete-face" ).autocomplete({
      source: function (request, response) {
      	 query_string = "?"+$.param({'dsc_face':{exp:" like'%"+request.term.replace(/^\s+|\s+$/g,"")+"%' OR cod_face='"+request.term.replace(/^\s+|\s+$/g,"")+"'"}});
         aj.get(baseUrlApi()+"clinica/faces"+query_string)
			.success(function(data, status, headers, config) {
				regiao_auto_complete = []
				$.each(data,function(i,v){
					regiao_auto_complete.push({
						label : v.cod_face+' - '+v.dsc_face,
						valor : v.cod_face+' - '+v.dsc_face,
						id    : v.id
					})
				});
				response(regiao_auto_complete);
			})
			.error(function(data, status, headers, config) {
				response([]);
		});	
      },
      select:function(event, ui){
      	//$(this).attr('data-id-procedimento',ui.item.id);
      	$scope.$apply(function () {
            ng.procedimento.id_regiao = ui.item.id ;
        });
      }
    }).keydown(function(e){
    	 var tecla = (e.keyCode?e.keyCode:e.which);
    	 if(tecla == 9 && regiao_auto_complete.length == 1){
    	 	//$(this).attr('data-id-procedimento',regiao_auto_complete[0].id) ;
    	 	$(this).val(regiao_auto_complete[0].label);
    	 	$('#procedimento-valor').focus();

    	 	$scope.$apply(function () {
            	ng.procedimento.id_regiao = regiao_auto_complete[0].id ;
        	});
    	 	return false ;

    	 }else if(tecla == 9){
    	 	$('#procedimento-valor').focus();
    	 	return false ;
    	 }
    }).focus(function(e){
    	$(this).val('');
      	//$(this).attr('data-id-procedimento','') ;
      	$scope.$apply(function () {
            ng.procedimento.id_regiao = null ;
        });
    }).blur(function(e){
    	var self = this ;
    	setTimeout(function(){
    		if(!$.isNumeric(ng.procedimento.id_regiao)){
    			$(self).val('');
    		}
    	},300);
    });

    var atividade_procedimentos_principal_auto_complete = [] ;
    $( "#ui-auto-complete-procedimento-principal-atividade" ).autocomplete({
      source: function (request, response) {
      	var query_string = "clinica/paciente/"+ng.atendimento_selecionado.id_paciente+"/procedimentos?tiv->id_procedimento_principal[exp]=IS NULL&tav->id_item_venda[exp]= IS NULL&tiv->id_status_procedimento[exp]= IN(1,2) AND tiv.id_especialidade_procedimento = "+(!empty(ng.id_especialidade_procedimento)? "'"+ng.id_especialidade_procedimento+"'"  : 'NULL' )+" ";
      	query_string += "&"+$.param({'(tp->dsc_procedimento':{exp:"like '%"+request.term.replace(/^\s+|\s+$/g,"")+"%' OR tp.cod_procedimento='"+request.term.replace(/^\s+|\s+$/g,"")+"')"}});
      	aj.get(baseUrlApi()+query_string)
			.success(function(data, status, headers, config) {
				atividade_procedimentos_principal_auto_complete = []
				$.each(data,function(i,v){
					atividade_procedimentos_principal_auto_complete.push({
						label : v.dsc_procedimento,
						valor : v.dsc_procedimento,
						id    : v.id_procedimento
					})
				});
				response(atividade_procedimentos_principal_auto_complete);
			})
			.error(function(data, status, headers, config) {
				response([]);
		});	
      },
      select:function(event, ui){
      	//$(this).attr('data-id-procedimento',ui.item.id);
      	$scope.$apply(function () {
            ng.atividade.id_procedimento_principal = ui.item.id ;
        });
      }
    }).keydown(function(e){
    	 var tecla = (e.keyCode?e.keyCode:e.which);
    	 if(tecla == 9 && atividade_procedimentos_principal_auto_complete.length == 1){
    	 	//$(this).attr('data-id-procedimento',atividade_procedimentos_principal_auto_complete[0].id) ;
    	 	$(this).val(atividade_procedimentos_principal_auto_complete[0].label);
    	 	$('#ui-auto-complete-procedimento-atividade').focus();
    	 	$scope.$apply(function () {
            	ng.atividade.id_procedimento_principal = atividade_procedimentos_principal_auto_complete[0].id ;
        	});
    	 	return false ;

    	 }else if(tecla == 9){
    	 	$('#ui-auto-complete-procedimento-atividade').focus();
    	 	return false ;
    	 }
    }).focus(function(e){
    	$(this).val('');
      	//$(this).attr('data-id-procedimento','') ;
      	$scope.$apply(function () {
            ng.atividade.id_procedimento_principal = null ;
        });
    }).blur(function(e){
    	var self = this ;
    	setTimeout(function(){
    		if(!$.isNumeric(ng.atividade.id_procedimento_principal)){
    			$(self).val('');
    		}
    	},300);
    });

    var atividade_procedimentos_auto_complete = [] ;
    $( "#ui-auto-complete-procedimento-atividade" ).autocomplete({
      source: function (request, response) {
      	var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento+"&"+$.param({'dsc_procedimento':{exp:"like '%"+request.term.replace(/^\s+|\s+$/g,"")+"%' OR cod_procedimento='"+request.term.replace(/^\s+|\s+$/g,"")+"'"}});
      	aj.get(baseUrlApi()+"clinica/procedimentos"+query_string)
			.success(function(data, status, headers, config) {
				atividade_procedimentos_auto_complete = []
				$.each(data,function(i,v){
					atividade_procedimentos_auto_complete.push({
						label : v.dsc_procedimento,
						valor : v.dsc_procedimento,
						id    : v.id
					})
				});
				response(atividade_procedimentos_auto_complete);
			})
			.error(function(data, status, headers, config) {
				response([]);
		});	
      },
      select:function(event, ui){
      	//$(this).attr('data-id-procedimento',ui.item.id);
      	$scope.$apply(function () {
            ng.atividade.id_procedimento = ui.item.id ;
        });
      }
    }).keydown(function(e){
    	 var tecla = (e.keyCode?e.keyCode:e.which);
    	 if(tecla == 9 && atividade_procedimentos_auto_complete.length == 1){
    	 	//$(this).attr('data-id-procedimento',atividade_procedimentos_auto_complete[0].id) ;
    	 	$(this).val(atividade_procedimentos_auto_complete[0].label);
    	 	$('#atividade-data').focus();
    	 	$scope.$apply(function () {
            	ng.atividade.id_procedimento = atividade_procedimentos_auto_complete[0].id ;
        	});
    	 	return false ;

    	 }else if(tecla == 9){
    	 	$('#atividade-data').focus();
    	 	return false ;
    	 }
    }).focus(function(e){
    	$(this).val('');
      	//$(this).attr('data-id-procedimento','') ;
      	$scope.$apply(function () {
            ng.atividade.id_procedimento = null ;
        });
    }).blur(function(e){
    	var self = this ;
    	setTimeout(function(){
    		if(!$.isNumeric(ng.atividade.id_procedimento)){
    			$(self).val('');
    		}
    	},300);
    });

});

app.directive('bsTooltip', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            $timeout(function () {
                	  element.find("[data-toggle=tooltip]").tooltip();
                	  element.find(".datepicker").datepicker();
            });
        }
    }
});

