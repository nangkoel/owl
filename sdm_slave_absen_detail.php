<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('config/connection.php');

$proses=$_POST['proses'];
$absnId=$_POST['absnId'];
$kdOrg=$_POST['kdOrg'];
$tgAbsn=tanggalsystem($_POST['tgAbsn']);

	switch($proses)
	{
		case'createTable':
		//$thisDate=date("Y-m-d");
		$table .= "<table id='ppDetailTable'>";
		//echo"warning:".$table;
		# Header
		$table .= "<thead>";
		$table .= "<tr class=rowheader>";
		$table .= "<td>".$_SESSION['lang']['namakaryawan']."</td>";
		$table .= "<td>".$_SESSION['lang']['shift']."</td>";
		//$table .= "<td>".$_SESSION['lang']['status']." ".$_SESSION['lang']['premi']."</td>"; 
		$table .= "<td>".$_SESSION['lang']['absensi']."</td>";
		$table .= "<td>".$_SESSION['lang']['jamMsk']."</td>";
		$table .= "<td>".$_SESSION['lang']['jamPlg']."</td>";
		//$table .= "<td>".$_SESSION['lang']['pembagiancatu']."</td>"; 
		
		$table .= "<td title='kehadiran kurang dari 7 jam/Presence under 7 hours'>".$_SESSION['lang']['penaltykehadiran']."</td>"; 
		$table .= "<td>".$_SESSION['lang']['premi']."</td>";
		$table .= "<td>".$_SESSION['lang']['keterangan']."</td>";
		$table .= "<td>Action</td>";
		$table .= "</tr>";
		$table .= "</thead>";

		# Data
		$table .= "<tbody id='detailBody'>";
		$idAbn=explode("###",$absnId);
		$tgl=tanggalsystem($idAbn[1]);
                $where= " tipekaryawan!=0";
		if(strlen($idAbn[0])>4)
		{
			$where.=" and subbagian='".$idAbn[0]."'  and (tanggalkeluar>".$tgl." or tanggalkeluar='0000-00-00')";
                        $ha="select karyawanid,nik,namakaryawan,subbagian from ".$dbname.".datakaryawan
                             where ".$where." and karyawanid is not null ";
		}
		else
		{
			$where.=" and lokasitugas='".$idAbn[0]."' and (subbagian IS NULL or subbagian='0' or subbagian='') and (tanggalkeluar>".$tgl." or tanggalkeluar='0000-00-00')";
                        $ha="select karyawanid,nik,namakaryawan,subbagian from ".$dbname.".datakaryawan
                             where ".$where." and karyawanid is not null ";
                        $ha.="UNION 
                             select a.karyawanid as karyawanid,nik,namakaryawan,b.lokasitugas from ".$dbname.".setup_temp_lokasitugas a 
                             left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                             where kodeorg='".$idAbn[0]."'   and a.karyawanid is not null";
		}
                //echo $ha;
		$hi=mysql_query($ha) or die (mysql_error($conn));
		while($rKry=mysql_fetch_assoc($hi)){
                    if(strlen($idAbn[0])==4){
                        if(strlen($rKry['karyawanid'])<10){
                            $rKry['karyawanid']=  addZero($rKry['karyawanid'], 10);
                        }
					   #jika karyawan cuti tidak muncul untuk absensinya
					   $sCek="select daritanggal,sampaitanggal from ".$dbname.".sdm_cutidt where karyawanid='".$rKry['karyawanid']."' order by daritanggal desc";
						$qCek=mysql_query($sCek) or die(mysql_error($conn));
						$rCek=mysql_fetch_assoc($qCek);
						if(($rCek['daritanggal']>=$tgl)&&($rCek['sampaitanggalfrom']<=$tgl)){
							continue;
						}
                      $scek="select * from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$rKry['karyawanid']."'";
                      $qcek=mysql_query($scek) or die(mysql_error($conn));
                      $rcek=mysql_num_rows($qcek);
                        if($rcek>0){
                            $rcekd=mysql_fetch_assoc($qcek);
                            if($rcekd['kodeorg']==$idAbn[0]){
                               $optKry.="<option value=".$rKry['karyawanid'].">".$rKry['nik']." - ".$rKry['namakaryawan']."</option>";
                            }
                        }else{
                            $optKry.="<option value=".$rKry['karyawanid'].">".$rKry['nik']." - ".$rKry['namakaryawan']."</option>";
                        }
                    }else{
                        $optKry.="<option value=".$rKry['karyawanid'].">".$rKry['nik']." - ".$rKry['namakaryawan']."</option>";
                    }
		}

		$whre=" kodeorg='".$idAbn[0]."'";
		$optShift=makeOption($dbname,'pabrik_5shift','shift',$whre) ;
		$optAbsen=makeOption($dbname,'sdm_5absensi','kodeabsen,keterangan') ;
		for($t=0;$t<24;)
		{
				if(strlen($t)<2)
				{
						$t="0".$t;
				}
				$jm.="<option value=".$t." ".($t==00?'selected':'').">".$t."</option>";
				$t++;
		}
		for($y=0;$y<60;)
		{
				if(strlen($y)<2)
				{
						$y="0".$y;
				}
				$mnt.="<option value=".$y." ".($y==00?'selected':'').">".$y."</option>";
				$y++;
		}

		$table .= "<tr id='detail_tr' class='rowcontent'>";
		
		$table .="<td><select id=krywnId onchange=getPremiTetap() style=\"width:200px;\">".$optKry."</select> <img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namakaryawan']."/".$_SESSION['lang']['nik']."','1',event);\"  /></td>";
		
	/*	$table .= "<td>".makeElement("krywnId",'select','',
		array('style'=>'width:250px','onchange'=>'getPremiTetap()'),$optKry)."
                           <img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namakaryawan']."/".$_SESSION['lang']['nik']."','1',event);\"  />
                           </td>";*/
		
		$table .= "<td>".makeElement("shiftId",'text','',
		array('style'=>'width:120px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
		//$table .= "<td><select id=premiPil name=premiPil onchange=getPremiTetap()><option value=1>Yes</option><option value=0>No</option></select></td>";
		$table .= "<td>".makeElement("absniId",'select','',
		array('style'=>'width:100px','onchange'=>'getPremiTetap()'),$optAbsen)."</td>";
		$table .= "<td><select id=jmId name=jmId onchange=getPremiTetap()>".$jm."</select>:<select id=mntId name=mntId onchange=getPremiTetap()>".$mnt."</select></td>";
		$table .= "<td><select id=jmId2 name=jmId2 onchange=getPremiTetap()>".$jm."</select>:<select id=mntId2 name=mntId2 onchange=getPremiTetap()>".$mnt."</select></td>";
		//$table .= "<td><select id=catu name=catu><option value=1>Yes</option><option value=0>No</option></select></td>";
		
		$table .= "<td><input type=text id=dendakehadiran class=myinputtextnumber size=12 onkeypress=\"return angka_doang(event)\" value=0></td>";
		$table .= "<td><input type=text id=premiInsentif class=myinputtextnumber size=12 onkeypress=\"return angka_doang(event)\" /></td>";
		$table .= "<td>".makeElement("ktrng",'text','',
		array('style'=>'width:150px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
		# Add, Container Delete
		$table .= "<td><input type=hidden id=insentif value='' /><input type=hidden id=premi /><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/>";
		$table .= "&nbsp;<img id='detail_delete' /></td>";
		$table .= "</tr>";
		$table .= "</tbody>";
		$table .= "</table>";
		echo $table;
		break;
		case'loadDetail':
		$sDt="select * from ".$dbname.".sdm_absensidt where kodeorg='".$kdOrg."' and tanggal='".$tgAbsn."'";
		$qDt=mysql_query($sDt) or die(mysql_error());
		while($rDet=mysql_fetch_assoc($qDt))
		{
			$sNm="select nik,namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$rDet['karyawanid']."'";
			$qNm=mysql_query($sNm) or die(mysql_error());
			$rNm=mysql_fetch_assoc($qNm);

			$sAbsn="select keterangan from ".$dbname.".sdm_5absensi where kodeabsen='".$rDet['absensi']."'";
			$qAbsn=mysql_query($sAbsn) or die(mysql_error());
			$rAbsn=mysql_fetch_assoc($qAbsn);
			$no+=1;
			$strot=0;
			$drpermi=$rDet['premi'];
			if($drpermi!=0){
				$strot=1;
			}
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$rNm['nik']."</td>
                        <td>".$rNm['namakaryawan']."</td>
			<td>".$rDet['shift']."</td>
			<td>".$rAbsn['keterangan']."</td>
			<td>".$rDet['jam']."</td>
			<td>".$rDet['jamPlg']."</td>
			<td align=right>".number_format($drpermi)."</td>
			<td align=right>".number_format($rDet['penaltykehadiran'])."</td>
			<td>".$rDet['penjelasan']."</td>";
			if($rDet['absensi']!='C'){
				echo"<td><img src=images/application/application_edit.png class=resicon  title='Edit' 
				onclick=\"editDetail('".$rDet['karyawanid']."','".$rDet['shift']."','".$rDet['absensi']."','".$rDet['jam']."','".$rDet['jamPlg']."','".$rDet['penjelasan']."','".$rDet['penaltykehadiran']."','".$rDet['premi']."','".$rDet['insentif']."');\">
				<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDetail('".$rDet['kodeorg']."','".tanggalnormal($rDet['tanggal'])."','".$rDet['karyawanid']."');\" ></td>";
			}
			echo"</tr>";
		}

		break;
                case'getKary'://indra
                if($_POST['unit']==''){
                    exit("error:".$_SESSION['lang']['kodeorg']." can't empty");
                    
                }

                $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
                $tab.="<thead>";
                $tab.="<tr><td>".$_SESSION['lang']['nik']."</td>";
                $tab.="<td>".$_SESSION['lang']['namakaryawan']."</td>";
                $tab.="</tr></thead><tbody>";
                if(strlen($_POST['unit'])=='6'){
                    $wher="subbagian='".$_POST['unit']."'  and (tanggalkeluar>".tanggaldgnbar($_POST['tanggalcr'])." or tanggalkeluar='0000-00-00')  and tipekaryawan!=0";
                    $sDt="select karyawanid,nik,namakaryawan,subbagian from ".$dbname.".datakaryawan
                             where ".$wher." and karyawanid is not null and (namakaryawan like '%".$_POST['nmkary']."%' or nik like '%".$_POST['nmkary']."%')";
                }else{
                    $wher="lokasitugas='".$_POST['unit']."' and (subbagian is null or subbagian='') and tipekaryawan!=0   and (tanggalkeluar>".tanggaldgnbar($_POST['tanggalcr'])." or tanggalkeluar='0000-00-00')";
                        $sDt="select karyawanid,nik,namakaryawan,subbagian from ".$dbname.".datakaryawan
                             where ".$wher." and karyawanid is not null and (namakaryawan like '%".$_POST['nmkary']."%' or nik like '%".$_POST['nmkary']."%')";
                        $sDt.="UNION 
                             select a.karyawanid as karyawanid,nik,namakaryawan,b.lokasitugas from ".$dbname.".setup_temp_lokasitugas a 
                             left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
                             where kodeorg='".$_POST['unit']."'   and a.karyawanid is not null and (namakaryawan like '%".$_POST['nmkary']."%' or nik like '%".$_POST['nmkary']."%')";
                }
               //echo $sDt;
                $qDt=mysql_query($sDt) or die(mysql_error($conn));
		while($rDt=mysql_fetch_assoc($qDt)){
                    if(strlen($rDt['karyawanid'])<10){
                        $rDt['karyawanid']=  addZero($rDt['karyawanid'], 10);
                    }
                        $clid="onclick=setKary('".$rDt['karyawanid']."') style=cursor:pointer;";
                        $tab.="<tr ".$clid." class=rowcontent><td>".$rDt['nik']."</td>";
                        $tab.="<td>".$rDt['namakaryawan']."</td>";
                        $tab.="</tr>";
                	}

                $tab.="</tbody></table>";
                echo $tab;
            break;
		default:
		break;
	}

?>