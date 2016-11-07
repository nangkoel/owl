<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//$arr="##kdSarana##tglKonSarana##kondId##idProgress##method";
$method=$_POST['method'];
$kdSarana=$_POST['kdSarana'];
$tglKonSarana=tanggalsystem($_POST['tglKonSarana']);
$kondId=$_POST['kondId'];
$idProgress=$_POST['idProgress'];
$jmlhSarana=$_POST['jmlhSarana'];
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

$optKlmpk2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sKlmpk2="select distinct jenis,nama,satuan from ".$dbname.".sdm_5jenis_prasarana order by nama asc";
$qKlmpk2=mysql_query($sKlmpk2) or die(mysql_error());
while($rKlmpk2=mysql_fetch_assoc($qKlmpk2))
{
    $orgNmKlmpk2[$rKlmpk2['jenis']]=$rKlmpk2['nama'];
    $arrSat[$rKlmpk2['jenis']]=$rKlmpk2['satuan'];
}
	switch($method)
	{
		case'insert':
		if(($kdSarana=='')||($tglKonSarana=='')||($kondId=='')||($idProgress==''))
		{
			echo"warning:Semua Field tidak boleh kosong";
			exit();
		}
               
                if($jmlhSarana==''||$jmlhSarana=='0')
                {
                    exit("Error:Jumlah tidak boleh kosong");
                }
                $sCek2="select distinct jumlah,jenisprasarana from ".$dbname.".sdm_prasarana where kodeprasarana='".$kdSarana."'";
		$qCek2=mysql_query($sCek2) or die(mysql_error($conn));
		$rCek2=mysql_fetch_assoc($qCek2);
                if($rCek2['jumlah']<$jmlhSarana)
                {
                    exit("Error:Jumlah tidak boleh lebih dari ".$arrSat[$rCek2['jenisprasarana']]." yang tersedia");
                }
		$sCek="select * from ".$dbname.".sdm_kondisi_prasarana where kodeprasarana='".$kdSarana."' and tanggal='".$tglKonSarana."'";
		$qCek=mysql_query($sCek) or die(mysql_error($conn));
		$rCek=mysql_num_rows($qCek);
		if($rCek>0)
		{
			echo"warning:Data Sudah ada";
			exit();
		}
		else
		{
                    $sIns="insert into ".$dbname.".sdm_kondisi_prasarana (kodeprasarana, jumlah, kondisi, tanggal, progress, karyawanid) 
                           values ('".$kdSarana."','".$jmlhSarana."','".$kondId."','".$tglKonSarana."','".$idProgress."','".$_SESSION['standard']['userid']."')";
                    if(!mysql_query($sIns))
                    {
                            echo"Gagal".mysql_error($conn);
                    }
		}
		break;
		case'loadData':
		$no=0;	 
		$arrProgrs=array("1"=>$_SESSION['lang']['slsiPerbaikan'],"2"=>$_SESSION['lang']['dlmPerbaikan']);
		$str="select a.* from ".$dbname.".sdm_kondisi_prasarana a  left join ".$dbname.".sdm_prasarana b on a.kodeprasarana=b.kodeprasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."'
                           order by tahunperolehan,bulanperolehan desc";
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

                    $sql2="select count(*) as jmlhrow from ".$dbname.".sdm_kondisi_prasarana a  left join ".$dbname.".sdm_prasarana b on a.kodeprasarana=b.kodeprasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."'
                           order by tahunperolehan,bulanperolehan desc";
                    //exit("Error".$sql2);
                    $query2=mysql_query($sql2) or die(mysql_error());
                    while($jsl=mysql_fetch_object($query2)){
                    $jlhbrs= $jsl->jmlhrow;
                    }
                    $str="select a.*,b.jenisprasarana,b.lokasi  from ".$dbname.".sdm_kondisi_prasarana a  left join ".$dbname.".sdm_prasarana b on a.kodeprasarana=b.kodeprasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."'
                           order by tahunperolehan,bulanperolehan desc limit ".$offset.",".$limit." ";
                   $res=mysql_query($str) or die(mysql_error());
                    while($bar=mysql_fetch_assoc($res))
                    {
                    $no+=1;	
                    echo"<tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".$bar['kodeprasarana']."</td>
                    <td>".$orgNmKlmpk2[$bar['jenisprasarana']]."</td>
                    <td>".$bar['lokasi']."</td>
                    <td>".tanggalnormal($bar['tanggal'])."</td>
                    <td>".$bar['kondisi']."</td>
                    <td>".$arrProgrs[$bar['progress']]."</td>
                    <td align=right>".number_format($bar['jumlah'],0)."</td>
                    <td>".$arrSat[$bar['jenisprasarana']]."</td>
                    <td>
                      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['kodeprasarana']."','".tanggalnormal($bar['tanggal'])."');\"> 
                      <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['kodeprasarana']."','".tanggalnormal($bar['tanggal'])."');\">
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
		if(($kdSarana=='')||($tglKonSarana=='')||($kondId=='')||($idProgress==''))
		{
			echo"warning:Semua Field tidak boleh kosong";
			exit();
		}
               
                if($jmlhSarana==''||$jmlhSarana=='0')
                {
                    exit("Error:Jumlah tidak boleh kosong");
                }
                $sCek2="select distinct jumlah,jenisprasarana from ".$dbname.".sdm_prasarana where kodeprasarana='".$kdSarana."'";
		$qCek2=mysql_query($sCek2) or die(mysql_error($conn));
		$rCek2=mysql_fetch_assoc($qCek2);
                if($rCek2['jumlah']<$jmlhSarana)
                {
                    exit("Error:Jumlah tidak boleh lebih dari ".$arrSat[$rCek2['jenisprasarana']]." yang tersedia");
                }
//                $sIns="insert into ".$dbname.".sdm_kondisi_prasarana (kodeprasarana, jumlah, kondisi, tanggal, progress, karyawanid) 
//                           values ('".$kdSarana."','".$jmlhSarana."','".$kondId."','".$tglKonSarana."','".$idProgress."','".$_SESSION['standard']['userid']."')";
                $sUpd="update ".$dbname.".sdm_kondisi_prasarana set `jumlah`='".$jmlhSarana."',`kondisi`='".$kondId."',`progress`='".$idProgress."',`karyawanid`='".$_SESSION['standard']['userid']."'
                       where kodeprasarana='".$kdSarana."' and tanggal='".$tglKonSarana."'";
                if(!mysql_query($sUpd))
                {
                        echo"Gagal".mysql_error($conn);
                }
		
		break;
		case'delData':
		$sDel="delete from ".$dbname.".sdm_kondisi_prasarana where  kodeprasarana='".$kdSarana."' and tanggal='".$tglKonSarana."'";
		if(!mysql_query($sDel))
		{
			echo"Gagal".mysql_error($conn);
		}
		break;
		case'getData':
		$sDt="select * from ".$dbname.".sdm_kondisi_prasarana where kodeprasarana='".$kdSarana."' and tanggal='".$tglKonSarana."'";
		$qDt=mysql_query($sDt) or die(mysql_error($conn));
		$rDet=mysql_fetch_assoc($qDt);
		echo $rDet['jumlah']."###".$rDet['kondisi']."###".$rDet['progress'];
		break;
                case'getSatuan':
                $sSatuan2="select distinct jenisprasarana from ".$dbname.".sdm_prasarana where kodeprasarana='".$kdSarana."'";
                //    exit("error".$sSatuan2);
                $qSatuan2=mysql_query($sSatuan2) or die(mysql_error());
                $rSatuan2=mysql_fetch_assoc($qSatuan2);
                
                $sSatuan="select distinct satuan from ".$dbname.".sdm_5jenis_prasarana where jenis='".$rSatuan2['jenisprasarana']."'";
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