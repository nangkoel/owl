<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/hr.js></script>
<script language=javascript1.2 src=js/blocker.js></script>
<script language=javascript1.2 src=js/generic.exe></script>
<script language=JavaScript1.2>
field='PRODUCTCODE';
</script>
<?php
include('master_mainMenu.php');

$str="select * from ".$dbname.".msproduct order by PRODUCTCODE";
$res=mysql_query($str);

OPEN_BOX('');
echo OPEN_THEME('Master Data Product/Barang :');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>Data Product</b> Yang Sudah berhasil Disimpan Tidak Dapat Dihapus,<br>Harap Berhati2 dalam Melakukan Penambahan Data.Hanya Perubahan Data yang Diperkenankan.
     </fieldset>";
echo"<br>
    <input type=hidden value=simpan id=idx>
	<span id=note style='font-weight:bolder;'>Simpan</span> Product :
    <br><br>
	<dd>
	Kode Product &nbsp;: &nbsp;<input type=text class=myinputtext size=8 maxlength=8 id=newTitle style='text-align:right' onkeypress='return charAndNum(event);'><br><br>
    Nama Product : &nbsp;<input type=text class=myinputtext size=30 maxlength=30 id=name style='text-align:right' onkeypress='return charAndNum(event);'>
	<button class=mybutton onclick=saveProduct()>
	 Simpan
	 </button>
	<button class=mybutton onclick=clearProduct()>
	 Batal
	 </button>
	 </dd>
	 <hr>";
echo"
<form>
<table width=100% border=0>
     <tr>
     <td>List Data Product:</td>
     <td align=right>
     <div style='background-image:url(images/background.jpg);border:#000000 50px;width:250px;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:13px;top:400px;'>
	 <input type=hidden id=nlokasi value='Master>Product'>
	 <input type=hidden id=norder value=PRODUCTCODE>
	 <input type=radio value=PRODUCTCODE  name=option checked=true onclick=\"field=this.value;\">Kode
	 <input type=radio value=PRODUCTNAME  name=option onclick=\"field=this.value;\">Nama<br>
	 Text yang dicari:<input type=text id=cari size=10 value='".@$_GET['cari']."'><input name=btcari type=button value=Cari class=tombol title=Cari onclick=cari2()>
	 </div>
     </td></tr>
     </table>
</form>";
if(strlen(@$_GET['order'])>0)
{
 $order=$_GET['order'];
}
else
{
 $order='PRODUCTCODE';
}

if(@$_GET['offset'])
{
	if(strlen(@$_GET['cari']))
	{
		$str="select * from ".$dbname.".msproduct where ".$_GET['field']." like '%".$_GET['cari']."%' order by ".$order." limit ".$_GET['offset'].",10";
	}
	else
	{
	$str="select * from ".$dbname.".msproduct order by ".$order." limit ".$_GET['offset'].",10";
	}
$offset=$_GET['offset'];
$no=$offset;

}
else
{
	if(strlen(@$_GET['cari']))
	{
		$str="select * from ".$dbname.".msproduct where ".$_GET['field']." like '%".$_GET['cari']."%' order by ".$order." limit 0,10";
	}
else
{
$str="select * from ".$dbname.".msproduct order by ".$order." limit 0,10";
}
$offset=0;
$no=0;
}
//echo $str;

$res=mysql_query($str);
//echo mysql_error($con);
if(mysql_num_rows($res)>0)
{
 if(@$_GET['cari'])
 {
 	echo"<center><strong><font size=3 face='arial narrow'>Hasil Pencarian terhadap <font color=red>'".$_GET['cari']."'</font> pada kolom ".$_GET['field']." </font></strong></center>";
 }
echo"
     <span id=result>
     <div style='height:280px;'>
     <table width=500px class=sortable border=0 cellspacing=1>
     <thead>
	 <tr class=rowheader>
	 <td width=5px align=center>No.</td>
	 <td width=100px align=center title='Click untuk mengurutkan berdasarkan Kode' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=PRODUCTCODE&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Kode Barang</td>
	 <td align=center title='Click untuk mengurutkan berdasarkan Nama' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=PRODUCTNAME&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Nama Barang</td>
	 <td>Edit</td></tr>
	 </thead>
	 <tbody>";
//$no=0;
while($bar=mysql_fetch_object($res))
 {  $no+=1;
 	echo "<tr class=rowcontentClick>
	      <td class=firsttd align=center>".$no."</td>
		  <td align=left>".$bar->PRODUCTCODE."</td>
		  <td align=left>".$bar->PRODUCTNAME."</td>
		  <td align=center><img title='Click to edit' class=editbtn src=images/edit.png  onclick=\"changeProduct('".$bar->PRODUCTCODE."','".$bar->PRODUCTNAME."');\"></td>
		  </tr>
		 ";
 }
echo"
<table><center style='background-image:url(images/tabx6-1.png)'>
<br>";

if(@$_GET['cari'])
 {
  $str1="select * from ".$dbname.".msproduct where ".$_GET['field']." like '%".$_GET['cari']."%'";
 }
 else
 {
 $str1="select * from ".$dbname.".msproduct";
 }
 $result1=mysql_query($str1);
 $jlh=mysql_num_rows($result1);
 echo "<a href='master_product.php?lokasi=Master>Product' title='Kembali ke Awal'><font color:white>Back to Top</a> &nbsp &nbsp &nbsp ";
 if($no>10)
 echo" <a href='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&offset=".($offset-10)."&order=".$order."&cari=".@$_GET['cari']."&field=".@$_GET['field']."' title='Sebelumnya'>< Prev</a> &nbsp &nbsp ";
 echo  ($offset+1)." - ".$no." of ".$jlh." &nbsp &nbsp &nbsp";
 if($no<$jlh)
 echo "<a href='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&offset=".($offset+10)."&order=".$order."&cari=".@$_GET['cari']."&field=".@$_GET['field']."' title='Selanjutnya'>Next ></a>";
 echo"<br><br></center>";

}
else
{
 echo"<table> <center><br>
<br>
<br>   <div style='width:500px; border:black solid 1px; background-color:silver;'>
    <b>\"".$_GET['cari']."\"</b>
	Tidak ditemukan pada kolom ".$_GET['field']."
           </div><a href='master_product.php?lokasi=Master>Product'>Back to Master</a>
</center>";
}
echo"</tbody>
     </table>
     </div>
	 </span>";

echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();
?>
