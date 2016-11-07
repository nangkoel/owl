<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$kdOrg=$_POST['kdOrg'];
$tgl=tanggalsystem($_POST['tgl']);
$noSpb=$_POST['noSpb'];
$noTrans=$_POST['noTrans'];
	switch($proses)
	{
		//load data
		case'getData':
		//$thnBln=date("Y-m");
		OPEN_BOX();
		 echo"<fieldset>
<legend>".$_SESSION['lang']['list']."</legend>";
			echo"<div style=\"width:600px; height:450px; overflow:auto;\">
			<table cellspacing=1 border=0 id=rkmndsiPupuk class='sortable'>
		<thead>
<tr class=rowheader>
<td>No</td>
<td>".$_SESSION['lang']['kodeorg']."</td>
<td>".$_SESSION['lang']['tglNospb']."</td>
<td>".$_SESSION['lang']['nospb']."</td>
<td>".$_SESSION['lang']['status']."</td>
<td>Action</td>
</tr>
</thead>
<tbody>
";
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$sql2="select count(*) as jmlhrow from ".$dbname.".pabrik_timbangan where kodeorg='".$kdOrg."' and tanggal='".$tgl."' order by `tanggal` desc";
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$slvhc="select nospb,notransaksi from ".$dbname.".pabrik_timbangan where kodeorg='".$kdOrg."' and tanggal='".$tgl."' order by `tanggal` desc limit ".$offset.",".$limit."";//echo $slvhc;
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		$row=mysql_num_rows($qlvhc);
		if($row>0)
		{
			while($res=mysql_fetch_assoc($qlvhc))
			{
				 $sNospb="select a.tanggal,a.kodeorg,a.posting,b.* from ".$dbname.".kebun_spbht a inner join ".$dbname.".kebun_spbdt b on a.nospb=b.nospb where a.nospb='".$res['nospb']."'"; //echo $sNospb;
				 $qNospb=mysql_query($sNospb) or die(mysql_error());
				 $rNospb=mysql_fetch_assoc($qNospb);
				if($rNospb['posting']<1)
				{
					$stat="Belum";
				}
				elseif($rNospb['posting']>0)
				{
					$stat="Sudah";	
				}
				$no+=1;
				echo"
				<tr class=rowcontent>
				<td>".$no."</td>
				<td>". $rNospb['kodeorg']."</td>
				<td>". tanggalnormal($rNospb['tanggal'])."</td>
				<td>". $rNospb['nospb']."</td>
				<td>". $stat."</td><td>";
				if($rNospb['posting']<1)
				{
					echo"<a href=# onclick=prosesData('".$rNospb['nospb']."','".$res['notransaksi']."')>".$_SESSION['lang']['belumposting']."</a>";
				}
				else
				{
					echo $_SESSION['lang']['posting'];
				}
				echo"</td></tr>";
			}
			
			echo"</tbody></table></div></fieldset>";
		}
		else
		{
			echo"<tr class=rowcontent><td colspan='6' align='center'>Not Found</td></tr></tbody></table></div></fieldset>";
		}
		CLOSE_BOX();
		break;
		case'PostingData':
		//echo"warning:masuk";
			$sNospb="select * from ".$dbname.".kebun_spbdt where nospb='".$noSpb."'";
			$qNospb=mysql_query($sNospb) or die(mysql_error());
			while($rNospb=mysql_fetch_assoc($qNospb))
			{
				$sTimbngn="select * from ".$dbname.".pabrik_timbangan where notransaksi='".$noTrans."' and nospb='".$noSpb."' ";
				$qTimbngn=mysql_query($sTimbngn) or die(mysql_error());
				$rTimbngn=mysql_fetch_assoc($qTimbngn);
                                $rTimbngn['beratbersih']=$rTimbngn['beratbersih']-$rTimbngn['kgpotsortasi'];
				$x=intval($rTimbngn['beratbersih']);
				$y=intval($rTimbngn['beratbersih']+$rTimbngn['brondolan']);
				$sBagi="SELECT sum(jjg+bjr+mentah+busuk+matang+lewatmatang)as pembagi,jjg,bjr  FROM ".$dbname.".kebun_spbdt where blok='".$rNospb['blok']."'";
				//echo "warning:".$sBagi."_____";
				$qBagi=mysql_query($sBagi) or die(mysql_error());
				$rBagi=mysql_fetch_assoc($qBagi);
				$berat=intval($rBagi['jjg'])*intval($rBagi['bjr']);
				$persen=$berat/intval($rBagi['pembagi']);
				
				$kgWb=$persen*$x;
				$totKg=$persen*$y;
				$kgBjr=$berat;
				
				$sUpd="update ".$dbname.".kebun_spbdt set kgwb='".$kgWb."',totalkg='".$totKg."',kgbjr='".$kgBjr."' where nospb='".$rNospb['nospb']."' and blok='".$rNospb['blok']."' "; //echo "warning:".$sUpd;exit();
				if(mysql_query($sUpd))
				{
					$sUpdate="update ".$dbname.".kebun_spbht set posting='1',postingby='".$_SESSION['standard']['userid']."' where nospb='".$rNospb['nospb']."'";
					if(mysql_query($sUpdate))
					echo"";
					else
					echo "DB Error : ".mysql_error($conn);				
				}
				else
				{
					echo "DB Error : ".mysql_error($conn);
				}
				
				
			}
		break;
		
		///insert data
		case'insert':
		//echo"warning:masuk";
		if(($jnsPpk=='')||($dosis==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$sCek="select kodeorg,tahuntanam,periodepemupukan from ".$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$sIns="insert into ".$dbname.".kebun_rekomendasipupuk (kodeorg, tahuntanam, kodebarang, dosis, satuan, periodepemupukan, jenisbibit) values 
			('".$idKbn."','".$thnTnm."','".$jnsPpk."','".$dosis."','".$satuan."','".$periode."','".$jnsBibit."')";
			//echo"warning:".$sIns;exit();
			if(mysql_query($sIns))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		}
		else
		{
			echo"warning:This Data Already Input";
			exit();
		}
		break;
		
		//getData
		case'getData':
		$sGet="select * from ".$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."'";
		//echo"warning:".$sGet;
		$qGet=mysql_query($sGet) or die(mysql_error());
		$rGet=mysql_fetch_assoc($qGet);
		
		echo $rGet['kodeorg']."###".$rGet['tahuntanam']."###".$rGet['kodebarang']."###".$rGet['dosis']."###".$rGet['satuan']."###".$rGet['periodepemupukan']."###".$rGet['jenisbibit'];
		break;
		
		case'update':
		if(($jnsPpk=='')||($dosis==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$sUp="update ".$dbname.".kebun_rekomendasipupuk set kodebarang='".$jnsPpk."', dosis='".$dosis."', satuan='".$satuan."', jenisbibit='".$jnsBibit."' where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."'";
			//echo"warning:".$sIns;exit();
			if(mysql_query($sUp))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		//hapus data
		case'delData':
		$sDel="delete from ".$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."'";
		if(mysql_query($sDel))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		
		//cari transaksi
			case 'cariData':
		 OPEN_BOX();
		 echo"<fieldset>
<legend>".$_SESSION['lang']['result']."</legend>";
			echo"<div style=\"width:600px; height:450px; overflow:auto;\">
			<table cellspacing=1 border=0 class='sortable'>
		<thead>
<tr class=rowheader>
<td>".$_SESSION['lang']['tahunpupuk']."</td>
<td>".$_SESSION['lang']['kebun']."</td>
<td>".$_SESSION['lang']['tahuntanam']."</td>
<td>".$_SESSION['lang']['jenisPupuk']."</td>
<td>".$_SESSION['lang']['dosis']."</td>
<td>".$_SESSION['lang']['satuan']."</td>
<td>".$_SESSION['lang']['jenisbibit']."</td>
<td>Action</td>
</tr>
</thead>
<tbody>
";		
			if($periode!='')
			{
				$where=" periodepemupukan LIKE  '%".$periode."%'";
			}
			elseif($idKbn!='')
			{
				$where.=" kodeorg LIKE '".$idKbn."'";
			}
			elseif(($periode!='')&&($idKbn!=''))
			{
				$where.=" periodepemupukan LIKE '%".$periode."%' and kodeorg LIKE '%".$idKbn."%'";
			}
		//echo $strx; exit();
		
				$strx="select * from ".$dbname.".kebun_rekomendasipupuk where ".$where." order by periodepemupukan desc";
				
		//echo "warning:".$strx; exit();
		
		
			if($qry=mysql_query($strx))
			{
				$numrows=mysql_num_rows($qry);
				if($numrows<1)
				{
					echo"<tr class=rowcontent><td colspan=9>Not Found</td></tr>";
				}
				else
				{
					while($res=mysql_fetch_assoc($qry))
					{	
					$skdBrg="select  namabarang,satuan from ".$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";//echo $skdBrg;
					$qkdBrg=mysql_query($skdBrg) or die(mysql_error());
					$rBrg=mysql_fetch_assoc($qkdBrg);
					
					$sBibit="select jenisbibit  from ".$dbname.".setup_jenisbibit where jenisbibit='".$res['jenisbibit']."'" ;
					$qBibit=mysql_query($sBibit) or die(mysql_error());
					$rBibit=mysql_fetch_assoc($qBibit);
					
					$no+=1;
					echo"
					<tr class=rowcontent>
					<td>".$no."</td>
					<td>". $res['periodepemupukan']."</td>
					<td>". $res['kodeorg']."</td>
					<td>". $res['tahuntanam']."</td>
					<td>". $rBrg['namabarang']."</td>
					<td>". $res['dosis']."</td>
					<td>". $rBrg['satuan']."</td>
					<td>".$rBibit['jenisbibit']."</td>";
					echo"
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."');\">
					<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['notransaksi']."');\" >
				</td>
					</tr>";
					}
					echo"</tbody></table></div></fieldset>";
					
				}
			 }	
			else
			{
				echo "Gagal,".(mysql_error($conn));
			}	
			CLOSE_BOX();
		break;
		default:
		break;
	}
?>