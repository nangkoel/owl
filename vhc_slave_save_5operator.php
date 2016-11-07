<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$kdKry=$_POST['kdKry'];
$stat=$_POST['status'];
$kodeOrg=$_POST['kodeOrg'];
$kdVhc=$_POST['kdVhc'];
$nikKar=makeOption($dbname,'datakaryawan','karyawanid,nik');
$lokasiKar=makeOption($dbname,'datakaryawan','karyawanid,lokasitugas');
$nik=$_POST['nik'];
$nmkrywn=$_POST['nmkrywn'];
$kdkndrn=$_POST['kdkndrn'];
$arrPos=array("0"=>"NonAktif","1"=>"Aktif");
$nmKrywn=$_POST['nmKrywn'];
switch($proses)
{
	case'insert_karyawan':
	if($kdKry=='')
	{
		echo"warning:Please Select Karyawan";
		exit();
	}
	$sqlCek="select * from ".$dbname.".vhc_5operator where karyawanid='".$kdKry."'";
	$queryCek=mysql_query($sqlCek) or die(mysql_error());
	$rowCek=mysql_fetch_row($queryCek);
	if($rowCek<1)
	{
		$skry="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$kdKry."'";
		$qkry=mysql_query($skry) or die(mysql_error());
		$rkry=mysql_fetch_assoc($qkry);
		$sqlIns="insert into ".$dbname.".vhc_5operator (`karyawanid`,`nama`,`aktif`,`vhc`) values ('".$kdKry."','".$rkry['namakaryawan']."','".$stat."','".$kdVhc."')";
		if(mysql_query($sqlIns))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
	}
	else
	{
		echo"warning:Already Insert";
                exit();
	}
	break;
	case'deleteKry':
	$sdel="delete from ".$dbname.".vhc_5operator where karyawanid='".$kdKry."'";
	if(mysql_query($sdel))
	echo"";
	else
	echo "DB Error : ".mysql_error($conn);
	break;
        
        
        
        
        
        
        
	case'load_new_data':
           // exit("Error:masuk");
	$limit=25;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	$optLtgs=makeOption($dbname, 'datakaryawan','karyawanid,lokasitugas');
        
	$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_5operator where karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')) order by nama asc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}

	$arrPos=array("NonAktif","Aktif");
	$str="select * from ".$dbname.".vhc_5operator where karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')) order by nama asc limit ".$offset.",".$limit."";
        //echo $str;
	if($res=mysql_query($str))
	{
	while($bar=mysql_fetch_object($res))
            {

            $no+=1;
            //echo $minute_selesai; exit();
            
                 echo"<tr class=rowcontent id='tr_".$no."'>
                <td>".$no."</td>
                <td>".$nikKar[$bar->karyawanid]."</td>
                <td>".$bar->nama."</td>
				<td>".$lokasiKar[$bar->karyawanid]."</td>
                <td>".$arrPos[$bar->aktif]."</td>
                <td>".$bar->vhc."</td>
                <td>
                <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->karyawanid."','".$bar->aktif."','".$bar->vhc."');\">		
                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delOpt('".$bar->karyawanid."');\">
                </td>
                </tr>";
            
          }
	echo" <tr><td colspan=5 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
				<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>";
        }	
	else
	{
	echo " Gagal,".(mysql_error($conn));
	}	
	break;
	case'update_karyawan':
	$sql="update ".$dbname.".vhc_5operator set aktif='".$stat."',vhc='".$kdVhc."' where karyawanid='".$kdKry."'";
	if(mysql_query($sql))
	echo"";
	else
	echo " Gagal,".(mysql_error($conn));
	break;
	case'getKrywan':
	$sDtkry="select namakaryawan,karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$kodeOrg."'";
	$qDtkry=mysql_query($sDtkry) or die(mysql_error());
	while($rDtkry=mysql_fetch_assoc($qDtkry))
	{
		$optKry.="<option value=".$rDtkry['karyawanid']." ".($rDtkry['karyawanid']==$kdKry?'selected':'').">".$rDtkry['namakaryawan']."</option>";
	}
	echo $optKry;
	break;
        
        
        
        
        
        case'loadDatavhcopr':
		
		//exit("Error:MASUKCOI");
		
	echo"
	<div id=container>
		<table class=sortable cellspacing=1 border=0>
	     <!--thead>
			 <tr class=rowheader>
				 <td align=center>".$_SESSION['lang']['nourut']."</td>
				 <td align=center>".$_SESSION['lang']['nik']."</td>
				 <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
				 <td align=center>".$_SESSION['lang']['lokasitugas']."</td>
				 <td align=center>".$_SESSION['lang']['status']."</td>
				 <td align=center>".$_SESSION['lang']['kodevhc']."</td>
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead-->
		<tbody>";
		if($nik!=''){
                    //$whr.=" and ".$dbname.".datakaryawan.nik like '%".$_POST['nik']."%'";
                    //$whr.=" b.nik like '%".$nik."%' ";
                    $whr.=" and nik like '%".$nik."%' ";
                }
                if($nmkrywn!=''){
                    //$whr.=" a.nama like '%".$nmkrywn."%' ";
                    $whr.=" and nama like '%".$nmkrywn."%' ";
                }
                if($kdkndrn!=''){
                    //$whr.=" a.vhc like '%".$kdkndrn."%' ";
                    $whr.=" and vhc like '%".$kdkndrn."%' ";
                }
                		
		$limit=15;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
				
		/*$ql2="select * from ".$dbname.".vhc_5operator";// echo $ql2;notran
                 */ 
                $ql2="select count(*) as jmlhrow from ".$dbname.".vhc_5operator left join ".$dbname.".datakaryawan on 
                      vhc_5operator.karyawanid=datakaryawan.karyawanid where vhc_5operator.karyawanid in 
                     (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas in 
                     (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')) 
                     ".$whr." order by nama asc "; 
		//exit("Error:$ql2");
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
                
		/*$i="select * from ".$dbname.".vhc_5operator 
                    where karyawanid like '%".$_POST['nik']."%' ".$whr." 
                    order by karyawanid asc  limit ".$offset.",".$limit."";
                */
                /*$i="select vhc_5operator.*,datakaryawan.* from ".$dbname.".vhc_5operator"
                        . ",".$dbname.".datakaryawan 
                    where vhc_5operator.karyawanid=datakaryawan.karyawanid 
                    ".$whr." order by datakaryawan.nik asc limit ".$offset.",".$limit."";*/
                
                /*$i="select * from ".$dbname.".vhc_5operator a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                     where  ".$whr." order by nik asc limit ".$offset.",".$limit."";*/
                $i="select * from ".$dbname.".vhc_5operator a left join ".$dbname.".datakaryawan b on
                    a.karyawanid=b.karyawanid where a.karyawanid in 
                    (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas in 
                    (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')) 
                    ".$whr." order by nama asc limit ".$offset.",".$limit."";
                
                //exit("Error:$i");
                /*$i="select vhc_5operator.*,datakaryawan.* from ".$dbname.".vhc_5operator"
                        . ",".$dbname.".datakaryawan 
                    where vhc_5operator.karyawanid=datakaryawan.karyawanid and ".$dbname.".datakaryawan.nik like '%".$_POST['nik']."%'  
                    order by datakaryawan.nik asc limit ".$offset.",".$limit."";  */             
                
		
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
	
                    $no+=1;
                    echo "<tr class=rowcontent>";
                    echo "<td align=center>".$no."</td>";
                    echo "<td align=left>".$d['nik']."</td>";
                    echo "<td align=right>".$d['nama']."</td>";
                    echo "<td align=right>".$d['lokasitugas']."</td>";
                    echo "<td align=right>".$arrPos[$d['aktif']]."</td>";
                    echo "<td align=right>".$d['vhc']."</td>";
                    echo "<td align=center>
                    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$d['karyawanid']."','".$d['aktif']."','".$d['vhc']."');\">
                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delOpt('".$d['karyawanid']."');\"></td>";
                    echo "</tr>";
		}
		echo"
		<tr class=rowheader><td colspan=18 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=loadDatavhcopr(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=loadDatavhcopr(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		echo"</tbody></table>";
    break;
    case'getKrywn':
               $tab="<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                        <div style=\"overflow:auto;height:295px;width:455px;\">
                        <table cellpading=1 border=0 class=sortbale>
                        <thead>
                        <tr class=rowheader>
                        <td>No.</td>
                        <td>".$_SESSION['lang']['nik']."</td>
                        <td>".$_SESSION['lang']['employeename']."</td>
                        <td>".$_SESSION['lang']['lokasitugas']."</td>
                        </tr><tbody>
                        ";

            if($nmKrywn=='')
            {
                $sLoad="select karyawanid,namakaryawan,lokasitugas,nik from ".$dbname.".datakaryawan where tipekaryawan!='0'
                        order by namakaryawan asc";
            }
            else
            {
                $sLoad="select karyawanid,namakaryawan,lokasitugas,nik from ".$dbname.".datakaryawan where tipekaryawan!='0'
                     and namakaryawan like '%".$nmKrywn."%' order by namakaryawan asc";
            }
            //$sLoad="select kodebarang,namabarang,satuan,inactive from ".$dbname.".log_5masterbarang where   (kodebarang like '%".$nmBrg."%' or namabarang like '%".$nmBrg."%') ".$add."";
            
               //echo $sLoad;
               //exit("Error:$sLoad");
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
           
                $tab.="<tr class=rowcontent onclick=\"setData('".$res['karyawanid']."','".$res['nik']."','".$res['namakaryawan']."','".$res['lokasitugas']."')\" title='".$res['namakaryawan']."'>";
            
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$res['nik']."</td>";
            $tab.="<td>".$res['namakaryawan']."</td>";
            $tab.="<td>".$res['lokasitugas']."</td>";
            $tab.="</tr>";
        }
        echo $tab;

        break;
        
	default:
	break;
}

?>