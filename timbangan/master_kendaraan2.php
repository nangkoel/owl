<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/hr.js></script>
<script language=javascript1.2 src=js/blocker.js></script>
<script language=javascript1.2 src=js/generic.exe></script>
<script language=JavaScript1.2>
field='VEHNOCODE';
</script>
<?php
include('master_mainMenu.php');

$str="select * from ".$dbname.".msvehicle order by VEHNOCODE";
$res=mysql_query($str);
$stg="select * from ".$dbname.".msvendortrp order by TRPCODE";
$reg=mysql_query($stg);
$opt_vendor="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_object($reg))
		{
			$opt_vendor.="<option value='".$bag->TRPCODE."'>".$bag->TRPNAME."</option>";
		}
$xcv="select * from ".$dbname.".msvehtype order by VEHTYPECODE";
$rex=mysql_query($xcv);
$opt_tipe="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_object($rex))
		{
			$opt_tipe.="<option value='".$bag->VEHTYPECODE."'>".$bag->VEHTYPENAME."</option>";
		}
OPEN_BOX('');
echo OPEN_THEME('Master Data Kendaraan :');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>Data Kendaraan</b> Yang Sudah berhasil Disimpan Tidak Dapat Dihapus,<br>Harap Berhati2 dalam Melakukan Penambahan Data.Hanya Perubahan Data yang Diperkenankan.
     </fieldset>";
echo"<br>
    <input type=hidden value=simpan id=idx>
	<span id=note style='font-weight:bolder;'>Simpan</span> Kendaraan :
    <br><br>
	<dd>Vendor &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<select id=company>".$opt_vendor."</select><br><br>
	Tipe Kendaraan &nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<select id=tipe>".$opt_tipe."</select><br><br>
	No. Kendaraan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;<input type=text class=myinputtext size=14 maxlength=14 id=newTitle style='text-align:right' onkeypress='return charAndNum(event);'><br><br>
    Tarra Minimum &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=text class=myinputtext size=6 maxlength=6 id=name style='text-align:right' onkeypress='return angka_doang(event);'> <b>Kg</b>
    &nbsp;&nbsp;&nbsp;<br><br>
	Tarra Maksimum &nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=text class=myinputtext size=6 maxlength=6 id=name2 style='text-align:right' onkeypress='return angka_doang(event);'> <b>Kg</b><br><br>
	Nama Supir &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=text class=myinputtext size=30 maxlength=30 id=supir style='text-align:right' onkeypress='return charAndNum(event);'>&nbsp;&nbsp;&nbsp;
	No. SIM &nbsp;
    : &nbsp;<input type=text class=myinputtext size=15 maxlength=15 id=nosim style='text-align:right' onkeypress='return charAndNum(event);'><br><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<button class=mybutton onclick=saveKendaraan()>
	 Simpan
	 </button>
	<button class=mybutton onclick=clearKendaraan()>
	 Batal
	 </button>
	 </dd>
	 <hr>";
echo"
<form>
<table width=100% border=0>
     <tr>
     <td>List Data Kendaraan:</td>
     <td align=right>
     <div style='background-image:url(images/background.jpg);border:#000000 50px;width:250px;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:13px;top:400px;'>
	 <input type=hidden id=nlokasi value='Master>Kendaraan'>
	 <input type=hidden id=norder value=VEHNOCODE>
	 <input type=radio value=VEHNOCODE  name=option checked=true onclick=\"field=this.value;\">No.Kendaraan
	 <input type=radio value=VEHDRIVER  name=option onclick=\"field=this.value;\">Driver<br>
	 Text yang dicari:<input type=text id=cari name=cari size=10 value='".@$_GET['cari']."'><input name=btcari type=button value=Cari class=tombol title=Cari onclick=cari2()>
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
 $order='TRPCODE';
}

if(@$_GET['offset'])
{
	if(strlen(@$_GET['cari']))
	{
		$str="select * from wbridge.msvehicle where ".$_GET['field']." like '%".$_GET['cari']."%' order by ".$order." limit ".$_GET['offset'].",10";
	}
	else
	{
	$str="select * from wbridge.msvehicle order by ".$order." limit ".$_GET['offset'].",10";
	}
$offset=$_GET['offset'];
$no=$offset;

}
else
{
	if(strlen(@$_GET['cari']))
	{
		$str="select * from wbridge.msvehicle where ".$_GET['field']." like '%".$_GET['cari']."%' order by ".$order." limit 0,10";
	}
else
{
$str="select * from wbridge.msvehicle order by ".$order." limit 0,10";
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
 { 	echo"<center><strong><font size=3 face='arial narrow'>Hasil Pencarian terhadap <font color=red>'".$_GET['cari']."'</font> pada kolom ".$_GET['field']." </font></strong></center>";
 }
echo"
     <span id=result>
     <div style='height:280px;'>
     <table width=600px class=sortable border=0 cellspacing=1>
     <thead>
	 <tr class=rowheader>
	 <td width=5px align=center>No.</td>
	 <td width=100px align=center title='Click untuk mengurutkan berdasarkan No. Kendaraan' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=VEHNOCODE&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">No.Kendaraan</td>
	 <td>Vendor</td>
	 <td>Jenis Kendaraan</td>
	 <td align=center title='Click untuk mengurutkan berdasarkan Tarra Minimum' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=VEHTARMIN&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Tarra Minimum</td>
	 <td align=center title='Click untuk mengurutkan berdasarkan Tarra Maksimum' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=VEHTARMAX&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Tarra Maksimum</td>
	 <td>Edit</td></tr>
	 </thead>
	 <tbody>";
$no=0;
while($bar=mysql_fetch_object($res))
 {  $no+=1;
    $ter="select TRPNAME from ".$dbname.".msvendortrp where TRPCODE='".$bar->TRPCODE."' order by TRPNAME";
 	$se=mysql_query($ter);
 	while($ccc=mysql_fetch_object($se)){
 		$trpname=$ccc->TRPNAME;
 	}
 	$txc="select VEHTYPENAME from ".$dbname.".msvehtype where VEHTYPECODE='".$bar->VEHTYPECODE."' order by VEHTYPENAME";
 	$seh=mysql_query($txc);
 	while($sss=mysql_fetch_object($seh)){
 		$vehtypename=$sss->VEHTYPENAME;
 	}
 	echo "<tr class=rowcontentClick>
	      <td class=firsttd align=center>".$no."</td>
		  <td align=center>".$bar->VEHNOCODE."</td>
		  <td align=center>".$trpname."</td>
		  <td align=center>".$vehtypename."</td>
		  <td align=center>".$bar->VEHTARMIN."</td>
		  <td align=center>".$bar->VEHTARMAX."</td>
		  <td align=center><img title='Click to edit' class=editbtn src=images/edit.png  onclick=\"changeKendaraan('".$bar->VEHNOCODE."','".$bar->TRPCODE."','".$trpname."','".$bar->VEHTYPECODE."','".$vehtypename."','".$bar->VEHTARMIN."','".$bar->VEHTARMAX."','".$bar->VEHDRIVER."','".$bar->VEHDRVSIM."');\"></td>
		  </tr>
		 ";
 }
echo"
<table><center style='background-image:url(images/title_bg.jpg)'>
<br>";

if(@$_GET['cari'])
 {
  $str1="select * from wbridge.msvehicle where ".$_GET['field']." like '%".$_GET['cari']."%'";
 }
 else
 {
 $str1="select * from wbridge.msvehicle";
 }
 $result1=mysql_query($str1);
 $jlh=mysql_num_rows($result1);
 echo "<a href='master_kendaraan.php?lokasi=Master>Kendaraan' title='Kembali ke Awal'><font color:white>Back to Top</a> &nbsp &nbsp &nbsp ";
 if($no>10)
 echo" <a href='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&offset=".($offset-10)."&order=".$order."&cari=".@$_GET['cari']."&field=".@$_GET['field']."' title='Sebelumnya'>< Prev</a> &nbsp &nbsp ";
 echo  ($offset+1)." - ".$no." of ".$jlh." &nbsp &nbsp &nbsp";
 if($no<$jlh)
 echo "<a href='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&offset=".($offset+10)."&order=".$order."&cari=".@$_GET['cari']."&field=".@$_GET['field']."' title='Selanjutnya'>Next ></a>";
 echo"<br><br></center>";

}
else
{
 echo" <center><br>
<br>
<br>   <div style='width:600px; border:black solid 1px; background-color:silver;'>
    <b>\"".$_GET['cari']."\"</b>
	Tidak ditemukan pada kolom ".$_GET['field']."
           </div><a href='master_kendaraan.php?lokasi=Master>Kendaraan'>Back to Master</a>
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
