app.controller('RelatorioAniversariantes', function($scope, $http, $window, UserService,FuncionalidadeService, EmpreendimentoService) {
	var ng 				= $scope,
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.vendas 		   	= null;
	ng.paginacao 	   	= {};
	ng.busca			= {};
	ng.busca_modal		= {};

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
	
	ng.aniversariantes_mes = 0;
	ng.loadAniversariantesMes = function(){
		aj.get(baseUrlApi()+"cliente/aniversariantes?tue->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config){
				ng.aniversariantes_mes = data;		
			})
			.error(function(data, status, headers, config){
				ng.aniversariantes_mes = 0 ;
			})
	}

	ng.loadAniversariantesMes();
});
