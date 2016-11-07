<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include('lib/zMysql.php');
include_once('lib/zLib.php');

$method =$_POST['method'];
$tanggalkeluar=tanggalsystem($_POST['tanggalkeluar']);
$karyawanid=$_POST['karyawanid'];
$alasan=$_POST['alasan'];


switch($method)
{
	
	case'getFormKeluar':
	
		echo"
			<table cellpadding=1 cellspacing=1 border=0 class=sortable>
				<thead><tr class=rowheader>
					<td colspan=3 align=center align=center>".$_SESSION['lang']['alasankeluar']."</td>
				</tr></thead>";
				
					echo"<tr class=rowcontent>
							<td>".$_SESSION['lang']['alasankeluar']."</td>
							<td>:</td>
							<td><textarea name=textarea id=alasan cols=45 rows=5></textarea></td>
						</tr>";
				echo"<tr class=rowcontent>
					<td colspan=3 align=center>
						<button class=mybutton onclick=saveFormKeluar()>".$_SESSION['lang']['save']."</button>
						
					</td>
				</tr>
				
			</table>";//<button class=mybutton onclick=cancelFormApv()>".$_SESSION['lang']['cancel']."</button>
	break;
	
	
	
	case'saveFormKeluar':
		$i="insert into ".$dbname.".sdm_exitinterview (karyawanid,tanggal,alasan,updateby) 
			values ('".$karyawanid."','".$tanggalkeluar."','".$alasan."','".$_SESSION['standard']['userid']."')";
			//exit("Error:$i");
        if(mysql_query($i))
        {
        }
        else
        {
            echo " Gagal,".addslashes(mysql_error($conn));
        }
	
	
	
	 	
	break;
	
	
}

?>