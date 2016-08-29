<?php
/*
|--------------------------------------------------------------------------
| Administrator email
|--------------------------------------------------------------------------
|
| this is the email whereby the application will send emails and  
| whereby it will send things. Misael Burboa
*/
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'smtp.tpitic.com.mx';
$config['smtp_port'] = '25';
$config['smtp_user'] = 'cmburboa@tpitic.com.mx';
$config['smtp_pass'] = '14705780'; //change this
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['wordwrap'] = TRUE;
$config['newline'] = "\r\n";
?>