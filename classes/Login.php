<?php
	require_once('Usuario.php');

	/**
	 * Cria a classe Login, que contém o que é necessário para iniciar a sessão de um usuário
	 */
	class Login {
		private static $erro = 'Erro desconhecido.';

		public static function entrar($email, $senha) {
			if (empty($email)) {
				self::$erro = 'Insira um e-mail.';
				return false;
			} elseif (empty($senha)) {
				self::$erro = 'Insira uma senha.';
				return false;
			} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				self::$erro = 'E-mail em formato inválido.';
				return false;
			} else {
				$busca_email = Conexao::selecionar('*', 'usuarios', 'email', '=', $email);
				// se a função selecionar() acima não retornar uma excessão, significa que não houve erros
				if (!is_a($busca_email, 'PDOException')) {
					$usuario = $busca_email->fetch();
					if ($busca_email->rowCount() == 0) {
						self::$erro = 'O e-mail inserido não corresponde a nenhuma conta.';
						return false;
					} else {
						if (password_verify($senha, $usuario['senha'])) {
							// pega o IP do usuário
							$ip = Usuario::obterIPUsuario();
							// atualiza o último acesso, colocando a data atual (usada na hora de fazer login)
							$atualizacao_ultimo_acesso = Usuario::atualizarUsuario($usuario, 'ultimo_acesso', date('Y-m-d H:i:s'));
							// atualiza o último ip, colocando o ip atual (usado na hora de fazer login)
							$atualizacao_ultimo_ip = Usuario::atualizarUsuario($usuario, 'ultimo_ip', $ip);
							// se tudo deu certo (as atualizações acima retornaram true)...
							if ($atualizacao_ultimo_acesso && $atualizacao_ultimo_ip) {
								// ...cria a sessão do usuário
								$_SESSION['usuario_id'] = $usuario['id'];
								return true;
							} else {
								return false;
							}
						} else {
							self::$erro = 'Senha incorreta.';
							return false;
						}
					}
				} else {
					self::$erro = 'Desculpe, algo deu errado.';
					return false;
				}
			}
		}

		// retorna o erro
		public static function obterErro() {
			return self::$erro;
		}
	}

?>
