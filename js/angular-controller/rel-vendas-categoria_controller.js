app.controller('RelatorioVendasCategoriaCtrl', function($scope, $http, $window, UserService, EmpreendimentoService) {
	var ng 				= $scope;
		aj 				= $http;
	ng.userLogged 		= UserService.getUserLogado();
	ng.dados_empreendimento = EmpreendimentoService.getDadosEmpreendimento(ng.userLogged.id_empreendimento);
	ng.itensPorPagina 	= 10;
	ng.paginacao 	   	= {};
	ng.busca			= {}
	ng.busca.categorias = '';
	ng.items 			= null;
	ng.busca.arrCategorias  = [] ;

	ng.doExportExcel = function(id_table){
    	$('#'+ id_table).tableExport({
    		filename: id_table, 
    		type:'excel', 
    		escape:'false'
    	});
    }

	ng.reset = function() {
		 $("#dtaInicial").val('');
		 $("#dtaFinal").val('');
		 ng.categoria = {} ;
		 ng.busca.categorias = '' ;
	}

	ng.resetFilter = function() {
		if(!$.isNumeric(ng.busca.id_categoria)){
			return ;
		}
		ng.reset();
		ng.loadVendas();
	}

	ng.aplicarFiltro = function() {
		$("#modal-aguarde").modal('show');
		ng.loadVendas();
	}

	ng.qtd_total_vendida = 0;
	ng.vlr_total_vendida = 0;

	ng.loadVendas = function() {
		ng.qtd_total_vendida = 0;
		ng.vlr_total_vendida = 0;
		ng.items = [];

		//var id_categoria 	= (_id_categoria != "" && !isNaN(_id_categoria)) ? _id_categoria : (!isNaN(ng.categoria.id) ? ng.categoria.id : "");

			
		var queryString 	= "?id_empreendimento="+ ng.userLogged.id_empreendimento;

		if(ng.busca.arrCategorias.length > 0){
			var in_id_cat = [];
			$.each(ng.busca.arrCategorias,function(i,x){
				in_id_cat.push(x.id_categoria) ;
			});
			queryString += "&"+$.param({'id_categoria':in_id_cat});
		}
		if(moment(ng.busca.dtaInicial).isValid() && !empty(ng.busca.dtaInicial) )
			queryString += "&dtaInicial="+ng.busca.dtaInicial;

		if(moment(ng.busca.dtaFinal).isValid() && !empty(ng.busca.dtaFinal))
			queryString += "&dtaFinal="+ ng.busca.dtaFinal;

		aj.get(baseUrlApi()+"relatorio/vendas/consolidado/categoria/"+ queryString)
			.success(function(data, status, headers, config) {
				ng.items = data.vendas;

				$.each(ng.items, function(i,item){
					ng.qtd_total_vendida += parseInt(item.qtd_total_vendida, 10);
					ng.vlr_total_vendida += parseFloat(item.vlr_total_vendida);
				});

				$("#modal-aguarde").modal('hide');

			})
			.error(function(data, status, headers, config) {
				ng.items = null;
				$("#modal-aguarde").modal('hide');
			});
	}

	ng.showCategorias = function(){
   		ng.busca.categorias = "" ;
   		if(ng.openModalCategoras){
   			ng.loadCategorias(0,10);
   			$('#list_categorias').modal('show');
   			$('#list_categorias').on('shown.bs.modal', function (e) {
			    $('#busca-modal-categorias').focus();
			});
   		}else
   			ng.openModalCategoras = true ;
   	}

   	ng.categoriaIsSelected = function(id_categoria){
   		if(typeof ng.busca.arrCategorias != 'object')
   			return ;
   		var r = false ;
   		$.each(ng.busca.arrCategorias,function(i,x){
   			if(Number(x.id_categoria) == Number(id_categoria)){
   				r = true ;
   				return ;
   			}
   		});
   		return r ;
   	}

   	ng.loadCategorias = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?id_empreendimento="+ng.userLogged.id_empreendimento;

    	if(!empty(ng.busca.categorias)){
    		var busca = ng.busca.categorias.replace(/\s/g, '%');
    		query_string += "&("+$.param({'descricao_categoria':{exp:"like'%"+busca+"%'"}})+")";
    	}

		ng.categorias = [];
		aj.get(baseUrlApi()+"categorias/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.categorias        	= data.categorias;
				ng.paginacao.categorias = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				ng.categorias = [];
			});
	}

	ng.selectCategoria = function(item){
		ng.busca.arrCategorias.push({ id_categoria : item.id, text: item.descricao_categoria});
		//$('#list_categorias').modal('hide');
	}

	ng.openModalCategoras = true ;
	$scope.$watch('busca.arrCategorias', function(newValue, oldValue) {
	 	if(oldValue.length > newValue.length ){
	 		ng.openModalCategoras = false ;
	 		setTimeout(function(){
				ng.openModalCategoras = true ;
			},100);
	 	}
     }, true);

	ng.mensagens = function(classe , msg,alertClass){
		alertClass = alertClass == null  ? '.alert-sistema' : alertClass ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset();
		if(!empty(getUrlVars().nme_categoria) && (moment(getUrlVars().dtaInicial,'DD-MM-YYYY').isValid() && moment(getUrlVars().dtaFinal,'DD-MM-YYYY').isValid() ) ) {
			$("#modal-aguarde").modal('show');
			ng.busca.arrCategorias.push({ id_categoria : getUrlVars().id_categoria, text: decodeURI(getUrlVars().nme_categoria)});
			ng.busca.dtaInicial = moment(getUrlVars().dtaInicial,'DD-MM-YYYY').format('YYYY-MM-DD');
			ng.busca.dtaFinal = moment(getUrlVars().dtaFinal,'DD-MM-YYYY').format('YYYY-MM-DD') ;
			ng.loadVendas(getUrlVars().id_categoria, getUrlVars().dtaInicial, getUrlVars().dtaFinal);
		}
		else
			ng.categoria.descricao_categoria = '';
	});

app.directive('bsPopover', function () {
    return function (scope, element, attrs) {
        element.find("a[rel=popover]").popover({
            placement: 'bottom',
            html: 'true'
        });
    };
});

