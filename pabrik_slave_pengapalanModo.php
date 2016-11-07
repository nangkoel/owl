<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

?>	

<?php		

$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];

$notran=$_POST['notran'];
$tgl=tanggalsystem($_POST['tgl']);
$kodeorg=$_POST['kodeorg'];
$nokontrak=$_POST['nokontrak'];
$nodo=$_POST['nodo'];
$kdCust=$_POST['kdCust'];
$kdbarang=$_POST['kdbarang'];
$kdKapal=$_POST['kdKapal'];
$transp=$_POST['transp'];
$berat=$_POST['berat'];
$perSch=$_POST['perSch'];
$notranSch=$_POST['notranSch'];
$nokontrakSch=$_POST['nokontrakSch'];


$nmCust=makeOption($dbname,'pmn_4customer','kodecustomer,namacustomer');
$nmBarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$nmTranp=makeOption($dbname,'log_5supplier','supplierid,namasupplier');

if($method=='excel')
{
	$perSch=$_GET['perSch'];
	$notranSch=$_GET['notranSch'];
	$nokontrakSch=$_GET['nokontrakSch'];
}


$where="left(notransaksi,4)='H01M' and millcode='H01M'";

if($perSch!='')
	$where.="and tanggal like '".$perSch."%'";

if($notranSch!='')
	$where.="and notransaksi like '%".$notranSch."%'";
	
if($nokontrakSch!='')
	$where.="and nokontrak like '%".$nokontrakSch."%'";	

?>

<?php



switch($method)
{
	case 'excel':
	
		$border="border='1'";
		$bgcolor="bgcolor=#CCCCCC";
		
		$stream="".$_SESSION['lang']['pengapalanmodo']."<br />Periode : ".$perSch."";		
		$stream.="<table cellspacing='1' class='sortable'  ".$border.">";
		$stream.="<thead class=rowheader>
			  <tr>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['nourut']."</td>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['notransaksi']."</td>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['tanggal']."</td>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['pabrik']."</td>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['NoKontrak']."</td>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['nodo']."</td>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['nmcust']."</td>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['kodebarang']."</td>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['kodekapal']."</td>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['transporter']."</td>
				 <td align=center ".$bgcolor.">".$_SESSION['lang']['beratBersih']."</td>
			  </tr>
		</thead>
		<tbody>";

		
		$w="select * from ".$dbname.".pabrik_timbangan where  ".$where."   ";
				
		$i=mysql_query($w) or die (mysql_error($conn));
		while($b=mysql_fetch_assoc($i))
		{
			$no+=1;
			$stream.="<tr ".$border."  class=rowcontent>";
					$stream.="<td align=center>".$no."</td>";
					$stream.="<td align=left>".$b['notransaksi']."</td>";
					$stream.="<td align=left>".tanggalnormal($b['tanggal'])."</td>";
					$stream.="<td align=left>".$b['millcode']."</td>";
					$stream.="<td align=left>".$b['nokontrak']."</td>";
					$stream.="<td align=left>".$b['nodo']."</td>";
					$stream.="<td align=left>".$nmCust[$b['kodecustomer']]."</td>";
					$stream.="<td align=left>".$nmBarang[$b['kodebarang']]."</td>";
					$stream.="<td align=left>".$b['nokendaraan']."</td>";
					$stream.="<td align=left>".$nmTranp[$b['trpcode']]."</td>";
					$stream.="<td align=right>".number_format($b['beratbersih'])."</td>";
			$stream.="</tr>";		
		}
 
	
	
		$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Kelengkapan_Loses".$tglSkrg;
		if(strlen($stream)>0)
		{
			if ($handle = opendir('tempExcel')) {
				while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					@unlink('tempExcel/'.$file);
				}
				}	
				closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$stream))
			{
				echo "<script language=javascript1.2>
				parent.window.alert('Can't convert to excel format');
				</script>";
				exit;
			}
			else
			{
				echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls';
				</script>";
			}
			closedir($handle);
		}           
	break;
		
	case'getCust':
	
		$i="select koderekanan,kodebarang from ".$dbname.".pmn_kontrakjual where nokontrak='".$nokontrak."'";
	//exit("Error:$i");
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$optCust.="<option value='".$d['koderekanan']."'>".$nmCust[$d['koderekanan']]."</option>";
		}
		
		$n2=mysql_query($i) or die (mysql_error($conn));
		while($d2=mysql_fetch_assoc($n2))
		{
			$optBarang.="<option value='".$d2['kodebarang']."'>".$nmBarang[$d2['kodebarang']]."</option>";
			$namabarang=$d2['kodebarang'];
		}
		if($namabarang=='40000002'){
			$kom="KER";
			$whr=" and kodetangki !='BKL01'";
		}else{
			$kom="CPO";
			$whr=" and kodetangki not in ('ST01','ST02')";
		}
		$optTangki="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$sTangki="select kodetangki,keterangan from ".$dbname.".pabrik_5tangki 
				  where kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$whr."  and komoditi like '%".$kom."%'";
				  //exit("error".$sTangki);
		$qTangki=mysql_query($sTangki) or die(mysql_error($conn));
		while($rTangki=mysql_fetch_assoc($qTangki)){
			$optTangki.="<option value='".$rTangki['kodetangki']."'>".$rTangki['keterangan']."</option>";
		}
	
	echo $optCust."###".$optBarang."###".$optTangki;
	break;

	case'insert':
	
/*	
		$d="select beratbersih,kuantitaskontrak,selisih,nokontrak from ".$dbname.".pabrik_kontrakjual_vs_timbangan where nokontrak='".$nokontrak."' ";
		$e=mysql_query($d) or die (mysql_error($conn));
		$f=mysql_fetch_assoc($e);
			$kuantitas=$f['kuantitaskontrak'];
			$sisa=$f['selisih'];
			$beratbersih=$f['beratbersih'];
			
			if($beratbersih==0 or $beratbersih==null)
				$beratbersih=$kuantitas;
			
			if($sisa==0 or $sisa==null)
				$sisa=$kuantitas;	
			
			$cek=$sisa+$berat;
			
		//exit("Error:$kuantitas.___.$sisa.___.$cek.___.$berat.___.$beratbersih");
			#cara cek 1 = jika param berat > dari sisa 
			#cara cek 2 = jika berat bersih+param berat>berat bersih
			
			
		if($berat>$sisa)
		{
			exit("Error:Jumlah sudah melewati kontrak");
		}*/
	
		$str="insert into ".$dbname.".pabrik_timbangan (`notransaksi`,`tanggal`,`millcode`,`nokontrak`,`nodo`,`kodecustomer`
				,`kodebarang`,`nokendaraan`,`trpcode`,`beratbersih`,`username`,`kodeorg`,`sloc`)
				values ('".$notran."','".$tgl."','".$kodeorg."','".$nokontrak."','".$nodo."','".$kdCust."','".$kdbarang."','".$kdKapal."',
				'".$transp."','".$berat."','".$_SESSION['standard']['username']."','','".$_POST['kdTangki']."')";
//                exit("Error:".$str);

		if(mysql_query($str))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	case'update':
		$str="update ".$dbname.".pabrik_timbangan set tanggal='".$tgl."',nokontrak='".$nokontrak."',nodo='".$nodo."',kodecustomer='".$kdCust."',
		kodebarang='".$kdbarang."',nokendaraan='".$kdKapal."',trpcode='".$transp."',beratbersih='".$berat."',sloc='".$_POST['kdTangki']."'
		where notransaksi='".$notran."' and millcode='".$kodeorg."'";
		
		if(mysql_query($str))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	
	break;
	
	
	
	case'loadData'://No.Transaksi	Tanggal	Kodeorg	No.Kontrak	No.DO	Custommer	Kode Barang	Kode Kapal	Transporter	Berat Berih	Aksi

	echo"
	<div id=container>
		
		<table class=sortable cellspacing=1 border=0>
	     <thead>
			 <tr class=rowheader>
			 	 <td align=center>".$_SESSION['lang']['nourut']."</td>
			 	 <td align=center>".$_SESSION['lang']['notransaksi']."</td>
				 <td align=center>".$_SESSION['lang']['tanggal']."</td>
				 <td align=center>".$_SESSION['lang']['pabrik']."</td>
				 <td align=center>".$_SESSION['lang']['NoKontrak']."</td>
				 
				 <td align=center>".$_SESSION['lang']['nodo']."</td>
				 <td align=center>".$_SESSION['lang']['nmcust']."</td>
				 
				 <td align=center>".$_SESSION['lang']['kodebarang']."</td>
				 <td align=center>".$_SESSION['lang']['kodekapal']."</td>
				 <td align=center>".$_SESSION['lang']['transporter']."</td>
				 <td align=center>".$_SESSION['lang']['beratBersih']." (Kg)</td>
				 
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead>
		<tbody>";
		
		
		
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".pabrik_timbangan where ".$where." ";// where kodeorg='".$kodeorg."' and periode='".$per."'
		//exit("Error:$ql2");
		//where kodeorg='".$kodeorg."' and periode='".$per."' order by lastupdate
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".pabrik_timbangan where ".$where."   limit ".$offset.",".$limit."";
		
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$d['notransaksi']."</td>";
			echo "<td align=left>".tanggalnormal($d['tanggal'])."</td>";
			echo "<td align=left>".$d['millcode']."</td>";
			
			echo "<td align=left>".$d['nokontrak']."</td>";
			echo "<td align=left>".$d['nodo']."</td>";
			echo "<td align=left>".$nmCust[$d['kodecustomer']]."</td>";
			echo "<td align=left>".$nmBarang[$d['kodebarang']]."</td>";
			
			echo "<td align=left>".$d['nokendaraan']."</td>";
			echo "<td align=left>".$nmTranp[$d['trpcode']]."</td>";
			echo "<td align=right>".number_format($d['beratbersih'])."</td>";
				
			/*<img src=images/application/application_edit.png class=resicon  caption='Edit' 
					onclick=\"edit('".$d['notransaksi']."','".tanggalnormal($d['tanggal'])."','".$d['nodo']."','".$d['nokendaraan']."',
					'".$d['trpcode']."','".$d['beratbersih']."','".$d['nokontrak']."','".$d['kodecustomer']."','".$d['kodebarang']."');\">*/
					
			echo "<td align=center>
				
				<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['notransaksi']."');\"></td>";		
			echo "</tr>";
		}//function edit(notran,tgl,nodo,kdKapal,transp,berat,nokontrak,kdCust,kdbarang)
		echo"
		<tr class=rowheader><td colspan=43 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		echo"</tbody></table>";
    break;
	
	
	case'delete':
		$i="delete from ".$dbname.".pabrik_timbangan where notransaksi='".$notran."'";
		//exit("Error:$i");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	
	case'update':
	
	//exit("Error:masuk");
		$i="update ".$dbname.".pabrik_kelengkapanloses set nilai='".$inpEdit."',`updateby`='".$_SESSION['standard']['userid']."' where kodeorg='".$kodeorgEdit."' and tanggal='".$tglEdit."' and id='".$idEdit."'";
		//exit("Error:$i");
		if(mysql_query($i))
		{//berhasil kosongin aja
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	

	
	
	
	
	
	default;
}
?>
