app.controller('RomaneioConferenciaController', function($scope, $http, $window, UserService) {
	var ng = $scope,
		aj = $http;

	ng.userLogged = UserService.getUserLogado();
	ng.itens = [
		{
			nme_deposito: "CDP CASA BRANCA",
			nme_produto: "PRODUTO FDSF",
			dta_validade: "01/2015",
			qtd_item: 134,
			produtos: []
		},
		{
			nme_deposito: "CDP CASA BRANCA",
			nme_produto: "PRODUTO FDSF",
			dta_validade: "02/2015",
			qtd_item: 100,
			produtos: []
		},
		{
			nme_deposito: "CDP JAIME RODRIGUES",
			nme_produto: "PRODUTO FDSF",
			dta_validade: "01/2015",
			qtd_item: 34,
			produtos: []
		},
		{
			nme_deposito: "CDP JAIME RODRIGUES",
			nme_produto: "PRODUTO ASFDSAFSD",
			dta_validade: "01/2015",
			qtd_item: 324,
			produtos: []
		}
	];

	ng.reset = function() {
		ng.itens = [];
		$(".has-error").removeClass("has-error");
	}

	ng.resetFilter = function() {
		ng.reset();
	}

	_.groupByMulti = function (obj, values, context) {
	    if (!values.length)
	        return obj;
	    var byFirst = _.groupBy(obj, values[0], context),
	        rest = values.slice(1);
	    for (var prop in byFirst) {
	        byFirst[prop] = _.groupByMulti(byFirst[prop], rest, context);
	    }
	    return byFirst;
	};

	ng.depositos = _.groupByMulti(ng.itens, ['nme_deposito', 'nme_produto']);

	console.log(ng.depositos);
});
