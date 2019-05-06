<?php
	require_once('classes/Usuario.php');

	// se o usuário não estiver logado...
	if (!Usuario::estaLogado()) {
		// ...redireciona para a página Entrar
		header('Location: entrar.php');
		exit();
	} elseif (!Usuario::emailEstaAtivado(Usuario::obterUsuarioPorSessao())) {
		header('Location: confirmar_email.php');
		exit();
	}

	// gera um hash do ID da sessão atual
	$token = md5(session_id());

	$usuario = Usuario::obterUsuarioPorSessao();
	// permissão mínima para que apareça o botão "Painel" que redireciona para o painel de administração
	$permissao_minima_painel = 2;
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Home: <?php echo $usuario['nome'] ?> | <?php echo SITE_TITULO ?></title>
	</head>

	<body class="text-center">
		<div class="principal" style="max-width: 400px;">
			<?php if (isset($_GET['email_confirmado'])) { ?>
				<div class="text-sucesso mb-2">
					<?php echo 'E-mail confirmado com sucesso!' ?>
				</div>
			<?php } ?>
			<?php if (isset($_GET['email_alterado'])) { ?>
				<div class="text-sucesso mb-2">
					<?php echo 'E-mail alterado com sucesso!' ?>
				</div>
			<?php } ?>
			<?php if (isset($_GET['troca_email_cancelada'])) { ?>
				<div class="text-sucesso mb-2">
					<?php echo 'A troca de e-mail foi cancelada!' ?>
				</div>
			<?php } ?>
			
			<table style="width: 100%; text-align: left">
				<tbody>
					<tr>
						<td>ID:</td>
						<td><?php echo $usuario['id'] ?></td>
					</tr>
					<tr>
						<td>Nome:</td>
						<td><?php echo $usuario['nome'] ?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?php echo $usuario['email'] ?></td>
					</tr>
					<tr>
						<td>Data de Registro:</td>
						<td><?php echo date('d/m/Y H:i', strtotime($usuario['registro_data'])) ?></td>
					</tr>
					<tr>
						<td>Último acesso:</td>
						<td><?php echo date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])) ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<!-- se o usuário tiver a permissão mínima (ou maior), ele poderá acessar o painel clicando no botão -->
			<?php if ($usuario['permissao'] >= $permissao_minima_painel) { ?>
				<a class="btn btn-lg btn-info btn-block" href="admin/index.php">Painel</a>
			<?php } ?>
			<a class="btn btn-lg btn-primary btn-block" href="ajustes.php">Ajustes</a>
			<a class="btn btn-lg btn-danger btn-block" href="sair.php?token=<?php echo $token ?>">Sair</a>
			<p class="mt-4 mb-0 text-muted">&copy; 2019 - Emerson</p>
		</div>
	</body>
</html>
