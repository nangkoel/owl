<?php
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');

if(isset($_GET['method']))
{
        $method=$_GET['method'];
        $statPP=$_GET['statPP'];
}
else{
        $method=$_POST['method'];
}

$nopp=$_POST['nopp'];
$jmlh_realisai=$_POST['jmlh_realisai'];
$sat_realisasi=$_POST['sat_realisasi'];
$lokal=$_POST['lokal'];
$purchaser=$_POST['purchase'];
$kd_brng=$_POST['kdbrg'];
$kdBrgBaru=$_POST['kdBrgBaru'];
$jumlahBaru=$_POST['jumlahBaru'];
$_POST['statPP']==''?$statPP=$_GET['statPP']:$statPP=$_POST['statPP'];
$_POST['userid']==''?$userid=$_GET['userid']:$userid=$_POST['userid'];
$cm_hasil=$_POST['cm_hasil'];
$spr2="select namabarang,kodebarang,satuan from ".$dbname.".log_5masterbarang order by namabarang asc";
$rep2=mysql_query($spr2) or die(mysql_error());
while($bas2=mysql_fetch_object($rep2))
{
    $rDtBrg[$bas2->kodebarang]=$bas2->namabarang;
    $nmSatuan[$bas2->kodebarang]=$bas2->satuan;
}
$kolom=$_POST['kolom'];
$comment=$_POST['comment'];
$kode_brg=$_POST['kd_brg'];
$alsnDtolak=$_POST['alsnDtolk'];
$periode=$_POST['periode'];
$kodeorg=$_POST['kodeorg'];
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$tglHrini=date("Ymd");
$blmVer=$_POST['blmVer'];
$_POST['unitIdCr']==''?$unitIdCr=$_GET['unitIdCr']:$unitIdCr=$_POST['unitIdCr'];
$_POST['klmpKbrg']==''?$klmpKbrg=$_GET['klmpKbrg']:$klmpKbrg=$_POST['klmpKbrg'];
$_POST['kdBarangCari']==''?$kdBarangCari=$_GET['kdBarangCari']:$kdBarangCari=$_POST['kdBarangCari'];
$nmBrg=$_POST['nmBrg'];
$tglSdt=tanggalsystem($_POST['tglSdt']);



switch ($method)
{
 case 'insert_detail_pp' :

    if($jmlh_realisai==0)
    {
        echo "Warning: Realization must greater than 0 ".$jmlh_realisai."";
        exit ();
    }
    else
    {
        $sql="select * from ".$dbname.".log_prapodt where nopp='".$nopp."' and status!='3'";
            /* @var $query cek data di database */
        if(mysql_query($sql))
        {
            $query=mysql_query($sql);
            while($res=mysql_fetch_assoc($query))
            {
                if($res['$purchaser']=='0000000000')
                {
                    $sql2="update ".$dbname.".log_prapodt set purchaser='".$purchaser."',lokalpusat='".$lokal."',realisasi='".$jmlh_realisai."',satuanrealisasi='".$sat_realisasi."',tglAlokasi='".$tglHrini."' where kodebarang='".$kd_brng."' and nopp='".$nopp."'";
                   // echo "warning:".$sql2;exit ();
                    $query2=mysql_query($sql2) or die(mysql_error());
                    break;
                }
                else
                {
                    $sCek="select distinct jumlahpesan from ".$dbname.".log_podt where nopp='".$nopp."' and kodebarang='".$kd_brng."'";
                    $qCek=mysql_query($sCek) or die(mysql_error());
                    $rCek=mysql_fetch_assoc($qCek);
                    if($jmlh_realisai<$rCek['jumlahpesan'])
                    {
                        exit("Error: Realization less than requested");
                    }
                    $sql2="update ".$dbname.".log_prapodt set purchaser='".$purchaser."',lokalpusat='".$lokal."',realisasi='".$jmlh_realisai."',satuanrealisasi='".$sat_realisasi."',tglAlokasi='".$tglHrini."' where kodebarang='".$kd_brng."' and nopp='".$nopp."'";
                    //echo "warning:".$sql2;exit ();
                    $query2=mysql_query($sql2) or die(mysql_error());
                    break;
                }
            }
        }
        else
        {
            echo $sql;
            echo " Gagal,".addslashes(mysql_error($conn));
            exit();
        }
    }
 break;
 case 'cari_pp':
 //echo "warning:masuk";
echo" <table class=\"sortable\" cellspacing=\"1\" border=\"0\">
         <thead>
         <tr class=rowheader>
         <td rowspan=2 align=center>No.</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['kodeorg']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['nopp']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['harga']."</td>
         <td rowspan=2 align=center>Advance Action</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['chat']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>
         <td colspan=2 align=center>".$_SESSION['lang']['diminta']."</td>
         <td colspan=2 align=center>".$_SESSION['lang']['disetujui']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['purchaser']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['lokasitugas']."</td>
         <td rowspan=2 align=center>O.std</td>

         <td rowspan=2 colspan='3' align=center>Action</td>
         </tr>
         <tr class=rowheader>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
         </tr>
         </thead>
         <tbody>";
        $limit=25;
        $page=0;
        if(isset($_POST['page']))
        {
        $page=$_POST['page'];
        if($page<0)
          $page=0;
        }
        $offset=$page*$limit;

                        $txt_search=$_POST['txtSearch'];
                        $txtCari=$_POST['txtCari'];


                if(($txt_search=='')&&($txt_tgls==''))
                {
                        $where=" ";
                }
                if($txt_search!='')
                {
                        $where.="and b.nopp LIKE  '%".$txt_search."%'   ";
                }
                if($_POST['tglCari']!='')
                {
                        $where.=" and a.tanggal LIKE '".$_POST['tglCari']."%'";
                }
                if($userid!='')
                {
                  $where.=" and purchaser='".$userid."'";
                }

                if($unitIdCr!='')
                {
                    $where.=" and b.nopp like '%".$unitIdCr."%'";
                }
                if($klmpKbrg!=''&&$kdBarangCari=='')
                {
                    $where.=" and substr(kodebarang,1,3)='".$klmpKbrg."'";
                }
                if($kdBarangCari!='')
                {
                    $where.=" and kodebarang='".$kdBarangCari."'";
                }

                    if($statPP==1)
                    {
                            $strx="SELECT  distinct a.`tanggal`, a.`close`,b.*  FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
                                WHERE a.close = '2' and b.status='0' and create_po!='0' ".$where."  ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc limit ".$offset.",".$limit." ";		


                            $sql="SELECT  distinct  a.`tanggal`,  a.`close`, b.* FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
                                WHERE a.close = '2' and b.status='0' and create_po!='0' ".$where."   ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc ";
                    }
                    else if($statPP==0)
                    {
                            $strx="SELECT distinct  a.`tanggal`,  a.`close`, b.*  FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
                                WHERE a.close = '2' and b.status='0'  and create_po='0' ".$where."   ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc  limit ".$offset.",".$limit." ";//echo $strx;

                            $sql="SELECT distinct  a.`tanggal`,  a.`close`, b.* FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp  
                                WHERE a.close = '2' and b.status='0' and create_po='0' ".$where."   ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc";

                    }
                    else if($statPP==2)
                    {

                        $strx="SELECT   distinct a.`tanggal`, a.`persetujuan1`, a.`persetujuan2`, a.`persetujuan3`, a.`persetujuan4`, a.`persetujuan5`, a.`close`, a.`hasilpersetujuan1`, a.`hasilpersetujuan2`, a.`hasilpersetujuan3`, a.`hasilpersetujuan4`, a.`hasilpersetujuan5`, a.`tglp1`, a.`tglp2`, a.`tglp3`, a.`tglp4`, a.`tglp5`,b.*  
                               FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
                               WHERE a.close = '2' and b.status='0' and b.create_po='0' ".$where."   ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc limit ".$offset.",".$limit."";
                        $sql="SELECT   distinct a.`tanggal`, a.`persetujuan1`, a.`persetujuan2`, a.`persetujuan3`, a.`persetujuan4`, a.`persetujuan5`, a.`close`, a.`hasilpersetujuan1`, a.`hasilpersetujuan2`, a.`hasilpersetujuan3`, a.`hasilpersetujuan4`, a.`hasilpersetujuan5`, a.`tglp1`, a.`tglp2`, a.`tglp3`, a.`tglp4`, a.`tglp5`,b.*  
                               FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
                               WHERE a.close = '2' and b.status='0' and b.create_po='0' ".$where."  ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc ";
                    }

                    $query=mysql_query($sql) or die(mysql_error());
                    $jsl=mysql_num_rows($query);
                    $jlhbrs= $jsl;
                    
                    //echo $strx;
    if($res=mysql_query($strx))
    {   $row=mysql_num_rows($res);
        if($row!=0)
        { 
            while($bar=mysql_fetch_object($res))
            {			
                    $koderorg=substr($bar->nopp,15,4);
                    $spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
                    $rep=mysql_query($spr) or die(mysql_error($conn));
                    $bas=mysql_fetch_object($rep);

//                    $spr2="select namabarang,satuan from ".$dbname.".log_5masterbarang where kodebarang='".$bar->kodebarang."'";
//                    $rep2=mysql_query($spr2) or die(mysql_error());
//                    $bas2=mysql_fetch_object($rep2);
                    $optSatuan="<option value=".$nmSatuan[$bar->kodebarang].">".$nmSatuan[$bar->kodebarang]."</option>";
                    $where=" kodebarang='".$bar->kodebarang."' and darisatuan='".$nmSatuan[$bar->kodebarang]."'";
                    $sSknv="select satuankonversi from ".$dbname.".log_5stkonversi where ".$where."";
                    $qSknv=mysql_query($sSknv) or die(mysql_error());
                    while($rSknv=mysql_fetch_assoc($qSknv))
                    {
                        if ($bar->satuanrealisasi==$rSknv['satuankonversi']){
                            $optSatuan.="<option value=".$rSknv['satuankonversi']." selected>".$rSknv['satuankonversi']."</option>";
                        } else {
                            $optSatuan.="<option value=".$rSknv['satuankonversi'].">".$rSknv['satuankonversi']."</option>";
                        }
                    }
                    $no+=1;
                            $sPoDet="select nopo from ".$dbname.".log_po_vw where statuspo<5 and nopp='".$bar->nopp."' and kodebarang='".$bar->kodebarang."'";
                            $qPoDet=mysql_query($sPoDet) or die(mysql_error());
                            $rCek=mysql_num_rows($qPoDet);
                            if($rCek>0)
                            {
                                    //echo"warning:A";
                                    $rPoDet=mysql_fetch_assoc($qPoDet);
                                    $sPo="select tanggal,stat_release from ".$dbname.".log_poht where nopo='".$rPoDet['nopo']."'";
                                    $qPo=mysql_query($sPo) or die(mysql_error());
                                    $rPo=mysql_fetch_assoc($qPo);


                                    $stat=$rPo['stat_release'];
                                    $nopo=$rPoDet['nopo'];

                            }
                            else
                            {	
                                    //echo"B";
                                    $tglPP=explode("-",$bar->tanggal);
                                    $date1 = $tglPP[2];
                                    $month1 = $tglPP[1];
                                    $year1 = $tglPP[0];
                                    //$tgl1 = $bar->tanggal;
                                    $tgl2 = date("Y-m-d"); 
                                    $pecah2 = explode("-", $tgl2);
                                    $date2 = $pecah2[2];
                                    $month2 = $pecah2[1];
                                    $year2 =  $pecah2[0];


                                    $stat=0;					
                            }
                            $sPoDetHrg="select distinct hargasatuan from ".$dbname.".log_podt where  kodebarang='".$bar->kodebarang."' order by nopo desc";
                            $qPoDetHrg=mysql_query($sPoDetHrg) or die(mysql_error());
                            $rCekHrg=mysql_fetch_assoc($qPoDetHrg);
                    $jd1 = GregorianToJD($month1, $date1, $year1);
                    $jd2 = GregorianToJD($month2, $date2, $year2);
                    $jmlHari=$jd2-$jd1;

                    $optPur="<option value=''></option>";
                    $klq="select karyawanid,namakaryawan from ".$dbname.".`datakaryawan` where  (bagian='PUR') and kodejabatan in (1,33,39,109) and tanggalkeluar='0000-00-00' order by namakaryawan asc ";
                    $qry=mysql_query($klq) or die(mysql_error());

                    while($rst=mysql_fetch_object($qry))
                    {
                            if($bar->purchaser==$rst->karyawanid)
                            {
                                    $optPur.="<option value=".$rst->karyawanid." selected>".$rst->namakaryawan."</option>";
                            }
                            else
                            {
                                    $optPur.="<option value=".$rst->karyawanid.">".$rst->namakaryawan."</option>";
                            }
                            //$optPur.="<option value='".$rst->karyawanid."'  '".($bar->purchaser==$rst->karyawanid?' selected':'')."'  >".$rst->namakaryawan."</option>";
                    }

                    if($bar->lokalpusat!=0)
                    {
                            $ckh="checked=checked";

                    }
                    else
                    {
                            $ckh='';
                    }
                    if($bar->purchaser!='0000000000')
                    {
                       // $stat_view="style=display:none;";
                        $read_only2="disabled=disabled";
                        $ckh.=" disabled=disabled";
                    }
                    $optLokasi='';
                    $cl=array(0=>'Head Office',1=>'Local');
                    foreach($cl as $rw =>$isi)
                    {
                            $optLokasi.="<option '".($bar->lokalpusat==$rw?'selected=selected':'')."'value='".$rw."'>".$isi."</option>";
                    }

                    //periksa chat==================================
                    $strChat="select *  from ".$dbname.".log_pp_chat where 
                              kodebarang='".$bar->kodebarang."' and nopp='".$bar->nopp."'";
                    $resChat=mysql_query($strChat);

                    if(mysql_num_rows($resChat)>0)
                    {
                            $ingChat="<img src='images/chat1.png' onclick=\"loadPPChat('".$bar->nopp."','".$bar->kodebarang."',event);\" class=resicon>";
                    }		  
                    else			
                    {
                            $ingChat="<img src='images/chat0.png'  onclick=\"loadPPChat('".$bar->nopp."','".$bar->kodebarang."',event);\" class=resicon>";
                    }

                            echo"<tr class=rowcontent id='tr_".$no."' title='".$_SESSION['lang']['tgldibutuhkan'].":".tanggalnormal($bar->tgl_sdt)."'>
                              <td>".$no."</td>
                              <td >".$koderorg."</td>
                              <td id=nopp_".$no."  onclick=\"getDataPP('".$bar->nopp."')\" style=\"cursor:pointer\">".$bar->nopp."</td>
              <td id=kd_brg_".$no.">".$bar->kodebarang."</td>
                              <td nowrap>".substr($rDtBrg[$bar->kodebarang],0,33)."</td>
                              <td align=right>".number_format($rCekHrg['hargasatuan'],2)."</td>";
                              if($stat!=1)
                              {
                              echo"<td align=\"center\">";
                               if($_SESSION['empl']['kodejabatan']=='33') 
                               {
                              echo"
                              <img src=images/application/application_add.png class=resicon title='Additional Material' onclick=\"getDataPP5('".$bar->nopp."')\" />&nbsp;";
                               }
                              echo"
                              <img src=images/application/application_edit.png class=resicon  title='Replace material code' onclick=\"searchBrg('".$no."','".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=notrans_".$no." name=notrans_".$no." value=".$bar->nopp."><input type=hidden id=kdbrg_".$no." name=kdbrg_".$no."  value=".$bar->kodebarang."><input type=hidden id=qtyawal_".$no." name=qtyawal_".$no."  value=".$bar->jumlah."><input type=hidden id=nomor name=nomor  value=".$no.">',event);\">&nbsp;
                              <img src=images/application/application_go.png class=resicon title='Submission-re verify' onclick=\"ajukanForm('".$bar->nopp."')\" /></td>";
                              }
                              else
                              {
                                      echo"<td>".$_SESSION['lang']['release_po']."</td>";
                              }
                              echo"<td>".$ingChat."</td>
                              <td nowrap>".tanggalnormal($bar->tanggal)."</td>

                             <td align=center id=jumlahawal_".$no.">".$bar->jumlah."</td>
                              <td>".$nmSatuan[$bar->kodebarang]."</td>
                              
							  <td align=right><input type=text id=realisasi_".$no." name=realisasi_".$no." onkeypress='return angka_doang(event)' class='myinputtextnumber' ".$read_only2." value=$bar->realisasi style='width:50px;' /></td>
                                                          <td><select id=satreal_".$no." id=satreal_".$no." ".$read_only2.">$optSatuan</select></td>
                              
							  
							  
							  <td><select id=purchase_name_".$no." id=purchase_name_".$no." ".$read_only2.">$optPur</select></td>";
                             if($stat==1)
                              {
                                  if($_SESSION['empl']['kodejabatan']=='33') 
                                  {
                                    echo"
                                              <td valign=middle align=center nowrap><input type=checkbox id=lokalpusat_".$no."  ".$ckh." /> Local
                                              </td>";
                                              echo"
                                                <td align=center title=\"Selisih Tanggal PP dengan Tanggal Hari ini\" >".$jmlHari."</td>
                                              <td ".$stat_view."><img src=images/save.png class=resicon  title='Save' onclick=\"AddPur('".$no."');\"></td>
                                              <td align=center><img src=images/application/application_edit.png class=resicon  title='Edit Data' onclick=\"EditPur('".$no."');\"></td><td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar->nopp."','','log_slave_print_log_pp',event);\"></td>";
                                  }
                                  else
                                  {
                                      echo "<td align=left colspan=5>".$nopo."</td>";
                                  }
                              }
                              else
                              {
                                             echo"
                                              <td valign=middle align=center nowrap><input type=checkbox id=lokalpusat_".$no."  ".$ckh." /> Local
                                              </td>";
                                              echo"
                                                <td align=center title=\"Selisih Tanggal PP dengan Tanggal Hari ini\" >".$jmlHari."</td>
                                              <td ".$stat_view."><img src=images/save.png class=resicon  title='Save' onclick=\"AddPur('".$no."');\"></td>
                                              <td align=center><img src=images/application/application_edit.png class=resicon  title='Edit Data' onclick=\"EditPur('".$no."');\"></td><td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar->nopp."','','log_slave_print_log_pp',event);\"></td>";
                             }
                             echo"</tr>";
                    }	
                echo"<tr><td colspan=14 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
           <br>
       <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
           <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
           </td>
           </tr>"; 
            }
            else
            {
                echo"<tr class=rowcontent><td colspan=16>Not Found</td></tr>";
            }


        }	
        else
        {
                echo " Gagal,".(mysql_error($conn));
        }	

         echo" </tbody>
         </table><input type='hidden' id='halPage' name='halPage' value='".$page."' />";
         break;
		 
		 
		 
		 //indz
         case 'refresh_data':

         echo" <table class=\"sortable\" cellspacing=\"1\" border=\"0\">
         <thead>
         <tr class=rowheader>
         <td rowspan=2 align=center>No.</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['kodeorg']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['nopp']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>
         <td rowspan=2 align=center>Advance Action</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['chat']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>
         <td colspan=2 align=center>".$_SESSION['lang']['diminta']."</td>
         <td colspan=2 align=center>".$_SESSION['lang']['disetujui']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['purchaser']."</td>
         <td rowspan=2 align=center>".$_SESSION['lang']['lokasitugas']."</td>
         <td rowspan=2 align=center>O.std</td>

         <td rowspan=2 colspan='3' align=center>Action</td>
         </tr>
         <tr class=rowheader>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
                
         </tr>
         </thead>
         <tbody>";
        $limit=25;
        $page=0;
        if(isset($_POST['page']))
        {
        $page=$_POST['page'];
        if($page<0)
          $page=0;
        }
        $offset=$page*$limit;
        $sql="select count(*) as jmlhrow FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
               WHERE a.close = '2' and b.status='0' and b.create_po='0' ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc";
        //echo $sql;
        $query=mysql_query($sql) or die(mysql_error());
        while($jsl=mysql_fetch_object($query)){
        $jlhbrs= $jsl->jmlhrow;
        }

        $str="SELECT  a.`tanggal`, a.`persetujuan1`, a.`persetujuan2`, a.`persetujuan3`, a.`persetujuan4`, a.`persetujuan5`, a.`close`, a.`hasilpersetujuan1`, a.`hasilpersetujuan2`, a.`hasilpersetujuan3`, a.`hasilpersetujuan4`, a.`hasilpersetujuan5`, a.`tglp1`, a.`tglp2`, a.`tglp3`, a.`tglp4`, a.`tglp5`,b.*  
              FROM ".$dbname.".log_prapodt b  LEFT JOIN  ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
              WHERE a.close = '2' and b.status='0' and b.create_po='0' ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc limit ".$offset.",".$limit."";
        if(mysql_query($str))
        {
            $res=mysql_query($str);
            $total=mysql_num_rows($res);
            echo"<tr><td colspan=16>Total Items: ".$jlhbrs."</td></tr>";
            while($bar=mysql_fetch_object($res))
            {			
                    $koderorg=substr($bar->nopp,15,4);	

                    $no+=1;
                            $optSatuan="<option value=".$nmSatuan[$bar->kodebarang].">".$nmSatuan[$bar->kodebarang]."</option>";
                            $where=" kodebarang='".$bar->kodebarang."' and darisatuan='".$nmSatuan[$bar->kodebarang]."'";
                            $sSknv="select satuankonversi from ".$dbname.".log_5stkonversi where ".$where."";
                            $qSknv=mysql_query($sSknv) or die(mysql_error());
                            while($rSknv=mysql_fetch_assoc($qSknv))
                            {
                                if ($bar->satuanrealisasi==$rSknv['satuankonversi']){
                                    $optSatuan.="<option value=".$rSknv['satuankonversi']." selected>".$rSknv['satuankonversi']."</option>";
                                } else {
                                    $optSatuan.="<option value=".$rSknv['satuankonversi'].">".$rSknv['satuankonversi']."</option>";
                                }
                            }
                            $sPoDet="select nopo from ".$dbname.".log_po_vw where statuspo<5 and nopp='".$bar->nopp."' and kodebarang='".$bar->kodebarang."'";
                            $qPoDet=mysql_query($sPoDet) or die(mysql_error());
                            $rCek=mysql_num_rows($qPoDet);
                            if($rCek>0)
                            {
                                    //echo"warning:A";
                                    $rPoDet=mysql_fetch_assoc($qPoDet);
                                    $sPo="select tanggal,stat_release from ".$dbname.".log_poht where nopo='".$rPoDet['nopo']."'";
                                    $qPo=mysql_query($sPo) or die(mysql_error());
                                    $rPo=mysql_fetch_assoc($qPo);


                                    $stat=$rPo['stat_release'];
                                    $nopo=$rPoDet['nopo'];

                            }
                            else
                            {	
                                    //echo"B";
                                    $tglPP=explode("-",$bar->tanggal);
                                    $date1 = $tglPP[2];
                                    $month1 = $tglPP[1];
                                    $year1 = $tglPP[0];
                                    //$tgl1 = $bar->tanggal;
                                    $tgl2 = date("Y-m-d"); 
                                    $pecah2 = explode("-", $tgl2);
                                    $date2 = $pecah2[2];
                                    $month2 = $pecah2[1];
                                    $year2 =  $pecah2[0];

                                    //$tglC=substr($Tgl2,8,2);	
                                    //$tgl2=$tglA.$tglB.$tglC;	

                                    $stat=0;					
                            }

                    $jd1 = GregorianToJD($month1, $date1, $year1);
                    $jd2 = GregorianToJD($month2, $date2, $year2);
                    $jmlHari=$jd2-$jd1;


                    $optPur="<option value=''></option>";
                    $klq="select karyawanid,namakaryawan from ".$dbname.".`datakaryawan` where  bagian='PUR' and kodejabatan in (1,33,39,109) and tanggalkeluar='0000-00-00' order by namakaryawan asc ";

                    $qry=mysql_query($klq) or die(mysql_error());

                    while($rst=mysql_fetch_object($qry))
                    {
                            if($bar->purchaser==$rst->karyawanid)
                            {
                                    $optPur.="<option value=".$rst->karyawanid." selected>".$rst->namakaryawan."</option>";
                            }
                            else
                            {
                                    $optPur.="<option value=".$rst->karyawanid.">".$rst->namakaryawan."</option>";
                            }
                    }

                    if($bar->lokalpusat!=0)
                    {
                            $ckh="checked=checked";

                    }
                    else
                    {
                            $ckh='';
                    }
                    if($bar->purchaser!='0000000000')
                    {
                       // $stat_view="style=display:none;";
                        $read_only2="disabled=disabled";
                        $ckh.=" disabled=disabled";
                    }
                    $optLokasi='';
                    $cl=array(0=>'Head Office',1=>'Local');
                    foreach($cl as $rw =>$isi)
                    {
                            $optLokasi.="<option '".($bar->lokalpusat==$rw?'selected=selected':'')."'value='".$rw."'>".$isi."</option>";
                    }
                    //periksa chat==================================
                    $strChat="select *  from ".$dbname.".log_pp_chat where 
                              kodebarang='".$bar->kodebarang."' and nopp='".$bar->nopp."'";
                    $resChat=mysql_query($strChat);

                    if(mysql_num_rows($resChat)>0)
                    {
                            $ingChat="<img src='images/chat1.png' onclick=\"loadPPChat('".$bar->nopp."','".$bar->kodebarang."',event);\" class=resicon>";
                    }		  
                    else			
                    {
                            $ingChat="<img src='images/chat0.png'  onclick=\"loadPPChat('".$bar->nopp."','".$bar->kodebarang."',event);\" class=resicon>";
                    }
                    echo"<tr class=rowcontent id='tr_".$no."' title='".$_SESSION['lang']['tgldibutuhkan'].":".tanggalnormal($bar->tgl_sdt)."'>
                              <td>".$no."</td>
                              <td >".$koderorg."</td>
                              <td id=nopp_".$no." onclick=\"getDataPP('".$bar->nopp."')\" style=\"cursor:pointer\">".$bar->nopp."</td>
                              <td id=kd_brg_".$no.">".$bar->kodebarang."</td>
                              <td nowrap>".substr($rDtBrg[$bar->kodebarang],0,33)."</td>";
                              if($stat!=1)
                              {
                               echo"<td align=\"center\">";
                               if($_SESSION['empl']['kodejabatan']=='33') 
                               {
                              echo"
                              <img src=images/application/application_add.png class=resicon title='Additional material' onclick=\"getDataPP5('".$bar->nopp."')\" />&nbsp;";
                               }
                              echo"
                              <img src=images/application/application_edit.png class=resicon  title='Change material code' onclick=\"searchBrg('".$no."','".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=notrans_".$no." name=notrans_".$no." value=".$bar->nopp."><input type=hidden id=kdbrg_".$no." name=kdbrg_".$no."  value=".$bar->kodebarang."><input type=hidden id=qtyawal_".$no." name=qtyawal_".$no."  value=".$bar->jumlah."><input type=hidden id=nomor name=nomor  value=".$no.">',event);\">&nbsp;
                              <img src=images/application/application_go.png class=resicon title='Submission - re Verify' onclick=\"ajukanForm('".$bar->nopp."')\" /></td>";
                              }
                              else
                              {
                                      echo"<td>".$_SESSION['lang']['release_po']."</td>";
                              }
                              echo"
                              <td>".$ingChat."</td>
                              <td nowrap>".tanggalnormal($bar->tanggal)."</td>

                              <td align=center id=jumlahawal_".$no.">".$bar->jumlah."</td>
                              <td>".$nmSatuan[$bar->kodebarang]."</td>
                              
							  <td align=right>
							  	<input type=text id=realisasi_".$no." name=realisasi_".$no." onkeypress='return angka_doang(event)' class='myinputtextnumber' ".$read_only2." value=$bar->realisasi style='width:50px;' />
                                                                <td><select id=satreal_".$no." id=satreal_".$no." ".$read_only2.">$optSatuan</select></td>
							  </td>
                              
							  
							  <td><select id=purchase_name_".$no." id=purchase_name_".$no." ".$read_only2.">$optPur</select></td>";
                              if($stat==1)
                              {
                                  if($_SESSION['empl']['kodejabatan']=='33') 
                                  {
                                    echo"
                                              <td align=center nowrap><input type=checkbox id=lokalpusat_".$no."  ".$ckh." /> Local
                                              </td>";
                                              echo"
                                                <td align=center title=\"Selisih Tanggal PP dengan Tanggal Hari ini\" >".$jmlHari."</td>
                                              <td ".$stat_view."><img src=images/save.png class=resicon  title='Save' onclick=\"AddPur('".$no."');\"></td>
                                              <td align=center><img src=images/application/application_edit.png class=resicon  title='Edit Data' onclick=\"EditPur('".$no."');\"></td><td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar->nopp."','','log_slave_print_log_pp',event);\"></td>";
                                  }
                                  else
                                  {
                                      echo "<td align=left colspan=5>".$nopo."</td>";
                                  }
                              }
                              else 
                              {
                                              echo"
                                              <td align=center nowrap><input type=checkbox id=lokalpusat_".$no."  ".$ckh." /> Local</td>
                                                <td align=center title=\"Selisih Tanggal PP dengan Tanggal Hari ini\" >".$jmlHari."</td>
                                              <td ".$stat_view."><img src=images/save.png class=resicon  title='Save' onclick=\"AddPur('".$no."');\"></td>
                                              <td align=center><img src=images/application/application_edit.png class=resicon  title='Edit Data' onclick=\"EditPur('".$no."');\"></td><td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar->nopp."','','log_slave_print_log_pp',event);\"></td>";
                             }
                             echo"</tr>";
                    }	 	  
                    echo"<tr><td colspan=17 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".$jlhbrs."
           <br>
       <button class=mybutton onclick=cariData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
           <button class=mybutton onclick=cariData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
           </td>
           </tr>"; 	
        }	
        else
        {
                echo " Gagal,".(mysql_error($conn));
        }	

         echo" </tbody>
         </table><input type='hidden' id='halPage' name='halPage' value='".$page."' />";
         break;
         case'cariBarang':
        $txtfind=$_POST['txtfind'];
                    $pil=$_POST['pil'];
        $str="select * from ".$dbname.".log_5masterbarang where namabarang like '%".$txtfind."%' or kodebarang like '%".$txtfind."%' ";
        if(mysql_query($str))
        {
                    $res=mysql_query($str);
                echo"
                <fieldset style=float:left;clear:both;>
                <legend>Result</legend>
                <div style=\"overflow:auto; height:280px;\" >
                <table class=data cellspacing=1 cellpadding=2  border=0>
                <thead>
                <tr class=rowheader>
                <td class=firsttd>
                No.
                </td>
                <td>".$_SESSION['lang']['kodebarang']."</td>
                <td>".$_SESSION['lang']['namabarang']."</td>
                <td>".$_SESSION['lang']['satuan']."</td>
                <td>".$_SESSION['lang']['saldo']."</td>
                </tr>
                </thead>
                <tbody>";
                $no=0;	 
                while($bar=mysql_fetch_object($res))
                {
                $no+=1;
                //===========================pengambilan saldo
                //ambil saldo barang
                $saldoqty=0;
                $str1="select sum(saldoqty) as saldoqty from ".$dbname.".log_5masterbarangdt where kodebarang='".$bar->kodebarang."'
                and kodeorg='".$_SESSION['empl']['kodeorganisasi']."'";
                $res1=mysql_query($str1);
                while($bar1=mysql_fetch_object($res1))
                {
                $saldoqty=$bar1->saldoqty;
                }

                //ambil pemasukan barang yang belum di posting
                $qtynotpostedin=0;
                $str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
                b on a.notransaksi=b.notransaksi where kodept='".$_SESSION['empl']['kodeorganisasi']."' and b.kodebarang='".$bar->kodebarang."' 
                and a.tipetransaksi<5
                and a.post=0
                group by kodebarang";

                $res2=mysql_query($str2);
                while($bar2=mysql_fetch_object($res2))
                {
                $qtynotpostedin=$bar2->jumlah;
                }
                if($qtynotpostedin=='')
                $qtynotpostedin=0;


                //ambil pengeluaran barang yang belum di posting
                $qtynotposted=0;
                $str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
                b on a.notransaksi=b.notransaksi where kodept='".$_SESSION['empl']['kodeorganisasi']."' and b.kodebarang='".$bar->kodebarang."' 
                and a.tipetransaksi>4
                and a.post=0
                group by kodebarang";

                $res2=mysql_query($str2);
                while($bar2=mysql_fetch_object($res2))
                {
                $qtynotposted=$bar2->jumlah;
                }
                if($qtynotposted=='')
                $qtynotposted=0;

                $saldoqty=($saldoqty+$qtynotpostedin)-$qtynotposted;
                //============================================		

                if($bar->inactive==1)
                {
                echo"<tr class=rowcontent style='cursor:pointer;'  title='Inactive' >";
                $bar->namabarang=$bar->namabarang. " [Inactive]";
                }
                else
                {	
                    $clikData="\"setBrg('".$bar->kodebarang."','".$bar->satuan."')\"";
                    if($pil==2)
                    {
                            $clikData="\"setBrg2('".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."')\"";
                    }
                echo"<tr class=rowcontent style='cursor:pointer;' onclick=".$clikData." title='Click' >";
                }   
                echo" <td class=firsttd>".$no."</td>
                <td>".$bar->kodebarang."</td>
                <td>".$bar->namabarang."</td>
                <td>".$bar->satuan."</td>
                <td align=right>".number_format($saldoqty,2,',','.')."</td>
                </tr>";
                }	 
                echo "</tbody>
                <tfoot>
                </tfoot>
                </table></div></fieldset>";
                }	
                else
                {
                echo " Gagal,".addslashes(mysql_error($conn));
                }	
         break;
         case'updateDtbarang':
         $sUpdate="update ".$dbname.".log_prapodt set kodebarang='".$kdBrgBaru."',jumlah=".$jumlahBaru.",realisasi=".$jumlahBaru." where nopp='".$nopp."' and kodebarang='".$kd_brng."'";
        
         if(mysql_query($sUpdate))
         {
                $sCek="select kodebarang from ".$dbname.".log_podt where nopp='".$nopp."' and kodebarang='".$kd_brng."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_num_rows($qCek);
                if($rCek>0)
                {
                        $sUpdPo="update ".$dbname.".log_podt set kodebarang='".$kdBrgBaru."' where nopp='".$nopp."' and kodebarang='".$kd_brng."'";
                        mysql_query($sUpdPo) or die(mysql_error($conn));
                }

         }
         else
         {
                 echo " Gagal,".addslashes(mysql_error($conn));
         }

         break;
         case 'excelData':

                $stream.=" <table border=\"1\">
         <thead>
         <tr>
         <td bgcolor=#DEDEDE align=center valign=middle>No.</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['kodeorg']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['nopp']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['kodebarang']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['namabarang']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['harga']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['tanggal']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['tgldibutuhkan']."</td>    
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['jmlhDiminta']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['jmlh_disetujui']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['purchaser']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['lokasitugas']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['jmlh_hari_outstanding']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['nopo']."</td>
         </tr>
         </thead>
         <tbody>";
                        $txt_search=$_GET['txtSearch'];
                        $txtCari=$_GET['txtCari'];
                        $txt_tgl=tanggalsystem($_GET['tglCari']);

                if(($txt_search=='')&&($txt_tgls==''))
                {
                        $where=" ";
                }
                if($txt_search!='')
                {
                        $where.="and b.nopp LIKE  '".$txt_search."%'   ";
                }
                if($_GET['tglCari']!='')
                {
                        $where.=" and a.tanggal LIKE '".$_GET['tglCari']."%'";
                }
                if($userid!='')
                {
                  $where.=" and purchaser='".$userid."'";
                }

                if($unitIdCr!='')
                {
                    $where.=" and b.nopp like '%".$unitIdCr."%'";
                }
                if($klmpKbrg!=''&&$kdBarangCari=='')
                {
                    $where.=" and substr(kodebarang,1,3)='".$klmpKbrg."'";
                }
                if($kdBarangCari!='')
                {
                    $where.=" and kodebarang='".$kdBarangCari."'";
                }
                if($statPP==0)
                {
                    $where.=" and purchaser='0000000000'";
                }
                //
                    if($statPP==1)
                    {
                            $strx="SELECT  distinct a.`tanggal`, a.`persetujuan1`, a.`persetujuan2`, a.`persetujuan3`, a.`persetujuan4`, a.`persetujuan5`, a.`close`, a.`hasilpersetujuan1`, a.`hasilpersetujuan2`, a.`hasilpersetujuan3`, a.`hasilpersetujuan4`, a.`hasilpersetujuan5`, a.`tglp1`, a.`tglp2`, a.`tglp3`, a.`tglp4`, a.`tglp5`,b.*  
                                   FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
                                   WHERE a.close = '2' and b.status='0' and create_po!='0' ".$where."  ORDER BY a.nopp asc ";

                    }
                    else if($statPP==0)
                    {
                            $strx="SELECT  distinct a.`tanggal`, a.`persetujuan1`, a.`persetujuan2`, a.`persetujuan3`, a.`persetujuan4`, a.`persetujuan5`, a.`close`, a.`hasilpersetujuan1`, a.`hasilpersetujuan2`, a.`hasilpersetujuan3`, a.`hasilpersetujuan4`, a.`hasilpersetujuan5`, a.`tglp1`, a.`tglp2`, a.`tglp3`, a.`tglp4`, a.`tglp5`,b.*   
                                   FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
                                   WHERE a.close = '2' and b.status='0'  and create_po='0'  ".$where."  ORDER BY a.nopp asc  ";

                    }
                    else if($statPP==2)
                    {
                        $strx="SELECT   distinct a.`tanggal`, a.`persetujuan1`, a.`persetujuan2`, a.`persetujuan3`, a.`persetujuan4`, a.`persetujuan5`, a.`close`, a.`hasilpersetujuan1`, a.`hasilpersetujuan2`, a.`hasilpersetujuan3`, a.`hasilpersetujuan4`, a.`hasilpersetujuan5`, a.`tglp1`, a.`tglp2`, a.`tglp3`, a.`tglp4`, a.`tglp5`,b.*  
                               FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
                               WHERE a.close = '2' and b.status='0' and b.create_po='0'  ".$where."   ORDER BY a.tanggal ";
                    }


            if(mysql_query($strx))
            {
                    $res=mysql_query($strx);
            while($bar=mysql_fetch_object($res))
            {			
                    $koderorg=substr($bar->nopp,15,4);
                    $spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
                    $rep=mysql_query($spr) or die(mysql_error($conn));
                    $bas=mysql_fetch_object($rep);

                    $spr2="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar->kodebarang."'";
                    $rep2=mysql_query($spr2) or die(mysql_error());
                    $bas2=mysql_fetch_object($rep2);
                    $no+=1;
                            $sPoDet="select distinct nopo from ".$dbname.".log_podt where nopp='".$bar->nopp."' and kodebarang='".$bar->kodebarang."'";
                            $qPoDet=mysql_query($sPoDet) or die(mysql_error());
                            $rCek=mysql_num_rows($qPoDet);
                            if($rCek>0)
                            {
                                    //echo"warning:A";
                                    $sPoDet="select distinct nopo from ".$dbname.".log_podt where nopp='".$bar->nopp."' and kodebarang='".$bar->kodebarang."'";
                                    $qPoDet=mysql_query($sPoDet) or die(mysql_error());
                                    $rPoDet=mysql_fetch_assoc($qPoDet);
                                    $sPo="select tanggal from ".$dbname.".log_poht where nopo='".$rPoDet['nopo']."'";
                                    $qPo=mysql_query($sPo) or die(mysql_error());
                                    $rPo=mysql_fetch_assoc($qPo);

                                    $tglA=substr($rPo['tanggal'],0,4);
                                    $tglB=substr($rPo['tanggal'],5,2);
                                    $tglC=substr($rPo['tanggal'],8,2);
                                    $tgl2=$tglA.$tglB.$tglC;

                                    $tGl1=substr($bar->tanggal,0,4);
                                    $tGl2=substr($bar->tanggal,5,2);
                                    $tGl3=substr($bar->tanggal,8,2);
                                    $tgl2=$tglA.$tglB.$tglC;
                                    $tgl1 =$tGl1.$tGl2.$tGl3;

                                    $stat=1;
                                    $nopo=$rPoDet['nopo'];

                            }
                            else
                            {	
                                    //echo"B";
                                    $tGl1=substr($bar->tanggal,0,4);
                                    $tGl2=substr($bar->tanggal,5,2);
                                    $tGl3=substr($bar->tanggal,8,2);
                                    //$tgl1 = $bar->tanggal;
                                    $tgl1 =$tGl1.$tGl2.$tGl3;
                                    $Tgl2 = date('Y-m-d');			
                                    $tglA=substr($Tgl2,0,4);
                                    $tglB=substr($Tgl2,5,2);
                                    $tglC=substr($Tgl2,8,2);	
                                    $tgl2=$tglA.$tglB.$tglC;	

                                    $stat=0;					
                            }

                    $starttime=strtotime($tgl1);//time();// tanggal sekarang
                    $endtime=strtotime($tgl2);//tanggal pembuatan dokumen
                    $timediffSecond = abs($endtime-$starttime);
                    $base_year = min(date("Y", $tGl1), date("Y", $tglA));
                    $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                    $jmlHari=date("j", $diff) - 1;
                    $klq="select namakaryawan from ".$dbname.".`datakaryawan` where  karyawanid='".$bar->purchaser."'";
                    $qry=mysql_query($klq) or die(mysql_error());
                    $rNm=mysql_fetch_assoc($qry);
                    $bar->lokalpusat!=0?$chk="Local":$chk="Head Office";
                    $sPoDetHrg="select distinct hargasatuan from ".$dbname.".log_podt where  kodebarang='".$bar->kodebarang."' order by nopo desc";
                            $qPoDetHrg=mysql_query($sPoDetHrg) or die(mysql_error());
                            $rCekHrg=mysql_fetch_assoc($qPoDetHrg);
                    $stream.="<tr>
                              <td>".$no."</td>
                              <td>".$koderorg."</td>
                              <td>".$bar->nopp."</td>
                              <td>".$bar->kodebarang."</td>
                              <td>".substr($bas2->namabarang,0,33)."</td>
                              <td align=right>".number_format($rCekHrg['hargasatuan'],2)."</td>
                              <td>".tanggalnormal($bar->tanggal)."</td>
                              <td>".tanggalnormal($bar->tgl_sdt)."</td>    
                              <td align=right>".number_format($bar->jumlah,2)."</td>
                              <td align=right>".number_format($bar->realisasi,2)."</td>
                              <td>".$rNm['namakaryawan']."</td> 
                              <td>".$chk."</td> <td>".$jmlHari."</td>";
                             $stream.= "<td align=center>". $nopo."</td>";


                            $stream.="</tr>";
                    }	 	  

    }	
    else
    {
            echo " Gagal,".(mysql_error($conn));
    }	

     $stream.=" </tbody>";
     //=================================================

    $stream.="</table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
 $dte=date("His");
  $nop_="ListVerivikasiBarang_".$dte;
  $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $stream);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";

         break;
         case'getForm':
         $kolom=0;
         $sCek="select * from ".$dbname.".log_prapoht where nopp='".$_POST['nopp']."'";
        // echo $sCek;
         $qCek=mysql_query($sCek) or die(mysql_error());
         $rCek=mysql_fetch_assoc($qCek);
         for($a=1;$a<6;$a++)
         {
                 if($rCek['persetujuan'.$a]!='')
                 {
                         $kolom+=1;
                 }
                 else
                 {
                         $kolom+=1;
                         break;
                 }
         }
        // echo $kolom;
         echo"<br />	
         <input type=\"hidden\" id='kolom' name='kolom' value=".$kolom." />
         <fieldset><legend>Approval</legend>
            <div id=test style=display:block>
            <fieldset>
            <legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$_POST['nopp']."  /></legend>
            <table cellspacing=1 border=0>
            <tr>
            <td colspan=3>
             Submit to the next verification :</td>
            </tr>
            <td>".$_SESSION['lang']['namakaryawan']."</td>
            <td>:</td>
            <td valign=top>";

            $optPur='';
            //$klq="select karyawanid,namakaryawan,lokasitugas,bagian from ".$dbname.".`datakaryawan` where karyawanid!='".$_SESSION['standard']['userid']."' and tipekaryawan='0' and lokasitugas!='' order by namakaryawan asc";
            if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
                $klq="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas,b.bagian from ".$dbname.".setup_approval a 
                      left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                      a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and a.kodeunit like '%HO'  order by b.namakaryawan asc";
            } else {
                if ($_SESSION['empl']['regional']=='KALIMANTAN'){
                    if ($_SESSION['empl']['kodegolongan']>='8'){
                        $klq="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas,b.bagian from ".$dbname.".setup_approval a 
                              left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                              a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and b.lokasitugas like '%HO' and b.kodegolongan>='8' order by b.namakaryawan asc";
                    } else {
                        $klq="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas,b.bagian from ".$dbname.".setup_approval a 
                              left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                              a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and a.kodeunit like '%HO' 
                              and b.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by b.namakaryawan asc";
                    }
                } else {
                    if ($_SESSION['empl']['kodegolongan']>='8'){
                        $klq="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas,b.bagian from ".$dbname.".setup_approval a 
                              left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                              a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and a.kodeunit like '%HO' and b.kodegolongan>='8' 
                              and b.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by b.namakaryawan asc";
                    } else {
                        $klq="select distinct a.karyawanid,b.namakaryawan,b.lokasitugas,b.bagian from ".$dbname.".setup_approval a 
                              left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where 
                              a.karyawanid!='".$_SESSION['standard']['userid']."' and a.applikasi='PP2' and a.kodeunit like '%HO' 
                              and b.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by b.namakaryawan asc";
                    }
                }
            }


            $qry=mysql_query($klq) or die(mysql_error());
            while($rst=mysql_fetch_object($qry))
            {
                    $sBag="select nama from ".$dbname.".sdm_5departemen where kode='".$rst->bagian."'";
                    $qBag=mysql_query($sBag) or die(mysql_error());
                    $rBag=mysql_fetch_assoc($qBag);
                    $optPur.="<option value='".$rst->karyawanid."'>".$rst->namakaryawan." [".$rst->lokasitugas."]  [".$rBag['nama']."]</option>";
            }
            echo"

                    <select id=user_id name=user_id  style=\"width:150px;\">
                            ".$optPur." 
                    </select></td></tr>
                    <tr>
                    <tr>
                    <td>".$_SESSION['lang']['note']."</td>
                    <td>:</td>
                    <td><input type=text id=comment_fr name=comment_fr class=myinputtext onClick='return tanpa_kutip(event)'  style=\"width:150px;\" /></td>
                    </tr>
                    <td colspan=3 align=center>
                    <button class=mybutton onclick=forwardPP() title=\"Submit to the next verification\" id=Ajukan >".$_SESSION['lang']['diajukan']."</button>

                    <button class=mybutton onclick=cancel() title=\"Close this form\">".$_SESSION['lang']['cancel']."</button>
                    </td></tr></table><br /> 
                    <input type=hidden name=method id=method  /> 
                    <input type=hidden name=user_id id=user_id value=".$_SESSION['standard']['userid']." />
                    <input type=hidden name=nopp id=nopp value=".$_POST['nopp']."  /> 
                    </fieldset></div><br />
                    </fieldset><br />
                    ";

                    echo"<fieldset>
                    <legend>Rejection</legend>
                    <div id=rejected_form>
                    <fieldset>
                    <legend><input type=text readonly=readonly name=dnopp id=dnopp value=".$_POST['nopp']."  /></legend>
                    <table cellspacing=1 border=0>
                    <tr>
                    <td colspan=3>
                    Rejection Form </td></tr>
                    <tr>
                    <td>".$_SESSION['lang']['note']."</td>
                    <td>:</td>
                    <td><input type=text id=cmnt_tolak name=cmnt_tolak class=myinputtext onClick=\"return tanpa_kutip(event)\" /></td>
                    </tr>
                    <tr><td colspan=3 align=center>
                    <button class=mybutton onclick=\"rejected_pp_proses()\" >".$_SESSION['lang']['ditolak']."</button>
                    <button class=mybutton onclick=\"rejected_some_proses('".$_POST['nopp']."','".$kolom."')\" >".$_SESSION['lang']['ditolak_some']."</button>
                    <button class=mybutton onclick=cancel() title=\"Close this form\">".$_SESSION['lang']['cancel']."</button>
                    </td></tr></table>
                    </fieldset>
                    </div>
                    </fieldset>";
         break;
         case'insertFwrdpp':
         $sCek="select * from ".$dbname.".log_prapoht where nopp='".$nopp."'";
         $qCek=mysql_query($sCek) or die(mysql_error());
         $rCek=mysql_fetch_assoc($qCek);
         for($i=1;$i<6;$i++)
         {
                 if($rCek['persetujuan'.$i]=="")
                 {
                         $ar=$i;
                         break;
                 }
         }
         if($ar==6)
         {
                 echo"warning: No more submission";
                 exit();
         }
         else
         {
         $thisDate=date("d-m-Y");
         $pls=$ar+1;
        /*  $sUp="update ".$dbname.".log_prapoht set persetujuan".$ar."='".$_SESSION['standard']['userid']."',tglp".$ar."='".tanggalsystem($thisDate)."',close='1',persetujuan".$pls."='".$userid."' where nopp='".$nopp."'"; */
		 $sUp="update ".$dbname.".log_prapoht set persetujuan".$ar."='".$userid."',komentar".$ar."='".$_POST['cm_hasil']."',close='1' where nopp='".$nopp."'";
         if(mysql_query($sUp))
         echo"";
         else
         echo " Gagal,".(mysql_error($conn));
         }
         break;
         case'rejected_pp_ex':
         if($kolom<6)
         {
                 $tglSkrng=date("Y-m-d");
                 $sUpdatePP="update ".$dbname.".log_prapoht set komentar".$kolom."='".$comment."',hasilpersetujuan".$kolom."='3',tglp".$kolom."='".$tglSkrng."',persetujuan".$kolom."='".$_SESSION['standard']['userid']."' where nopp='".$nopp."'";
                 if(mysql_query($sUpdatePP)){
						 $sData="select distinct kodebarang,create_po from ".$dbname.".log_prapodt where nopp='".$nopp."'";
						 $qData=mysql_query($sData) or die(mysql_error($conn));
						 while($rData=mysql_fetch_assoc($qData)){
							 if($rData['create_po']==0){
									$sql3="update ".$dbname.".log_prapodt set status='3',ditolakoleh='".$_SESSION['standard']['userid']."' where nopp='".$nopp."' and kodebarang='".$rData['kodebarang']."'";
								$query3=mysql_query($sql3) or die(mysql_error());
							 }
						 }
                         
                 }
                 else
                 {
                         echo " Gagal,".addslashes(mysql_error($conn));
                         echo $sUpdatePP;exit();
                 }
         }
         else
         {
                 echo"warning: Please contact administrator";
                 exit();
         }
         break;
        case 'get_form_rejected_some':
        $sql="select * from ".$dbname.".log_prapodt where `nopp`='".$nopp."'";

        $query=mysql_query($sql) or die(mysql_error());

        echo"
        <fieldset><input type=hidden id=kolom value=".$kolom.">
        <legend><input type=text id=rnopp name=rnopp value=".$nopp." readonly=readonly /></legend>
        <div style=overflow:auto;width=850px;height:350px;>
        <table cellspacing=1 border=0 class=sortable>
        <thead class=rowheader>
        <tr>
        <td>No.</td>
        <td>".$_SESSION['lang']['kodebarang']."</td>
        <td>".$_SESSION['lang']['namabarang']."</td>
        <td>".$_SESSION['lang']['satuan']."</td>
        <td>".$_SESSION['lang']['kodeanggaran']."</td>
        <td>".$_SESSION['lang']['jmlhDiminta']."</td>
        <td>".$_SESSION['lang']['tanggalSdt']."</td>
        <td>".$_SESSION['lang']['keterangan']."</td>
        <td>".$_SESSION['lang']['alasanDtolak']."</td>
        <td colspan=2>Action</td>
        </tr>
        </thead>

        <tbody id=reject_some class=rowcontent>

        ";
        while($res=mysql_fetch_assoc($query)){
        $no+=1;
        $sql2="select namabarang,satuan from ".$dbname.".log_5masterbarang where `kodebarang`='".$res['kodebarang']."'";
        $query2=mysql_query($sql2) or die(mysql_error());
        $res2=mysql_fetch_assoc($query2);
        if($res['status']=='3'){
                $dis="disabled=disabled";
        }
        else
        {
                $dis="";
        }
        echo"<tr>
        <td>".$no."</td>
        <td id=kdBrg_".$no.">".$res['kodebarang']."</td>
        <td>".$res2['namabarang']."</td>
        <td>".$res2['satuan']."</td>
        <td id=kd_angrn_".$no.">".$res['kd_anggran']."</td>
        <td id=jmlh_".$no.">".$res['jumlah']."</td>
        <td id=tgl_".$no.">".$res['tgl_sdt']."</td>
        <td id=ket_".$no.">".$res['keterangan']."</td>
        <td><input type=text id=alsnDtolak_".$no." name=alsnDtolak_".$no." class=myinputtext style=width:100px /></td>
        <td><button class=mybutton onclick=\"rejected_some('".$nopp."','".$no."','".$kolom."')\" ".$dis." >".$_SESSION['lang']['ditolak']."</button></td>
        </tr>";
        }
        echo"</tbody><tfoot><tr><td colspan=10 align=center><button class=mybutton onclick=\"rejected_some_done()\" >".$_SESSION['lang']['done']."</button></td></tr></tfoot></table></div></fieldset><input type=hidden id=user_id name=user_id value='".$_SESSION['standard']['userid']."'>";
        break;

        case 'rejected_some_done':
        $user_id=$_POST['user_id'];
        for($i=1;$i<6;$i++)
        {
                $sql="select * from ".$dbname.".log_prapoht where nopp='".$_POST['nopp']."' and persetujuan".$i."='".$user_id."' ";
                if($query2=mysql_query($sql2))
                {
                        while($res=mysql_fetch_assoc($query))
                        {
                                for($i=1;$i<6;$i++)
                                {	
                                        if($res['hasilpersetujuan'.$i]=='')
                                        {
                                                $sql2="update ".$dbname.".log_prapoht set hasilpersetujuan".$i."='1'";
                                        }
                                }
                        }

                        break;
                }
                else
                {
                        echo $sql2;exit();
                        echo " Gagal,".addslashes(mysql_error($conn));
                }
        }
        break;
        case 'rejected_some_input' :
                $where=" nopp='".$nopp."' and kodebarang='".$kode_brg."'";
                $sCek="select status,create_po from ".$dbname.".log_prapodt where nopp='".$nopp."' and status='0' ";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_num_rows($qCek);
                if($rCek>1){
                        $sql="select * from ".$dbname.".log_sudahpo_vsrealisasi_vw where".$where; 
                        $query=mysql_query($sql) or die(mysql_error());
                        $res=mysql_fetch_assoc($query);
                        if(($res['status']==0)&&($res['ditolakoleh']==0000000000)){
                                if($res['create_po']==0 or $res['selisih']>0){
                                        $sql2="update ".$dbname.".log_prapodt set status='3',ditolakoleh='".$_SESSION['standard']['userid']."',alasanstatus='".$alsnDtolak."' where".$where;
                                        if(!mysql_query($sql2)){
                                                        echo $sql2;exit();
                                                        echo " Gagal,".addslashes(mysql_error($conn));
                                        }
                                } 
                        }
                        else
                        {
                                echo"warning: Already exist";
                                exit();
                        }
                }
                else
                {
                        echo"warning: this only has one item";
                        exit();
                }
        break;
        case'getSummary':
       if($periode=='')
       {
          $periode=date("Y-m");
       }
       $tab.="<link rel=stylesheet type=text/css href=style/generic.css>
        <script language=javascript1.2 src='js/generic.js'></script>
        <script language=javascript1.2 src='js/log_verivikasi.js'></script>";
       $tab.="<br /><fieldset><legend>Summarry Purchaser</legend>";
       $tab.="Till month : <span id=tglPeriode>".$periode."</span><br /><br />";
       $optper="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
       $sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_prapoht  order by tanggal desc";
       $qPeriode=mysql_query($sPeriode) or die(mysql_error());
       while($rPeriode=mysql_fetch_assoc($qPeriode))
       {
           if($rPeriode['periode']!='0000-00')
           {
               $thn=explode("-", $rPeriode['periode']);
               if($thn[1]=='12')
               {
               $optper.="<option value='".substr($rPeriode['periode'],0,4)."' ".(substr($rPeriode['periode'],0,4)==$periode?'selected':'').">".substr($rPeriode['periode'],0,4)."</option>";
               }
               $optper.="<option value='".$rPeriode['periode']."' ".($rPeriode['periode']==$periode?'selected':'').">".substr($rPeriode['periode'],5,2)."-".substr($rPeriode['periode'],0,4)."</option>";
           }
       }
       $tab.="".$_SESSION['lang']['periode'].":<select id=period name=period onchange=getSumData()>".$optper."</select><br />";

       $tab.="<table border=0 cellspacing=1 cellpading=0><thead>
            <tr class=rowheader>
            <td rowspan='2'  align=center>No.</td>
            <td rowspan='2' align=center>".$_SESSION['lang']['purchaser']."</td>";
            $sPt="select kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
            $qData=fetchData($sPt);
           $jumlahData=count($qData);
            $a=1;
           foreach($qData as $brsData => $rData)
           {
               $kdOrg[]=$rData;
               $tab.="<td colspan=4 align=center>".$rData['kodeorganisasi']."</td>";
               $a++;
           }
           $tab.="<tr class=rowheader>";
           for($acd=0;$acd<$jumlahData;$acd++)
           {
               $tab.="<td align=center'>Tot. Item</td><td align=center bgcolor='green'>On PO</td><td align=center bgcolor='red'>Blm PO</td><td align=center>% Out</td>";
           }
           $tab.="</tr></thead><tbody id=isiContain>";
           $sPur="select karyawanid,namakaryawan from ".$dbname.".datakaryawan 
               where bagian='PUR'  and kodejabatan!='33' and (tanggalkeluar>'".date('Y-m-d')."' or tanggalkeluar='0000-00-00')  order by namakaryawan asc";

           $qPur=fetchData($sPur);
           foreach($qPur as $brsKary)
           {
               foreach($kdOrg as $brsData3 => $rData3)
                {
                   //get data blm po
                  $sDt=" SELECT count(kodebarang) as jmlhPo,kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ".$dbname.".log_prapoht a LEFT JOIN ".$dbname.".log_prapodt b ON a.nopp = b.nopp
                         WHERE  b.status='0' and kodeorg='".$rData3['kodeorganisasi']."' and substr(tanggal,1,7) like '%".$periode."%' and b.purchaser='".$brsKary['karyawanid']."' ";

                $qDt=mysql_query($sDt) or die(mysql_error());
                $rDt=mysql_fetch_assoc($qDt);

                //get data sdh po
                $sDt2=" SELECT a.kodeorg,purchaser,substr(a.tanggal,1,7) as periode FROM ".$dbname.".log_prapoht a LEFT JOIN ".$dbname.".log_prapodt b ON a.nopp = b.nopp
                        LEFT JOIN ".$dbname.".log_po_vw c ON b.nopp = c.nopp  
                        WHERE b.status='0' and c.statuspo<>5 and a.kodeorg='".$rData3['kodeorganisasi']."' and substring(a.tanggal,1,7) like '%".$periode."%' and c.nopo!='' and b.purchaser='".$brsKary['karyawanid']."'  group by b.kodebarang  ";

                $qDt2=mysql_query($sDt2) or die(mysql_error());
                $jmlhPo2=mysql_num_rows($qDt2);
                $rDt2=mysql_fetch_assoc($qDt2);
                $totalPo2[$rDt['purchaser']][$rDt['kodeorg']]+=$jmlhPo2;
                $totalPo[$rDt['purchaser']][$rDt['kodeorg']]+=$rDt['jmlhPo'];
                $all[$rDt['purchaser']][$rDt['kodeorg']]+=$totalPo[$rDt['purchaser']][$rDt['kodeorg']]-$totalPo2[$rDt['purchaser']][$rDt['kodeorg']];
                $tempTotalPo2[$rDt['purchaser']]+=$totalPo2[$rDt['purchaser']][$rDt['kodeorg']];
                $sisa[$rDt['purchaser']]+=$totalPo[$rDt['purchaser']][$rDt['kodeorg']];
                }
                $DtaAll[]=$brsKary;
           }
            foreach($DtaAll as $brs)
            {
                $no++;
                $tab.="<tr class=rowcontent onclick=\"detailData('".$brs['karyawanid']."','".$periode."')\" style=\"cursor:pointer;\">";
                $tab.="<td>".$no."</td>";
                $tab.="<td>".$brs['namakaryawan']."</td>";
                foreach($kdOrg as $brsData2 => $rData2)
                {
                    if($totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']]!=0)
                    {
                      @$persen5[$brs['karyawanid']][$rData2['kodeorganisasi']]=($all[$brs['karyawanid']][$rData2['kodeorganisasi']]/$totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']])*100;
                    }
                    $tab.="<td align=right>".number_format($totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']],0)."</td>";
                    $tab.="<td align=right>".number_format($totalPo2[$brs['karyawanid']][$rData2['kodeorganisasi']],0)."</td>";
                    $tab.="<td align=right>".number_format($all[$brs['karyawanid']][$rData2['kodeorganisasi']],0)."</td>";
                    $tab.="<td align=right>".number_format($persen5[$brs['karyawanid']][$rData2['kodeorganisasi']],0)."</td>";
                    $totTrbitPO[$rData2['kodeorganisasi']]+=$totalPo2[$brs['karyawanid']][$rData2['kodeorganisasi']];
                    $blmPO[$rData2['kodeorganisasi']]+=$totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']];
                    $grndTotal[$rData2['kodeorganisasi']]+=$all[$brs['karyawanid']][$rData2['kodeorganisasi']];
                }
            }
           $col=$a+2;
           $sAll="select count(*) as jmlh from ".$dbname.".log_prapodt where purchaser='0000000000'";
           $qAll=mysql_query($sAll) or die(mysql_error());
           $rAll=mysql_fetch_assoc($qAll);
           if($totalBlm!=0)
           {
            @$persenTot=($totalSemua/$totalBlm)*100;
           }
           $tab.="<tr class=rowcontent><td colspan=2>Total all Items</td>";
            foreach($kdOrg as $brsData2 => $rData2)
            {
                if($blmPO[$rData2['kodeorganisasi']]!=0)
                {
                    @$presen[$rData2['kodeorganisasi']]=$totTrbitPO[$rData2['kodeorganisasi']]/$blmPO[$rData2['kodeorganisasi']]*100;
                }
                $tab.="<td align=right>".number_format($blmPO[$rData2['kodeorganisasi']],0)."</td>";
                $tab.="<td align=right>".number_format($totTrbitPO[$rData2['kodeorganisasi']],0)."</td>";
                $tab.="<td align=right>".number_format($grndTotal[$rData2['kodeorganisasi']],0)."</td>";
                $tab.="<td align=right>".number_format($presen[$rData2['kodeorganisasi']],0)."</td>";
            }
           $tab.="</tr>";
           $tab.="</tbody></table></fieldset>";
           echo $tab;
        break;
        case'detailSum':

            $tab.="<link rel=stylesheet type=text/css href=style/generic.css>
            <script language=javascript1.2 src='js/generic.js'></script>
            <script language=javascript1.2 src='js/log_verivikasi.js'></script>";
            $thn=substr($periode,0,4);
            $sPur="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$userid."'";
            $qPur=mysql_query($sPur) or die(mysql_error());
            $rPur=mysql_fetch_assoc($qPur);
            $tab.="<fieldset><legend>Summary</legend>";
            $tab.="Purchaser : ".$rPur['namakaryawan']."<br />
                   ".$_SESSION['lang']['periode']." : ".$thn."<br />
                <img onclick=detailExcel2('".$userid."','".$periode."') src=images/excel.jpg class=resicon title='MS.Excel'>
                <table cellspacing=1 border=0 cellpading=0>
                <thead>";
            $sPt="select kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
            $qData=fetchData($sPt);
            $tab.="<tr class=rowheader>";
            $tab.="<td rowspan=2>".$_SESSION['lang']['periode']."</td>";    
           $sPt="select kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
            $qData=fetchData($sPt);
           $jumlahData=count($qData);
            $a=1;
           foreach($qData as $brsData => $rData)
           {
               $kdOrg[]=$rData;
               $tab.="<td colspan=4 align=center>".$rData['kodeorganisasi']."</td>";
               $a++;
           }
           $tab.="</tr><tr class=rowheader>";

           for($acd=0;$acd<$jumlahData;$acd++)
           {
               $tab.="<td align=center'>Total. Item</td><td align=center bgcolor='green'>On PO</td><td align=center bgcolor='red'>Not PO</td><td align=center>% Out</td>";
           }
            $tab.="</tr></thead><tbody>";

            $sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_poht where substr(tanggal,1,4)='".$thn."' order by tanggal desc";
            $qPeriode=mysql_query($sPeriode) or die(mysql_error());
            while($rPeriode=mysql_fetch_assoc($qPeriode))
            {
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$rPeriode['periode']."</td>";
                foreach ($qData as $brsData2 =>$rData2)
                {
                    $sDt="SELECT count(kodebarang) as jmlhPo,kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
                        WHERE  b.status='0' and kodeorg='".$rData2['kodeorganisasi']."' and substr(tanggal,1,7) like '%".$rPeriode['periode']."%' and purchaser='".$userid."' ";

                    $qDt=mysql_query($sDt) or die(mysql_error());
                    $rDt=mysql_fetch_assoc($qDt);

                    $sDt2="SELECT a.kodeorg,purchaser,substr(a.tanggal,1,7) as periode FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp
                           LEFT JOIN ".$dbname.".log_po_vw c ON b.nopp = c.nopp  
                           WHERE  b.status='0' and c.statuspo<>5 and a.kodeorg='".$rData2['kodeorganisasi']."' and substr(a.tanggal,1,7) like '%".$rPeriode['periode']."%' and c.nopo!='' and purchaser='".$userid."' group by b.kodebarang";
 
                    $qDt2=mysql_query($sDt2) or die(mysql_error());
                    $jmlhPo2=mysql_num_rows($qDt2);
                    $rDt2=mysql_fetch_assoc($qDt2);
                    $totalPo2[$rDt2['purchaser']][$rDt2['kodeorg']][$rDt2['periode']]+=$jmlhPo2;
                    $totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']]+=$rDt['jmlhPo'];
                    $all[$rDt2['purchaser']][$rDt2['kodeorg']][$rDt2['periode']]=$totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']]-$totalPo2[$rDt2['purchaser']][$rDt2['kodeorg']][$rDt2['periode']];
                    //$sisa[$rDt['purchaser']][$rDt['periode']]+=$totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']];


                    if($totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']]!=0)
                    {
                        if($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']]!=0)
                        {
                            @$persenId[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']]=$totalPo2[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']]/$totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']]*100;
                        }
                       $tab.="<td align=right><a href='#' onclick=detailExcel('".$rDt['purchaser']."','".$rDt['kodeorg']."','".$rDt['periode']."')>".number_format($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']],0)."</a></td>";
                       $tab.="<td align=right>".number_format($totalPo2[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']],0)."</td>";
                       $tab.="<td align=right>".number_format($all[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']],0)."</td>";
                       $tab.="<td align=right>".number_format($persenId[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']],0)."</td>";
                    }
                    else
                    {
                      $tab.="<td align=right>".number_format($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']],0)."</td>";
                      $tab.="<td align=right>".number_format($totalPo2[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']],0)."</td>";
                      $tab.="<td align=right>".number_format($all[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']],0)."</td>";
                      $tab.="<td align=right>".number_format($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']],0)."</td>";
                    }

                    $jmlhAll[$rData2['kodeorganisasi']]+=$all[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']];
                    $jmlhTrbtPo[$rData2['kodeorganisasi']]+=$totalPo2[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']];
                    $jmlhBlmpo[$rData2['kodeorganisasi']]+=$totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']];

                }

                $tab.="</tr>";
            }
            $tab.="<tr class=rowcontent><td>&nbsp;</td>";
            foreach ($qData as $brsData3 =>$rData3)
            {
               if($jmlhBlmpo[$rData3['kodeorganisasi']]!=0)
               {
                   @$persenTotal[$rData3['kodeorganisasi']]= $jmlhTrbtPo[$rData3['kodeorganisasi']]/$jmlhBlmpo[$rData3['kodeorganisasi']]*100;
                   $tab.="<td align=right><a href='#' onclick=detailExcel('".$userid."','".$rData3['kodeorganisasi']."','".$thn."')>".number_format($jmlhBlmpo[$rData3['kodeorganisasi']],0)."</a></td>";
                   $tab.="<td align=right>".number_format($jmlhTrbtPo[$rData3['kodeorganisasi']],0)."</td>";
                   $tab.="<td align=right>".number_format($jmlhAll[$rData3['kodeorganisasi']],0)."</td>";
                   $tab.="<td align=right>".number_format($persenTotal[$rData3['kodeorganisasi']],0)."</td>";
               }
               else
               {
                   $tab.="<td align=right>".number_format($jmlhBlmpo[$rData3['kodeorganisasi']],0)."</td>";
                   $tab.="<td align=right>".number_format($jmlhTrbtPo[$rData3['kodeorganisasi']],0)."</td>";
                   $tab.="<td align=right>".number_format($jmlhAll[$rData3['kodeorganisasi']],0)."</td>";
                   $tab.="<td align=right>".number_format(0,0)."</td>";
               }
            }

            $tab.="</tr>";
            $tab.="</tbody></table></fieldset>";
            echo $tab;
        break;
         case'getSummar':
        if($periode=='')
       {
          $periode=date("Y-m");
       }

            $sPt="select kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
            $qData=fetchData($sPt);
           $jumlahData=count($qData);
            $a=1;
           foreach($qData as $brsData => $rData)
           {
               $kdOrg[]=$rData;
           }

           $sPur="select karyawanid,namakaryawan from ".$dbname.".datakaryawan 
               where bagian='PUR' and kodejabatan!='33' and (tanggalkeluar>'".date('Y-m-d')."' or tanggalkeluar='0000-00-00')  order by namakaryawan asc";

           $qPur=fetchData($sPur);
           foreach($qPur as $brsKary)
           {
               foreach($kdOrg as $brsData3 => $rData3)
                {
                   //get data blm po
                  $sDt=" SELECT   count(kodebarang) as jmlhPo,kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp
                                    WHERE  b.status='0'  and kodeorg='".$rData3['kodeorganisasi']."' and substr(tanggal,1,7) like '%".$periode."%' and b.purchaser='".$brsKary['karyawanid']."'";

                $qDt=mysql_query($sDt) or die(mysql_error());
                $rDt=mysql_fetch_assoc($qDt);

                //get data sdh po
                $sDt2=" SELECT kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp
                        LEFT JOIN ".$dbname.".log_podt c ON b.nopp = c.nopp  
                                    WHERE b.status='0' and kodeorg='".$rData3['kodeorganisasi']."' and substring(tanggal,1,7) like '%".$periode."%' and c.nopo!='' and b.purchaser='".$brsKary['karyawanid']."' group by b.kodebarang  ";

                $qDt2=mysql_query($sDt2) or die(mysql_error());
                $jmlhPo2=mysql_num_rows($qDt2);
                $rDt2=mysql_fetch_assoc($qDt2);
                $totalPo2[$rDt['purchaser']][$rDt['kodeorg']]+=$jmlhPo2;
                $totalPo[$rDt['purchaser']][$rDt['kodeorg']]+=$rDt['jmlhPo'];
                $all[$rDt['purchaser']][$rDt['kodeorg']]+=$totalPo[$rDt['purchaser']][$rDt['kodeorg']]-$totalPo2[$rDt['purchaser']][$rDt['kodeorg']];
                $tempTotalPo2[$rDt['purchaser']]+=$totalPo2[$rDt['purchaser']][$rDt['kodeorg']];
                $sisa[$rDt['purchaser']]+=$totalPo[$rDt['purchaser']][$rDt['kodeorg']];
                }
                $DtaAll[]=$brsKary;
           }
            foreach($DtaAll as $brs)
            {
                $no++;
                $tab.="<tr class=rowcontent onclick=\"detailData('".$brs['karyawanid']."','".$periode."')\" style=\"cursor:pointer;\">";
                $tab.="<td>".$no."</td>";
                $tab.="<td>".$brs['namakaryawan']."</td>";
                foreach($kdOrg as $brsData2 => $rData2)
                {
                    if($totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']]!=0)
                    {
                      @$persen5[$brs['karyawanid']][$rData2['kodeorganisasi']]=($all[$brs['karyawanid']][$rData2['kodeorganisasi']]/$totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']])*100;
                    }
                    $tab.="<td align=right>".number_format($totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']],0)."</td>";
                    $tab.="<td align=right>".number_format($totalPo2[$brs['karyawanid']][$rData2['kodeorganisasi']],0)."</td>";
                    $tab.="<td align=right>".number_format($all[$brs['karyawanid']][$rData2['kodeorganisasi']],0)."</td>";
                    $tab.="<td align=right>".number_format($persen5[$brs['karyawanid']][$rData2['kodeorganisasi']],0)."</td>";
                    $totTrbitPO[$rData2['kodeorganisasi']]+=$totalPo2[$brs['karyawanid']][$rData2['kodeorganisasi']];
                    $blmPO[$rData2['kodeorganisasi']]+=$totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']];
                    $grndTotal[$rData2['kodeorganisasi']]+=$all[$brs['karyawanid']][$rData2['kodeorganisasi']];
                }
            }
           $col=$a+2;
           $sAll="select count(*) as jmlh from ".$dbname.".log_prapodt where purchaser='0000000000'";
           $qAll=mysql_query($sAll) or die(mysql_error());
           $rAll=mysql_fetch_assoc($qAll);
           if($totalBlm!=0)
           {
            @$persenTot=($totalSemua/$totalBlm)*100;
           }
           $tab.="<tr class=rowcontent><td colspan=2>Total all Items</td>";
            foreach($kdOrg as $brsData2 => $rData2)
            {
                if($blmPO[$rData2['kodeorganisasi']]!=0)
                {
                    @$presen[$rData2['kodeorganisasi']]=$totTrbitPO[$rData2['kodeorganisasi']]/$blmPO[$rData2['kodeorganisasi']]*100;
                }
                $tab.="<td align=right>".number_format($blmPO[$rData2['kodeorganisasi']],0)."</td>";
                $tab.="<td align=right>".number_format($totTrbitPO[$rData2['kodeorganisasi']],0)."</td>";
                $tab.="<td align=right>".number_format($grndTotal[$rData2['kodeorganisasi']],0)."</td>";
                $tab.="<td align=right>".number_format($presen[$rData2['kodeorganisasi']],0)."</td>";
            }
           $tab.="</tr>";
           $tab.="</tbody></table></fieldset>";
           echo $tab."###".$periode;
        break;
       case 'dataDetail':
        $userid=$_GET['userid'];
        $kodeorg=$_GET['kodeorg'];
        $periode=$_GET['periode'];
        $stream.=" 
         <table border=\"1\">
         <thead>
         <tr>
         <td bgcolor=#DEDEDE align=center valign=middle>No.</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['nopp']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['kodebarang']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['namabarang']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['tanggal']." PR</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['tgldibutuhkan']."</td>             
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['tanggal']." Alokasi</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['jmlhDiminta']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['jmlh_disetujui']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['satuan']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['jmlh_hari_outstanding']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['nopo']."</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['tanggal']." PO</td>
         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['namasupplier']."</td>
         </tr>
         </thead>
         <tbody>";
        $sql="SELECT   kodeorg,b.purchaser,jumlah,realisasi,a.tanggal,b.kodebarang,b.nopp,b.tgl_sdt,b.tglAlokasi FROM 
            ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp
              WHERE  b.status='0'  and kodeorg='".$kodeorg."' and substr(tanggal,1,7) like '%".$periode."%' and purchaser='".$userid."'";
        //echo $sql;
        if(mysql_query($sql))
        {
        $res=mysql_query($sql);
            while($bar=mysql_fetch_object($res))
            {			
                    $no+=1;
                        $sPp2="select nopo  from ".$dbname.".log_po_vw where statuspo<>5 and nopp='".$bar->nopp."' and kodebarang='".$bar->kodebarang."'";
                        $qPp2=mysql_query($sPp2) or die(mysql_error());
                        $rPp2=mysql_fetch_object($qPp2);
                        if (mysql_num_rows($qPp2)>0){
                            $sPp="select tanggal,kodesupplier from ".$dbname.".log_poht where nopo='".$rPp2->nopo."'";
                            $qPp=mysql_query($sPp) or die(mysql_error());
                            $rPp=mysql_fetch_object($qPp);

                            if($rPp->tanggal!='0000-00-00')
                            {
                                    $tglA=substr($rPp->tanggal,0,4);
                                    $tglB=substr($rPp->tanggal,5,2);
                                    $tglC=substr($rPp->tanggal,8,2);
                                    $tgl2=$tglA.$tglB.$tglC;

                                    $tGl1=substr($bar->tglAlokasi,0,4);
                                    $tGl2=substr($bar->tglAlokasi,5,2);
                                    $tGl3=substr($bar->tglAlokasi,8,2);
                                    $tgl2=$tglA.$tglB.$tglC;
                                    $tgl1 =$tGl1.$tGl2.$tGl3;

                                    $starttime=strtotime($tgl1);//time();// tanggal sekarang
                                    $endtime=strtotime($tgl2);//tanggal pembuatan dokumen
                                    $timediffSecond = abs($endtime-$starttime);
                                    $base_year = min(date("Y", $tGl1), date("Y", $tglA));
                                    $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                                    $jmlHari=date("j", $diff) - 1;
                                    $tglSkrg=$rPp->tanggal;
                            }
                            else
                            {
                                    $tglSkrg=date("Y-m-d");
                                    $tglA=substr($bar->tglAlokasi,0,4);
                                    $tglB=substr($bar->tglAlokasi,5,2);
                                    $tglC=substr($bar->tglAlokasi,8,2);

                                    $tgl2=$tglA.$tglB.$tglC;

                                    $tGl1=substr($tglSkrg,0,4);
                                    $tGl2=substr($tglSkrg,5,2);
                                    $tGl3=substr($tglSkrg,8,2);
                                    $tgl2=$tglA.$tglB.$tglC;
                                    $tgl1 =$tGl1.$tGl2.$tGl3;

                                    $starttime=strtotime($tgl1);//time();// tanggal sekarang
                                    $endtime=strtotime($tgl2);//tanggal pembuatan dokumen
                                    $timediffSecond = abs($endtime-$starttime);
                                    $base_year = min(date("Y", $tGl1), date("Y", $tglA));
                                    $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                                    $jmlHari=date("j", $diff) - 1;
                            }
                        }
                        else
                        {
                                $day = 60*60*24;
                                $tglSkrg=date("Y-m-d");
                                $tglA=substr($bar->tglAlokasi,0,4);
                                $tglB=substr($bar->tglAlokasi,5,2);
                                $tglC=substr($bar->tglAlokasi,8,2);

                                $tGl1=substr($tglSkrg,0,4);
                                $tGl2=substr($tglSkrg,5,2);
                                $tGl3=substr($tglSkrg,8,2);
                                $tgl2=$tglA.$tglB.$tglC;
                                $tgl1 =$tGl1.$tGl2.$tGl3;

                                $date1 = strtotime($tgl2);
                                $date2 = strtotime($tgl1);
                                $jmlHari = round(($date2 - $date1)/$day);
                                $tglSkrg="";
                        }

                    $sNmSup="select distinct namasupplier from ".$dbname.".log_5supplier where supplierid='".$rPp->kodesupplier."'";
                    $qNmSup=mysql_query($sNmSup) or die(mysql_error());
                    $rNmSup=mysql_fetch_assoc($qNmSup);
                    $namaSup=($rPp2->nopo!='')?$rNmSup['namasupplier']:"";

                    $stream.="<tr>
                              <td>".$no."</td>
                              <td>".$bar->nopp."</td>
                              <td>'".$bar->kodebarang."</td>
                              <td nowrap>".substr($rDtBrg[$bar->kodebarang],0,33)."</td>
                              <td>".$bar->tanggal."</td>
                              <td>".$bar->tgl_sdt."</td>
                              <td>".$bar->tglAlokasi."</td>
                              <td align=right>".number_format($bar->jumlah,2)."</td>
                              <td align=right>".number_format($bar->realisasi,2)."</td>
                              <td>".$nmSatuan[$bar->kodebarang]."</td>
                              <td>".$jmlHari."</td> 
                              <td>".$rPp2->nopo."</td> 
                              <td>".$tglSkrg."</td>";
                             $stream.= "<td nowrap>".$namaSup."</td>";
                             $stream.="</tr>";
                    }	 	  

    }	
    else
    {
            echo " Gagal,".(mysql_error($conn));
    }	

     $stream.=" </tbody>";
     //=================================================

    $stream.="</table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
   $time=date("Hms");
  $nop_="listBarang_".$periode."_".$userid."_".$time;
  $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $stream);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";

         break;
         case 'dataDetailEx':
            // echo "warning:test";
                $userid=$_GET['userid'];
                $periode=$_GET['periode'];
                $kodeorg=$_GET['kodeorg'];
            $thn=substr($periode,0,4);
            $sPur="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$userid."'";
            $qPur=mysql_query($sPur) or die(mysql_error());
            $rPur=mysql_fetch_assoc($qPur);

            $tab.="<table cellspacing=1 border=0 cellpading=0>
                <tr><td colspan=2>Purchaser</td><td> :</td><td colspan=3  align=left> ".$rPur['namakaryawan']."</td><td>&nbsp</td></tr>
                <tr><td colspan=2>".$_SESSION['lang']['periode']."</td><td> :</td><td colspan=3 align=left> ".$thn."</td><td>&nbsp</td></tr>
                 </table>";
               $tab.="
                <table cellspacing=1 border=1 cellpading=0>
                <thead>";
            $sPt="select kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
            $qData=fetchData($sPt);
            $tab.="<tr class=rowheader>";
            $tab.="<td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['periode']."</td>";    
            foreach($qData as $brsData => $rData)
            {                   
               $tab.="<td bgcolor=#DEDEDE align=center valign=middle>".$rData['kodeorganisasi']."</td>";
            }
            $tab.="<td bgcolor=#DEDEDE align=center valign=middle>Total Item</td><td bgcolor=#DEDEDE align=center valign=middle>Terbit PO</td>
                <td bgcolor=#DEDEDE align=center valign=middle>Outstanding PO</td>
                <td bgcolor=#DEDEDE align=center valign=middle>% Outstanding</td>";
            $tab.="</tr></thead><tbody>";

                $sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_poht where substr(tanggal,1,4)='".$thn."' order by tanggal desc";
            $qPeriode=mysql_query($sPeriode) or die(mysql_error());
            while($rPeriode=mysql_fetch_assoc($qPeriode))
            {
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$rPeriode['periode']."</td>";
                foreach ($qData as $brsData2 =>$rData2)
                {
                    $sDt="SELECT count(kodebarang) as jmlhPo,kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp
                          WHERE  b.status='0' and kodeorg='".$rData2['kodeorganisasi']."' and substr(tanggal,1,7) like '%".$rPeriode['periode']."%' and purchaser='".$userid."' ";                 

                    $qDt=mysql_query($sDt) or die(mysql_error());
                    $rDt=mysql_fetch_assoc($qDt);

                    $sDt2="SELECT  kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp
                           LEFT JOIN ".$dbname.".log_podt c ON b.nopp = c.nopp  
                           WHERE  b.status='0' and kodeorg='".$rData2['kodeorganisasi']."' and substr(tanggal,1,7) like '%".$rPeriode['periode']."%' and c.nopo!='' and purchaser='".$userid."'  group by b.kodebarang ";                    
                    $qDt2=mysql_query($sDt2) or die(mysql_error());
                    $jmlhPo2=mysql_num_rows($qDt2);
                    $rDt2=mysql_fetch_assoc($qDt2);

                    $totalPo2[$rDt2['purchaser']][$rDt2['kodeorg']][$rDt2['periode']]+=$jmlhPo2;
                    $totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']]+=$rDt['jmlhPo'];
                    $tempTotalPo2[$rDt2['purchaser']][$rDt2['periode']]+=$totalPo2[$rDt2['purchaser']][$rDt2['kodeorg']][$rDt2['periode']];
                    $sisa[$rDt['purchaser']][$rDt['periode']]+=$totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']];
                    if($totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']]!=0)
                    {
                       $tab.="<td align=right>".number_format($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']],0)."</td>";
                    }
                    else
                    {
                      $tab.="<td align=right>".number_format($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']],0)."</td>";  
                    }
                     $jmlh[$rData2['kodeorganisasi']]+=$totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']];

                }
                $totBlm[$userid][$rPeriode['periode']]=$sisa[$userid][$rPeriode['periode']]-$tempTotalPo2[$userid][$rPeriode['periode']];
                if($sisa[$userid][$rPeriode['periode']]!=0)
                {
                    $persen[$userid][$rPeriode['periode']]=($totBlm[$userid][$rPeriode['periode']]/$sisa[$userid][$rPeriode['periode']])*100;
                }
                $tab.="<td  align=right>".number_format($sisa[$userid][$rPeriode['periode']],0)."</td>";
                $tab.="<td  align=right>".number_format($tempTotalPo2[$userid][$rPeriode['periode']],0)."</td>";
                $tab.="<td  align=right>".number_format($totBlm[$userid][$rPeriode['periode']],0)."</td>";
                $tab.="<td  align=right>".number_format($persen[$userid][$rPeriode['periode']],0)."</td>";
                $tab.="</tr>";
                $totItem+=$sisa[$userid][$rPeriode['periode']];
                $trbtPo+=$tempTotalPo2[$userid][$rPeriode['periode']];
                $blmPo+=$totBlm[$userid][$rPeriode['periode']];
                if($totItem!=0)
                {$totPersen=($blmPo/$totItem)*100;}
            }
            $tab.="<tr class=rowcontent><td>&nbsp;</td>";
            foreach ($qData as $brsData3 =>$rData3)
            {

                   $tab.="<td align=right>".number_format($jmlh[$rData3['kodeorganisasi']],0)."</td>";
            }
            $tab.="<td  align=right>".number_format($totItem,0)."</td>";
            $tab.="<td  align=right>".number_format($trbtPo,0)."</td>";
            $tab.="<td  align=right>".number_format($blmPo,0)."</td>";
            $tab.="<td  align=right>".number_format($totPersen,0)."</td>";
            $tab.="</tr>";        
            $tab.="</tbody>";
         //=================================================

        $tab.="</table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
 $jam=date("Hms");
  $nop_="listBarang__".$userid."_".$jam;
  $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $tab);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";

         break;
         case 'listVerivikasiPP':
         $optPur="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                       $klq="select karyawanid,namakaryawan from ".$dbname.".`datakaryawan` where  (bagian='PUR') and kodejabatan in (1,33,39,109) and tanggalkeluar='0000-00-00' order by namakaryawan asc ";

                        $qry=mysql_query($klq) or die(mysql_error());

                        while($rst=mysql_fetch_object($qry))
                        {

                                        $optPur.="<option value=".$rst->karyawanid.">".$rst->namakaryawan."</option>";
                         }
                        $cl=array(0=>'Head Office',1=>'Local');
                        foreach($cl as $rw =>$isi)
                        {
                                $optLokasi.="<option  value='".$rw."'>".$isi."</option>";
                        }
                $str="SELECT  distinct a.`tanggal`, a.`persetujuan1`, a.`persetujuan2`, a.`persetujuan3`, a.
                     `persetujuan4`, a.`persetujuan5`, a.`close`, a.`hasilpersetujuan1`, a.`hasilpersetujuan2`, 
                      a.`hasilpersetujuan3`, a.`hasilpersetujuan4`, a.`hasilpersetujuan5`, a.`tglp1`, a.`tglp2`, 
                      a.`tglp3`, a.`tglp4`, a.`tglp5`,b.*,c.nopo FROM ".$dbname.".log_prapodt b 
                      LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp 
                      LEFT JOIN ".$dbname.".log_podt c ON b.nopp=c.nopp  
                      WHERE b.nopp='".$nopp."' and a.close=2 and b.status=0 and create_po!='1' group by kodebarang ORDER BY a.tanggal desc ";
                $res2=mysql_query($str) or die(mysql_error($conn));
                $row=mysql_num_rows($res2);

         echo"
         <input type=\"hidden\" id=ppno name=ppno value=".$nopp." />
         <fieldset><legend>".$nopp."</legend> 
         <table cellpadding=1 cellspacing=1 border=0 class=sortable>
         <thead>
         <tr class=rowheader>
         <td colspan=3>Verification Form</td>
         </tr>
         </thead>
         <tbody>
         <tr class=rowcontent><td colspan=2>".$_SESSION['lang']['jumlah']." Item</td><td id=totalBrg>".$row."</td></tr>
         <tr class=rowcontent><td colspan=2>".$_SESSION['lang']['purchaser']."</td><td><select id=purId2 name=purId2 style=width:150px;>".$optPur."</select></td></tr>
         <tr class=rowcontent><td colspan=2>".$_SESSION['lang']['lokasitugas']."</td><td><select id=lokId name=lokId style=width:150px;>".$optLokasi."</select></td></tr>
         <tr><td colspan=3><button class=mybutton onclick=saveSemua(1) id=saveAll title=Simpan Semua>".$_SESSION['lang']['save']." ".$_SESSION['lang']['all']."</button><button class=mybutton onclick=cancel()>".$_SESSION['lang']['cancel']."</button></td></tr>
         </tbody>
         </table><br />";

        if(mysql_query($str))
        {
           // echo $str;
            $res=mysql_query($str);
         echo"<fieldset><legend>".$_SESSION['lang']['list']." Item</legend>
         <div  style=overflow:auto;width:650px;height:275px;>
         <table class=\"sortable\" cellspacing=\"1\" border=\"0\">
         <thead>
         <tr class=rowheader>
         <td rowspan=2>No.</td>
         <td rowspan=2>".$_SESSION['lang']['kodebarang']."</td>
         <td rowspan=2>".$_SESSION['lang']['namabarang']."</td>
         <td rowspan=2>".$_SESSION['lang']['tanggal']." PP</td>
         <td rowspan=2>".$_SESSION['lang']['jmlhDiminta']."</td>
         <td colspan=2 align=center>".$_SESSION['lang']['realisasi']."</td>
         <td rowspan=2>".$_SESSION['lang']['harga']."</td>
         <td rowspan=2 colspan='3' align=\"center\">Action</td>
         </tr>
         <tr class=rowheader>
         <td>".$_SESSION['lang']['jumlah']."</td>
         <td>".$_SESSION['lang']['satuan']."</td>
         </tr>
         </thead>
         <tbody>";
            while($bar=mysql_fetch_object($res))
            {			
                    $koderorg=substr($bar->nopp,15,4);
                    $spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
                    $rep=mysql_query($spr) or die(mysql_error($conn));
                    $bas=mysql_fetch_object($rep);
                    $no+=1;
                    $optSatuan="<option value=".$nmSatuan[$bar->kodebarang].">".$nmSatuan[$bar->kodebarang]."</option>";
                    $where=" kodebarang='".$bar->kodebarang."' and darisatuan='".$nmSatuan[$bar->kodebarang]."'";
                    $sSknv="select satuankonversi from ".$dbname.".log_5stkonversi where ".$where."";
                    $qSknv=mysql_query($sSknv) or die(mysql_error());
                    while($rSknv=mysql_fetch_assoc($qSknv))
                    {
                        if ($bar->satuanrealisasi==$rSknv['satuankonversi']){
                            $optSatuan.="<option value=".$rSknv['satuankonversi']." selected>".$rSknv['satuankonversi']."</option>";
                        } else {
                            $optSatuan.="<option value=".$rSknv['satuankonversi'].">".$rSknv['satuankonversi']."</option>";
                        }
                    }
                    
                            $sPoDet="select distinct hargasatuan from ".$dbname.".log_podt where  kodebarang='".$bar->kodebarang."' order by nopo desc";
                            $qPoDet=mysql_query($sPoDet) or die(mysql_error());
                            $rCek=mysql_fetch_assoc($qPoDet);

                    if($bar->realisasi=='')
                    {
                        $bar->realisasi=0;
                    }

                    echo"<tr class=rowcontent id='rew_".$no."'>
                              <td>".$no."</td>
                              <td id=kdBrg_".$no.">".$bar->kodebarang."</td>
                              <td>".substr($rDtBrg[$bar->kodebarang],0,33)."</td>
                              <td nowrap>".tanggalnormal($bar->tanggal)."</td>
                              <td align=center id=jmlh_".$no.">".$bar->jumlah."</td>
                              <td align=right><input type=text id=realisasi2_".$no." name=realisasi2_".$no." onkeypress='return angka_doang(event)' class='myinputtextnumber' value=$bar->jumlah style='width:50px;'/></td>
                              <td><select id=satreal2_".$no." id=satreal2_".$no.">$optSatuan</select></td>
                              <td align=right  >".number_format($rCek['hargasatuan'],2)."</td>";

                             echo"<td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar->nopp."','','log_slave_print_log_pp',event);\"></td></tr>";


                }

        }	
        else
        {
                echo " Gagal,".(mysql_error($conn));
        }	

         echo" </tbody>
         </table></div></fieldset></fieldset>";
         break;
         case 'listVerivikasiPP2':
         $optPur="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                        $klq="select karyawanid,namakaryawan from ".$dbname.".`datakaryawan` where  bagian='PUR' and kodejabatan in (1,33,39,109) and tanggalkeluar='0000-00-00' order by namakaryawan asc ";

                        $qry=mysql_query($klq) or die(mysql_error());

                        while($rst=mysql_fetch_object($qry))
                        {

                                        $optPur.="<option value=".$rst->karyawanid.">".$rst->namakaryawan."</option>";
                                //$optPur.="<option value='".$rst->karyawanid."'  '".($bar->purchaser==$rst->karyawanid?' selected':'')."'  >".$rst->namakaryawan."</option>";
                        }
                        $cl=array(0=>'Head Office',1=>'Local');
                        foreach($cl as $rw =>$isi)
                        {
                                $optLokasi.="<option  value='".$rw."'>".$isi."</option>";
                        }
                $str="SELECT  distinct a.`tanggal`, a.`persetujuan1`, a.`persetujuan2`, a.`persetujuan3`, a.`persetujuan4`, a.`persetujuan5`, a.`close`, a.`hasilpersetujuan1`, a.`hasilpersetujuan2`, a.`hasilpersetujuan3`, a.`hasilpersetujuan4`, a.`hasilpersetujuan5`, a.`tglp1`, a.`tglp2`, a.`tglp3`, a.`tglp4`, a.`tglp5`,b.*,c.nopo FROM ".$dbname.".log_prapodt b LEFT JOIN ".$dbname.".log_prapoht a ON a.nopp = b.nopp LEFT JOIN ".$dbname.".log_podt c ON b.nopp=c.nopp  
                WHERE b.nopp='".$nopp."' and create_po!='1' group by kodebarang ORDER BY a.tanggal desc ";
                $res2=mysql_query($str) or die(mysql_error($conn));
                $row=mysql_num_rows($res2);

         echo"
         <input type=\"hidden\" id=ppno name=ppno value=".$nopp." />
         <fieldset><legend>".$nopp."</legend> 
         <table cellpadding=1 cellspacing=1 border=0 class=sortable>
         <thead>
         <tr class=rowheader>
         <td colspan=3>Form Verivikasi</td>
         </tr>
         </thead>
         <tbody>
         <tr class=rowcontent><td colspan=2>".$_SESSION['lang']['jumlah']." Item</td><td id=totalBrg_2>".$row."</td></tr>
         <tr class=rowcontent><td colspan=2>".$_SESSION['lang']['purchaser']."</td><td><select id=purId2_2 name=purId2_2 style=width:150px;>".$optPur."</select></td></tr>
         <tr class=rowcontent><td colspan=2>".$_SESSION['lang']['lokasitugas']."</td><td><select id=lokId_2 name=lokId_2 style=width:150px;>".$optLokasi."</select></td></tr>
         <tr><td colspan=3><button class=mybutton onclick=saveSemua2(1) id=saveAll2 title='Save All'>".$_SESSION['lang']['save']." ".$_SESSION['lang']['all']."</button><button class=mybutton onclick=cancel()>".$_SESSION['lang']['cancel']."</button></td></tr>
         </tbody>
         </table><br />";

        if($res=mysql_query($str))
        {
           // echo $str;
         echo"<fieldset><legend>".$_SESSION['lang']['list']." Item</legend>
         <div  style=overflow:auto;width:650px;height:275px;>
         <table class=\"sortable\" cellspacing=\"1\" border=\"0\">
         <thead>
         <tr class=rowheader>
         <td>No.</td>
     <td>".$_SESSION['lang']['kodebarang']."</td>
         <td>".$_SESSION['lang']['namabarang']."</td>
         <td>".$_SESSION['lang']['tanggal']." PP</td>
         <td>".$_SESSION['lang']['jmlhDiminta']."</td>
         <td>".$_SESSION['lang']['jumlahrealisasi']."</td>
         <td colspan='3' align=\"center\">Action</td>
         </tr>
         </thead>
         <tbody>";
                while($bar=mysql_fetch_object($res))
                {			
                        $koderorg=substr($bar->nopp,15,4);
                        $spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
                        $rep=mysql_query($spr) or die(mysql_error($conn));
                        $bas=mysql_fetch_object($rep);
                        $no+=1;
                                $sPoDet="select nopo from ".$dbname.".log_podt where nopp='".$bar->nopp."' and kodebarang='".$bar->kodebarang."'";
                                $qPoDet=mysql_query($sPoDet) or die(mysql_error());
                                $rCek=mysql_fetch_assoc($qPoDet);

                        if($bar->realisasi=='')
                        {
                            $bar->realisasi=0;
                        }

                        echo"<tr class=rowcontent id='rew_".$no."'>
                                  <td>".$no."</td>
                                  <td id=kdBrg_2_".$no.">".$bar->kodebarang."</td>
                                  <td>".substr($rDtBrg[$bar->kodebarang],0,33)."</td>
                                  <td>".tanggalnormal($bar->tanggal)."</td>
                                  <td align=center id=jmlh_2_".$no.">".$bar->jumlah."</td>
                                  <td align=center  >".$bar->realisasi."</td>";

                                 echo"<td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar->nopp."','','log_slave_print_log_pp',event);\"></td></tr>";


                }

        }	
        else
        {
                echo " Gagal,".(mysql_error($conn));
        }	

         echo" </tbody>
         </table></div></fieldset></fieldset>";
         break;

         case 'listAddPP':

        $str="select distinct * from ".$dbname.".log_prapodt where nopp='".$nopp."' and status!=3";

           // echo $str;
         $res=mysql_query($str);
         echo"<fieldset><legend>".$_SESSION['lang']['form']."</legend>";
         $lstData=mysql_fetch_assoc($res);
         echo"<table class=\"sortable\" cellspacing=\"1\" border=\"0\"><thead>";
         echo"<tr class=rowheader><td>".$_SESSION['lang']['nopp']."</td><td>".$_SESSION['lang']['tanggalSdt']."</td></tr></thead><tbody>";
         echo"<tr class=rowcontent><td id=noppAja>".$lstData['nopp']."</td><td id=tglSdt>".tanggalnormal($lstData['tgl_sdt'])."</td></tr></tbody></table><br />";
         echo"<div id=listDataPP><table class=\"sortable\" cellspacing=\"1\" border=\"0\"><thead>";
         echo"<tr class=rowheader><td>".$_SESSION['lang']['namabarang']."</td><td>".$_SESSION['lang']['satuan']."</td><td>".$_SESSION['lang']['jumlah']."</td><td>*</td></tr></thead><tbody>";
         echo"<tr class=rowcontent><td><input type=text class=myinputtext onkeypress='return tanpa_kutip(event)' id=nmBarang onclick=\"cariBarang();\" /></td>
             <td><input type=text disabled class=myinputtext id=satuanForm /></td>
             <td><input type=text class=myinputtextnumber onkeypress='return angka_doang(event)' id=jmlhBrg /></td>";
         echo"<td><img src=images/save.png class=resicon onclick=tambahBarang() /></td>";
         echo"</tr></tbody></table><input type=hidden id=kdBarang /></div>";
         echo"<div id=cariBarang style=display:none>
              <fieldset style=float:left><legend>".$_SESSION['lang']['findnoBrg']."</legend>".$_SESSION['lang']['find']."<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=cariBarangGet()>Find</button></fieldset>
              <div id=container5></div></div>";
         echo"</fieldset>";

         break;

         case'insertPurchaser':
         if($purchaser=='')
         {
                 exit("Error: Purchaser is obligatory");
         }
         $sql2="update ".$dbname.".log_prapodt set purchaser='".$purchaser."',lokalpusat='".$lokal."',realisasi='".$jmlh_realisai."',satuanrealisasi='".$sat_realisasi."',tglAlokasi='".$tglHrini."' where nopp='".$nopp."' and kodebarang='".$kd_brng."' and status!='3'";

         if(!mysql_query($sql2))
          {
         echo $sql2;
                   echo " Gagal,".addslashes(mysql_error($conn));
          }

         break;
         case'loadTools':
             $tab="<table class=sortable border=0 cellspacing=1 cellpadding=1><thead>
                 <tr class=rowheader>
                 <td>No.</td>
                 <td>".$_SESSION['lang']['kodeorg']."</td>
                 <td>".$_SESSION['lang']['namaorganisasi']."</td>
                 </tr>
                 </thead><tbody>";
             $sql="select distinct kodeorg  from ".$dbname.".log_prapoht where nopp in (select a.nopp from ".$dbname.".log_prapoht a left join ".$dbname.".log_prapodt b on a.nopp=b.nopp where close=2 and b.status<3 and purchaser=0)";
             $query=mysql_query($sql) or die(mysql_error());
             while($res=mysql_fetch_assoc($query))
             {

                 $no+=1;

                 $tab.="<tr class=rowcontent onclick=detailPo(".$no.") style='cursor:pointer'>
                     <td>".$no."</td>
                      <td id=kodeOrg_".$no.">".$res['kodeorg']."</td>
                     <td>".$optNm[$res['kodeorg']]."</td></tr>";
                     $tab.="<tr><td colspan=3><div id=dataPO_".$no."></div></td></tr>";
                }


             $tab.="</tbody></table>";
             echo $tab;
         break;
         case'loadPPDetail':
         $brsKe=$_POST['brsKe'];
         $tab="<img onclick=\"closeList(".$brsKe.");\" title=\"Tutup\" class=\"resicon\" src=\"images/close.gif\">";
         $tab.="<table cellspacing=1 cellpadding=1 border=0 width=100%>
                     <thead>
                    <tr class=rowheader>
                    <td>No</td>
                    <td>".$_SESSION['lang']['nopp']."</td>
                    <td>".$_SESSION['lang']['unit']."</td>
                    <td>".$_SESSION['lang']['jumlah']."</td>
                    <td>".$_SESSION['lang']['print']."</td>
                   </tr>
                     </thead><tbody>";
         $sql2="select b.kodebarang,a.kodeorg,b.nopp from ".$dbname.".log_prapodt b left join ".$dbname.".log_prapoht a on a.nopp=b.nopp 
             where purchaser=0000000000 and status<3 and kodeorg='".$kodeorg."' and a.close=2 group by nopp order by substring(a.nopp,16,4) asc";
         $query=mysql_query($sql2) or die(mysql_error());
         $jmlData=mysql_num_rows($query);
         $tab.="<tr  class=rowcontent><td colspan=5>Total PP :".$jmlData."</td></tr>";
         while($rwd=mysql_fetch_assoc($query))
         {
             $sJum="select count(kodebarang) as jumlah from ".$dbname.".log_prapodt where nopp='".$rwd['nopp']."' and purchaser=0000000000 and status<3";
             $qJum=mysql_query($sJum) or die(mysql_error($conn));
             $rJum=mysql_fetch_assoc($qJum);
                $no+=1;
                $koderorg=substr($rwd['nopp'],15,4);
                $tab.="<tr class=rowcontent>
                    <td>".$no."</td>
                    <td  onclick=\"getDataPP2('".$rwd['nopp']."')\" style=\"cursor:pointer\">".$rwd['nopp']."</td>
                    <td align=center>".$koderorg."</td>
                    <td align=right>".$rJum['jumlah']."</td>
                    <td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$rwd['nopp']."','','log_slave_print_log_pp',event);\"></td></tr>";
        }
         $tab.="</tbody></table>";
         echo $tab;

         break;
         case'loadBarang':
             $optBarang="<option value=''>".$_SESSION['lang']['all']."</option>";
             $sKodenbarna="select distinct kodebarang,namabarang from ".$dbname.".log_5masterbarang where substr(kodebarang,1,3) = '".$klmpKbrg."' order by namabarang asc";

             $qKodeBarang=mysql_query($sKodenbarna) or die(mysql_error());
             while($rKodebarang=mysql_fetch_assoc($qKodeBarang))
             {
                 $optBarang.="<option value='".$rKodebarang['kodebarang']."'>".$rKodebarang['namabarang']."</option>";
             }
             echo $optBarang;
         break;
          case'getBarang':
               $tab="<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                        <div style=\"overflow:auto;height:295px;width:455px;\">
                        <table cellpading=1 border=0 class=sortbale>
                        <thead>
                        <tr class=rowheader>
                        <td>No.</td>
                        <td>".$_SESSION['lang']['kodebarang']."</td>
                        <td>".$_SESSION['lang']['namabarang']."</td>
                        <td>".$_SESSION['lang']['satuan']."</td>
                        </tr><tbody>
                        ";

            if($klmpKbrg!='')
            {
                    $add=" and kelompokbarang='".$klmpKbrg."'";
            }
            $sLoad="select kodebarang,namabarang,satuan,inactive from ".$dbname.".log_5masterbarang where   (kodebarang like '%".$nmBrg."%' or namabarang like '%".$nmBrg."%') ".$add."";
        //   echo $sLoad;
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            if($res['inactive']==1)
            {
             $tab.="<tr bgcolor='red' title='inactive'>";
            }
            else
            {
                $tab.="<tr class=rowcontent onclick=\"setData('".$res['kodebarang']."','".$res['namabarang']."','".$res['satuan']."')\" title='".$res['namabarang']."'>";
            }
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$res['kodebarang']."</td>";
            $tab.="<td>".$res['namabarang']."</td>";
            $tab.="<td>".$res['satuan']."</td>";
            $tab.="</tr>";
        }
        echo $tab;

        break;
        case'addBarangTopp':
            if($jmlh_realisai=='')
            {
                exit("Error: Quantity is obligatory");
            }
            if($kd_brng=='')
            {
                exit("Error: Material Code is obligatory");
            }
            $optNama=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
            $sData="select distinct lokalpusat,purchaser,tglAlokasi from ".$dbname.".log_prapodt where nopp='".$nopp."'";
            $qData=mysql_query($sData) or die(mysql_error());
            $rData=mysql_fetch_assoc($qData);
            $sSat="select satuan from ".$dbname.".log_5masterbarang where kodebarang='".$kd_brng."'";
            $qSat=mysql_query($sSat) or die(mysql_error());
            $rSat=mysql_fetch_assoc($qSat);

            $sIns="insert into ".$dbname.".log_prapodt (nopp, kodebarang, jumlah, realisasi, satuanrealisasi, keterangan, tgl_sdt, lokalpusat,  tglAlokasi, purchaser) values
                   ('".$nopp."','".$kd_brng."','".$jmlh_realisai."','".$jmlh_realisai."','".$rSat['satuan']."','Tambah Barang oleh ".$_SESSION['empl']['name']."','".$tglSdt."','".$rData['lokalpusat']."','".$rData['tglAlokasi']."','".$rData['purchaser']."')";
            if(mysql_query($sIns))
            {
                echo 1;
            }
            else
            {
                echo "Gagal".$sIns."___".mysql_error();
            }
        break;
     default :
     break;

}
?>

