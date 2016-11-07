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

$str="select * from ".$dbname.".msvendorbuyer order by BUYERCODE";
$res=mysql_query($str);

OPEN_BOX('');
echo OPEN_THEME('Master Data Customer :');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>Data Customer</b> Yang Sudah berhasil Disimpan Tidak Dapat Dihapus,<br>Harap Berhati2 dalam Melakukan Penambahan Data.Hanya Perubahan Data yang Diperkenankan.
     </fieldset>";
echo"<br>
    <input type=hidden value=simpan id=idx>
	<span id=note style='font-weight:bolder;'>Simpan</span> Customer :
    <br><br>
	<dd>Kode Customer &nbsp;&nbsp;&nbsp;: &nbsp;<input type=text class=myinputtext size=10 maxlength=10 id=newTitle style='text-align:center' onkeypress='return charAndNum(event);'><br><br>
    Nama Customer &nbsp;&nbsp;: &nbsp;<input type=text class=myinputtext size=40 maxlength=40 id=name><br><br>
    Alamat &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<input type=text class=myinputtext size=50 maxlength=50 id=alamat onkeypress='return charAndNum(event);'><br><br>
	Kota &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<input type=text class=myinputtext size=20 maxlength=20 id=kota onkeypress='return charAndNum(event);'>
	<button class=mybutton onclick=saveCustomer()>
	 Simpan
	 </button>
	<button class=mybutton onclick=clearCustomer()>
	 Batal
	 </button>
	 </dd>
	 <hr>";
echo"
<form onSubmit=cari()>
List Data Customer:
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit class=mybutton2 value=Cari>
</form>
     <span id=result>
     <div style='height:170px;overflow:auto'>
     <table width=500px class=sortable border=0 cellspacing=1>
     <thead>
	 <tr class=rowheader>
	 <td align=center>No.</td>
	 <td>Kode Customer</td>
	 <td>Nama Customer</td>
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
		  <td align=left>".$bar->BUYERCODE."</td>
		  <td align=left>".$bar->BUYERNAME."</td>
		  <td align=left>".$bar->BUYERADDR."</td>
		  <td align=left>".$bar->BUYERCITY."</td>
		  <td align=center><img title='Click to edit' class=editbtn src=images/edit.png  onclick=\"changeCustomer('".$bar->BUYERCODE."','".$bar->BUYERNAME."','".$bar->BUYERADDR."','".$bar->BUYERCITY."');\"></td>
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
