<?php if($_SESSION['user']['id_empreendimento'] == '75'){ ?>
	<li>
		<a href="relatorio-diario-clinica.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Rel. Diário Atendimento
			</span>
		</a>
	</li>
	<li>
		<a href="rel_analitico_estoque.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Estoque Analítico
			</span>
		</a>
	</li>
	<li>
		<a href="rel_produtos_vencidos.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Produtos Vencidos
			</span>
		</a>
	</li>
	<li>
		<a href="rel_produtos_vencer.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Produtos a Vencer
			</span>
		</a>
	</li>

	<li>
		<a href="rel_total_produto_estoque.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Produtos em Estoque 
			</span>
		</a>
	</li>
	<li>
		<a href="rel_produtos_estoque_minimo.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Produtos c/ Estoque Mín.
			</span>
		</a>
	</li>
	<li>
		<a href="rel_pagamentos.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Pagamentos
			</span>
		</a>
	</li>
	<li>
		<a href="rel_contas_pagar.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Contas a Pagar
			</span>
		</a>
	</li>
	<li>
		<a href="rel_saldo_devedor_cliente.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Saldo Devedor Cliente
			</span>
		</a>
	</li>
	<li>
		<a href="rel_pagamentos_fornecedor.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Pag. a Fornecedores
			</span>
		</a>
	</li>
<?php }else{ ?>
	<li>
		<a href="rel_analitico_estoque.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Estoque Analítico
			</span>
		</a>
	</li>
	<li>
		<a href="rel_produtos_vencidos.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Produtos Vencidos
			</span>
		</a>
	</li>
	<li>
		<a href="rel_produtos_vencer.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Produtos a Vencer
			</span>
		</a>
	</li>

	<li>
		<a href="rel_total_produto_estoque.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Produtos em Estoque 
			</span>
		</a>
	</li>
	<li>
		<a href="rel_produtos_estoque_minimo.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Produtos c/ Estoque Mín.
			</span>
		</a>
	</li>
	<li>
		<a href="rel-vendas-categoria.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Vendas p/ Categoria
			</span>
		</a>
	</li>
	<li>
		<a href="rel_vendas_produto.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Vendas p/ Produto
			</span>
		</a>
	</li>
	<li>
		<a href="rel_vendas_produto_mes.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Vendas p/ Produto p/ mês
			</span>
		</a>
	</li>
	<li>
		<a href="rel_total_vendas_cliente.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Vendas p/ Cliente
			</span>
		</a>
	</li>
	<li>
		<a href="rel_total_vendas_vendedor.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Vendas p/ Vendedor
			</span>
		</a>
	</li>
	<li>
		<a href="rel_fechamento_mensal.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Fechamento Mensal
			</span>
		</a>
	</li>
	<li>
		<a href="rel_pagamentos.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Pagamentos
			</span>
		</a>
	</li>
	<li>
		<a href="rel_contas_pagar.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Contas a Pagar
			</span>
		</a>
	</li>
	<li>
		<a href="rel_saldo_devedor_cliente.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Saldo Devedor Cliente
			</span>
		</a>
	</li>
	<li>
		<a href="rel_contas_receber.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Contas a Receber
			</span>
		</a>
	</li>
	<li>
		<a href="rel_consolidado_caixa.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Resumo de Caixas
			</span>
		</a>
	</li>
	<li>
		<a href="rel_caixas.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Movimentação de Caixa
			</span>
		</a>
	</li>
	<li>
		<a href="rel_pagamentos_fornecedor.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Pag. a Fornecedores
			</span>
		</a>
	</li>
	<li>
		<a href="rel-movimentacao-estoque.php">
			<span class="submenu-label">
				<i class="fa fa-file-text-o"></i> Mov. de Estoque
			</span>
		</a>
	</li>
<?php } ?>