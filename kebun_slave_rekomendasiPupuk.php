<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$kdBrg=$_POST['kdBrg'];
$periode=$_POST['periode'];
$idKbn=$_POST['idKbn'];
$thnTnm=$_POST['thnTnm'];
$jnsPpk=$_POST['jnsPpk'];
$dosis=$_POST['dosis'];
$dosis2=$_POST['dosis2'];
$dosis3=$_POST['dosis3'];
$jnsBibit=$_POST['jnsBibit'];
$satuan=$_POST['satuan'];
$kdAfd=$_POST['kdAfd'];
$kdBlok=$_POST['kdBlok'];
$oldBlok=$_POST['oldBlok'];

	switch($proses)
	{
		//load data
		case'loadData':
		//$thnBln=date("Y-m");
		OPEN_BOX();
		 echo"<fieldset>
<legend>".$_SESSION['lang']['list']."</legend>";
			echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_rekomendasipupuk','','','kebun_slave_rekomendasipupukPdf',event);\">&nbsp;
			<img onclick=dataKeExcel(event,'kebun_slave_rekomendasipupukExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'>
			<table cellspacing=1 border=0 id=rkmndsiPupuk class='sortable'>
		<thead>
<tr class=rowheader>
<td>No</td>
<td>".$_SESSION['lang']['tahunpupuk']."</td>
<td>".$_SESSION['lang']['afdeling']."</td>
<td>".$_SESSION['lang']['blok']."</td>
<td>".$_SESSION['lang']['tahuntanam']."</td>
<td>".$_SESSION['lang']['jenisPupuk']."</td>
<td>".$_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']." 1</td>
<td>".$_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']." 2</td>
<td>".$_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']." 3</td>
<td>".$_SESSION['lang']['satuan']."</td>
<td>".$_SESSION['lang']['jenisbibit']."</td>
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
		
		$sql2="select count(*) as jmlhrow from ".$dbname.".kebun_rekomendasipupuk where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `periodepemupukan` desc";
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$slvhc="select * from ".$dbname.".kebun_rekomendasipupuk where  substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `periodepemupukan` desc limit ".$offset.",".$limit."";
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		while($res=mysql_fetch_assoc($qlvhc))
		{
			$skdBrg="select  namabarang,satuan from ".$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";//echo $skdBrg;
			$qkdBrg=mysql_query($skdBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qkdBrg);
			
			$sBibit="select jenisbibit  from ".$dbname.".setup_jenisbibit where jenisbibit='".$res['jenisbibit']."'" ;
			$qBibit=mysql_query($sBibit) or die(mysql_error());
			$rBibit=mysql_fetch_assoc($qBibit);
			
			$sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$res['kodeorg']."'";
			$qOrg=mysql_query($sOrg) or die(mysql_error());
			$rOrg=mysql_fetch_assoc($qOrg);
			
			$no+=1;

		echo"
					<tr class=rowcontent>
					<td>".$no."</td>
					<td>". $res['periodepemupukan']."</td>
					<td>". $rOrg['namaorganisasi']."</td>
					<td>". $res['blok']."</td>
					<td>". $res['tahuntanam']."</td>
					<td>". $rBrg['namabarang']."</td>
					<td align='right'>". $res['dosis']."</td>
					<td align='right'>". $res['dosis2']."</td>
					<td align='right'>". $res['dosis3']."</td>
					<td>". $rBrg['satuan']."</td>
					<td>".$rBibit['jenisbibit']."</td>";
						if(substr($res['kodeorg'],0,4)==$_SESSION['empl']['lokasitugas'])
						{
							echo"
							<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['periodepemupukan']."','".$res['kodeorg']."','".$res['tahuntanam']."','".$res['blok']."');\">
							<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$res['periodepemupukan']."','".$res['kodeorg']."','".$res['tahuntanam']."','".$res['blok']."');\" >
						</td>
						</tr>";
						}
					}
					echo"
					<tr><td colspan=9 align=center>
					".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
					<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
					<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
					</td>
					</tr>";
					echo"</table></fieldset>";
					CLOSE_BOX();
		break;
		case'getSatuan':
			$skdBrg="select  satuan from ".$dbname.".log_5masterbarang where kodebarang='".$kdBrg."'";//echo $skdBrg;
			$qkdBrg=mysql_query($skdBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qkdBrg);
			echo $rBrg['satuan'];
		break;
		
		///insert data
		case'insert':
		//echo"warning:masuk";
		if(($jnsPpk=='')||($dosis==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$sCek="select kodeorg,tahuntanam,periodepemupukan from ".$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."' and blok='".$kdBlok."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$sIns="insert into ".$dbname.".kebun_rekomendasipupuk (kodeorg,blok, tahuntanam, kodebarang, dosis, dosis2, dosis3, satuan, periodepemupukan, jenisbibit) values 
			('".$idKbn."','".$kdBlok."','".$thnTnm."','".$jnsPpk."','".$dosis."','".$dosis2."','".$dosis3."','".$satuan."','".$periode."','".$jnsBibit."')";
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
		$sGet="select * from ".$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."' and blok='".$kdBlok."'";
		//echo"warning:".$sGet;
		$qGet=mysql_query($sGet) or die(mysql_error());
		$rGet=mysql_fetch_assoc($qGet);
		
		echo $rGet['kodeorg']."###".$rGet['kodebarang']."###".$rGet['dosis']."###".$rGet['satuan']."###".$rGet['periodepemupukan']."###".$rGet['jenisbibit']."###".$rGet['blok']."###".$rGet['dosis2']."###".$rGet['dosis3'];
		break;
		
		case'update':
		if(($jnsPpk=='')||($dosis==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$sUp="update ".$dbname.".kebun_rekomendasipupuk set kodebarang='".$jnsPpk."', dosis='".$dosis."', dosis2='".$dosis2."', dosis3='".$dosis3."', satuan='".$satuan."', jenisbibit='".$jnsBibit."',blok='".$kdBlok."',tahuntanam='".$thnTnm."' where kodeorg='".$idKbn."' and periodepemupukan='".$periode."' and blok='".$oldBlok."'";
			//echo"warning:".$sIns;exit();
			if(mysql_query($sUp))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		//hapus data
		case'delData':
		$sDel="delete from ".$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and blok='".$kdBlok."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."'";
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
<td>No</td>
<td>".$_SESSION['lang']['tahunpupuk']."</td>
<td>".$_SESSION['lang']['kebun']."</td>
<td>".$_SESSION['lang']['tahuntanam']."</td>
<td>".$_SESSION['lang']['jenisPupuk']."</td>
<td>".$_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']."</td>
<td>".$_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']." 2</td>
<td>".$_SESSION['lang']['dosis']." ".$_SESSION['lang']['rotasi']." 3</td>
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
				$where.=" kodeorg LIKE '%".$idKbn."%'";
			}
			elseif(($periode!='')&&($idKbn!=''))
			{
				$where.=" periodepemupukan LIKE '%".$periode."%' and kodeorg LIKE '%".$idKbn."%'";
			}
		//echo $strx; exit();
			$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$sql2="select count(*) as jmlhrow from ".$dbname.".kebun_rekomendasipupuk where  ".$where." order by `periodepemupukan` desc";
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
                
		$strx="select * from ".$dbname.".kebun_rekomendasipupuk where ".$where." order by periodepemupukan desc limit ".$offset.",".$limit."";
				
		// echo "warning:".$strx; exit();
		
		
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
					<td>". $res['dosis2']."</td>
					<td>". $res['dosis3']."</td>
					<td>". $rBrg['satuan']."</td>
					<td>".$rBibit['jenisbibit']."</td>";
					if(substr($res['kodeorg'],0,4)==$_SESSION['empl']['lokasitugas'])
						{
							echo"
							<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."');\">
							<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['notransaksi']."');\" >
						</td>
							</tr>";
						}
					}
                                        echo"
					<tr><td colspan=9 align=center>
					".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
					<button class=mybutton onclick=cariHasil(".($page-1).");>".$_SESSION['lang']['pref']."</button>
					<button class=mybutton onclick=cariHasil(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
					</td>
					</tr>";
					echo"</tbody></table></div></fieldset>";
					
				}
			 }	
			else
			{
				echo "Gagal,".(mysql_error($conn));
			}	
			CLOSE_BOX();
		break;
		case'getBlok':
		$optBlok="<option value=></option>";
		$sBlok="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$kdAfd."'";
		//echo "warning".$kdBlok;exit();
		$qBlok=mysql_query($sBlok) or die(mysql_error());
		while($rBlok=mysql_fetch_assoc($qBlok))
		{
			if($kdBlok!='')
			{
				//echo"test";
				$optBlok.="<option value='".$rBlok['kodeorganisasi']."'  ".($kdBlok==$rBlok['kodeorganisasi']?'selected':'').">".$rBlok['namaorganisasi']."</option>";
			}
			else
			{
				$optBlok.="<option value=".$rBlok['kodeorganisasi'].">".$rBlok['namaorganisasi']."</option>";
			}
		}
		echo $optBlok;
		break;
		case'getThn':
		$sThn="select tahuntanam from ".$dbname.".setup_blok where kodeorg='".$kdBlok."'";
		//echo "warning".$sThn;
		$qThn=mysql_query($sThn) or die(mysql_error());
		$rThn=mysql_fetch_assoc($qThn);
		echo $rThn['tahuntanam'];
		break;
		default:
		break;
	}
?>