app.controller('PlanejamentoPlanoContasController', function($scope, $http, $window, $dialogs, UserService, PrestaShop){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
    ng.paginacao    = {planejamentos : [] } ;
    ng.editing = false;

    ng.reset = function(){
    	ng.itemEditado = null ;
    	$('#dtaInicial').datepicker('setDate', null);
		$('#dtaFinal').datepicker('setDate', null);
		ng.meses = [] ;
    }

    ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

    ng.showBoxNovo = function(onlyShow){
    	ng.editing = !ng.editing;

    	if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			ng.editing = true ;
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
			ng.plano_contas[i].dta_inicial = dtaInicial ;
			ng.plano_contas[i].dta_final = dtaFinal ;
			var meses = [] ;

			aux = ng.deparaTabelaEdit(v.id_plano_conta);

			if(!aux){
				$.each(ng.meses,function(x,y){
					var mes = {} ;
					mes = {
						mes : y,
						valor : 0
					}
					meses.push(mes);
				});		
			}else{
				meses = angular.copy(aux);	
			}

			
			ng.plano_contas[i].meses = angular.copy(meses) ;
		});
	}

	ng.deparaTabelaEdit = function(id_plano_conta){
		if(!ng.itemEditado) return false ;
		var r = false 
		$.each(ng.itemEditado.meses,function(x,y){
			if(angular.isArray(y) && id_plano_conta == y[0].id_plano_conta){
				r = y ;
				return  ;
			}
		});
		return r ;
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


	ng.planejamento = null ;

	ng.load = function(offset, limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;

		var query_string = "" ;

		aj.get(baseUrlApi()+"planejamento_plano_contas/"+ng.userLogged.id_empreendimento+"/"+ offset +"/"+ limit + query_string)
			.success(function(data, status, headers, config) {
				ng.planejamentos = data.planejamentos;
				ng.paginacao.planejamentos = data.paginacao ;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.planejamentos = [];
			});
	}

	ng.salvar = function() {
		var btn = $('#btn-salvar');
   		btn.button('loading');
		var url = 'planejamento_plano_contas';
		
		var dtaInicial = $('#dtaInicial').val();
		var dtaFinal   = $('#dtaFinal').val();

		var post = {
			id 					: (empty(ng.itemEditado) ? null : ng.itemEditado.id),
			id_empreendimento 	: ng.userLogged.id_empreendimento,
			dta_inicial 		: dtaInicial,
			dta_final 			: dtaFinal,
			plano_contas  		: angular.copy(ng.plano_contas)
		} ;

		if(ng.itemEditado){
			post.id = ng.itemEditado.id ;
			var url = 'planejamento_plano_contas/update';
		}


		aj.post(baseUrlApi()+url, {json : JSON.stringify(post)})
			.success(function(data, status, headers, config) {
				ng.itemEditado = null ;
				ng.reset();
				ng.showBoxNovo();
				ng.mensagens('alert-success','<strong>Planejamento salvo com sucesso!</strong>');
				btn.button('reset');
				ng.load();
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406) {
					$('.alert-periodo').show();
					$('html,body').animate({scrollTop: 0},'slow');
					setTimeout(function(){
						$('.alert-periodo').fadeOut('slow');
					},7000);
				}
			});


	}

	ng.itemEditado = null  ; 
	ng.editar = function(item){
		ng.itemEditado = angular.copy(item);
		$('html,body').animate({scrollTop: 0},'slow');
		$('#dtaInicial').datepicker('update', item.dta_inicial);
		$('#dtaFinal').datepicker('update', item.dta_final);
		ng.showBoxNovo(true);
		ng.gerarTabela();
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este planejamento?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"planejamento_plano_contas/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Planejamento excluido com sucesso</strong>');
					ng.reset();
					ng.load();
				})
		}, undefined);
	}


	ng.load();
	ng.loadPlanoContas();
	
});