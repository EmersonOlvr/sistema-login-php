<?php
	require_once('../classes/Usuario.php');
	require_once('../classes/Logs.php');

	// permissão mínima para acessar esta página
	$permissao_minima = 2;
	// se o usuário não estiver logado...
	if (!Usuario::estaLogado()) {
		$msg_log = Usuario::obterIPUsuario().' tentou acessar o Painel (página: logspainel)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a index
		header('Location: ' .SITE_URL. '/index.php');
		exit();
	// se o usuário estiver logado mas não tiver a permissão necessária para prosseguir...
	} elseif (Usuario::obterUsuarioPorSessao()['permissao'] < $permissao_minima) {
		$msg_log = Usuario::obterUsuarioPorSessao()['nome'].' tentou acessar o Painel sem permissão (página: logspainel)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a home
		header('Location: ' .SITE_URL. '/home.php');
		exit(); 
	} elseif (!Usuario::emailEstaAtivado(Usuario::obterUsuarioPorSessao())) {
		header('Location: ../confirmar_email.php');
		exit();
	} else {
		// faz uma busca e retorna todos os logs da tabela logs_painel
		$busca_logs = Conexao::selecionar('*', 'logs_painel', '', '', '');
		// se a busca retornar um erro...
		if (is_a($busca_logs, 'PDOException')) {
			// redireciona para a página index
			header('Location: index.php');
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Logs do Painel - Painel | <?php echo SITE_TITULO ?></title>
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
									<h3>Usuários</h3>
								</div>
								<div class="module-body table">
									<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped  display"
										width="100%">
										<thead>
											<tr>
												<th>ID</th>
												<th>Data</th>
												<th>Tipo</th>
												<th>Mensagem</th>
											</tr>
										</thead>
										<tbody>
										<?php foreach ($busca_logs as $linha) { ?>
											<tr class="odd gradeX">
												<td> <?php echo $linha['id'] ?> </td>
												<td style="width: 130px;"> <?php echo date('d/m/Y H:i:s', strtotime($linha['data'])) ?> </td>
												<td> <?php echo $linha['tipo'] ?> </td>
												<td> <?php echo $linha['mensagem'] ?> </td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<footer>
			<?php require_once('includes/footer.php') ?>
		</footer>
	</body>
</html>
