<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('config/connection.php');

if($_POST['proses']=='createTable') {
    # Get Data
	if(isset($_SESSION['temp']['OrgKd']))
	{
		unset($_SESSION['temp']['OrgKd']);
	}
	$idAbn=explode("###",$_POST['absnId']);
	// echo"warning:".$idAbn[1]."###".$idAbn[0];exit();
	$_SESSION['temp']['OrgKd']=$idAbn[0];
	//exit();
	//print_r($_SESSION['temp']['OrgKd']);
	//echo "warning:".$_SESSION['temp']['OrgKd'];exit();
    $query = selectQuery($dbname,'sdm_absensidt',"*","`tanggal`='".tanggalsystem($idAbn[1])."' and `kodeorg`='".$idAbn[0]."'"); 
    $data = fetchData($query);
    
    # Create Detail Table
	/*echo"warning:";
	echo"<pre>";
	print_r($data);
	echo"</pre>";
	exit();*/
    createTabDetail($_POST['absnId'],$data);
} else {
    $data = $_POST;
    unset($data['proses']);
    switch($_POST['proses']) {
        case 'detail_add' :
			if(isset($_SESSION['temp']['OrgKd']))
			{
				unset($_SESSION['temp']['OrgKd']);
			}
			#Check Header
			$tmp=explode("###",$_POST['absnId']);
			$kdOrg=$tmp[0];
			$_SESSION['temp']['OrgKd']=$kdOrg;
			//echo "warning:".$_SESSION['temp']['OrgKd'];exit();
			$tgl=tanggalsystem($tmp[1]);
			$sql="select kodeorg,tanggal from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";//echo "warning:".$sql;exit();
			$query=mysql_query($sql) or die(mysql_error());
			$res=mysql_fetch_row($query);
			//echo "warning:".$res;exit();
			if($res<1)
			{
				$sins="insert into ".$dbname.".sdm_absensiht (`kodeorg`,`tanggal`,`periode`) values ('".$kdOrg."','".$tgl."','".$_POST['period']."')";
				if(mysql_query($sins))
				{
					$dins="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`, `penjelasan`) values ('".$kdOrg."','".$tgl."','".$_POST['krywnId']."','".$_POST['shifTid']."','".$_POST['asbensiId']."','".$_POST['Jam']."','".$_POST['ket']."')";
					//echo "warning:test".$dins;
					if(mysql_query($dins))
					{
						echo"";
					}
					else
					{
					//echo "warning:masuk";
					echo "DB Error : ".mysql_error($conn);
					}
				}
				else
				{
					echo "DB Error : ".mysql_error($conn);
				}
			}
			else
			{
				$dins="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`, `penjelasan`) values 
				('".$kdOrg."','".$tgl."','".$_POST['krywnId']."','".$_POST['shifTid']."','".$_POST['asbensiId']."','".$_POST['Jam']."','".$_POST['ket']."')";
					//echo "warning:test".$dins;
					if(mysql_query($dins))
					{
						echo"";
					}
					else
					{
					//echo "warning:masuk";
					echo "DB Error : ".mysql_error($conn);
					}
			}
            break;
        case 'detail_edit' :
            # Check Valid Data
			//echo "warning:masuk";
		
			$tmp=explode('###',$data['absnId']);
			$kdOrg=$tmp[0];
			$_SESSION['temp']['OrgKd']=$kdOrg;
			$tgl=tanggalsystem($tmp[1]);
			$sCek="select posting from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
			$qCek=mysql_query($sCek) or die(mysql_error());
			$rCek=mysql_fetch_assoc($qCek);
			if($rCek['posting']=='1')
			{
				echo"warning:Already Post This Data";
				exit();
			}
		                 
            # Create Condition
            $where = "`tanggal`='".$tgl."'";
            $where .= " and `kodeorg`='".$kdOrg."'";
			$where .= " and karyawanid='".$data['krywnId']."'";
            
            # Make Query
           
		   $query = "update ".$dbname.".`sdm_absensidt` set shift='".$data['shifTid']."',absensi='".$data['asbensiId']."',jam='".$data['Jam']."', penjelasan='".$data['ket']."' where ".$where."";
			//echo"warning:".$query;exit();
            # Update Data
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error($conn);
            }
			//echo "warning:".$query;exit();
            break;
			
        case 'detail_delete' :
		$tmp=explode('###',$data['absnId']);
		$kdOrg=$tmp[0];
		$tgl=tanggalsystem($tmp[1]);
		$sCek="select posting from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_assoc($qCek);
		if($rCek['posting']=='1')
		{
			echo"warning:Already Post This Data";
			exit();
		}
			//echo "warning:masuk";  
            $data = $_POST;
                             
            # Create Condition
            $where = "`tanggal`='".$tgl."'";
            $where .= " and `kodeorg`='".$kdOrg."'";
			$where .= " and karyawanid='".$data['krywnId']."'";
            
            # Create Query
            $query = "delete from `".$dbname."`.`sdm_absensidt` where ".$where;
			//echo "warning:".$query;
            //echo query;
            # Delete
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error($conn);
            }
            break;
        default :
            break;
    }
}

function createTabDetail($id,$data) {
   // $table = "<b>".$_SESSION['lang']['nospb']."</b> : ".makeElement("detail_kode",'text',$id,array('disabled'=>'disabled','style'=>'width:150px'));
   global $dbname;
    $table .= "<table id='ppDetailTable'>";
	//echo"warning:".$table;
    # Header
    $table .= "<thead>";
    $table .= "<tr>";
    $table .= "<td>".$_SESSION['lang']['namakaryawan']."</td>";
 	$table .= "<td>".$_SESSION['lang']['shift']."</td>";
  	$table .= "<td>".$_SESSION['lang']['absensi']."</td>";
  	$table .= "<td>".$_SESSION['lang']['jam']."</td>";
    $table .= "<td>".$_SESSION['lang']['keterangan']."</td>";
    $table .= "<td>Action</td>";
    $table .= "</tr>";
    $table .= "</thead>";
    
    # Data
    $table .= "<tbody id='detailBody'>";
    
    $i=0;
	
    #======= Display Data =======
    if($data!=array()) {
        foreach($data as $key=>$row) {
			//sort data karyawan by subbagian dan lokasi tugas
		
			
			if(strlen($row['kodeorg'])>4)
			{
				$where=" subbagian='".$row['kodeorg']."'";
			}
			else
			{
				$where="lokasitugas='".$row['kodeorg']."' and subbagian is NULL";
			}
			
			
         	$whre=" kodeorg='".$row['kodeorg']."'";
			$optShift=makeOption('owl','pabrik_5shift','shift',$whre) ;
			
			$optAbsen=makeOption('owl','sdm_5absensi','kodeabsen,keterangan') ;
			
			 //echo"warning:".$where;exit();
			$optKry=makeOption('owl','datakaryawan','karyawanid,namakaryawan',$where);
			$jmr=explode(':',$row['jam']); //echo "warning".$jm[0];
			for($t=0;$t<24;)
			{
				if(strlen($t)<2)
				{
					$t="0".$t;
				}
				$jm.="<option value=".$t." ".($t==$jmr[0]?'selected':'').">".$t."</option>";
				$t++;
			}
			for($y=0;$y<60;)
			{
				if(strlen($y)<2)
				{
					$y="0".$y;
				}
				$mnt.="<option value=".$y." ".($y==$jmr[1]?'selected':'').">".$y."</option>";
				$y++;
			}
			
			
//			$table .= "<tr id='detail_tr_".$key."' class='rowcontent' onclick=\"editData(".$key.")\">";
			$table .= "<tr id='detail_tr_".$key."' class='rowcontent'>";
			$table .= "<td>".makeElement("krywnId_".$key."",'select',$row['karyawanid'],
			array('style'=>'width:150px','disabled'=>'true'),$optKry)."</td>";
			$table .= "<td>".makeElement("shiftId_".$key."",'text',$row['shift'],
			array('style'=>'width:120px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
			$table .= "<td>".makeElement("absniId_".$key."",'select',$row['absensi'],
			array('style'=>'width:100px'),$optAbsen)."</td>";
			$table .= "<td><select id=jmId_".$key." name=jmId_".$key." >".$jm."</select>:<select id=mntId_".$key." name=mntId_".$key." >".$mnt."</select></td>";
			$table .= "<td>".makeElement("ktrng_".$key."",'text',$row['penjelasan'],
			array('style'=>'width:150px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
			$table .= "<td><img id='detail_edit_".$key."' title='Edit' class=zImgBtn onclick=\"editDetail('".$key."')\" src='images/001_45.png'/>";
			$table .= "&nbsp;<img id='detail_delete_".$key."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$key."')\" src='images/delete_32.png'/></td>";
			$table .= "</tr>";
			$i = $key;
        }
        $i++;
    }
    
    #======= New Row ===========

	$idAbn=explode("###",$id);
	
	
	if(strlen($idAbn[0])>4)
	{
		$where=" subbagian='".$idAbn[0]."'";
	}
	else
	{
		$where="lokasitugas='".$idAbn[0]."' and subbagian is NULL";
	}
	//$where=" lokasitugas='".$idAbn[0]."' or subbagian='".$idAbn[0]."'"; 
	//echo"warning:".$where."__".$sPil."___".$idAbn[0]."___=".$rPil['subbagian'];exit();
	$optKry=makeOption('owl','datakaryawan','karyawanid,namakaryawan',$where);
		//$_SESSION['temp']['OrgKd']=$idAbn[0];
	$whre=" kodeorg='".$idAbn[0]."'";
	$optShift=makeOption('owl','pabrik_5shift','shift',$whre) ;
	$optAbsen=makeOption('owl','sdm_5absensi','kodeabsen,keterangan') ;
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
	
	
	
	$table .= "<tr id='detail_tr_".$i."' class='rowcontent'>";
	$table .= "<td>".makeElement("krywnId_".$i."",'select','',
	array('style'=>'width:150px'),$optKry)."</td>";
	$table .= "<td>".makeElement("shiftId_".$i."",'text','',
	array('style'=>'width:120px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
	$table .= "<td>".makeElement("absniId_".$i."",'select','',
	array('style'=>'width:100px'),$optAbsen)."</td>";
	$table .= "<td><select id=jmId_".$i." name=jmId_".$i." >".$jm."</select>:<select id=mntId_".$i." name=mntId_".$i.">".$mnt."</select></td>";
	$table .= "<td>".makeElement("ktrng_".$i."",'text','',
	array('style'=>'width:150px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";


	/*$kodeOrg=substr($id,8,6);
	$where=" induk='".$kodeOrg."' and tipe='BLOK'"; //echo"warning:".$where;exit();
	$optBlok=makeOption('owl','organisasi','kodeorganisasi,namaorganisasi',$where);
	$table .= "<tr id='detail_tr_".$i."' class='rowcontent'>";
	$table .= "<td>".makeElement("blok_".$i."",'select','',
	array('style'=>'width:150px','onchange'=>"getBjr('".$i."')"),$optBlok)."<input type=hidden id=oldBlok_".$i." /></td>";
	$table .= "<td>".makeElement("bjr_".$i."",'textnum','',
	array('style'=>'width:120px','disabled'=>'disabled',))."</td>";
	$table .= "<td>".makeElement("jjng_".$i."",'textnum','',
	array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','maxlength'=>'12'))."</td>";
	$table .= "<td>".makeElement("brondln_".$i."",'textnum','',
	array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','maxlength'=>'12'))."</td>";
*/
    # Add, Container Delete
    $table .= "<td><img id='detail_add_".$i."' title='Simpan' class=zImgBtn onclick=\"addDetail('".$i."')\" src='images/save.png'/>";
    $table .= "&nbsp;<img id='detail_delete_".$i."' /></td>";
    $table .= "</tr>";
    $table .= "</tbody>";
    $table .= "</table>";
    echo $table;
}
?>