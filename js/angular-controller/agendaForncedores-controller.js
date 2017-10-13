app.controller('AgendaForncedoresController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.categoria 	= {};
    ng.categorias	= [];


    var default_config = {
    	dias_semana : [
			{dia:'Dom',value:0, porcentagem:0, valor:0},
			{dia:'Seg',value:0, porcentagem:0, valor:0},
			{dia:'Ter',value:0, porcentagem:0, valor:0},
			{dia:'Qua',value:0, porcentagem:0, valor:0},
			{dia:'Qui',value:0, porcentagem:0, valor:0},
			{dia:'Sex',value:0, porcentagem:0, valor:0},
			{dia:'Sab',value:0, porcentagem:0, valor:0}
    	],
    	forma_valor 	   : 1,
    	forma_porcentagem  : 0,
    	tipo_diario 	   : 1,
    	tipo_semanal 	   : 0,
    	valor_semanal 	   : 0,
    	porcentagem_semanal: 0,
        excedente          : 0
    }

    ng.simulador = {
        valor_pedido              : null,
        qtd_parcelas              : null,
        intervalo                 : null,
        dta_faturamento           : null,
        dias_primeira_parcela     : null,
        dias_ultima_parcela       : null,
        dta_primeira_parcela      : null,
        dta_limite_ultima_parcela : null,
        pagamento_parcelado       : [] 
    }

    ng.editing = false;
    ng.event   = false ;

    ng.simulador_msg = false ;
    ng.simularPagamento = function(){
        ng.removeError();
        var error = 0 ;
        ng.simulador.pagamento_parcelado = [] ;
        ng.simulador_msg = false ;
        $.each(ng.simulador,function(i,v){
            if((v == '' || v == null) && (i != 'pagamento_parcelado' && i != 'dta_primeira_parcela' && i != 'dta_limite_ultima_parcela')){
                $("#"+i+"").addClass('has-error');
                $("#"+i+"").attr('data-placement','bottom').attr('title',"Este campo é obrigátorio");
                $("#"+i+"").tooltip();
                error ++ ;
            }
        });
        if(error > 0)
            return false

        console.log(ng.simulador);
        ng.simulador.dta_primeira_parcela      = somadias(uiDateFormat(ng.simulador.dta_faturamento,'99/99/999'),Number(ng.simulador.dias_primeira_parcela));
        ng.simulador.dta_limite_ultima_parcela = somadias(ng.simulador.dta_primeira_parcela ,Number(ng.simulador.dias_ultima_parcela));
        ng.simulador.valor_parcela             = ng.simulador.valor_pedido/Number(ng.simulador.qtd_parcelas);

        var simulador           = angular.copy(ng.simulador);
        var parcela_anterior    = simulador.dta_primeira_parcela ; 
        var limite_parcela      = Number(simulador.qtd_parcelas);
        var parcela_corrente    =  simulador.dta_primeira_parcela ;
        var dta_limite_ultima_parcela = formatDate(simulador.dta_limite_ultima_parcela);
        var pagamento_parcelado = [{ vlr_parcela:simulador.valor_parcela,dta_pagamento:parcela_corrente,intervalo:simulador.intervalo }] ;

        while((limite_parcela-1) > 0){
            parcela_corrente = somadias(parcela_corrente,Number(simulador.intervalo));
            pagamento_parcelado.push({vlr_parcela:simulador.valor_parcela ,dta_pagamento:parcela_corrente,intervalo:simulador.intervalo,limite_data:!(formatDate(parcela_corrente) > dta_limite_ultima_parcela)  });
            limite_parcela -- ;
        }
        var dta_ultima_parcela = formatDate(pagamento_parcelado[pagamento_parcelado.length-1].dta_pagamento);

        if( dta_ultima_parcela > dta_limite_ultima_parcela )
            ng.simulador_msg = "Os pagamentos marcados em vermelho excederam o limite de dias permitido";

        ng.simulador.pagamento_parcelado = pagamento_parcelado;
        console.log(pagamento_parcelado);
    }

    ng.loafConfig = function(){
    	ng.configPagamentos = null ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
		.success(function(data, status, headers, config) {
			try {
                if(data.pagamentos_fornecedores_agenda != '')
			 	    ng.configPagamentos = $.parseJSON(data.pagamentos_fornecedores_agenda);
                else{
                    ng.configPagamentos = default_config ;
                }
			}
			catch (err) {
			  	ng.configPagamentos = default_config ;
			}
		})
		.error(function(data, status, headers, config) {
			ng.configPagamentos = default_config ;
		});
    }

    ng.clickDiaSemana = function(item){
    	if(Number(item.value) == 0){
    		item.porcentagem = 0 ;
    		item.valor = 0 ;
    	}
    }

    ng.changeForma = function(tipo){
    	ng.removeError();
    	if(tipo == 'valor' && $(event.target).is(':checked')){
    		 ng.configPagamentos.forma_valor = 1 ;
    		 ng.configPagamentos.forma_porcentagem = 0 ;
    		 ng.configPagamentos.porcentagem_semanal = 0 ;
    		 $.each(ng.configPagamentos.dias_semana, function(i,x){
    			x.porcentagem = 0;	
    		 });
    	}else{
    		 ng.configPagamentos.forma_valor = 0 ;
    		 ng.configPagamentos.forma_porcentagem = 1 ;
    		 ng.configPagamentos.valor_semanal = 0 ;
    		  $.each(ng.configPagamentos.dias_semana, function(i,x){
    			x.valor = 0;	
    		 });
    	}
    }

     ng.changeTipo = function(tipo){
     	ng.removeError();
    	if(tipo == 'diario' && $(event.target).is(':checked')){
    		 ng.configPagamentos.tipo_diario = 1 ;
    		 ng.configPagamentos.tipo_semanal = 0 ;
    		 ng.configPagamentos.porcentagem_semanal = 0 ;
    		 ng.configPagamentos.valor_semanal = 0 ;
    	}else{
    		 ng.configPagamentos.tipo_diario = 0 ;
    		 ng.configPagamentos.tipo_semanal = 1 ;
    		 $.each(ng.configPagamentos.dias_semana, function(i,x){
    			x.porcentagem = 0;
    			x.valor = 1;	
    		 });
    	}
    }

    ng.SalvarConfigPagamento = function(){
        if(ng.configPagamentos != false){
    	ng.removeError();
    	var error = 1 ;
         if(ng.configPagamentos.tipo_diario == 1){
    	    $.each(ng.configPagamentos.dias_semana, function(i,x){
    			if(Number(x.value) == 1){
    				error = 0;
    				return false
    			}
    	    });
        }else{
            error = 0 ;
        }
    	 if(error == 1){
    	 	$(".config-semana").addClass('has-error');
    	 	$(".config-semana").css({background:"#FFE3E3"});
    	 	$(".config-semana").attr('data-placement','top').attr('title',"É obrigátorio a escolha de pelo menos um dia na semana");
       		$(".config-semana").tooltip();
       		return false ;
    	 	
    	 }
    	 error = 0 ;
    	 if(ng.configPagamentos.forma_porcentagem == 1){
    	 	if(ng.configPagamentos.tipo_diario == 1){
    	 	$.each(ng.configPagamentos.dias_semana, function(i,x){
    			if(x.value == 1 && x.porcentagem <=0){
    				error++ ;
    				$(".row-dia-"+i+"").addClass('has-error');
    	 			$(".row-dia-"+i+"").attr('data-placement','bottom').attr('title',"É obrigátorio definir um valor para este dia");
       				$(".row-dia-"+i+"").tooltip();
    			}
    		 });
    	 	}else if(ng.configPagamentos.tipo_semanal == 1){
    	 		if(ng.configPagamentos.porcentagem_semanal<=0){
    	 			error ++ ;
					$(".valor-semanal").addClass('has-error');
	 				$(".valor-semanal").attr('data-placement','bottom').attr('title',"É obrigátorio definir um valor para semana");
   					$(".valor-semanal").tooltip();
   				}
    		}
    	 }

    	 if(ng.configPagamentos.forma_valor== 1){
    	 	if(ng.configPagamentos.tipo_diario == 1){
    	 	$.each(ng.configPagamentos.dias_semana, function(i,x){
    			if(x.value == 1 && x.valor <=0){
    				error++ ;
    				$(".row-dia-"+i+"").addClass('has-error');
    	 			$(".row-dia-"+i+"").attr('data-placement','bottom').attr('title',"É obrigátorio definir um valor para este dia");
       				$(".row-dia-"+i+"").tooltip();
    			}
    		 });
    	 	}else if(ng.configPagamentos.tipo_semanal == 1){
    	 		if(ng.configPagamentos.valor_semanal<=0){
					$(".valor-semanal").addClass('has-error');
	 				$(".valor-semanal").attr('data-placement','bottom').attr('title',"É obrigátorio definir um valor para semana");
   					$(".valor-semanal").tooltip();
   				}
    		}
    	 }

    	 if(error > 0)
    	 	return false ;

         ng.configPagamentos.excedente = Number(ng.configPagamentos.excedente) ;
         var chaves = [
            {
                nome:'pagamentos_fornecedores_agenda',
                valor: JSON.stringify(angular.copy(ng.configPagamentos)) ,
                id_empreendimento : ng.userLogged.id_empreendimento
            }
        ];  
        }else{
            var chaves = [
            {
                nome:'pagamentos_fornecedores_agenda',
                valor: '' ,
                id_empreendimento : ng.userLogged.id_empreendimento
            }
        ];  
        }

    	$("#btn-salvar-config").button('loading');


		aj.post(baseUrlApi()+"configuracao/save",{chaves:chaves})
		.success(function(data, status, headers, config) {
			$("#btn-salvar-config").button('reset');
			ng.mensagens('alert-success','Configurações salvas com sucesso','.alert-config');
		})
		.error(function(data, status, headers, config) {
			$("#btn-salvar-config").button('reset');
			ng.mensagens('alert-danger','Ocorreu um erro ao salvar as Configurações','.alert-config');
		});
    }

    ng.removeError = function(){
    	$('div').removeClass('has-error');
    	$(".config-semana").css({background:"none"});
    }

    var eventsElements = [] ;
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay',
			},
			defaultDate: NOW('en'),
			lang: 'pt-br',
			editable: false,
			eventLimit: true, // allow "more" link when too many events,
			events: {
				url: baseUrlApi()+'agenda/fornecedor',
				type: 'GET',
      			data: {
            		id_empreendimento: ng.userLogged.id_empreendimento
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
			loading: function(bool) {
				$('#loading').toggle(bool);
			},
            dayClick: function(date, jsEvent, view){
                dia = subtraiData(date._d,0);
                var dia_semana = new Date(date._d);
                var dia_semana = dia_semana.getDay();
                var placement  = dia_semana == 5 ?  'left' : 'top' ; 
                $(this).popover({
                    title: 'Detalhes do dia',
                    placement: placement,
                    content: '<strong>loading ... </strong>',
                    html: true,
                    container: 'body',
                    trigger  :'focus',
                }).popover('show');
                var elemento = this ;
                $( "body" ).on( "mouseover", this , function() {
                    $(elemento).popover('hide');
                });

                aj.get(baseUrlApi()+"agenda/detalhes/dia/"+ng.userLogged.id_empreendimento+"/"+dia)
                    .success(function(data, status, headers, config) {
                        var tr = "";var desc = "";var vlr = ""; var a_receber = 0 ; var recebido = 0;var valores_agenda="";
                        valores_agenda = '</table></div>'
                                +'<div class="col-sm-6">'
                                +'<table id="data" class="table table-bordered table-hover table-striped table-condensed">'
                                +'<thead>'
                                +'<tr>'
                                +'<th colspan="2" class="text-center">Valores Agenda</th>'
                                +'</tr>'
                                +'</thead>'
                                +'<tbody>';
                        if(data.valores_agenda != null){
                             valores_agenda+="<tr>"
                                +"<td  class='text-left' style='width:70px'>Permitido</td>"
                                +"<td  class='text-right' >R$ "+numberFormat((data.valores_agenda.vlr_permitido_dia),2,',','.')+"</td>"
                                +"<tr>"

                                +"<tr>"
                                +"<td  class='text-left' style='width:70px'>Pago</td>"
                                +"<td  class='text-right' >R$ "+numberFormat((data.valores_agenda.pagamentos_realizados),2,',','.')+"</td>"
                                +"<tr>"

                                +"<tr class='warning'>"
                                +"<td  class='text-left' style='width:70px'>disponivel</td>"
                                +"<td  class='text-right' >R$ "+numberFormat((data.valores_agenda.disponivel),2,',','.')+"</td>"
                                +"<tr>"

                                +'</tbody>'
                                +'</table></div>';
                        }else{
                            valores_agenda+="<tr>"
                                +"<td colspan='2' class='text-right' >Agenda não configurada</td>"
                                +"<tr>" 
                                +'</tbody>'
                                +'</table></div>';
                        }

                               

                        $.each(data.faturamento,function(i,item){
                            if(item.tipo == 'a_receber'){
                                desc = "A receber";
                                vlr  = numberFormat(item.total,2,',','.');
                                a_receber = item.total ;
                            }else if(item.tipo == 'a_pagar'){
                                desc = "A pagar";
                                vlr  = numberFormat(item.total,2,',','.');
                            }else if(item.tipo == 'recebido'){
                                desc     = "Recebido";
                                vlr      = numberFormat(item.total,2,',','.');
                                recebido = item.total ;
                            }else if(item.tipo == 'pago'){
                                desc = "Pago";
                                vlr  = numberFormat(item.total,2,',','.');
                            }
                            tr += "<tr>"
                                    +"<td  class='text-left' style='width:70px'>"+desc+"</td>"
                                    +"<td  class='text-right' >R$ "+vlr+"</td>"
                                 +"<tr>";
                            if(item.tipo == 'a_pagar'){

                                tr += "<tr class='warning'>"
                                    +"<td  class='text-left' style='width:70px'>total</td>"
                                    +"<td  class='text-right' >R$ "+numberFormat((a_receber - item.total),2,',','.')+"</td>"
                                 +"<tr>";
                            }

                             if(item.tipo == 'pago'){

                                tr += "<tr class='warning'>"
                                    +"<td  class='text-left' style='width:70px'>total</td>"
                                    +"<td  class='text-right' >R$ "+numberFormat((recebido - item.total),2,',','.')+"</td>"
                                 +"<tr>";
                            }

                        });
                        var template = '<div class="row"><div class="col-sm-6">'
                                +'<table id="data" class="table table-bordered table-hover table-striped table-condensed">'
                                +'<thead>'
                                +'<tr>'
                                +'<th colspan="2" class="text-center">Faturamento</th>'
                                +'</tr>'
                                +'</thead>'
                                +'<tbody>'
                                +tr
                                +'</tbody>'
                                +valores_agenda
                                +'</div>';
                        //$('.popover .popover-content').html(template)
                        $(elemento).popover('destroy').popover({
                            title: 'Detalhes do dia <strong>'+formatDateBR(dia)+"<strong>",
                            placement: placement,
                            content: template,
                            html: true,
                            container: 'body',
                            trigger  :'focus',
                        }).popover('show');

                })
                    .error(function(data, status, headers, config) {
                      
                });
            },
			viewRender:function( view, element ){
				console.log(view);
			}
		});

    $( "body" ).on( "click", ".fc-day-number", function() {
        var date = $(this).attr('data-date');
        //console.log( date );
    });

	ng.modalConfig = function(){
		ng.loafConfig();
		$('#modal-config').modal('show');
	}

    ng.modalSimulador = function(){
        $('#modal-simulador').modal('show');
         ng.simulador = {
            valor_pedido              : null,
            qtd_parcelas              : null,
            intervalo                 : null,
            dta_faturamento           : null,
            dias_primeira_parcela     : null,
            dias_ultima_parcela       : null,
            dta_primeira_parcela      : null,
            dta_limite_ultima_parcela : null,
            pagamento_parcelado       : [] 
        }
        ng.removeError();
    }

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.loafConfig();

});
