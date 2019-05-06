<?php
	require_once('classes/Usuario.php');

	// se o usuário não estiver logado...
	if (!Usuario::estaLogado()) {
		// ...redireciona para a página Index
		header('Location: index.php');
		exit();
	}

	// cria um hash do id da sessão atual
	$token = md5(session_id());

	// se receber via GET (url) a variável 'token' e o valor dela for igual a variável $token
	if (isset($_GET['token']) && $_GET['token'] === $token) {
		Usuario::destruirSessao();
		header("Location: index.php?saiu");
		exit();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Sair | <?php echo SITE_TITULO ?></title>
	</head>

	<body class="text-center">
		<div class="principal">
			<h1 class="mt-2 h3 mb-3 font-weight-normal">Deseja mesmo sair?</h1>
			<div class="row">
				<div class="col-md">
					<a class="btn btn-lg btn-success btn-block" href="sair.php?token=<?php echo $token ?>">Sim</a>
				</div>
				<div class="col-md">
					<a class="btn btn-lg btn-danger btn-block" href="home.php">Não</a>
				</div>
			</div>
			<p class="mt-4 mb-0 text-muted">&copy; 2019 - Emerson</p>
		</div>
	</body>
</html>
