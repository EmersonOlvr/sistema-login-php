<?php
	require_once('../../classes/Usuario.php');
	require_once('../../classes/Logs.php');
	
	$id_atual = $_POST['id_atual'];

	// permissão mínima para acessar esta página
	$permissao_minima = 2;
	// se o usuário não estiver logado...
	if (!Usuario::estaLogado()) {
		$msg_log = Usuario::obterIPUsuario().' tentou acessar o Painel (página: util/editarUsuario)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a index
		header('Location: ' .SITE_URL. '/index.php');
		exit();
	// se o usuário estiver logado mas não tiver a permissão necessária para prosseguir...
	} elseif (Usuario::obterUsuarioPorSessao()['permissao'] < $permissao_minima) {
		$msg_log = Usuario::obterUsuarioPorSessao()['nome'].' tentou acessar o Painel sem permissão (página: util/editarUsuario)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a home
		header('Location: ' .SITE_URL. '/home.php');
		exit();
	// valida cada variável recebida via POST, quando dá erro, redireciona para a página usuario
	} elseif (!filter_input(INPUT_POST, 'id_atual', FILTER_VALIDATE_INT)) {
		header('Location: ' .SITE_URL. '/admin/procurarusuario.php?id_invalido&origem=editarUsuario');
		exit();
	} elseif (!filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)) {
		header('Location: ' .SITE_URL. '/admin/procurarusuario.php?id_invalido&origem=editarUsuario');
		exit();
	} elseif (!filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS)) {
		header('Location: ' .SITE_URL. '/admin/usuario.php?id='.$_POST['id_atual'].'&nome_invalido&origem=editarUsuario');
		exit();
	} elseif (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
		header('Location: ' .SITE_URL. '/admin/usuario.php?id='.$_POST['id_atual'].'&email_invalido&origem=editarUsuario');
		exit();
	} elseif (!filter_input(INPUT_POST, 'email_ativado', FILTER_VALIDATE_INT) && filter_input(INPUT_POST, 'email_ativado', FILTER_VALIDATE_INT) != 0) {
		header('Location: ' .SITE_URL. '/admin/usuario.php?id='.$_POST['id_atual'].'&email_ativado_invalido&origem=editarUsuario');
		exit();
	} elseif (!filter_input(INPUT_POST, 'permissao', FILTER_VALIDATE_INT)) {
		header('Location: ' .SITE_URL. '/admin/usuario.php?id='.$_POST['id_atual'].'&permissao_invalida&origem=editarUsuario');
		exit();
	} else {
		// já que não há nada de errado, passamos os valores recebidos para sua respectivas variáveis
		$id_atual = $_POST['id_atual'];
		$novo_id = $_POST['id'];
		$novo_nome = $_POST['nome'];
		$novo_email = strtolower($_POST['email']);
		$email_ativado = $_POST['email_ativado'];
		$nova_permissao = $_POST['permissao'];

		// faz uma busca na tabela de usuários pelo usuário que tem o id fornecido via POST ($id_atual)
		$usuario = Usuario::obterUsuarioPorID($id_atual);
		// se não retornar uma excessão...
		if (!is_a($usuario, 'PDOException')) {
			// ...se não existir nenhum usuário com $id_atual
			if ($usuario == null) {
				// redireciona para a página procurarusuario
				header('Location: ' .SITE_URL. '/admin/procurarusuario.php?id_inexistente&origem=editarUsuario');
				exit();
			} else {
				// obtêm o usuário que está gerenciando o Painel
				$usuario_staff = Usuario::obterUsuarioPorSessao();

				// verifica abaixo se os novos dados são realmente novos, se não for não atualiza nada
				if ($novo_nome != $usuario['nome']) {
					$msg_log = $usuario_staff['nome'].' alterou o nome de '.$usuario['nome'].' para: '.$novo_nome;
					if ($usuario_staff['id'] == $usuario['id']) {
						$msg_log = $usuario_staff['nome'].' alterou o próprio nome para: '.$novo_nome;
					}
					$atualizacao_nome = Usuario::atualizarUsuario($usuario, 'nome', $novo_nome);
					if (!$atualizacao_nome) {
						header('Location: ' .SITE_URL. '/admin/usuario.php?id=' .$id_atual. '&erro_nome');
						exit();
					}
					$atualizou = true;
					// atualiza o usuário
					$usuario = Usuario::obterUsuarioPorID($id_atual);
					Logs::inserirLogPainelSucesso($msg_log);
				}
				if ($novo_email != $usuario['email']) {
					$msg_log = $usuario_staff['nome'].' alterou o e-mail de '.$usuario['nome'].' ('.$usuario['email'].') para: '.$novo_email;
					if ($usuario_staff['id'] == $usuario['id']) {
						$msg_log = $usuario_staff['nome'].' alterou o próprio e-mail ('.$usuario['email'].') para: '.$novo_email;
					}
					$atualizacao_email = Usuario::atualizarUsuario($usuario, 'email', $novo_email);
					if (!$atualizacao_email) {
						header('Location: ' .SITE_URL. '/admin/usuario.php?id=' .$id_atual. '&erro_email');
						exit();
					}
					$atualizou = true;
					// atualiza o usuário
					$usuario = Usuario::obterUsuarioPorID($id_atual);
					Logs::inserirLogPainelSucesso($msg_log);
				}
				if ($email_ativado != $usuario['email_ativado']) {
					// se o e-mail da conta tiver sido desativado um código de confirmação terá que ser re-enviado para o e-mail
					// do usuário para que a conta dele possa ser reativada
					if ($email_ativado == 0) {
						$palavra = ' desativou';
						$cod_email = Usuario::gerarCodEmail();
						Usuario::atualizarUsuario($usuario, 'cod_email', $cod_email);
						Usuario::enviarCodigoConfirmacao(Usuario::obterUsuarioPorID($id_atual));
					}
					if ($email_ativado == 1) {
						$palavra = ' ativou';
						Usuario::atualizarUsuario($usuario, 'cod_email', 'NULL');
					}
					$msg_log = $usuario_staff['nome'].$palavra.' o e-mail da conta de '.$usuario['nome'];
					if ($usuario_staff['id'] == $usuario['id']) {
						$msg_log = $usuario_staff['nome'].$palavra.' o e-mail da própria conta';
					}

					Usuario::atualizarUsuario($usuario, 'email_ativado', $email_ativado);
					$atualizou = true;
					// atualiza o usuário
					$usuario = Usuario::obterUsuarioPorID($id_atual);
					Logs::inserirLogPainelSucesso($msg_log);
				}
				if ($nova_permissao != $usuario['permissao']) {
					$msg_log = $usuario_staff['nome'].' alterou a permissão de '.$usuario['nome'].' ('.$usuario['permissao'].') para: '.$nova_permissao;
					if ($usuario_staff['permissao'] == $usuario['permissao']) {
						$msg_log = $usuario_staff['nome'].' alterou a própria permissão ('.$usuario['permissao'].') para: '.$nova_permissao;
					}
					$atualizacao_permissao = Usuario::atualizarUsuario($usuario, 'permissao', $nova_permissao);
					if (!$atualizacao_permissao) {
						header('Location: ' .SITE_URL. '/admin/usuario.php?id=' .$id_atual. '&erro_permissao');
						exit();
					}
					$atualizou = true;
					// atualiza o usuário
					$usuario = Usuario::obterUsuarioPorID($id_atual);
					Logs::inserirLogPainelSucesso($msg_log);
				}
				if ($novo_id != $id_atual) {
					$msg_log = $usuario_staff['nome'].' alterou o ID de '.$usuario['nome'].' ('.$id_atual.') para: '.$novo_id;
					if ($usuario_staff['id'] == $usuario['id']) {
						$msg_log = $usuario_staff['nome'].' alterou o próprio ID ('.$id_atual.') para: '.$novo_id;
					}
					$atualizacao_id = Usuario::atualizarUsuario($usuario, 'id', $novo_id);
					if ($atualizacao_id) {
						if ($usuario_staff['id'] == $id_atual) {
							$_SESSION['usuario_id'] = $novo_id;
						}
						Logs::inserirLogPainelSucesso($msg_log);
						// Não precisa definir $atualizou = true, pois já vai redirecionar
						header('Location: ' .SITE_URL. '/admin/usuario.php?id=' .$novo_id. '&atualizado');
						exit();
					} else {
						header('Location: ' .SITE_URL. '/admin/usuario.php?id=' .$id_atual. '&erro_id');
						exit();
					}
				}

				// verifica se algum atributo foi atualizado
				if (isset($atualizou)) {
					header('Location: ' .SITE_URL. '/admin/usuario.php?id=' .$id_atual. '&atualizado');
					exit();
				} else {
					header('Location: ' .SITE_URL. '/admin/usuario.php?id=' .$id_atual);
					exit();
				}
			}
		} else {
			header('Location: ' .SITE_URL. '/admin/procurarusuario.php?erro&origem=editarUsuario');
			exit();
		}
	}
?>
