<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/trx.js></script>
<script language=javascript1.2 src=js/blocker.js></script>
<script language=javascript1.2 src=js/generic.exe></script>
<?php
include('master_mainMenu.php');

//$str="select * from ".$dbname.".bagian order by kd_seksi";
$str="select * from ".$dbname.".bpssvr order by addr";
$res=mysql_query($str);
$stg="select * from ".$dbname.".mswilayah order by WILCODE";
$reg=mysql_query($stg);
$opt_wil="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_object($reg))
		{
			$opt_wil.="<option value='".$bag->WILCODE."'>".$bag->WILNAME."</option>";
		}
$xcv="select * from ".$dbname.".mscompany order by WILCODE";
$rex=mysql_query($xcv);
$opt_comp="<option value='0'>Silakan Pilih...</option>";
	/*while($bag=mysql_fetch_object($rex))
		{
			$opt_comp.="<option value='".$bag->COMPCODE."'>".$bag->COMPNAME."</option>";
		}*/
OPEN_BOX('');
echo OPEN_THEME('Setting Server Jakarta:');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>IP Server</b> Yang Sudah berhasil Disimpan Tidak Dapat Dihapus,<br>Harap Berhati2 dalam Melakukan Konfigurasi Server.Hanya Perubahan Data yang Diperkenankan.
     </fieldset>";
echo"<br>
    <input type=hidden value=simpan id=idx>
	<span id=note style='font-weight:bolder;'>Simpan</span> IP Server Jakarta :
    <br><br>
	<dd>Wilayah &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<select id=wilayah>".$opt_wil."</select><br><br>
    IP Addr &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=text class=myinputtext size=25 maxlength=25 id=ip><br><br>
    Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=text class=myinputtext size=25 maxlength=25 id=name><br><br>
    Port &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=text class=myinputtext size=4 maxlength=4 id=port>
	<button class=mybutton onclick=saveIP()>Simpan</button>
	<button class=mybutton onclick=clearIP()>Batal</button>
	 </dd>
	 <hr>";
echo"
<form onSubmit=cari()>
List Data IP Server:
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit class=mybutton2 value=Cari>
</form>
     <span id=result>
     <div style='height:170px;overflow:auto'>
     <table width=500px class=sortable border=0 cellspacing=1 >
     <thead>
	 <tr class=rowheader>
	 <td align=center>No.</td>
	 <td>Wilayah</td>
	 <td>IP Server</td>
	 <td>Nama Komputer</td>
	 <td>Port</td>
	 <td>Edit</td></tr>
	 </thead>
	 <tbody>";
$no=0;
while($bar=mysql_fetch_object($res))
 {  $no+=1;
 	echo "<tr class=rowcontentClick>
	      <td class=firsttd align=center>".$no."</td>
		  <td align=center>".$bar->wilayah."</td>
		  <td align=center>".$bar->addr."</td>
		  <td align=center>".$bar->name."</td>
		  <td align=center>".$bar->port."</td>
		  <td align=center><img title='Click to edit' class=editbtn src=images/edit.png  onclick=\"editIP('".$bar->wilayah."','".$bar->addr."','".$bar->name."','".$bar->port."');\"></td>
		  </tr>
		 ";
 }

echo"</tbody>
     </table>
     </div>
	 </span>";

echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();
?>


?>

</body>

</html>