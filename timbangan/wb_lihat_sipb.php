<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/hr.js></script>
<script language=javascript1.2 src=js/blocker.js></script>
<script language=javascript1.2 src=js/generic.js></script>
<script language=JavaScript1.2>
field='SIPBNO';
</script>
<?php
include('master_mainMenu.php');

$str="select * from ".$dbname.".mssipb order by SIPBNO";
$res=mysql_query($str);
$stg="select * from ".$dbname.".mscontract order by CTRDATE desc";
$reg=mysql_query($stg);
$opt_ctr="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_object($reg))
		{
			//$opt_ctr.="<option value='".$bag->CTRNO."'>".$bag->CTRNO.".".$bag->BUYERCODE."</option>";
			$opt_ctr.="<option value='".$bag->CTRNO."'>".$bag->CTRNO."</option>";
		}
$cvb="select * from ".$dbname.".msproduct   where PRODUCTCODE like '40%' order by PRODUCTNAME";
$zxc=mysql_query($cvb);
$opt_brg="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_object($zxc))
		{
			$opt_brg.="<option value='".$bag->PRODUCTCODE."'>".$bag->PRODUCTNAME."</option>";
		}
$as="select * from ".$dbname.".msvendortrp order by TRPNAME";
$zx=mysql_query($as);
$opt_trp="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_object($zx))
		{
			$opt_trp.="<option value='".$bag->TRPCODE."'>".$bag->TRPNAME."</option>";
		}

OPEN_BOX('','Master SIPB');

if(strlen(@$_GET['order'])>0)
{
 $order=$_GET['order'];
}
else
{
 $order='SIPBNO';
}

if(@$_GET['offset'])
{
	if(strlen(@$_GET['cari']))
	{
		$str="select * from ".$dbname.".mssipb where ".$_GET['field']." like '%".$_GET['cari']."%' order by SIPBDATE desc limit ".$_GET['offset'].",10";
	}
	else
	{
	$str="select * from ".$dbname.".mssipb order by SIPBDATE desc limit ".$_GET['offset'].",10";
	}
$offset=$_GET['offset'];
$no=$offset;

}
else
{
	if(strlen(@$_GET['cari']))
	{
		$str="select * from ".$dbname.".mssipb where ".$_GET['field']." like '%".$_GET['cari']."%' order by SIPBDATE desc limit 0,10";
	}
else
{
$str="select * from ".$dbname.".mssipb order by SIPBDATE desc limit 0,10";
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
     <table  class=sortable border=0 cellspacing=1>
     <thead>
	 <tr class=rowheader>
	 <td  align=center>No.</td>
	<td  align=center align=center title='Click untuk mengurutkan berdasarkan No.Kontrak' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=SIPBNO&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">No. Kontrak</td>
	   <td align=center title='Click untuk mengurutkan berdasarkan No. SIPB' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=SIPBNO&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">No. SIPB</td>
      <td align=center title='Click untuk mengurutkan berdasarkan tanggal' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=SIPBDATE&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Tanggal SIPB</td>
	  <td align=center title='Click untuk mengurutkan berdasarkan Product' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=PRODUCTCODE&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Product</td>
	  <td align=center title='Click untuk mengurutkan berdasarkan tanggal' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=BUYERCODE&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Pembeli</td>
	  <td align=center title='Click untuk mengurutkan berdasarkan Transporter' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=TRPCODE&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Transporter</td>
	  <td align=center title='Click untuk mengurutkan berdasarkan Kuantitas' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=SIPBQTY&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Sisa (KG)</td>
	  <td align=center title='Click untuk mengurutkan berdasarkan Keterangan' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=DESCRIPTION&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Keterangan</td>
      <td  align=center title='Click untuk mengurutkan berdasarkan Status' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=SIPBSTATUS&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Status</td>
	 </tr>
	 </thead>
	 <tbody>";
//$no=0;
while($bar=mysql_fetch_object($res))
 {  $no+=1;
   	$str3="select CTRNO from ".$dbname.".mscontract where CTRNO='".$bar->CTRNO."'";
  	$res3=mysql_query($str3);
  	$nama_contract='';
 	 while($bar1=mysql_fetch_array($res3))
 	 {
  		$nama_contract=$bar1[0];
  	 }
  $str4="select TRPCODE,TRPNAME from ".$dbname.".msvendortrp where TRPCODE='".$bar->TRPCODE."'";
  $res4=mysql_query($str4);
  $nama_transporter='';
  while($bar1=mysql_fetch_array($res4))
  {
  	$nama_transporter=$bar1[1];
  }
  $str5="select * from ".$dbname.".msvendorbuyer,".$dbname.".mscontract where ".$dbname.".mscontract.CTRNO='".$bar->CTRNO."' and ".$dbname.".msvendorbuyer.BUYERCODE=".$dbname.".mscontract.BUYERCODE";
  $res5=mysql_query($str5);
  $nama_buyer='';
  while($bar1=mysql_fetch_array($res5))
  {
  	$nama_buyer=$bar1[1];
  }
  $str6="select * from ".$dbname.".msproduct,".$dbname.".mssipb where ".$dbname.".mssipb.PRODUCTCODE='".$bar->PRODUCTCODE."' and ".$dbname.".msproduct.PRODUCTCODE=".$dbname.".mssipb.PRODUCTCODE";
  $res6=mysql_query($str6);
  $productname='';
  while($bar1=mysql_fetch_array($res6))
  {
  	$productname=$bar1[1];
  }
 	echo "<tr class=rowcontentClick>
	      <td class=firsttd align=center>".$no."</td>
		  <td>".$bar->CTRNO."</td>
  		  <td>".$bar->SIPBNO."</td>
		  <td align=left>".tanggalnormal($bar->SIPBDATE)."</td>
		  <td>".$productname."</td>
		  <td>".$nama_buyer."</td>
		  <td>".$nama_transporter."</td>
		  <td align=right>".number_format($bar->SIPBQTY,2,',','.')."</td>
		  <td>".$bar->DESCRIPTION."</td>
		  <td align=center>".$bar->SIPBSTATUS."</td>
		  </tr>
		 ";
 }
echo"
<table>
<br>";

if(@$_GET['cari'])
 {
  $str1="select * from ".$dbname.".mssipb where ".$_GET['field']." like '%".$_GET['cari']."%'";
 }
 else
 {
 $str1="select * from ".$dbname.".mssipb";
 }
 $result1=mysql_query($str1);
 $jlh=mysql_num_rows($result1);
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
           </div><a href='master_sipb.php?lokasi=Master>SIPB'>Back to Master</a>";
}
echo"</tbody>
     </table>";

CLOSE_BOX();
echo close_body();
?>
