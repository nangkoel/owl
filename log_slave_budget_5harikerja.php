<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$tahunbudget=$_POST['tahunbudget'];
$kodeorg=$_POST['kodeorg'];
$hrsetahun=$_POST['hrsetahun'];
$hrminggu=$_POST['hrminggu'];
$hrlibur=$_POST['hrlibur'];
$hrliburminggu=$_POST['hrliburminggu'];
$hkeffektif=$_POST['hkeffektif'];
$method=$_POST['method'];
$oldtahunbudget=$_POST['oldtahunbudget'];
$oldkodeorg=$_POST['oldkodeorg'];


	switch($method)
	{
		case'insert':
		$oldtahunbudget==''?$oldtahunbudget=$_POST['tahunbudget']:$oldtahunbudget=$_POST['oldtahunbudget'];
		$sCek="select tahunbudget from ".$dbname.".bgt_hk where tahunbudget='".$oldtahunbudget."'";
		$qCek=mysql_query($sCek) or die(mysql_error($conn));
		$rCek=mysql_num_rows($qCek);
                if(strlen($tahunbudget)<4)
                {
                    exit("Error:Panjang Karakter Kurang");
                }
		if($tahunbudget=='')
		{
			echo "warning : Tahun Budget masih kosong";
			exit();
		}
		else if ($hrsetahun=='')
		{
			echo "warning : Hari dalam satu tahun masih kosong";
			exit();
		}
		else if ($hrminggu=='')
		{
			echo "warning : Hari dalam satu minggu masih kosong";
			exit();
		}
		else if ($hrlibur=='')
		{
			echo "warning : Hari libur masih kosong";
			exit();
		}
		else if ($hrliburminggu =='')
		{
			echo "warning : Hari libur minggu masih kosong";
			exit();
		}
		//exit("error".$rCek."__".$sCek);
			if($rCek>0)
			{
				$sDel="delete from ".$dbname.".bgt_hk
						where tahunbudget='".$oldtahunbudget."' ";	   
				//exit("Error".$sDel); 
					if(mysql_query($sDel))
					{
					$sDel2="insert into ".$dbname.".bgt_hk (`tahunbudget`,`harisetahun`,`hrminggu`,`hrlibur`,`hrliburminggu`,`updatedby`) 
                                            values ('".$tahunbudget."','".$hrsetahun."','".$hrminggu."','".$hrlibur."','".$hrliburminggu."','".$_SESSION['standard']['userid']."')";
				//exit("Error".$sDel2);
				if(mysql_query($sDel2))
				echo"";
				else
				echo " Gagal,".addslashes(mysql_error($conn));
					}
					else	
					{
						echo " Gagal,".addslashes(mysql_error($conn));
					}	
			}				
			else
			{
			$sIns="insert into ".$dbname.".bgt_hk (`tahunbudget`,`harisetahun`,`hrminggu`,`hrlibur`,`hrliburminggu`,`updatedby`) 
                            values ('".$tahunbudget."','".$hrsetahun."','".$hrminggu."','".$hrlibur."','".$hrliburminggu."','".$_SESSION['standard']['userid']."')";
			//exit("Error".$sIns);
			if(!mysql_query($sIns))
			{
				echo"Gagal".mysql_error($conn);
			}
			}
			
		
		//		<td>".substr($bar['alamat'],0,50)."</td>
		break;
		case'loadData':
		$str="select * from ".$dbname.".bgt_hk  order by tahunbudget desc";
		$res=mysql_query($str) or die(mysql_error($conn));
		while($bar=mysql_fetch_assoc($res))
		{
		$a[$bar['tahunbudget']]=intval($bar['harisetahun']);
                $b[$bar['tahunbudget']]=intval($bar['hrminggu']);
                $c[$bar['tahunbudget']]=intval($bar['hrlibur']);
                $d[$bar['tahunbudget']]=intval($bar['hrliburminggu']);
                $hasil[$bar['tahunbudget']]=$a[$bar['tahunbudget']]-(($b[$bar['tahunbudget']]+$c[$bar['tahunbudget']])-$d[$bar['tahunbudget']]);
		$no+=1;	
		echo"<tr class=rowcontent>
		<td>".$no."</td>
		<td align=right>".$bar['tahunbudget']."</td>
		<td align=right>".$bar['harisetahun']."</td>
		<td align=right>".$bar['hrminggu']."</td>
		<td align=right>".$bar['hrlibur']."</td>
		<td align=right>".$bar['hrliburminggu']."</td>
		<td align=right>".$hasil[$bar['tahunbudget']]."</td>
		<td align=center><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['tahunbudget']."');\"></td>
		</tr>";	
		}     
		break;

		case'getData':

		$sDt="select * from ".$dbname.".bgt_hk where tahunbudget='".$tahunbudget."'  order by tahunbudget desc";
		$qDt=mysql_query($sDt) or die(mysql_error($conn));
		$rDet=mysql_fetch_assoc($qDt);
		echo $rDet['tahunbudget']."###".$rDet['harisetahun']."###".$rDet['hrminggu']."###".$rDet['hrlibur']."###".$rDet['hrliburminggu'];
		break;
		default:
		break;
	}
?>