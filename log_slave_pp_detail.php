<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

if($_POST['proses']=='createTable') {
    # Get Data
    $query = selectQuery($dbname,'log_prapodt',"*","`nopp`='".$_POST['id']."'"); 
    $data = fetchData($query);
    $method=$_POST['method'];
    # Create Detail Table
    createTabDetail($_POST['id'],$data);
} else {
    $data = $_POST;
        $tglsdt=tanggalsystem($data['tgl_sdt']);
        $rtglpp=tanggalsystem($data['rtgl_pp']);
    unset($data['proses']);
    switch($_POST['proses']) {
        case 'detail_add' :
            # Check Valid Data
                        $starttime=strtotime($data['rtgl_pp']);//time();// tanggal sekarang
                        $endtime=strtotime($data['tgl_sdt']);//tanggal pembuatan dokumen
                        $timediff = $endtime-$starttime;
                        $days=intval($timediff/86400);
                        //echo "Warning :".$days;//exit();
                        //echo "warning:".$data['kd_brg']."--".$data['kd_angrn']."--".$data['jmlhDiminta']."--".$data['tgl_sdt']."--".$data['rtgl_pp'];exit();
            if(($data['kd_brg']=='') or ($data['jmlhDiminta']=='') or ($tglsdt<$rtglpp) or $days<7) {
                echo "Error : ".$_SESSION['lang']['pp7hari'];
                exit;
            }
			$sSat="select satuan from ".$dbname.".log_5masterbarang where kodebarang='".$data['kd_brg']."'";
			$qSat=mysql_query($sSat) or die(mysql_error());
			$rSat=mysql_fetch_assoc($qSat);
                        $ql="select * from ".$dbname.".log_prapoht where `nopp`='".$data['kode']."'" ;
                        $qry=mysql_query($ql) or die(mysql_error());
                        $res=mysql_fetch_assoc($qry);
                        if($res['nopp']!='')
                        {
                                        # Make Query

                                        $data['tgl_sdt']=tanggalsystem($data['tgl_sdt']);
                                   /* $colomn=array('nopp','kodebarang','kd_anggran','jumlah','tgl_sdt','keterangan');
                                        $query = insertQuery($dbname,'log_prapodt',$data,$colomn); */
                                        $query="insert into ".$dbname.".log_prapodt (`nopp`,`kodebarang`,`kd_anggran`,`jumlah`,`tgl_sdt`,`keterangan`,`satuanrealisasi`) values ('".$data['kode']."','".$data['kd_brg']."','".$data['kd_angrn']."','".$data['jmlhDiminta']."','".$data['tgl_sdt']."','".$data['ket']."','".$rSat['satuan']."')";
                                # Insert Data
                                if(!mysql_query($query)) {
                                        echo "DB Error : ".mysql_error($conn);
                                }
                                //echo $query; exit();
                        }
                        else
                        {	
                                $nopp=$data['kode'];
                                $tgl=tanggalsystem($data['rtgl_pp']);
                                $kodeorg=$data['rkd_bag'];
                                $id_user=$_POST['user_id'];
                                //if($_SESSION['empl']['tipeinduk']=='HOLDING')
//				{
//				$sorg="select alokasi from ".$dbname.".organisasi where kodeorganisasi='".$kodeorg."'";
//				}
//				else
//				{
//				$sorg="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kodeorg."'";
//				}
                                $sorg="select alokasi from ".$dbname.".organisasi where kodeorganisasi='".$kodeorg."'";
                                $qorg=mysql_query($sorg) or die(mysql_error());
                                $rorg=mysql_fetch_assoc($qorg);
                                //if($rorg['induk']=='')
//				{
//				$rorg['induk']=$rorg['alokasi'];
//				}
                                $kd_org=$rorg['alokasi'];
                                $ins="insert into ".$dbname.".log_prapoht (`nopp`,`kodeorg`,`tanggal`,`dibuat`,`catatan`) values ('".$nopp."','".$kd_org."','".$tgl."','".$id_user."','".$_POST['catatan']."')";
                                //exit("Error:$ins");
								$qry=mysql_query($ins) or die(mysql_error());
                                $data['tgl_sdt']=tanggalsystem($data['tgl_sdt']);
                                $query="insert into ".$dbname.".log_prapodt (`nopp`,`kodebarang`,`kd_anggran`,`jumlah`,`tgl_sdt`,`keterangan`,`satuanrealisasi`) values ('".$data['kode']."','".$data['kd_brg']."','".$data['kd_angrn']."','".$data['jmlhDiminta']."','".$data['tgl_sdt']."','".$data['ket']."','".$rSat['satuan']."')";
                                # Insert Data
                                if(!mysql_query($query)) {
                                echo "DB Error : ".mysql_error($conn);
                                }

                        }
            break;
			
			
			
			
			
			
        case 'detail_edit' :
           
            # Check Valid Data
            if(($data['nopp']=='') or ($data['kd_brg']=='') or ($data['jmlhDiminta']=='')) {
                echo "Error : Data should not be empty";
                exit;
            }

            # Rearrange Data
                        $data['tgl_sdt']=tanggalsystem($data['tgl_sdt']);

            # Create Condition
            $where = "`nopp`='".$data['nopp']."'";
            $where .= " and `kodebarang`='".$data['kd_brg']."'";

            # Make Query
           // unset($data['nopp']);
            //unset($data['kd_brg']);
                        //$column=array('kodebarang','kd_anggran','jumlah','tgl_sdt','keterangan');
           // $query = updateQuery($dbname,'log_prapodt',$data,$column,$where);
            $sSat="select satuan from ".$dbname.".log_5masterbarang where kodebarang='".$data['kd_brg']."'";
            $qSat=mysql_query($sSat) or die(mysql_error());
            $rSat=mysql_fetch_assoc($qSat);
                   $query = "update ".$dbname.".`log_prapodt` set kodebarang='".$data['kd_brg']."',kd_anggran='".$data['kd_angrn']."',jumlah='".$data['jmlhDiminta']."',tgl_sdt='".$data['tgl_sdt']."', keterangan='".$data['ket']."', satuanrealisasi='".$rSat['satuan']."' where `nopp`='".$data['nopp']."' and `kodebarang`='".$data['oldKdbrg']."'";

            # Update Data
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error($conn);
            }
            echo $query; exit();
            break;

        case 'detail_delete' :
            $data = $_POST;

            # Rearrange Data
            $tmpTgl = tanggalsystem($data['tgl_sdt']);
            $data['tgl_sdt'] = $tmpTgl;


            # Create Condition
            $where = "`nopp`='".$data['nopp']."'";
            $where .= " and `kodebarang`='".$data['kd_brg']."'";

            # Create Query
            $query = "delete from `".$dbname."`.`log_prapodt` where ".$where;
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
        global $dbname;
        global $conn;
        global $key;
        global $method;

    //echo "<button class=mybutton onclick=addNewRow('detailBody',true)>Add Details</button><br />";
    $table = "<b>".$_SESSION['lang']['nopp']."</b> : ".makeElement("detail_kode",'text',$id,array('disabled'=>'disabled','style'=>'width:150px'));
    $table .= "<table id='ppDetailTable'>";
    # Header
    $table .= "<thead>";
    $table .= "<tr>";
    $table .= "<td>".$_SESSION['lang']['kodebarang']."</td>";
    $table .= "<td>".$_SESSION['lang']['namabarang']."</td>";
    $table .= "<td>".$_SESSION['lang']['satuan']."</td>";
    $table .= "<td>".$_SESSION['lang']['kodeanggaran']."</td>";
    $table .= "<td>".$_SESSION['lang']['jmlhDiminta']."</td>";
    $table .= "<td>".$_SESSION['lang']['tanggalSdt']."</td>";
    $table .= "<td>".$_SESSION['lang']['keterangan']."</td>";
//    $table .= "<td>"."<a href=# onclick=addNewRow(detailBody,true)><img src='images\newfile.png'></a>"."</td>";
    $table .= "<td colspan=3>Action</td>";
    $table .= "</tr>";
    $table .= "</thead>";

    # Data
    $table .= "<tbody id='detailBody'>";

    $i=0;
	 
	//exit("error:");
    #======= Display Data =======
    if($data!=array()) {
        foreach($data as $key=>$row) {
                        $ql="select * from ".$dbname.".`log_5masterbarang` where `kodebarang`='".$row['kodebarang']."'"; //echo $ql;
						//exit("error".$ql);
                        $qry=mysql_query($ql) or die(mysql_error());
                        $res=mysql_fetch_assoc($qry);
                /*	
                        $ql2="select * from ".$dbname.".`keu_anggaran` where `kodeanggaran`='".$row['kd_anggran']."'"; //echo $ql;
                        $qry2=mysql_query($ql2) or die(mysql_error());
                        $res2=mysql_fetch_assoc($qry2);
                */	
            $tmpTgl = tanggalnormal($row['tgl_sdt']);
            $row['tgl_sdt'] = $tmpTgl;
			$table .= "<tr id='detail_tr_".$key."' class='rowcontent'>";
            $table .= "<td onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";><input type=text class=myinputtext value=".$row['kodebarang']." id='kd_brg_".$key."' style='width:120px' readonly /></td>";
            $table .= "<td onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";>
			<input type=text class=myinputtext value='".$res['namabarang']."' id='nm_brg_".$key."' style='width:120px' readonly />
			</td>";
             $table .= "<td onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";>
			 <input type=text class=myinputtext value=".$res['satuan']." id='sat_".$key."' style='width:70px' readonly />
			 <img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";><input type=hidden id=oldKdbrg_".$key." name=oldKdbrg_".$key." value='".$row['kodebarang']."'>
			 </td>";
            $table .= "<td><input type=text id='kd_angrn_".$key."' style='width:70px' disabled=disabled class=myinputtext /><img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchAngrn('".$_SESSION['lang']['findAngrn']."','<fieldset><legend>".$_SESSION['lang']['findnoAngrn']."</legend>Find<input type=text class=myinputtext id=no_angrn><button class=mybutton onclick=findAngrn()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";></td>";
                        $table .= "<td>".makeElement("jmlhDiminta_".$key."",'textnum',$row['jumlah'],
                array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)','class=myinputtext'))."</td>";	
                        $table .= "<td>
						<input type=text id='tgl_sdt_".$key."' value='".$row['tgl_sdt']."' onkeypress='return tanpa_kutip(event)' readonly='readonly' onmousemove='setCalendar(this.id)' style='width:70px' class='myinputtext' /></td>";
                        $table .= "<td>
						<input type=text id='ket_".$key."' class=myinputtext style='width:130px' value='".$row['keterangan']."' onkeypress='return tanpa_kutip(event)' />";
            $table .= "<td><img id='detail_edit_".$key."' title='Edit' class=zImgBtn onclick=\"editDetail('".$key."')\" src='images/save.png'/>";
            $table .= "&nbsp;<img id='detail_delete_".$key."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$key."')\" src='images/delete_32.png'/></td>";
                        $table .= "</tr>";
            /* $table .= "<tr id='detail_tr_".$key."' class='rowcontent'>";
            $table .= "<td onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";>".makeElement("kd_brg_".$key."",'txt',$row['kodebarang'],
                array('style'=>'width:120px','readonly'=>'readonly','cursor'=>'pointer','class=myinputtext'))."</td>";
            $table .= "<td onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";>".makeElement("nm_brg_".$key."",'txt',$res['namabarang'],
                array('style'=>'width:120px','readonly'=>'readonly','cursor'=>'pointer','class=myinputtext'))."</td>";
                        $table .= "<td onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";>".makeElement("sat_".$key."",'txt',$res['satuan'],
                array('style'=>'width:70px','readonly'=>'readonly','cursor'=>'pointer','class=myinputtext'))."<img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";><input type=hidden id=oldKdbrg_".$key." name=oldKdbrg_".$key." value='".$row['kodebarang']."'></td>";
                        $table .= "<td>".makeElement("kd_angrn_".$key."",'txt','',
                array('style'=>'width:70px','disabled'=>'disabled','class=myinputtext'))."<img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchAngrn('".$_SESSION['lang']['findAngrn']."','<fieldset><legend>".$_SESSION['lang']['findnoAngrn']."</legend>Find<input type=text class=myinputtext id=no_angrn><button class=mybutton onclick=findAngrn()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";></td>";
                        $table .= "<td>".makeElement("jmlhDiminta_".$key."",'textnum',$row['jumlah'],
                array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)','class=myinputtext'))."</td>";	
                        $table .= "<td>".makeElement("tgl_sdt_".$key."",'txt',$row['tgl_sdt'],
        array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)','onmousemove'=>'setCalendar(this.id)','readonly'=>'readonly','class=myinputtext'))."</td>";
                        $table .= "<td>".makeElement("ket_".$key."",'txt',$row['keterangan'],
                array('style'=>'width:130px','class=myinputtext','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
            $table .= "<td><img id='detail_edit_".$key."' title='Edit' class=zImgBtn onclick=\"editDetail('".$key."')\" src='images/save.png'/>";
            $table .= "&nbsp;<img id='detail_delete_".$key."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$key."')\" src='images/delete_32.png'/></td>";
                        $table .= "</tr>"; */
            $i = $key;
        }

                $i++;
    }

    #======= New Row ===========
    $table .= "<tr id='detail_tr_".$i."' class='rowcontent'>";
        $table .= "<td onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$i.">',event)\";>".makeElement("kd_brg_".$i."",'txt','',array('style'=>'width:120px','readonly'=>'readonly','cursor'=>'pointer','class=myinputtext'))."</td>";
        $table .= "<td onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$i.">',event)\";>".makeElement("nm_brg_".$i."",'txt','',array('style'=>'width:120px','readonly'=>'readonly','cursor'=>'pointer','class=myinputtext'))."</td>";
        $table .= "<td onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$i.">',event)\";>".makeElement("sat_".$i."",'txt','',array('style'=>'width:70px','readonly'=>'readonly','cursor'=>'pointer','class=myinputtext'))."<img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><input type=hidden id=nomor name=nomor value=".$i."><div id=container></div>',event)\";><input type=hidden id=oldKdbrg_".$i." name=oldKdbrg_".$i.">"."</td>";
        $table .= "<td>".makeElement("kd_angrn_".$i."",'txt','',array('style'=>'width:70px','disabled'=>'disabled','class=myinputtext'))."<img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchAngrn('".$_SESSION['lang']['findAngrn']."','<fieldset><legend>".$_SESSION['lang']['findnoAngrn']."</legend>Find<input type=text class=myinputtext id=no_angrn><button class=mybutton onclick=findAngrn()>Find</button></fieldset><input type=hidden id=nomor name=nomor value=".$i."><div id=container></div>',event)\";></td>";
        $table .= "<td>".makeElement("jmlhDiminta_".$i."",'textnum','',array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)','class=myinputtext'))."</td>";	
        $table .= "<td>".makeElement("tgl_sdt_".$i."",'txt','',array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)','onmousemove'=>'setCalendar(this.id)','readonly'=>'readonly','class=myinputtext'))."</td>";
        $table .= "<td>".makeElement("ket_".$i."",'txt','',array('style'=>'width:130px','class=myinputtext','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
        $table .= makeElement("nopp_".$i."",'hidden',$id,array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";


    # Add, Container Delete
    $table .= "<td><img id='detail_add_".$i."' title='Simpan' class=zImgBtn onclick=\"addDetail('".$i."')\" src='images/save.png'/>";
    $table .= "&nbsp;<img id='detail_delete_".$i."' /></td>";
    $table .= "</tr>";

    $table .= "</tbody>";
    $table .= "</table>";
    if($method=='update'){
        $whrdt="nopp='".$id."'";
        $cttn=  makeOption($dbname, 'log_prapoht', 'nopp,catatan',$whrdt);
         echo $table."####".$cttn[$id];
    }else{
        echo $table;
    }
   
}
?>