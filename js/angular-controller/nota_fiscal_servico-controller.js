app.controller('NotaFiscalServicoController', function($scope, $http, $window, $dialogs, UserService, ConfigService, AsyncAjaxSrvc){
	$scope.userLogged = UserService.getUserLogado();
	$scope.busca = { text: "" }
	$scope.paginacao = { regras_tributacao: [] };
	$scope.nf = { outros: {data_emissao: moment().format('DD/MM/YYYY HH:mm:ss')} };

	$('#sizeToggle').trigger("click");

	$scope.showModal = function(modal) {
		$('#list_'+modal).modal('show');
		$scope.loadRegrasTributacao(0,10);
	}

	$scope.loadRegrasTributacao = function(offset,limit) {
		offset = empty(offset) ? 0 : offset ;
		limit = empty(limit) ? 10 :  limit ;
		$scope.regrasTributacao = null ;

		var queryString = "?cplSql= WHERE trs.cod_empreendimento = "+ $scope.userLogged.id_empreendimento +" AND trs.flg_excluido = 0";

		if(!empty($scope.busca.text)){
			var busca_like = $scope.busca.text.replace(/\s/g, '%');
			queryString += " AND (mi.nome LIKE '%"+busca_like+"%' OR te.uf LIKE '%"+ busca_like +"%' OR te.nome LIKE '%"+ busca_like +"%')";
		}

		$http.get(baseUrlApi()+"regras_servico/"+offset+"/"+limit+encodeURI(queryString) )
			.success(function(data, status, headers, config) {
				$.each(data.regras, function(i,item){
					data.regras[i].prc_tributos = 0;

					if(item.flg_retem_cofins === 1)
						data.regras[i].prc_tributos += item.prc_retencao_cofins;

					if(item.flg_retem_csll === 1)
						data.regras[i].prc_tributos += item.prc_retencao_csll;
					
					if(item.flg_retem_inss === 1)
						data.regras[i].prc_tributos += item.prc_retencao_inss;
					
					if(item.flg_retem_ir === 1)
						data.regras[i].prc_tributos += item.prc_retencao_ir;
					
					if(item.flg_retem_iss_pf === 1 || item.flg_retem_iss_pj === 1)
						data.regras[i].prc_tributos += item.prc_retencao_iss;
					
					if(item.flg_retem_pis === 1)
						data.regras[i].prc_tributos += item.prc_retencao_pis;
				});

				$scope.regrasTributacao = data.regras;
				$scope.paginacao.regras_tributacao = data.paginacao;
			})
			.error(function(data, status, headers, config) {
				$scope.regrasTributacao = [];
				$scope.paginacao.regras_tributacao = [];
			});
	}

	$scope.selectRegraTributacao = function(regra) {
		$scope.nf.regra_tributacao = angular.copy( regra );
		$scope.nf.regra_tributacao.prc_tributos = 0;
		$scope.nf.regra_tributacao.prc_tributos += $scope.nf.regra_tributacao.prc_retencao_iss;
		
		if($scope.nf.regra_tributacao.flg_retem_inss == 1)
			$scope.nf.regra_tributacao.prc_tributos += $scope.nf.regra_tributacao.prc_retencao_inss;
		
		if($scope.nf.regra_tributacao.flg_retem_pis == 1)
			$scope.nf.regra_tributacao.prc_tributos += $scope.nf.regra_tributacao.prc_retencao_pis;
		
		if($scope.nf.regra_tributacao.flg_retem_cofins == 1)
			$scope.nf.regra_tributacao.prc_tributos += $scope.nf.regra_tributacao.prc_retencao_cofins;
		
		if($scope.nf.regra_tributacao.flg_retem_csll == 1)
			$scope.nf.regra_tributacao.prc_tributos += $scope.nf.regra_tributacao.prc_retencao_csll;
		
		if($scope.nf.regra_tributacao.flg_retem_ir == 1)
			$scope.nf.regra_tributacao.prc_tributos += $scope.nf.regra_tributacao.prc_retencao_ir;

		$('#list_regrasTributacao').modal('hide');
	}

	$scope.calcularImpostos = function() {
		$("#btnCalcular").button('loading');
		$("#modal-calculando").modal('show');
		var postData = angular.copy($scope.nf);
		$http.post(baseUrlApi()+"nfse/calcular", postData)
			.success(function(data, status, headers, config) {
				$scope.nf.total = data.total;
				setTimeout(function(){
					$("#modal-calculando").modal('hide');
					$("#btnCalcular").button('reset');
				}, 2000);
			})
			.error(function(errors, status, headers, config) {
				$("#modal-calculando").modal('hide');
				$("#btnCalcular").button('reset');
			});
	}
	
	$scope.transmitirNFSe = function() {
		$("#btnTransmitir").button('loading');
		$("#modal-transmissao").modal('show');
		if(!$("#msg-success").hasClass('hide'))
			$("#msg-success").addClass('hide');
		
		var postData = angular.copy($scope.nf);
			postData.data_emissao = moment(postData.data_emissao, 'DD/MM/YYYY HH:mm:ss').format('YYYY/MM/DD HH:mm:ss');

		$http.post(baseUrlApi()+"nfse/transmitir", postData)
			.success(function(data, status, headers, config) {
				setTimeout(function(){
					$("#modal-transmissao").modal('hide');
					$("#btnTransmitir").button('reset');
					$("#msg-success").removeClass('hide');
				}, 2000);
			})
			.error(function(data, status, headers, config) {
				$("#modal-transmissao").modal('hide');
				$("#btnTransmitir").button('reset');

				var msg = "" ;
				if( typeof data != 'undefined' && !empty(data.erros)){
					$.each(JSON.parse(data.erros[0].mensagem).erros,function(i,v) {
						msg += v.mensagem+"<br/>";
					});
				}
				else
					msg = data;
			
				$dialogs.error('<strong>'+msg+'</strong>'+'<br><br><pre style="overflow:auto;height: 300px;" >'+data.json+'</pre>');
				$('#notifyModal h4').addClass('text-warning');
			});
	}

	function loadData() {
		if(typeof getUrlVars().id != "undefined"){
			$http.get(baseUrlApi()+"nfse/por/ordem-servico/"+ getUrlVars().id)
				.success(function(data, status, headers, config) {
					if(data.status != 'autorizado')
						loadDadosOrdemServicoByIdURL(true);
					else {
						loadDadosOrdemServicoByIdURL(false);

						$scope.nf.emitente = {};
						$scope.nf.emitente.num_cnpj 						= data.cnpj_emitente;
						$scope.nf.emitente.num_inscricao_municipal 			= data.inscricao_municipal_emitente;
						$scope.nf.emitente.nme_razao_social 				= data.nome_emitente;
						$scope.nf.emitente.nme_fantasia 					= data.nome_fantasia_emitente;
						$scope.nf.emitente.num_cep 							= data.cep_emitente;
						$scope.nf.emitente.nme_logradouro 					= data.logradouro_emitente;
						$scope.nf.emitente.num_logradouro 					= data.numero_emitente;
						$scope.nf.emitente.nme_bairro_logradouro 			= data.bairro_emitente;
						$scope.nf.emitente.uf 								= data.uf_emitente;
						$scope.nf.emitente.nme_estado 						= data.estado_emitente;
						$scope.nf.emitente.nme_municipio 					= data.municipio_emitente;
						$scope.nf.emitente.id_ibge_municipio 				= data.cod_ibge_municipio_emitente;

						$scope.nf.tomador = {};
						$scope.nf.tomador.tipo_cadastro = data.tipo_cadastro_destinatario;

						if(data.tipo_cadastro_destinatario == 'pf') {
							$scope.nf.tomador.cpf = data.cpf_destinatario;
							$scope.nf.tomador.nome = data.nome_destinatario;
						}
						else if(data.tipo_cadastro_destinatario == 'pj') {
							$scope.nf.tomador.cnpj = data.cnpj_destinatario;
							$scope.nf.tomador.num_inscricao_municipal = data.inscricao_municipal_destinatario;
							$scope.nf.tomador.razao_social = data.nome_destinatario;
							$scope.nf.tomador.nome_fantasia = data.nome_destinatario;
						}

						$scope.nf.tomador.endereco 							= data.logradouro_destinatario;
						$scope.nf.tomador.numero 							= data.numero_destinatario;
						$scope.nf.tomador.end_complemento 					= data.complemento_destinatario;
						$scope.nf.tomador.bairro 							= data.bairro_destinatario;
						$scope.nf.tomador.id_ibge_municipio 				= data.codigo_municipio_destinatario;
						$scope.nf.tomador.nme_municipio 					= data.municipio_destinatario;
						$scope.nf.tomador.nme_estado						= data.estado_destinatario;
						$scope.nf.tomador.cep 								= data.cep_destinatario;
						$scope.nf.tomador.tel_fixo 							= data.telefone_destinatario;
						$scope.nf.tomador.email 							= data.email_destinatario;
						$scope.nf.tomador.id_ibge_municipio 				= data.cod_ibge_municipio_destinatario;

						$scope.nf.regra_tributacao = {};
						$scope.nf.regra_tributacao.nme_regra_servico 		= data.nme_regra_servico;
						$scope.nf.regra_tributacao.cod_servico_municipio 	= data.issqn_item_lista_servico;
						$scope.nf.regra_tributacao.dsc_servico_municipio 	= data.issqn_descricao_servico_municipio;
						$scope.nf.regra_tributacao.prc_retencao_iss 		= data.issqn_aliquota;
						$scope.nf.regra_tributacao.cod_servico_municipio 	= data.issqn_item_lista_servico;
						$scope.nf.regra_tributacao.dsc_servico_municipio 	= data.issqn_descricao_servico_municipio;
						$scope.nf.regra_tributacao.id 						= data.cod_regra_servico;
						$scope.nf.regra_tributacao.nme_regra_servico 		= data.nme_regra_servico;
						$scope.nf.regra_tributacao.prc_tributos 			= data.percentual_total_tributos;
						$scope.nf.regra_tributacao.flg_retem_iss_pf 		= (data.issqn_tipo_pessoa == 'pf') ? 1 : 0;
						$scope.nf.regra_tributacao.flg_retem_iss_pj 		= (data.issqn_tipo_pessoa == 'pj') ? 1 : 0;
						
						$scope.nf.total = {};
						$scope.nf.total.vlr_deducoes 						= data.issqn_valor_total_deducao;
						$scope.nf.total.vlr_pis 							= data.valor_pis_servicos;
						$scope.nf.total.vlr_cofins 							= data.valor_cofins_servicos;
						$scope.nf.total.vlr_inss 							= data.valor_inss_servicos;
						$scope.nf.total.vlr_ir 								= data.valor_ir_servicos;
						$scope.nf.total.vlr_csll 							= data.valor_csll_servicos;
						$scope.nf.total.flg_iss_retido 						= (data.issqn_retido == 1);
						$scope.nf.total.vlr_iss_pf 							= data.issqn_valor_total;
						$scope.nf.total.vlr_iss_pj 							= data.issqn_valor_total;
						$scope.nf.total.vlr_iss_retido 						= data.issqn_valor_retido;
						$scope.nf.total.vlr_outras_retencoes 				= data.issqn_valor_total_outras_retencoes;
						$scope.nf.total.vlr_desconto_incondicionado 		= data.issqn_valor_total_desconto_incondicionado;
						$scope.nf.total.vlr_desconto_condicionado 			= data.issqn_valor_total_desconto_condicionado;
						
						$scope.nf.outros.codigo_cnae 						= data.issqn_codigo_cnae;
						$scope.nf.outros.codigo_tributario_municipio 		= data.issqn_codigo_tributario_municipio;
						$scope.nf.outros.discriminacao_servico 				= data.discriminacao_servico;
						
						$scope.nf.outros = {};
						$scope.nf.outros.cod_nota_fiscal 					= data.cod_nota_fiscal;
						$scope.nf.outros.data_emissao 						= moment(data.data_emissao, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss');
						$scope.nf.outros.codigo_cnae 						= data.issqn_codigo_cnae;
						$scope.nf.outros.codigo_tributario_municipio 		= data.issqn_codigo_tributario_municipio;
						$scope.nf.outros.codigo_obra 						= data.codigo_obra;
						$scope.nf.outros.art 								= data.art;
						$scope.nf.outros.discriminacao_servico 				= data.discriminacao_servico;
						$scope.nf.outros.caminho_danfe						= data.caminho_danfe;
						$scope.nf.outros.caminho_xml_nota_fiscal			= data.caminho_xml_nota_fiscal
						$scope.nf.outros.num_documento_fiscal				= data.numero;
					}
				})
				.error(function(data, status, headers, config) {
					if(status == 404)
						loadDadosOrdemServicoByIdURL(true);
				});
		}
	}

	function loadDadosOrdemServicoByIdURL(getDadosTomador){
		$http.get(baseUrlApi()+"ordem-servico/"+ getUrlVars().id)
			.success(function(data, status, headers, config) {
				$scope.nf.ordem_servico = data;
				if(empty($scope.nf.outros.discriminacao_servico))
					$scope.nf.outros.discriminacao_servico = data.dsc_observacoes_gerais;
				if(getDadosTomador)
					loadDadosTomador();
				loadServicos();
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	function loadDadosEmitente() {
		$http.get(baseUrlApi()+"empreendimentos?id="+ $scope.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				$scope.nf.emitente = data[0];
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	function loadDadosTomador() {
		$http.get(baseUrlApi()+"usuarios?usu->id="+ $scope.nf.ordem_servico.cod_cliente +'&tue->id_empreendimento='+ $scope.userLogged.id_empreendimento)
			.success(function(data, status, headers, config) {
				$scope.nf.tomador = data.usuarios[0];
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	function loadServicos() {
		$http.get(baseUrlApi()+"ordem-servico/"+ $scope.nf.ordem_servico.cod_ordem_servico +"/servicos")
			.success(function(data, status, headers, config) {
				$scope.nf.ordem_servico.servicos = data;
			})
			.error(function(data, status, headers, config) {
				console.log(data, status, headers, config);
			});
	}

	loadDadosEmitente();
	loadData();
});
