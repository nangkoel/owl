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

$str="select * from ".$dbname.".msvendortrp order by TRPCODE";
$res=mysql_query($str);

OPEN_BOX('');
echo OPEN_THEME('Master Data Vendor :');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>Data Vendor</b> Yang Sudah berhasil Disimpan Tidak Dapat Dihapus,<br>Harap Berhati2 dalam Melakukan Penambahan Data.Hanya Perubahan Data yang Diperkenankan.
     </fieldset>";
echo"<br>
    <input type=hidden value=simpan id=idx>
	<span id=note style='font-weight:bolder;'>Simpan</span> Vendor :
    <br><br>
	<dd>Kode Vendor &nbsp;&nbsp;&nbsp;: &nbsp;<input type=text class=myinputtext size=10 maxlength=10 id=newTitle style='text-align:center' onkeypress='return charAndNum(event);'><br><br>
    Nama Vendor &nbsp;&nbsp;: &nbsp;<input type=text class=myinputtext size=40 maxlength=40 id=name><br><br>
    Alamat &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<input type=text class=myinputtext size=40 maxlength=40 id=alamat onkeypress='return charAndNum(event);'><br><br>
	Kota &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<input type=text class=myinputtext size=10 maxlength=20 id=kota onkeypress='return charAndNum(event);'>
	<button class=mybutton onclick=saveVendor()>
	 Simpan
	 </button>
	<button class=mybutton onclick=clearVendor()>
	 Batal
	 </button>
	 </dd>
	 <hr>";
echo"
<form onSubmit=cari()>
List Data Vendor:
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit class=mybutton2 value=Cari>
</form>
     <span id=result>
     <div style='height:170px;overflow:auto'>
     <table width=500px class=sortable border=0 cellspacing=1>
     <thead>
	 <tr class=rowheader>
	 <td align=center>No.</td>
	 <td>Kode Vendor</td>
	 <td>Nama Vendor</td>
	 <td>Alamat</td>
	 <td>Kota</td>
	 <td>Edit</td></tr>
	 </thead>
	 <tbody>";
$no=0;
while($bar=mysql_fetch_object($res))
 {  $no+=1;
 	echo "<tr  class=rowcontentClick>
	      <td class=firsttd align=center>".$no."</td>
		  <td align=left>".$bar->TRPCODE."</td>
		  <td align=left>".$bar->TRPNAME."</td>
		  <td align=left>".$bar->TRPADDR."</td>
		  <td align=left>".$bar->TRPCITY."</td>
		  <td align=center><img title='Click to edit' class=editbtn src=images/edit.png  onclick=\"changeVendor('".$bar->TRPCODE."','".$bar->TRPNAME."','".$bar->TRPADDR."','".$bar->TRPCITY."');\"></td>
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
