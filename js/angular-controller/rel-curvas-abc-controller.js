app.controller('RelatorioCurvasABC', function($scope, $http, $window, UserService, ConfigService, FuncionalidadeService, EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.config     = ConfigService.getConfig(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.vendas 		   	= null;
	ng.paginacao 	   	= {};
	ng.busca			= {}
	ng.busca.vendedores  = '';
	ng.busca.arrFabricantes  = [] ;
	ng.busca.flg_campo_ordenacao = 'qtd';

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.funcioalidadeAuthorized = function(cod_funcionalidade){
		return FuncionalidadeService.Authorized(cod_funcionalidade,ng.userLogged.id_perfil,ng.userLogged.id_empreendimento);
	}

	ng.showProdutos = function(){
   		ng.busca.produtos = "" ;
   		ng.loadProdutos(0,10);
   		$('#list_produtos').modal('show');
   	}

	ng.loadProdutos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		var query_string = "?group=&emp->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.produtos != ""){
			query_string += "&("+$.param({'prd->nome':{exp:"like'%"+ng.busca.produtos+"%' OR fab.nome_fabricante like'%"+ng.busca.produtos+"%'"}})+")";
		}

		ng.produtos = [];
		aj.get(baseUrlApi()+"estoque/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.produtos        = data.produtos ;
				ng.paginacao.produtos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.produtos = [];
			});
	}

	ng.addProduto = function(item){
		ng.produto = item ;
		$('#list_produtos').modal('hide');
	}

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.vendedor = null;
		 ng.produto = null;
		 ng.data = null;
		 ng.paginacao.data = null;
		 ng.busca.id_fabricante   = null;
		 ng.busca.nome_fabricante = null;
		 ng.msg_error = null;
		 ng.busca.arrFabricantes = [];
	}

	ng.resetFilter = function() {
		ng.reset();
	}

	ng.aplicarFiltro = function() {
		if((ng.busca.arrFabricantes.length > 0) && (ng.busca.arrFabricantes.length < 3)){
			alert("Você deve informar ao menos 3 fabricantes");
		}
		else {
			ng.getData(0,ng.itensPorPagina);
			ng.msg_error = null;
		}
	}


	ng.getData = function(offset,limit) {
		ng.busca.dtaInicial = $("#dtaInicial").val();
		ng.busca.dtaFinal = $("#dtaFinal").val();

		var queryString = "?id_empreendimento="+ ng.userLogged.id_empreendimento;

		if(!empty(ng.busca.dtaInicial))
			queryString += "&dtaInicial="+ moment(ng.busca.dtaInicial, "DD/MM/YYYY").format('YYYY-MM-DD');

		if(!empty(ng.busca.dtaFinal))
			queryString += "&dtaFinal="+ moment(ng.busca.dtaFinal, "DD/MM/YYYY").format('YYYY-MM-DD');

		if(ng.busca.arrFabricantes.length > 0){
			var in_id_fab = [];
			$.each(ng.busca.arrFabricantes,function(i,x){
				in_id_fab.push(x.id_fabricante) ;
			});
			queryString += "&"+$.param({'id_fabricante':in_id_fab});
		}

		if(!empty(ng.busca.flg_campo_ordenacao)) {
			queryString += "&flg_campo_ordenacao="+ ng.busca.flg_campo_ordenacao;
		}

		$("#modal-aguarde").modal('show');

		ng.data = [];
		
		ng.faixas_curva_abc = JSON.parse(ng.config.faixas_curva_abc);

		aj.get(baseUrlApi()+"relatorio/vendas/consolidado/fabricante/"+offset+'/'+limit+queryString)
			.success(function(data, status, headers, config) {
				ng.data = data.data;
				ng.paginacao.vendas = data.paginacao ;
				$("#modal-aguarde").modal('hide');

				ng.qtd_linhas = ng.data.length;
				ng.vrl_soma_valores = 0;

				angular.forEach(ng.data, function(item){
					if(ng.busca.flg_campo_ordenacao == 'qtd'){
						ng.vrl_soma_valores += item.qtd_vendida;
					}
					else if(ng.busca.flg_campo_ordenacao == 'vlr'){
						ng.vrl_soma_valores += item.vlr_vendido;
					}
				});
				
				var index_faixa_a = _.findIndex(ng.faixas_curva_abc, {faixa: 'curva_a'});
				var index_faixa_b = _.findIndex(ng.faixas_curva_abc, {faixa: 'curva_b'});
				var index_faixa_c = _.findIndex(ng.faixas_curva_abc, {faixa: 'curva_c'});

				ng.faixas_curva_abc[index_faixa_a].label = 'A';
				ng.faixas_curva_abc[index_faixa_a].color = 'success';
				
				ng.faixas_curva_abc[index_faixa_b].label = 'B';
				ng.faixas_curva_abc[index_faixa_b].color = 'warning';
				
				ng.faixas_curva_abc[index_faixa_c].label = 'C';
				ng.faixas_curva_abc[index_faixa_c].color = 'danger';

				ng.faixas_curva_abc[index_faixa_c].qtd_linhas = Math.ceil((ng.qtd_linhas * ng.faixas_curva_abc[index_faixa_c].valor) / 100);
				ng.faixas_curva_abc[index_faixa_b].qtd_linhas = Math.ceil((ng.qtd_linhas * ng.faixas_curva_abc[index_faixa_b].valor) / 100);
				ng.faixas_curva_abc[index_faixa_a].qtd_linhas = ng.qtd_linhas - (ng.faixas_curva_abc[index_faixa_c].qtd_linhas + ng.faixas_curva_abc[index_faixa_b].qtd_linhas	);

				var idx_inicio_faixa_a = 0;
					idx_fim_faixa_a = ((idx_inicio_faixa_a + ng.faixas_curva_abc[index_faixa_a].qtd_linhas) - 1);
				
				var idx_inicio_faixa_b = (idx_fim_faixa_a + 1);
					idx_fim_faixa_b = ((idx_inicio_faixa_b + ng.faixas_curva_abc[index_faixa_b].qtd_linhas) - 1);
				
				var idx_inicio_faixa_c = (idx_fim_faixa_b + 1);
					idx_fim_faixa_c = ((idx_inicio_faixa_c + ng.faixas_curva_abc[index_faixa_c].qtd_linhas) - 1);

				// somando valores de cada faixa
				for (var i=idx_inicio_faixa_a;i<=idx_fim_faixa_a;i++) {
					if(empty(ng.faixas_curva_abc[index_faixa_a].vlr_soma))
						ng.faixas_curva_abc[index_faixa_a].vlr_soma = 0;

					ng.data[i].color = 'success';

					if(ng.busca.flg_campo_ordenacao == 'qtd'){
						ng.faixas_curva_abc[index_faixa_a].vlr_soma += ng.data[i].qtd_vendida;
					}
					else if(ng.busca.flg_campo_ordenacao == 'vlr'){
						ng.faixas_curva_abc[index_faixa_a].vlr_soma += ng.data[i].vlr_vendido;
					}
				}

				for (var i=idx_inicio_faixa_b;i<=idx_fim_faixa_b;i++) {
					if(empty(ng.faixas_curva_abc[index_faixa_b].vlr_soma))
						ng.faixas_curva_abc[index_faixa_b].vlr_soma = 0;

					ng.data[i].color = 'warning';

					if(ng.busca.flg_campo_ordenacao == 'qtd'){
						ng.faixas_curva_abc[index_faixa_b].vlr_soma += ng.data[i].qtd_vendida;
					}
					else if(ng.busca.flg_campo_ordenacao == 'vlr'){
						ng.faixas_curva_abc[index_faixa_b].vlr_soma += ng.data[i].vlr_vendido;
					}
				}

				for (var i=idx_inicio_faixa_c;i<=idx_fim_faixa_c;i++) {
					if(empty(ng.faixas_curva_abc[index_faixa_c].vlr_soma))
						ng.faixas_curva_abc[index_faixa_c].vlr_soma = 0;

					ng.data[i].color = 'danger';

					if(ng.busca.flg_campo_ordenacao == 'qtd'){
						ng.faixas_curva_abc[index_faixa_c].vlr_soma += ng.data[i].qtd_vendida;
					}
					else if(ng.busca.flg_campo_ordenacao == 'vlr'){
						ng.faixas_curva_abc[index_faixa_c].vlr_soma += ng.data[i].vlr_vendido;
					}
				}

				angular.forEach(ng.faixas_curva_abc, function(faixa) {
					faixa.prc_faixa = ((faixa.vlr_soma / ng.vrl_soma_valores) * 100);
				});

				console.log(ng.faixas_curva_abc);

				Highcharts.setOptions({
					lang: {
						decimalPoint: ',',
						thousandsSep: '.'
					}
				});

				var pointFormat = '';

				if(ng.busca.flg_campo_ordenacao == 'qtd')
					pointFormat = '<tr><td style="padding:0;text-align:right;"><b>{point.y:,.0f}</b></td></tr>';
				else
					pointFormat = '<tr><td style="padding:0;text-align:right;"><b>R$ {point.y:,.2f}</b></td></tr>';

			 	var myChart = Highcharts.chart('container', {
			 		colors: ['#A4D092', '#FFF18F', '#E27373'],
				 	chart: {
				 		type: 'column'
				 	},
				 	title: {
				 		text: 'Curva ABC'
				 	},
				 	xAxis: {
				 		categories: [((ng.busca.flg_campo_ordenacao == 'qtd') ? 'Quantidade' : 'Valor')]
				 	},
				 	yAxis: {
				 		title: {
				 			text: (ng.busca.flg_campo_ordenacao == 'qtd') ? 'Qtde.' : 'R$'
				 		}
				 	},
				 	series: [
				 		{
				 			name: 'A',
				 			data: [
				 				ng.faixas_curva_abc[index_faixa_a].vlr_soma
				 			],
				 			tooltip: {
					 			headerFormat: '<span style="font-size:10px">{point.key}</span><br/><table>',
					 			pointFormat: pointFormat,
					 			footerFormat: '</table>',
					 			shared: true,
					 			useHTML: true
					 		}
				 		},
				 		{
				 			name: 'B',
				 			data: [
				 				ng.faixas_curva_abc[index_faixa_b].vlr_soma
				 			],
				 			tooltip: {
					 			headerFormat: '<span style="font-size:10px">{point.key}</span><br/><table>',
					 			pointFormat: pointFormat,
					 			footerFormat: '</table>',
					 			shared: true,
					 			useHTML: true
					 		}
				 		},
				 		{
				 			name: 'C',
				 			data: [
				 				ng.faixas_curva_abc[index_faixa_c].vlr_soma
				 			],
				 			tooltip: {
					 			headerFormat: '<span style="font-size:10px">{point.key}</span><br/><table>',
					 			pointFormat: pointFormat,
					 			footerFormat: '</table>',
					 			shared: true,
					 			useHTML: true
					 		}
				 		}
				 	]
				});
			})
			.error(function(data, status, headers, config) {
				$("#modal-aguarde").modal('hide');
				ng.data = null;
				ng.status = status;
				ng.msg_error = data;
				ng.paginacao.data = null;
			});
	}

	ng.modalFabricantes = function(){
		if(ng.openModalFabricantes){
   			$('#modal-fabricantes').modal('show');
			ng.loadFabricantes(0,10);
		}
		else
   			ng.openModalFabricantes = true ;
	}


	ng.busca_vazia = {} ;
	ng.loadFabricantes = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 10 : limit;
		ng.busca_vazia.fabricantes = false ;
		var query_string = "?tfe->id_empreendimento="+ng.userLogged.id_empreendimento ;
		if(!empty(ng.busca.fabricantes))
			query_string  += "&"+$.param({nome_fabricante:{exp:"like '%"+ng.busca.fabricantes+"%'"}});

    	aj.get(baseUrlApi()+"fabricantes/"+offset+"/"+limit+query_string)
		.success(function(data, status, headers, config) {
			ng.fabricantes = data.fabricantes ;	
			ng.paginacao.fabricantes = data.paginacao ;
		})
		.error(function(data, status, headers, config) {
			if(status != 404)
				alert("ocorreu um erro");
			else{
				ng.paginacao.fabricantes = [] ;
				ng.fabricantes = [] ;	
				ng.busca_vazia.fabricantes = true ;
			}
				
		});
	}

	ng.addFabricante = function(item){
		ng.busca.id_fabricante   = item.id;
		ng.busca.nome_fabricante = item.nome_fabricante;
    	$('#modal-fabricantes').modal('hide');
	}

	ng.selectFabricante = function(item){
		ng.busca.arrFabricantes.push({ id_fabricante : item.id, text: item.nome_fabricante});
		//$('#modal-fabricantes').modal('hide');
	}

	ng.fabricanteIsSelected = function(id_fabricante){
   		if(typeof ng.busca.arrFabricantes != 'object')
   			return ;
   		var r = false ;
   		$.each(ng.busca.arrFabricantes,function(i,x){
   			if(Number(x.id_fabricante) == Number(id_fabricante)){
   				r = true ;
   				return ;
   			}
   		});
   		return r ;
   	}

   	ng.openModalFabricantes = true ;
	$scope.$watch('busca.arrFabricantes', function(newValue, oldValue) {
	 	if(oldValue.length > newValue.length ){
	 		ng.openModalFabricantes = false ;
	 		setTimeout(function(){
				ng.openModalFabricantes = true ;
			},100);
	 	}
     }, true);

	ng.getData(0,10);
	ng.reset();
});
