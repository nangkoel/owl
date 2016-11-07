<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/rpt.js></script>
<script language=javascript1.2>
function printTiket()
{
	notiket=document.getElementById('notiket').value;
	tujuan=document.getElementById('tujuan').options[document.getElementById('tujuan').selectedIndex].value;
	wbx=notiket.substr(0,1);
	if(notiket=='')
	{
		alert('Masukkan No.Tiket');
	}
	else
	{
		window.open(tujuan+'?TICKETNO='+notiket+'&IDWB='+wbx,'location=0','resizable=0','scrollbars=0','navigation bar=0','width=100','height=100');
	}
}
</script>	
<?php
include('master_mainMenu.php');
echo"<div align=left id=b style='width:250px;'>"; 
echo OPEN_BOX('','');
echo OPEN_THEME('Print Tiket');
echo OPEN_BOX();

echo"<table>
	   <tr><td>No.Tiket</td><td>:&nbsp;<input type=text id=notiket size=10 onkeypress=\"return tanpa_kutip(event);\"></td></tr>
		<tr><td>Barang</td><td><select id=tujuan>
		<option value='fpdf/kartu_timbang_cpo_pk_form.php'>CPO/PK/CK</option>
		<option value='fpdf/kartu_timbang_tbs_form.php'>TBS Internal</option>
		<option value='fpdf/kartu_timbang_tbs_form.php'>TBS External</option>
		<option value='fpdf/kartu_timbang_pengiriman_barang_form.php'>Pengiriman Lain</option>
		<option value='fpdf/kartu_timbang_lain_form.php'>Penerimaan Lain</option>
		</select></td></tr>
		</table>
		<table align=center> 
		 <tr><td><input type=button id=button1 tabindex='2' class=tombol2 value=PRINT onclick=printTiket();></td></tr>
		 </table>
     ";

echo CLOSE_BOX();
echo CLOSE_THEME();
echo CLOSE_BOX();
echo"</div>";

?>
