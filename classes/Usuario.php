<?php
	require_once('Conexao.php');
	require_once('Email.php');

	/**
	 * Cria a classe Usuario, que contém tudo o que é necessário para gerenciar um usuário
	 */
	class Usuario {
		/**
		 * Obtenções
		 */
		// usuário
		public static function obterUsuarioPorID($id) {
			if (empty($id)) {
				return null;
			} elseif (!filter_var($id, FILTER_VALIDATE_INT)) {
				return null;
			} else {
				$usuario = Conexao::selecionar('*', 'usuarios', 'id', '=', $id);
				if (!is_a($usuario, 'PDOException')) {
					return $usuario->fetch();
				}
				return null;
			}
		}
		public static function obterUsuarioPorNome($nome) {
			if (empty($nome)) {
				return null;
			} elseif (!filter_var($nome, FILTER_SANITIZE_SPECIAL_CHARS)) {
				return null;
			} else {
				$usuario = Conexao::selecionar('*', 'usuarios', 'nome', '=', $nome);
				if (!is_a($usuario, 'PDOException')) {
					return $usuario->fetch();
				}
				return null;
			}
		}
		public static function obterUsuarioPorEmail($email) {
			if (empty($email)) {
				return null;
			} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				return null;
			} else {
				$usuario = Conexao::selecionar('*', 'usuarios', 'email', '=', $email);
				if (!is_a($usuario, 'PDOException')) {
					return $usuario->fetch();
				}
				return null;
			}
		}
		public static function obterUsuarioPorSessao() {
			if (!self::estaLogado()) {
				return null;
			} else {
				$usuario_id = $_SESSION['usuario_id'];
				$usuario = Conexao::selecionar('*', 'usuarios', 'id', '=', $usuario_id);
				if (!is_a($usuario, 'PDOException')) {
					return $usuario->fetch();
				}
				return null;
			}
		}
		public static function emailEstaAtivado($usuario) {
			if ($usuario['email_ativado'] == '1') {
				return true;
			}
			// retorno padrão
			return false;
		}
		
		// sessão
		public static function estaLogado() {
			// verifica se 'usuario_id' já está na sessão
			if (isset($_SESSION['usuario_id'])) {
				// testa a conexão com o banco
				$teste_conexao = Conexao::conectar_bd();
				// se conectar_bd() retornar uma excessão (ou seja, deu erro na conexão com o banco)...
				if (is_a($teste_conexao, 'PDOException')) {
					// destrói a sessão (por segurança e para evitar bugs)
					unset($_SESSION['usuario_id']);
					session_destroy();
					return false;
				}
				// a conexão está OK
				return true;
			}
			// retorno padrão
			return false;
		}
		public static function destruirSessao() {
			// pra destruir uma sessão, o usuário precisa estar logado
			if (self::estaLogado()) {
				unset($_SESSION['usuario_id']);
				session_destroy();
				return true;
			}
			// retorno padrão
			return false;
		}
		
		// IP
		public static function obterIPUsuario() {
			if (isset($_SERVER['HTTP_CLIENT_IP'])) {
				return $_SERVER['HTTP_CLIENT_IP'];
			} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			} elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
				return $_SERVER['HTTP_X_FORWARDED'];
			} elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
				return $_SERVER['HTTP_FORWARDED_FOR'];
			} elseif (isset($_SERVER['HTTP_FORWARDED'])) {
				return $_SERVER['HTTP_FORWARDED'];
			} elseif (isset($_SERVER['REMOTE_ADDR'])) {
				return $_SERVER['REMOTE_ADDR'];
			} else {
				// retorna null se nenhuma das formas acima retornar um IP (muito improvável)
				return null;
			}
		}

		/**
		 * Atualizações
		 */
		public static function atualizarUsuario($usuario, $atributo, $novoValor) {
			$atualizacao = Conexao::atualizar('usuarios', $atributo, $novoValor, 'id', '=', $usuario['id']);
			if (!is_a($atualizacao, 'PDOException')) {
				return true;
			}
			// retorno padrão
			return false;
		}

		/**
		 * Outros
		 */
		// gera um código de confirmação de e-mail único para cada usuário
		public static function gerarCodEmail() {
			// opções para a geração do nome da página
			$letras_e_numeros =  "bZwCF8gSak4Ru1KVoz2yJXQrPTmi7Mc3BH0Lv5YtsWlOIDdfN6qGejAp9xUEnh";
			// quantidade de caracteres em $letras_e_numeros (62)
			$qtd = strlen($letras_e_numeros);
			// quantidade de caracteres no código que será gerado (4 = 13.388.280 códigos diferentes)
			$max = 4;
			// guardará um número aleatório de 0 a 58 ($qtd - $max)
			$inicio = rand(0, $qtd - $max);

			// pega 4 ($max) caracteres de $letras_e_numeros (depois de embaralhar)
			$codigo = substr(str_shuffle($letras_e_numeros), $inicio, $max);
			// faz uma busca no banco para checar se o $codigo gerado já existe
			$busca_codigo = Conexao::selecionar('*', 'usuarios', 'cod_email', '=', $codigo);
			if (!is_a($busca_codigo, 'PDOException')) {
				while ($busca_codigo->rowCount() > 0) {
					$codigo = substr(str_shuffle($letras_e_numeros), $inicio, $max);
					$busca_codigo = Conexao::selecionar('*', 'usuarios', 'cod_email', '=', $codigo);
				}
				return $codigo;
			}
			return null;
		}
		// gera um código de confirmação de e-mail pendente único para cada usuário
		public static function gerarCodEmailPendente() {
			// opções para a geração do nome da página
			$letras_e_numeros =  "bZwCF8gSak4Ru1KVoz2yJXQrPTmi7Mc3BH0Lv5YtsWlOIDdfN6qGejAp9xUEnh";
			// quantidade de caracteres em $letras_e_numeros (62)
			$qtd = strlen($letras_e_numeros);
			// quantidade de caracteres no código que será gerado (4 = 13.388.280 códigos diferentes)
			$max = 4;
			// guardará um número aleatório de 0 a 58 ($qtd - $max)
			$inicio = rand(0, $qtd - $max);

			// pega 4 ($max) caracteres de $letras_e_numeros (depois de embaralhar)
			$codigo = substr(str_shuffle($letras_e_numeros), $inicio, $max);
			// faz uma busca no banco para checar se o $codigo gerado já existe
			$busca_codigo = Conexao::selecionar('*', 'usuarios', 'cod_email_pendente', '=', $codigo);
			if (!is_a($busca_codigo, 'PDOException')) {
				while ($busca_codigo->rowCount() > 0) {
					$codigo = substr(str_shuffle($letras_e_numeros), $inicio, $max);
					$busca_codigo = Conexao::selecionar('*', 'usuarios', 'cod_email_pendente', '=', $codigo);
				}
				return $codigo;
			}
			return null;
		}
		public static function enviarCodigoConfirmacao($usuario) {
			if (is_null($usuario['cod_email'])) {
				$codigo_confirmacao = self::gerarCodEmail();
				self::atualizarUsuario($usuario, 'cod_email', $codigo_confirmacao);
			} else {
				$codigo_confirmacao = $usuario['cod_email'];
			}

			$destinatario = $usuario['email'];
			$assunto = 'Confirmação de E-mail';
			$corpo = "Use <b>$codigo_confirmacao</b> como código de confirmação de e-mail da sua conta no ".SITE_TITULO.'.';

			$envio = Email::enviarEmail($destinatario, $assunto, $corpo);
				
			if ($envio) {
				return true;
			} else {
				return false;
			}
		}
		public static function enviarCodigoAlteracao($usuario) {
			if (is_null($usuario['cod_email_pendente'])) {
				$codigo_alteracao = self::gerarCodEmailPendente();
				self::atualizarUsuario($usuario, 'cod_email_pendente', $codigo_alteracao);
			} else {
				$codigo_alteracao = $usuario['cod_email_pendente'];
			}

			$destinatario = $usuario['email_pendente'];
			$assunto = 'Alteração de E-mail';
			$corpo = "Use <b>$codigo_alteracao</b> como código de confirmação da alteração de e-mail da sua conta no ".SITE_TITULO.'.';

			$envio = Email::enviarEmail($destinatario, $assunto, $corpo);
				
			if ($envio) {
				return true;
			} else {
				return false;
			}
		}
	}
?>
