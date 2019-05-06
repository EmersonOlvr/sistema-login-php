<?php
	require_once('classes/Registro.php');

	// se o usuário estiver logado...
	if (Usuario::estaLogado()) {
		// ...redireciona para a página Home
		header("Location: home.php");
		exit();
	}

	// se receber no mínimo 1 variável via POST (quando clicar em Registrar)
	if (count($_POST) > 0) {
		$nome = isset($_POST['nome']) ? $_POST['nome'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$senha = isset($_POST['senha']) ? $_POST['senha'] : '';
		$senha_repetida = isset($_POST['senha_repetida']) ? $_POST['senha_repetida'] : '';

		// se conseguir registrar
		if (Registro::registrar($nome, $email, $senha, $senha_repetida)) {
			$sucesso = 'Registrado com sucesso.';
		} else {
			$erro = Registro::obterErro();
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Registrar | <?php echo SITE_TITULO ?></title>
		<script type="text/javascript">
			function desativar(field) {
				field.disabled = true;
			}
		</script>
	</head>

	<body class="text-center">
		<form name="reg_form" class="principal form-registrar" method="POST" onsubmit="desativar(this.registrar)">
			<h1 class="mt-2 h3 mb-3 font-weight-normal">Criar nova conta</h1>
			<!-- verifica se a variável $erro existe (ela é criada quando ocorre algum erro) -->
			<?php if (isset($erro)) { ?>
				<div class="text-erro mb-2">
					<?php echo $erro ?>
				</div>
			<?php } ?>
			<!-- verifica se a variável $sucesso existe (ela é criada quando o usuário é registrado com sucesso) -->
			<?php if (isset($sucesso)) { ?>
				<div class="text-sucesso mb-2">
					<?php echo $sucesso ?>
				</div>
			<?php } ?>

			<input type="text" name="nome" class="form-control" pattern="[a-zA-Z0-9]{2,64}" placeholder="Nome" required autofocus>
			<input type="email" id="email" name="email" class="form-control" placeholder="Endereço de E-mail" required>
			<input type="password" name="senha" class="form-control" minlength="6" placeholder="Senha" autocomplete="off" required>
			<input type="password" name="senha_repetida" class="form-control" placeholder="Senha (repita)" autocomplete="off" required>
			<button class="btn btn-lg btn-primary btn-block" name="registrar" type="submit">Registrar</button>

			<p class="mt-1 text-muted text-left">ou <a href="entrar.php">Faça login</a></p>
			<p class="mt-4 mb-0 text-muted">&copy; 2019 - Emerson</p>
		</form>
	</body>
</html>
