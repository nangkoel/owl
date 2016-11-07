<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/hr.js></script>
<script language=javascript1.2 src=js/blocker.js></script>
<script language=javascript1.2 src=js/generic.exe></script>
<?php
include('master_mainMenu.php');

$str="select * from ".$dbname.".mswilayah order by wilcode";
$res=mysql_query($str);
OPEN_BOX('');
echo OPEN_THEME('Master Data Wilayah:');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>Data Wilayah</b> Yang Sudah berhasil Disimpan Tidak Dapat Dihapus,<br>Harap Berhati2 dalam Melakukan Penambahan Data.Hanya Perubahan Data yang Diperkenankan.
     </fieldset>";
echo"<br>
    <input type=hidden value=simpan id=idx>
	<span id=note style='font-weight:bolder;'>Simpan</span> Wilayah :
    <br><br>
	<dd>Kode &nbsp;&nbsp;&nbsp;: &nbsp;<input type=text class=myinputtext id=newTitle size=4 maxlength=4 style='text-align:center' onkeypress='return charAndNum(event);'><br><br>
    Uraian &nbsp;:&nbsp;&nbsp;<input type=text class=myinputtext size=30 maxlength=40 id=name>
	<button class=mybutton onclick=simpanWilayah()>
	 Simpan
	 </button>
	<button class=mybutton onclick=batalWilayah()>
	 Batal
	 </button>
	 </dd>
	 <hr>";
echo"
<form onSubmit=cari()>
List Data Wilayah:
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit class=mybutton2 value=Cari>
</form>
     <span id=result>
     <div style='height:170px;overflow:auto'>
     <table width=500px class=sortable border=0 cellspacing=1>
     <thead>
	 <tr class=rowheader>
	 <td align=center>No.</td>
	 <td>Kode</td>
	 <td>Uraian</td><td>Edit</td></tr>
	 </thead>
	 <tbody>";
$no=0;
while($bar=mysql_fetch_object($res))
 {  $no+=1;

 	echo "<tr class=rowcontentClick>
	      <td class=firsttd align=center>".$no."</td>
		  <td align=center>".$bar->WILCODE."</td>
		  <td align=center>".$bar->WILNAME."</td>
		  <td align=center><img title='Click to edit' class=editbtn src=images/edit.png  onclick=\"changeWilayah('".$bar->WILCODE."','".$bar->WILNAME."');\"></td>
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
