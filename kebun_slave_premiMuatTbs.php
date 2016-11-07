<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$per=$_POST['per'];
$proses=$_GET['proses'];
$kodeorg=$_POST['kodeorg'];
$proses2=$_POST['proses'];
$karyawanid=$_POST['karyawanid'];
$premiinput=$_POST['premiinput'];
$loadingtype=$_POST['loadingtype'];

$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$nikKar=makeOption($dbname,'datakaryawan','karyawanid,nik');
$nmKeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
                      where kodeorg='".$kodeorg."' and periode='".$per."'";
$rTgl=fetchData($sTgl);

//echo $per.___.$kodeorg;

$stream="<table cellspacing='1' border='0' class='sortable'>
			<thead class=rowheader>
				<tr class=rowheader>
					<td align=center>".$_SESSION['lang']['nourut']."</td>
					<td align=center>".$_SESSION['lang']['karyawanid']."</td>
					<td align=center>".$_SESSION['lang']['nik']."</td>
					<td align=center>".$_SESSION['lang']['namakaryawan']."</td>
					<td align=center>".$_SESSION['lang']['premi']."</td>
					<td align=center>".$_SESSION['lang']['hasilkerjajumlah']." Kg</td>
					<td align=center>".$_SESSION['lang']['absensi']."</td>
					
					<td align=center>".$_SESSION['lang']['premi']." (Rp)</td>
				</tr>
			</thead>
			<tbody>";/*<td align=center>".$_SESSION['lang']['kodekegiatan']."</td>
					<td align=center>".$_SESSION['lang']['namakegiatan']."</td>*/
                $arrDt=array("LOADINGDT"=>"LOADINGDT","LOADINGFORD"=>"LOADINGFORD");                       
		$i="select sum(hasilkerja) as hasilkerja,sum(jhk) as jhk,karyawanid,kodekegiatan from ".$dbname.".kebun_kehadiran_vw 
		where unit='".$kodeorg."' and  tanggal between '".$rTgl[0]['tanggalmulai']."' and '".$rTgl[0]['tanggalsampai']."' and kodekegiatan in ('611020416','611020420','611020418')
		group by karyawanid,kodekegiatan";//echo $i;//where jhk>'20'
		#perubahan kodekegiatan
		#611020221-->611020416
		#611020228-->611020420
		#611020224-->611020418
		$n=mysql_query($i) or (mysql_error($conn));
		while($d=mysql_fetch_assoc($n)){
                    if(($d['karyawanid']!='')||(!is_null($d['karyawanid'])!='')){
                        if($d['kodekegiatan']=='611020416'){
                            $dtJhkDt[$d['karyawanid']]['LOADINGDT']=$d['jhk'];
                            $dtHslKrj[$d['karyawanid']]['LOADINGDT']+=$d['hasilkerja'];
                        }else{
                            $dtJhkDt[$d['karyawanid']]['LOADINGFORD']+=$d['jhk'];
                            $dtHslKrj[$d['karyawanid']]['LOADINGFORD']+=$d['hasilkerja'];
                        }
                        $dtKary[$d['karyawanid']]=$d['karyawanid'];
                    }
                }
                $nor=0;
                $idr=0;
                $haha="select distinct * from ".$dbname.".kebun_5premimuat 
                       where kodekegiatan in ('611020416','611020420','611020418')  order by volume desc";
                $hihi=mysql_query($haha) or die(mysql_error($conn));
                while($huhu=mysql_fetch_assoc($hihi)){
                    if($huhu['kodekegiatan']=='611020416'){
                        $nor+=1;
                         $totRow['LOADINGDT']+=1;
                         $jumhr['LOADINGDT'.$nor]=$huhu['jumlahhari'];
                         $nildt['LOADINGDT'.$nor]=$huhu['volume'];
                         $volume['LOADINGDT'.$nor]=$huhu['rupiah'];
                    }elseif($huhu['kodekegiatan']=='611020420'){
                         $idr+=1;
                         $totRow['LOADINGFORD']+=1;
                         $jumhr['LOADINGFORD'.$idr]=$huhu['jumlahhari'];
                         $nildt['LOADINGFORD'.$idr]=$huhu['volume'];
                         $volume['LOADINGFORD'.$idr]=$huhu['rupiah'];
                    }
                }
        
			foreach($dtKary as $lstKaryawn){
                            foreach($arrDt as $lsLoad){
                                if(($dtHslKrj[$lstKaryawn][$lsLoad]!='')&&($dtHslKrj[$lstKaryawn][$lsLoad]!=0)){
                                $no+=1;
                                
                                $stream.= "<tr class=rowcontent id=row".$no.">";
                                $stream.= "<td align=center>".$no."</td>";
                                $stream.= "<td align=left id=karyawanid".$no.">".$lstKaryawn."</td>";
                                $stream.= "<td align=left>".$nikKar[$lstKaryawn]."</td>";
                                $stream.= "<td align=left>".$nmKar[$lstKaryawn]."</td>";
                                $stream.= "<td align=left><input type=hidden id=loadingtype".$no." value='".$lsLoad."' />".$lsLoad."</td>";
                                $stream.= "<td align=right>".intval($dtHslKrj[$lstKaryawn][$lsLoad])."</td>";
                                $stream.= "<td align=right>".ceil($dtJhkDt[$lstKaryawn][$lsLoad])."</td>";
                                $angk=1;
                                $angk2=2;
                                $angk3=3;
                                $premi[$lstKaryawn][$lsLoad]=0;
                                if((intval($dtHslKrj[$lstKaryawn][$lsLoad])>=$nildt[$lsLoad.$angk])){
                                    if((ceil($dtJhkDt[$lstKaryawn][$lsLoad])>=$jumhr[$lsLoad.$angk])){
                                        $premi[$lstKaryawn][$lsLoad]=$volume[$lsLoad.$angk];//exit("error:".$nildt[$lsLoad.$as]."___".$no);
                                    }
                                }elseif((intval($dtHslKrj[$lstKaryawn][$lsLoad]<$nildt[$lsLoad.$angk]))&&(intval($dtHslKrj[$lstKaryawn][$lsLoad]>=$nildt[$lsLoad.$angk2]))){
                                    if((ceil($dtJhkDt[$lstKaryawn][$lsLoad])>=$jumhr[$lsLoad.$angk2])){
                                        $premi[$lstKaryawn][$lsLoad]=$volume[$lsLoad.$angk2];
                                    }
                                }elseif((intval($dtHslKrj[$lstKaryawn][$lsLoad]<$nildt[$lsLoad.$angk2]))&&(intval($dtHslKrj[$lstKaryawn][$lsLoad]>=$nildt[$lsLoad.$angk3]))){
                                    if((ceil($dtJhkDt[$lstKaryawn][$lsLoad])>=$jumhr[$lsLoad.$angk3])){
                                        $premi[$lstKaryawn][$lsLoad]=$volume[$lsLoad.$angk3];
                                    }
                                }
                                $stream.= "<td align=right><input type=hidden id=premiinput".$no." value='".$premi[$lstKaryawn][$lsLoad]."' />".$premi[$lstKaryawn][$lsLoad]."</td>";
                                $stream.="</tr>";
                                }
                            }
                        }
  
		$stream.="</table>";


 
##periksa data kosong atau tidak
$n=mysql_query($i) or (mysql_error($conn));
  
#periksa apakah sudah tutup buku dan gaji close
$xi="select distinct * from ".$dbname.".sdm_5periodegaji where periode='".$per."' 
              and kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses='1'";
$xu=mysql_query($xi) or die(mysql_error($conn));
if(mysql_num_rows($xu)>0)
    $aktif2=false;
       else
     $aktif2=true;

       $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$per."' and 
             kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
       $res=mysql_query($str);
       if(mysql_num_rows($res)>0)
           $aktif=false;
       else
           $aktif=true;

  
  if($per=='')
  {
	  exit("Error:Periode masih kosong");
  }

if(!$aktif2 || !$aktif)
{
	$stream.="<b>You can't proses this periode because acoounting or payroll periode for ".$_SESSION['empl']['lokasitugas']." has been closed</b>";	
  //exit("Error:Periode gaji/akuntansi untuk ".$_SESSION['empl']['lokasitugas']." sudah ditutup");
}
else if (mysql_num_rows($n)<1)
{
	$stream="Data was empty";
} 
else
{
	$stream.="<button class=mybutton onclick=saveAll(".$no.");>".$_SESSION['lang']['proses']."</button>";	
}

	

		
switch($proses)
{
	case'preview':
		echo $stream;
	break;
	default;
}
switch($proses2)
{	
	case'savedata':
//	exit("Error:MASUK");

	if($premiinput=='0' or $premiinput=='')
	{
	}
	else
	{
		$str="insert into ".$dbname.".kebun_premikemandoran (periode,kodeorg,karyawanid,jabatan,pembagi,premiinput,updateby,posting)
		values ('".$per."','".$kodeorg."','".$karyawanid."','".$loadingtype."','1','".$premiinput."','".$_SESSION['standard']['userid']."','1')";
                //exit("Error:$str");
		if(mysql_query($str))
		{//case berhasil kosongin aja
		}
		else
		{
			$str="update ".$dbname.".kebun_premikemandoran set premiinput='".$premiinput."',posting=1
			 where periode='".$per."' and kodeorg='".$kodeorg."' and karyawanid='".$karyawanid."'
                         and jabatan='".$loadingtype."'";

			if(mysql_query($str))
			{//case berhasil kosongin aja
			}
			else
			{
				echo " Gagal,".addslashes(mysql_error($conn));
			}
		
		}
	}
	break;
	default:
}





?>