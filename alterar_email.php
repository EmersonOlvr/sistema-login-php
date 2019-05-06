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

	$usuario = Usuario::obterUsuarioPorSessao();

	// se o usuário não tiver email_pendente...
	if ($usuario['email_pendente'] == null) {
		// ...redireciona para a página Home
		header('Location: home.php');
		exit();
	}

	$palavra = 'Enviamos';
	if (isset($_GET['reenviado'])) {
		$palavra = 'Re-enviamos';
	}
	if (!empty($_POST['codigo'])) {
		$codigo = $_POST['codigo'];
		// se o código inserido for igual ao cod_email_pendente do usuário armazenado no banco...
		if ($codigo === $usuario['cod_email_pendente']) {
			// o e-mail atual do usuário é mudado para o e-mail que antes estava pendente
			Usuario::atualizarUsuario($usuario, 'email', $usuario['email_pendente']);
			// agora (pelo menos por enquanto) o usuário não tem mais email_pendente
			Usuario::atualizarUsuario($usuario, 'email_pendente', 'NULL');
			Usuario::atualizarUsuario($usuario, 'cod_email_pendente', 'NULL');
			// redireciona para a página Home
			header('Location: home.php?email_alterado');
			exit();
		} else {
			$erro = 'Código inválido!';
		}
	} else {
		$erro = 'Insira o código!';
	}

	// se receber via POST a variável 'reenviar' (quando clica em 'Re-enviar')...
	if (isset($_POST['reenviar'])) {
		// ...reenvia o cod_email_pendente para o email_pendente do usuário
		Usuario::enviarCodigoAlteracao($usuario);
		// recarrega a página passando a variável 'reenviado'
		header('Location: ?reenviado');
		exit();
	}
	// se receber via POST a variável 'cancelar' (quando clica em 'Cancelar a troca...')...
	if (isset($_POST['cancelar'])) {
		// ...cancela a troca de e-mail, ou seja, não existirá mais email_pendente
		Usuario::atualizarUsuario($usuario, 'email_pendente', 'NULL');
		// e nem um cod_email_pendente
		Usuario::atualizarUsuario($usuario, 'cod_email_pendente', 'NULL');
		header('Location: home.php?troca_email_cancelada');
		exit();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Alteração de E-mail | <?php echo SITE_TITULO ?></title>
		<script type="text/javascript">
			function desativar(field) {
				field.disabled = true;
			}
		</script>
	</head>

	<body class="text-center">
		<div class="principal" style="max-width: 400px;">
			<h1 class="mt-2 h3 mb-3 font-weight-normal text-center">Alteração de E-mail</h1>
			<!-- verifica se a variável $erro existe (ela é criada quando ocorre algum erro) -->
			<?php if (isset($erro)) { ?>
				<div class="text-erro mb-2">
					<?php echo $erro ?>
				</div>
			<?php } ?>

			Olá <?php echo $usuario['nome'] ?>, você solicitou a troca do seu e-mail. <?php echo $palavra ?> um código de confirmação de e-mail para <b><?php echo $usuario['email_pendente'] ?></b>.<br> Insira-o abaixo para confirmar a troca de e-mail.
			<div class="row mt-2 justify-content-center">
				<form method="POST" onsubmit="desativar(this.btn_reenviar)">
					<input type="hidden" name="reenviar">
					<input type="submit" name="btn_reenviar" class="btn btn-primary" value="Reenviar">
				</form>
				<span class="ml-2 mr-2 mt-1">ou</span>
				<form method="POST">
					<input type="hidden" name="cancelar">
					<input type="submit" name="btn_cancelar" class="btn btn-danger" value="Cancelar">
				</form>
			</div>
			<br>

			<form method="POST">
				<div class="input-group">
					<input type="text" name="codigo" class="form-control" minlength="4" maxlength="4" placeholder="Código de confirmação..." required>
					<div class="input-group-append">
						<input type="submit" class="btn btn-lg btn-success btn-block" value="Validar">
					</div>
				</div>
			</form>

			<p class="mt-4 mb-0 text-muted">&copy; 2019 - Emerson</p>
		</div>
	</body>
</html>
