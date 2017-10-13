<?php
	$menu = $_SESSION['user']['menu'];
?>
<div class="main-menu">
	<ul>
	<?php
		foreach ($menu as $n1) {
	?>
		<li class="<?php  echo (!isset($n1['itens']) || ( count($n1['itens'])<=0 || !empty($n1['url_modulo']) ) ) ? '' : 'openable' ?>">
			<a href="<?php  echo (!isset($n1['itens']) || ( count($n1['itens'])<=0 || !empty($n1['url_modulo']) ) ) ? $n1['url_modulo'] : '#' ?>" >
				<span class="menu-icon"><i class="fa <?php  echo $n1['icn_modulo'] ?> fa-lg"></i></span>
				<span class="text"><?php  echo $n1['nme_modulo'] ?></span>
				<span class="menu-hover"></span>
			 </a>
			<?php if(isset($n1['itens']) && count($n1['itens'])>0){ ?>
			<ul class="submenu">
				<?php foreach ($n1['itens'] as $n2) { ?>
						<li>
							<a href="<?php  echo $n2['url_modulo'] ?>">
								<span class="submenu-label">
									<i class="fa <?php  echo $n2['icn_modulo'] ?>"></i> <?php  echo $n2['nme_modulo'] ?>
								</span>
							</a>
						</li>
				<?php } ?>
			</ul>
			<?php } ?>
		</li>
	<?php
		}
	?>
	</ul>
</div>