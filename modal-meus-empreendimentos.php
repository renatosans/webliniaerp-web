<div class="modal fade meus-empreendimentos" ng-controller="MasterController" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Empreendimentos</h4>
				<p>Selecione o empreendimento que deseja trabalhar</p>
			</div>
			<div class="modal-body" style="overflow-y: auto; max-height: 500px;">
				<table class="table table-bordered table-condensed table-striped table-hover">
					<thead>
						<tr>
							<th colspan="2">Nome</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="item in meusEmpreendimentos">
							<td>{{ item.nome_empreendimento }}</td>
							<td width="80">
								<button ng-click="changeEmpreendimento(item)" class="btn btn-success btn-xs" type="button">
									<i class="fa fa-check-square-o"></i> Selecionar
								</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>