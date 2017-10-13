app.controller('FaixaDescontoController', function($scope, $http, $window, $dialogs, UserService){

	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.categoria 	= {};
    ng.categorias	= [];
    ng.faixa        = {perc_desconto_min:0,perc_desconto_max:0,id_cor:null,id_cor:null};
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

	ng.selCor = function(index){
		ng.faixa.id_cor = ng.cores[index].id;
		console.log(ng.cor);
	}

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.faixa   = {perc_desconto_min:0,perc_desconto_max:0,id_cor:null};
		ng.removeError();
		ng.editing = false;
	}


	ng.loadCores = function() {
		aj.get(baseUrlApi()+"cores?tc->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.cores = data;
			})
			.error(function(data, status, headers, config) {
					ng.cores = [];
			});
	}

	ng.loadFaixas = function() {
		aj.get(baseUrlApi()+"faixasdesconto?fai->id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.faixas = data;
			})
			.error(function(data, status, headers, config) {
					ng.faixas = [];
			});
	}

	ng.salvar = function() {
		var btn 	 = $('#salvar-cor');
			btn.button('loading');
		var url 	 = 'faixadesconto';
		var itemPost = angular.copy(ng.faixa);
		var msg      = 'Faixa salva com sucesso!';

		ng.removeError();
		itemPost.perc_desconto_min = itemPost.perc_desconto_min/100;
		itemPost.perc_desconto_max = itemPost.perc_desconto_max/100;

		if(ng.faixa.id != null && ng.faixa.id > 0) {
			itemPost.id  = ng.faixa.id;
			url 		+= '/update';
			var msg      = 'Faixa atualizada com sucesso!';

		}

		itemPost.id_empreendimento  = ng.userLogged.id_empreendimento;

		aj.post(baseUrlApi()+url, itemPost)
			.success(function(data, status, headers, config) {
				ng.mensagens('alert-success','<strong>'+msg+'</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.loadFaixas();
				ng.loadCores();
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406) {
					var errors = data;

					$.each(errors, function(i, item) {
						$("#"+i).addClass("has-error");

						var formControl = $($("#"+i).find(".form-control")[0])
							.attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
						formControl.tooltip();

						if(i == 'id_cor'){
							$("#"+i).parent().css({border:"1px solid #a94442",background: '#FFEAEA'}).addClass('has-error');

							$("#"+i).parent().attr("data-toggle", "tooltip")
							.attr("data-placement", "bottom")
							.attr("title", item)
							.attr("data-original-title", item);
							$("#"+i).parent().tooltip();
						}
					});
				}
			});
	}

	ng.removeError = function(){
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").tooltip('destroy');
		$(".has-error").css({border:"none",background: 'none'}).addClass('has-error');
		$(".has-error").removeClass("has-error");
	}

	ng.editar = function(item) {
		var item = angular.copy(item);
		item.perc_desconto_min = item.perc_desconto_min * 100;
		item.perc_desconto_max = item.perc_desconto_max * 100;
		ng.faixa = angular.copy(item);
		ng.showBoxNovo(true);
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir está faixa?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"faixadesconto/delete/"+item.id)
				.success(function(data, status, headers, config) {
					ng.mensagens('alert-success','<strong>Faixa excluido com sucesso</strong>');
					ng.loadFaixas();
					ng.loadCores();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.loadCores();
	ng.loadFaixas();
});
