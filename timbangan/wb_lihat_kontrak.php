<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/hr.js></script>
<script language=javascript1.2 src=js/blocker.js></script>
<script language=javascript1.2 src=js/generic.js></script>
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

OPEN_BOX('','Master Kontrak');

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
	 <td align=center>No.</td>
	 <td align=center title='Click untuk mengurutkan berdasarkan No.Kontrak' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=CTRNO&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">No. Kontrak</td>
	<td align=center title='Click untuk mengurutkan berdasarkan tanggal' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=CTRDATE&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Tanggal Kontrak</td>
      <td align=center title='Click untuk mengurutkan berdasarkan Pembeli' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=BUYERCODE&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Pembeli</td>
	  <td align=center title='Click untuk mengurutkan berdasarkan Kuantitas' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=CTRQTY&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Kuantitas (KG)</td>
<td>Terpenuhi</td> 	  
<td align=center title='Click untuk mengurutkan berdasarkan Keterangan' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=DESCRIPTION&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Keterangan</td>
      <td  align=center title='Click untuk mengurutkan berdasarkan Status' onclick=\"window.location='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&order=CTRSTATUS&cari=".@$_GET['cari']."&field=".@$_GET['field']."'\">Status</td>
         
      
	</tr>
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
     $strx="select sum(netto) as terpenuhi from ".$dbname.".mstrxtbs where CTRNO='".$bar->CTRNO."'";
     $resx=mysql_query($strx);
     $terpenuhi=0;
     while($barx=mysql_fetch_object($resx))
     {
         $terpenuhi=$barx->terpenuhi;
     }
 	echo "<tr class=rowcontentClick>
	      <td class=firsttd align=center>".$no."</td>
		  <td align=left>".$bar->CTRNO."</td>
		  <td align=left>".tanggalnormal($bar->CTRDATE)."</td>
		  <td align=center>".$buyername."</td>
		  <td align=right>".number_format($bar->CTRQTY,2,',','.')."</td>
                      <td align=right>".number_format($terpenuhi,2,',','.')."</td> 
		  <td align=center>".$bar->DESCRIPTION."</td>
		  <td align=center>".$bar->CTRSTATUS."</td>
                     
		  </tr>
		 ";
 }
echo"
<table>
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
 if($no>10)
 echo" <a href='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&offset=".($offset-10)."&order=".$order."&cari=".@$_GET['cari']."&field=".@$_GET['field']."' title='Sebelumnya'>< Prev</a> &nbsp &nbsp ";
 echo  ($offset+1)." - ".$no." of ".$jlh." &nbsp &nbsp &nbsp";
 if($no<$jlh)
 echo "<a href='".$_SERVER['PHP_SELF']."?lokasi=".$_GET['lokasi']."&offset=".($offset+10)."&order=".$order."&cari=".@$_GET['cari']."&field=".@$_GET['field']."' title='Selanjutnya'>Next ></a>";
 echo"<br><br>";

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

CLOSE_BOX();
echo close_body();
?>
