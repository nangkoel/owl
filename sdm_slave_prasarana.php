<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//$arr="##kdOrg##idKlmpk##idJenis##idLokasi##jmlhSarana##method##thnPerolehan##blnPerolehan##statFr";
$method=$_POST['method'];
$kdOrg=$_POST['kdOrg'];
$idKlmpk=$_POST['idKlmpk'];
$idJenis=$_POST['idJenis'];
$idLokasi=$_POST['idLokasi'];
$jmlhSarana=$_POST['jmlhSarana'];
$thnPerolehan=$_POST['thnPerolehan'];
$blnPerolehan=$_POST['blnPerolehan'];
$statFr=$_POST['statFr'];
$idData=$_POST['idData'];
$idData=$_POST['idData'];
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$sKlmpk="select distinct * from ".$dbname.".sdm_5kl_prasarana order by kode asc";
$qKlmpk=mysql_query($sKlmpk) or die(mysql_error());
while($rKlmpk=mysql_fetch_assoc($qKlmpk))
{
    $orgNmKlmpk[$rKlmpk['kode']]=$rKlmpk['nama'];
}
$optKlmpk2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sKlmpk2="select distinct jenis,nama from ".$dbname.".sdm_5jenis_prasarana order by nama asc";
$qKlmpk2=mysql_query($sKlmpk2) or die(mysql_error());
while($rKlmpk2=mysql_fetch_assoc($qKlmpk2))
{
    $orgNmKlmpk2[$rKlmpk2['jenis']]=$rKlmpk2['nama'];
}
	switch($method)
	{
		case'insert':
		if(($thnPerolehan=='')||($blnPerolehan=='')||($idLokasi=='')||($idJenis=='')||($idKlmpk=='')||($kdOrg==''))
		{
			echo"warning:Semua Field tidak boleh kosong";
			exit();
		}
                if($blnPerolehan>12)
                {
                    exit("Error:Bulan di luar standard");
                }
                if($jmlhSarana==''||$jmlhSarana=='0')
                {
                    exit("Error:Jumlah tidak boleh kosong");
                }
		$sCek="select * from ".$dbname.".sdm_prasarana where tahunperolehan='".$thnPerolehan."' and bulanperolehan='".$blnPerolehan."' and 
                       lokasi='".$idLokasi."' and kelompokprasarana='".$idKlmpk."' and jenisprasarana='".$idJenis."'";
		$qCek=mysql_query($sCek) or die(mysql_error($conn));
		$rCek=mysql_num_rows($qCek);
		if($rCek>0)
		{
			echo"warning:Data Sudah ada";
			exit();
		}
		else
		{
                    $sIns="insert into ".$dbname.".sdm_prasarana (kodeorg,  tahunperolehan, bulanperolehan, jumlah, kelompokprasarana, status, lokasi, jenisprasarana) 
                           values ('".$kdOrg."','".$thnPerolehan."','".$blnPerolehan."','".$jmlhSarana."','".$idKlmpk."','".$statFr."','".$idLokasi."','".$idJenis."')";
                    if(!mysql_query($sIns))
                    {
                            echo"Gagal".mysql_error($conn);
                    }
		}
		break;
		case'loadData':
		$no=0;	 
		$arr=array("0"=>"Tidak Aktif","1"=>"Aktif");
		$str="select * from ".$dbname.".sdm_prasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tahunperolehan,bulanperolehan desc";
                //echo $str;
		$res=mysql_query($str) or die(mysql_error());
                $row=mysql_num_rows($res);
                if($row>0)
                {
                    $limit=20;
                    $page=0;
                    if(isset($_POST['page']))
                    {
                    $page=$_POST['page'];
                    if($page<0)
                    $page=0;
                    }
                    $offset=$page*$limit;

                    $sql2="select count(*) as jmlhrow from ".$dbname.".sdm_prasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tahunperolehan,bulanperolehan desc";
                    //exit("Error".$sql2);
                    $query2=mysql_query($sql2) or die(mysql_error());
                    while($jsl=mysql_fetch_object($query2)){
                    $jlhbrs= $jsl->jmlhrow;
                    }
                    $str="select * from ".$dbname.".sdm_prasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tahunperolehan,bulanperolehan desc limit ".$offset.",".$limit." ";
                   $res=mysql_query($str) or die(mysql_error());
                    while($bar=mysql_fetch_assoc($res))
                    {
                    $no+=1;	
                    echo"<tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".$optNmOrg[$bar['kodeorg']]."</td>
                    <td>".$orgNmKlmpk[$bar['kelompokprasarana']]."</td>
                    <td>".$orgNmKlmpk2[$bar['jenisprasarana']]."</td>
                    <td>".$optNmOrg[$bar['lokasi']]."</td>
                    <td align=right>".number_format($bar['jumlah'],0)."</td>
                    <td align=right>".$bar['tahunperolehan']."</td>
                    <td align=right>".$bar['bulanperolehan']."</td>
                    <td>".$arr[$bar['status']]."</td>
                    <td>
                      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['kodeprasarana']."');\"> 
                      <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['kodeprasarana']."');\">
                      </td>
                    </tr>";	
                    }    
                        echo" <tr><td colspan=10 align=center>
                        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                        <button class=mybutton onclick=cariBast2(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                        <button class=mybutton onclick=cariBast2(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                        </td>
                        </tr>";   
                }
                else
                {
                    echo "<tr class=rowcontent><td colspan=10>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
		break;
		case'update':
		if(($thnPerolehan=='')||($blnPerolehan=='')||($idLokasi=='')||($idJenis=='')||($idKlmpk=='')||($kdOrg==''))
		{
			echo"warning:Semua Field tidak boleh kosong";
			exit();
		}
                if($blnPerolehan>12)
                {
                    exit("Error:Bulan di luar standard");
                }
                if($jmlhSarana==''||$jmlhSarana=='0')
                {
                    exit("Error:Jumlah tidak boleh kosong");
                }
//                 $sIns="insert into ".$dbname.".sdm_prasarana (kodeorg,  tahunperolehan, bulanperolehan, jumlah, kelompokprasarana, status, lokasi, jenisprasarana) 
//                           values ('".$kdOrg."','".$thnPerolehan."','".$blnPerolehan."','".$jmlhSarana."','".$idKlmpk."','".$statFr."','".$idLokasi."','".$idJenis."')";
			$sUpd="update ".$dbname.".sdm_prasarana set `tahunperolehan`='".$thnPerolehan."',`bulanperolehan`='".$blnPerolehan."',`jumlah`='".$jmlhSarana."',`kelompokprasarana`='".$idKlmpk."',
                               status='".$statFr."',lokasi='".$idLokasi."',jenisprasarana='".$idJenis."'
                               where kodeprasarana='".$idData."'";
			if(!mysql_query($sUpd))
			{
				echo"Gagal".mysql_error($conn);
			}
		
		break;
		case'delData':
		$sDel="delete from ".$dbname.".sdm_prasarana where kodeprasarana='".$idData."'";
		if(!mysql_query($sDel))
		{
			echo"Gagal".mysql_error($conn);
		}
		break;
		case'getData':
		$sDt="select * from ".$dbname.".sdm_prasarana where kodeprasarana='".$idData."'";
		$qDt=mysql_query($sDt) or die(mysql_error($conn));
		$rDet=mysql_fetch_assoc($qDt);
		echo $rDet['tahunperolehan']."###".$rDet['bulanperolehan']."###".$rDet['jumlah']."###".$rDet['kelompokprasarana']."###".$rDet['status']."###".$rDet['lokasi']."###".$rDet['jenisprasarana']."###".$rDet['kodeprasarana'];
		break;
                case'getSatuan':
                $sSatuan="select distinct satuan from ".$dbname.".sdm_5jenis_prasarana where jenis='".$idJenis."'";
                $qSatuan=mysql_query($sSatuan) or die(mysql_error());
                $rSatuan=mysql_fetch_assoc($qSatuan);
                
                echo $rSatuan['satuan'];
                break;
                case'getJenis':
                $optKlmpk2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $sKlmpk2="select distinct jenis,nama from ".$dbname.".sdm_5jenis_prasarana where kelompok='".$idKlmpk."' order by nama asc";
                $qKlmpk2=mysql_query($sKlmpk2) or die(mysql_error());
                while($rKlmpk2=mysql_fetch_assoc($qKlmpk2))
                {
                    if($idJenis!='')
                    {
                       $optKlmpk2.="<option value='".$rKlmpk2['jenis']."'  ".($rKlmpk2['jenis']==$idJenis?"selected":"").">".$rKlmpk2['nama']."</option>";
                    }
                    else
                    {
                        $optKlmpk2.="<option value='".$rKlmpk2['jenis']."'>".$rKlmpk2['nama']."</option>";
                    }
                }
                echo $optKlmpk2;
                break;
		default:
		break;
	}
?>