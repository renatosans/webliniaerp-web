app.controller('PlanejamentoPlanoContasController', function($scope, $http, $window, $dialogs, UserService, PrestaShop){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.fabricante 	= {};
    ng.fabricantes	= [];
    ng.paginacao    = {fabricantes : [] } ;
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

	ng.meses = [] ;
	ng.gerarTabela = function(){
		var dtaInicial = $('#dtaInicial').val();
		var dtaFinal   = $('#dtaFinal').val();

		var meses = [] ;

		for (
			var i = dtaInicial ;
			moment(i, "MM/YYYY").diff(moment(dtaFinal, "MM/YYYY"), 'months') <= 0;
			i = moment(i,'MM/YYYYY').add(1, 'month').format('MM/YYYY') 
		) {
			meses.push(i);
		}

		
		ng.meses = meses ;

		$.each(ng.plano_contas,function(i,v){
			var meses = [] ;
			$.each(ng.meses,function(x,y){
				var mes = {} ;
				mes = {
					mes : y,
					valor : 0
				}
				meses.push(mes);
			});	
			ng.plano_contas[i].meses = angular.copy(meses) ;
		});
	}

	ng.loadPlanoContas = function() {
		ng.plano_contas = [] ;
		aj.get(baseUrlApi()+"planocontas?tpc->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				var item = {} ;
				$.each(data, function(i, v){
					var item = {} ;
					item.id_plano_conta = Number(v.id);
					item.dsc_completa = v.dsc_completa;
					ng.plano_contas.push(item);
				});
				console.log(ng.plano_contas);
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.roleList = [];
			});
	}

	ng.salvar = function(){
		console.log(ng.plano_contas);
	}

	ng.loadPlanoContas();
	
});