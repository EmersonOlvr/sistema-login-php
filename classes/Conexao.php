<?php
	/*
	 * Use este script para definir o domínio, o nome do seu site, configurar a conexão com o banco de dados e
	 * as configurações do envio de e-mail
	 */

	// Configurações do site
	define('SITE_URL', 		'http://localhost/sistema');
	define('SITE_TITULO', 	'Sistema de Login');

	// inicia a sessão (vai ser extremamente necessário para os arquivos que requirirem este, exemplo: Login.php)
	session_start();

	/*
	 * Cria a classe Conexao, que contém tudo o que é necessário para conectar, inserir, atualizar, deletar e 
	 * obter dados do banco de dados
	 */
	class Conexao {
		// Configurações do banco de dados
		private static $bd_host = 		'localhost'; // geralmente é: localhost
		private static $bd_nome = 		'sistema'; // nome do seu banco de dados
		private static $bd_usuario = 	'root'; // geralmente é: root
		private static $bd_senha = 		'senha'; // senha do usuário acima

		public static function conectar_bd() {
			// tenta...
			try {
				// ...instânciar o objeto PDO para se conectar ao banco
				$conexao = new PDO("mysql:host=" .self::$bd_host. "; dbname=" .self::$bd_nome. ";charset=utf8", self::$bd_usuario, self::$bd_senha);
				$conexao->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				// retorna o objeto
				return $conexao;
			// se der erro...
			} catch (PDOException $exception) {
				throw new PDOException($exception->getMessage());
			}
		}

		private static function gerar_interrogacoes($array) {
			$interrogacoes = '';

			for ($i = 0; $i < count($array); $i++) {
				if ($i == count($array)-1) {
					$interrogacoes .= '?';
				} else {
					$interrogacoes .= '?, ';
				}
			}

			return $interrogacoes;
		}
		public static function inserir($tabela, $atributo, $valor) {
			// chama a função gerar_interrogacoes(), para gerar as interrogacoes necessárias para a função prepare() do PDO (logo abaixo)
			$interrogacoes = Conexao::gerar_interrogacoes($valor);
			// testa a conexão primeiro, se não retornar uma excessão...
			if (!is_a(Conexao::conectar_bd(), 'PDOException')) {
				// ...prepara a statement
				$statement = Conexao::conectar_bd()->prepare("INSERT INTO $tabela($atributo) VALUES ($interrogacoes);");
				
				for ($i=0; $i < count($valor); $i++) {
					if (strtoupper($valor[$i]) == 'NULL') {
						// substitui os valores vagos da statement (que nesse caso são as interrogações)
						$statement->bindValue($i+1, null, PDO::PARAM_NULL);
					} else {
						// substitui os valores vagos da statement (que nesse caso são as interrogações)
						$statement->bindParam($i+1, $valor[$i]);
					}
				}

				// tenta...
				try {
					// ...executar a statement
					$statement->execute();
					return $statement;
				// se der erro...
				} catch (PDOException $exception) {
					// ...retorna a excessão, para que ela possa ser tratada por quem chamou esta função
					return $exception;
				}
			} else {
				// retorna a excessão que foi retornada na função conectar_bd(), para ser tratada por quem chamou esta função
				return Conexao::conectar_bd();
			}
		}
		public static function atualizar($tabela, $atributo, $novoValor, $atributoCondicao = '', $condicao = '', $valorCondicao = '') {
			// testa a conexão primeiro, se não retornar uma excessão...
			if (!is_a(Conexao::conectar_bd(), 'PDOException')) {
				// se passar $atributoCondicao, $condicao e $valorCondicao como parâmetros...
				if (!empty($atributoCondicao) && !empty($condicao) && !empty($valorCondicao)) {
					// ...prepara a statement
					$statement = Conexao::conectar_bd()->prepare("UPDATE $tabela SET $atributo = :novoValor 
																WHERE $atributoCondicao $condicao :valorCondicao");
					if (strtoupper($novoValor) == 'NULL') {
						$statement->bindValue(':novoValor', null, PDO::PARAM_NULL);
					} else {
						$statement->bindParam(':novoValor', $novoValor);
					}
					if (strtoupper($valorCondicao) == 'NULL') {
						$statement->bindValue(':valorCondicao', null, PDO::PARAM_NULL);
					} else {
						$statement->bindParam(':valorCondicao', $valorCondicao);
					}
					// tenta...
					try {
						// ...executar a statement
						$statement->execute();
						return $statement;
					// se der erro...
					} catch (PDOException $exception) {
						// ...retorna a excessão, para que ela possa ser tratada por quem chamou esta função
						return $exception;
					}
				} else {
					// prepara a statement
					$statement = Conexao::conectar_bd()->prepare("UPDATE $tabela SET $atributo = :novoValor");
					if (strtoupper($novoValor) == 'NULL') {
						$statement->bindValue(':novoValor', null, PDO::PARAM_NULL);
					} else {
						$statement->bindParam(':novoValor', $novoValor);
					}

					// tenta...
					try {
						// ...executar a statement
						$statement->execute();
						return $statement;
					// se der erro...
					} catch (PDOException $exception) {
						// ...retorna a excessão, para que ela possa ser tratada por quem chamou esta função
						return $exception;
					}
				}
			} else {
				// retorna a excessão que foi retornada na função conectar_bd(), para ser tratada por quem chamou esta função
				return Conexao::conectar_bd();
			}
		}
		public static function deletar($tabela, $atributo = '', $condicao = '', $valor = '') {
			// testa a conexão primeiro, se não retornar uma excessão...
			if (!is_a(Conexao::conectar_bd(), 'PDOException')) {
				// se passar $atributo, $condicao e $valor como parâmetros...
				if (!empty($atributo) && !empty($condicao) && !empty($valor)) {
					// ...prepara a statement
					$statement = Conexao::conectar_bd()->prepare("DELETE FROM $tabela WHERE $atributo $condicao :valor");
					if (strtoupper($valor) == 'NULL') {
						$statement->bindValue(':valor', null, PDO::PARAM_NULL);
					} else {
						$statement->bindParam(':valor', $valor);
					}

					// tenta...
					try {
						// ...executar a statement
						$statement->execute();
						return $statement;
					// se der erro...
					} catch (PDOException $exception) {
						// ...retorna a excessão, para que ela possa ser tratada por quem chamou esta função
						return $exception;
					}
				} else {
					// tenta...
					try {
						// ...preparar a query (a tabela será esvaziada)
						$query = Conexao::conectar_bd()->query("TRUNCATE $tabela");
						// retorna a quantidade de linhas afetadas
						return $query;
					// se der erro...
					} catch (PDOException $exception) {
						// ...retorna a excessão, para que ela possa ser tratada por quem chamou esta função
						return $exception;
					}
				}
			} else {
				// retorna a excessão que foi retornada na função conectar_bd(), para ser tratada por quem chamou esta função
				return Conexao::conectar_bd();
			}
		}
		public static function selecionar($atributo, $tabela, $atributoCondicao = '', $condicao = '', $valorCondicao = '') {
			// testa a conexão primeiro, se não retornar uma excessão...
			if (!is_a(Conexao::conectar_bd(), 'PDOException')) {
				// se passar $atributoCondicao, $condicao e $valorCondicao como parâmetros...
				if (!empty($atributoCondicao) && !empty($condicao) && !empty($valorCondicao)) {
					// tenta...
					try {
						// ...preparar a statement
						$statement = Conexao::conectar_bd()->prepare("SELECT $atributo FROM $tabela WHERE $atributoCondicao $condicao :valorCondicao");
						if (strtoupper($valorCondicao) == 'NULL') {
							$statement->bindValue(':valorCondicao', null, PDO::PARAM_NULL);
						} else {
							$statement->bindParam(':valorCondicao', $valorCondicao);
						}
						// executa a statement
						$statement->execute();
						// retorna a statement
						return $statement;
					// se der erro...
					} catch (PDOException $exception) {
						// ...retorna a excessão, para que ela possa ser tratada por quem chamou esta função
						return $exception;
					}
				// senão...
				} else {
					// tenta...
					try {
						// ...preparar a query
						$query = Conexao::conectar_bd()->query("SELECT $atributo FROM $tabela");
						// retorna a query, para que ela possa ser usada no script que chamou esta função
						return $query;
						// um exemplo de uso seria: ...selecionar('tabela', 'atributo')->rowCount()
						// o exemplo acima retorna a quantidade de linhas que foram afetadas com a execução da query
					// se der erro...
					} catch (PDOException $exception) {
						// ...retorna a excessão, para que ela possa ser tratada por quem chamou esta função
						return $exception;
					}
				}
			} else {
				// retorna a excessão que foi retornada na função conectar_bd(), para ser tratada por quem chamou esta função
				return Conexao::conectar_bd();
			}
		}
	}
?>
