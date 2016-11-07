<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

echo"<script language=javascript1.2 src=\"js/generic.js\"></script>
<script language=javascript1.2 src=\"js/kebun_operasional.js\"></script>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zSearch.js></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
";

$notrans = $_GET['notransaksi'];
$kodeorg = $_GET['kodeorg'];

OPEN_BOX();
echo'<fieldset style="float: left;">';
echo'<legend><b>'.$kodeorg.' - '.$notrans.'</b></legend>';
echo'<table cellspacing="1" border="0" >';

echo"<tr class=myinputtext>
        <td>".$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['sisip']."</td>
        <td><input type='text' id='jumlah' name='jumlah' class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=30 style='height:20px; width:150px;' /></td>
      </tr>    
      <tr class=myinputtext>
        <td><label>".$_SESSION['lang']['penyebab']." ".$_SESSION['lang']['sisip']."</label></td>
        <td><textarea rows=2 style='width:150px' id='penyebab' name='penyebab' onkeypress=\"return tanpa_kutip(event);\" /></textarea></td>
      </tr>    
        <tr class=myinputtext>
        <td colspan=2>
            <hidden id='notrans' name='notrans' value='".$notrans."'/>
            <hidden id='kodeorg' name='kodeorg' value='".$kodeorg."'/>
            <hidden id='progress' name='progress' value=''/>
            <button class=mybutton id='simpan' onclick=saveSisip()>".$_SESSION['lang']['save']."</button>
        </td>
        </tr>      ";

echo'</table>';

CLOSE_BOX();   
       
?>
