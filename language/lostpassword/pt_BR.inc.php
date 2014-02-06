<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_base_language_file('lostpassword'));

$lang['lostpassword']['success']='<h1>Senha alterada</h1><p>Sua senha foi alterada com sucesso. Agora você pode continuar para a página de login.</p>';
$lang['lostpassword']['send']='Enviar';
$lang['lostpassword']['login']='Login';

$lang['lostpassword']['lost_password_subject']='Nova senha solicitada';
$lang['lostpassword']['lost_password_body']='%s,

Você solicitou uma nova senha para %s. Seu nome de usuário é "%s".

Clique no link abaixo (ou copie e cole em um browser) para alterar sua senha:

%s

Se você não solicitou uma nova senha por favor ignore esta mensagem.';

$lang['lostpassword']['lost_password_error']='Não foi possível encontrar o endereço de e-mail solicitado.';
$lang['lostpassword']['lost_password_success']='Um e-mail foi enviado para seu endereço, com instruções sobre como alterar a senha.';

$lang['lostpassword']['enter_password']='Por favor digite uma nova senha';

$lang['lostpassword']['new_password']='Nova senha';
$lang['lostpassword']['lost_password']='Senha esquecida';

$lang['lostpassword']['confirm_password']='Confirme a senha';
?>
