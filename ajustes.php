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

	$msgs = array(); // mensagens de sucesso serão guardadas aqui
	$alterou = false;
	$usuario = Usuario::obterUsuarioPorSessao();

	if (!empty($_POST['novo_nome'])) {
		$novo_nome = $_POST['novo_nome'];
		// se $novo_nome for diferente do nome atual do usuário...
		if ($novo_nome != $usuario['nome']) {
			if (strlen($novo_nome) < 2 || strlen($novo_nome) > 64) {
				$erro = 'Nome inválido. Mínimo 2 e máximo de 64 caracteres.';
			} elseif (!preg_match('/^[a-z\d]{2,64}$/i', $novo_nome)) {
				$erro = 'Nome inválido. Somente letras e números são permitidos.';
			} else {
				$atualizacao_nome = Usuario::atualizarUsuario($usuario, 'nome', $novo_nome);
				if (!is_a($atualizacao_nome, 'PDOException')) {
					$usuario = Usuario::obterUsuarioPorSessao(); // atualiza o usuário
					array_push($msgs, 'Nome alterado com sucesso.');
					$alterou = true;
				} elseif ($atualizacao_nome->errorInfo[0] == 23000) {
					$erro = 'Este nome já está em uso.';
				} else {
					$erro = 'Desculpe, algo deu errado.';
				}
			}
		}
	}
	if (!empty($_POST['novo_email'])) {
		$novo_email = strtolower($_POST['novo_email']);
		// se $novo_nome for diferente do e-mail atual e do email_pendente do usuário...
		if ($novo_email != $usuario['email'] && $novo_email != $usuario['email_pendente']) {
			if (strlen($novo_email) > 64) {
				$erro = 'E-mail muito grande. Máximo de 64 caracteres.';
			} elseif (!filter_var($novo_email, FILTER_VALIDATE_EMAIL)) {
				$erro = 'E-mail em formato inválido.';
			} else {
				$busca = Conexao::selecionar('*', 'usuarios', 'email', '=', $novo_email);
				if (!is_a($busca, 'PDOException')) {
					if ($busca->rowCount() == 0) {
						$cod_email_pendente = Usuario::gerarCodEmailPendente();
						Usuario::atualizarUsuario($usuario, 'email_pendente', $novo_email);
						Usuario::atualizarUsuario($usuario, 'cod_email_pendente', $cod_email_pendente);
						$usuario = Usuario::obterUsuarioPorSessao(); // atualiza o usuário
						Usuario::enviarCodigoAlteracao($usuario);
						$alterou = true;
					} else {
						$erro = 'Este e-mail já está em uso.';
					}
				} else {
					$erro = 'Desculpe, algo deu errado.';
				}
			}
		}
	}
	if (!empty($_POST['nova_senha'])) {
		$nova_senha = $_POST['nova_senha'];
		if (!isset($_POST['nova_senha_repetida'])) {
			$erro = 'Por favor, repita a senha.';
		} elseif ($_POST['nova_senha_repetida'] !== $nova_senha) {
			$erro = 'As senhas não conferem.';
		} elseif (strlen($_POST['nova_senha_repetida']) < 6) {
			$erro = 'Senha muito curta. Mínimo de 6 caracteres.';
		} else {
			$hash_nova_senha = password_hash($nova_senha, PASSWORD_DEFAULT);
			$atualizacao_senha = Usuario::atualizarUsuario($usuario, 'senha', $hash_nova_senha);
			if (!is_a($atualizacao_senha, 'PDOException')) {
				array_push($msgs, 'Senha alterada com sucesso.');
				$alterou = true;
			} else {
				$erro = 'Desculpe, algo deu errado.';
			}
		}
	}

	if ($alterou && count($msgs) > 0) {
		$sucesso = $msgs;
	}
	// se houver um e-mail pendente para alteração
	if ($usuario['email_pendente'] != null) {
		$pendencia = "Você tem uma <a href='alterar_email.php'>alteração de e-mail</a> pendente.";
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Ajustes | <?php echo SITE_TITULO ?></title>

		<script type="text/javascript">
			function requerido(input_atual, input_alvo) {
				if (input_atual.value.length > 0) {
					input_alvo.required = true;
				} else {
					input_alvo.required = false;
				}
			}
			function desativar(field) {
				field.disabled = true;
			}
		</script>
	</head>

	<body class="text-center">
		<div class="principal" style="max-width: 410px;">
			<h1 class="mt-2 h3 mb-3 font-weight-normal">Ajustes da conta</h1>
			<!-- verifica se a variável $erro existe (ela é criada quando ocorre algum erro) -->
			<?php if (isset($erro)) { ?>
				<div class="text-erro mb-2">
					<?php echo $erro ?>
				</div>
			<?php } ?>
			<!-- verifica se a variável $sucesso existe (ela é criada quando os dados do usuário são atualizados) -->
			<?php if (isset($sucesso)) { 
				foreach ($sucesso as $msg) { ?>
					<div class="text-sucesso mb-2">
						<?php echo $msg ?>
					</div>
			<?php } } ?>
			<!-- verifica se a variável $pendencia existe (ela é criada quando o usuário altera seu e-mail atual) -->
			<?php if (isset($pendencia)) { ?>
				<div class="mb-2" style="color: blue;">
					<?php echo $pendencia ?>
				</div>
			<?php } ?>

			<form method="POST" onsubmit="desativar(this.salvar)">
				<table style="width: 100%; text-align: left">
					<tbody>
						<tr>
							<td><label for="novo_nome">Nome:</label></td>
							<td>
								<input class="form-control" type="text" id="novo_nome" name="novo_nome" value="<?php echo $usuario['nome'] ?>" pattern="[a-zA-Z0-9]{2,64}" placeholder="Insira um nome..." required>
							</td>
						</tr>
						<tr>
							<td><label for="novo_email">Email:</label></td>
							<td>
								<input class="form-control" type="email" id="novo_email" name="novo_email" value="<?php echo $usuario['email'] ?>" placeholder="Insira um e-mail..." required>
							</td>
						</tr>
						<tr>
							<td><label for="nova_senha">Nova senha:</label></td>
							<td>
								<input class="form-control" type="password" id="nova_senha" name="nova_senha" placeholder="Nova senha..." minlength="6" autocomplete="off" oninput="requerido(nova_senha, nova_senha_repetida)">
							</td>
						</tr>
						<tr>
							<td><label for="nova_senha_repetida">Nova senha (repita):</label></td>
							<td>
								<input class="form-control" type="password" id="nova_senha_repetida" name="nova_senha_repetida" placeholder="Nova senha (repita)..." minlength="6" autocomplete="off" oninput="requerido(nova_senha_repetida, nova_senha)">
							</td>
						</tr>
					</tbody>
				</table>
				<br>
				<button class="btn btn-lg btn-success btn-block" name="salvar" type="submit">Salvar</button>
				<a class="btn btn-lg btn-primary btn-block" href="home.php">Voltar</a>
			</form>
			<p class="mt-4 mb-0 text-muted">&copy; 2019 - Emerson</p>
		</div>
	</body>
</html>
