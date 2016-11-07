<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
//$postingSpk=$param['postingSpk'];
$proses=$param['proses'];
$noTrans=$param['noTrans'];
switch($proses)
{
	case'postingSpk':
	$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='".$app."'");
	$tmpPost = fetchData($qPosting);
	$postJabatan = $tmpPost[0]['jabatan'];
	
	$sCek="select kodeorg,notransaksi,divisi,posting from ".$dbname.".log_spkht where notransaksi='".$noTrans."' and posting=0
               and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_num_rows($qCek);
	if($rCek>0)
	{
		//periksa realisasi
                while($bar=mysql_fetch_object($qCek))
                {
                    $x =0;
                    $strx="select sum(jumlahrealisasi) from ".$dbname.".log_baspk 
                          where notransaksi='".$noTrans."'";
                    $resx=mysql_query($strx);
                    while($barx=mysql_fetch_array($resx))
                    {
                      $x= $barx[0]; 
                    }   
                    //lihat postingan-=============================
                    $y ='';
                    $strx="select statusjurnal from ".$dbname.".log_baspk 
                          where notransaksi='".$noTrans."' and statusjurnal=0";
                    $resx=mysql_query($strx);           
                    if(mysql_num_rows($resx)>0)
                        exit('Warning:Realisasi SPK belum di posting');
                    else if($x==0 and $y=='')
                        exit('Warning:Belum Ada Realisasi');
                    else
                    {}
            
                }
               
                $sCekTot="select kodeblok from ".$dbname.".log_spkdt where notransaksi='".$noTrans."'";
		$qCekTot=mysql_query($sCekTot) or die(mysql_error());
		$rCekTot=mysql_num_rows($qCekTot);
		
		$sCekTot2="select kodeblok from ".$dbname.".log_baspk where notransaksi='".$noTrans."'";
		$qCekTot2=mysql_query($sCekTot2) or die(mysql_error());
		$rCekTot2=mysql_num_rows($qCekTot2);
		if($rCekTot2==0 or $rCekTot2=='')
		{
			echo"warning:BAPP Belum Ada Realisasi";
			exit();
		}
		else
		{
			$sUp="update  ".$dbname.".log_spkht set posting='1' where notransaksi='".$noTrans."'";
			//echo "warning".$sUp;exit();
			if(!mysql_query($sUp))
			{
				echo "DB Error : ".mysql_error($conn);
				exit();
			}
			else
			{
				$sUpBaspk="update ".$dbname.".log_baspk set posting='1' where notransaksi='".$noTrans."'";
				if(!mysql_query($sUpBaspk))
				{
					$sUp="update  ".$dbname.".log_spkht set posting='0' where notransaksi='".$noTrans."'";
					mysql_query($sUp) or die(mysql_error());
					echo "DB Error : ".mysql_error($conn);
					exit();
				}
			}
		}
	}
	else
	{
		echo"warning:Sudah Terposting";
		exit();
	}
	break;
	default:
	break;
}
?>