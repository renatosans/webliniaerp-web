app.controller('PerfisController', function($scope, $http, $window, $dialogs, UserService){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	var perfilTO = {
			nome:null,status:true,id_empreendimento:ng.userLogged.id_empreendimento,modulos:[],
			empreendimentos:[{
				nome_empreendimento:ng.userLogged.nome_empreendimento,
				id_empreendimento:ng.userLogged.id_empreendimento
			}]
		};
	ng.perfil = angular.copy(perfilTO) ;
    ng.editing = false;
    ng.busca = {perfis:""};
    ng.currentPag = {
    	perfis:{
    		offset:0,
    		limit:10
    	}
    }

    ng.chosen_perc_venda = [
    	{dsc:'Selecione',vlr:null},{dsc:'Tabela',vlr:'vlr_custo'}, {dsc:'Atacado',vlr:'perc_venda_atacado'}, {dsc:'Intermediario',vlr:'perc_venda_intermediario'}, {dsc:'Varejo',vlr:'perc_venda_varejo'}
    ]

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
					ng.reset();
					$('html,body').animate({scrollTop: 0},'slow');
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
			});
		}
	}

	ng.isNumeric = function(vlr){
		return $.isNumeric(vlr);
	}

	ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.perfil = angular.copy(perfilTO) ;
		ng.perfil.modulos = [] ;
		var treeview = $('#treeview-modulos').treeview('getUnselected', null);
		$checkableTree.treeview('collapseAll', { silent: true });
		$.each(treeview,function(i,v){
				$checkableTree.treeview('uncheckNode', [v.nodeId, {silent: true}]);
		});
	}
	var $checkableTree  ;
	function treeviewCheckChildren(node){
		if(!empty(node.nodes && node.nodes.length > 0)){
			treeviewExpanded(node);
			$.each(node.nodes,function(i,v){
		        if(!v.state.checked){
			        $scope.$apply(function () {
			           ng.perfil.modulos.push(v.id_modulo);
			        });
					$checkableTree.treeview('checkNode', [v.nodeId, {silent: true}]);
				}
				treeviewCheckChildren(v);
			});
		}
	}

	function treeviewExpanded(node){
		if(!node.state.expanded)
			$('#treeview-modulos').treeview('toggleNodeExpanded', [ node.nodeId, { silent: true } ]);
	}

	function treeviewCollapsing (node){
		if(node.state.expanded)
			$('#treeview-modulos').treeview('toggleNodeExpanded', [ node.nodeId, { silent: true } ]);
	}

	function treeviewUnCheckChildren(node){
		if(!empty(node.nodes && node.nodes.length > 0)){
			$.each(node.nodes,function(i,v){
		        if(v.state.checked){
		        	var index = ng.perfil.modulos.indexOf(v.id_modulo);
		            $scope.$apply(function () {
		           	   ng.perfil.modulos.splice(index,1);
		        	});
					$checkableTree.treeview('uncheckNode', [v.nodeId, {silent: true}]);
				}
				treeviewUnCheckChildren(v);
			});
		}
	}

	function checkPai(node){
		var parent = $checkableTree.treeview('getParent', node);
		if(!empty(parent.state)){
			if(!parent.state.checked){
				 $scope.$apply(function () {
		           ng.perfil.modulos.push(parent.id_modulo);
		        });
				$checkableTree.treeview('checkNode', [parent.nodeId, {silent: true}]);
			}
		}
	}
	
	ng.treeviewConstruct = function(data){
		console.log(data);
			$checkableTree = $('#treeview-modulos').treeview({
	          data: data,
	          showIcon: false,
	          expandIcon: 'glyphicon glyphicon-chevron-right',
	          collapseIcon: 'glyphicon glyphicon-chevron-down',
	          showCheckbox: true,
	          showBorder: false,
	          selectedBackColor: "white",
	          selectedColor: "#777",
	          onhoverColor:false,
	          onNodeChecked: function(event, node) {
	          	$scope.$apply(function () {
		           ng.perfil.modulos.push(node.id_modulo);
		        });	
		        checkPai(node);
		        treeviewCheckChildren(node);
	          },
	          onNodeUnchecked: function (event, node) {
	            var index = ng.perfil.modulos.indexOf(node.id_modulo);
	            $scope.$apply(function () {
	           	   ng.perfil.modulos.splice(index,1);
	        	});
	        	treeviewUnCheckChildren(node);
	          },
	        }).treeview('collapseAll', { silent: true });
	        var a =$checkableTree.treeview('search',
            [
              4,
              'data.cod_modulo',
              {
                ignoreCase: true,
                exactMatch: true,
                revealResults: false
              }
            ]
          );

	       console.log(a);
	}

	ng.subMenuConstruct = function(arrpai,arr){
		var menu = [] ;
		$.each(arr,function(key,value){
			if(arrpai.id_modulo == value.id_modulo_pai){
				var item = {
					id_modulo : value.id_modulo,
					id_pai : value.id_modulo_pai,
					data : {id_modulo:value.id_modulo.toString()},
					text : value.nme_modulo,
					nodes : ng.subMenuConstruct(value,arr),
					icone : value.icn_modulo
				};	
				if(item.nodes.length == 0) delete item.nodes ;
				menu.push(item);	
			}
		});

		return menu ;
	}

	ng.menuConstruct = function(Modulos){
		var menu = [] ;
		$.each(Modulos,function(key,value){
			if(empty(value.id_modulo_pai)){
				var itens = ng.subMenuConstruct(value,Modulos)
				if(itens.length > 0){
					menu.push({
						id_modulo : value.id_modulo,
						data : {id_modulo:value.id_modulo.toString()},
						text : value.nme_modulo,
						nodes : ng.subMenuConstruct(value,Modulos),
						icone : "fa-signal",
						selectable:false
					});	
				}else{
					menu.push({
						id_modulo : value.id_modulo,
						data : {id_modulo:value.id_modulo.toString()},
						text : value.nme_modulo,
						icone : "fa-signal",
						selectable:false
					});			
				}
				
			}
		});

		return menu ;
	}

	ng.loadModulos = function() {
		aj.get(baseUrlApi()+"modulos/"+ng.userLogged.id_empreendimento+"?cplSql= ORDER BY tm.psc_menu_modulo ASC")
			.success(function(data, status, headers, config) {
				var menu = ng.menuConstruct(data);
				ng.treeviewConstruct(menu);
				//console.log(menu);
			})
			.error(function(data, status, headers, config) {
				if(status == 404){

				}
				
			});
	}

	ng.salvar = function(event) {
		$('#nome').removeClass('has-error');$('input','#nome').tooltip('destroy');$('#modulos').removeClass('panel-error').tooltip("destroy");
		var btn = $(event.target);
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');
		var perfil = angular.copy(ng.perfil);
		perfil.status = Number(perfil.status); 
		var url = baseUrlApi()+"perfil/salvar" ;
		var msg = 'Perfil cadastrado com sucesso';
		if(!empty(ng.perfil.id)){
			url = baseUrlApi()+"perfil/update" ;
			msg = 'Perfil alterado com sucesso';
		}
	    perfil.empreendimentos = pick(perfil.empreendimentos,['id_empreendimento'],true);
		aj.post(url,perfil)
			.success(function(data, status, headers, config) {
				btn.button('reset');
				ng.showBoxNovo();
				ng.reset();
				ng.mensagens('alert-success','<strong>'+msg+'</strong>','#alert');
				$('html,body').animate({scrollTop: $('#panel-listagem').offset().top-50 },'slow');
				ng.loadPerfis();
				return;
			})
			.error(function(data, status, headers, config) {
				btn.button('reset');
				if(status == 406){
					if(status == 406){
			 			$.each(data, function(i, item) {
			 				if(i != 'modulos'){
							$("#"+i).addClass("has-error");
							$('input',"#"+i).attr("data-toggle", "tooltip").attr("data-placement", "top").attr("title", item).attr("data-original-title", item).tooltip();
			 				}else{
			 					$("#"+i).addClass("panel-error").attr("data-toggle", "tooltip").attr("data-placement", "top").attr("title", item).attr("data-original-title", item).tooltip();
			 				}
						});
						$('html,body').animate({scrollTop: 0},'slow');
				 	}else{
				 		$dialogs.notify('Desculpe!','<strong>Acorreu um erro durante o processo</strong>');
						return;
				 	}
				}	
				
			});
	}

	ng.perfis = {} ;
	ng.loadPerfis = function(offset,limit,posOp){
		ng.perfis.perfis = null ;
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit  ;
		ng.currentPag.perfis.offset = offset;
		ng.currentPag.perfis.limit  = limit ;
		var queryString = "?id_empreendimento="+ng.userLogged;
		queryString += empty(ng.busca) ? '' : '&nome='+ng.busca ;
		aj.get(baseUrlApi()+"perfis/"+offset+"/"+limit+"?tpue->id_empreendimento="+ng.userLogged.id_empreendimento+"&cplSql= ORDER BY tp.nome ASC")
			.success(function(data, status, headers, config) {
				ng.perfis = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.perfis.perfis = [];
					ng.perfis.paginacao = [];
			});
	}

	ng.editar = function(item,event) {
		var btn = $(event.target) ;
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');
		ng.perfil = angular.copy(item) ; 
		aj.get(baseUrlApi()+"modulos/"+ng.userLogged.id_empreendimento+"/"+item.id)
		.success(function(perfis, status, headers, config) {
			var treeview = $('#treeview-modulos').treeview('getUnselected', null);
			ng.perfil.modulos = [] ;
			$.each(treeview,function(i,v){
				if($.isNumeric(getIndex('id_modulo',v.id_modulo,perfis))){
					$checkableTree.treeview('checkNode', [v.nodeId, {silent: true}]);
					treeviewExpanded(v);
			        ng.perfil.modulos.push(v.id_modulo);
			        ng.showBoxNovo(true);
				}else{
					$checkableTree.treeview('uncheckNode', [v.nodeId, {silent: true}]);
					treeviewCollapsing(v);
				}
			});
			$('html,body').animate({scrollTop: 0},'slow');
			aj.get(baseUrlApi()+"perfil/empreendimentos?tpue->id_perfil="+item.id)
			.success(function(data, status, headers, config) {
				ng.perfil.empreendimentos = data;
				btn.button('reset');
			})
			.error(function(data, status, headers, config) {
				ng.perfil.empreendimentos = [] ;
				btn.button('reset');
			});
		})
		.error(function(data, status, headers, config) {
			ng.perfil.modulos = [] ;
			ng.perfil.empreendimentos = [{id_empreendimento:ng.userLogged.id_empreendimento}] ;
			btn.button('reset');
			if(status != 404)
				$dialogs.notify('','<strong>Ocorreu um erro ao carregar os dados</strong>');
			else{
				ng.showBoxNovo(true);
				$('html,body').animate({scrollTop: 0},'slow');
			}
		});	
	}

	ng.delete = function(){
		console.log(ng.myForm.nome.$valid);
	}

	ng.showEmpreendimentos = function() {
		$('#list_empreendimentos').modal('show');
		ng.loadAllEmpreendimentos(0,10);
	}

	ng.empreendimentos = {itens:[],paginacao:[]};
	ng.loadAllEmpreendimentos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

    	var query_string = "?id_usuario="+ng.userLogged.id;
    	if(!empty(ng.busca.empreendimento)){
    		query_string = "&" +$.param({nome_empreendimento:{exp:"like'%"+ng.busca.empreendimento+"%'"}});
    	}

    	ng.empreendimentos = [];
		aj.get(baseUrlApi()+"empreendimentos/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.empreendimentos = {itens:data.empreendimentos,paginacao:data.paginacao};
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.empreendimentos = {itens:[],paginacao:[]};
			});
	}


	ng.addEmpreendimento = function(item,$event){
		var btn = $(event.target) ;
		if(!(btn.is(':button')))
			btn = $(btn.parent('button'));
		btn.button('loading');
		item = angular.copy(item);
		item.id_empreendimento = item.id ;
		ng.perfil.empreendimentos.push(item);
		btn.button('reset');
	}

	ng.delEmpreendimento = function(index,item) {
		ng.perfil.empreendimentos.splice(index,1);
	}

	ng.empreendimentoSelected = function(item){
		var saida = false ;
		$.each(ng.perfil.empreendimentos,function(i,v){
			if(Number(item.id) == Number(v.id_empreendimento)){
				saida = true ;
				return false ;
			}
		});
		return saida ;
	}

	ng.loadModulos();
	ng.loadPerfis();
});

app.directive('bsTooltip', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            $timeout(function () {
                	  element.find("[data-toggle=tooltip]").tooltip();
                	  console.log(element.find("[data-toggle=tooltip]"));
            });
        }
    }
});
