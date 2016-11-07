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
echo OPEN_THEME('Konfigurasi System :');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>Konfigurasi System</b> Yang Sudah berhasil Disimpan Tidak Dapat Dihapus,<br>Harap Berhati2 dalam Melakukan Perubahan Data.Hanya Perubahan Data yang Diperkenankan.
     </fieldset>";
echo"<br>
	<table border=0>
	<tr><td>KODE PERUSAHAAN</td><td>:</td><td><input type=text id=COMPCODE size=3 onkeypress='return char_only(event);' style='text-align:center'  maxlength=4 value='".$COMPCODE."' disabled=true>-<input type=text id=COMPNAME size=40 value='".$COMPNAME."' disabled=true></td></tr>
	<tr><td>KODE MILL</td><td>:</td><td><input type=text id=MILLCODE size=3 onkeypress='return char_only(event);' style='text-align:left' maxlength=4 value='".$MILLCODE."' disabled=true>-<input type=text id=MILLNAME size=40 value='".$MILLNAME."' disabled=true></td></tr>
	<tr><td>NAMA MANAGER</td><td>:</td><td colspan=3><input type=text id=MNGRNAME size=20 onkeypress='return char_only(event);' style='text-align:left' maxlength=30 value='".$MGRNAME."' disabled=true>*Harus Diisi</td></tr>
	<tr><td>NAMA KASIE</td><td>:</td><td colspan=3><input type=text id=KTUNAME size=20 onkeypress='return char_only(event);' style='text-align:left' maxlength=30 value='".$KTUNAME."' disabled=true>*Harus Diisi</td>
	<tr><td>NAMA KRANI</td><td>:</td><td colspan=3><input type=text id=KRANINAME size=20 onkeypress='return char_only(event);' style='text-align:left' maxlength=30 value='".$KRANINAME."' disabled=true>*Harus Diisi</td></tr>
	<tr><td>PERIODE ANTAR KENDARAAN</td><td>:</td><td colspan=3><input type=text id=TIMEVEH size=2 onkeypress='return char_only(event);' style='text-align:center' maxlength=2 value='".$TIMEVEH."' disabled=true>&nbsp;Menit</td></tr>
    <tr><td>ID TIMBANGAN</td><td>:</td><td colspan=3><input type=text id=IDWB size=2 onkeypress='return char_only(event);' style='text-align:center' maxlength=2 value='".$IDWB."' disabled=true></td></tr>
	</table>
	<table align=center>
	<tr>
		<td><button class=mybutton id=simpan onclick=simpanSystem() disabled=true>Simpan</button></td>
		<td><button class=mybutton id=rubah onclick=ubahSystem()>Ubah</button></td>
		<td><button class=mybutton id=batal onclick='window.location.reload();'>Batal</button></td>
	</tr>
	</table>";




echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();
?>
