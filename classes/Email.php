<?php
	require_once('Conexao.php');
	require_once('phpmailer/PHPMailer.php');
	require_once('phpmailer/SMTP.php');
	require_once('phpmailer/Exception.php');

	class Email {
		// Configurações do envio de E-mail
		private static $remetente = 		'exemplo@gmail.com'; // E-mail usado para enviar e-mails
		private static $remetente_senha = 	'senha'; // Senha do e-mail acima
		private static $remetente_nome = 	SITE_TITULO; // Nome do rementente (Ex.: Sistema de Login)

		public static function enviarEmail($destinatario, $assunto, $corpo) {
			try {
				$mail = new PHPMailer\PHPMailer\PHPMailer();
				// Configurações do servidor
				$mail->SMTPDebug = 0;										// Debugar: 1 = erros e mensagens, 2 = mensagens apenas
				$mail->isSMTP();											// Define mailer para usar SMTP
				$mail->Host = 'smtp.gmail.com';								// Especifica o servidor SMTP principal
				$mail->SMTPAuth = true;										// Ativa autenticação SMTP
				$mail->Username = self::$remetente;							// Nome de usuário SMTP
				$mail->Password = self::$remetente_senha;					// Senha SMTP
				$mail->SMTPSecure = 'tls';									// Ativa criptografia TLS, também aceita `ssl`
				$mail->Port = 587;											// Porta TCP para conectar
				$mail->setLanguage('pt_br');

				// Remetente
				$mail->setFrom(self::$remetente, self::$remetente_nome);	// E-mail do Rementente, Nome do Remetente (opcional)

				// Destinatário(s)
				$mail->addAddress($destinatario);							// Adiciona um destinatário (pode ter mais de um)
				
				// Conteúdo
				$mail->isHTML(true);										// Define o formato do e-mail como HTML
				$mail->CharSet = 'UTF-8';									// Define o CharSet como UTF-8
				$mail->Subject = $assunto;									// Assunto do e-mail que será enviado
				$mail->Body	= $corpo;										// Corpo do e-mail que será enviado
				
				$mail->send();												// Por fim, envia o e-mail
				return true;												// Tudo deu certo, retorna true
			} catch (Exception $e) {
				return false;												// Algo deu errado, retorna false
			}
		}
	}
?>