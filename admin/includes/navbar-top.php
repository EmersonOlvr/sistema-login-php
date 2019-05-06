<?php
	require_once('../classes/Usuario.php');
?>

<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
				<i class="icon-reorder shaded"></i>
            </a>
            <a class="brand" href="index.php">Painel | <?php echo SITE_TITULO ?> </a>
			<div class="nav-collapse collapse navbar-inverse-collapse">
				<ul class="nav pull-right">
					<li class="nav-user dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img src="../img/usuarios/perfil/foto.png" class="nav-avatar" />
						<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="../home.php">Voltar para a Home</a></li>
							<li><a href="usuario.php?id=<?php echo Usuario::obterUsuarioPorSessao()['id'] ?>">Editar meu usuário</a></li>
							<li class="divider"></li>
							<li><a href="../sair.php?token=<?php echo md5(session_id()) ?>"><i class="fas fa-sign-out-alt"></i>Sair </a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
