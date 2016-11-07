<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();

include('master_mainMenu.php');

open_box('width:800px;overflow:auto','INI TAB');


$head[0]='Database Option';
$head[1]='Script Backup';
$head[2]='Configuration';
$head[3]='Riwayat Pekerjaan';
$head[4]='Riwayat Sakit';
//$head[5]='Other';

$content[0]="<table border=1><tr><td>adasdasd</td><td>asdasdsad</td></tr></table>";
$content[1]="asdasdsad";
$content[2]="Nngkoel";
$content[3]="Nngkoel1";
$content[4]="Nngkoel2";

echo"<button onclick=lockScreen('progress')>asdsad</button>";
drawTab('nangkoel',$head,$content,200,800);
close_box();

drawTab('angkoel',array('test','satu'),array('ini satu','ini dua'),150,600);
echo close_body();
?>
