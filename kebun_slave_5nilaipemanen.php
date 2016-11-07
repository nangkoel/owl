<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php
$method=$_POST['method'];
?>

<?php
switch($method)
{
	case 'insert':
            if($_SESSION['language']=='ID'){           
                if (empty($_POST['karyawanid']) || empty($_POST['periode'])){
                    echo "peringatan: Anda harus melengkapi kolom isian!";
                    exit();
                }

                $sql_cek_karyawanid="select * from ".$dbname.".kebun_5nilaipemanen where karyawanid like '".$_POST['karyawanid']."' and periodegaji='".$_POST['periode']."'";
                $hasil = mysql_query($sql_cek_karyawanid);
                $nilai_record= mysql_num_rows($hasil);
                if ($nilai_record > 0) {
                    echo "Karyawan ini sudah ada dalam daftar!";
                    exit();                
                    }

                $i="insert into ".$dbname.".kebun_5nilaipemanen (karyawanid,periodegaji,nilai,updateby)
                values ('".$_POST['karyawanid']."','".$_POST['periode']."','".$_POST['nilai']."','".$_SESSION['standard']['userid']."')";
                if(mysql_query($i))
                echo "Berhasil disimpan.";
                else
                echo "Gagal, ".addslashes(mysql_error($conn));
            }else{
                if (empty($_POST['karyawanid']) || empty($_POST['periode'])){
                    echo "warning: You must complete the form!";
                    exit();
                }

                $sql_cek_karyawanid="select * from ".$dbname.".kebun_5nilaipemanen where karyawanid like '".$_POST['karyawanid']."' and periodegaji='".$_POST['periode']."'";
                $hasil = mysql_query($sql_cek_karyawanid);
                $nilai_record= mysql_num_rows($hasil);
                if ($nilai_record > 0) {
                    echo "warning: This employee already exist in the list!";
                    exit();                
                    }

                $i="insert into ".$dbname.".kebun_5nilaipemanen (karyawanid,periodegaji,nilai,updateby)
                values ('".$_POST['karyawanid']."','".$_POST['periode']."','".$_POST['nilai']."','".$_SESSION['standard']['userid']."')";
                if(mysql_query($i))
                echo "Successfully saved.";
                else
                echo "Failed, ".addslashes(mysql_error($conn));                
            }
	break;
	
	case 'update':
            if($_SESSION['language']=='ID'){
                if (empty($_POST['karyawanid']) || empty($_POST['periode'])){
                    echo "peringatan: Anda harus melengkapi kolom isian!";
                    exit();
                }

                $i="update ".$dbname.".kebun_5nilaipemanen set nilai=".$_POST['nilai'].",updateby='".$_SESSION['standard']['userid'].
                "' where karyawanid='".$_POST['karyawanid']."' and periodegaji='".$_POST['periode']."'";
                if(mysql_query($i))
                echo "Berhasil disimpan.";
                else
                echo "Gagal, ".addslashes(mysql_error($conn));
            }else{
                if (empty($_POST['karyawanid']) || empty($_POST['periode'])){
                    echo "warning: You must complete the form!";
                    exit();
                }

                $i="update ".$dbname.".kebun_5nilaipemanen set nilai=".$_POST['nilai'].",updateby='".$_SESSION['standard']['userid'].
                "' where karyawanid='".$_POST['karyawanid']."' and periodegaji='".$_POST['periode']."'";
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
                                         <td align=center>".$_SESSION['lang']['periode']."</td>
                                         <td align=center>".$_SESSION['lang']['karyawanid']."</td>
                                         <td align=center>".$_SESSION['lang']['nik']."</td>
                                         <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
                                         <td align=center nowrap>".$_SESSION['lang']['nilai']."</td>
                                         <td align=center nowrap>".$_SESSION['lang']['updateby']."</td>
                                         <td align=center>Aksi</td>                                     
                                 </tr>
                        </thead>
                        <tbody>";

                    $tutup=false;
                    if ($_POST['persch']!=''){
                        $whr.=" and periodegaji='".$_POST['persch']."'";
                        #cek periode
                        $qPer = selectQuery($dbname,'sdm_5periodegaji','*',
                            "periode='".$_POST['persch']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'");
                        $Per=fetchData($qPer);
                        if ($Per[0]['sudahproses']==1) $tutup=true;
                    }
                    if ($_POST['nmkar']!=''){
                        $whr.=" and b.namakaryawan like '%".$_POST['nmkar']."%'";
                    }
                    $ql2="select a.*,b.nik,b.namakaryawan from ".$dbname.".kebun_5nilaipemanen a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'".$whr;
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
                            switch ($row['nilai']):
                                case 1: $nil='A';break;
                                case 2: $nil='B';break;
                                case 3: $nil='C';break;
                            endswitch;
                            $no+=1;
                            echo "<tr class=rowcontent>";
                            echo "<td align=center>".$no."</td>";
                            echo "<td align=left nowrap>".$row['periodegaji']."</td>";
                            echo "<td align=left>".$row['karyawanid']."</td>";
                            
//                            $kriteria1="karyawanid like '".$row['karyawanid']."'";
//                            $carinik = makeOption($dbname,'datakaryawan','karyawanid,nik',$kriteria1,'0',true);
//                            echo "<td>".$carinik[$row['karyawanid']]."</td>";
                            
//                            $kriteria2="karyawanid like '".$row['karyawanid']."'";
//                            $carinama = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$kriteria2,'0',true);
//                            echo "<td>".$carinama[$row['karyawanid']]."</td>";
                            echo "<td align=center>".$row['nik']."</td>";
                            echo "<td align=center>".$row['namakaryawan']."</td>";
                            echo "<td align=center>".$nil."</td>";
                            echo "<td align=left nowrap>".$optKarRow[$row['updateby']]."</td>";
                            if ($tutup){
                                echo "<td></td>";
                            } else {
                                echo "<td align=center nowrap>
                                <img src=images/application/application_edit.png class=resicon title='Edit' caption='Edit' onclick=\"fillField('".$row['karyawanid']."','".$row['periodegaji']."','".$row['nilai']."');\">
                                <img src=images/application/application_delete.png class=resicon title='Delete' caption='Delete' onclick=\"del('".$row['karyawanid']."','".$row['periodegaji']."');\"></td>";
                            }
                            echo "</tr>";
                    }
                    echo"</tbody></table>";
        break;

	case 'delete':
                if($_SESSION['language']=='ID'){
                    $i="delete from ".$dbname.".kebun_5nilaipemanen where karyawanid='".$_POST['karyawanid']."' and periodegaji='".$_POST['periode']."'";
                    if(mysql_query($i)){
                        echo "Berhasil dihapus.";
                    }else{
                        echo "Gagal, ".addslashes(mysql_error($conn));
                    }
                }else{
                    $i="delete from ".$dbname.".kebun_5nilaipemanen where karyawanid='".$_POST['karyawanid']."' and periodegaji='".$_POST['periode']."'";
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
