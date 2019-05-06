<?php
	require_once('classes/Login.php');

	// se o usuário estiver logado...
	if (Usuario::estaLogado()) {
		// ...redireciona para a página Home
		header("Location: home.php");
		exit();
	}

	// se receber dados (quando clicar em Entrar)
	if (isset($_POST['entrar'])) {
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$senha = isset($_POST['senha']) ? $_POST['senha'] : '';

		$entrar = Login::entrar($email, $senha);

		if ($entrar) {
			header('Location: home.php');
			exit();
		} else {
			$erro = Login::obterErro();
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Entrar | <?php echo SITE_TITULO ?></title>
	</head>

	<body class="text-center">
		<form class="principal form-entrar" method="POST">
			<h1 class="mt-2 h3 mb-3 font-weight-normal">Acessar conta</h1>
			<!-- verifica se a variável $erro existe (ela é criada quando ocorre algum erro) -->
			<?php if (isset($erro)) { ?>
				<div class="text-erro mb-2">
					<?php echo $erro ?>
				</div>
			<?php } ?>

			<input type="email" id="inputEmail" class="form-control" name="email" placeholder="E-mail" required autofocus>
			<input type="password" id="inputPassword" class="form-control" name="senha" placeholder="Senha" required autocomplete="off">
			<button class="btn btn-lg btn-primary btn-block" type="submit" name="entrar">Entrar</button>

			<p class="mt-1 text-muted text-left">ou <a href="registrar.php">Registre-se</a></p>
			<p class="mt-4 mb-0 text-muted">&copy; 2019 - Emerson</p>
		</form>
	</body>
</html>
