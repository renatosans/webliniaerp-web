app.controller('RelatorioContasPagar', function($scope, $http, $window, UserService) {
	var ng 				= $scope,
		aj			    = $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.busca      		= {clientes:''};
	ng.cliente    		= {};
	ng.tipoBusca        = 'periodo' ;
	ng.extrato          = [] ;
	ng.dadosExtrato     = null ;
	ng.currentDate      = new Date() ;


	var params = getUrlVars();

	ng.getExtrato = function(){
		ng.dadosExtrato = [];
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass('has-error');
		var url = baseUrlApi()+"usuarios/extrato/"+ng.userLogged.id_empreendimento+"/"+ng.cliente.id+"?";
		var query_string = "";
		var error_periodo = 0 ;
		
		if(ng.tipoBusca == "periodo"){
			if(empty(ng.cliente.id)){
				ng.addError("#form_cliente","A escolha do cliente é obrigatória");
				error_periodo ++ ;
			}

			var dataInicial = ng.busca.dataInicial;
			var dataFinal   = ng.busca.dataFinal ;
			if(empty(dataInicial)){
				ng.addError('#form_dta_inicial',"A data inicial é obrigatória");
				if(empty(dataFinal)){
					ng.addError('#form_dta_final',"A data final é obrigatória");
				}
				error_periodo ++ ;
			}else if(empty(dataFinal)){
				ng.addError('#form_dta_final',"A data inicial é obrigatória");
				error_periodo ++ ;
			}else if(dataInicial > dataFinal){
				ng.addError('#form_dta_inicial',"A data inicial não pode ser maior que a data final");
				error_periodo ++ ;
			}

			if(error_periodo > 0)
				return;
			query_string += $.param(
										{
											sql:{
													literal_exp:"dta_entrada between '"+dataInicial+"'  AND '"+dataFinal+" 23:59:59' ORDER BY dta_entrada ASC,id ASC"
												},
											busca_saldo_anterior:{
												dta_entrada:{
													literal_exp: "dta_entrada < '"+dataInicial+"'"
												},
												dta_venda:{
													literal_exp: "dta_venda < '"+dataInicial+"'"
												}
											}
										}
								   );
			
		}else if(ng.tipoBusca == "intervalo"){
			var intervalo = ng.busca.intervalo;
			var error_interval = 0 ;
			if(empty(ng.cliente.id)){
				ng.addError("#form_cliente_2","A escolha do cliente é obrigatória");
				error_interval ++ ;
			}
			if(isNaN(ng.busca.intervalo)){
				ng.addError('#form_busca_interval',"Preencha a quantidade de dias desejado para a busca");
				error_interval ++ ;
			}

			if(error_interval > 0)
				return ;

			query_string += $.param(
											{
												sql:{
														literal_exp:"dta_entrada > DATE_ADD(NOW(),INTERVAL -"+intervalo+" day) ORDER BY dta_entrada ASC,id ASC"
													},
												busca_saldo_anterior:{
													dta_entrada:{
														literal_exp: "dta_entrada < DATE_ADD(NOW(),INTERVAL -"+intervalo+" day)"
													},
													dta_venda:{
														literal_exp: "dta_venda < DATE_ADD(NOW(),INTERVAL -"+intervalo+" day)"
													}
												}
											}
									   );
		}
		$('#modal-aguarde').modal({
				  backdrop: 'static',
				  keyboard: false
				});

		aj.get(url+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.extrato,function(i,item){
					if(item.tipo == "pagamento" && item.id_forma_pagamento == 6){
						data.extrato[i].template_popover = ng.templatePopover(item.dta_parcelas);
					}
				});
				console.log('----------------------');
				console.log(data);
				ng.dadosExtrato = data;
				ng.dadosBusca = {
						cliente   	: ng.cliente,
						tipoBusca 	: ng.tipoBusca,
						intervalo 	: ng.busca.intervalo,
						dataInicial : ng.busca.dataInicial,
						dataFinal   : ng.busca.dataFinal
				}
				$('#modal-aguarde').modal("hide");
				ctr_popover = true;
			})
			.error(function(data, status, headers, config) {
				alert("Ocorreu um erro ao fazer a busca, tente novamente mais tarde");
				$('#modal-aguarde').modal("hide");
			});
	}

	ng.templatePopover = function(data){
		var tr = "" ;
		$.each(data,function(i,item){
				tr += "<tr>"
					 	+"<td  class='text-right'>"+(i+1)+"º</td>"
					 	+"<td  class='text-right' >"+formatDateBR(item)+"</td>"
					 +"<tr>";

		});
		var template = '<table id="data" class="table table-bordered table-hover table-striped table-condensed">'
					+'<thead>'
					+'<tr>'
					+'<th>Parcela</th>'
					+'<th>Data do Pagamento</th>'
					+'</tr>'
					+'</thead>'
					+'<tbody>'
					+tr
					+'</tbody>'
					+'</table>';
		console.log(template);
		return template ;
	}

	ng.selCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;
		ng.loadCliente(offset,limit);
		$("#list_clientes").modal("show");
	}

	ng.loadCliente= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+")";

		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.clientes+"%' OR tpj.nome_fantasia LIKE '%"+ng.busca.clientes+"%' OR tpj.nome_fantasia LIKE '%"+ng.busca.clientes+"%' OR usu.apelido LIKE '%"+ng.busca.clientes+"%')"}});
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

			});
	}

	ng.addCliente = function(item){
		ng.cliente = item;
		$("#list_clientes").modal("hide");
		aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ ng.userLogged.id_empreendimento+"?usu->id="+item.id)
			.success(function(data, status, headers, config) {
				ng.cliente.vlr_saldo_devedor = data.vlr_saldo_devedor;
			})
			.error(function(data, status, headers, config) {
				console.log('erro ao consultar saldo do cliente');
			});
	}

	ng.setBusca = function(tipo){
		ng.tipoBusca = tipo ;
		ng.cliente = {};
		if(tipo == "intervalo"){
			$("#dtaInicial").val("");
			$("#dtaFinal").val("");
		}else if (tipo == "periodo"){
			ng.busca.intervalo = "" ;
		}
	}

	var ctr_popover = false ;

	ng.popover = function(){
		if(ctr_popover){
			console.log('teste');
			$("a[rel=popover]").popover({
	            placement: 'top',
	            html: 'true'
        	});
        }
        ctr_popover = false;
	}

	ng.addError = function(el,msg){
		$(el).addClass("has-error");
					var formControl = $(el)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", msg)
						.attr("data-original-title", msg);
					formControl.tooltip();
	}

});

