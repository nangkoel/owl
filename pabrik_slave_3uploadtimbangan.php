<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

if(isset($_POST['proses'])!='')
{
	$proses=$_POST['proses'];
}
elseif(isset($_GET['proses'])!='')
{
	$proses=$_GET['proses'];
}
$periode=$_POST['periode'];
$idRemote=$_POST['idRemote'];
//$arr="##dbnm##prt##pswrd##ipAdd##period##kdBrg##usrName";
$dbnm=$_POST['dbnm'];
$prt=$_POST['prt'];
$pswrd=$_POST['pswrd'];
$ipAdd=$_POST['ipAdd'];
//$period=$_POST['period'];
$period=explode("-",$_POST['period']);
$tglPeriod=$period[2]."-".$period[1]."-".$period[0];
//$kdBrg=$_POST['kdBrg'];
//$kdBrg="('40000001','40000002','40000003','40000004','40000005')";
$usrName=$_POST['usrName'];
$lksiServer=$_POST['lksiServer'];
$idTimbangan =$_POST['idTimbangan'];
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$tglData=tanggalsystem($_POST['tglData']);

$custData=$_POST['custData'];
$kbn=$_POST['kbn'];
$pabrik=$_POST['pabrik'];
$kdBrg=$_POST['kdBrg'];
$spbno=$_POST['spbno'];
$sibno=$_POST['sibno'];
$thnTnm=$_POST['thnTnm'];
$thnTnm2=$_POST['thnTnm2'];
$thnTnm3=$_POST['thnTnm3'];
$jmlhjjg=$_POST['jmlhjjg'];
$jmlhjjg2=$_POST['jmlhjjg2'];
$jmlhjjg3=$_POST['jmlhjjg3'];
$brndln=$_POST['brndln'];
$nodo=$_POST['nodo'];
$kdVhc=$_POST['kdVhc'];
$spir=$_POST['spir'];
$jmMasuk=$_POST['jmMasuk'];
$jmKeluar=$_POST['jmKeluar'];
$brtBrsih=$_POST['brtBrsih'];
$brtMsk=$_POST['brtMsk'];
$brtOut=$_POST['brtOut'];
$usrNm=$_POST['usrNm'];
$kntrkNo=$_POST['kntrkNo'];

switch($proses)
{
	case'preview':
/*	$dbserver='192.168.1.204';
	//$dbserver='localhost';
	$dbport  ='3306';
	$dbname  ='owl';
	$uname   ='root';
	$passwd  ='dbdev';
	//$passwd  ='root';*/
	//echo"warning:".$ipAdd;
	//exit();127.0.0.1
///	$ipAdd='192.168.1.204';
	if($lksiServer=='')
	{
		echo"warning:Lokasi Harus Di Isi";
		exit();
	}
	$arr="##dbnm##prt##pswrd##ipAdd##period##usrName##lksiServer";
	@$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
	//$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd);

	//$sCob="select * from ".$dbnm.".mstrxtbs where PRODUCTCODE in ('40000001','40000002','40000003','40000004','40000005') and GI='0' and DATEIN like '%".$period."%' and OUTIN='0'";
	$sCob="select * from ".$dbnm.".mstrxtbs where PRODUCTCODE in ('40000001','40000002','40000003','40000004','40000005') and GI='0' and DATEIN like '%".$tglPeriod."%' and OUTIN='0'";
	//echo $sCob;exit();
	$res=mysql_query($sCob,$corn) or die(mysql_error());
	$row=mysql_num_rows($res);
	if($row>0)
	{
	//echo"warning:".$sCob;`notransaksi`, `tanggal`, `kodeorg`, `kodecustomer`, `jumlahtandan1`, `kodebarang`, `jammasuk`, `beratmasuk`, `jamkeluar`, `beratkeluar`, `nokendaraan`, `supir`, `nospb`, `nokontrak`, `nodo`, `nosipb`, `thntm1`, `thntm2`, `thntm3`, `jumlahtandan2`, `jumlahtandan3`, `brondolan`, `username`, `millcode`, `beratbersih`,`intex`
	echo"<button class=mybutton onclick=uploadData('".$row."','".$arr."') id=btnUpload>".$_SESSION['lang']['startUpload']."</button>
	<div style='overflow:auto;height:350px;max-width:1000px'>
	 <table class=sortable cellspacing=1 border=0>
	<thead>
	<tr class=rowheader>
	<td>No.</td>
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['notransaksi']."</td>
	<td>".$_SESSION['lang']['kodecustomer']."</td>
	<td>".$_SESSION['lang']['NoKontrak']."</td>
	<td>".$_SESSION['lang']['kebun']."</td>
	<td>".$_SESSION['lang']['pabrik']."</td>
	<td>".$_SESSION['lang']['kodebarang']."</td>
	<td>".$_SESSION['lang']['nospb']."</td>
	<td>".$_SESSION['lang']['nosipb']."</td>
	<td>".$_SESSION['lang']['tahuntanam']." 1</td>
	<td>".$_SESSION['lang']['tahuntanam']." 2</td>
	<td>".$_SESSION['lang']['tahuntanam']." 3</td>
	<td>".$_SESSION['lang']['jmlhTandan']." 1</td>
	<td>".$_SESSION['lang']['jmlhTandan']." 2</td>
	<td>".$_SESSION['lang']['jmlhTandan']." 3</td>
	<td>".$_SESSION['lang']['brondolan']."</td>
	<td>".$_SESSION['lang']['nodo']."</td>
	<td>".$_SESSION['lang']['kodenopol']."</td>
	<td>".$_SESSION['lang']['sopir']."</td>
	<td>".$_SESSION['lang']['jammasuk']."</td>
	<td>".$_SESSION['lang']['jamkeluar']."</td>
	<td>".$_SESSION['lang']['beratBersih']."</td>
	<td>".$_SESSION['lang']['beratMasuk']."</td>
	<td>".$_SESSION['lang']['beratKeluar']."</td>
	<td>".$_SESSION['lang']['username']."</td>
	</tr>
	</thead><tbody id=ListData><tr  class=rowcontent><td colspan=25>Total Data :".$row."</td></tr>";

		while($hsl=mysql_fetch_assoc($res))
		{
			$no+=1;
			$jmMasuk=substr($hsl['DATEIN'],10,9);
			$jmKeluar=substr($hsl['DATEOUT'],10,9);
			echo"<tr class=rowcontent id=row_".$no." >
			<td >".$no."</td>
			<td id=tglData_".$no.">".tanggalnormal($hsl['DATEIN'])."</td>
			<td id=isiData_".$no.">".$hsl['TICKETNO2']."</td>
			<td id=custData_".$no.">".$hsl['TRPCODE']."</td>
			<td id=kntrkNo_".$no.">".$hsl['CTRNO']."</td>
			<td id=kbn_".$no.">".$hsl['UNITCODE']."</td>
			<td id=pabrik_".$no.">".$hsl['MILLCODE']."</td>
			<td id=kdBrg_".$no.">".$hsl['PRODUCTCODE']."</td>
			<td id=spbno_".$no.">".$hsl['SPBNO']."</td>
			<td id=sibno_".$no.">".$hsl['SIPBNO']."</td>
			<td id=thnTnm_".$no.">".$hsl['TAHUNTANAM']."</td>
			<td id=thnTnm2_".$no.">".$hsl['TAHUNTANAM2']."</td>
			<td id=thnTnm3_".$no.">".$hsl['TAHUNTANAM3']."</td>
			<td id=jmlhjjg_".$no.">".$hsl['JMLHJJG']."</td>
			<td id=jmlhjjg2_".$no.">".$hsl['JMLHJJG2']."</td>
			<td id=jmlhjjg3_".$no.">".$hsl['JMLHJJG3']."</td>
			<td id=brndln_".$no.">".$hsl['BRONDOLAN']."</td>
			<td id=nodo_".$no.">".$hsl['NODOTRP']."</td>
			<td id=kdVhc_".$no.">".$hsl['VEHNOCODE']."</td>
			<td id=spir_".$no.">".$hsl['DRIVER']."</td>
			<td id=jmMasuk_".$no.">".$jmMasuk."</td>
			<td id=jmKeluar_".$no.">".$jmKeluar."</td>
			<td align=right id=brtBrsih_".$no.">".$hsl['NETTO']."</td>
			<td align=right id=brtMsk_".$no.">".$hsl['WEI1ST']."</td>
			<td align=right id=brtOut_".$no.">".$hsl['WEI2ND']."</td>
			<td id=usrNm_".$no.">".$hsl['USERID']."</td>
			</tr>
			";
			
		}
	}
	else
	{
		echo" <table class=sortable cellspacing=1 border=0>
	<thead>
	<tr class=rowheader>
	<td>No.</td>
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['notransaksi']."</td>
	<td>".$_SESSION['lang']['nospb']."</td>
	<td>".$_SESSION['lang']['nosipb']."</td>
	<td>".$_SESSION['lang']['nodo']."</td>
	<td>".$_SESSION['lang']['kebun']."</td>
	<td>".$_SESSION['lang']['kodenopol']."</td>
	<td>".$_SESSION['lang']['sopir']."</td>
	<td>".$_SESSION['lang']['jammasuk']."</td>
	<td>".$_SESSION['lang']['jamkeluar']."</td>
	<td>".$_SESSION['lang']['beratBersih']."</td>
	</tr>
	</thead><tbody><tr class=rowcontent align=center><td colspan=12>Not Found</td></tr>";
	}
	echo"</tbody></table></div>";
	break;
	case'uploadData':
	//echo"warning:masuk";
	//$arr="##dbnm##prt##pswrd##ipAdd##period##usrName##lksiServer";
	
	//$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd);
/*	$sCob="select * from ".$dbnm.".mstrxtbs where TICKETNO2='".$idTimbangan."' and GI='0' and OUTIN='0'";
	echo"warning:".$sCob;exit();
	$res=mysql_query($sCob,$corn) or die(mysql_error());
	$hsl=mysql_fetch_assoc($res);*/
	//
	
		$sCek="select notransaksi from ".$dbname.".pabrik_timbangan where notransaksi='".$idTimbangan."'";
		//echo $sCek;exit();
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			if(($kbn=='NULL')||($kbn==''))
			{
				$inTex=0;
			}
			else
			{
				$sCek="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kbn."'";
				$qCek=mysql_query($sCek) or die(mysql_error());
				$rCek=mysql_fetch_assoc($qCek);
		
				if($rCek['induk']!='PMO')
				{
					$inTex=2;
				}
				elseif(eregi("e$",$kbn))
				{
					$inTex=1;
				}
			}
			$DtTime=date("Y-m-d H:i:s");
			
			$sIns="INSERT INTO ".$dbname.".`pabrik_timbangan` (`notransaksi`, `tanggal`, `kodeorg`, `kodecustomer`, `jumlahtandan1`, `kodebarang`, `jammasuk`, `beratmasuk`, `jamkeluar`, `beratkeluar`, `nokendaraan`, `supir`, `nospb`, `nokontrak`, `nodo`, `nosipb`, `thntm1`, `thntm2`, `thntm3`, `jumlahtandan2`, `jumlahtandan3`, `brondolan`, `username`, `millcode`, `beratbersih`,`intex`,`timbangonoff`) VALUES ('".$idTimbangan."','".$tglData."','".$kbn."','".$custData."','".$jmlhjjg."','".$kdBrg."','".$jmMasuk."','".$brtMsk."','".$jmKeluar."','".$brtOut."','".$kdVhc."','".$spir."','".$spbno."','".$kntrkNo."','".$nodo."','".$sibno."','".$thnTnm."','".$thnTnm2."','".$thnTnm3."','".$jmlhjjg2."','".$jmlhjjg3."','".$brndln."','".$usrNm."','".$pabrik."','".$brtBrsih."','".$inTex."','0')";
			//echo "warning".$sIns;exit();
			if(mysql_query($sIns))
			{
				@$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
				$sUp="update ".$dbnm.".mstrxtbs set GI='".$DtTime."' where TICKETNO2='".$idTimbangan."'";
				//echo"warning".$sUp;exit();
				mysql_query($sUp,$corn);
				$stat=1;
				echo $stat;
			}
			else
			{
				//echo "DB Error : ".mysql_error($conn)."___".$sIns;
				$stat=0;
				echo $stat;
			}
		}
		else
		{
			//echo "DB Error : ".mysql_error($conn)."___".$sCek;
			@$corn=mysql_connect($ipAdd.":".$prt,$usrName,$pswrd) or die("Error/Gagal :Unable to Connect to database : ".$ipAdd);
			$sUp="update ".$dbnm.".mstrxtbs set GI='".$DtTime."' where TICKETNO2='".$idTimbangan."'";
			mysql_query($sUp,$corn);
			$stat=1;
			echo $stat;
		}
	break;
	

	case'getDataLokasi':
	//echo"warning:Masuk";
	$sql="select * from ".$dbname.".setup_remotetimbangan where id='".$idRemote."'";
	//echo"warning:".$sql;
	$query=mysql_query($sql) or die(mysql_error());
	$res=mysql_fetch_assoc($query);
	echo $res['ip']."###".$res['port']."###".$res['dbname']."###".$res['username']."###".$res['password'];
	break;
	default:
	break;
}

?>