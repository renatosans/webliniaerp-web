app.controller('RelatorioDiarioClinicaController', function($scope, $http, $window, $dialogs, UserService,ConfigService){
	var ng = $scope,
		aj = $http;

	ng.userLogged 	 		= UserService.getUserLogado();

    ng.gerarRelatorio = function(){
    	var dta = moment($('#dta-rel-diario').val(),'DD/MM/YYYY');
    	if(!dta.isValid())
    		return
    	var now = dta.format('YYYY-MM-DD');
    	$('#loading-relatorio').modal('show');
		$('#iframe-rel-diario').html('<iframe id="iframe-rel-diario"  src="'+baseUrlApi()+'relPDF?template=relatorio_clinica_diario&dados[id_empreendimento]='+ng.userLogged.id_empreendimento+'&dados[now]='+now+'" frameborder=0 allowTransparency="true"  style=" width: 100%;height: 900px;background: #fff;border: none;overflow: hidden; display:none"></iframe>')
		$('#iframe-rel-diario iframe').load(function(){
	        $(this).show();
	       	$('#loading-relatorio').modal('hide');
	    });
    }

     ng.gerarRelatorio();
});