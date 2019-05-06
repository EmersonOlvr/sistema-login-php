<?php
	require_once('classes/Usuario.php');

	// se o usuário não estiver logado...
	if (!Usuario::estaLogado()) {
		// ...redireciona para a página Entrar
		header("Location: entrar.php");
		exit();
	} elseif (Usuario::emailEstaAtivado(Usuario::obterUsuarioPorSessao())) {
		header('Location: home.php');
		exit();
	}

	$usuario = Usuario::obterUsuarioPorSessao();
				
	$palavra = 'enviamos';
	if (isset($_GET['reenviado'])) {
		$palavra = 're-enviamos';
	}
	// se receber via POST a variável 'codigo' e ela não estiver vazia...
	if (!empty($_POST['codigo'])) {
		$codigo = $_POST['codigo'];
		// se o código inserido for igual ao cod_email do usuário armazenado no banco
		if ($codigo === $usuario['cod_email']) {
			// agora o usuário terá seu e-mail ativado
			Usuario::atualizarUsuario($usuario, 'email_ativado', '1');
			Usuario::atualizarUsuario($usuario, 'cod_email', 'NULL');
			header('Location: home.php?email_confirmado');
			exit();
		} else {
			$erro = 'Código inválido!';
		}
	}

	// se receber a variável 'reenviar' via GET (quando clica em Reenviar)...
	if (isset($_POST['reenviar'])) {
		// ...reenvia o cod_email para o e-mail do usuário
		Usuario::enviarCodigoConfirmacao($usuario);
		// recarrega a página passando a variável 'reenviado'
		header('Location: ?reenviado');
		exit();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Confirmação de E-mail | <?php echo SITE_TITULO ?></title>
		<script type="text/javascript">
			function desativar(field) {
				field.disabled = true;
			}
		</script>
	</head>

	<body class="text-center">
		<div class="principal" style="max-width: 400px;">
			<h1 class="mt-2 h3 mb-3 font-weight-normal text-center">Confirmação de E-mail</h1>
			<!-- verifica se a variável $erro existe (ela é criada quando ocorre algum erro) -->
			<?php if (isset($erro)) { ?>
				<div class="text-erro mb-2">
					<?php echo $erro ?>
				</div>
			<?php } ?>

			Olá <?php echo $usuario['nome'].', '.$palavra ?> um código de confirmação de e-mail para <b><?php echo $usuario['email'] ?></b>, insira-o abaixo para ativar sua conta.
			<form method="POST" onsubmit="desativar(this.btn_reenviar)">
				<input type="hidden" name="reenviar">
				<input type="submit" name="btn_reenviar" class="btn btn-primary mt-2" value="Reenviar código de confirmação">
			</form>
			<br>

			<form method="POST">
				<div class="input-group">
					<input type="text" name="codigo" class="form-control" minlength="4" maxlength="4" placeholder="Código de confirmação..." required>
					<div class="input-group-append">
						<input type="submit" class="btn btn-lg btn-success btn-block" value="Validar">
					</div>
				</div>
			</form>
			<p class="mt-1 text-muted text-left">ou <a href="sair.php?token=<?php echo md5(session_id())?>">Sair</a></p>

			<p class="mt-4 mb-0 text-muted">&copy; 2019 - Emerson</p>
		</div>
	</body>
</html>
