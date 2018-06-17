app.controller('CategoriasController', function($scope, $http, $window, $dialogs, UserService,PrestaShop,Ezcommerce){
	var ng = $scope
		aj = $http;

	ng.baseUrl 		= baseUrl();
	ng.userLogged 	= UserService.getUserLogado();
	ng.categorias	= [];
	ng.paginacao = { itens: [] } ;

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

	ng.mensagens = function(classe , msg){
		$('.alert-sistema').fadeIn().addClass(classe).html(msg);

		setTimeout(function(){
			$('.alert-sistema').fadeOut('slow');
		},5000);
	}

	ng.reset = function() {
		ng.categoria = {};
		ng.empreendimentosAssociados = [{ id : ng.userLogged.id_empreendimento,nome_empreendimento:ng.userLogged.nome_empreendimento }];
		ng.editing = false;
		$($(".has-error").find(".form-control")).tooltip('destroy');
		$(".has-error").removeClass("has-error");
	}

	ng.busca = { text: "", empreendimento: "" };
	ng.empreendimentosAssociados = [{ id : ng.userLogged.id_empreendimento,nome_empreendimento:ng.userLogged.nome_empreendimento,flg_visivel:1 }];
	ng.showEmpreendimentos = function() {
		$('#list_empreendimentos').modal('show');
		ng.loadAllEmpreendimentos(0,10);
	}

	ng.loadAllEmpreendimentos = function(offset, limit) {
		offset = offset == null ? 0  : offset;
		limit  = limit  == null ? 20 : limit;

		var query_string = "?id_usuario="+ng.userLogged.id;
		if(ng.busca.empreendimento != ""){
			query_string += "&" +$.param({nome_empreendimento:{exp:"like'%"+ng.busca.empreendimento+"%'"}});
		}

		ng.empreendimentos = [];
		aj.get(baseUrlApi()+"empreendimentos/"+offset+"/"+limit+"/"+query_string)
			.success(function(data, status, headers, config) {
				ng.empreendimentos = data.empreendimentos;
				ng.paginacao.empreendimentos = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.empreendimentos = [];
			});
	}

	ng.loadEmpreendimentosByCategoria = function() {
		aj.get(baseUrlApi()+"empreendimentos/ref/categoria/"+ng.categoria.id)
			.success(function(data, status, headers, config) {
				ng.empreendimentosAssociados = [];
				ng.empreendimentosAssociados = data;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.empreendimentos = [];
			});
	}

	ng.empreendimentoIsSelected = function(item){
		var r = false ;
		$.each(ng.empreendimentosAssociados,function(i,v){
			if(Number(item.id)==Number(v.id)){
				r = true ;
				return;
			}
		});
		return r ;
	}

	ng.delEmpreendimento = function(item) {
		ng.empreendimentosAssociados.pop(item);
	}

	ng.addEmpreendimento = function(item) {
		if(ng.empreendimentosAssociados == null)
			ng.empreendimentosAssociados = [];

		var s = true;

		$.each(ng.empreendimentosAssociados, function(i, emp) {
			if(emp.id == item.id)
				s = false;
		});

		if(s) {
			ng.empreendimentosAssociados.push(item);
		}
		else {
			$('#list_empreendimentos').modal('hide');
			ng.mensagens('alert-danger','<strong>Este empreendimento já foi adicionado a listagem</strong>');
		}
	}

	ng.paginacao = { itens: [] } ;
	ng.resetFilter = function() {
		ng.busca.text = "" ;
		ng.reset();
		ng.load(0,10);
	}

	/*ng.load = function(offset, limit) {
		offset = offset == null ? 0 : offset ;
		limit  = limit  == null ? 10 : limit ;

		var query_string = "?tce->id_empreendimento="+ng.userLogged.id_empreendimento;

		if(ng.busca.text != "")
			query_string += "&("+$.param({descricao_categoria:{exp:"like '%"+ng.busca.text+"%' OR id = '"+ng.busca.text+"'"}})+")";

		aj.get(baseUrlApi()+"categorias/" + offset + "/" + limit + query_string)
			.success(function(data, status, headers, config) {
				ng.categorias = data.categorias;
				ng.paginacao.itens = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				if(status == 404)
					ng.categorias = [];
			});
	}*/

	ng.salvar = function() {
		var url = 'categoria';
		var itemPost = {};

		if(ng.empreendimentosAssociados == null || ng.empreendimentosAssociados.length == 0) {
			ng.mensagens('alert-danger','<strong>Você deve selecionar ao menos um empreendimento</strong>');
			btn.button('reset');
			return false;
		}

		if(ng.categoria.id != null && ng.categoria.id > 0) {
			itemPost.id = ng.categoria.id;
			url += '/update';
		}

		itemPost.pth_thumbnail			= ng.categoria.pth_thumbnail;
		itemPost.thumbnail				= ng.categoria.thumbnail;
		itemPost.banner					= ng.categoria.banner;
		itemPost.pth_banner				= ng.categoria.pth_banner;
		itemPost.id_empreendimento 		= ng.userLogged.id_empreendimento;
		itemPost.descricao_categoria 	= ng.categoria.descricao_categoria;
		itemPost.hex_cor_box 			= ng.categoria.hex_cor_box;
		itemPost.hex_cor_letra 			= ng.categoria.hex_cor_letra;
		itemPost.empreendimentos 		= ng.empreendimentosAssociados;
		itemPost.id_pai 				= empty(ng.categoria.id_pai) ? null : ng.categoria.id_pai ;

		aj.post(baseUrlApi()+url, {data: JSON.stringify(itemPost)})
			.success(function(data, status, headers, config) {
				if( empty(ng.categoria.id) )
					itemPost.id = data.categoria.id
				PrestaShop.send('post',baseUrlApi()+"prestashop/categoria",itemPost);
				Ezcommerce.send('post',baseUrlApi()+"ezcommerce/catalogows/categoria/"+ng.userLogged.id_empreendimento,itemPost);
				ng.mensagens('alert-success','<strong>Categoria salvo com sucesso!</strong>');
				ng.showBoxNovo();
				ng.reset();
				ng.load();
			})
			.error(function(data, status, headers, config) {
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
					});
				}
			});
	}

	ng.editar = function(item) {
		ng.categoria = angular.copy(item);

		if(!empty(ng.categoria.pth_thumbnail)){
			ng.categoria.thumbnail = {
				path: ng.categoria.pth_thumbnail.substring(ng.categoria.pth_thumbnail.indexOf('assets'), ng.categoria.pth_thumbnail.length),
				updated: false
			};
		}

		if(!empty(ng.categoria.pth_banner)){
			ng.categoria.banner = {
				path: ng.categoria.pth_banner.substring(ng.categoria.pth_banner.indexOf('assets'), ng.categoria.pth_banner.length),
				updated: false
			};
		}

		ng.categoria.id_pai = empty(ng.categoria.id_pai) ? null : ""+ng.categoria.id_pai;
		ng.showBoxNovo(true);
		ng.loadEmpreendimentosByCategoria();
	}

	ng.delete = function(item){
		dlg = $dialogs.confirm('Atenção!!!' ,'<strong>Tem certeza que deseja excluir este categoria?</strong>');

		dlg.result.then(function(btn){
			aj.get(baseUrlApi()+"categoria/delete/"+item.id)
				.success(function(data, status, headers, config) {
					PrestaShop.send('delete',baseUrlApi()+"prestashop/categoria/"+item.id+"/"+ng.userLogged.id_empreendimento);
					ng.mensagens('alert-success','<strong>Categoria excluido com sucesso</strong>');
					ng.reset();
					ng.load();
				})
				.error(defaulErrorHandler);
		}, undefined);
	}

	ng.integracao = function(){
		
	}

	ng.load= function() {
		var query_string = "?cat->flg_excluido=0&tce->id_empreendimento="+ng.userLogged.id_empreendimento;
		aj.get(baseUrlApi()+"categorias/treeview"+ query_string)
			.success(function(data, status, headers, config) {
				ng.categorias = ng.montarTabelaCategria([],data,0);
				ng.categoriasChosen = [{id:null,descricao_categoria:'Selecione'}];
				ng.categoriasChosen  = ng.categoriasChosen.concat(ng.categorias);
				setTimeout(function(){
					$("select").trigger("chosen:updated");
				},300);
			})
			.error(function(data, status, headers, config) {
				
			});
	}

	ng.montarTabelaCategria = function(saida,dados,nivel){
		$.each(dados,function(i,item){
			var aux = nivel ;
			var filhos = item.filhos ;
			delete item.filhos ;
			item.nivel="";
			if(aux > 0)
			for(i=0;i<nivel;i++){ item.nivel+= "&nbsp;&nbsp;&nbsp;"}
			saida.push(item);
			if(!empty(filhos)){
				aux ++ ;
				saida = ng.montarTabelaCategria(saida,filhos,aux);
			}
		});
		return saida ;
	}



	function defaulErrorHandler(data, status, headers, config) {
		ng.mensagens('alert-danger','<strong>'+ data +'</strong>');
	}

	ng.load(0,10);

	setTimeout(function(){
	  $('.btn-upload input[type="file"]').on('change', function(){
		var file = this.files[0]; // get selected file
		var reader = new FileReader();

		ng.fileModel = $(this).data().model; // get attribute model name

		if(empty(ng.categoria)){
			ng.categoria = {};
		}

		if(empty(ng.categoria[ng.fileModel])) // validate if is empty
			ng.categoria[ng.fileModel] = {}; // create as object

		// detect file type
		var type = file.type.substring(file.type.indexOf('/')+1, file.type.length);
		if(empty(type)){
			type = file.name.substring((file.name.lastIndexOf('.')+1), file.name.length);
		}
		
		ng.categoria[ng.fileModel].name = file.name; // file name
		ng.categoria[ng.fileModel].type = type; // file type
		ng.categoria[ng.fileModel].size = (file.size / 1024); // file size

		// adjust file size string name
		if(ng.categoria[ng.fileModel].size < 1024)
			ng.categoria[ng.fileModel].size = numberFormat(ng.categoria[ng.fileModel].size, 2, ',', '.') + ' KB';
		else if(ng.categoria[ng.fileModel].size > 1024)
			ng.categoria[ng.fileModel].size = numberFormat(ng.categoria[ng.fileModel].size, 2, ',', '.') + ' MB';

		// after loading file...
		reader.onload = function (e) {
			ng.categoria[ng.fileModel].path = e.target.result; // get base64 string of file
			ng.categoria[ng.fileModel].updated = true;
		  	setTimeout(function(){
				ng.$apply(); // apply changes in the screen
			},1);
		}

		if(!empty(file))
			reader.readAsDataURL(file);
		});
	}, 10);
});
