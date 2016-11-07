<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/hr.js></script>
<script language=javascript1.2 src=js/trx.js></script>
<script language=javascript1.2 src=js/generic.js></script>
<?php
include('master_mainMenu.php');

$str="select * from ".$dbname.".mssystem order by MILLCODE";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
	$COMPCODE=$bar->COMPCODE;$MNGRNAME=$bar->MNGRNAME;
	$COMPNAME=$bar->COMPNAME;$KTUNAME=$bar->KTUNAME;
	$MILLCODE=$bar->MILLCODE;$KRANINAME=$bar->KRANINAME;
	$MILLNAME=$bar->MILLNAME;$TIMEVEH=$bar->TIMEVEH;$IDWB=$bar->IDWB;
}
OPEN_BOX('');
echo OPEN_THEME('Hapus No. Tiket :');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>No. Tiket</b> Yang Sudah Dihapus Tidak Dapat Dikembalikan Lagi Datanya,<br>Harap Berhati2 dalam Melakukan Penghapusan No.Tiket.
     </fieldset>";
echo"<br>
	<table border=0 align=center>
	<tr><td>Masukkan No. Tiket</td><td>:</td><td colspan=3><input type=text id=TICKETNO size=5 onkeypress='return charAndNum(event);' style='text-align:left' maxlength=7></td></tr>
	</table>
	<table align=center>
	<tr>
		<td><button class=mybutton id=hapusT onclick=hapusTiket()>Hapus</button></td>
		<td><button class=mybutton id=batal onclick='window.location.reload();'>Batal</button></td>
	</tr>
	</table>";




echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();
?>
