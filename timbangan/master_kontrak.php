<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/hr.js></script>
<script language=javascript1.2 src=js/blocker.js></script>
<script language=javascript1.2 src=js/generic.exe></script>
<script language=JavaScript1.2>
field='CTRNO';
</script>
<?php
include('master_mainMenu.php');

$str="select * from ".$dbname.".mscontract order by CTRNO";
$res=mysql_query($str);
$stg="select * from ".$dbname.".msvendorbuyer order by BUYERCODE";
$reg=mysql_query($stg);
$opt_vendor="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_object($reg))
		{
			$opt_vendor.="<option value='".$bag->BUYERCODE."'>".$bag->BUYERNAME."</option>";
		}

OPEN_BOX('');
echo OPEN_THEME('Master Data Kontrak :');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>Data Kontrak</b> Yang Sudah berhasil Disimpan Tidak Dapat Dihapus,<br>Harap Berhati2 dalam Melakukan Penambahan Data.Hanya Perubahan Data yang Diperkenankan.
     </fieldset>";
echo"<br>
    <input type=hidden value=simpan id=idx>
	<span id=note style='font-weight:bolder;'>Simpan</span> Kontrak :
    <br><br>
	<dd>No. Kontrak &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;
	<input type=text class=myinputtext size=30 maxlength=45 id=newTitle  onkeypress='return tanpa_kutip_dan_sepasi(event);'><br><br>
	Tgl Kontrak &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=text id=tglkontrak class=myinputtext size=10 onmousemove=setCalendar(this.id) onkeypress=\"return false;\">
    &nbsp;&nbsp;&nbsp;<br><br>
	Nama Pembeli &nbsp;&nbsp;
	: &nbsp;<select id=buyer>".$opt_vendor."</select><br><br>
	Qty Kontrak &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=text class=myinputtext size=9 maxlength=9 id=qty style='text-align:right' onkeypress='return angka_doang(event);'> <b>Kg</b><br><br>
	Ktrk.Pembeli &nbsp;&nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=text class=myinputtext size=20 maxlength=50 id=ket onkeypress='return tanpa_kutip_dan_sepasi(event);'>&nbsp;&nbsp;&nbsp;<br>
	Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=radio id=status1 name=status value=Aktif checked>Aktif <input type=radio id=status2 name=status value='Tidak Aktif'>Tidak Aktif<br><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<button class=mybutton onclick=saveKontrak()>
	 Simpan
	 </button>
	<button class=mybutton onclick=clearKontrak()>
	 Batal
	 </button>
	 </dd>
	 <hr>";
echo"
<form>
<table width=100% border=0>
     <tr>
     <td>List Data Kontrak:</td>
     <td align=right>
     <div style='background-image:url(images/background.jpg);border:#000000 50px;width:250px;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:13px;top:400px;'>
	 <input type=hidden id=nlokasi value='Master>Kontrak'>
	 <input type=hidden id=norder value=CTRNO>
	 <input type=radio value=CTRNO  name=option checked=true onclick=\"field=this.value;\">No.Kontrak
	 <input type=radio value=CTRDATE  name=option onclick=\"field=this.value;\">Tanggal<br>
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
 $order='CTRNO';
}

if(@$_GET['offset'])
{
	if(strlen(@$_GET['cari']))
	{
		$str="select * from ".$dbname.".mscontract where ".$_GET['field']." like '%".$_GET['cari']."%' order by CTRDATE desc limit ".$_GET['offset'].",10";
	}
	else
	{
	$str="select * from ".$dbname.".mscontract order by CTRDATE desc limit ".$_GET['offset'].",10";
	}
$offset=$_GET['offset'];
$no=$offset;

}
else
{
	if(strlen(@$_GET['cari']))
	{
		$str="select * from ".$dbname.".mscontract where ".$_GET['field']." like '%".$_GET['cari']."%' order by CTRDATE desc  limit 0,10";
	}
else
{
$str="select * from ".$dbname.".mscontract order by CTRDATE desc limit 0,10";
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
     <div style='height:500spx;'>
     <table class=sortable border=0 cellspacing=1>
     <thead>
	 <tr class=rowheader>
	 <td width=5px align=center>No.</td>
	 <td width=100px align=center title='Click untuk mengurutkan berdasarkan No.Kontrak' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=CTRNO&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">No. Kontrak</td>
	<td align=center title='Click untuk mengurutkan berdasarkan tanggal' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=CTRDATE&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Tanggal Kontrak</td>
      <td align=center title='Click untuk mengurutkan berdasarkan Pembeli' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=BUYERCODE&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Pembeli</td>
	  <td align=center title='Click untuk mengurutkan berdasarkan Kuantitas' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=CTRQTY&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Kuantitas (KG)</td>
	  <td align=center title='Click untuk mengurutkan berdasarkan Keterangan' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=DESCRIPTION&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Keterangan</td>
      <td width=100px align=center title='Click untuk mengurutkan berdasarkan Status' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=CTRSTATUS&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Status</td>
	 <td>Edit</td></tr>
	 </thead>
	 <tbody>";
//$no=0;
while($bar=mysql_fetch_object($res))
 {  $no+=1;
    $ter="select BUYERNAME from ".$dbname.".msvendorbuyer where BUYERCODE='".$bar->BUYERCODE."' order by BUYERCODE";
 	$se=mysql_query($ter);
 	while($ccc=mysql_fetch_object($se)){
 		$buyername=$ccc->BUYERNAME;
 	}
 	echo "<tr class=rowcontentClick>
	      <td class=firsttd align=center>".$no."</td>
		  <td align=left>".$bar->CTRNO."</td>
		  <td align=left>".tanggalnormal($bar->CTRDATE)."</td>
		  <td align=center>".$buyername."</td>
		  <td align=right>".number_format($bar->CTRQTY,2,',','.')."</td>
		  <td align=center>".$bar->DESCRIPTION."</td>
		  <td align=center>".$bar->CTRSTATUS."</td>
		  <td align=center><img title='Click to edit' class=editbtn src=images/edit.png  onclick=\"changeKontrak('".$bar->CTRNO."','".tanggalnormal($bar->CTRDATE)."','".$bar->BUYERCODE."','".$buyername."','".$bar->CTRQTY."','".$bar->DESCRIPTION."','".$bar->CTRSTATUS."');\"></td>
		  </tr>
		 ";
 }
echo"
<table><center style='background-image:url(images/tabx6-1.png)'>
<br>";

if(@$_GET['cari'])
 {
  $str1="select * from ".$dbname.".mscontract where ".$_GET['field']." like '%".$_GET['cari']."%'";
 }
 else
 {
 $str1="select * from ".$dbname.".mscontract";
 }
 $result1=mysql_query($str1);
 $jlh=mysql_num_rows($result1);
 echo "<a href='master_kontrak.php?lokasi=Master>Kontrak' title='Kembali ke Awal'><font color:white>Back to Top</a> &nbsp &nbsp &nbsp ";
 if($no>10)
 echo" <a href='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&offset=".($offset-10)."&order=".$order."&cari=".@$_GET['cari']."&field=".@$_GET['field']."' title='Sebelumnya'>< Prev</a> &nbsp &nbsp ";
 echo  ($offset+1)." - ".$no." of ".$jlh." &nbsp &nbsp &nbsp";
 if($no<$jlh)
 echo "<a href='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&offset=".($offset+10)."&order=".$order."&cari=".@$_GET['cari']."&field=".@$_GET['field']."' title='Selanjutnya'>Next ></a>";
 echo"<br><br></center>";

}
else
{
 echo" <table><center><br>
<br>
<br>   <div style='width:600px; border:black solid 1px; background-color:silver;'>
    <b>\"".$_GET['cari']."\"</b>
	Tidak ditemukan pada kolom ".$_GET['field']."
           </div><a href='master_kontrak.php?lokasi=Master>Kontrak'>Back to Master</a>
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
