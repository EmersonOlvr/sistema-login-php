<?php
	require_once('Conexao.php');

	/**
	 * Cria a classe Logs, que contém tudo o que é necessário para gerenciar os logs de sucesso/erro do painel
	 * (tabela logs_painel)
	 */
	class Logs {
		/**
		 * Obtenções
		 */
		public static function obterLogsPainelSucesso() {
			$busca_logs_painel_sucesso = Conexao::selecionar('*', 'logs_painel', 'tipo', '=', 'Sucesso');
			if (!is_a($busca_logs_painel_sucesso, 'PDOException')) {
				return $busca_logs_painel_sucesso->fetch();
			}
			return null;
		}
		public static function obterLogsPainelErro() {
			$busca_logs_painel_erro = Conexao::selecionar('*', 'logs_painel', 'tipo', '=', 'Erro');
			if (!is_a($busca_logs_painel_erro, 'PDOException')) {
				return $busca_logs_painel_erro->fetch();
			}
			return null;
		}

		/**
		 * Inserções
		 */
		public static function inserirLogPainelSucesso($mensagem) {
			$insercao_log_painel_sucesso = Conexao::inserir('logs_painel', 'data, tipo, mensagem', [date('Y-m-d H:i:s'), 'Sucesso', $mensagem]);
			if (!is_a($insercao_log_painel_sucesso, 'PDOException')) {
				return true;
			}
			return false;
		}
		public static function inserirLogPainelErro($mensagem) {
			$insercao_log_painel_erro = Conexao::inserir('logs_painel', 'data, tipo, mensagem', [date('Y-m-d H:i:s'), 'Erro', $mensagem]);
			if (!is_a($insercao_log_painel_erro, 'PDOException')) {
				return true;
			}
			return false;
		}
	}
?>
