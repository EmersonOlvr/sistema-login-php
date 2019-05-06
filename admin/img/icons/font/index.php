<?php
	// impede que alguém acesse este diretório (só o diretório, os arquivos não)
	// por exemplo, se o intruso souber o nome do arquivo ele conseguirá vê-lo
	header('Location: ../index.php');
	exit();
?>