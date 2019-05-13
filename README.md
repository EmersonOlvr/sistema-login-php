# Sistema de Login com PHP
Sistema de Login escrito em PHP que faz uso do PDO para conectar ao banco de dados. Com um visual agradável usando Bootstrap 4, este sistema de login pode facilmente ser implementado em qualquer projeto ou ser usado como base para novos. [Capturas de tela](https://github.com/EmersonOlvr/sistema-login-php/tree/master/screenshots).

![Tela de Login](screenshots/login_m.png)

## Características
- Registro de conta
- Confirmação de e-mail com código (usando o [PHPMailer](https://github.com/PHPMailer/PHPMailer) para o envio de e-mails)
- Login na conta
- Ajustes na conta (alteração de nome, e-mail e/ou senha)
- Gerenciamento de contas de usuários atráves do Painel Administrativo
- Confirmação de saída (logout)
 
## Configuração
Crie um banco de dados no MySQL e em seguida importe as tabelas necessárias localizadas em [sql/sistema.sql](sql/sistema.sql). Após isso configure a conexão com o banco de dados definindo o host, nome do banco, usuário e senha que você utiliza para acessar o MySQL no arquivo [classes/Conexao.php](classes/Conexao.php). Por último defina o e-mail e senha da conta que será usada para enviar códigos de confirmação para todas as contas novas que forem registradas.
 
Fácil, não? Após isso o Sistema de Login estará pronto para ser usado em seu projeto.
