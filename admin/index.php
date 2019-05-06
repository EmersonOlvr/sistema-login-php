<?php
	require_once('../classes/Usuario.php');
	require_once('../classes/Logs.php');

	// permissão mínima para acessar esta página
	$permissao_minima = 2;
	// se o usuário não estiver logado...
	if (!Usuario::estaLogado()) {
		$msg_log = Usuario::obterIPUsuario().' tentou acessar o Painel (página: index)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a index
		header('Location: ' .SITE_URL. '/index.php');
		exit();
	// se o usuário estiver logado mas não tiver a permissão necessária para prosseguir...
	} elseif (Usuario::obterUsuarioPorSessao()['permissao'] < $permissao_minima) {
		$msg_log = Usuario::obterUsuarioPorSessao()['nome'].' tentou acessar o Painel sem permissão (página: index)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a home
		header('Location: ' .SITE_URL. '/home.php');
		exit();
	} elseif (!Usuario::emailEstaAtivado(Usuario::obterUsuarioPorSessao())) {
		header('Location: ../confirmar_email.php');
		exit();
	} else {
		// busca todos os usuários do banco de dados e armazena o resultado em $busca
		$busca = Conexao::selecionar('*', 'usuarios', '', '', '');
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Início - Painel | <?php echo SITE_TITULO ?></title>
	</head>
	<body>
		<?php require_once('includes/navbar-top.php') ?>
		<div class="wrapper">
			<div class="container">
				<div class="row">
					<?php require_once('includes/navbar-left.php') ?>
					<div class="span9">
						<div class="content">
							<div class="btn-controls">
								<div class="btn-box-row row-fluid">
									<a href="#" class="btn-box big span4">
										<i class=" icon-random"></i>
										<b>65%</b>
										<p class="text-muted">Growth</p>
									</a>
									<a href="usuarios.php" class="btn-box big span4">
										<i class="icon-user"></i>
										<!-- verifica a quantidade de linhas (ou seja, usuários) que foram retornadas na $busca -->
										<b><?php echo $busca->rowCount() ?></b>
										<p class="text-muted">Usuários registrados</p>
									</a>
									<a href="#" class="btn-box big span4"><i class="icon-money"></i><b>15,152</b>
										<p class="text-muted">Profit</p>
									</a>
								</div>
							</div>
							<div class="module">
								<div class="module-head">
									<h3>Todos os usuários</h3>
								</div>
								<div class="module-body table">
									<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped  display"
										width="100%">
										<thead>
											<tr>
												<th>ID</th>
												<th>Nome</th>
												<th>Email</th>
												<th>Ativado</th>
												<th>Permissão</th>
												<th>Último acesso</th>
											</tr>
										</thead>
										<tbody>
										<?php foreach ($busca as $coluna) { ?>
											<tr class="odd gradeX">
												<td> <?php echo $coluna['id'] ?> </td>
												<td> <?php echo $coluna['nome'] ?> </td>
												<td> <?php echo $coluna['email'] ?> </td>
												<?php if ($coluna['email_ativado'] == 1) {
													$email_ativado =  'Sim';
												} else {
													$email_ativado = 'Não';
												} ?>
												<td> <?php echo $email_ativado ?> </td>
												<td> <?php echo $coluna['permissao'] ?> </td>
												<td> <?php echo date('d/m/Y H:i:s', strtotime($coluna['ultimo_acesso'])) ?> </td>
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
