<?php
	require_once('classes/Usuario.php');

	// se o usuário estiver logado...
	if (Usuario::estaLogado()) {
		// ...redireciona para a página Home
		header("Location: home.php");
		exit();
	}

	if (isset($_GET['saiu'])) {
		$msg = 'Você saiu com sucesso!';
	} elseif (isset($_GET['erro'])) {
		$erro = 'Desculpe, algo deu errado.';
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Index | <?php echo SITE_TITULO ?></title>
	</head>

	<body class="text-center">
		<div class="principal">
			<h1 class="mt-2 h3 mb-3 font-weight-normal">Olá</h1>
			<!-- verifica se a variável $erro existe (ela é criada quando ocorre algum erro) -->
			<?php if (isset($erro)) { ?>
				<div class="text-erro mb-2">
					<?php echo $erro ?>
				</div>
			<?php } ?>
			<!-- verifica se a variável $msg existe (ela é criada quando o usuário faz logout) -->
			<?php if (isset($msg)) { ?>
				<div class="text-sucesso mb-2">
					<?php echo $msg ?>
				</div>
			<?php } ?>
			
			<div class="row">
				<div class="col-md">
					<a class="btn btn-lg btn-primary btn-block" href="entrar.php">Entrar</a>
				</div>
				<div class="col-md">
					<a class="btn btn-lg btn-primary btn-block" href="registrar.php">Registrar</a>
				</div>
			</div>
			<p class="mt-4 mb-0 text-muted">&copy; 2019 - Emerson</p>
		</div>
	</body>
</html>
