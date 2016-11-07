<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php
$karyawanid=$_POST['karyawanid'];
$jumlahkuota=$_POST['jumlahkuota'];
$method=$_POST['method'];
?>

<?php
switch($method)
{
	case 'insert':
            if($_SESSION['language']=='ID'){           
                if (empty($karyawanid) || empty($jumlahkuota)){
                    echo "peringatan: Anda harus melengkapi kolom isian!";
                    exit();
                }

                $sql_cek_karyawanid="select*from ".$dbname.".log_5kuotabensin where karyawanid like '".$karyawanid."'" ;
                $hasil = mysql_query($sql_cek_karyawanid);
                $jumlah_record= mysql_num_rows($hasil);
                if ($jumlah_record > 0) {
                    echo "peringatan: Karyawan ini sudah ada dalam daftar!";
                    exit();                
                    }

                $i="insert into ".$dbname.".log_5kuotabensin (karyawanid,jumlah,updateby)
                values ('".$karyawanid."','".$jumlahkuota."','".$_SESSION['standard']['userid']."')";
                if(mysql_query($i))
                echo "Berhasil disimpan.";
                else
                echo "Gagal, ".addslashes(mysql_error($conn));
            }else{
                if (empty($karyawanid) || empty($jumlahkuota)){
                    echo "warning: You must complete the form!";
                    exit();
                }

                $sql_cek_karyawanid="select*from ".$dbname.".log_5kuotabensin where karyawanid like '".$karyawanid."'" ;
                $hasil = mysql_query($sql_cek_karyawanid);
                $jumlah_record= mysql_num_rows($hasil);
                if ($jumlah_record > 0) {
                    echo "warning: This employee already exist in the list!";
                    exit();                
                    }

                $i="insert into ".$dbname.".log_5kuotabensin (karyawanid,jumlah,updateby)
                values ('".$karyawanid."','".$jumlahkuota."','".$_SESSION['standard']['userid']."')";
                if(mysql_query($i))
                echo "Successfully saved.";
                else
                echo "Failed, ".addslashes(mysql_error($conn));                
            }
	break;
	
	case 'update':
            if($_SESSION['language']=='ID'){
                if (empty($karyawanid) || empty($jumlahkuota)){
                    echo "peringatan: Anda harus melengkapi kolom isian!";
                    exit();
                }

                $i="update ".$dbname.".log_5kuotabensin set jumlah=".$jumlahkuota.",updateby='".$_SESSION['standard']['userid'].
                "' where karyawanid='".$karyawanid."'";
                if(mysql_query($i))
                echo "Berhasil disimpan.";
                else
                echo "Gagal, ".addslashes(mysql_error($conn));
            }else{
                if (empty($karyawanid) || empty($jumlahkuota)){
                    echo "warning: You must complete the form!";
                    exit();
                }

                $i="update ".$dbname.".log_5kuotabensin set jumlah=".$jumlahkuota.",updateby='".$_SESSION['standard']['userid'].
                "' where karyawanid='".$karyawanid."'";
                if(mysql_query($i))
                echo "Successfully saved.";
                else
                echo "Failed, ".addslashes(mysql_error($conn));
            }                
	break;
		
        case'loadData':
                echo"
                <div style='height:320px;overflow:auto'>
                        <table class=sortable cellspacing=1 border=0>
                     <thead>
                                 <tr class=rowheader>
                                         <td align=center>".$_SESSION['lang']['nourut']."</td>
                                         <td align=center>".$_SESSION['lang']['karyawanid']."</td>
                                         <td align=center>".$_SESSION['lang']['nik']."</td>
                                         <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
                                         <td align=center nowrap>".$_SESSION['lang']['kuotaperbulan']."</td>
                                         <td align=center nowrap>".$_SESSION['lang']['updateby']."</td>
                                         <td align=center>Aksi</td>                                     
                                 </tr>
                        </thead>
                        <tbody>";

                    //$ql2="select * from ".$dbname.".log_5kuotabensin WHERE kodeorganisasi LIKE '".$_SESSION['empl']['kodeorganisasi']."'";
                    $ql2="select * from ".$dbname.".log_5kuotabensin";
                    //$ql2="SELECT log_5kuotabensin.karyawanid, datakaryawan.nik, datakaryawan.namakaryawan, log_5kuotabensin.jumlah, log_5kuotabensin.updatetime, log_5kuotabensin.updateby, datakaryawan.kodeorganisasi
                    //      FROM `log_5kuotabensin`
                    //      INNER JOIN `datakaryawan` ON datakaryawan.karyawanid = log_5kuotabensin.karyawanid
                    //      WHERE datakaryawan.kodeorganisasi LIKE '".$_SESSION['empl']['kodeorganisasi']."'";
                    $n=mysql_query($ql2) or die(mysql_error());
                    $no=0;
                    $data = array();
                    $optKarRow = array();
                    while($d=mysql_fetch_assoc($n)) {
                        $data[] = $d;
                    }
                    if(!empty($data)) {
                        $whereKarRow = "karyawanid in (";
                        $notFirst = false;
                        foreach($data as $key=>$row) {
                            if($row['updateby']!='') {
                                if($notFirst==false) {
                                    $whereKarRow .= $row['updateby'];
                                    $notFirst=true;
                                } else {
                                    $whereKarRow .= ",".$row['updateby'];
                                }
                            }
                        }
                        $whereKarRow .= ")";
                        $optKarRow = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKarRow,'0',true);
                    }
                    $dataShow = $data;
                    foreach($dataShow as $key=>$row) {
                            $no+=1;
                            echo "<tr class=rowcontent>";
                            echo "<td align=center>".$no."</td>";
                            echo "<td align=left>".$row['karyawanid']."</td>";
                            
                            $kriteria1="karyawanid like '".$row['karyawanid']."'";
                            $carinik = makeOption($dbname,'datakaryawan','karyawanid,nik',$kriteria1,'0',true);
                            
                            echo "<td>".$carinik[$row['karyawanid']]."</td>";
                            
                            $kriteria2="karyawanid like '".$row['karyawanid']."'";
                            $carinama = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$kriteria2,'0',true);
                            
                            echo "<td>".$carinama[$row['karyawanid']]."</td>";
                            echo "<td align=right>".$row['jumlah']."</td>";
                            echo "<td align=left nowrap>".$optKarRow[$row['updateby']]."</td>";
                            echo "<td align=center nowrap>
                            <img src=images/application/application_edit.png class=resicon title='Edit' caption='Edit' onclick=\"fillField('".$row['karyawanid']."','".$row['jumlah']."');\">
                            <img src=images/application/application_delete.png class=resicon title='Delete' caption='Delete' onclick=\"del('".$row['karyawanid']."',".$row['jumlah'].");\"></td>";
                            echo "</tr>";
                    }
                    echo"</tbody></table>";
        break;

	case 'delete':
                //exit("Error:hahaha");
                //$i="delete from ".$dbname.".log_5kuotabensin where karyawanid='".$karyawanid."'";
                //exit("Error.$str");
                if($_SESSION['language']=='ID'){
                    $i="delete from ".$dbname.".log_5kuotabensin where karyawanid='".$karyawanid."'";
                    if(mysql_query($i)){
                        echo "Berhasil dihapus.";
                    }else{
                        echo "Gagal, ".addslashes(mysql_error($conn));
                    }
                }else{
                    $i="delete from ".$dbname.".log_5kuotabensin where karyawanid='".$karyawanid."'";
                    if(mysql_query($i)){
                        echo "Successfully deleted.";
                    }else{
                        echo "Failed, ".addslashes(mysql_error($conn));
                    }
                }
                   
	break;
        
default:
}
?>
