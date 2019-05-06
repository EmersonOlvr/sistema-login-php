<?php
	require_once('../../classes/Usuario.php');
	require_once('../../classes/Logs.php');

	// permissão mínima para acessar esta página
	$permissao_minima = 2;
	// se o usuário não estiver logado...
	if (!Usuario::estaLogado()) {
		$msg_log = Usuario::obterIPUsuario().' tentou acessar o Painel (página: util/excluirUsuario)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a index
		header('Location: ' .SITE_URL. '/index.php');
		exit();
	// se o usuário estiver logado mas não tiver a permissão necessária para prosseguir...
	} elseif (Usuario::obterUsuarioPorSessao()['permissao'] < $permissao_minima) {
		$msg_log = Usuario::obterUsuarioPorSessao()['nome'].' tentou acessar o Painel sem permissão (página: util/excluirUsuario)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a home
		header('Location: ' .SITE_URL. '/home.php');
		exit();
	// valida a variável 'id' recebida via POST, se retornar false...
	} elseif (!filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)) {
		// redireciona para a página usuarios
		header('Location: ' .SITE_URL. '/admin/usuarios.php?id_invalido&origem=excluirUsuario');
		exit();
	} else {
		// já que tudo deu certo, atribui o valor recebido na sua respectiva variável
		$id = $_POST['id'];
		$usuario_staff = Usuario::obterUsuarioPorSessao();

		// somente se o id a excluir for diferente do id do administrador que está logado...
		if ($id !== $usuario_staff['id']) {
			// ...faz uma busca pelo id fornecido
			$busca_por_id = Conexao::selecionar('id, nome', 'usuarios', 'id', '=', $id);
			// se a busca não der erro...
			if (!is_a($busca_por_id, 'PDOException')) {
				// ...e se encontrar um usuário...
				if ($busca_por_id->rowCount() == 1) {
					$usuario = $busca_por_id->fetch();
					$msg_log = $usuario_staff['nome'].' excluiu o usuário: '.$usuario['nome'];
					Logs::inserirLogPainelSucesso($msg_log);
					$exclusao = Conexao::deletar('usuarios', 'id', '=', $usuario['id']);
					// volta para a página usuarios enviando via GET a variável usuario_excluido que será tratada lá
					// (para exibir uma mensagem de sucesso)
					header('Location: ' .SITE_URL. '/admin/usuarios.php?usuario_excluido');
					exit();
				} else {
					header('Location: ' .SITE_URL. '/admin/usuarios.php?id_inexistente&origem=excluirUsuario');
					exit();
				}
			} else {
				header('Location: ' .SITE_URL. '/admin/usuarios.php?erro&origem=excluirUsuario');
				exit();
			}
		} else {
			// um administrador não pode excluir ele mesmo
			header('Location: ' .SITE_URL. '/admin/usuarios.php?impossivel&origem=excluirUsuario');
			exit();
		}
	}
?>
