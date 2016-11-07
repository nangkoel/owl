<?php
session_start();
include_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


if($_POST['proses']=='createTable') {
    # Get Data
    if($_POST['saveStat']==1)
    {
        $table="log_prapodt";
        $where="nopp='".$_POST['id']."' and status=0";
    }
    else
    {
        $where="`nomor`='".$_POST['id']."'";
        $table="log_permintaanhargadt";
        $_POST['idPer']=$_POST['id'];
    }
    $query = selectQuery($dbname,$table,"*",$where);
    $data = fetchData($query);
    
    # Create Detail Table
    createTabDetail($_POST['id'],$data);
} else {
    $data = $_POST;
    unset($data['proses']);
    switch($_POST['proses']) {
        case 'detail_add' :
            # Check Valid Data
            if($data['kdbrg']=='' or $data['jmlh']=='' ) {
                echo "Error : Kode Barang, Spesifikasi dan Jumlah Barang Tidak Boleh Kosong";
                exit;
            }
            if($data['tglDari']=='')
            {
                $data['tglDari']='00-00-0000';
            }
            if($data['tglSamp']=='')
            {
                $data['tglSamp']='00-00-0000';
            }
            if($data['jmlhKurs']=='')
            {
                $data['jmlhKurs']=1;
            }
            if($data['kurs']=='')
            {
                $data['kurs']=null;
            }
			$ql="select nomor from ".$dbname.".log_perintaanhargaht where `nomor`='".$data['kode']."'" ;
			$qry=mysql_query($ql) or die(mysql_error());
			$res=mysql_num_rows($qry);
			if($res>0)
			{
					# Make Query
					if($data['price']=='')
                                        {
                                            $data['price']=0;
                                        }
                                       
					$query="insert into ".$dbname.".log_permintaanhargadt (`nomor`,`kodebarang`,`harga`,`spec`,`jumlah`, `kurs`,`matauang`,`tgldari`,`tglsmp`) 
                                            values ('".$data['kode']."','".$data['kdbrg']."','".$data['price']."','".$data['rspek']."','".$data['jmlh']."','".$data['jmlhKurs']."','".$data['kurs']."','".tanggalsystem($data['tglDari'])."','".tanggalsystem($data['tglSamp'])."')";
					//echo "warning:".$query;exit();
				# Insert Data
				if(!mysql_query($query)) {
					echo "DB Error : ".mysql_error($conn);
				}
				//echo $query; exit();
			}
			else
			{			
					$ins="insert into ".$dbname.".log_perintaanhargaht (`nomor`,`tanggal`,`purchaser`,`supplierid`) 
					values ('".$data['no_permintaan']."','".tanggalsystem($data['tgl'])."','".$data['user_id']."','".$data['supplier_id']."')";
					if(!mysql_query($ins)) {
					echo "DB Error : ".mysql_error($conn);
					}
					else
					{
//					insert into ".$dbname.".log_permintaanhargadt (`nomor`,`kodebarang`,`spec`,`jumlah`,`kurs`,`matauang`) 
//                                                                    values('".$no_prmntan."','".$kdbrg."','".$spec."','".$jmlh."','".$jmlhKurs."','".$mtuang."')		
                                            
                                          if($data['price']=='')
                                        {
                                            $data['price']=0;
                                        }
                                            $query="insert into ".$dbname.".log_permintaanhargadt (`nomor`,`kodebarang`,`harga`,`spec`,`jumlah`, `kurs`,`matauang`,`tgldari`,`tglsmp`) 
                                                            values ('".$data['kode']."','".$data['kdbrg']."','".$data['price']."','".$data['rspek']."','".$data['jmlh']."','".$data['jmlhKurs']."','".$data['kurs']."','".tanggalsystem($data['tglDari'])."','".tanggalsystem($data['tglSamp'])."')";
						//echo "warning:".$query;exit();
					# Insert Data
					if(!mysql_query($query)) {
						echo "DB Error : ".mysql_error($conn);
					}
					}
					
				
			}
            break;
        case 'detail_edit' :
            # Check Valid Data
		
            if($data['kdbrg']=='' or $data['jmlh']=='' ) {
                echo "Error : Data Barang, Jumlah tidak boleh kosong";
                exit;
            }
            
            # Rearrange Data
            
            # Create Condition
            $where = "`nomor`='".$data['kode']."'";
            $where .= " and `kodebarang`='".$data['kdbrg']."'";
            if($data['price']=='')
            {
                $data['price']=0;
            }
            # Make Query
		   $query = "update ".$dbname.".`log_permintaanhargadt` set kodebarang='".$data['kdbrg']."',harga='".$data['price']."',spec='".$data['rspek']."',jumlah='".$data['jmlh']."',kurs='".$data['jmlhKurs']."',matauang='".$data['krs']."',`tgldari`='".tanggalsystem($data['tglDari'])."',`tglsmp`='".tanggalsystem($data['tglSamp'])."'  
                       where `nomor`='".$data['kode']."' and `kodebarang`='".$data['oldKdbrg']."'";
		  //echo "warning".$query;exit();

            # Update Data
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error($conn);
            }
            //echo $query; exit();
            break;
			
        case 'detail_delete' :
            $data = $_POST;
            
            # Rearrange Data
                   
            
            # Create Condition
            $where = "`nomor`='".$data['kode']."'";
            $where .= " and `kodebarang`='".$data['kdbrg']."'";
            
            # Create Query
            $query = "delete from `".$dbname."`.`log_permintaanhargadt` where ".$where;
            
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
	global $optKurs;
	
    //echo "<button class=mybutton onclick=addNewRow('detailBody',true)>Add Details</button><br />";
    $table = "<b>".$_SESSION['lang']['nopermintaan']."</b> : ".makeElement("detail_kode",'text',$_POST['idPer'],array('disabled'=>'disabled','style'=>'width:150px'));
    $table .= "<table id='ppDetailTable'>";
    # Header
    $table .= "<thead>";
    $table .= "<tr>";
    $table .= "<td>".$_SESSION['lang']['kodebarang']."</td>";
    $table .= "<td>".$_SESSION['lang']['namabarang']."</td>";
    $table .= "<td>".$_SESSION['lang']['satuan']."</td>";
	//$table .= "<td>".$_SESSION['lang']['nopp']."</td>";
    $table .= "<td>".$_SESSION['lang']['spesifikasi']."</td>";
    
    $table .= "<td>".$_SESSION['lang']['kurs']."</td>";
    $table .= "<td>".$_SESSION['lang']['tgldari']."</td>";
    $table .= "<td>".$_SESSION['lang']['tglsmp']."</td>";
    $table .= "<td>".$_SESSION['lang']['jumlah']."</td>";
    $table .= "<td>".$_SESSION['lang']['harga']."</td>";
    $table .= "<td>".$_SESSION['lang']['subtotal']."</td>";
//    $table .= "<td>"."<a href=# onclick=addNewRow(detailBody,true)><img src='images\newfile.png'></a>"."</td>";
    $table .= "<td colspan=3>Action</td>";
    $table .= "</tr>";
    $table .= "</thead>";
    
    # Data
    $table .= "<tbody id='detailBody'>";
    
    $i=0;
    
    #======= Display Data =======
    if($data!=array()) {
        foreach($data as $key=>$row) {
			$ql="select * from ".$dbname.".`log_5masterbarang` where `kodebarang`='".$row['kodebarang']."'"; //echo $ql;
			$qry=mysql_query($ql) or die(mysql_error());
			$res=mysql_fetch_assoc($qry);
			$columnw=array(0=>'Rupiah',1=>'USD');
			$optTest=makeOption('','',$columnw,'',3);
			
			$optNopp='';
			$sql="SELECT a.nopp FROM ".$dbname.".`log_prapodt` a left join ".$dbname.".`log_prapoht` b on a.nopp=b.nopp where b.close=2 
			and (a.create_po is null or create_po='') 
			and a.kodebarang='".$row['kodebarang']."'"; //echo "warning".$sql;
			$query=mysql_query($sql) or die(mysql_error());
			while($rest=mysql_fetch_assoc($query))
			{
			$optNopp.="<option '".($row['nopp']==$rest['nopp']?'selected=selected':'')."' value=".$rest['nopp'].">".$rest['nopp']."</option>";
			}
			$optKurs2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
			$sKurs2="select  distinct kode,kodeiso from ".$dbname.".setup_matauang order by kode desc";
			$qKurs2=mysql_query($sKurs2) or die(mysql_error());
			while($rKurs2=mysql_fetch_assoc($qKurs2))
			{
				$optKurs2.="<option value=".$rKurs2['kode']." ".($row['matauang']==$rKurs2['kode']?'selected=selected':'')." >".$rKurs2['kodeiso']."</option>";
			}
			if($row['tgldari']=='')
                        {
                            $row['tgldari']="0000-00-00";
                        }
                        if($row['tglsmp']=='')
                        {
                            $row['tglsmp']="0000-00-00";
                        }
                        if($row['kurs']=='')
                        {
                            $row['kurs']=1;
                        }
                    
                            $sub_total=$row['harga']*$row['jumlah'];
                 
            $table .= "<tr id='detail_tr_".$key."' class='rowcontent'>";
            $table .= "<td id='dtKdbrg_".$key."'>".makeElement("kd_brg_".$key."",'txt',$row['kodebarang'],
                array('style'=>'width:120px','disabled'=>'disabled','class=myinputtext'))." <input type=hidden id=oldKdbrg_".$key." name=oldKdbrg_".$key." value=".$row['kodebarang']." /></td>";
            $table .= "<td>".makeElement("nm_brg_".$key."",'txt',$res['namabarang'],
                array('style'=>'width:120px','disabled'=>'disabled','class=myinputtext'))."</td>";
            $table .= "<td>".makeElement("sat_".$key."",'txt',$res['satuan'],
                array('style'=>'width:70px','disabled'=>'disabled','class=myinputtext'));//."<img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";></td>";
                                //$table .= "<td><select id=nopp_".$key.">".$optNopp."</select></td>";
            $table .= "<td>".makeElement("spek_".$key."",'txt',$row['spec'],
                array('style'=>'width:230px','class=myinputtext','onkeypress'=>'return angka_doang(event)','maxlenght'=>'100'))."</td>";

            $table .= "<td><select id=kurs_".$key." onchange='getKurs(".$key.")'>".$optKurs2."</select><input type=hidden id=jmlhKurs_".$key." name=jmlhKurs_".$key." value=".$row['kurs']." /></td>";
            $table .= "<td>".makeElement("tgl_dari_".$key."",'txt',tanggalnormal($row['tgldari']),
            array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)','onmousemove'=>'setCalendar(this.id)','readonly'=>'readonly','class=myinputtext'))."</td>";
            $table .= "<td>".makeElement("tgl_smp_".$key."",'txt',tanggalnormal($row['tglsmp']),
            array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)','onmousemove'=>'setCalendar(this.id)','readonly'=>'readonly','class=myinputtext'))."</td>";
                        //$table .= "<td>".makeElement("kurs_".$key."",'select',$row['kurs'],
            //                array('style'=>'width:70px','class=myinputtext','onkeypress'=>'return tanpa_kutip(event)'),$optKurs)."</td>";
            $table .= "<td>".makeElement("jumlah_".$key."",'textnumber',number_format($row['jumlah'],2),
                array('style'=>'width:70px','class=myinputtext','onkeypress'=>'return angka_doang(event)','onblur'=>"display_number('".$key."')",'onfocus'=>"normal_number('".$key."')",'onkeyup'=>"calculate('".$key."')"))."</td>";
            $table .= "<td>".makeElement("price_".$key."",'textnumber',number_format($row['harga'],2,'.',','),
                array('style'=>'width:100px','onblur'=>"display_number('".$key."')",'onfocus'=>"normal_number('".$key."')",'onkeyup'=>"calculate('".$key."')"))."</td>";
            $table .= "<td>".makeElement("total_".$key."",'textnum',number_format($sub_total,2,'.',','),
                array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','disabled'=>'disabled'))."</td>";
            $table .= "<td align=center><img id='detail_delete_".$key."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$key."')\" src='images/delete_32.png'/></td>";
            $table .= "</tr>";
            $i = $key;
        }
        $i++;
    }
//    <img id='detail_edit_".$key."' title='Edit' class=zImgBtn onclick=\"editDetail('".$key."')\" src='images/save.png'/>";
//            $table .= "&nbsp;
   
$sHrgPnwr="select ppn,subtotal,diskonpersen,nilaidiskon,nilaipermintaan,catatan from ".$dbname.".log_perintaanhargaht where nomor='".$_POST['idPer']."'";
$qHrgPnwr=mysql_query($sHrgPnwr) or die(mysql_error($conn));
$rHrgPnwr=mysql_fetch_assoc($qHrgPnwr);
if($rHrgPnwr['subtotal']==''||is_null($rHrgPnwr['subtotal']))
{
    $rHrgPnwr['subtotal']=$rHrgPnwr['ppn']=$rHrgPnwr['diskonpersen']=0;
    $rHrgPnwr['nilaipermintaan']= $rHrgPnwr['nilaidiskon']=0;
}
         #======= New Row ===========
	//$columnw=array(0=>'Rupiah',1=>'USD');
//	$optTest=makeOption('','',$columnw,'',3);
//	$sql="SELECT a.nopp FROM ".$dbname.".`log_prapodt` a left join ".$dbname.".`log_prapoht` b on a.nopp=b.nopp where b.close=2 
//			and (a.create_po is null or create_po='') 
//			and a.kodebarang='".$row['kodebarang']."'"; //echo "warning".$sql;
//			$query=mysql_query($sql) or die(mysql_error());
//			while($rest=mysql_fetch_assoc($query))
//			{
//			$optNopp.="<option '".($row['nopp']==$rest['nopp']?'selected=selected':'')."' value=".$rest['nopp'].">".$rest['nopp']."</option>";
//			}
//                        $sKurs="select distinct kode,kodeiso from ".$dbname.".setup_matauang order by kode desc";
//	$qKurs=mysql_query($sKurs) or die(mysql_error());
//	while($rKurs=mysql_fetch_assoc($qKurs))
//	{
//		$optKurs.="<option value=".$rKurs['kode'].">".$rKurs['kodeiso']."</option>";
//	}
//    $table .= "<tr id='detail_tr_".$i."' class='rowcontent'>";
//	$table .= "<td>".makeElement("kd_brg_".$i."",'txt','',array('style'=>'width:120px','disabled'=>'disabled','class=myinputtext','onchange'=>"get_nopp('".$i."')"))."<input type=hidden id=oldKdbrg_".$i." name=oldKdbrg_".$i." /></td>";
//	$table .= "<td>".makeElement("nm_brg_".$i."",'txt','',array('style'=>'width:120px','disabled'=>'disabled','class=myinputtext'))."</td>";
//	$table .= "<td>".makeElement("sat_".$i."",'txt','',array('style'=>'width:70px','disabled'=>'disabled','class=myinputtext'))."<img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><input type=hidden id=nomor name=nomor value=".$i."><div id=container></div>',event)\";>";
//	//$table .= "<td><select id=nopp_".$i.">".$optNopp."</select></td>";
//	//$table .= makeElement("nomor_".$i."",'hidden',$id,array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)'));."</td>";
//	$table .= "<td>".makeElement("spek_".$i."",'txt','',array('style'=>'width:230px','class=myinputtext','onkeypress'=>'return tanpa_kutip(event)','maxlenght'=>'100'))."</td>";
//	$table .= "<td>".makeElement("jumlah_".$i."",'textnumber','',array('style'=>'width:70px','class=myinputtext','onkeypress'=>'return angka_doang(event)','onblur'=>"display_number('".$i."')",'onfocus'=>"normal_number('".$i."')"))."</td>";
//	$table .= "<td>".makeElement("price_".$i."",'textnumber','',array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)','onblur'=>"display_number('".$i."')",'onfocus'=>"normal_number('".$i."')"))."</td>";
//	$table .= "<td><select id=kurs_".$i." onchange='getKurs(".$i.")'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optKurs."</select><input type=hidden id=jmlhKurs_".$i." name=jmlhKurs_".$i." /></td>";
//        $table .= "<td>".makeElement("tgl_dari_".$i."",'txt','',
//        array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)','onmousemove'=>'setCalendar(this.id)','readonly'=>'readonly','class=myinputtext'))."</td>";
//         $table .= "<td>".makeElement("tgl_smp_".$i."",'txt','',
//        array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)','onmousemove'=>'setCalendar(this.id)','readonly'=>'readonly','class=myinputtext'))."</td>";
////	$table .= "<td>".makeElement("kurs_".$i."",'select','',array('style'=>'width:70px','class=myinputtext'),$optTest)."</td>";
//     # Add, Container Delete
//    $table .= "<td><img id='detail_add_".$i."' title='Tambah' class=zImgBtn onclick=\"addDetail('".$i."')\" src='images/save.png'/>";
//    $table .= "&nbsp;<img id='detail_delete_".$i."' /></td>";
//    $table .= "</tr>";
    //$table.="<tr id='detail_tr_".$i."'><td colspan=12 align='center;>".makeElement("btnAll",'btn',$_SESSION['lang']['save'],array('onclik'=>'saveAll()'))."</td></tr>";
     $table.="<tr><td>&nbsp;</td>
            <td colspan=8 align=right>". $_SESSION['lang']['subtotal']."</td>
            <td><input type=text id=total_harga_po name=total_harga_po disabled  class=myinputtextnumber  style=width:100px value='".$rHrgPnwr['subtotal']."' /></td>
        </tr>
        <tr>
            <td >&nbsp;</td>
            <td colspan=8 align=right>".$_SESSION['lang']['diskon']."Discount</td>
            <td><input type=text  id=angDiskon name=angDiskon class=myinputtextnumber style=width:100px onkeyup=calculate_angDiskon() onkeypress=return angka_doang(event) onblur=\"getZero()\" value='".$rHrgPnwr['nilaidiskon']."' /></td>
        </tr>
		    <tr>
            <td >&nbsp;</td>
            <td colspan=8 align=right>Discount (%)</td>
            <td><input type=text  id=diskon name=diskon class=myinputtextnumber style=width:100px onkeyup=calculate_diskon() maxlength=3 onkeypress=return angka_doang(event) onblur=\"getZero()\" value='".$rHrgPnwr['diskonpersen']."' /> </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan=8 align=right>".$_SESSION['lang']['diskon']."PPh/PPn (%)</td>
            <td><input type=text id=ppN name=ppN  class=myinputtextnumber style=width:100px onkeyup=calculatePpn()  maxlength=2  onkeypress=return angka_doang(event) onblur=\"getZero()\"   />  <input type=hidden id=ppn name=ppn class=myinputtext onkeypress=return angka_doang(event) style=width:100px onblur=\"getZero()\" /><br /><span id=hslPPn>".$rHrgPnwr['ppn']."</span> </td>
        </tr>
         <tr>
            <td>&nbsp;</td>
            <td colspan=8 align=right>".$_SESSION['lang']['grnd_total']."</td>
            <td><input type=text id=grand_total name=grand_total disabled  class=myinputtextnumber style=width:100px value='".$rHrgPnwr['nilaipermintaan']."' /></td>
        </tr><input type=hidden id=sub_total name=sub_total value='".$rHrgPnwr['subtotal']."'><input type=hidden id=nilai_diskon name=nilai_diskon value='".$rHrgPnwr['nilaidiskon']."'  />";
    $table.= "</tbody>";
	
    $table .= "</table>";
    if($rHrgPnwr['catatan']=='')
    {
    echo $table."###";
    }
    else
    {
       echo $table."###".$rHrgPnwr['catatan'];
    }
}
?>