<?php
	require_once('Usuario.php');

	/**
	 * cria a classe RegistroUsuario, que cadastra novos usuários no banco de dados
	 */
	class Registro {
		private static $erro = 'Erro desconhecido.';

		// registro de novo usuário
		public static function registrar($nome, $email, $senha, $senhaRepetida) {
			// faz uma série de validações nos parâmetros recebidos...
			if (empty($nome)) {
				self::$erro = 'Insira um nome.';
				return false;
			} elseif (strlen($nome) < 2 || strlen($nome) > 64) {
				self::$erro = 'Nome inválido. Mínimo 2 e máximo de 64 caracteres.';
				return false;
			} elseif (!preg_match('/^[a-z\d]{2,64}$/i', $nome)) {
				self::$erro = 'Nome inválido. Somente letras e números são permitidos.';
				return false;
			} elseif (empty($email)) {
				self::$erro = 'Insira um e-mail.';
				return false;
			} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				self::$erro = 'E-mail em formato inválido.';
				return false;
			} elseif (strlen($email) > 64) {
				self::$erro = 'E-mail muito grande. Máximo de 64 caracteres.';
				return false;
			} elseif (empty($senha)) {
				self::$erro = 'Insira uma senha.';
				return false;
			} elseif (strlen($senha) < 6) {
				self::$erro = 'Senha muito curta. Mínimo de 6 caracteres.';
				return false;
			} elseif (empty($senhaRepetida)) {
				self::$erro = 'Por favor, repita a senha.';
				return false;
			} elseif ($senha !== $senhaRepetida) {
				self::$erro = 'As senhas não conferem.';
				return false;
			} else {
				$email = strtolower($email); // deixa o $email minúsculo
				$hash_senha = password_hash($senha, PASSWORD_DEFAULT); // cria um hash aleatório da senha passada por parâmetro
				$ip = filter_var(Usuario::obterIPUsuario(), FILTER_VALIDATE_IP); // pega o IP do usuário
				$cod_email = Usuario::gerarCodEmail(); // gera um código de confirmação de e-mail único

				$busca_nome = Conexao::selecionar('*', 'usuarios', 'nome', '=', $nome);
				$busca_email = Conexao::selecionar('*', 'usuarios', 'email', '=', $email);
				if (!is_a($busca_nome, 'PDOException') && !is_a($busca_email, 'PDOException')) {
					if ($busca_nome->rowCount() > 0 || $busca_email->rowCount() > 0) {
						self::$erro = 'O nome/e-mail inserido já está em uso!';
						return false;
					}
				}

				// insere o usuário no banco
				$insercao = Conexao::inserir('usuarios', 
													'nome, email, senha, registro_data, registro_ip, ultimo_acesso, ultimo_ip, cod_email', 
													[$nome, $email, $hash_senha, date('Y-m-d H:i:s'), $ip, date('Y-m-d H:i:s'), $ip, $cod_email]
													);

				// se a insercao acima não retornar uma excessão...
				if (!is_a($insercao, 'PDOException')) {
					// envia o código de confirmação para o e-mail usado no registro
					Usuario::enviarCodigoConfirmacao(Usuario::obterUsuarioPorEmail($email));
					return true;
				} else {
					/*// errorInfor[0] contém informações de erro sobre a consulta SQL recém-executada
					// 23000 é um código de erro quando há os mesmos dados na coluna definida como única
					if ($insercao->errorInfo[0] == 23000) {
						self::$erro = 'O nome/e-mail inserido já está em uso!';
						return false;
					}*/
					self::$erro = $insercao->getMessage();
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
