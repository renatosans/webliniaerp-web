app.controller('DashboardIntegradoController', function($scope, $http, $window, UserService) {
	$scope.userLogged = UserService.getUserLogado();
	$scope.empreendimentos = UserService.getMeusEmpreendimentos();

	angular.forEach($scope.empreendimentos, function(empreendimento){
			empreendimento.icon 	= '<img src="assets/imagens/logos/'+ empreendimento.nme_logo +'" />';
			empreendimento.name 	= empreendimento.nome_empreendimento;
			empreendimento.ticked 	= false;
	});

	$scope.localLang = {
		selectAll       : "Marcar todos",
		selectNone      : "Desmarcar todos",
		reset           : "Recarregar todos",
		search          : "Pesquisar...",
		nothingSelected : "Nenhum empreendimento selecionado"
	};

	var comma_separator_number_step = $.animateNumber.numberStepFactories.separator('.');

	function showLoadingOverlay(elSeletor) {
		var _overlayDiv = $(elSeletor).parent().children('.loading-overlay');
			_overlayDiv.addClass('active');
	}

	function hideLoadingOverlay(elSeletor) {
		var _overlayDiv = $(elSeletor).parent().children('.loading-overlay');
			_overlayDiv.removeClass('active');
	}

	$scope.getTotalReceberHoje = function(ids_empreendimento) {
		showLoadingOverlay('.vlr_receber_hoje .refresh-button');
		$scope.vlr_receber_hoje = 0;
		$http.get(baseUrlApi()+"dashboard/integrado/total/receber/hoje/"+ ids_empreendimento)
			.success(function(data, status, headers, config) {
				$scope.vlr_receber_hoje = data[0].vlr_receber_hoje;
				$('.vlr_receber_hoje .value').animateNumber(
					{
						number: $scope.vlr_receber_hoje,
						numberStep: comma_separator_number_step
					}
				);
				hideLoadingOverlay('.vlr_receber_hoje .refresh-button');
			});
	}

	$scope.getTotalPagarHoje = function(ids_empreendimento) {
		showLoadingOverlay('.vlr_pagar_hoje .refresh-button');
		$scope.vlr_pagar_hoje = 0;
		$http.get(baseUrlApi()+"dashboard/integrado/total/pagar/hoje/"+ ids_empreendimento)
			.success(function(data, status, headers, config) {
				$scope.vlr_pagar_hoje = data[0].vlr_pagar_hoje;
				$('.vlr_pagar_hoje .value').animateNumber(
					{
						number: $scope.vlr_pagar_hoje,
						numberStep: comma_separator_number_step
					}
				);
				hideLoadingOverlay('.vlr_pagar_hoje .refresh-button');
			});
	}

	$scope.getTotalReceberAtrasado = function(ids_empreendimento) {
		showLoadingOverlay('.vlr_receber_atrasado .refresh-button');
		$scope.vlr_receber_atrasado = 0;
		$http.get(baseUrlApi()+"dashboard/integrado/total/receber/atrasado/"+ ids_empreendimento)
			.success(function(data, status, headers, config) {
				$scope.vlr_receber_atrasado = data[0].vlr_receber_atrasado;
				$('.vlr_receber_atrasado .value').animateNumber(
					{
						number: $scope.vlr_receber_atrasado,
						numberStep: comma_separator_number_step
					}
				);
				hideLoadingOverlay('.vlr_receber_atrasado .refresh-button');
			});
	}

	$scope.getTotalPagarAtrasado = function(ids_empreendimento) {
		showLoadingOverlay('.vlr_pagar_atrasado .refresh-button');
		$scope.vlr_pagar_atrasado = 0;
		$http.get(baseUrlApi()+"dashboard/integrado/total/pagar/atrasado/"+ ids_empreendimento)
			.success(function(data, status, headers, config) {
				$scope.vlr_pagar_atrasado = data[0].vlr_pagar_atrasado;
				$('.vlr_pagar_atrasado .value').animateNumber(
					{
						number: $scope.vlr_pagar_atrasado,
						numberStep: comma_separator_number_step
					}
				);
				hideLoadingOverlay('.vlr_pagar_atrasado .refresh-button');
			});
	}

	$scope.getFaturamentoMensal = function(ids_empreendimento) {
		$scope.vlr_total_faturamento = 0;
		$scope.arr_faturamento_mensal = [];
		$http.get(baseUrlApi()+"dashboard/integrado/faturamento/mensal/"+ ids_empreendimento)
			.success(function(data, status, headers, config) {
				angular.forEach(data.meses, function(mes) {
					$scope.vlr_total_faturamento += mes.vlr_faturamento;
					$scope.arr_faturamento_mensal.push(mes.vlr_faturamento);
				});

				$('#faturamentoNumber').animateNumber(
					{
						number: $scope.vlr_total_faturamento,
						numberStep: comma_separator_number_step
					}
				);

				$('#faturamentoChart').sparkline($scope.arr_faturamento_mensal, {
					type: 'bar', 
					barColor: '#3C8DBC',	
					height:'35px',
					weight:'96px'
				});
			});
	}

	$scope.getDespesasMensal = function(ids_empreendimento) {
		$scope.vlr_total_despesas = 0;
		$scope.arr_despesas_mensal = [];
		$http.get(baseUrlApi()+"dashboard/integrado/despesas/mensal/"+ ids_empreendimento)
			.success(function(data, status, headers, config) {
				angular.forEach(data.meses, function(mes) {
					$scope.vlr_total_despesas += mes.vlr_despesa;
					$scope.arr_despesas_mensal.push(mes.vlr_despesa);
				});

				$('#despesasNumber').animateNumber(
					{
						number: $scope.vlr_total_despesas,
						numberStep: comma_separator_number_step
					}
				);

				$('#despesasChart').sparkline($scope.arr_despesas_mensal, {
					type: 'bar', 
					barColor: '#FC8675',	
					height:'35px',
					weight:'96px'
				});
			});
	}

	$scope.resetFilter = function() {
		$scope.vlr_receber_hoje = 0;
		$scope.vlr_pagar_hoje = 0;
		$scope.vlr_receber_atrasado = 0;
		$scope.vlr_pagar_atrasado = 0;
		$scope.vlr_total_faturamento = 0;
		$scope.arr_faturamento_mensal = [];
		$scope.vlr_total_despesas = 0;
		$scope.arr_despesas_mensal = [];
		$('#faturamentoNumber').text('0');
		$('#despesasNumber').text('0');
		$('#faturamentoChart').text('');
		$('#despesasChart').text('');
	}

	$scope.applyFilter = function() {
		var ids_empreendimento = "";
		angular.forEach($scope.empreendimentos_selecionados, function(empSelected, index) {
			ids_empreendimento += empSelected.id;
			if(index < ($scope.empreendimentos_selecionados.length-1))
				ids_empreendimento += ',';
		});
		
		$scope.getTotalReceberHoje(ids_empreendimento);
		$scope.getTotalPagarHoje(ids_empreendimento);
		$scope.getTotalReceberAtrasado(ids_empreendimento);
		$scope.getTotalPagarAtrasado(ids_empreendimento);
		$scope.getFaturamentoMensal(ids_empreendimento);
		$scope.getDespesasMensal(ids_empreendimento);
	}

	/*
	$('#lucroChart').sparkline([220,160,189,156,201,220,104,242,221,111,164,242,183,165], {
		type: 'bar', 
		barColor: '#65CEA7',	
		height:'35px',
		weight:'96px'
	});

	var lineChart = Morris.Line({
		element: 'lineChart',
		data: [
			{ y: '2016-01', a: 30,  b: 20 },
			{ y: '2016-02', a: 45,  b: 35 },
			{ y: '2016-03', a: 60,  b: 60 },
			{ y: '2016-04', a: 75,  b: 65 },
			{ y: '2016-05', a: 50,  b: 70 },
			{ y: '2016-06', a: 80,  b: 85 },
			{ y: '2016-07', a: 100, b: 90 }
		],
		xkey: 'y',
		grid: false,
		ykeys: ['a', 'b'],
		labels: ['Receitas', 'Despesas'],
		lineColors: ['#8CB4BC', '#538792'],
		gridTextColor : '#fff'
	});

	var lucroNumber = $('#lucroNumber').text();

	$({numberValue: 0}).animate({numberValue: lucroNumber}, {
		duration: 1000,
		easing: 'linear',
		step: function() {
			$('#lucroNumber').text(Math.ceil(this.numberValue));
		}
	});
	*/

	$('#container').highcharts({
        title: {
            text: null
        },
        xAxis: {
            categories: ['01', '02', '03', '04', '05']
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Receitas',
            color: '#65cea7',
            type: 'column',
            data: [5, 3, 4, 7, 2]
        }, {
            name: 'Despesas',
            color: '#fc8675',
            type: 'column',
            data: [-2, -2, -3, -2, -1]
        }, {
            name: 'Saldo',
            color: '#6bafbd',
            type: 'spline',
            data: [3, 1, 1, 5, 1]
        }]
    });
});