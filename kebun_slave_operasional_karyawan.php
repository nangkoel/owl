<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
	case 'getAllPt':
		if($param['tipe']=='all') {
            $pt=$_SESSION['empl']['kodeorganisasi'];
			$str="select karyawanid,nik,namakaryawan,subbagian from ".$dbname.".datakaryawan where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')  and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
                and tipekaryawan in('2','3','4','6') order by namakaryawan";
        } else {    
            $subbagian=substr($param['kodeorg'],0,4);
            $str="select karyawanid,nik,namakaryawan,subbagian from ".$dbname.".datakaryawan where lokasitugas='".$subbagian."'  and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
                and tipekaryawan in('2','3','4','6') order by namakaryawan";
        }   
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            echo"<option value='".$bar->karyawanid."'>".$bar->nik." - ".$bar->namakaryawan." - ".$bar->subbagian."</option>";
        }
		break;
	default:
	break;
}
?>