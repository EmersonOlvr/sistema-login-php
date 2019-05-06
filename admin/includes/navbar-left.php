<div class="span3">
	<div class="sidebar">

		<ul class="widget widget-menu unstyled">
			<li class="active">
				<a href="index.php"><i class="fas fa-home"></i>Início</a>
			</li>
			<li>
				<a href="usuarios.php"><i class="fas fa-users"></i>Usuários</a>
			</li>
			<li>
				<a href="procurarusuario.php"><i class="fas fa-search"></i>Procurar usuário</a>
			</li>
		</ul>
		<ul class="widget widget-menu unstyled">
			<li>
				<a href="logspainel.php"><i class="fas fa-list"></i>Logs do Painel</a>
			</li>
		</ul>
		<ul class="widget widget-menu unstyled">
			<li><a href="../sair.php?token=<?php echo md5(session_id()) ?>"><i class="fas fa-sign-out-alt"></i>Sair </a></li>
		</ul>

	</div>
</div>
