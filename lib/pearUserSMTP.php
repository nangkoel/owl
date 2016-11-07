<?php
  $smtp		="192.168.1.100";
  $sslsmtp	='';
  $port		='25';
  $username	='system@minanga.co.id';
  $password	='system';
  $timeout	=NULL;
  $auth		=true;
//=================================  
// just set above  
  $arrsmtp=Array(//thie is standard smpt with auth
  	"host"		=>$smtp,
	"port"		=>$port,
	"auth"		=>$auth,
	"username"	=>$username,
	"password"	=>$password,
	"timeout"	=>$timeout
  );
  
  $arrsmtpSSL=Array(////thie is standard smpt with SSL auth
  	"host"		=>'ssl://'.$smtp,
	"port"		=>$port,
	"auth"		=>$auth,
	"username"	=>$username,
	"password"	=>$password,
	"timeout"	=>$timeout	
  );  
?>
