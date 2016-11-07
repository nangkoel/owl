<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php
$kodeorg=$_POST['kodeorg'];
$kodetangki=$_POST['kodetangki'];
$suhu=$_POST['suhu'];
$kepadatan=$_POST['kepadatan'];
$ketetapan=$_POST['ketetapan'];
$method=$_POST['method'];
?>

<?php
switch($method)
{
	case 'insert':
            if (empty($kodeorg) || empty($kodetangki)){
                echo"warning:Please Complete The Form";
                exit();
            }
            $i="insert into ".$dbname.".pabrik_5ketetapansuhu (kodeorg,kodetangki,suhu,kepadatan,ketetapan,updateby)
            values ('".$kodeorg."','".$kodetangki."','".$suhu."','".$kepadatan."','".$ketetapan."','".$_SESSION['standard']['userid']."')";
            if(mysql_query($i))
            echo"";
            else
            echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	case 'update':
            if (empty($kodeorg) || empty($kodetangki)){
                echo"warning:Please Complete The Form";
                exit();
            }
		
            $i="update ".$dbname.".pabrik_5ketetapansuhu set kepadatan=".$kepadatan.",ketetapan='".$ketetapan."',updateby='".$_SESSION['standard']['userid'].
            "' where kodeorg='".$kodeorg."' AND kodetangki='".$kodetangki."' AND suhu=".$suhu;
            if(mysql_query($i))
            echo"";
            else
            echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
		
        case'loadData':
            echo"
            <div style='height:220px;overflow:auto'>
                    <table class=sortable cellspacing=1 border=0>
                 <thead>
                             <tr class=rowheader>
                                     <td align=center>".$_SESSION['lang']['nourut']."</td>
                                     <td align=center>".$_SESSION['lang']['kodetangki']."</td>
                                     <td align=center>".$_SESSION['lang']['suhu']."</td>
                                     <td align=center>".$_SESSION['lang']['kepadatan']."</td>
                                     <td align=center>".$_SESSION['lang']['ketetapan']."</td>
                                     <td align=center>".$_SESSION['lang']['updateby']."</td>
                                     <td align=center>".$_SESSION['lang']['action']."</td>
                             </tr>
                    </thead>
                    <tbody>";

                    $ql2="select * from ".$dbname.".pabrik_5ketetapansuhu WHERE kodeorg LIKE '".$_SESSION['empl']['lokasitugas']."'";
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
                            echo "<td align=left>".$row['kodetangki']."</td>";
                            echo "<td align=right>".$row['suhu']."</td>";
                            echo "<td align=right>".$row['kepadatan']."</td>";
                            echo "<td align=right>".$row['ketetapan']."</td>";
                            echo "<td align=left>".$optKarRow[$row['updateby']]."</td>";
                            echo "<td align=center>
                            <img src=images/application/application_edit.png class=resicon title='Edit' caption='Edit' onclick=\"fillField('".$row['kodeorg']."','".$row['kodetangki']."','".$row['suhu']."','".$row['kepadatan']."','".$row['ketetapan']."');\">
                            <img src=images/application/application_delete.png class=resicon title='Delete' caption='Delete' onclick=\"del('".$row['kodeorg']."','".$row['kodetangki']."','".$row['suhu']."');\"></td>";
                            echo "</tr>";
                    }

                    echo"</tbody></table>";
        break;

	case 'delete':
	//exit("Error:hahaha");
		$i="delete from ".$dbname.".pabrik_5ketetapansuhu where kodeorg='".$kodeorg."' AND kodetangki='".$kodetangki."' AND suhu='".$suhu."'";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
        
	case'getTangki':
            $sGet="select kodetangki,keterangan from ".$dbname.".pabrik_5tangki where kodeorg='".$kodeorg."'";
            $qGet=mysql_query($sGet) or die(mysql_error());
            while($rGet=mysql_fetch_assoc($qGet)){
                $optTangki.="<option value=".$rGet['kodetangki'].">".$rGet['keterangan']."</option>";
            }
            echo $optTangki;
	break;

default:
}
?>
