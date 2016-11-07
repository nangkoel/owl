<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$notransaksi=$_POST['notransaksi'];
$tanggal=tanggalsystem($_POST['tanggal']);
$jenisby=$_POST['jenisby'];
$jumlahhrd=$_POST['jumlahhrd']; 
$method=$_POST['method'];
$jumlah=$_POST['jumlah']; 
$keterangan=$_POST['keterangan'];


if($jumlahhrd=='')
  $jumlahhrd=0;


if($method=='updateall')
{
	$str="update ".$dbname.".sdm_pjdinasdt
	       set jumlahhrd=jumlah
	      where notransaksi='".$notransaksi."' and jumlahhrd=0"; 
	//echo "Error:".$str;	  
	if(mysql_query($str))
		{}
	else
   		{
		 echo " Gagal:".addslashes(mysql_error($conn));	 
		 exit(0);
		}
}
if($method=='update')
{
	$str="update ".$dbname.".sdm_pjdinasdt
	       set jumlahhrd=".$jumlahhrd."
	      where jenisbiaya=".$jenisby." and notransaksi='".$notransaksi."'
		  and tanggal=".$tanggal." and jumlah='".$jumlah."' and keterangan='".$keterangan."'"; 
	if(mysql_query($str))
		{}
	else
   		{
		 echo " Gagal:".addslashes(mysql_error($conn));	 
		 exit(0);
		}
}
if($method=='finish')
{
	$str="select count(*) as jumlah from ".$dbname.".sdm_pjdinasdt
	       where notransaksi='".$notransaksi."' and jumlahhrd=0"; 
	//echo "Error:".$str;	  
	if(mysql_query($str)){
                $datakosong=fetchData($str);
		if ($datakosong[0]['jumlah']>0){
                    echo " Gagal: Ada biaya yang belum disetujui.";
                    exit(0);
                }
        } else {
		 echo " Gagal:".addslashes(mysql_error($conn));	 
		 exit(0);
	}
                
	$str="update ".$dbname.".sdm_pjdinasht
	       set statuspertanggungjawaban=1
	      where  notransaksi='".$notransaksi."'"; 
	if(mysql_query($str))
		{}
	else
   		{
   			echo " Gagal:".addslashes(mysql_error($conn));	 
		 exit(0);
		}	
}

$str="select a.*,b.keterangan as jns,b.id as bid from ".$dbname.".sdm_pjdinasdt a
      left join ".$dbname.".sdm_5jenisbiayapjdinas b on a.jenisbiaya=b.id
	  where a.notransaksi='".$notransaksi."' order by tanggal";
$res=mysql_query($str);
$no=0;
$total=0;
if (mysql_num_rows($res)==0){
    exit("Error: Belum ada pertanggungjawaban dibuat pada Perjalanan Dinas ini.");
} 
while($bar=mysql_fetch_object($res))
{
	$no+=1;
	echo"<tr class=rowcontent>
	     	<td id=no>".$no."</td>
                    <td>".tanggalnormal($bar->tanggal)."</td>
		    <td id=tgl".$no.">".$bar->jns."</td>
			<td id=ket".$no.">".$bar->keterangan."</td>
			<td id=jml".$no." align=right>".number_format($bar->jumlah,2,'.','.')."</td>
			<td id=by".$no." value=".$bar->bid." hidden></td>
			<td align=right>
			<img src='images/puzz.png' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('jumlahhrd".$bar->bid.$no."').value='".$bar->jumlah."'\">
			<input type=text id='jumlahhrd".$bar->bid.$no."' class=myinputtextnumber size=15 onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='".number_format($bar->jumlahhrd,2,'.',',')."'>
			<img src='images/save.png' title='Save' class=resicon onclick=\"saveApprvPJD('".$bar->bid."','".$bar->notransaksi."','".tanggalnormal($bar->tanggal)."','".$bar->jumlah."','".$bar->keterangan."','".$no."','".$no."')\"></td>
			</tr>";
	$total+=$bar->jumlah;		
}
	echo"<tr class=rowcontent>
	     	<td colspan=4 align=center>TOTAL</td>
			<td align=right>".number_format($total,2,'.','.')."</td>
		    <td></td>
			</tr>";

	echo"<tr class=rowcontent>
	     	<td colspan=6 align=right valign=center><button class=mybutton onclick=saveApprvPJDAll('".$notransaksi."')>".$_SESSION['lang']['save']." ".$_SESSION['lang']['biaya']." ".$_SESSION['lang']['all']."</button></td>
             </tr>";

?>