<?php
	require_once('../classes/Usuario.php');
	require_once('../classes/Logs.php');

	// permissão mínima para acessar esta página
	$permissao_minima = 2;
	// se o usuário não estiver logado...
	if (!Usuario::estaLogado()) {
		$msg_log = Usuario::obterIPUsuario().' tentou acessar o Painel (página: procurarusuario)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a index
		header('Location: ' .SITE_URL. '/index.php');
		exit();
	// se o usuário estiver logado mas não tiver a permissão necessária para prosseguir...
	} elseif (Usuario::obterUsuarioPorSessao()['permissao'] < $permissao_minima) {
		$msg_log = Usuario::obterUsuarioPorSessao()['nome'].' tentou acessar o Painel sem permissão (página: procurarusuario)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a home
		header('Location: ' .SITE_URL. '/home.php');
		exit();
	} elseif (!Usuario::emailEstaAtivado(Usuario::obterUsuarioPorSessao())) {
		header('Location: ../confirmar_email.php');
		exit();
	} else {
		// se receber via POST alguma das variáveis a seguir...
		if (isset($_POST['id'])) {
			// valida a variável recebida via POST, se a validação retornar false...
			if (!filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)) {
				// recarrega a página passado uma variável via GET que será tratada (mais abaixo)
				header('Location: ?id_invalido&origem=procurarusuario');
				exit();
			} else {
				header('Location: usuarios.php?id=' .$_POST['id']);
				exit();
			}
		} elseif (isset($_POST['nome'])) {
			// valida a variável recebida via POST, se a validação retornar false...
			if (!filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS)) {
				// recarrega a página passado uma variável via GET que será tratada (mais abaixo)
				header('Location: ?nome_invalido&origem=procurarusuario');
				exit();
			} else {
				header('Location: usuarios.php?nome=' .$_POST['nome']);
				exit();
			}
		} elseif (isset($_POST['email'])) {
			// valida a variável recebida via POST, se a validação retornar false...
			if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
				// recarrega a página passado uma variável via GET que será tratada (mais abaixo)
				header('Location: ?email_invalido&origem=procurarusuario');
				exit();
			} else {
				header('Location: usuarios.php?email=' .$_POST['email']);
				exit();
			}
		}

		// se receber via GET alguma das variáveis a seguir...
		if (isset($_GET['erro'])) {
			$erro = 'Não foi possível se conectar ao banco de dados.';
		} elseif (isset($_GET['id_invalido'])) {
			$erro = '<b>ID</b> inválido.';
		} elseif (isset($_GET['nome_invalido'])) {
			$erro = '<b>Nome</b> inválido.';
		} elseif (isset($_GET['email_invalido'])) {
			$erro = '<b>E-mail</b> inválido.';
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Procurar usuário - Painel | <?php echo SITE_TITULO ?></title>
	</head>
	<body>
		<?php require_once('includes/navbar-top.php') ?>
		<div class="wrapper">
			<div class="container">
				<div class="row">
					<?php require_once('includes/navbar-left.php') ?>
					<div class="span9">
						<div class="content">
							<div class="module">
								<div class="module-head">
									<h3>Procurar usuário</h3>
								</div>
								<div class="module-body">
								<!-- verifica se a variável $erro existe (ela é criada quando recebe algum erro via GET) -->
								<?php if (isset($erro)) { ?>
									<div class="alert alert-error">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<strong>Erro!</strong> <?php echo $erro ?>
									</div>
								<?php } ?>

									<form method="POST">
										<table class="table table-bordered">
											<thead>
												<th colspan="2">Procurar usuário por ID</th>
											</thead>
											<tbody>
												<tr>
													<td><input type="number" name="id" placeholder="ID do usuário a procurar..." required></td>
													<td><input type="submit" class="btn btn-large btn-primary" name="procurar_usuario" value="Procurar"></td>
												</tr>
											</tbody>
										</table>
									</form>
									<br>
									<form method="POST">
										<table class="table table-bordered">
											<thead>
												<th colspan="2">Procurar usuário por Nome</th>
											</thead>
											<tbody>
												<tr>
													<td><input type="text" name="nome" placeholder="Nome do usuário a procurar..." required></td>
													<td><input type="submit" class="btn btn-large btn-primary" name="procurar_usuario" value="Procurar"></td>
												</tr>
											</tbody>
										</table>
									</form>
									<br>
									<form method="POST">
										<table class="table table-bordered">
											<thead>
												<th colspan="2">Procurar usuário por E-mail</th>
											</thead>
											<tbody>
												<tr>
													<td><input type="email" name="email" placeholder="E-mail do usuário a procurar..." required></td>
													<td><input type="submit" class="btn btn-large btn-primary" name="procurar_usuario" value="Procurar"></td>
												</tr>
											</tbody>
										</table>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php require_once('includes/footer.php') ?>
	</body>
</html>
