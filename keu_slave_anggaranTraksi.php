<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$proses=$_POST['proses'];
$thnAnggrn=$_POST['thnAnggrn'];
$kdVhc=$_POST['kdVhc'];
$jmlhHari=$_POST['jmlhHari'];
$pemakaianHm=$_POST['pemakaianHm'];
$jmlhHrTdkOpr=$_POST['jmlhHrTdkOpr'];
$jmlhHrTdkOpr=$_POST['jmlhHrTdkOpr'];
$lksiTugas=$_SESSION['empl']['lokasitugas'];
$kdBrg=$_POST['kdBrg'];
$oldKdbrg=$_POST['oldKdbrg'];
$jmlh=$_POST['jmlh'];

//alokasi

$kdOrg=$_POST['kdOrg'];
$jmlhMeter=$_POST['jmlhMeter'];
$jmlhJan=$_POST['jmlhJan'];
$jmlhFeb=$_POST['jmlhFeb'];
$jmlhMar=$_POST['jmlhMar'];
$jmlhApr=$_POST['jmlhApr'];
$jmlhMei=$_POST['jmlhMei'];
$jmlhJun=$_POST['jmlhJun'];
$jmlhJul=$_POST['jmlhJul'];
$jmlhAug=$_POST['jmlhAug'];
$jmlhSep=$_POST['jmlhSep'];
$jmlhOkt=$_POST['jmlhOkt'];
$jmlhNov=$_POST['jmlhNov'];
$jmlhDes=$_POST['jmlhDes'];



	switch($proses)
	{
	
		case'insert':
		if(($thnAnggrn=='')||($pemakaianHm=='')||($jmlhHari=='')||($jmlhHrTdkOpr==''))
		{
			echo"warning:Please Complete The Form";
			exit();		
		}
		$sCek="select kodevhc,tahun from ".$dbname.".keu_anggaranvhcht where tahun='".$thnAnggrn."' and kodevhc='".$kdVhc."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$jlhOprSthn=$jmlhHari*$pemakaianHm;
			$sIns="insert into ".$dbname.".keu_anggaranvhcht (orgdata, kodevhc, tahun, jlhharioperasi, merterperhari, jlhharitdkoperasi, jlhoperasisetahun) values ('".$lksiTugas."','".$kdVhc."','".$thnAnggrn."','".$jmlhHari."','".$pemakaianHm."','".$jmlhHrTdkOpr."','".$jlhOprSthn."')";
			if(mysql_query($sIns))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		}
		else
		{
			echo"warning:Already Input This Data";
			exit();
		}
		break;
		case'update':
		if(($thnAnggrn=='')||($pemakaianHm=='')||($jmlhHari=='')||($jmlhHrTdkOpr==''))
		{
			echo"warning:Please Complete The Form";
			exit();		
		}
		$jlhOprSthn=$jmlhHari*$pemakaianHm;
		$sUpd="update ".$dbname.".keu_anggaranvhcht set orgdata='".$lksiTugas."', jlhharioperasi='".$jmlhHari."', merterperhari='".$pemakaianHm."', jlhharitdkoperasi='".$jmlhHrTdkOpr."', jlhoperasisetahun='".$jlhOprSthn."' where  kodevhc='".$kdVhc."' and tahun='".$thnAnggrn."'";
		if(mysql_query($sUpd))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		case'insertDetail':
		if(($jmlh=='0'))
		{
			echo"warning:Please Complete The Form";
			exit();		
		}
		$sCek="select kodevhc,tahun,kodebarang from ".$dbname.".keu_anggaranvhcdt where tahun='".$thnAnggrn."' and kodevhc='".$kdVhc."' and kodebarang='".$kdBrg."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$sHrg="select hargasatuan from ".$dbname.".log_5masterbaranganggaran where kodebarang='".$kdBrg."'";
			$qHrg=mysql_query($sHrg) or die(mysql_error());
			$rHrg=mysql_fetch_assoc($qHrg);
			$hrgTot=$rHrg['hargasatuan']*$jmlh;
			$sIns="insert into ".$dbname.".keu_anggaranvhcdt (kodevhc, tahun, kodebarang, jumlah, hargatotal) values ('".$kdVhc."','".$thnAnggrn."','".$kdBrg."','".$jmlh."','".$hrgTot."')";//echo "warning".$sIns;exit();
			if(mysql_query($sIns))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		}
		else
		{
			echo"warning:Already Input This Data";
			exit();
		}
		break;
		case'updateDetail':
		if(($jmlh=='0'))
		{
			echo"warning:Please Complete The Form";
			exit();		
		}
		$sHrg="select hargasatuan from ".$dbname.".log_5masterbaranganggaran where kodebarang='".$kdBrg."'";
		$qHrg=mysql_query($sHrg) or die(mysql_error());
		$rHrg=mysql_fetch_assoc($qHrg);
		$hrgTot=$rHrg['hargasatuan']*$jmlh;
		
		$sUpd="update ".$dbname.".keu_anggaranvhcdt set kodebarang='".$kdBrg."', jumlah='".$jmlh."',hargatotal='".$hrgTot."' where kodevhc='".$kdVhc."' and tahun='".$thnAnggrn."' and kodebarang='".$oldKdbrg."'";
		if(mysql_query($sUpd))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
		break;
		case'insertAlokasi':
		//echo"warning:masukaa";
		if(($jmlhMeter=='0')||($jmlhJan=='0')||($jmlhFeb=='0')||($jmlhMar=='0')||($jmlhApr=='0')||($jmlhMei=='0')||($jmlhJun=='0')||($jmlhJul=='0'))
		{
			echo"warning:Please Complete The Form";
			exit();		
		}
		if(($jmlhAug=='0')||($jmlhSep=='0')||($jmlhNov=='0')||($jmlhDes=='0')||($jmlhDes=='0'))
		{
			echo"warning:Please Complete The Form";
			exit();		
		}
		$tot=intval($jmlhJan+$jmlhFeb+$jmlhMar+$jmlhApr+$jmlhMei+$jmlhJun+$jmlhJul+$jmlhAug+$jmlhSep+$jmlhOkt+$jmlhNov+$jmlhDes);
		if($jmlhMeter!=$tot)
		{
			echo"warning:Total on Jumlah Meter must Same with Total per Month".$tot."____".$jmlhMeter;
			exit();
		}
		$sCek="select kodevhc,tahun,kodeorg from ".$dbname.".keu_anggaranalokasivhc where tahun='".$thnAnggrn."' and kodevhc='".$kdVhc."' and kodeorg='".$kdOrg."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			
			$sIns="insert into ".$dbname.".keu_anggaranalokasivhc (kodevhc, tahun, kodeorg, jlhmeter, jan, feb, mar, apr, mei, jun, jul, agu, sep, okt, nov, des) values ('".$kdVhc."','".$thnAnggrn."','".$kdOrg."','".$jmlhMeter."','".$jmlhJan."','".$jmlhFeb."','".$jmlhMar."','".$jmlhApr."','".$jmlhMei."','".$jmlhJun."','".$jmlhJul."','".$jmlhAug."','".$jmlhSep."','".$jmlhOkt."','".$jmlhNov."','".$jmlhDes."')";//echo "warning".$sIns;exit();
			if(mysql_query($sIns))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		}
		else
		{
			echo"warning:Already Input This Data";
			exit();
		}
		break;
		case'updateAlokasi':
		if(($jmlhMeter=='0')||($jmlhJan=='0')||($jmlhFeb=='0')||($jmlhMar=='0')||($jmlhApr=='0')||($jmlhMei=='0')||($jmlhJun=='0')||($jmlhJul=='0'))
		{
			echo"warning:Please Complete The Form";
			exit();		
		}
		if(($jmlhAug=='0')||($jmlhSep=='0')||($jmlhNov=='0')||($jmlhDes=='0')||($jmlhDes=='0'))
		{
			echo"warning:Please Complete The Form";
			exit();		
		}
		$tot=intval($jmlhJan+$jmlhFeb+$jmlhMar+$jmlhApr+$jmlhMei+$jmlhJun+$jmlhJul+$jmlhAug+$jmlhSep+$jmlhOkt+$jmlhNov+$jmlhDes);
		if($jmlhMeter!=$tot)
		{
			echo"warning:Total on Jumlah Meter must Same with Total per Month".$tot."____".$jmlhMeter;
			exit();
		}		
		$sUpd="update ".$dbname.".keu_anggaranalokasivhc set jlhmeter='".$jmlhMeter."', jan='".$jmlhJan."', feb='".$jmlhFeb."', mar='".$jmlhMar."', apr='".$jmlhApr."', mei='".$jmlhMei."', jun='".$jmlhJun."', jul='".$jmlhJul."', agu='".$jmlhAug."', sep='".$jmlhSep."', okt='".$jmlhOkt."', nov='".$jmlhNov."',des='".$jmlhDes."' where kodevhc='".$kdVhc."' and tahun='".$thnAnggrn."' and kodeorg='".$kdOrg."'  ";
			if(mysql_query($sUpd))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		case'loadData':
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".keu_anggaranvhcht order by `tahun` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$str="select * from ".$dbname.".keu_anggaranvhcht order by `tahun` desc limit ".$offset.",".$limit."";
		if($res=mysql_query($str))
		{
			while($bar=mysql_fetch_assoc($res))
			{
				$no+=1;
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$bar['tahun']."</td>
			<td>".$bar['kodevhc']."</td>
			<td>".$bar['jlhharioperasi']."</td>
			<td>".$bar['jlhoperasisetahun']."</td>
			<td>".$bar['jlhharitdkoperasi']."</td>";
			echo"
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['tahun']."','".$bar['kodevhc']."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar['tahun']."','".$bar['kodevhc']."');\"><img onclick=\"masterPDF('keu_anggaranvhcht','".$bar['tahun'].",".$bar['kodevhc'].",".$bar['orgdata']."','','keu_slaveanggaranTraksiPdf',event);\" title=Print class=resicon src=images/pdf.jpg></td>
			</tr>";
			}	 	 
			echo"
			<tr><td colspan=7 align=center>
			".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
			<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
			<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
			</td>
			</tr>";     	
		}	
		else
		{
		echo " Gagal,".(mysql_error($conn));
		}	
		break;
		case'loadaLokasi':
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".keu_anggaranalokasivhc where tahun='".$thnAnggrn."' and kodevhc='".$kdVhc."' order by `tahun` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$str="select * from ".$dbname.".keu_anggaranalokasivhc where tahun='".$thnAnggrn."' and kodevhc='".$kdVhc."'  order by `tahun` desc limit ".$offset.",".$limit."";//echo $str;
		if($res=mysql_query($str))
		{
			while($bar=mysql_fetch_assoc($res))
			{
				$no+=1;
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$bar['kodeorg']."</td>
			<td>".$bar['jlhmeter']."</td>
			<td>".$bar['jan']."</td>
			<td>".$bar['feb']."</td>
			<td>".$bar['mar']."</td>
			<td>".$bar['apr']."</td>
			<td>".$bar['mei']."</td>
			<td>".$bar['jun']."</td>
			<td>".$bar['jul']."</td>
			<td>".$bar['agu']."</td>
			<td>".$bar['sep']."</td>
			<td>".$bar['okt']."</td>
			<td>".$bar['nov']."</td>
			<td>".$bar['des']."</td>
";
			echo"
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillFieldAlokasi('".$bar['tahun']."','".$bar['kodevhc']."','".$bar['kodeorg']."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldataLok('".$bar['tahun']."','".$bar['kodevhc']."','".$bar['kodeorg']."');\"></td>
			</tr>";
			}	 	 
			echo"
			<tr><td colspan=15 align=center>
			".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
			<button class=mybutton onclick=cariLokasi(".($page-1).");>".$_SESSION['lang']['pref']."</button>
			<button class=mybutton onclick=cariLokasi(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
			</td>
			</tr>";     	
		}	
		else
		{
		echo " Gagal,".(mysql_error($conn));
		}	
		break;
		case'loadDetail':
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".keu_anggaranvhcdt where tahun='".$thnAnggrn."' and kodevhc='".$kdVhc."' order by `tahun` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$str="select * from ".$dbname.".keu_anggaranvhcdt where tahun='".$thnAnggrn."' and kodevhc='".$kdVhc."'  order by `tahun` desc limit ".$offset.",".$limit."";
		if($res=mysql_query($str))
		{
			while($bar=mysql_fetch_assoc($res))
			{
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
			$no+=1;
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$rBrg['namabarang']."</td>
			<td>".number_format($bar['jumlah'],2)."</td>";
			echo"
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillFieldDetail('".$bar['kodebarang']."','".$bar['jumlah']."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldataDet('".$bar['tahun']."','".$bar['kodevhc']."','".$bar['kodebarang']."');\"></td>
			</tr>";
			}	 	 
			echo"
			<tr><td colspan=7 align=center>
			".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
			<button class=mybutton onclick=cariDetail(".($page-1).");>".$_SESSION['lang']['pref']."</button>
			<button class=mybutton onclick=cariDetail(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
			</td>
			</tr>";     	
		}	
		else
		{
		echo " Gagal,".(mysql_error($conn));
		}	
		break;	
		case'getDataHeader':
		$sGet="select * from ".$dbname.".keu_anggaranvhcht where kodevhc='".$kdVhc."' and tahun='".$thnAnggrn."'";
		//echo"warning".$sGet;
		$qGet=mysql_query($sGet) or die(mysql_error());
		$rGet=mysql_fetch_assoc($qGet);
		//orgdata, kodevhc, tahun, jlhharioperasi, merterperhari, jlhharitdkoperasi, jlhoperasisetahun
		echo $rGet['kodevhc']."###".$rGet['tahun']."###".$rGet['jlhharioperasi']."###".$rGet['merterperhari']."###".$rGet['jlhharitdkoperasi'];
		break;	
		case'getDataAlokasi':
		$sGet="select * from ".$dbname.".keu_anggaranalokasivhc where kodevhc='".$kdVhc."' and tahun='".$thnAnggrn."' and kodeorg='".$kdOrg."'";
		//echo"warning".$sGet;
		$qGet=mysql_query($sGet) or die(mysql_error());
		$rGet=mysql_fetch_assoc($qGet);
		//kodevhc, tahun, kodeorg, jlhmeter, jan, feb, mar, apr, mei, jun, jul, agu, sep, okt, nov, des
		echo $rGet['kodeorg']."###".$rGet['jlhmeter']."###".$rGet['jan']."###".$rGet['feb']."###".$rGet['mar']."###".$rGet['apr']."###".$rGet['mei']."###".$rGet['jun']."###".$rGet['jul']."###".$rGet['agu']."###".$rGet['sep']."###".$rGet['okt']."###".$rGet['nov']."###".$rGet['des'];
		
		break;
		case'delHeader':
		$sDelLok="delete from ".$dbname.".keu_anggaranalokasivhc where kodevhc='".$kdVhc."' and tahun='".$thnAnggrn."'";
		if(mysql_query($sDelLok))
		{
			$sDelDetail="delete from ".$dbname.".keu_anggaranvhcdt where kodevhc='".$kdVhc."' and tahun='".$thnAnggrn."'";
			if(mysql_query($sDelDetail))
			{
				$sDel="delete from ".$dbname.".keu_anggaranvhcht where kodevhc='".$kdVhc."' and tahun='".$thnAnggrn."'";
				if(mysql_query($sDel))	
				echo"";
				else
				echo "DB Error : ".mysql_error($conn);
				
			}
			else
			{
				echo "DB Error : ".mysql_error($conn);
			}
		}
		else
		{	
			echo "DB Error : ".mysql_error($conn);
		}
		break;
		case'delLoksi':
		$sDelLok="delete from ".$dbname.".keu_anggaranalokasivhc where kodevhc='".$kdVhc."' and tahun='".$thnAnggrn."' and kodeorg='".$kdOrg."'";
		if(mysql_query($sDelLok))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
		break;
		case'delDet':
		$sDelDetail="delete from ".$dbname.".keu_anggaranvhcdt where kodevhc='".$kdVhc."' and tahun='".$thnAnggrn."' and kodebarang='".$kdBrg."'";
		if(mysql_query($sDelDetail))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
		break;
		default:
		break;
	}



?>