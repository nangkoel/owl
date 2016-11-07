<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_POST['proses'];
$karyawanid=$_POST['karyawanid'];
$kdorg=$_POST['kdorg'];
$per=$_POST['per'];
$premi=$_POST['premi'];

switch($proses)
{	
	case'savedataDt':
	
		if($premi>0)
		{
			$str="insert into ".$dbname.".`kebun_premikemandoran` (`kodeorg`,`karyawanid`,`periode`,`jabatan`,`premiinput`,`updateby`,`posting`,`pembagi`) 	
					values ('".$kdorg."','".$karyawanid."','".$per."','RAWATDT','".$premi."','".$_SESSION['standard']['userid']."','1','1')";	
			//	exit("Error:$str");
			if(mysql_query($str))
			{
			}
			else
			{
				$str="update  ".$dbname.".`kebun_premikemandoran` set posting='1',pembagi='1',premiinput='".$premi."',updateby='".$_SESSION['standard']['userid']."'
					where kodeorg='".$kdorg."' and karyawanid='".$karyawanid."' and periode='".$per."' and jabatan='RAWATDT'";	
			//	exit("Error:$str");
				if(mysql_query($str))
				{
					
				}
				else
				{
					echo " Gagal,".addslashes(mysql_error($conn));
				}
				//echo " Gagal,".addslashes(mysql_error($conn));
			}
		}
	break;
	
	case'savedataAb':
	//exit("Error:MASUK");
		if($premi>0)
		{
			$str="insert into ".$dbname.".`kebun_premikemandoran` (`kodeorg`,`karyawanid`,`periode`,`jabatan`,`premiinput`,`updateby`,`posting`,`pembagi`) 	
					values ('".$kdorg."','".$karyawanid."','".$per."','RAWATAB','".$premi."','".$_SESSION['standard']['userid']."','1','1')";	
			//	exit("Error:$str");
			if(mysql_query($str))
			{
			}
			else
			{
				$str="update  ".$dbname.".`kebun_premikemandoran` set posting='1',pembagi='1',premiinput='".$premi."',updateby='".$_SESSION['standard']['userid']."'
					where kodeorg='".$kdorg."' and karyawanid='".$karyawanid."' and periode='".$per."' and jabatan='RAWATAB'";	
			//	exit("Error:$str");
				if(mysql_query($str))
				{
					
				}
				else
				{
					echo " Gagal,".addslashes(mysql_error($conn));
				}
				//echo " Gagal,".addslashes(mysql_error($conn));
			}
		}
	break;
	
	case'savedataCu':
	//exit("Error:MASUK");
		if($premi>0)
		{
			$str="insert into ".$dbname.".`kebun_premikemandoran` (`kodeorg`,`karyawanid`,`periode`,`jabatan`,`premiinput`,`updateby`,`posting`,`pembagi`) 	
					values ('".$kdorg."','".$karyawanid."','".$per."','RAWATCU','".$premi."','".$_SESSION['standard']['userid']."','1','1')";	
			//	exit("Error:$str");
			if(mysql_query($str))
			{
			}
			else
			{
				$str="update  ".$dbname.".`kebun_premikemandoran` set posting='1',premiinput='".$premi."',pembagi='1',updateby='".$_SESSION['standard']['userid']."'
					where kodeorg='".$kdorg."' and karyawanid='".$karyawanid."' and periode='".$per."' and jabatan='RAWATCU'";	
			//	exit("Error:$str");
				if(mysql_query($str))
				{
					
				}
				else
				{
					echo " Gagal,".addslashes(mysql_error($conn));
				}
				//echo " Gagal,".addslashes(mysql_error($conn));
			}
		}
	break;
	
	default:
}

?>