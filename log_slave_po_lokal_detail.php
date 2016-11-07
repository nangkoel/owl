<?php
session_start();
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
if($_POST['proses']=='createTable')
{
        # Get Data untuk detail PO
    $rnopp=$_POST['nopp'];
    $baris=$_POST['baris'];
    $kdbrg=$_POST['kdbrg'];
    $bara=0;
    foreach($rnopp as $row =>$Rslt)
    {
           for($a=0;$a<$row;$a++)
           {
               for($b=0;$b<$baris;$b++)
               {
                   if($a!=$b)
                   { 
                       if($kdbrg[$a]==$kdbrg[$b])
                       {
                           $cek+=1;
                           $cekBrg2=$kdbrg[$a];
                       }
                   }  

               }
           }
//           if($cek!=0)
//           {
//
//               echo"warning: Material number : ".$cekBrg2." more than one";
//               exit();
//           }
//           else
//           {
                     if($row==0)
                      {
                        $where.=" nopp='".$Rslt."'";
                        $where2.=" kodebarang='".$kdbrg[$row]."'";			
                      }
                      else
                      {
                        $where.=" or nopp='".$Rslt."'";
                        $where2.=" or kodebarang='".$kdbrg[$row]."'";
                      }   
         //  }
    }

    $query="select * from ".$dbname.".log_prapodt where (".$where.") and (".$where2.")";
    $data = fetchData($query);

        //generate nopo
        $rnopp=$_POST['nopp'];
        $tgl=  date('Ymd');
        $bln = substr($tgl,4,2);
        $thn = substr($tgl,0,4);

        $where="";
        $where2="";
        foreach($rnopp as $row =>$Rslt)
        {
            $kdbrg=$_POST['kdbrg'];
            if($row==0)
            {
                $where.=" nopp='".$Rslt."'";
                $where2.=" kodebarang=$kdbrg[$row]";
            }
            else
            {
                $where.=" or nopp='".$Rslt."'";
                $where2.=" or kodebarang=$kdbrg[$row]";
            }
        }
        $sql="select * from ".$dbname.".log_prapodt where ($where) and ($where2)"; //echo $sql;
        $query=mysql_query($sql) or die(mysql_error());

        $cond="";
        $i=0;
        while($res=mysql_fetch_assoc($query))
        {

            $nopp=substr($res['nopp'],15,4);
             if($i==0)
            {
               // $cond.=" kodeorganisasi='".$nopp."'";
                 $cond.=" nopp='".$res['nopp']."'";
            }
            else
            {
                $cond.=" or nopp='".$res['nopp']."'";
            }
            $i++;
            $i++;

            //echo $nopp."#";
        }
        $sql2="select distinct kodeorg from ".$dbname.".log_prapoht where ($cond)";// exit("Error".$sql2);//echo $sql2;
        $query2=mysql_query($sql2) or die(mysql_error());
        $res2=mysql_fetch_assoc($query2);
        $kd_induk=$res2['kodeorg'];
        $nopo="/".date('Y')."/PO/".$nopp."/".$kd_induk; 
        $ql="select `nopo` from ".$dbname.".`log_poht` where nopo like '%".$nopo."%' order by `nopo` desc limit 0,1";
        $qr=mysql_query($ql) or die(mysql_error());
        $rp=mysql_fetch_object($qr);
        $awal=substr($rp->nopo,0,3);
        $awal=intval($awal);
        $cekbln=substr($rp->nopo,4,2);
        $cekthn=substr($rp->nopo,7,4);

        if(($bln!=$cekbln)&&($thn!=$cekthn))
        {
        //echo $awal; exit();
                $awal=1;
        }
        else
        {
                $awal++;
        }
        $counter=addZero($awal,3);
        $nopo=$counter."/".$bln."/".$thn."/PO/".$nopp."/".$kd_induk;
        echo $nopo."###";
        createTabDetail($Rslt,$data);
}

if($_POST['proses']=='edit_po')
{
        $query="select * from ".$dbname.".log_podt where nopo='".$_POST['nopo']."'"; //echo $query;exit();
        $data = fetchData($query);
        createTabEditDetail($_POST['nopo'],$data);
}

if($_POST['proses']=='detail_delete')
{
          $data = $_POST;

            # Create Condition
            $where = "`nopo`='".$data['nopo']."'";
            $where .= " and `kodebarang`='".$data['kd_brg']."'";
                        $where .= " and `nopp`='".$data['nopp']."'";

            $sCekGdng="select distinct nopo from ".$dbname.".log_transaksi_vw where nopo='".$data['nopo']."' and kodebarang='".$data['kd_brg']."'";
            $qCekGdng=mysql_query($sCekGdng) or die(mysql_error($conn));
            //exit("Error:".$sCekGdng);
            $rCekGdng=mysql_num_rows($qCekGdng);
            if($rCekGdng>0)
            {
            exit("Error: PO : ".$data['nopo']." has  arrived at  warehouse, can not delete");
            }

            # Create Query
            $query = "delete from `".$dbname."`.`log_podt` where ".$where;
            //echo query;
            # Delete
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error($conn);
            }
            else
            {
                $sUpdate="update ".$dbname.".log_prapodt set create_po='0' where nopp='".$data['nopp']."' and kodebarang='".$data['kd_brg']."'";
                if(!mysql_query($sUpdate))
                {
                        echo "Gagal,".(mysql_error($conn));exit();
                }
            }
}
if($_POST['proses']=='listPp')
{
        $kode_pt=$_POST['kodept'];
        $user_id=$_POST['id_user'];
        if($user_id!=$_SESSION['standard']['userid']);
        {
                $user_id=$_SESSION['standard']['userid'];
        }
  /* 	$sql2="select * from ".$dbname.".log_sudahpo_vsrealisasi_vw where substr(nopp,16,4)='".$_SESSION['empl']['lokasitugas']."' and lokalpusat='1'
                        and status!='3' and (selisih>0 or selisih is null)";*/
        if($_SESSION['empl']['kodejabatan']=='5')
        {
            $sql2="select * from  ".$dbname.".log_sudahpo_vsrealisasi_vw  where (kodept='".$kode_pt."' and lokalpusat='1' and status!='3') and (selisih>0 or selisih is null)";
        }
        else
        {
            $sql2="select * from  ".$dbname.".log_sudahpo_vsrealisasi_vw  where (kodept='".$kode_pt."' and purchaser='".$user_id."' and lokalpusat='1' and status!='3') and (selisih>0 or selisih is null)";
        }
                //exit("Error".$sql2);
                $query2=mysql_query($sql2) or die(mysql_error());

                while($res2=mysql_fetch_object($query2))
                {
                         $no+=1;
                         $sbrg="select * from ".$dbname.".log_5masterbarang where kodebarang='".$res2->kodebarang."'";
                         $qbrg=mysql_query($sbrg) or die(mysql_error());
                         $rbrg=mysql_fetch_object($qbrg);

                         $sJmlhPsn="select sum(jumlahpesan) as jmlhPesan from ".$dbname.".log_podt where nopp='".$res2->nopp."' and kodebarang='".$res2->kodebarang."'";
                        // echo $sJmlhPsn;
                         $qJmlhPsn=mysql_query($sJmlhPsn) or die(mysql_error());
                         $rJmlhPsn=mysql_fetch_assoc($qJmlhPsn);

                         echo"
                         <tr class=rowcontent ".$show." id=tr_".$no.">
                                <td>".$no."</td>
                                <td id=nopp_".$no.">".$res2->nopp."</td>
                                <td id=kdbrg_".$no.">".$rbrg->kodebarang."</td>
                                <td>".$rbrg->namabarang."</td>
                                <td>".$rbrg->satuan."</td>
                                <td align=center>".$res2->realisasi."</td>
                                                                                <td align=center>".tanggalnormal($res2->tgl_sdt)."</td>";
                        if($res2->selisih==''){
                                echo "<td align=center>".$res2->realisasi."</td>";
                        } elseif($res2->selisih!=$res2->realisasi) {
                                $blm_pesan=$res2->selisih;
                                echo "<td align=center>".$blm_pesan."</td>";
                        }
                        if($res2->jlpesan=='')
                        {$jlpesan=0;}
                        else
                        {$jlpesan=$rJmlhPsn['jmlhPesan'];}
                        //$res2->jlpesan==''?0:$res2->jlpesan
                        echo"<td  align=center>".$jlpesan."</td>";
                        echo "<td align=center><input type=checkbox id=plh_pp_".$no." name=plh_pp_".$no." ".$test." /></td>
                         </tr>";
                }
                echo"<tr><td colspan=9 align=center>
                <button name=process id=process onclick=process()>". $_SESSION['lang']['proses']."</button>
                <button name=cancel id=cancel onclick=cancel_headher()>". $_SESSION['lang']['cancel']."</button>
                </td></tr>";


}
function createTabDetail($id,$data) {
        global $conn;
        global $dbname;

   // $table .= "<table id='ppDetailTable'>";
    # Header
    $table .= "<thead>";
    $table .= "<tr class=rowcontent>";
    $table .= "<td>".$_SESSION['lang']['nopp']."</td>";
    $table .= "<td>".$_SESSION['lang']['kodebarang']."</td>";
    $table .= "<td>".$_SESSION['lang']['namabarang']."</td>";
    $table .= "<td>".$_SESSION['lang']['jmlh_brg_blm_po']."</td>";
    $table .= "<td>".$_SESSION['lang']['jmlhPesan']."</td>";
    $table .= "<td>".$_SESSION['lang']['satuan']."</td>";
    $table .= "<td>".$_SESSION['lang']['kurs']."</td>";
    $table .= "<td>".$_SESSION['lang']['hargasatuan']."</td>";
    $table .= "<td>".$_SESSION['lang']['subtotal']."</td>";
    $table .= "<td>Action</td>";
    $table .= "</tr>";
    $table .= "</thead>";

    # Data
    $table .= "<tbody id='detailBody'>";

   // $i=0;

    #======= Display Data =======
    if($data!=array()) {
        foreach($data as $key=>$row) {
                        //get satuan dan nama barang di log_5masterbarang
            $ql="select satuan,namabarang from ".$dbname.".`log_5masterbarang` where `kodebarang`='".$row['kodebarang']."'"; //echo $ql;
            $qry=mysql_query($ql) or die(mysql_error());
            $res=mysql_fetch_assoc($qry);


                $sSat="select satuan from ".$dbname.".log_5masterbarang where kodebarang='".$row['kodebarang']."'";
                $qSat=mysql_query($sSat) or die(mysql_error());
                $rSat=mysql_fetch_assoc($qSat);
                $optSatuan="<option value=".$rSat['satuan'].">".$rSat['satuan']."</option>";
                $where=" kodebarang='".$row['kodebarang']."' and darisatuan='".$res['satuan']."'";

                $sSknv="select satuankonversi from ".$dbname.".log_5stkonversi where ".$where."";
                $qSknv=mysql_query($sSknv) or die(mysql_error());
                while($rSknv=mysql_fetch_assoc($qSknv))
                {
                        $optSatuan.="<option value=".$rSknv['satuankonversi'].">".$rSknv['satuankonversi']."</option>";
                }
                $snmkary="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
                $qnmkary=mysql_query($snmkary) or die(mysql_error());
                $rnmkary=mysql_fetch_assoc($qnmkary);


                $optTest=makeOption( $dbname,'setup_matauang','kode,kodeiso');

                //$optSatuan="<option value='".$res['satuan']."'>".$res['satuan']."</option>".$optSatuan;
                $sqjmlh="select selisih,jlpesan,realisasi from ".$dbname.".log_sudahpo_vsrealisasi_vw where nopp='".$row['nopp']."' and kodebarang='".$row['kodebarang']."'";
                //echo "warning :".$sqjmlh; exit();
                $qujmlh=mysql_query($sqjmlh) or die(mysql_error());
                $resjmlh=mysql_fetch_assoc($qujmlh);
                if($resjmlh['selisih']==''){
                $row['realisasi']=$row['realisasi'];
                } elseif($resjmlh['selisih']!=$resjmlh['realisasi']) {
                        $row['realisasi']=$resjmlh['selisih'];
                }

            $table .= "<tr id='detail_tr_".$key."' class='rowcontent'>";
            $table .= "<td id='dtNopp_".$key."'>".makeElement("rnopp_".$key."",'txt',$row['nopp'],
                array('style'=>'width:120px','disabled'=>'disabled'))."</td>";
            $table .= "<td id='dtKdbrg_".$key."'>".makeElement("rkdbrg_".$key."",'txt',$row['kodebarang'],
                array('style'=>'width:120px','disabled'=>'disabled'))."</td>";
            $table .= "<td>".makeElement("nm_brg_".$key."",'txt',$res['namabarang'],
                array('style'=>'width:120px','disabled'=>'disabled'))."</td>";
            $table .= "<td>".makeElement("realisasi_".$key."",'txt',$row['realisasi'],
                array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)','disabled'=>'disabled','class=myinputtext'))."</td>";

            $table .= "<td>".makeElement("jmlhDiminta_".$key."",'textnum','',
                array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)','onblur'=>"display_number('".$key."')",'onkeyup'=>"calculate('".$key."')"))."</td>";
         /*   $table .= "<td>".makeElement("sat_".$key."",'select',$res['satuan'],
                array('style'=>'width:70px'),$optSatuan)."</td>";*/
                                $table.="<td><select id=sat_".$key." style='width:70px'>".$optSatuan."</option></td>";
             $table .= "<td>".makeElement("kurs_".$key."",'select',$row['kurs'],
                array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)'),$optTest)."</td>";
            $table .= "<td>".makeElement("harga_satuan_".$key."",'textnum','',
                array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)',
                                'onkeyup'=>"calculate('".$key."')",'onblur'=>"periksa_isi(this)",'onblur'=>"display_number('".$key."')",'onfocus'=>"normal_number('".$key."')"))."</td>";
            $table .= "<td>".makeElement("total_".$key."",'textnum','',
                array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','disabled'=>'disabled'))."</td>";
          //  $table .= "<td><img id='detail_save_".$key."' title='Save' class=zImgBtn onclick=\"editDetail('".$key."')\" src='images/save.png'/>";
            $table .= "<td align_center><img id='detail_delete_".$key."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$key."')\" src='images/delete_32.png'/></td>";
                        $table .= "</tr>";
            $i = $key;
        }
        $i++;
    }
            $table.="<tr><td>&nbsp;</td>
                <td colspan=7 align=right>". $_SESSION['lang']['subtotal']."</td>
            <td><input type=text id=total_harga_po name=total_harga_po disabled  class=myinputtextnumber  style=width:100px /></td>
        </tr>
       <tr>
            <td >&nbsp;</td>
            <td colspan=7 align=right>".$_SESSION['lang']['diskon']."</td>
            <td><input type=text  id=angDiskon name=angDiskon class=myinputtextnumber style=width:100px onkeyup=calculate_angDiskon()  onkeypress=return angka_doang(event) onblur=\"getZero()\"  /></td>
        </tr>
                    <tr>
            <td >&nbsp;</td>
            <td colspan=7 align=right>".$_SESSION['lang']['diskon']." (%)</td>
            <td><input type=text  id=diskon name=diskon class=myinputtextnumber style=width:100px onkeyup=calculate_diskon() maxlength=3 onkeypress=return angka_doang(event) onblur=\"getZero()\" /> </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan=7 align=right>".$_SESSION['lang']['persenppn']." (%)</td>
            <td><input type=text id=ppN name=ppN  class=myinputtextnumber style=width:100px onkeyup=calculatePpn()  maxlength=2  onkeypress=return angka_doang(event) onblur=\"getZero()\" /> <input type=hidden id=ppn name=ppn class=myinputtext onkeypress=return angka_doang(event) style=width:100px /><span id=hslPPn> </span> </td>
        </tr>
         <tr>
            <td>&nbsp;</td>
            <td colspan=7 align=right>".$_SESSION['lang']['grnd_total']."</td>
            <td><input type=text id=grand_total name=grand_total disabled  class=myinputtextnumber style=width:100px  /></td>
        </tr><input type=hidden id=sub_total name=sub_total ><input type=hidden id=nilai_diskon name=nilai_diskon  />";
    $table .= "</tbody>";
  //  $table .= "</table> <br />";

    echo $table;
}

function createTabEditDetail($id,$data) {
    global $conn;
        global $dbname;

  //  $table .= "<table id='ppDetailTable'>";
    # Header
    $table .= "<thead>";
    $table .= "<tr class=rowcontent>";
    $table .= "<td>".$_SESSION['lang']['nopp']."</td>";
    $table .= "<td>".$_SESSION['lang']['kodebarang']."</td>";
    $table .= "<td>".$_SESSION['lang']['namabarang']."</td>";
    $table .= "<td>".$_SESSION['lang']['jmlh_brg_blm_po']."</td>";
        $table .= "<td>".$_SESSION['lang']['jmlhPesan']."</td>";
        $table .= "<td>".$_SESSION['lang']['satuan']."</td>";
    $table .= "<td>".$_SESSION['lang']['kurs']."</td>";
    $table .= "<td>".$_SESSION['lang']['hargasatuan']."</td>";
    $table .= "<td>".$_SESSION['lang']['subtotal']."</td>";
    $table .= "<td>Action</td>";
    $table .= "</tr>";
    $table .= "</thead>";

    # Data
    $table .= "<tbody id='detailBody'>";

   // $i=0;

    #======= Display Data =======
    if($data!=array()) {

        foreach($data as $key=>$row) {
           //get satuan dan nama barang di log_5masterbarang
            $ql="select satuan,namabarang from ".$dbname.".`log_5masterbarang` where `kodebarang`='".$row['kodebarang']."'"; //echo $ql;
            $qry=mysql_query($ql) or die(mysql_error());
            $res=mysql_fetch_assoc($qry);


                    //get satuan konversi di log_5stkonversi
                    $sSat="select satuan from ".$dbname.".log_5masterbarang where kodebarang='".$row['kodebarang']."'";
                    $qSat=mysql_query($sSat) or die(mysql_error());
                    $rSat=mysql_fetch_assoc($qSat);
                    $optSatuan="<option value=".$rSat['satuan'].">".$rSat['satuan']."</option>";
                    $where=" kodebarang='".$row['kodebarang']."' and darisatuan='".$res['satuan']."'";

                    $sSknv="select satuankonversi from ".$dbname.".log_5stkonversi where ".$where."";
                    $qSknv=mysql_query($sSknv) or die(mysql_error());
                    while($rSknv=mysql_fetch_assoc($qSknv))
                    {
                            $optSatuan.="<option value=".$rSknv['satuankonversi'].">".$rSknv['satuankonversi']."</option>";
                    }


                    $optTest=makeOption( $dbname,'setup_matauang','kode,kodeiso');


                    $sqpp="select * from  ".$dbname.".log_sudahpo_vsrealisasi_vw where nopp='".$row['nopp']."' and kodebarang='".$row['kodebarang']."'";
                    $qpp=mysql_query($sqpp) or die(mysql_error());
                    $rpp=mysql_fetch_assoc($qpp);
            $sub_tot=$row['jumlahpesan']*$row['hargasbldiskon'];

        $sjmlh="select sum(jumlahpesan) as jumlahPesan from ".$dbname.".log_podt where kodebarang='".$row['kodebarang']."' and nopp='".$row['nopp']."'";
                        //echo "warning:".$sjmlh;exit();
                    $qjmlh=mysql_query($sjmlh) or die(mysql_error());
                    $resjmlh=mysql_fetch_assoc($qjmlh);

                    $sEdit="select jumlahpesan from ".$dbname.".log_podt where nopo='".$id."' and kodebarang='".$row['kodebarang']."' and nopp='".$row['nopp']."'";
                    $qEdit=mysql_query($sEdit) or die(mysql_error());
                    $rEdit=mysql_fetch_assoc($qEdit);

                    $tmpil=($rpp['realisasi']-$resjmlh['jumlahPesan'])+$rEdit['jumlahpesan'];


            $table .= "<tr id='detail_tr_".$key."' class='rowcontent'>";
            $table .= "<td id='dtNopp_".$key."'>".makeElement("rnopp_".$key."",'txt',$row['nopp'],
                array('style'=>'width:120px','disabled'=>'disabled'))."</td>";
            $table .= "<td id='dtKdbrg_".$key."'>".makeElement("rkdbrg_".$key."",'txt',$row['kodebarang'],
                array('style'=>'width:120px','disabled'=>'disabled'))."</td>";
            $table .= "<td>".makeElement("nm_brg_".$key."",'txt',$res['namabarang'],
                array('style'=>'width:120px','disabled'=>'disabled'))."</td>";
            $table .= "<td>".makeElement("realisasi_".$key."",'txt',$tmpil,
                array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)','disabled'=>'disabled','class=myinputtext'))."</td>";
            $table .= "<td>".makeElement("jmlhDiminta_".$key."",'textnum',$row['jumlahpesan'],
                array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)','onblur'=>"display_number('".$key."')",'onkeyup'=>"calculate('".$key."')"))."</td>";
            $table.="<td><select id=sat_".$key." style='width:70px'>".$optSatuan."</option></td>";
             $table .= "<td>".makeElement("kurs_".$key."",'select',$row['matauang'],
                array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)'),$optTest)."</td>";
            $table .= "<td>".makeElement("harga_satuan_".$key."",'textnum',number_format($row['hargasbldiskon'],2,'.',','),
                array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)','onkeyup'=>"calculate('".$key."')",'onblur'=>"periksa_isi(this)",'onblur'=>"display_number('".$key."')",'onfocus'=>"normal_number('".$key."')"))."</td>";
            $table .= "<td>".makeElement("total_".$key."",'textnum',number_format($sub_tot,2,'.',','),
                array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','disabled'=>'disabled'))."</td>";
          //  $table .= "<td><img id='detail_save_".$key."' title='Save' class=zImgBtn onclick=\"editDetail('".$key."')\" src='images/save.png'/>";
            $table .= "<td align=center><img id='detail_delete_".$key."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$key."')\" src='images/delete_32.png'/></td>";
                    $table .= "</tr>";
            $i = $key;
        }
        $i++;
    }
          $table.="<tr><td>&nbsp;</td>
            <td colspan=7 align=right>". $_SESSION['lang']['subtotal']."</td>
            <td><input type=text id=total_harga_po name=total_harga_po disabled  class=myinputtextnumber  style=width:100px /></td>
        </tr>
        <tr>
            <td >&nbsp;</td>
            <td colspan=7 align=right>".$_SESSION['lang']['diskon']."</td>
            <td><input type=text  id=angDiskon name=angDiskon class=myinputtextnumber style=width:100px onkeyup=\"calculate_angDiskon()\"  onkeypress=\"return angka_doang(event)\" onblur=\"getZero()\" /></td>
        </tr>
                    <tr>
            <td >&nbsp;</td>
            <td colspan=7 align=right>".$_SESSION['lang']['diskon']." (%)</td>
            <td><input type=text  id=diskon name=diskon class=myinputtextnumber style=width:100px onkeyup=calculate_diskon() maxlength=3 onkeypress=return angka_doang(event) onblur=\"getZero()\" /> </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan=7 align=right>".$_SESSION['lang']['persenppn']." (%)</td>
            <td><input type=text id=ppN name=ppN  class=myinputtextnumber style=width:100px onkeyup=calculatePpn()  maxlength=2  onkeypress=return angka_doang(event) onblur=\"getZero()\" /> <input type=hidden id=ppn name=ppn class=myinputtext onkeypress=return angka_doang(event) style=width:100px /><span id=hslPPn> </span> </td>
        </tr>
         <tr>
            <td>&nbsp;</td>
            <td colspan=7 align=right>".$_SESSION['lang']['grnd_total']."</td>
            <td><input type=text id=grand_total name=grand_total disabled  class=myinputtextnumber style=width:100px /></td>
        </tr><input type=hidden id=sub_total name=sub_total ><input type=hidden id=nilai_diskon name=nilai_diskon  />";
    $table .= "</tbody>";
        $sPoht="select tanggalkirim,lokasipengiriman,syaratbayar,uraian,purchaser from ".$dbname.".log_poht where nopo='".$id."' ";
        $qPoht=mysql_query($sPoht) or die(mysql_error());
        $rPoht=mysql_fetch_assoc($qPoht);
        $snmkary="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$rPoht['purchaser']."'";
        $qnmkary=mysql_query($snmkary) or die(mysql_error());
        $rnmkary=mysql_fetch_assoc($qnmkary);

    echo $table."###".$rPoht['lokasipengiriman']."###".$rPoht['syaratbayar']."###".$rPoht['uraian']."###".$rnmkary['namakaryawan'];
}

if($_POST['proses']=='update_po_total'){
            $sub_total=$_POST['subtot'];
            $disc=$_POST['diskon'];
            $nilai_dis=$_POST['nildiskon'];
            $nppn=$_POST['ppn'];
            $nilai_po=$_POST['grand_total'];
            $ongkirim=$_POST['ongkirim'];
            if(($_POST['ongKirimPPn']!='')||($_POST['ongKirimPPn']!='0')){
                $ppnongkirim=$ongkirim*($_POST['ongKirimPPn']/100);
            }else{
                $ppnongkirim=0;
            }
            
            $strx="update ".$dbname.".log_poht set `subtotal`='".$sub_total."',`diskonpersen`='".$disc."',`nilaidiskon`='".$nilai_dis."',`ppn`='".$nppn."',`nilaipo`='".$nilai_po."',
                   ongkosangkutan='".$ongkirim."',`miscppn`='".$_POST['miscppn']."',`misc`='".$_POST['misc']."',`ongkirimppn`='".$ppnongkirim."',`updateby`='".$_SESSION['standard']['userid']."'
                   where nopo='".$nopo."'";
            if(!mysql_query($strx)){
                    echo "Gagal,".(mysql_error($conn));exit();
            }

    
}
?>