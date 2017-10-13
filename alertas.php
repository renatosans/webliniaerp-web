<li class="dropdown" data-toggle="tooltip" data-placement="bottom" title="Ir p/ Transferências">
	<a href="pedido_transferencia.php"><i class="fa fa-arrows-h fa-lg"></i></a>
</li>
<li class="dropdown" ng-controller="AlertasController">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#">
		<i class="fa fa-bell fa-lg"></i>
		<span class="notification-label bounceIn animation-delay6" ng-if="alertas.length > 0">{{ alertas.length }}</span>
	</a>
	<ul class="dropdown-menu notification dropdown-3" style="width: 300px;">
		<li ng-if="alertas.length == 0"><a href="#">Você não tem novas alertas</a></li>
		<li ng-if="alertas.length > 0"><a href="#">Você tem {{ alertas.length }} {{ alertas.length > 1 && 'novas alertas' || 'nova alerta' }}</a></li>
		<li ng-repeat="item in alertas">
			<a href="{{ item.link }}">
				<span class="notification-icon bg-{{ item.type }}">
					<i ng-class="{'fa fa-warning': item.type == 'warning', 'fa fa-flash': item.type == 'danger'}"></i>
				</span>
				<span style="white-space: normal;" class="m-left-xs">{{ item.message }}</span>
				<!-- <span class="time text-muted">{{ item.time }}</span> -->
			</a>
		</li>
		<!-- <li ng-if="alertas.length > 0"><a href="#">Ver todas as alertas</a></li> -->
	</ul>
</li>