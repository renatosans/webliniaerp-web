<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="utf-8">
	<title>WebliniaERP-aqui</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Bootstrap core CSS -->
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

	<!-- Pace -->
	<link href="css/pace.css" rel="stylesheet">

	<!-- Color box -->
	<link href="css/colorbox/colorbox.css" rel="stylesheet">

	<!-- Datepicker -->
	<link href="css/datepicker.css" rel="stylesheet"/>

	<!-- Endless -->
	<link href="css/endless.min.css" rel="stylesheet">
	<link href="css/endless-landing.min.css" rel="stylesheet">

	<link href="css/custom.css" rel="stylesheet">

	<?php include("google_analytics.php"); ?>

	<style>
		.datepicker{
			z-index:1151 !important;
		}
		
		@media (min-width: 768px) {
			.navbar-nav>li>a {
				padding-top: 25px;
				padding-bottom: 25px;
			}
		}

		.navbar-nav>li>a {
			line-height: 57px;
		}

		.navbar-brand img {
			max-height: 80px;
		}

		@media(max-width: 768px) {
			.navbar-brand img {
				max-height: 30px;
			}
		}

		.nav>li>a.login {
			background-color: #1E4259;
			color: #FFF;
		}

		.nav>li>a.login:hover,
		.nav>li>a.login:focus {
			background-color: #2E4C5E;
		}
	</style>
  </head>

  <body class="overflow-hidden">
	<!-- Overlay Div -->
	<div id="overlay" class="transparent"></div>

	<div id="wrapper" class="preload">
		<header class="navbar navbar-fixed-top bg-white">
			<div class="container">
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a href="#" class="navbar-brand">
						<img src="img/logo-horizontal-color.png">
					</a>
				</div>
				<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#landing-content" class="top-link">Home</a></li>
						<li><a href="#feature" class="top-link">Principais Recursos</a></li>
						<li><a href="#testemonials" class="top-link">Depoimentos</a></li>
						<!--<li><a href="#pricing" class="top-link">Planos e Preços</a></li>
						<li><a href="#contact" class="top-link">Fale Conosco</a></li>-->
						<li><a href="login.php" class="top-link login"><i class="fa fa-lock"></i> Entrar</a></li>
					</ul>
				</nav>
			</div>
		</header>

		<div id="landing-content">
			<div id="main-slider" class="carousel slide bg-dark" data-ride="carousel">
				<ol class="carousel-indicators">
					<li data-target="#main-slider" data-slide-to="0" class="active"></li>
					<li data-target="#main-slider" data-slide-to="1"></li>
					<li data-target="#main-slider" data-slide-to="2"></li>
				</ol>

				<div class="carousel-inner">
					<div class="item active">
						<img src="img/bg-fundo2.jpg" alt="" class="img-background">
						<div class="row" style="margin-top: 100px;">
							<div class="col-xs-6 text-right">
								<h2 class="m-top-lg m-right-xs text-white fadeInDownLarge ">Gestão & Controle</h2>
								<p class="text-white fadeInUpLarge  m-right-xs hidden-xs">
									Tudo o que você precisa para gerenciar sua empresa, num só lugar!<br>
									Agora é fácil ter controle financeiro e administrativo <br>
									da sua micro e pequena empresa. E o melhor, de onde você quiser.
								</p>
								<a href="#" class="btn btn-info btn-lg fadeInLeftLarge m-bottom-lg m-right-xs hidden-xs" data-toggle="modal" data-target="#myModal">Teste por 15 dias <i class="fa fa-arrow-right"></i></a>
							</div>
							<div class="col-xs-6">
								
							</div>
						</div>
					</div>

					<div class="item">
						<img src="img/bg-fundo3.jpg" alt="" class="img-background">
						<div class="row" style="margin-top: 100px;">
							<div class="col-xs-7 text-right">
								<h2 class="m-top-lg text-white fadeInDownLarge ">Alta Produtividade x Baixo investimento</h2>
								<p class="text-white fadeInUpLarge  hidden-xs">
									Por ser 100% web, você não tem custos com profissionais especializados, <br>
									infraestrutura, licenças e ainda aumenta sua produtividade <br>
									controlando a gestão de toda sua empresa.
								</p>
								<a href="#" class="btn btn-info btn-lg fadeInRightLarge hidden-xs" data-toggle="modal" data-target="#myModal">Teste por 15 dias <i class="fa fa-arrow-right"></i></a>
							</div>
							<div class="col-xs-5 text-right">
								
							</div>
						</div>
					</div>

					<div class="item">
						<img src="img/bg-fundo5.jpg" alt="" class="img-background">
						<div class="row text-center" style="margin-top: 100px;">
							<h2 class="m-top-lg text-white fadeInDownLarge animation-delay2">Segurança para você e sua empresa</h2>
							<p class="text-white fadeInUpLarge animation-delay2 hidden-xs">
								Com o WebliniaERP seus dados ficam armazenados na nuvem e você pode acessá-los quando e de onde quiser. <br>
								Tudo isso com total segurança e garantia de disponibilidade.
							</p>
							<a href="#" class="btn btn-info btn-lg fadeInRightLarge animation-delay2 hidden-xs" data-toggle="modal" data-target="#myModal">Teste por 15 dias <i class="fa fa-arrow-right"></i></a>
						</div>
					</div>
				</div>

				<a class="left carousel-control" href="#main-slider" data-slide="prev">
					<span class="fa fa-chevron-left"></span>
				</a>

				<a class="right carousel-control" href="#main-slider" data-slide="next">
					<span class="fa fa-chevron-right"></span>
				</a>
			</div>

			<div class="bg-white content-padding text-center font-lg">
				<div class="container">
					<span class="m-right-sm">QUER SABER COMO O <span class="text-primary">WEBLINIA</span><span class="text-danger">ERP</span> PODE AJUDAR O SEU NEGÓCIO?</span>
					<div class="inline-block m-top-sm">
						<a href="#" class="btn btn-success btn-lg m-bottom-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-tag"></i> AGENDE UMA VISITA</a>
						<a href="#" class="btn btn-info btn-lg m-bottom-sm fadeInRightLarge animation-delay2" data-toggle="modal" data-target="#myModal">Teste por 15 dias <i class="fa fa-arrow-right"></i></a>
					</div>
				</div>
			</div>

			<div class="bg-light padding-md" id="feature">
				<div class="container">
					<div class="row">
						<div class="col-sm-4 content-padding">
							<div class="feature-icon text-center">
								<i class="fa fa-desktop fa-3x"></i>
							</div>
							<div class="text-center font-lg">
								<h3>Frente de Caixa (PDV)</h3>
							</div>
							<p>
								Funções de suporte nos recibos e vendas, fechamento de caixa, devoluções, trocas, descontos;<br>
								Gestão de inventário, transferências entre depósitos, entrada e saída de produtos;<br>
							</p>
						</div><!-- /.col -->
						<div class="col-sm-4 content-padding">
							<div class="feature-icon text-center ">
								<i class="fa fa-exchange fa-4x"></i>
							</div>
							<div class="text-center font-lg">
								<h3>Contas a Pagar e Receber</h3>
							</div>
							<p>
								Você tem à sua disposição um fluxo de caixa completo para seu negócio!<br>
								Seja informado do vencimento de suas contas e veja em tempo real a saúde financeira da sua empresa.
							</p>
						</div><!-- /.col -->
						<div class="col-sm-4 content-padding">
							<div class="feature-icon text-center">
								<i class="fa fa-bar-chart fa-3x"></i>
							</div>
							<div class="text-center font-lg">
								<h3>Painel de Indicadores</h3>
							</div>
							<p>
								Visualize as principais informações do seu negócio de forma rápida. <br>
								Com o painel de indicadores você tem sempre a mão informações importantes para a tomada de decisão na sua empresa.
							</p>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div>
			</div>

			<div class="padding-md">
				<div class="container">
					<div class="row">
						<div class="col-sm-4 content-padding">
							<div class="feature-icon text-center">
								<i class="fa fa-tablet fa-4x"></i>
							</div>
							<div class="text-center font-lg">
								<h3>Layout Responsivo</h3>
							</div>
							<p>
								Com o WebliniaERP você acessa os dados de sua empresa e confere relatórios em tempo real, quando e de onde quiser, seja por smartphone, tablet, notebook e computador.
								O controle total da sua empresa a um clique de distância.
							</p>
						</div><!-- /.col -->
						<div class="col-sm-4 content-padding">
							<div class="feature-icon text-center">
								<i class="fa fa-cloud fa-4x"></i>
							</div>
							<div class="text-center font-lg">
								<h3>Totalmente Web</h3>
							</div>
							<p>
								Não perca mais fins de semana dentro da empresa analisando relatórios.<br>
								O WebliniaERP é 100% online, isso significa que você tem acesso às informações da sua empresa a qualquer dia de semana, 24 horas por dia e o mais importante: de qualquer lugar.
							</p>
						</div><!-- /.col -->
						<div class="col-sm-4 content-padding">
							<div class="feature-icon text-center">
								<i class="fa fa-bullhorn fa-4x"></i>
							</div>
							<div class="text-center font-lg">
								<h3>Alertas por E-mail</h3>
							</div>
							<p>
								Não se preocupe em anotar os pagamentos e recebíveis em bloco de anotação.<br>
								Deixe que o WebliniaERP te informe das transações diárias e de todas as movimentações de venda dos seus cliente.
							</p>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div>
			</div>

			<div id="testemonials">
				<div class="section-header">
					<hr class="left visible-lg">
					<span>
						DEPOIMENTOS
					</span>
					<hr class="right visible-lg">
				</div>

				<div class="container">
					<div class='row'>
						<div class='col-md-offset-1 col-md-10'>
							<div class="carousel slide" data-ride="carousel" id="quote-carousel">
								<!-- Bottom Carousel Indicators -->
								<ol class="carousel-indicators">
									<li data-target="#quote-carousel" data-slide-to="0" class="active"></li>
									<li data-target="#quote-carousel" data-slide-to="1"></li>
									<li data-target="#quote-carousel" data-slide-to="2"></li>
									<li data-target="#quote-carousel" data-slide-to="3"></li>
								</ol>
								
								<!-- Carousel Slides / Quotes -->
								<div class="carousel-inner">
									<!-- Quote 1 -->
									<div class="item active">
										<blockquote>
											<div class="row">
												<div class="col-sm-2 text-center">
													<img class="img-circle" src="https://scontent-mia1-1.xx.fbcdn.net/hphotos-xap1/v/t1.0-9/11742869_479718348857521_5158908504574540113_n.jpg?oh=180af3dee1ede84eeba890f1859173a2&oe=56EB30D3" style="width: 100px;height:100px;">
												</div>
												<div class="col-sm-10">
													<p>O WebliniaERP me ajuda a organizar minha empresa de forma fácil e ágil! O Suporte é rápido e me dá a segurança de que minha empresa está bem amparada! Uso e recomendo!</p>
													<small>Denise Macedo - Loja ClubeD</small>
												</div>
											</div>
										</blockquote>
									</div>
								
									<!-- Quote 2 -->
									<div class="item">
										<blockquote>
											<div class="row">
												<div class="col-sm-2 text-center">
													<img class="img-circle" src="https://scontent-mia1-1.xx.fbcdn.net/hphotos-xfa1/v/t1.0-9/389047_285820131552508_1190729944_n.png?oh=faeed10590e020f3dab614bc62b5fb66&oe=56F1CFF5" style="width: 100px;height:100px;">
												</div>
												<div class="col-sm-10">
													<p>Como usuária do sistema WebliniaERP, posso dizer que é uma excelente ferramenta de trabalho! Organiza todas as contas a pagar e receber, controle de produtos e estoques e milhões de outras funções. Atende todas as minhas necessidades, de forma simples, intuitiva e muito eficiente.</p>
													<small>Nana - Grupo Hage Suplementos</small>
												</div>
											</div>
										</blockquote>
									</div>

									<!-- Quote 3 -->
									<div class="item">
										<blockquote>
											<div class="row">
												<div class="col-sm-2 text-center">
													<img class="img-circle" src="https://fbcdn-sphotos-g-a.akamaihd.net/hphotos-ak-xft1/v/t1.0-9/10574358_417385575137088_3142687392634697989_n.png?oh=08819da60460842da82b553f6b4f6ca4&oe=56DD2C76&__gda__=1458620156_bf7930f38f2c6f64e6e9354670cbd424" style="width: 100px;height:100px;">
												</div>
												<div class="col-sm-10">
													<p>Depois que começei a usar o WebliniaERP percebi o quanto eu precisava de um sistema simples! Hoje todas vendas são registradas e acesso todos os relatórios pelo meu celular. Isso é incrível!</p>
													<small>Luciana Caetano - CliqueiComprei.com</small>
												</div>
											</div>
										</blockquote>
									</div>

									<!-- Quote 4 -->
									<div class="item">
										<blockquote>
											<div class="row">
												<div class="col-sm-2 text-center">
													<img class="img-circle" src="https://igcdn-photos-b-a.akamaihd.net/hphotos-ak-xpa1/t51.2885-15/e35/12135470_449087291948185_1473177149_n.jpg" style="width: 100px;height:100px;">
												</div>
												<div class="col-sm-10">
													<p>Sem dúvidas é a melhor ferramenta que já utilizei! Com o painel de indicadores tenho rapidamente as principais informações que preciso para gerenciar a minha loja. Recomendo!</p>
													<small>Alexandre Barufi - Elite Fit Suplementos</small>
												</div>
											</div>
										</blockquote>
									</div>
								</div>
						
								<!-- Carousel Buttons Next/Prev -->
								<a data-slide="prev" href="#quote-carousel" class="left carousel-control"><i class="fa fa-chevron-left"></i></a>
								<a data-slide="next" href="#quote-carousel" class="right carousel-control"><i class="fa fa-chevron-right"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="bg-white content-padding">
				<div class="container">
					<h3>
						<span class="text-muted">Se você precisa de mais,</span>
						<br>
						<strong>o WebliniaERP te oferece mais</strong>
					</h3>
					<div class="seperator"></div>
					<p>
						Se seu negócio precisa de mais recursos, você tem à sua disposição serviços exclusivos que tornarão sua gestão ainda mais eficiente.<br>
						Recorrência e parcelamento, cadastro de clientes e fornecedores, regime por caixa e competência, serviços bancários, e mais!
					</p>
					<div class="seperator"></div>
					<a href="#" class="btn btn-lg btn-success animated-element fadeInUp no-animation" data-toggle="modal" data-target="#myModal"><i class="fa fa-tag"></i> AGENDE UMA VISITA</a>
					<!-- <hr/> -->
					<!-- <img src="img/gallery01.png" alt="" class="fadeInRightLarge no-animation animated-element m-top-md animation-delay1"> -->
				</div>
			</div>

			<!--<div class="bg-light" id="pricing">
				<div class="container">
					<div class="padding-md">
						<div class="section-header">
							<hr class="left visible-lg">
							<span>
								PLANOS E PREÇOS
							</span>
							<hr class="right visible-lg">
						</div>
					</div>

					<div class="row row-merge">
						<div class="col-md-4 col-sm-6">
							<div class="pricing-widget fadeInUp animated-element no-animation">
								<div class="pricing-head">
									Plano Start
								</div>
								<div class="pricing-body">
									<div class="pricing-cost">
										<strong class="block">R$ 129,90</strong>
										<small>por mês</small>
									</div>
									<ul class="pricing-list text-center">
										<li><a class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">Teste agora por 15 dias!</a></li>
										<li>Usuários <strong>3</strong></li>
										<li><strike>Contas a pagar e receber</strike></li>
										<li><strike>Mapa de clientes</strike></li>
										<li><strike>Painel de indicadores (Dashboard)</strike></li>
										<li><strike>Controle de comissão por vendedores</strike></li>
										<li><strike>Controle de descontos por faixa</strike></li>
										<li>Service Desk Online</li>
										<li><strike>Suporte 24 horas</strike></li>
										<li><strike>Vitrine/Loja Virtual</strike></li>
										<li class="text-center"><a href="#" class="btn btn-default btn-success" data-toggle="modal" data-target="#myModal">Agende uma Visita</a></li>
									</ul>
								</div>
							</div>
						</div>

						<div class="col-md-4 col-sm-6">
							<div class="pricing-widget active fadeInUp animated-element no-animation animation-delay2">
								<div class="ribbon-wrapper">
									<div class="ribbon-inner shadow-pulse bg-danger">
										+ Popular
									</div>
								</div>
								<div class="pricing-head">
									Plano Platinum
								</div>
								<div class="pricing-body">
									<div class="pricing-cost">
										<strong class="block"><span class="font-12">R$ </span>199,90</strong>
										<small>por mês</small>
									</div>
									<ul class="pricing-list text-center">
										<li><a class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">Teste agora por 15 dias!</a></li>
										<li>Usuários <strong>10</strong></li>
										<li>Contas a pagar e receber</li>
										<li><strike>Mapa de clientes</strike></li>
										<li><strike>Painel de indicadores (Dashboard)</strike></li>
										<li>Controle de comissão por vendedores</li>
										<li><strike>Controle de descontos por faixa</strike></li>
										<li>Service Desk Online</li>
										<li><strike>Suporte 24 horas</strike></li>
										<li><strike>Vitrine/Loja Virtual</strike></li>
										<li class="text-center"><a href="#" class="btn btn-default btn-success" data-toggle="modal" data-target="#myModal">Agende uma Visita</a></li>
									</ul>
								</div>
							</div>
						</div>

						<div class="col-md-4 col-sm-6">
							<div class="pricing-widget fadeInUp animated-element no-animation animation-delay4">
								<div class="pricing-head">
									Plano Premium
								</div>
								<div class="pricing-body">
									<div class="pricing-cost">
										<strong class="block">R$ 299,90</strong>
										<small>por mês</small>
									</div>
									<ul class="pricing-list text-center">
										<li><a class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">Teste agora por 15 dias!</a></li>
										<li>Usuários <strong>ilimitados</strong></li>
										<li>Contas a pagar e receber</li>
										<li>Mapa de clientes</li>
										<li>Painel de indicadores (Dashboard)</li>
										<li>Controle de comissão por vendedores</li>
										<li>Controle de descontos por faixa</li>
										<li>Service Desk Online</li>
										<li>Suporte 24 horas</li>
										<li>Vitrine/Loja Virtual</li>
										<li class="text-center"><a href="#" class="btn btn-default btn-success" data-toggle="modal" data-target="#myModal">Agende uma Visita</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><!-- /pricing -->

			<div class="bg-light">
				<div class="text-center content-padding" id="contact">
					<div class="container">
						<h3>ASSINE NOSSA NEWSLETTER</h3>
						<p class="m-bottom-md">
							Inscrevendo-se na nossa newsletter você estará sempre atualizado com as <br>
							últimas novidades sobre gestão empresarial e dicas diversas de controle e organização.
						</p>

						<form id="form" class="form-news form form-inline" role="form">
							<div class="form-group">
								<label class="sr-only">Insira seu e-mail aqui</label>
								<input type="text" id="email" name="email" class="form-control input-lg" style="width: 300px;" placeholder="Digite aqui seu e-mail" id="newsletter">
							</div>
							<a class="btn btn-lg btn-info envia-news" data-loading-text="Aguarde..."><i class="fa fa-newspaper-o"></i> Assinar</a>
						</form>
					</div>

					<div class="seperator"></div>
					<div class="alert news"></div>
				</div>
			</div><!-- /newsletter-->

			<div class="content-padding bg-light">
				<div class="container">
					<div class="panel">
						<div class="panel-body content-padding">
							<div class="pull-left">
								<p class="font-lg">O WebliniaERP é o melhor sistema de gestão online para micro e pequenas empresas.</p>
								<p class="text-muted">Com o WebliniaERP você tem as informações necessárias para tomadas de decisões inteligentes. <br>E agora, você pode experimentar por 15 dias grátis!</p>
							</div>
							<a href="#" class="btn btn-lg btn-success pull-right m-top-xs fadeInLeftLarge no-animation animated-element animation-delay1"
								data-toggle="modal" data-target="#myModal">
								<i class="fa fa-tag"></i> AGENDE UMA VISITA
							</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Agende uma visita</h4>
						</div>
						<div class="modal-body">
							<form id="form" class="form form-visita" role="form">
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group name-group">
											<label class="control-label" for="name">Nome:</label>
											<input id="name" name="name" class="form-control"></input>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-8">
										<div class="form-group email-group">
											<label class="control-label" for="email">E-Mail:</label>
											<input id="email" name="email" class="form-control"></input>
										</div>
									</div>

									<div class="col-sm-4">
										<div class="form-group telefone-group">
											<label class="control-label" for="telefone">Telefone:</label>
											<input id="telefone" name="telefone" class="form-control"></input>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="form-group subject-group">
											<label class="control-label" for="subject">Assunto:</label>
											<input id="subject" name="subject" class="form-control" readonly="readonly" value="[WebliniaERP] | Agendar visita de demonstração"></input>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group gooddate-group">
											<label class="control-label" for="gooddate">Melhor data:</label>
											<div class="input-group">
												<input readonly="readonly" style="background:#FFF; cursor:pointer" type="text" id="gooddate" name="gooddate" class="datepicker form-control">
												<span class="input-group-addon" style="cursor:pointer;" id="cld_gooddate"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
									</div>

									<div class="col-sm-8">
										<div class="form-group message-group">
											<label class="control-label" for="message">Mensagem:</label>
											<textarea id="message" name="message" class="form-control" style="height: 100px;"></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12">
										<div class="alert visita"></div>
									</div>
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default cancel-visita" data-dismiss="modal">Cancelar</button>
							<button type="button" class="btn btn-primary agendar-visita" data-loading-text="Aguarde...">Agendar Visita</button>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /landing-content -->

		<footer>
			<div class="container">
				<div class="row">
					<div class="col-sm-3 padding-md">
						<p class="font-lg"><i class="fa fa-building"></i> Sobre Nós</p>
						<p>
							<small>
								WebliniaERP é um sistema que foi desenvolvido com característica próprias, para atender necessidades de micros e pequenos empresários.<br/>
								Nossos diferencias são agilidade, segurança e produtividade. 
							</small>
						</p>
					</div><!-- /.col -->
					<div class="col-sm-3 padding-md">
						<p class="font-lg"><i class="fa fa-link"></i> Links</p>
						<ul class="list-unstyled useful-link">
							<li>
								<a href="http://tracker.weblinia.com.br" target="_blank">
									<small><i class="fa fa-chevron-right"></i> Portal de Chamados</small>
								</a>
							</li>
						</ul>
					</div><!-- /.col -->
					<div class="col-sm-3 padding-md">
						<p class="font-lg"><i class="fa fa-thumbs-o-up"></i> Acompanhe</p>
						<a href="http://fb.com/WebliniaERP" class="social-connect tooltip-test facebook-hover" data-toggle="tooltip" data-original-title="Facebook"><i class="fa fa-facebook"></i></a>
					</div><!-- /.col -->
					<div class="col-sm-3 padding-md">
						<p class="font-lg"><i class="fa fa-phone"></i> Fale Conosco</p>
						<p>
							Telefones: <br>(11) 98565-4956 <br>(11) 97420-7398
						</p>
						<div class="seperator"></div>
						<a href="mailto:comercial@webliniaerp.com.br" class="btn btn-info"><i class="fa fa-at"></i> Enviar E-mail</a>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div>
		</footer>
	</div><!-- /wrapper -->

	<a href="" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>

	<!-- Le javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	<!-- Jquery -->
	<script src="js/jquery-1.10.2.min.js"></script>

	<!-- Bootstrap -->
	<script src="bootstrap/js/bootstrap.min.js"></script>

	<!-- Colorbox -->
	<script src='js/jquery.colorbox.min.js'></script>

	<!-- Waypoint -->
	<script src='js/waypoints.min.js'></script>

	<!-- LocalScroll -->
	<script src='js/jquery.localscroll.min.js'></script>

	<!-- ScrollTo -->
	<script src='js/jquery.scrollTo.min.js'></script>

	<!-- Modernizr -->
	<script src='js/modernizr.min.js'></script>

	<!-- Pace -->
	<script src='js/pace.min.js'></script>

	<!-- Popup Overlay -->
	<script src='js/jquery.popupoverlay.min.js'></script>

	<!-- Datepicker -->
	<script src='js/bootstrap-datepicker.min.js'></script>

	<!-- Slimscroll -->
	<script src='js/jquery.slimscroll.min.js'></script>

	<!-- Cookie -->
	<script src='js/jquery.cookie.min.js'></script>

	<!-- Endless -->
	<script src="js/endless/endless.js"></script>

	<script>
		$(function(){
			//Set the carousel options
			$('#quote-carousel').carousel({
				pause: true,
				interval: 10000,
			});

			$(".alert.news").hide();

			$('.datepicker').datepicker();
			$('.datepicker').on('changeDate', function(ev){$(this).datepicker('hide');});
			$("#cld_gooddate").on("click", function(){ $("#gooddate").trigger("focus"); });

			$('.animated-element').waypoint(function() {

				$(this).removeClass('no-animation');

			}, { offset: '80%' });

			$('.nav').localScroll({duration:1000});

			//Colorbox
			$('.gallery-zoom').colorbox({
				rel:'gallery',
				maxWidth:'90%',
				width:'800px'
			});

			$("#myModal").on("show.bs.modal", function(event){
				$(".alert.visita").hide();
			});

			$('.envia-news').click(function() {
				var postdata = $('.form-news').serialize();

				$.ajax({
					type: 'POST',
					url: 'util/script-envia-news.php',
					data: postdata,
					dataType: 'json',
					beforeSend: function() {
						$(".envia-news").button("loading");
					},
					error: function(x,y,z) {
						$(".envia-news").button("reset");
						$('.alert.news').addClass("alert-danger").html(x.responseJSON.message);
						$('.alert.news').show();
					},
					success: function(json) {
						$('.form-news').hide();
						$('.alert.news').removeClass("alert-danger");
						$('.alert.news').addClass("alert-success").html(json.message);
						$('.alert.news').show();
					}
				});
			});

			$(".agendar-visita").on("click", function() {
				var postdata = $('.form-visita').serialize();

				$.ajax({
					type: 'POST',
					url: 'util/script-envia-email.php',
					data: postdata,
					beforeSend: function() {
						$(".agendar-visita").button("loading");
						clearValidate();
					},
					error: function(x,y,z) {
						$(".agendar-visita").button("reset");

						if(x.status == 406) {
							$.each(x.responseJSON,function(i,item){
								$("." + i + "-group").addClass("has-error");
								$("." + i + "-group").attr("data-toggle", "tooltip")
									.attr("data-placement", "bottom")
									.attr("title", item)
									.attr("data-original-title", item);
								$("." + i + "-group").tooltip();
							});
						}
						else {
							$(".alert.visita").addClass("alert-danger").html(x.responseText).show();
						}
					},
					success: function(x,y,z) {
						$(".agendar-visita").button("reset");

						clearForm();
						clearValidate();

						$(".alert.visita").addClass("alert-success").html(x.message);
						$(".alert.visita").show();
					}
				})
			});

			$(".cancel-visita").on("click", function() {
				clearValidate();
				clearForm();
			});
		});

		function clearForm() {
			$(".form-visita input:not(input#subject)").val("");
			$("textarea").val("");
		}

		function clearValidate() {
			$(".form-group")
				.removeClass("has-error")
				.removeAttr("data-toggle")
				.removeAttr("data-placement")
				.removeAttr("title")
				.removeAttr("data-original-title");
			$(".alert.visita").hide();
		}
	</script>
  </body>
</html>
