app.controller('PedidoPersonalizadoController', function($scope,$compile, $http, $window, $dialogs, UserService,ConfigService){

	var ng = $scope
		aj = $http;
	ng.teste = 'merda';
	ng.baseUrl 		= baseUrl();
	ng.userLogged 	 = UserService.getUserLogado();
	ng.configuracoes = ConfigService.getConfig(ng.userLogged.id_empreendimento);
    ng.busca 		 = {produtos:"",depositos:"",empreendimento:"",clientes:"",acessorios:"",coresEstmapa:""};
    ng.pedido 		 = {id_cor_base: null,id_cor_tira_feminina: null,id_cor_tira_masculina: null,canal_venda:'Loja',observacao:"",flg_brinde:0}
    ng.editing 		 = false;
    ng.paginacao     = {};
    ng.gradeInfantil = [] ;
    ng.gradeAdulto   = [] ;
    ng.acessorios    = [] ; 
    ng.tela          = 'pedido';
    ng.cliente       = {indicacao:0,acao_cliente:null};
    var params      = getUrlVars();
    ng.carrinhoPedido = {} ;
    ng.gradesAnteriores = {} ;
   
	ng.modo_venda = 'pdv';

    ng.mensagens = function(classe , msg, alertClass){
		alertClass = alertClass != null  ?  alertClass:'.alert-sistema' ;
		$(alertClass).fadeIn().addClass(classe).html(msg);
		setTimeout(function(){
			$(alertClass).fadeOut('slow');
		},5000);
	}
    
    ng.showBoxNovo = function(onlyShow){
    	if(onlyShow) {
			$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
			$('#box-novo').show();
		}
		else {
			ng.reset();
			$('#box-novo').toggle(400, function(){
				if($(this).is(':visible')){
					$('i','#btn-novo').removeClass("fa-plus-circle").addClass("fa-minus-circle");
				}else{
					$('i','#btn-novo').removeClass("fa-minus-circle").addClass("fa-plus-circle");
				}
			});
		}
	}

	ng.modal = function(acao,id){
		$('#'+id).modal(acao);
	}

	ng.length = function(obj){
		return Object.keys(obj).length;
	}

	ng.montarGradePedido = function(){
		ng.gradeInfantil = [] ;
		ng.gradeAdulto   = [] ;
		var flg_base ;
		if( ng.pedido.tipo_base == 'quadrada' && ng.pedido.modelo_base =='linha_lika_shoes') flg_base = 'flg_base' ;
		else if( ng.pedido.tipo_base == 'redonda' && ng.pedido.modelo_base =='linha_lika_shoes') flg_base = 'flg_base_linha_redonda' ;
		else if( ng.pedido.tipo_base == 'quadrada' && ng.pedido.modelo_base =='linha_personalizada') flg_base = 'flg_base_personalizada_quadrada' ;
		else if( ng.pedido.tipo_base == 'redonda' && ng.pedido.modelo_base =='linha_personalizada') flg_base = 'flg_base_personalizada' ;

		if( ( !empty(ng.pedido.id_cor_base) && !empty(ng.pedido.id_cor_tira_feminina) ) || ( !empty(ng.pedido.id_cor_base) && !empty(ng.pedido.id_cor_tira_masculina) ) ){
			$('[data-popover-visible="1"]').popover('hide').attr('data-popover-visible','0');
			$('#modal-bases-tiras').modal({ backdrop: 'static',keyboard: false});
			ng.loadBaseSANDTiras(flg_base);
		}
	}

	ng.loadBaseSANDTiras = function(flg_base){
		var id_cor_tira_feminina = empty(ng.pedido.id_cor_tira_feminina)   ?  0 : angular.copy(ng.pedido.id_cor_tira_feminina) ; 
		var id_cor_tira_masculina = empty(ng.pedido.id_cor_tira_masculina) ?  0 : angular.copy(ng.pedido.id_cor_tira_masculina) ; 
		aj.get(baseUrlApi()+"pedido_personalizado/bases_tiras/"+ng.userLogged.id_empreendimento+"/"+flg_base+"/"+ng.pedido.id_cor_base+"/"+id_cor_tira_feminina+"/"+id_cor_tira_masculina)
			.success(function(data, status, headers, config) {
				var base, tira , indexMas, indexFem ;
				$.each(data,function(i,v){
					$.each(v.fem_itens,function(a,b){
						if(b.tipo == 'base'){
							v.fem_itens[a].tipo_base   = ng.pedido.tipo_base ;
							v.fem_itens[a].modelo_base = ng.pedido.modelo_base;

						}
					});
					$.each(v.mas_itens,function(a,b){
						if(b.tipo == 'base'){
							v.mas_itens[a].tipo_base   = ng.pedido.tipo_base ;
							v.mas_itens[a].modelo_base = ng.pedido.modelo_base;

						}
					});
					base  = ng.getbase(v.fem_itens); 
					tira  = ng.getTira(v.fem_itens,'tira_feminina');
					indexFem = "fem-"+v.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor ;
					tira  = ng.getTira(v.mas_itens,'tira_masculina');
					indexMas = "mas-"+v.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor ;
					
					if( !(ng.carrinhoPedido[indexMas] == undefined )){
						v.mas_qtd =  ng.carrinhoPedido[indexMas].qtd ;
					}

					if( !(ng.carrinhoPedido[indexFem] == undefined )){
						v.fem_qtd =  ng.carrinhoPedido[indexFem].qtd ;
					}

					v.perc_desconto_compra = 0 ;
					if(v.nome_tamanho <= '31/32'){
						if(v.fem_valid || v.mas_valid)
							ng.gradeInfantil.push(v);
					}else{
						if(v.fem_valid || v.mas_valid)
							ng.gradeAdulto.push(v);
					}
				});
					console.log(ng.gradeInfantil);
					console.log(ng.gradeAdulto);
				$('#modal-bases-tiras').modal('hide');
			})
			.error(function(data, status, headers, config) {
				$('#modal-bases-tiras').modal('hide');
				$dialogs.notify('Atenção!','<strong>Deculpe, Não Existe Resultado Para a Pesquisa.</strong>');
				return;
			});
	}

	var currentItemGrade = null ;
	var currentItemGradeTipo = null ;
	ng.openModalAcessorios = function(item,tipo){
		$('[data-popover-visible="1"]').popover('hide').attr('data-popover-visible','0');
		currentItemGradeTipo = tipo ;
		currentItemGrade = item ;
		ng.modal('show','modal-acessorios');
		ng.busca.acessorios = "" ;
		ng.loadAcessorios(0,10);
	}

	ng.existsAcessorio = function(item){
		var saida = false ;
		var acessorios = currentItemGradeTipo == 'masculino' ? currentItemGrade.acessoriosMasculinos : currentItemGrade.acessoriosFemininos ;
		if(acessorios == null || acessorios.length == 0)
			return saida ;
		$.each(acessorios,function(i,x){
			if(Number(x.id_produto) == Number(item.id_produto)){
				saida = true ;
				return ;
			}
		});

		return saida ;
	}

	ng.loadAcessorios = function(offset,limit){
			
		offset = offset == null ? 0  : offset ;
		limit  = limit  == null ? 10 : limit ;
		ng.acessorios = [] ;
		var query_string = "";
		query_string = empty(ng.busca.acessorios) ? '' : "?"+$.param({'(tp->nome':{exp:"LIKE '%"+ng.busca.acessorios+"%'"}})+" OR tp.id = '"+ng.busca.acessorios+"')";
		aj.get(baseUrlApi()+"pedido_personalizado/getAcessorios/51/"+offset+"/"+limit+query_string)
			.success(function(data, status, headers, config) {
				ng.acessorios = data ;
			})
			.error(function(data, status, headers, config) {
				ng.acessorios = null ;
				alert('Ocorreu um erro durante a busca.');
			});
	}

	ng.addAcessorio = function(item){
		if(currentItemGradeTipo == 'masculino'){
			if(currentItemGrade.acessoriosMasculinos == undefined)
			currentItemGrade.acessoriosMasculinos = [] ;
			currentItemGrade.acessoriosMasculinos.push(angular.copy(item));	
		}else{
			if(currentItemGrade.acessoriosFemininos == undefined)
			currentItemGrade.acessoriosFemininos = [] ;
			currentItemGrade.acessoriosFemininos.push(angular.copy(item));
		}
		
	}

	ng.inserirPedido = function(){
		$('[data-popover-visible="1"]').popover('hide').attr('data-popover-visible','0');
		var btn = $("#inserir-pedido") ;
		ng.montarchinelos();
		ng.calcularDesconto();
	}

	ng.montarchinelos = function(pedido_edit){
		pedido_edit  = pedido_edit == true ? pedido_edit : false ;
		var btn = $('#inserir-pedido');
		btn.button('loading');
		var chinelosAdultos  = {} ;
		var chinelosInfantis = {} ;
		var qtd_adultos  = 0 ;
		var qtd_infantis = 0 ;
		var indexGraAnt  = Number(ng.pedido.flg_brinde) ? "brinde-" : "" ;
	    var aux_pedido_id_cor_tira_feminina = $.isNumeric(ng.pedido.id_cor_tira_feminina) ? ng.pedido.id_cor_tira_feminina : '0'  ;
	    var aux_pedido_id_cor_tira_masculina = $.isNumeric(ng.pedido.id_cor_tira_masculina) ? ng.pedido.id_cor_tira_masculina :'0'  ;
		indexGraAnt +=  ng.pedido.id_cor_base+'-'+aux_pedido_id_cor_tira_feminina+'-'+aux_pedido_id_cor_tira_masculina+'-base_'+ng.pedido.modelo_base+'_'+ng.pedido.tipo_base;
		
		//Chinelos Adulto
		if(ng.gradeAdulto != null && ng.gradeAdulto.length >0){
			$.each(ng.gradeAdulto,function(i,v){

				var fem_qtd = $.isNumeric(v.fem_qtd) ? Number(v.fem_qtd) : 0 ;
				var mas_qtd = $.isNumeric(v.mas_qtd) ? Number(v.mas_qtd) : 0 ;
				var base ; 
				var tira ;
				var nome_chinelo;
				var insumos = [] ;
				if(fem_qtd > 0){
					qtd_adultos += fem_qtd
					base       = ng.getbase(v.fem_itens); 
					tira 	   = ng.getTira(v.fem_itens,'tira_feminina');
					insumos    = [base,tira] ;
					if(!empty(ng.pedido.modeloEstampa) && $.isNumeric(ng.pedido.modeloEstampa.id_produto))
						insumos = insumos.concat(ng.pedido.modeloEstampa);
					if(v.acessoriosFemininos != undefined && v.acessoriosFemininos.length > 0)
						insumos = insumos.concat(v.acessoriosFemininos);
					var modelo = ng.pedido.modelo_base == 'linha_lika_shoes' ? 'LINHA LIKA SHOES' : 'LINHA PERSONALIZADA' ;
					var tipo = ng.pedido.tipo_base == 'quadrada' ? 'QUAD.' : 'RED.' ;
					nome_chinelo = "CHINELO PER. FEM. BASE "+modelo+" "+tipo+" "+base.nome_tamanho+" "+base.nome_cor.toUpperCase()+" TIRA "+tira.nome_cor.toUpperCase();
					var brinde = '';
					var brinde  = Number(ng.pedido.flg_brinde) ? "-brinde" : "" ; 
					chinelosAdultos['fem-'+base.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor+brinde+'-base_'+ng.pedido.modelo_base+'_'+ng.pedido.tipo_base] = {
						nome:nome_chinelo ,
						qtd :fem_qtd,
						insumos :insumos,
						indexGrad : indexGraAnt,
						valor_desconto : 0,
						tipo : 'adulto',
						flg_brinde : Number(ng.pedido.flg_brinde),
						nome_tamanho : base.nome_tamanho
					};
				}else if(v.fem_valid){
					base       = ng.getbase(v.fem_itens); 
					tira 	   = ng.getTira(v.fem_itens,'tira_feminina');
					var brinde = '';
					brinde  = Number(ng.pedido.flg_brinde) ? "-brinde" : "" ;
					var index_aux = 'fem-'+base.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor+brinde+'-base_'+ng.pedido.modelo_base+'_'+ng.pedido.tipo_base;
					if(!(ng.carrinhoPedido[index_aux] == undefined)){
						delete ng.carrinhoPedido[index_aux] ;
					}
				}
				if(mas_qtd > 0){
					qtd_adultos += mas_qtd
					base       = ng.getbase(v.mas_itens); 
					tira 	   = ng.getTira(v.mas_itens,'tira_masculina');
					insumos    = [base,tira] ;
					if(!empty(ng.pedido.modeloEstampa) && $.isNumeric(ng.pedido.modeloEstampa.id_produto))
						insumos = insumos.concat(ng.pedido.modeloEstampa);
					if(v.acessoriosMasculinos != undefined && v.acessoriosMasculinos.length > 0)
						insumos = insumos.concat(v.acessoriosMasculinos);
					var modelo = ng.pedido.modelo_base == 'linha_lika_shoes' ? 'LINHA LIKA SHOES' : 'LINHA PERSONALIZADA' ;
					var tipo = ng.pedido.tipo_base == 'quadrada' ? 'QUAD.' : 'RED.' ;
					nome_chinelo = "CHINELO PER. MAS. BASE "+modelo+" "+tipo+" "+base.nome_tamanho+" "+base.nome_cor.toUpperCase()+" TIRA "+tira.nome_cor.toUpperCase();
					var brinde = '';
					brinde  = Number(ng.pedido.flg_brinde) ? "-brinde" : "" ;
					chinelosAdultos['mas-'+base.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor+brinde+'-base_'+ng.pedido.modelo_base+'_'+ng.pedido.tipo_base] = {
						nome:nome_chinelo ,
						qtd :mas_qtd,
						insumos :insumos,
						indexGrad : indexGraAnt,
						valor_desconto : 0,
						tipo : 'adulto',
						flg_brinde : Number(ng.pedido.flg_brinde),
						nome_tamanho : base.nome_tamanho
					};
				}else if(v.mas_valid){
					base       = ng.getbase(v.mas_itens); 
					tira 	   = ng.getTira(v.mas_itens,'tira_masculina');
					var brinde = '';
					brinde  = Number(ng.pedido.flg_brinde) ? "-brinde" : "" ;
					var index_aux =  'mas-'+base.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor+brinde+'-base_'+ng.pedido.modelo_base+'_'+ng.pedido.tipo_base;
					if(!(ng.carrinhoPedido[index_aux] == undefined)){
						delete ng.carrinhoPedido[index_aux] ;
					}
				}
			});
		}

		var vlr_uni_adulto_base   = ng.getPrecoChinelo('adulto',qtd_adultos) ;

		$.each(chinelosAdultos,function(i,v){
			var vlr_custo = 0 ;
			var vlr_uni_adulto = 0;
			var ex = 0 ;
			vlr_uni_adulto = vlr_uni_adulto_base+ng.getValorAdd(v.nome_tamanho);
		    $.each(v.insumos,function(x,y){
		    	if(y.tipo == undefined || y.tipo == 'acessorio'){
		    		vlr_custo += Number(y.qtd) * Number(y.vlr_custo_real) ;
		    		vlr_uni_adulto += Number(y.qtd) * Number(y.vlr_venda_atacado) ;
		    	}
		    	else{
		    		vlr_custo +=  Number(y.vlr_custo_real) ;
		    	}
		    });
		    chinelosAdultos[i].vlr_custo 			= vlr_custo ;
		    chinelosAdultos[i].valor_real_item  	= vlr_uni_adulto;
			chinelosAdultos[i].valor_uni      		= chinelosAdultos[i].valor_real_item ;
			ex = vlr_uni_adulto - vlr_custo ;
			chinelosAdultos[i].perc_margem_aplicada = ((ex * 100)/vlr_custo)/100;

		});

	//Chinelos Infantis
		if(ng.gradeInfantil != null && ng.gradeInfantil.length >0){
			$.each(ng.gradeInfantil,function(i,v){
				var fem_qtd = $.isNumeric(v.fem_qtd) ? Number(v.fem_qtd) : 0 ;
				var mas_qtd = $.isNumeric(v.mas_qtd) ? Number(v.mas_qtd) : 0 ;
				var base ; 
				var tira ;
				var nome_chinelo;
				var insumos = [] ;
				if(fem_qtd > 0){
					qtd_infantis += fem_qtd ;
					base       = ng.getbase(v.fem_itens); 
					tira 	   = ng.getTira(v.fem_itens,'tira_feminina');
					insumos    = [base,tira] ;
					if(!empty(ng.pedido.modeloEstampa) && $.isNumeric(ng.pedido.modeloEstampa.id_produto))
						insumos = insumos.concat(ng.pedido.modeloEstampa);
					if(v.acessoriosFemininos != undefined && v.acessoriosFemininos.length > 0)
						insumos = insumos.concat(v.acessoriosFemininos);
					var modelo = ng.pedido.modelo_base == 'linha_lika_shoes' ? 'LINHA LIKA SHOES' : 'LINHA PERSONALIZADA' ;
					var tipo = ng.pedido.tipo_base == 'quadrada' ? 'QUAD.' : 'RED.' ;
					nome_chinelo = "CHINELO PER. FEM. BASE "+modelo+" "+tipo+" "+base.nome_tamanho+" "+base.nome_cor.toUpperCase()+" TIRA "+tira.nome_cor.toUpperCase();
					var brinde = '';
					brinde  = Number(ng.pedido.flg_brinde) ? "-brinde" : "" ;
					chinelosInfantis['fem-'+base.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor+brinde+'-base_'+ng.pedido.modelo_base+'_'+ng.pedido.tipo_base] = {
						nome:nome_chinelo ,
						qtd :fem_qtd,
						insumos :insumos,
						indexGrad : indexGraAnt,
						valor_desconto : 0,
						tipo : 'infantil',
						flg_brinde : Number(ng.pedido.flg_brinde)
					};
				}else if(v.fem_valid){
					base       = ng.getbase(v.fem_itens); 
					tira 	   = ng.getTira(v.fem_itens,'tira_feminina');
					var brinde = '';
					brinde  = Number(ng.pedido.flg_brinde) ? "-brinde" : "" ;
					var index_aux = 'fem-'+base.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor+brinde+'-base_'+ng.pedido.modelo_base+'_'+ng.pedido.tipo_base ;
					if(!(ng.carrinhoPedido[index_aux] == undefined)){
						delete ng.carrinhoPedido[index_aux] ;
					}
				}
				if(v.mas_qtd > 0){
					qtd_infantis += mas_qtd ;
					base       = ng.getbase(v.mas_itens); 
					tira 	   = ng.getTira(v.mas_itens,'tira_masculina');
					insumos    = [base,tira] ;
					if(!empty(ng.pedido.modeloEstampa) && $.isNumeric(ng.pedido.modeloEstampa.id_produto))
						insumos = insumos.concat(ng.pedido.modeloEstampa);
					if(v.acessoriosMasculinos != undefined && v.acessoriosMasculinos.length > 0)
						insumos = insumos.concat(v.acessoriosMasculinos);
					var modelo = ng.pedido.modelo_base == 'linha_lika_shoes' ? 'LINHA LIKA SHOES' : 'LINHA PERSONALIZADA' ;
					var tipo = ng.pedido.tipo_base == 'quadrada' ? 'QUAD.' : 'RED.' ;
					nome_chinelo = "CHINELO PER. MAS. BASE "+modelo+" "+tipo+" "+base.nome_tamanho+" "+base.nome_cor.toUpperCase()+" TIRA "+tira.nome_cor.toUpperCase(); 
					var brinde  = Number(ng.pedido.flg_brinde) ? "-brinde" : "" ;
					chinelosInfantis["mas-"+base.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor+brinde+'-base_'+ng.pedido.modelo_base+'_'+ng.pedido.tipo_base]={
						nome:nome_chinelo ,
						qtd :mas_qtd,
						insumos :insumos,
						indexGrad : indexGraAnt,
						valor_desconto : 0,
						tipo : 'infantil',
						flg_brinde : Number(ng.pedido.flg_brinde)
					};
				}else if(v.mas_valid){
					base       = ng.getbase(v.mas_itens); 
					tira 	   = ng.getTira(v.mas_itens,'tira_masculina');
					var brinde = '';
					brinde  = Number(ng.pedido.flg_brinde) ? "-brinde" : "" ;
					var index_aux = 'mas-'+base.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor+brinde+'-base_'+ng.pedido.modelo_base+'_'+ng.pedido.tipo_base;
					if(!(ng.carrinhoPedido[index_aux] == undefined)){
						delete ng.carrinhoPedido[index_aux] ;
					}
				}
			});
		}
		var vlr_uni_infantil_base = ng.getPrecoChinelo('infantil',qtd_infantis) ;

		$.each(chinelosInfantis,function(i,v){
			var vlr_custo = 0  ;
			var vlr_uni_infantil = 0
			var ex = 0 ;
			vlr_uni_infantil = vlr_uni_infantil_base+ng.getValorAdd(v.nome_tamanho);
		    $.each(v.insumos,function(x,y){
		    	if(y.tipo == undefined || y.tipo == 'acessorio'){
		    		vlr_custo 		 += Number(y.qtd) * Number(y.vlr_custo_real) ;
		    		vlr_uni_infantil += Number(y.qtd) * Number(y.vlr_venda_atacado) ;
		    	}
		    	else{
		    		vlr_custo +=  Number(y.vlr_custo_real) ;
		    	}
		    });
		    chinelosInfantis[i].valor_real_item  = vlr_uni_infantil;
			chinelosInfantis[i].valor_uni        = chinelosInfantis[i].valor_real_item ;
		    chinelosInfantis[i].vlr_custo 		 = vlr_custo ;
		    ex = vlr_uni_infantil - vlr_custo ;
			chinelosInfantis[i].perc_margem_aplicada = ((ex * 100)/vlr_custo)/100;
		});
		
		//console.log("---------------- Chinelos Adultos ----------------");
		//console.log(chinelosAdultos);

		//console.log("---------------- Chinelos Infantis ----------------");
		//console.log(chinelosInfantis);

		ng.gradesAnteriores[indexGraAnt] = {
			gradeInfantil 		: angular.copy(ng.gradeInfantil),
			gradeAdulto   		: angular.copy(ng.gradeAdulto),
			coresEstampa  		: angular.copy(ng.pedido.coresEstampa),
			flg_brinde    		: angular.copy(ng.pedido.flg_brinde),
			modeloEstampa 	    : angular.copy(ng.pedido.modeloEstampa),
			tipo_base			: angular.copy(ng.pedido.tipo_base),
			modelo_base			: angular.copy(ng.pedido.modelo_base)
		}

		//console.log("---------------- Grades Anteriores ----------------");
		$.each(chinelosAdultos,function(i,x){
			ng.carrinhoPedido[i] = x;
		});
		$.each(chinelosInfantis,function(i,x){
			ng.carrinhoPedido[i] = x;
		});
		//console.log(ng.gradesAnteriores);
		console.log("---------------- pedido ----------------");
		console.log(ng.carrinhoPedido);

		ng.pedido.modeloEstampa = {};
		ng.pedido.coresEstampa = [] ;
		ng.pedido.flg_brinde = 0 ;
		ng.pedido.id_cor_base = null ;
		ng.pedido.id_cor_tira_feminina = null ;
		ng.pedido.id_cor_tira_masculina = null ;
		ng.gradeInfantil = [] ;
		ng.gradeAdulto   = [] ;
		ng.pedido.tipo_base = null ;
		ng.pedido.modelo_base = null ;

		btn.button('reset');
		if(!pedido_edit){
			$('html,body').animate({scrollTop: $('#fieldset-resumo-pedido').offset().top - 50},'slow');
			ng.mensagens('alert-success','<strong>Pedido Alterado Com Sucesso</strong>','.alert-item-pedido');
		}
	}

	ng.deleteItemPedido = function(index){
		delete ng.carrinhoPedido[index];
		$('html,body').animate({scrollTop: $('#fieldset-resumo-pedido').offset().top - 50},'slow');
		ng.mensagens('alert-success','<strong>Pedido Alterado Com Sucesso</strong>','.alert-item-pedido');
	}

	ng.hidePopOver = function(){
		$('[data-popover-visible="1"]').popover('hide').attr('data-popover-visible','0');	
	}

	var flg_brinde_ant ;
	ng.editarItemPedido = function(item){
		$('[data-popover-visible="1"]').popover('hide').attr('data-popover-visible','0');
		var id = Number(item.flg_brinde) ? item.indexGrad.replace("brinde-","").split("-") : item.indexGrad.split("-"); 
		ng.pedido.id_cor_base = Number(id[0]) ;
		ng.pedido.id_cor_tira_feminina = Number(id[1]) ;
		ng.pedido.id_cor_tira_masculina = Number(id[2]) ;
		var grade = ng.gradesAnteriores[item.indexGrad];
		ng.gradeInfantil = angular.copy(grade.gradeInfantil) ;
		ng.gradeAdulto   = angular.copy(grade.gradeAdulto) ;
		ng.pedido.coresEstampa = angular.copy(grade.coresEstampa) ;
		ng.pedido.flg_brinde  = angular.copy(grade.flg_brinde);
		ng.pedido.tipo_base  = angular.copy(grade.tipo_base);
		ng.pedido.modelo_base  = angular.copy(grade.modelo_base);
		flg_brinde_ant = ng.pedido.flg_brinde ;
		ng.pedido.modeloEstampa = angular.copy(grade.modeloEstampa) ;
		ng.setTipoBaseEdit();
		$('html,body').animate({scrollTop: $('#fieldset-item-pedido').offset().top - 50},'slow');
	}

	ng.getPrecoChinelo = function(tipo,qtd){
    	var qtd_i = 0 ;
    	var qtd_f = 0 ;
    	var qtd_arr = [] ;
    	var vlr_chinelo = false ;
    	if(tipo == 'infantil'){
    		$.each(ng.chinelosInfantis.precos,function(qtd_preco,vlr_preco){
    			qtd_arr = qtd_preco.split('-');
    			if(qtd_arr.length == 2){
    				qtd_i = Number(qtd_arr[0]);
    				qtd_f = Number(qtd_arr[1]);
    				if(qtd >= qtd_i && qtd <= qtd_f){
    					vlr_chinelo = Number(vlr_preco);
    					return;
    				}
    			}else{
    				qtd_i = Number(qtd_arr[0]);
    				if(qtd >= qtd_i){
    					vlr_chinelo = Number(vlr_preco);
    					return;
    				}
    			}  			
	    		if(vlr_chinelo != false)
	    			return ;
    		});	
    	}

    	if(tipo == 'adulto'){
    		$.each(ng.chinelosAdultos.precos,function(qtd_preco,vlr_preco){
    			qtd_arr = qtd_preco.split('-');
    			if(qtd_arr.length == 2){
    				qtd_i = Number(qtd_arr[0]);
    				qtd_f = Number(qtd_arr[1]);
    				if(qtd >= qtd_i && qtd <= qtd_f){
    					vlr_chinelo = Number(vlr_preco);
    					return;
    				}
    			}else{
    				qtd_i = Number(qtd_arr[0]);
    				if(qtd >= qtd_i){
    					vlr_chinelo = Number(vlr_preco);
    					return;
    				}
    			}  			
	    		if(vlr_chinelo != false)
	    			return ;
    		});	
    	}


    	return vlr_chinelo ;
    }

    ng.getValorAdd = function(nome_tamanho){
    	var vlrAdd = 0;
    	if(ng.pedido.tipo_base == 'quadrada') vlrAdd += ng.valoresAdicionais.chinelo_quadrado ;
		var qtd_cores_estampa =  _.groupBy(ng.pedido.coresEstampa,'id_cor') ;
		qtd_cores_estampa = (Object.keys(qtd_cores_estampa).length) - 1 ;
		if(qtd_cores_estampa > 0)  vlrAdd += (ng.valoresAdicionais.cor_adicional*qtd_cores_estampa) ;
		if(nome_tamanho > '40/40') vlrAdd += ng.valoresAdicionais.acima_41 ; 
		return vlrAdd ;
    }


	var dtaVenda	;
	var dtaEntrega 	;
	ng.salvar = function(){
		var error = 0 ;
    	$('.has-error').tooltip('destroy');
    	$('.has-error').removeClass('has-error');

		dtaVenda	= $("#dtaVenda").val();
		dtaEntrega 	= $("#dtaEntrega").val();

		if(empty(ng.cliente.acao_cliente)){
			$dialogs.notify('Atenção!','<strong>Informe um Cliente Para o Pedido.</strong>');
			return ;
		}

		if(ng.length(ng.carrinhoPedido) == 0){
			$dialogs.notify('Atenção!','<strong>Nenhum Produto Foi Montado.</strong>');
			return ;
		}

		if(empty(dtaVenda)){
			error ++ ;
			$("#label-dta-venda").addClass("has-error");
			 $("#form-dta-venda").addClass("has-error");
				var formControl = $("#form-dta-venda")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Campo obrigatório')
					.attr("data-original-title", 'Campo obrigatório');
				formControl.tooltip('show');
		}

		if(empty(dtaEntrega)){
			error ++ ;
			$("#label-dta-entrega").addClass("has-error");
			$("#form-dta-entrega").addClass("has-error");
				var formControl = $("#form-dta-entrega")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Campo obrigatório')
					.attr("data-original-title", 'Campo obrigatório');
				formControl.tooltip('show');
		}

		if(error > 0)
			return ;


		 $('#btn-salvar').button('loading');

		ng.cliente.id_empreendimento = ng.userLogged.id_empreendimento;
    	ng.cliente.empreendimentos   = [{id:ng.userLogged.id_empreendimento}];
    	aj.post(baseUrlApi()+"pedido/cliente/cadastro_rapido",ng.cliente)
			.success(function(data, status, headers, config) {
					ng.cliente.id = data.id_cliente ;
					ng.salvarPedidoPerosonalizado();
			})
			.error(function(data, status, headers, config) {
				$('#btn-salvar').button('reset');
				if(status == 406) {
		 			var errors = data;
		 			var first  = null ;
		 			$.each(errors, function(i, item) {
		 				$("#"+i).addClass("has-error");
		 				first = first == null ? $("#"+i) : first ;
		 				var formControl = $($("#"+i))
		 					.attr("data-toggle", "tooltip")
		 					.attr("data-placement", "bottom")
		 					.attr("title", item)
		 					.attr("data-original-title", item);
		 				formControl.tooltip();
		 			});
		 			$('html,body').animate({scrollTop: 0},100,function(){
		 				first.focus();
		 				first.tooltip('show');
		 			});
			 	}else{
			 		alert('Erro ao Atualizar Cliente')
			 	}
			});
	}

	ng.salvarPedidoPerosonalizado = function(){
		var qtd_base  = 0 ;
		var qtd_tiras = 0 ;
		var error = 0 ;
		var produtos = [];
		var id_produto_base = null;
		var chinelos_gerados = [] ;
		var itens = [] ;
		var estampas = {} ;
		$.each(ng.gradesAnteriores,function(i,item){
			$.each(item.coresEstampa,function(x,z){
				if(estampas[i] == undefined)
					estampas[i] = [];
				estampas[i].push(z);
			});
		});

		$.each(ng.carrinhoPedido,function(i,x) {
			var valor_desconto ;
			var valor_real_item ;
			if(Number(x.flg_brinde) == 1){
				valor_desconto = 1 ;
				valor_real_item = 0 ;
			}else{
				valor_desconto = (empty(x.valor_desconto) ? 0 : x.valor_desconto);
				valor_real_item = x.valor_real_item ;
			}
			chinelos_gerados.push({
				nome 					: x.nome,
				qtd 					: Number(x.qtd),
				tipo 					: x.tipo,
				valor_real_item 		: valor_real_item,
				vlr_custo 				: x.vlr_custo,
				perc_margem_aplicada    : x.perc_margem_aplicada,
				desconto_aplicado       : (valor_desconto > 0 ? 1 : 0),
				flg_brinde              : Number(x.flg_brinde),
				valor_desconto          : valor_desconto,
			});
			var base = ng.getbase(x.insumos);
			var aux  = [] ;
			$.each(x.insumos,function(y,z){
				aux.push({
					desconto_aplicado: 0,
					id_produto: z.id_produto,
					id_produto_base: ( z.tipo == 'base' ? null : base.id_produto),
					perc_desconto_compra: z.perc_desconto_compra,
					perc_imposto_compra: z.perc_imposto_compra,
					perc_margem_aplicada: z.perc_venda_atacado,
					qtd: ( !( empty(z.tipo) || z.tipo == 'acessorio' ) ? Number(x.qtd) : (Number(x.qtd) * Number(z.qtd)) ),
					tipo_produto: ( empty(z.tipo) ? 'acessorio' : z.tipo ),
					valor_desconto: z.valor_desconto,
					valor_real_item: z.vlr_venda_atacado,
					vlr_custo: z.vlr_custo_real,
					config_grad : x.indexGrad.replace("brinde-",""),
					flg_brinde  : Number(x.flg_brinde),
					tipo_base : ( empty(z.tipo_base) ? null : z.tipo_base ),
					modelo_base : ( empty(z.modelo_base) ? null : z.modelo_base )
				});
			});
			itens.push(aux);
		});

		dtaVenda 			= formatDate(dtaVenda);
		dtaEntrega  		= formatDate(dtaEntrega);

		var venda = {
			id_usuario : ng.userLogged.id ,
			id_cliente : ng.cliente.id,
			venda_confirmada : 0,
			id_empreendimento : ng.userLogged.id_empreendimento,
			id_status_pedido : 1,
			itens : itens,
			chinelos_gerados : chinelos_gerados,
			estampas : estampas,
			canal_venda : ng.pedido.canal_venda,
			dta_venda : dtaVenda,
			dta_entrega : dtaEntrega,
			observacao : ng.pedido.observacao,
			dta_lancamento : moment().format('YYYY-MM-DD HH:mm:ss') 
		}
		var pagamentos   = [] ;

		//pagamentos
		var Today        = new Date();
		var data_atual   = Today.getDate()+"/"+(Today.getMonth()+1)+"/"+Today.getFullYear();

		$.each(ng.recebidos, function(i,v){
			var parcelas = Number(v.parcelas);

			v.data_pagamento 			= formatDate(data_atual);
			v.id_abertura_caixa 		= ng.caixa_aberto.id ;
			v.id_plano_conta    		= ng.caixa.id_plano_caixa;
			v.id_tipo_movimentacao		= 3;
			v.id_cliente				= ng.cliente.id;
			v.id_forma_pagamento		= v.id_forma_pagamento;
			v.valor_pagamento			= v.valor;
			v.status_pagamento			= 1;
			v.id_empreendimento			= ng.userLogged.id_empreendimento;
			v.id_conta_bancaria       	= ng.caixa.id_caixa;
			v.id_cliente_lancamento		= ng.caixa.id_cliente_movimentacao_caixa;

			if(Number(v.id_forma_pagamento) == 6){

				var valor_parcelas 	 = v.valor/parcelas ;
				var next_date		 = somadias(data_atual,30);
				var itens_prc        = [] ;

				for(var count = 0 ; count < parcelas ; count ++){
					var item 			 = angular.copy(v);
					item.valor_pagamento = valor_parcelas ;
					item.data_pagamento  = formatDate(next_date) ;
					next_date			 = somadias(next_date,30);

					itens_prc.push(item);
				}

				pagamentos.push({id_forma_pagamento : v.id_forma_pagamento ,id_tipo_movimentacao: 3, parcelas:itens_prc});

			}else if(Number(v.id_forma_pagamento) == 2){
				$.each(ng.pg_cheques,function(i_cheque, v_cheque){
					v.id_banco 				= v_cheque.id_banco ;
					v.num_conta_corrente 	= v_cheque.num_conta_corrente ;
					v.num_cheque 			= v_cheque.num_cheque ;
					v.flg_cheque_predatado 	= v_cheque.flg_cheque_predatado ;
					v.data_pagamento 		= v_cheque.data_pagamento ;
					v.valor_pagamento 		= v_cheque.valor_pagamento ;
					v_push = angular.copy(v);
					pagamentos.push(v_push);
				});
			}else if(Number(v.id_forma_pagamento) == 4){
				$.each(ng.pg_boletos,function(i_boleto, v_boleto){
					v.id_banco 				= v_boleto.id_banco ;
					v.data_pagamento 		= v_boleto.data_pagamento ;
					v.valor_pagamento 		= v_boleto.valor_pagamento ;
					v.doc_boleto            = v_boleto.doc_boleto ;
					v.num_boleto            = v_boleto.num_boleto ;
					v.status_pagamento      = v_boleto.status_pagamento ;
					v_push = angular.copy(v);
					pagamentos.push(v_push);
				});
			}else{
				pagamentos.push(v);
			}
		});

		if(ng.troco > 0 && ng.modo_venda == 'pdv'){
			$.each(pagamentos,function(key,value){
				if(Number(value.id_forma_pagamento) == 3){
					pagamentos[key].valor           = pagamentos[key].valor_pagamento - ng.troco ;
					pagamentos[key].valor_pagamento = pagamentos[key].valor_pagamento - ng.troco ;
				}
			});
		}

		var vlr_restante = ng.vlrTotalCompra - ng.total_pg;

		if(vlr_restante > 0){
			item = {
			id_abertura_caixa		:ng.caixa_aberto.id,
			id_plano_conta   		:ng.caixa.id_plano_caixa,
			id_tipo_movimentacao 	: 5,
			valor 					:vlr_restante
			}
			pagamentos.push(item);
		}

		var url = "pedido_venda/gravar_pedido_venda" ;
		var msg = "Pedido cadastrado com sucesso" ;
		if( !empty(ng.pedido.id) ) {
			url += "/update" ;
			venda.id = ng.pedido.id;
			venda.id_pedido_gerado = ng.pedido.id_pedido_gerado ;
			msg = "Pedido atualizado com sucesso" ;
		}

		aj.post(baseUrlApi()+url,{pedido_venda:venda,pagamentos:pagamentos})
			.success(function(data, status, headers, config) {
				ng.tela = 'pedido';
				$.cookie("alerta", JSON.stringify({msg:msg,class:'alert-success'})) ;
				window.location = "lista_pedidos_personalizados.php";
			})
			.error(function(data, status, headers, config) {
				$('#btn-salvar').button('reset');
				alert("Ocorreu um erro ao cadastrar o pedido");
			});
	}

	ng.getbase = function(obj){
		var x ="";
		$.each(obj,function(i,v){
			if(v.tipo == 'base'){
				x = v ;
				return ;
			}	
		});
		return x ;
	}

	ng.getTira = function(obj,tipo){
		var x ="";
		$.each(obj,function(i,v){
			if(v.tipo == tipo){
				x = v ;
				return ;
			}	
		});
		return x ;
	}

	ng.totalPedido = function(real){
		var total = 0;
		$.each(ng.carrinhoPedido,function(i,x){
			if(Number(x.flg_brinde) != 1)
			total += x.qtd * x.valor_real_item ;
		});
		ng.vlrTotalCompra = Math.round(total * 100) / 100;
		return real == null ? numberFormat(total,2,',','.') : total ;
	}

	ng.qtdtotalItensPedido = function(real){
		var total = 0;
		$.each(ng.carrinhoPedido,function(i,x){
			total += x.qtd  ;
		});
		return total ;
	}


	ng.chosen_cor_base  = [] ;
	ng.loadCoresBase = function(flg){
		ng.pedido.id_cor_base = 0 ;
		ng.chosen_cor_base  = [{id:0,nome_cor:'Carregando ...'}] ;
		aj.get(baseUrlApi()+"pedido_personalizado/cores_base?cplSql=tpe.id_empreendimento="+ng.userLogged.id_empreendimento+" AND tcep.nome_campo = '"+flg+"' AND tvcep.valor_campo = 1 GROUP BY tcp.id ORDER BY tcp.nome_cor ASC")
			.success(function(data, status, headers, config) {
				ng.pedido.id_cor_base = null ;
				ng.chosen_cor_base  = [{id:null,nome_cor:'Selecione'}] ;
				ng.chosen_cor_base = ng.chosen_cor_base.concat(data);
				setTimeout(function(){ $("select").trigger("chosen:updated"); }, 300);
				//;
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.loadCoresBaseEdit = function(flg){
		var id_cor_base = angular.copy(ng.pedido.id_cor_base);
		ng.pedido.id_cor_base = 0 ;
		ng.chosen_cor_base  = [{id:0,nome_cor:'Carregando ...'}] ;
		aj.get(baseUrlApi()+"pedido_personalizado/cores_base?cplSql=tpe.id_empreendimento="+ng.userLogged.id_empreendimento+" AND tcep.nome_campo = '"+flg+"' AND tvcep.valor_campo = 1 GROUP BY tcp.id ORDER BY tcp.nome_cor ASC")
			.success(function(data, status, headers, config) {
				ng.pedido.id_cor_base = id_cor_base ;
				ng.chosen_cor_base  = [{id:null,nome_cor:'Selecione'}] ;
				ng.chosen_cor_base = ng.chosen_cor_base.concat(data);
				setTimeout(function(){ $("select").trigger("chosen:updated"); }, 300);
				//;
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.chosen_cor_tira  = [] ;
	ng.loadCoresTira = function(){
		ng.chosen_cor_tira  = [{id:null,nome_cor:'Selecione'}] ;
		aj.get(baseUrlApi()+"pedido_personalizado/cores_base?cplSql=tpe.id_empreendimento="+ng.userLogged.id_empreendimento+" AND (tcep.nome_campo IN ('flg_tira_personalizada_masculina','flg_tira_personalizada_feminina') ) AND tvcep.valor_campo = 1 GROUP BY tcp.id ORDER BY tcp.nome_cor ASC")
			.success(function(data, status, headers, config) {
				ng.chosen_cor_tira = ng.chosen_cor_tira.concat(data);
				setTimeout(function(){ $("select").trigger("chosen:updated"); }, 300);
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.abrirCaixa = function(){
   		aj.get(baseUrlApi()+"pedido_venda/abrir_caixa/"+ng.configuracoes.id_caixa_pedidos_venda+"/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.caixa_aberto = data ;
				ng.caixa = data ;
			})
			.error(function(data, status, headers, config) {
				alert(data);
		});
	}

	/*ng.configuracoes = {} ;
	ng.loadConfig = function(){
		var error = 0 ;
		aj.get(baseUrlApi()+"configuracoes/"+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.configuracoes = data ;
				ng.abrirCaixa();
			})
			.error(function(data, status, headers, config) {
				if(status == 404){
					ng.caixa_configurado = false ;
				}
			});
	}*/

	ng.loadMaquinetas = function() {
		ng.maquinetas = [];

		aj.get(baseUrlApi()+"maquinetas/?maq->id_empreendimento="+ng.userLogged.id_empreendimento+"&flg_excluido=0")
			.success(function(data, status, headers, config) {
				ng.maquinetas 			= data.maquinetas;
				ng.paginacao.maquinetas = [] ;
			})
			.error(function(data, status, headers, config) {
				ng.paginacao.maquinetas = [] ;
			});
	}

	ng.bancos = [] ;
	ng.loadBancos = function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 20 : limit;

		ng.bancos = [];

		aj.get(baseUrlApi()+"bancos")
			.success(function(data, status, headers, config) {
				ng.bancos = data.bancos;
			})
			.error(function(data, status, headers, config) {

			});
	}

	ng.loadContas = function() {
		aj.get(baseUrlApi()+"contas_bancarias?cnt->id_tipo_conta[exp]=!=5&id_empreendimento="+ng.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				ng.contas = data.contas;
			})
			.error(function(data, status, headers, config) {
				ng.contas = [] ;
			});
	}

	ng.total_pg             = 0 ;
	ng.troco				= 0;
	ng.pagamentos           = [];
	ng.vlrTotalCompra	    = 0;
	ng.formas_pagamento = [
		{nome:"Dinheiro",id:3},
		{nome:"Cheque",id:2},
		{nome:"Boleto Bancário",id:4},
		{nome:"Cartão de Débito",id:5},
		{nome:"Cartão de Crédito",id:6},
		{nome:"Transferência",id:8}
	  ];
	ng.cheques					=[{id_banco:null,valor:0,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0}];
	ng.boletos					= [{id_banco:null,num_conta_corrente:null,num_cheque:null,status_pagamento:0}];

	ng.telaPagamento = function () {
		var error = 0 ;
    	$('.has-error').tooltip('destroy');
    	$('.has-error').removeClass('has-error');

		dtaVenda	= $("#dtaVenda").val();
		dtaEntrega 	= $("#dtaEntrega").val();

		if(empty(ng.cliente.acao_cliente)){
			$dialogs.notify('Atenção!','<strong>Informe um Cliente Para o Pedido.</strong>');
			return ;
		}

		if(ng.length(ng.carrinhoPedido) == 0){
			$dialogs.notify('Atenção!','<strong>Nenhum Produto Foi Montado.</strong>');
			return ;
		}

		if(empty(dtaVenda)){
			error ++ ;
			$("#label-dta-venda").addClass("has-error");
			 $("#form-dta-venda").addClass("has-error");
				var formControl = $("#form-dta-venda")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Campo obrigatório')
					.attr("data-original-title", 'Campo obrigatório');
				formControl.tooltip('show');
		}

		if(empty(dtaEntrega)){
			error ++ ;
			$("#label-dta-entrega").addClass("has-error");
			$("#form-dta-entrega").addClass("has-error");
				var formControl = $("#form-dta-entrega")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Campo obrigatório')
					.attr("data-original-title", 'Campo obrigatório');
				formControl.tooltip('show');
		}

		if(error > 0)
			return ;
			ng.tela = 'receber_pagamento';
			//ng.receber_pagamento = true ;
			$('html,body').animate({scrollTop: 0},100);
	}

	ng.receberPagamento = function(){
		var produtos = angular.copy(ng.carrinho);
		var venda    = {
							id_usuario:ng.userLogged.id,
							id_cliente:parseInt(ng.cliente.id),
							venda_confirmada:1,
							id_empreendimento:ng.userLogged.id_empreendimento,
							id_deposito : ng.caixa.id_deposito
						};

		venda.id_cliente = isNaN(venda.id_cliente) ? "" : venda.id_cliente;

		$.each(produtos,function(index,value){
			produtos[index].venda_confirmada 	= 1 ;
			produtos[index].valor_produto 		= value.vlr_unitario;
			produtos[index].qtd           		= value.qtd_total;

			if(value.flg_desconto != null && Number(value.valor_desconto) > 0 && !isNaN(Number(value.valor_desconto))){
				produtos[index].desconto_aplicado	= parseInt(value.flg_desconto) != 1 && isNaN(parseInt(value.flg_desconto)) ? 0 : 1 ;
				produtos[index].valor_desconto      = parseInt(value.flg_desconto) == 1 ? value.valor_desconto/100 : 0 ;
			} else {
				produtos[index].desconto_aplicado	= 0 ;
				produtos[index].valor_desconto      = 0 ;
			}
		});

		/*
		* agrupando os produtos de 10 em 10
		*/

		var index_current 	  = 0  ;
		var n_repeat 	  	  = 10 ;
		var repeat_count      = 0  ;
		var produtos_enviar   = [] ;


		$.each(produtos,function(index,obj){
			if(repeat_count >= n_repeat){
					index_current ++ ;
					repeat_count = 0 ;
			}

			if(!(produtos_enviar[index_current] instanceof Array)){
				produtos_enviar[index_current] = [];
			}

			produtos_enviar[index_current].push(obj);
			repeat_count ++ ;
		});
		ng.out_produtos = [] ;
		ng.out_descontos = [] ;
		ng.verificaEstoque(produtos_enviar,0,'receber');
	}

	ng.receber = function(){
		if(!ng.vlrTotalCompra > 0){
			$dialogs.notify('Atenção!','<strong>Não há nenhum valor à receber</strong>');
			return;
		}
		$('#modal-receber').modal('show');
	}

	ng.recebidos = [] ;

	ng.totalPagamento = function(){
		var total = 0 ;
		$.each(ng.recebidos,function(i,v){
			total += Number(v.valor);
		});
		ng.total_pg = Math.round( total * 100) /100 ;
	}

	ng.calculaTroco = function(){
		var troco = 0 ;
		troco = ng.total_pg - ng.vlrTotalCompra;
		if(troco > 0)
			ng.troco = troco;
		else
			ng.troco = 0 ;
	}

	ng.pagamento = {};
	ng.pg_cheques = [] ;
	ng.aplicarRecebimento = function(){
		var restante  = Math.round((ng.vlrTotalCompra - ng.total_pg) * 100) /100 ;
		if((ng.pagamento.valor > restante) && (ng.pagamento.id_forma_pagamento != 3) && (ng.modo_venda == 'pdv')){
			ng.mensagens('alert-warning','<strong>o valor do pagamento utrapassa o valor restante à receber</strong>','.alert-pagamento');
			return;
		}

		if(ng.pagamento.id_forma_pagamento == 7 && ng.pagamento.valor > restante && (ng.modo_venda == 'pdv')){ 
			ng.mensagens('alert-warning','<strong>o valor do pagamento utrapassa o valor restante à receber</strong>','.alert-pagamento');
			return;
		}

		var error = 0 ;
		$('.has-error').tooltip('destroy');
		$('.has-error').removeClass('has-error');
		if(ng.pagamento.id_forma_pagamento ==  undefined || ng.pagamento.id_forma_pagamento ==  ''){
			error ++ ;
			$("#pagamento_forma_pagamento").addClass("has-error");

			var formControl = $("#pagamento_forma_pagamento")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'A escolha da forma de pagamento é obrigatória')
				.attr("data-original-title", 'A escolha da forma de chequ é obrigatória');
			formControl.tooltip();
		}
		if(ng.pagamento.valor ==  undefined || ng.pagamento.valor ==  ''){
			error ++ ;
			$("#pagamento_valor").addClass("has-error");

			var formControl = $("#pagamento_valor")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'O valor é obrigatório')
				.attr("data-original-title", 'O valor é obrigatório');
			formControl.tooltip();
		}

		if((ng.pagamento.id_maquineta ==  undefined || ng.pagamento.id_maquineta ==  '') && (ng.pagamento.id_forma_pagamento == 5 || ng.pagamento.id_forma_pagamento == 6 ) ){
			error ++ ;
			$("#pagamento_maquineta").addClass("has-error");

			var formControl = $("#pagamento_maquineta")
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("title", 'O escolha da maquineta é obrigatório')
				.attr("data-original-title", 'O escolha da maquineta é obrigatório');
			formControl.tooltip();
		}

		if(ng.pagamento.id_forma_pagamento == 2){
			$.each(ng.cheques, function(i,v){
				if($('.cheque_data input').eq(i).val() == "" || $('.cheque_data input').eq(i).val() == undefined ){
					$('.cheque_data').eq(i).addClass("has-error");

					var formControl = $('.cheque_data').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'A data do cheque é obrigatória')
						.attr("data-original-title", 'A data do cheque é obrigatória');
					formControl.tooltip();
					error ++ ;
				}

				if(v.valor_pagamento == "" || v.valor_pagamento == 0 || v.valor_pagamento == undefined ){
					$('.cheque_valor').eq(i).addClass("has-error");

					var formControl = $('.cheque_valor').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O valor do cheque é obrigatório')
						.attr("data-original-title", 'O valor do cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.id_banco == "" || v.id_banco == 0 || v.id_banco == undefined ){
					$('.cheque_banco').eq(i).addClass("has-error");

					var formControl = $('.cheque_banco').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O banco é obrigatório')
						.attr("data-original-title", 'O banco é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_conta_corrente == "" || v.num_conta_corrente == 0 || v.num_conta_corrente == undefined ){
					$('.cheque_cc').eq(i).addClass("has-error");

					var formControl = $('.cheque_cc').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O número da C/C é obrigatório')
						.attr("data-original-title", 'O Num. C/C é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_cheque == "" || v.num_cheque == 0 || v.num_cheque == undefined ){
					$('.cheque_num').eq(i).addClass("has-error");

					var formControl = $('.cheque_num').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O Núm. Cheque é obrigatório')
						.attr("data-original-title", 'O Núm. Cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}
			});

			//ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 4){
			$.each(ng.boletos, function(i,v){
				if($('.boleto_data input').eq(i).val() == "" || $('.boleto_data input').eq(i).val() == undefined ){
					$('.boleto_data').eq(i).addClass("has-error");

					var formControl = $('.boleto_data').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'A data do boleto é obrigatória')
						.attr("data-original-title", 'A data do boleto é obrigatória');
					formControl.tooltip();
					error ++ ;
				}

				if(v.valor_pagamento == "" || v.valor_pagamento == 0 || v.valor_pagamento == undefined ){
					$('.boleto_valor').eq(i).addClass("has-error");

					var formControl = $('.boleto_valor').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O valor do boleto é obrigatório')
						.attr("data-original-title", 'O valor do boleto é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.id_banco == "" || v.id_banco == 0 || v.id_banco == undefined ){
					$('.boleto_banco').eq(i).addClass("has-error");

					var formControl = $('.boleto_banco').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O banco é obrigatório')
						.attr("data-original-title", 'O banco é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.doc_boleto == "" || v.doc_boleto == 0 || v.doc_boleto == undefined ){
					$('.boleto_doc').eq(i).addClass("has-error");

					var formControl = $('.boleto_doc').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O documento do boleto é obrigatório')
						.attr("data-original-title", 'O documento do boleto é obrigatório');
					formControl.tooltip();
					error ++ ;
				}

				if(v.num_boleto == "" || v.num_boleto == 0 || v.num_boleto == undefined ){
					$('.boleto_num').eq(i).addClass("has-error");

					var formControl = $('.boleto_num').eq(i)
						.attr("data-toggle", "tooltip")
						.attr("data-placement", "bottom")
						.attr("title", 'O Núm. Cheque é obrigatório')
						.attr("data-original-title", 'O Núm. Cheque é obrigatório');
					formControl.tooltip();
					error ++ ;
				}
			});

			//ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 8){
			if(empty(ng.pagamento.id_banco)){
				$("#pagamento_id_banco").addClass("has-error");
				var formControl = $("#pagamento_id_banco")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Selecione o banco')
					.attr("data-original-title", 'Selecione o banco');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.agencia_transferencia)){
				$("#pagamento_agencia_transferencia").addClass("has-error");
				var formControl = $("#pagamento_agencia_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o número da agência')
					.attr("data-original-title", 'Informe o número da agência');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.conta_transferencia)){
				$("#pagamento_conta_transferencia").addClass("has-error");
				var formControl = $("#pagamento_conta_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o número da conta')
					.attr("data-original-title", 'Informe o número da conta');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.proprietario_conta_transferencia)){
				$("#proprietario_conta_transferencia").addClass("has-error");
				var formControl = $("#proprietario_conta_transferencia")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe o Proprietário da conta')
					.attr("data-original-title", 'Informe o Proprietário da conta');
				formControl.tooltip();
			}
			if(empty(ng.pagamento.id_conta_bancaria)){
				$("#pagamento_id_conta_transferencia_destino").addClass("has-error");
				var formControl = $("#pagamento_id_conta_transferencia_destino")
					.attr("data-toggle", "tooltip")
					.attr("data-placement", "bottom")
					.attr("title", 'Informe a conta de origem')
					.attr("data-original-title", 'Informe a conta de origem');
				formControl.tooltip();
			}
		}

		if(error > 0){
			return;
		}

		if((ng.pagamento.id_forma_pagamento == 6 || ng.pagamento.id_forma_pagamento == 2 || ng.pagamento.id_forma_pagamento == 4 ) && (ng.pagamento.parcelas ==  undefined || ng.pagamento.parcelas ==  '') ){
			ng.pagamento.parcelas = 1 ;
		}

		var push = true ;

		if(ng.pagamento.id_forma_pagamento == 2){


			$.each(ng.recebidos,function(a,b){
				if(Number(b.id_forma_pagamento) == 2){
					ng.recebidos.splice(a,1);
				}
			});


			var valor_parcelas = ng.pagamento.valor / ng.pagamento.parcelas;
			var count = 0 ;
			ng.pg_cheques = [];
			$.each(ng.cheques,function(index,value){
				value.id_forma_pagamento  	= ng.pagamento.id_forma_pagamento;
				value.valor 				= Math.round(valor_parcelas * 100) / 100;
				value.id_maquineta 			= ng.pagamento.id_maquineta;
				value.parcelas 				= 1 ;
				value.data_pagamento		= formatDate($('.chequeData').eq(count).val());
				//value.valor_pagamento		= valor_parcelas;
				ng.pg_cheques.push(value);
				count ++ ;
			});
		}else if(ng.pagamento.id_forma_pagamento == 4){


			$.each(ng.recebidos,function(a,b){
				if(Number(b.id_forma_pagamento) == 4){
					ng.recebidos.splice(a,1);
				}
			});


			var valor_parcelas = ng.pagamento.valor / ng.pagamento.parcelas;
			var count = 0 ;
			ng.pg_boletos = [];
			$.each(ng.boletos,function(index,value){
				value.id_forma_pagamento  	= ng.pagamento.id_forma_pagamento;
				//value.valor 				= Math.round(valor_parcelas * 100) / 100;
				value.id_maquineta 			= ng.pagamento.id_maquineta;
				value.parcelas 				= 1 ;
				value.data_pagamento		= formatDate($('.boletoData').eq(count).val());
			//value.valor_pagamento		= valor_parcelas;
				ng.pg_boletos.push(value);
				count ++ ;
			});
		}

		if(ng.pagamento.id_forma_pagamento == 3){
			$.each(ng.recebidos,function(x,y){
				if(Number(y.id_forma_pagamento) == 3){
					ng.recebidos[x].valor = ng.recebidos[x].valor + ng.pagamento.valor ;
					push = false ;
				}
			});
		}

		if(push){
			if(ng.pagamento.id_forma_pagamento == 8){
				var item = {
								id_forma_pagamento 				 : ng.pagamento.id_forma_pagamento,
								valor              				 : ng.pagamento.valor,
								id_maquineta	   				 : ng.pagamento.id_maquineta,
								parcelas           				 : ng.pagamento.parcelas,
								id_vale_troca     				 : ng.pagamento.id_vale_troca,
								agencia_transferencia            : ng.pagamento.agencia_transferencia,
								conta_transferencia              : ng.pagamento.conta_transferencia,
								proprietario_conta_transferencia : ng.pagamento.proprietario_conta_transferencia,
								id_conta_transferencia_destino   : ng.pagamento.id_conta_transferencia_destino,
								id_banco                         : ng.pagamento.id_banco
						   };
			}else{
				var item = {
								id_forma_pagamento 				 : ng.pagamento.id_forma_pagamento,
								valor              				 : ng.pagamento.valor,
								id_maquineta	   				 : ng.pagamento.id_maquineta,
								parcelas           				 : ng.pagamento.parcelas,
								id_vale_troca     				 : ng.pagamento.id_vale_troca
						   };
			}

			$.each(ng.formas_pagamento,function(i,v){
				if(v.id == ng.pagamento.id_forma_pagamento){
					item.forma_pagamento = v.nome ;
					return;
				}
			});
			ng.recebidos.push(item);
		}
		ng.totalPagamento();
		ng.calculaTroco();
		ng.pagamento = {} ;
	}

	ng.deleteRecebidos = function(index){
		ng.recebidos.splice(index,1);
		ng.totalPagamento();
		ng.calculaTroco();
	}



	ng.cancelarModal = function(id){
		$('#'+id).modal('hide');
	}

	ng.cancelarPagamento = function(){
		ng.tela = 'pedido';
		ng.recebidos = [];
		ng.totalPagamento();
		ng.calculaTroco();
	}



	var nParcelasAntCheque = 1 ;
	var nParcelasAntBoleto = 1 ;
	ng.pagamento.parcelas  = 1 ;

	ng.pushCheques = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.parcelas = empty(ng.pagamento.parcelas) ? 1 : ng.pagamento.parcelas ;
			ng.pagamento.parcelas = ng.pagamento.parcelas == "" ?  1 : ng.pagamento.parcelas ;
			if(ng.pagamento.parcelas > nParcelasAntCheque){
				var repeat = parseInt(ng.pagamento.parcelas) - parseInt(nParcelasAntCheque) ;
				while(repeat > 0){
					var item = {id_banco:null,valor_pagamento:0,num_conta_corrente:null,num_cheque:null,flg_cheque_predatado:0};
					ng.cheques.push(item);
					repeat -- ;
				}
			}else if(ng.pagamento.parcelas < nParcelasAntCheque){
				var repeat = parseInt(nParcelasAntCheque) - parseInt(ng.pagamento.parcelas) ;
				while(repeat > 0){
					var index = ng.cheques.length - 1;
					ng.cheques.splice(index,1);
					repeat -- ;
				}
			}
			nParcelasAntCheque = ng.pagamento.parcelas;
			ng.calTotalCheque();
			setTimeout(function(){ ng.loadDatapicker();}, 1000);
		}else if(ng.pagamento.id_forma_pagamento == 4){
			ng.pagamento.parcelas = empty(ng.pagamento.parcelas) ? 1 : ng.pagamento.parcelas ;
			ng.pagamento.parcelas = ng.pagamento.parcelas == "" ?  1 : ng.pagamento.parcelas ;
			if(ng.pagamento.parcelas > nParcelasAntBoleto){
				var repeat = parseInt(ng.pagamento.parcelas) - parseInt(nParcelasAntBoleto) ;
				while(repeat > 0){
					var item = {id_banco:null,valor_pagamento:0,num_conta_corrente:null,num_cheque:null,status_pagamento:0};
					ng.boletos.push(item);
					repeat -- ;
				}
			}else if(ng.pagamento.parcelas < nParcelasAntBoleto){
				var repeat = parseInt(nParcelasAntBoleto) - parseInt(ng.pagamento.parcelas) ;
				while(repeat > 0){
					var index = ng.cheques.length - 1;
					ng.boletos.splice(index,1);
					repeat -- ;
				}
			}
			nParcelasAntBoleto = ng.pagamento.parcelas;
			ng.calTotalBoleto();
			setTimeout(function(){ ng.loadDatapicker();}, 1000);
		}
	}


	ng.loadDatapicker = function(){
		$(".chequeData").datepicker();
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});

		$(".boletoData").datepicker();
		$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
		$(".dropdown-menu").mouseleave(function(){$('.dropdown-menu').hide();$('input.datepicker').blur()});
	}

	ng.selectChange = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.valor = 0 ;
			ng.pagamento.parcelas = ng.cheques.length  > 0 ? ng.cheques.length : 1 ;
			if(ng.cheques.length > 0)
				ng.calTotalCheque();
		}else if(ng.pagamento.id_forma_pagamento == 6){
			ng.pagamento.parcelas = 1 ;
		}else if(ng.pagamento.id_forma_pagamento == 4){
			ng.pagamento.valor = 0 ;
			ng.pagamento.parcelas = ng.boletos.length  > 0 ? ng.boletos.length : 1 ;
			if(ng.boletos.length > 0)
				ng.calTotalBoleto();
		}	

		ng.loadDatapicker();
	}

	ng.delItemCheque = function($index){
		ng.cheques.splice($index,1);
		ng.pagamento.parcelas = ng.cheques.length ;
		nParcelasAnt  = ng.pagamento.parcelas
	}

	ng.focusData  = function($index){
		if(ng.pagamento.id_forma_pagamento == 2)
			$(".chequeData").eq($index).trigger("focus");
		if(ng.pagamento.id_forma_pagamento == 4)
			$(".boletoData").eq($index).trigger("focus");
	}


	ng.calTotalCheque = function(){
		var valor = 0 ;
		$.each(ng.cheques,function(i,v){
			valor += Number(v.valor_pagamento);
		});

		ng.pagamento.valor = valor;

	}

	ng.calTotalBoleto = function(){
		var valor = 0 ;
		$.each(ng.boletos,function(i,v){
			valor += Number(v.valor_pagamento);
		});

		ng.pagamento.valor = valor;

	}

	ng.qtdCheque = function(){
		if(ng.pagamento.id_forma_pagamento == 2){
			ng.pagamento.parcelas = ng.cheques.length  > 0 ? ng.cheques.length : 1 ;
		}

	}

	ng.pagamentoFulso = function (){
		ng.receber_pagamento = true ;
		ng.venda_aberta 	 = true ;
		ng.pagamento_fulso   = true ;
	}

	ng.showVlrReal = function(){
		ng.show_vlr_real = !ng.show_vlr_real ;
	}
	ng.view = {desconto_all:0} ;
	ng.aplicarDescontoAll = function(){
		$(".has-error").tooltip('destroy');
		$(".has-error").removeClass('has-error');
		if(empty(ng.view.desconto_all)){
			$("#desconto-all").addClass("has-error");
			var formControl = $('#desconto-all')
				.attr("data-toggle", "tooltip")
				.attr("data-placement", "bottom")
				.attr("data-original-title", 'Infome o Desconto Desejado');
			formControl.tooltip();
			return ;
		}

		$.each(ng.chinelos_gerados,function(i,chinelo){
				chinelo.valor_desconto_cal = ng.view.desconto_all ;		
		});
		ng.view.desconto_all = 0 ;
		ng.calcSubTotal('desconto');
	}

	ng.loadComoEncontrou = function () {
		ng.comoencontrou = [];

		aj.get(baseUrlApi()+"comoencontrou")
		.success(function(data, status, headers, config) {
			data.push({id:"outros",nome:"Outros"});
			ng.comoencontrou = data;
		})
		.error(function(data, status, headers, config) {

		});
	}

	ng.selCliente = function(){
		var offset = 0  ;
    	var limit  =  10 ;;

			ng.loadCliente(offset,limit);
			$("#list_clientes").modal("show");
	}

	ng.addCliente = function(item){
		ng.cliente = item;
		ng.cliente.acao_cliente = "update"
		$("#list_clientes").modal("hide");
		aj.get(baseUrlApi()+"usuarios/saldodevedor/"+ ng.userLogged.id_empreendimento+"?usu->id="+item.id)
			.success(function(data, status, headers, config) {
				ng.cliente.vlr_saldo_devedor = data.vlr_saldo_devedor;
			})
			.error(function(data, status, headers, config) {
				console.log('erro ao consultar saldo do cliente');
			});
	}

	ng.loadCliente= function(offset,limit) {
		offset = offset == null ? 0  : offset;
    	limit  = limit  == null ? 10 : limit;
		ng.clientes = [];
		query_string = "?(tue->id_empreendimento[exp]=="+ng.userLogged.id_empreendimento+"&usu->id[exp]= NOT IN("+ng.configuracoes.id_cliente_movimentacao_caixa+","+ng.configuracoes.id_usuario_venda_vitrine+"))";

		if(ng.busca.clientes != ""){
			query_string += "&"+$.param({'(usu->nome':{exp:"like'%"+ng.busca.clientes+"%' OR usu.apelido LIKE '%"+ng.busca.clientes+"%')"}});
		}

		aj.get(baseUrlApi()+"usuarios/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				$.each(data.usuarios,function(i,item){
					ng.clientes.push(item);
				});
				ng.paginacao_clientes = [];
				$.each(data.paginacao,function(i,item){
					ng.paginacao_clientes.push(item);
				});
			})
			.error(function(data, status, headers, config) {
				ng.clientes = false ;
			});
	}

	ng.currentAcessorios = [] ;
	ng.deleteItemAcessorio  = function(index,event,indexPopOver,tipo){
		ng.currentAcessorios.splice(index,1);
		element = $('#popover-acessorio-'+tipo+'-'+indexPopOver);
		element.attr('data-popover-visible','0');
		ng.popoverAcessorios(ng.currentAcessorios,null,indexPopOver,tipo,element);
	}
	ng.addQtdAcessorio = function(index,event){
		var element = $(event.target);
		var qtd     = element.val();
		if(empty(qtd)){
			qtd = 1 ;
		}
		ng.currentAcessorios[index].qtd = Number(qtd);
	}
	ng.qtdtotalAcessorios = function(acessorios){
		var total = 0;
		$.each(acessorios,function(i,x){
			var qtd = empty(x.qtd) ? 1 : Number(x.qtd) ;
			total += qtd;
		});

		return total ;
	}
	ng.popoverAcessorios = function(acessorios,event,indexPopOver,tipo,element){

		var element = element == null ? $(event.target) : element ;
		if(!(element.is(':button')))
			element = $(element.parent('button'));

		if($(element).attr('data-popover-visible') == '1'){
			element.popover('hide').attr('data-popover-visible','0');
			return ;
		}

	   $('[data-popover-visible="1"]').popover('hide').attr('data-popover-visible','0');

	   ng.currentAcessorios = acessorios ;
			
	   element.popover('destroy').popover({
            title: 'Acessórios',
            placement: 'right',
            content: '<strong>loading ... </strong>',
            html: true,
            container: 'body',
            trigger  :'manual',
       }).popover('show').attr('data-popover-visible','1');

       
		if(!empty(acessorios) && acessorios.length > 0){
			 var bodyTbl = '<tbody>';
			 $.each(acessorios,function(i,item){
			 	bodyTbl += '<tr>'
			 					+'<td>'+item.id_produto+'</td>'
                       	  		+'<td>'+item.nome_produto+'</td>'
                         		+'<td class="text-center">'
                             		+'<input style="width:40px" ng-keyUp="addQtdAcessorio('+i+',$event)" onKeyPress="return SomenteNumero(event);" value="'+item.qtd+'" type="text" class="form-control input-xs text-center" />'
                          		+'</td>'
                          		+'<td align="center">'
                            		+'<i class="fa fa-trash-o" style="color:red;cursor:pointer" ng-click="deleteItemAcessorio('+i+',$event,'+indexPopOver+',\''+tipo+'\')"	data-toggle="tooltip"  class="btn btn-xs btn-danger" title="Excluir"></i>'
                          		+'</td>'
                          	+'</tr>';
			 });
			bodyTbl += '</tbody>';             	
		}else{
			 var bodyTbl = '<tr>'
		        				 +'<td colspan="4" >'
		                            +'Não existe acessorios para este item'
		                         +'</td>'
	                     	 +'</tr>';
		}	

	 	 var template = '<table class="table table-bordered table-condensed table-striped table-hover" width="300px">'
						+'<thead>'
							+'<tr>'
		                        +'<th>#</th>'
		                        +'<th class="text-center">Nome</th>'
		                        +'<th class="text-center">Qtd.</th>'
		                        +'<th class="text-center"></th>'
		                    +'</tr>'
                	  	+'</thead>'
						+bodyTbl
					+'<table>';

		 element.popover('destroy').popover({
            title: "Acessórios",
            placement: 'right',
            content: function () {return $compile(template)($scope); },
            html: true,
            container: 'body',
            trigger:'manual'
        }).popover('show').attr('data-popover-visible','1');

		$('[data-toggle="tooltip"]').tooltip();


	}

	ng.pedido.coresEstampa = [] ;
	ng.openModalCoresEstampa = function(){
		ng.modal('show','modal-cor-estampa');
		ng.busca.coresEstampa = "" ;
		ng.loadCoresEstampa(0,10);
	}

	ng.loadCoresEstampa = function(offset,limit) {
		ng.coresEstampa = [] ;
		offset = offset == null ? 0  : offset ;
		limit  = limit  == null ? 10 : limit ;
		var query_string = "?tcpe->id_empreendimento="+ng.userLogged.id_empreendimento;
		query_string += empty(ng.busca.coresEstampa) ? '' : "&"+$.param({'(tcp->nome_cor':{exp:"LIKE '%"+ng.busca.coresEstampa+"%'"}})+" OR tcp.id = '"+ng.busca.coresEstampa+"')";
		aj.get(baseUrlApi()+"cores_produto/"+offset+"/"+limit+query_string)
			.success(function(data, status, headers, config) {
				ng.coresEstampa = data;
			})
			.error(function(data, status, headers, config) {
				ng.coresEstampa = null;
			});
	}	
	ng.addCorEstampa = function(item){
		item = {
			id_cor    : item.id,
			dsc_local : item.dsc_local,
			nome_cor      : item.nome_cor
		}
		ng.pedido.coresEstampa.push(item);
	}
	ng.empty = function(vlr){
		return empty(vlr);
	}
	ng.existsCorEstampa = function(item){
		var saida = false ;
		$.each(ng.pedido.coresEstampa,function(i,x){
			if(Number(x.id_cor) == Number(item.id)){
				saida = true;
				return ;
			}
		});
		return saida ;
	}
	ng.deleteItemCorEstampa = function(index){
		ng.pedido.coresEstampa.splice(index,1);
	}

	ng.loadPedidoEdit = function(id_pedido){
		$('#modal-load-pedido').modal({ backdrop: 'static',keyboard: false});
		aj.get(baseUrlApi()+"pedido/grade_edit/"+id_pedido)
			.success(function(data, status, headers, config) {
				ng.editing = true ;
				ng.addCliente(data.cliente);
				$('#dtaVenda').val(formatDateBR(data.pedido.dta_venda));
				$('#dtaEntrega').val(formatDateBR(data.pedido.dta_entrega));
				ng.pedido.canal_venda = data.pedido.canal_venda ;
				ng.pedido.observacao = data.pedido.observacao ;
				ng.pedido.id = data.pedido.id;
				ng.pedido.id_pedido_gerado = data.pedido.id_pedido_gerado ;
				$.each(data.grade,function(i,grade){
					ng.gradeInfantil = [];
					ng.gradeAdulto   = [];
					var arr = i.replace("brinde-","").split("-");
					ng.pedido.id_cor_base = arr[0] ; 
					ng.pedido.id_cor_tira_feminina = arr[1] ;
					ng.pedido.id_cor_tira_masculina = arr[2] ;
					ng.pedido.flg_brinde = i.search('brinde') == 0 ? 1 : 0 ;
					ng.pedido.coresEstampa = [] ;
					var base, tira , indexMas, indexFem ;
					$.each(grade,function(x,item){
						if(!empty(item.tipo_base)) ng.pedido.tipo_base = item.tipo_base  ;
						if(!empty(item.modelo_base)) ng.pedido.modelo_base = item.modelo_base ;
						if(!empty(item.modeloEstampa)) ng.pedido.modeloEstampa = item.modeloEstampa ;
						if( ng.pedido.coresEstampa.length == 0 ) ng.pedido.coresEstampa = item.coresEstampa == undefined ? [] :  item.coresEstampa ;
						base  = ng.getbase(item.fem_itens); 
						tira  = ng.getTira(item.fem_itens,'tira_feminina');
						indexFem = "fem-"+item.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor ;
						tira  = ng.getTira(item.mas_itens,'tira_masculina');
						indexMas = "mas-"+item.id_tamanho+"-"+base.id_cor+"-"+tira.id_cor ;
						
						if( !(ng.carrinhoPedido[indexMas] == undefined )){
							item.mas_qtd =  ng.carrinhoPedido[indexMas].qtd ;
						}

						if( !(ng.carrinhoPedido[indexFem] == undefined )){
							item.fem_qtd =  ng.carrinhoPedido[indexFem].qtd ;
						}
						item.perc_desconto_compra = 0 ;
						if(item.nome_tamanho <= '31/32'){
							if(item.fem_valid || item.mas_valid)
								ng.gradeInfantil.push(item);
						}else{
							if(item.fem_valid || item.mas_valid)
								ng.gradeAdulto.push(item);
						}
					});
					ng.montarchinelos(true);
				});
				ng.gradeInfantil = [] ;
				ng.gradeAdulto   = [] ;
				ng.pedido.coresEstampa = [] ;
				ng.pedido.flg_brinde = 0 ;
				$('#modal-load-pedido').modal('hide');
			})
			.error(function(data, status, headers, config) {
				$('#modal-load-pedido').modal('hide');
				$dialogs.notify('Atenção!','<strong>Deculpe, Não Foi Possivel Carregar o Pedido.</strong>');
				return;
			});
	}

	ng.btnInsertCliente = function(){
		ng.cliente = {acao_cliente:'insert',indicacao:0} ;
	}

	ng.calcularDesconto = function(){
			ng.pedido.desconto = $.isNumeric(ng.pedido.desconto) && Number(ng.pedido.desconto) > 0 ? ng.pedido.desconto : 0 ;
			var total = 0;
			$.each(ng.carrinhoPedido,function(i,v){
				ng.carrinhoPedido[i].vlr_init = empty(v.vlr_init) ? Number(v.valor_real_item) : Number(v.vlr_init) ;
				total += v.qtd * ng.carrinhoPedido[i].vlr_init ;
			});
			var prc_dsc = (ng.pedido.desconto * 100) / total ;
			console.log(prc_dsc) ;
			$.each(ng.carrinhoPedido,function(i,v){
				ng.carrinhoPedido[i].vlr_init = empty(v.vlr_init) ? Number(v.valor_real_item) : Number(v.vlr_init) ;
				ng.carrinhoPedido[i].desconto_aplicado = ng.pedido.desconto > 0 ? 1 : 0 ;
				ng.carrinhoPedido[i].valor_desconto = prc_dsc/100 ;
				ng.carrinhoPedido[i].valor_real_item =  numberFormat(ng.carrinhoPedido[i].vlr_init - (ng.carrinhoPedido[i].vlr_init * ng.carrinhoPedido[i].valor_desconto),2,'.',',') ;
			});		
	}

	ng.openModalModelosEstampas = function(){
		ng.modal('show','modal-modelos-estampas');
		ng.busca.modeosEstampas = "" ;
		ng.loadModeloEstampas(0,10);
	}

	ng.modeloEstampas = {itens:[]} ;
	ng.loadModeloEstampas = function(offset,limit) {
		ng.modeloEstampas = {itens:[]} ;
		offset = offset == null ? 0  : offset ;
		limit  = limit  == null ? 10 : limit ;
		var query_string = "";
		query_string += empty(ng.busca.modeloEstampa) ? '' : "?"+$.param({'(tp->mome':{exp:"LIKE '%"+ng.busca.modeloEstampa+"%'"}})+" OR tp.id = '"+ng.busca.modeloEstampa+"')";
		aj.get(baseUrlApi()+"pedido_personalizado/getEstampas/"+ng.userLogged.id_empreendimento+"/"+offset+"/"+limit+query_string)
			.success(function(data, status, headers, config) {
				ng.modeloEstampas.itens = data.estampas;
				ng.modeloEstampas.paginacao = data.paginacao ;
			})
			.error(function(data, status, headers, config) {
				ng.modeloEstampas.itens = null;
				ng.modeloEstampas.paginacao = null ;
			});
	}	
	ng.addModeloEstampa = function(item){
		ng.pedido.modeloEstampa = angular.copy(item);
		ng.pedido.modeloEstampa.qtd = 1 ;
		ng.pedido.modeloEstampa.tipo = 'modelo_estampa' ;
		ng.modal('hide','modal-modelos-estampas');

	}

	ng.setTipoBase = function(){
		ng.gradeAdulto = [];
		ng.gradeInfantil = [] ;
		ng.pedido.id_cor_tira_feminina = null;
		ng.pedido.id_cor_tira_masculina = null ;
		if( ng.pedido.tipo_base == 'quadrada' && ng.pedido.modelo_base =='linha_lika_shoes') ng.loadCoresBase('flg_base') ;
		else if( ng.pedido.tipo_base == 'redonda' && ng.pedido.modelo_base =='linha_lika_shoes') ng.loadCoresBase('flg_base_linha_redonda') ;
		else if( ng.pedido.tipo_base == 'quadrada' && ng.pedido.modelo_base =='linha_personalizada') ng.loadCoresBase('flg_base_personalizada_quadrada') ;
		else if( ng.pedido.tipo_base == 'redonda' && ng.pedido.modelo_base =='linha_personalizada') ng.loadCoresBase('flg_base_personalizada') ;
	}

	ng.setTipoBaseEdit = function(){
		if( ng.pedido.tipo_base == 'quadrada' && ng.pedido.modelo_base =='linha_lika_shoes') ng.loadCoresBaseEdit('flg_base') ;
		else if( ng.pedido.tipo_base == 'redonda' && ng.pedido.modelo_base =='linha_lika_shoes') ng.loadCoresBaseEdit('flg_base_linha_redonda') ;
		else if( ng.pedido.tipo_base == 'quadrada' && ng.pedido.modelo_base =='linha_personalizada') ng.loadCoresBaseEdit('flg_base_personalizada_quadrada') ;
		else if( ng.pedido.tipo_base == 'redonda' && ng.pedido.modelo_base =='linha_personalizada') ng.loadCoresBaseEdit('flg_base_personalizada') ;
	}

	ng.formatPreco = function(){
		if(!empty(ng.configuracoes.valores_chinelos) && typeof parseJSON(ng.configuracoes.valores_chinelos) == 'object' ){
			var valoresChinelos = parseJSON(ng.configuracoes.valores_chinelos);
			 ng.chinelosInfantis = {tamanhos:[],precos:{}};
			 ng.chinelosAdultos = {tamanhos:[],precos:{}};
			 ng.valoresAdicionais = {} ;
			$.each(valoresChinelos,function(i,v){
				if(i == 'adulto'){
					if(!empty(v.tamanhos.de)) ng.chinelosAdultos.tamanhos[0] = v.tamanhos.de;
					if(!empty(v.tamanhos.ate)) ng.chinelosAdultos.tamanhos[1] = v.tamanhos.ate;
					$.each(v.faixas,function(a,b){
						var index ;
						if(!empty(b.de)) index = ""+b.de;
						if(!empty(b.ate)) index += "-"+b.ate;
						ng.chinelosAdultos.precos[index] = Number(b.valor);
					});
				}
				else if(i == 'infantil'){
					if(!empty(v.tamanhos.de)) ng.chinelosInfantis.tamanhos[0] = v.tamanhos.de;
					if(!empty(v.tamanhos.ate)) ng.chinelosInfantis.tamanhos[1] = v.tamanhos.ate;
					$.each(v.faixas,function(a,b){
						var index ;
						if(!empty(b.de)) index = ""+b.de;
						if(!empty(b.ate)) index += "-"+b.ate;
						ng.chinelosInfantis.precos[index] = Number(b.valor);
					});
				}
				else if(i=='adicionais'){
					$.each(v,function(y,z){
						ng.valoresAdicionais[y] = Number(z);
					});
				}

			});
		}else ng.valoresChinelos = false ;
	}
		

	if( !(params.id_pedido == undefined) )
		ng.loadPedidoEdit(params.id_pedido);

	ng.loadCoresTira();
	//ng.loadConfig();
	ng.loadMaquinetas();
	ng.loadBancos();
	ng.loadContas();
	ng.loadComoEncontrou();
	ng.formatPreco();
	if(empty(ng.configuracoes)) ng.caixa_configurado = false ;
	else ng.abrirCaixa();
});

app.directive('bsTooltip', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            $timeout(function () {
                	  element.find("[data-toggle=tooltip]").tooltip();
            });
        }
    }
});

app.directive('bsPopover', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            $timeout(function () {
                	  element.find("[data-toggle=popover]").popover();
            });
        }
    }
});