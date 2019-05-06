<?php
	require_once('../classes/Usuario.php');
	require_once('../classes/Logs.php');
	
	// permissão mínima para acessar esta página
	$permissao_minima = 2;
	// se o usuário não estiver logado...
	if (!Usuario::estaLogado()) {
		$msg_log = Usuario::obterIPUsuario().' tentou acessar o Painel (página: usuario)';
		Logs::inserirLogPainelErro($msg_log);
		// redireciona para a Index
		header('Location: ' .SITE_URL. '/index.php');
		exit();
	// se o usuário estiver logado mas não tiver a permissão necessária para prosseguir...
	} elseif (Usuario::obterUsuarioPorSessao()['permissao'] < $permissao_minima) {
		$msg_log = Usuario::obterUsuarioPorSessao()['nome'].' tentou acessar o Painel sem permissão (página: usuario)';
		Logs::inserirLogPainelErro($msg_log);
		// ...redireciona para a Home
		header('Location: ' .SITE_URL. '/home.php');
		exit();
	} elseif (!Usuario::emailEstaAtivado(Usuario::obterUsuarioPorSessao())) {
		header('Location: ../confirmar_email.php');
		exit();
	// valida a variável id recebida via GET, se a validação retornar false...
	} elseif (!filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {
		// ...redireciona para a página de procurar usuário
		header('Location: ' .SITE_URL. '/admin/procurarusuario.php?id_invalido&origem=usuario');
		exit();
	} else {
		// já que deu tudo certo na validação, o valor recebido via GET é atribuido a sua respectiva variável
		$id = $_GET['id'];
		$usuario = Usuario::obterUsuarioPorId($id);
		if ($usuario == null) {
			// redireciona para a página procurarusuario
			header('Location: ' .SITE_URL. '/admin/procurarusuario.php?usuario_inexistente&id=' .$id. '&origem=usuario');
			exit();
		}

		// se receber via GET alguma das variáveis a seguir...
		if (isset($_GET['atualizado'])) {
			$sucesso = 'Usuário atualizado com sucesso!';
		} elseif (isset($_GET['erro_id'])) {
			$erro = 'Não foi possível atualizar o <b>ID</b>.';
		} elseif (isset($_GET['erro_nome'])) {
			$erro = 'Não foi possível atualizar o <b>Nome</b>.';
		} elseif (isset($_GET['erro_email'])) {
			$erro = 'Não foi possível atualizar o <b>E-mail</b>.';
		} elseif (isset($_GET['erro_permissao'])) {
			$erro = 'Não foi possível atualizar a <b>Permissão</b>.';
		} elseif (isset($_GET['id_invalido'])) {
			$erro = '<b>ID</b> inválido.';
		} elseif (isset($_GET['nome_invalido'])) {
			$erro = '<b>Nome</b> inválido.';
		} elseif (isset($_GET['email_invalido'])) {
			$erro = '<b>E-mail</b> inválido.';
		} elseif (isset($_GET['email_ativado_invalido'])) {
			$erro = '<b>E-mail ativado</b> inválido.';
		} elseif (isset($_GET['permissao_invalida'])) {
			$erro = '<b>Permissão</b> inválida.';
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php require_once('includes/head.php') ?>
		<title>Editando o usuário: <?php echo $usuario['nome'] ?> - Painel | <?php echo SITE_TITULO ?></title>
		<script type="text/javascript">
			function desativar(field) {
				field.disabled = true;
			}
		</script>
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
									<h3>Editando o usuário: <?php echo $usuario['nome'] ?></h3>
								</div>
								<div class="module-body">
									<!-- verifica se a variável $erro existe (ela é criada quando recebe algum erro via GET) -->
									<?php if(isset($erro)) { ?>
										<div class="alert alert-error">
											<button type="button" class="close" data-dismiss="alert">×</button>
											<strong>Erro!</strong> <?php echo $erro ?>
										</div>
									<?php } ?>
									<!-- verifica se a variável $sucesso existe (ela é criada quando recebe 'atualizado' via GET) -->
									<?php if(isset($sucesso)) { ?>
										<div class="alert alert-success">
											<button type="button" class="close" data-dismiss="alert">×</button>
											<strong><?php echo $sucesso ?></strong>
										</div>
									<?php } ?>

									<form method="POST" action="util/editarUsuario.php" class="form-horizontal row-fluid" onsubmit="desativar(this.btn_atualizar)">
										<input type="hidden" name="id_atual" value="<?php echo $usuario['id'] ?>" required>
										<input type="hidden" name="local" value="usuario">

										<div class="control-group">
											<label class="control-label" for="id">ID</label>
											<div class="controls">
												<input type="number" class="span8" id="id" name="id" min="1" value="<?php echo $usuario['id'] ?>" placeholder="Digite um ID..." required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="nome">Nome</label>
											<div class="controls">
												<input type="text" class="span8" id="nome" name="nome" value="<?php echo $usuario['nome'] ?>" placeholder="Digite um nome..." required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="email">E-mail</label>
											<div class="controls">
												<input type="email" class="span8" id="email" name="email" value="<?php echo $usuario['email'] ?>" placeholder="Digite um e-mail..." required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="email_ativado">Email ativado</label>
											<div class="controls">
												<select class="span8" name="email_ativado">
													<option value=1 <?php if ($usuario['email_ativado'] == 1) {echo 'selected';} ?>>Sim</option>
													<option value=0 <?php if ($usuario['email_ativado'] == 0) {echo 'selected';} ?>>Não</option>
												</select>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="cod_email">Código do e-mail</label>
											<div class="controls">
												<?php if ($usuario['cod_email'] == null) {
													$cod_email =  'Nenhum (e-mail já ativado)';
												} else {
													$cod_email = $usuario['cod_email'];
												} ?>
												<input type="text" class="span8" id="cod_email" placeholder="<?php echo $cod_email ?>" disabled>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="email_pendente">E-mail pendente</label>
											<div class="controls">
												<?php if ($usuario['email_pendente'] == null) {
													$email_pendente = 'Nenhum';
												} else {
													$email_pendente = $usuario['email_pendente'];
												} ?>
												<input type="text" class="span8" id="email_pendente" placeholder="<?php echo $email_pendente ?>" disabled>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="cod_email_pendente">Código do e-mail pendente</label>
											<div class="controls">
												<?php if ($usuario['cod_email_pendente'] == null) {
													$cod_email_pendente =  'Nenhum';
												} else {
													$cod_email_pendente = $usuario['cod_email_pendente'];
												} ?>
												<input type="text" class="span8" id="cod_email_pendente" placeholder="<?php echo $cod_email_pendente ?>" disabled>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="permissao">Permissão</label>
											<div class="controls">
												<input type="number" class="span8" id="permissao" name="permissao" min="1" value="<?php echo $usuario['permissao'] ?>" placeholder="Digite uma Permissão (1-x)..." required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="registro_data">Registro Data</label>
											<div class="controls">
												<input type="number" class="span8" id="registro_data" min="1" placeholder="<?php echo date('d/m/Y H:i:s', strtotime($usuario['registro_data'])); ?>" disabled>
											</div>
										</div>
										
										<div class="control-group">
											<label class="control-label" for="registro_ip">Registro IP</label>
											<div class="controls">
												<input type="number" class="span8" id="registro_ip" min="1" placeholder="<?php echo $usuario['registro_ip'] ?>" disabled>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="ultimo_acesso">Último Acesso</label>
											<div class="controls">
												<input type="number" class="span8" id="ultimo_acesso" min="1" placeholder="<?php echo date('d-m-Y H:i:s', strtotime($usuario['ultimo_acesso'])) ?>" disabled>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="ultimo_ip">Último IP</label>
											<div class="controls">
												<input type="number" class="span8" id="ultimo_ip" min="1" placeholder="<?php echo $usuario['ultimo_ip'] ?>" disabled>
											</div>
										</div>

										<div class="control-group">
											<div class="controls">
												<button type="submit" class="btn btn-primary" name="btn_atualizar">Atualizar</button>
											</div>
										</div>
									</form>
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
