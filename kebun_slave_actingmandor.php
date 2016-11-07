<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//$arr="##nikMandor##nikMandorAct##periode##method";
$method=$_POST['method'];
$nikMandor=$_POST['nikMandor'];
$nikMandorAct=$_POST['nikMandorAct'];
$periode=$_POST['periode'];
	switch($method)
	{
		case'insert':
		if(($nikMandor=='')||($nikMandorAct==''))
		{
			echo"warning: ".$_SESSION['lang']['nikmandor']."/".$_SESSION['lang']['nikmandoracting']." tidak boleh kosong";
			exit();
		}
		$sCek="select * from ".$dbname.".kebun_actingmandor where "
                        . " kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$periode."' and karyawanid='".$nikMandor."'";
		$qCek=mysql_query($sCek) or die(mysql_error($conn));
		$rCek=mysql_num_rows($qCek);
		if($rCek>0)
		{
			echo"warning:Data sudah ada";
			exit();
		}
		else{	
                    $res=  mysql_fetch_assoc($qCek);
                    if($nikMandor==$nikMandorAct){
                        echo"warning:Data yang dipilih sama";
			exit();
                    }
                    if($res['karyid_acting']==$nikMandorAct){
                         echo"warning:Data sudah terdaftar di mandor yang lain";
			exit();
                    }
                    $sIns="insert into ".$dbname.".kebun_actingmandor (`kodeorg`,`karyawanid`,`karyid_acting`,`periodegaji`,`updateby`,`afdeling`) values ('".$_SESSION['empl']['lokasitugas']."','".$nikMandor."','".$nikMandorAct."','".$periode."','".$_SESSION['standard']['userid']."','".$_POST['afdId']."')";
                    if(!mysql_query($sIns)){
                            echo"Gagal".mysql_error($conn);
                    }
		}
		break;
		case'loadData':
                $wh="lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
                $optNmKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan',$wh);
                $optNikKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,nik',$wh);
                $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
                $tab.="<tr>";
                $tab.="<td>No.</td>";
                $tab.="<td>".$_SESSION['lang']['periodegaji']."</td>";
                $tab.="<td>".$_SESSION['lang']['nikmandor']."</td>";
                $tab.="<td>".$_SESSION['lang']['nikmandoracting']."</td>";
                $tab.="<td>".$_SESSION['lang']['updateby']."</td>";
                $tab.="<td>".$_SESSION['lang']['action']."</td></tr><tbody>";
                    
		$no=0;	 
		$str="select * from ".$dbname.".kebun_actingmandor where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periodegaji desc";
                //echo $str;
		$res=mysql_query($str);
		while($bar=mysql_fetch_assoc($res)){
                    $no+=1;	
                    $tab.="<tr class=rowcontent><td>".$no."</td>
                    <td>".$bar['periodegaji']."</td>
                    <td>".$optNikKar[$bar['karyawanid']]."-".$optNmKar[$bar['karyawanid']]."</td>
                    <td>".$optNikKar[$bar['karyid_acting']]."-".$optNmKar[$bar['karyid_acting']]."</td>
                    <td>".$optNmKar[$bar['updateby']]."</td>";
                    $sGaji="select distinct sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$bar['kodeorg']."' and periode='".$bar['periodegaji']."' order by periode desc";
                    $qGaji=mysql_query($sGaji) or die(mysql_error($conn));
                    $rGaji=mysql_fetch_assoc($qGaji);
                    if($rGaji['sudahproses']==0){
                    $tab.="<td>
                              <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['periodegaji']."','".$bar['karyawanid']."','".$bar['karyid_acting']."','".$bar['afdeling']."');\"> 
                              <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['periodegaji']."','".$bar['karyawanid']."');\">
                      </td>";
                    }else{
                         $tab.="<td></td>";
                    }
                     $tab.="</tr>";	
		}     
                $tab.="</tbody></table>";
                echo $tab;
		break;
		case'update':
		if(($nikMandor=='')||($nikMandorAct==''))
		{
			echo"warning: ".$_SESSION['lang']['nikmandor']."/".$_SESSION['lang']['nikmandoracting']." tidak boleh kosong";
			exit();
		}
		$sCek="select * from ".$dbname.".kebun_actingmandor where "
                        . " kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$periode."' and karyawanid='".$nikMandor."'";
		$qCek=mysql_query($sCek) or die(mysql_error($conn));
		$rCek=mysql_num_rows($qCek);
		if($rCek>0)
		{
			echo"warning:Data sudah ada";
			exit();
		}
		else{	
                    $res=  mysql_fetch_assoc($qCek);
                    if($nikMandor==$nikMandorAct){
                        echo"warning:Data yang dipilih sama";
			exit();
                    }
                    if($res['karyid_acting']==$nikMandorAct){
                         echo"warning:Data sudah terdaftar di mandor yang lain";
			exit();
                    }
                    $sDel="delete from ".$dbname.".kebun_actingmandor where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$periode."' and karyawanid='".$nikMandor."'";
                    if(!mysql_query($sDel))
                    {
                            echo"Gagal".mysql_error($conn);
                    }else{
                    $sIns="insert into ".$dbname.".kebun_actingmandor (`kodeorg`,`karyawanid`,`karyid_acting`,`periodegaji`,`updateby`,`afdeling`) values ('".$_SESSION['empl']['lokasitugas']."','".$nikMandor."','".$nikMandorAct."','".$periode."','".$_SESSION['standard']['userid']."','".$_POST['afdId']."')";
                    if(!mysql_query($sIns)){
                            echo"Gagal".mysql_error($conn);
                    }
                    }
		}
		break;
		case'delData':
                $sGaji="select distinct sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."' order by periode desc";
                $qGaji=mysql_query($sGaji) or die(mysql_error($conn));
                $rGaji=mysql_fetch_assoc($qGaji);
                if($rGaji['sudahproses']==0){
                    $sDel="delete from ".$dbname.".kebun_actingmandor where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$periode."' and karyawanid='".$nikMandor."'";
                    if(!mysql_query($sDel))
                    {
                            echo"Gagal".mysql_error($conn);
                    }
                }else{
                    exit("warning: Periode gaji sudah tutup");
                }
		break;
                
		default:
		break;
	}
?>
