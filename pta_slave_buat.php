<?php
        require_once('master_validation.php');
        require_once('config/connection.php');
        include('lib/nangkoelib.php');
        include_once('lib/zLib.php');

$tgl=  date('Ymd');
$kdKeg=$_POST['kdKeg'];
$tanggal=$_POST['tgl'];
$method=$_POST['method'];
$pta=$_POST['nopta'];
$klmpk=$_POST['kelompok'];
$tgl=tanggalsystem($_POST['tgl']);
$jls =$_POST['penjelasan'];
$pta = "PTA".$_SESSION['empl']['lokasitugas'].date('Ymd');
$notransaksi=$_POST['notransaksi'];
$krywnId=$_POST['krywnId'];



switch($method){
    case 'getsatuan':
        $sSat="select satuan from ".$dbname.".setup_kegiatan where kodekegiatan='".$kdKeg."'";
        $qSat=mysql_query($sSat) or die(mysql_error($conn));
        $rSat=mysql_fetch_assoc($qSat);
        echo $rSat['satuan'];
        exit();
    break;
    case'cariBarangDlmDtBs':
        $txtfind=$_POST['txtfind'];
        $str="select * from ".$dbname.".log_5masterbarang where namabarang like '%".$txtfind."%' or kodebarang like '%".$txtfind."%' ";
        if($res=mysql_query($str))
        {
            echo"
            <fieldset>
            <legend>Result</legend>
            <div style=\"overflow:auto; height:300px;\" >
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
                //pengambilan saldo
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
                       and a.tipetransaksi>4 and a.post=0 group by kodebarang";

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
                    echo"<tr bgcolor='red' style='cursor:pointer;'  title='Inactive' >";
                        $bar->namabarang=$bar->namabarang. " [Inactive]";
                        $bgr=" bgcolor='red'";
                }
                else
                {				
                    echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setBrg('".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."')\" title='Click' >";
                }   
                echo" <td class=firsttd >".$no."</td>
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
    case 'add':
        if($_POST['noakunData']=='')
        {
        exit("Error: Account number required");
        }
        if($_POST['tipe_pta']=='')
        {
        exit("Error:Type required");
        }
        if($_POST['jenis_pta']=='')
        {
        exit("Error: Group required");
        }
        #=============== Get Nomor PTA

        $sCek="select distinct notransaksi from ".$dbname.".pta_ht where notransaksi='".$pta."'";
        $qCek=mysql_query($sCek) or die(mysql_error($conn));
        $rCek=mysql_num_rows($qCek);
        //exit("Error:masuk__".$rCek);
        if($rCek<1)
        {
            $simpanht="INSERT INTO ".$dbname.".pta_ht(notransaksi,tanggal,kelompok,penjelasan,dibuat)
                      VALUES ('".$pta."','".$tgl."','".$klmpk."','".str_replace(array("\r", "\n"), '\n', $jls)."',".$_SESSION['standard']['userid'].")";

            if(mysql_query($simpanht))
            {

                $simpandt="INSERT INTO ".$dbname.".pta_dt(notransaksi,tanggal,noakun,tipepta,jenispta,volume,satuanv,
                           jumlah,satuanj,rupiah,kodekegiatan,alokasibiaya,kodevhc,kodebarang,unit)
                           VALUES('".$pta."','".$tgl."','".$_POST['noakunData']."','".$_POST['tipe_pta']."','".$_POST['jenis_pta']."',
                           '".$_POST['vol_pekerjaan']."','".$_POST['satuan_vol']."',".$_POST['jml'].",'".$_POST['satuan_jml']."',".$_POST['jml_rp'].",
                           '".$_POST['kegId']."','".$_POST['alokasi']."','".$_POST['kode_vhc']."','".$_POST['kdbrng']."','".$_SESSION['empl']['lokasitugas']."')";

                if(!mysql_query($simpandt)) 
                {
                    echo " Gagal,".addslashes(mysql_error($conn)) ;
                }

            }
            else 
            {
                echo " Gagal,".addslashes(mysql_error($conn)) ;
            }
        }
        else
        {   
            $simpandt="INSERT INTO ".$dbname.".pta_dt(notransaksi,tanggal,noakun,tipepta,jenispta,volume,satuanv,
                       jumlah,satuanj,rupiah,kodekegiatan,alokasibiaya,kodevhc,kodebarang,unit)
                       VALUES('".$pta."','".$tgl."','".$_POST['noakunData']."','".$_POST['tipe_pta']."','".$_POST['jenis_pta']."',
                       '".$_POST['vol_pekerjaan']."','".$_POST['satuan_vol']."',".$_POST['jml'].",'".$_POST['satuan_jml']."',".$_POST['jml_rp'].",
                       '".$_POST['kegId']."','".$_POST['alokasi']."','".$_POST['kode_vhc']."','".$_POST['kdbrng']."','".$_SESSION['empl']['lokasitugas']."')";

            if(!mysql_query($simpandt)) 
            {
                echo " Gagal,".addslashes(mysql_error($conn));
            }

        }
     break;
     case'editData':
     $sUpdate="update ".$dbname.".pta_ht set penjelasan='".$_POST['penjelasan']."' where notransaksi='".$_POST['nopta']."'";
     if(mysql_query($sUpdate))
     {
         if($_POST['noakunData']!='')
         {
          $simpandt="INSERT INTO ".$dbname.".pta_dt(notransaksi,tanggal,noakun,tipepta,jenispta,volume,satuanv,
                       jumlah,satuanj,rupiah,kodekegiatan,alokasibiaya,kodevhc,kodebarang,unit)
                       VALUES('".$_POST['nopta']."','".$tgl."','".$_POST['noakunData']."','".$_POST['tipe_pta']."','".$_POST['jenis_pta']."',
                       '".$_POST['vol_pekerjaan']."','".$_POST['satuan_vol']."','".$_POST['jml']."','".$_POST['satuan_jml']."','".$_POST['jml_rp']."',
                       '".$_POST['kegId']."','".$_POST['alokasi']."','".$_POST['kode_vhc']."','".$_POST['kdbrng']."','".$_SESSION['empl']['lokasitugas']."')";

            if(!mysql_query($simpandt)) 
            {
                echo " Gagal,".addslashes(mysql_error($conn));
            }
         }
     }
     else
     {
         echo " Gagal,".addslashes(mysql_error($conn));
     }
     break;
     case'loaddata':
        $sCek="select a.*,b.namakaryawan from ".$dbname.".pta_ht a 
                    left join ".$dbname.".datakaryawan b on a.dibuat=b.karyawanid 
                    where a.notransaksi='".$pta."'";
        $qCek=mysql_query($sCek) or die(mysql_error($conn));
        $head=mysql_fetch_assoc($qCek);

        $sLoad="select * from ".$dbname.".pta_dt 
                where unit='".$_SESSION['empl']['lokasitugas']."' and notransaksi='".$pta."'";
        $qLoad=mysql_query($sLoad) or die(mysql_error());
        while($row=mysql_fetch_assoc($qLoad))
        {

            $no+=1;
            $tab.= 
                "<tr class=rowcontent>
                    <td align=center>";
                 if($head['persetujuan1']=='' and $head['persetujuan2']=='' and $head['persetujuan3']=='' and $head['persetujuan4']==''){
            $tab.=" <img src=images/delete1.jpg class=resicon  title='Delete' onclick=\"delData('".$row['notransaksi']."',
                    '".$row['jenispta']."','".$row['alokasibiaya']."','".$row['kodevhc']."','".$row['kodebarang']."','".$row['noakun']."');\" >";
                 }         
            $tab.="</td>
                    <td align=center>$no</td>
                    <td align=left>".$row['tipepta']."</td>
                    <td align=left>".$row['jenispta']."</td>
                    <td align=left>".$row['noakun']."</td>
                    <td align=left>".$row['kodekegiatan']."</td>
                    <td align=right>".number_format($row['rupiah'])."</td>
                    <td align=left>".$row['alokasibiaya']."</td>
                    <td align=left>".$row['kodevhc']."</td>
                    <td align=left>".$row['kodebarang']."</td>
                    <td align=right>".$row['volume']."</td>
                    <td align=left>".$row['satuanv']."</td>
                    <td align=right>".$row['jumlah']."</td>
                    <td align=left>".$row['satuanj']."</td>
                     <td align=left>".$head['namakaryawan']."</td>    
                </tr>";
            $total+=$row['rupiah'];
          }
         $tab.=" <tr class=rowcontent>
           <td colspan=6 align=center>Total</td>
           <td align=right>".number_format($total)."</td>
           <td colspan=8 align=center></td>
          </tr>";

            $sCek="select distinct tanggal,notransaksi,penjelasan,kelompok from ".$dbname.".pta_ht order by notransaksi desc";//swhere notransaksi='".$notransaksi."'";
            $qCek=mysql_query($sCek) or die(mysql_error($conn));
            $rCek=mysql_fetch_assoc($qCek);
            if($rCek['notransaksi']==$pta)
            {
                echo $tab."###".$rCek['notransaksi']."###".tanggalnormal($rCek['tanggal'])."###".$rCek['penjelasan']."###".$rCek['kelompok'];
            }
            else
            {
                echo $tab."###".$pta;
            }
     break;
     case'getData':
         $sCek="select distinct status1,namakaryawan  from ".$dbname.".pta_ht a left join ".$dbname.".datakaryawan b
             on a.dibuat=b.karyawanid where notransaksi='".$notransaksi."'";
         $qCek=mysql_query($sCek) or die(mysql_error($conn));
         $rCek=mysql_fetch_assoc($qCek);
         if($rCek['status1']!=0)
         {
             exit("Error: Approval has been filled");
         }
        $sLoad="select * from ".$dbname.".pta_dt 
                where notransaksi='".$notransaksi."'";
        $qLoad=mysql_query($sLoad) or die(mysql_error());
        while($row=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.= 
                "<tr class=rowcontent>
                    <td align=center>";
                 if($head['persetujuan1']=='' and $head['persetujuan2']=='' and $head['persetujuan3']=='' and $head['persetujuan4']==''){
            $tab.=" <img src=images/delete1.jpg class=resicon  title='Delete' onclick=\"delData('".$row['notransaksi']."',
                    '".$row['jenispta']."','".$row['alokasibiaya']."','".$row['kodevhc']."','".$row['kodebarang']."','".$row['noakun']."');\" >";
                 }         
            $tab.="</td>
                    <td align=center>$no</td>
                    <td align=left>".$row['tipepta']."</td>
                    <td align=left>".$row['jenispta']."</td>
                    <td align=left>".$row['noakun']."</td>
                    <td align=left>".$row['kodekegiatan']."</td>
                    <td align=right>".number_format($row['rupiah'])."</td>
                    <td align=left>".$row['alokasibiaya']."</td>
                    <td align=left>".$row['kodevhc']."</td>
                    <td align=left>".$row['kodebarang']."</td>
                    <td align=right>".$row['volume']."</td>
                    <td align=left>".$row['satuanv']."</td>
                    <td align=right>".$row['jumlah']."</td>
                    <td align=left>".$row['satuanj']."</td>
                    <td align=left>".$rCek['namakaryawan']."</td>    
                </tr>";
            $total+=$row['rupiah'];
          }
         $tab.=" <tr class=rowcontent>
           <td colspan=6 align=center>Total</td>
           <td align=right>".number_format($total)."</td>
           <td colspan=8 align=center></td>
          </tr>";

            $sCek="select distinct tanggal,notransaksi,penjelasan,kelompok from ".$dbname.".pta_ht where notransaksi='".$notransaksi."'order by notransaksi desc";//swhere notransaksi='".$notransaksi."'";
            $qCek=mysql_query($sCek) or die(mysql_error($conn));
            $rCek=mysql_fetch_assoc($qCek);

                echo $tab."###".$rCek['notransaksi']."###".tanggalnormal($rCek['tanggal'])."###".$rCek['penjelasan']."###".$rCek['kelompok'];

     break;
     case'daftarData':
         echo "<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
         echo"<td align=center>No.</td><td align=center>".$_SESSION['lang']['nopta']."</td>
                          <td align=center>".$_SESSION['lang']['penjelasan']."</td>
                          <td align=center>".$_SESSION['lang']['jumlah']." (Rp.)</td>

                          <td align=center colspan=2>".$_SESSION['lang']['action']."</td></thead><tbody>";
                $limit=10;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".pta_ht where substr(notransaksi,4,4)='".$_SESSION['empl']['lokasitugas']."'  order by `tanggal` desc";// echo $ql2;
                //echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }
                if($jlhbrs==0)
                {
                    echo"<tr class=rowcontent><td colspan=6>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
                else
                {
                //$slvhc="select * from ".$dbname.".sdm_ijin where  karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc limit ".$offset.",".$limit." ";
                $slvhc="select *  from ".$dbname.".pta_ht where substr(notransaksi,4,4)='".$_SESSION['empl']['lokasitugas']."'    order by `tanggal` desc limit ".$offset.",".$limit." ";

                $qlvhc=mysql_query($slvhc) or die(mysql_error());

                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
                    $no+=1;
                    $sData="select sum(rupiah) as rupiah from ".$dbname.".pta_dt where notransaksi='".$rlvhc['notransaksi']."'";
                    $qData=mysql_query($sData) or die(mysql_error($conn));
                    $rData=mysql_fetch_assoc($qData);
                    echo"
                    <tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".$rlvhc['notransaksi']."</td>
                    <td>".$rlvhc['penjelasan']."</td>
                    <td align=right>".number_format($rData['rupiah'],2)."</td>";
               echo"<td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".$rlvhc['notransaksi']."',event)\"></td>";
               if($rlvhc['status1']==0)
               {
               echo"<td align=center><img src=images/application/application_edit.png class=resicon  title='Edit Data ".$rlvhc['notransaksi']."' onclick=\"editData('".$rlvhc['notransaksi']."')\">&nbsp;";
               echo"<img src=images/application/application_delete.png class=resicon  title='Delete Data ".$rlvhc['notransaksi']."' onclick=\"deleteData('".$rlvhc['notransaksi']."')\"></td>";
               }

            }//end while
                echo"
                </tr><tr class=rowheader><td colspan=6 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
            echo"</tbody></table>";
                }

     break;
    case'deleteData':
    $sDelete="delete from ".$dbname.".pta_ht where notransaksi='".$_POST['notransaksi']."'";

     if(!mysql_query($sDelete))
     {
         echo "DB Error : ".mysql_error($conn);  
     }
    break;
     case 'delete':
         if($_POST['nopta']!=''){$where.=" ";}
         if($_POST['jenispta']!=''){$where.=" and jenispta='".$_POST['jenispta']."'";}
         if($_POST['alokasi']!=''){$where.=" and alokasibiaya='".$_POST['alokasi']."'";}
         if($_POST['kdvhc']!=''){$where.=" and kodevhc='".$_POST['kdvhc']."'";}
         if($_POST['kdbrng']!=''){$where.=" and kodebarang='".$_POST['kdbrng']."'";}
         if($_POST['noakun']!=''){$where.=" and noakun='".$_POST['noakun']."'";}
        $sDel="delete from ".$dbname.".pta_dt where notransaksi='".$_POST['nopta']."' and noakun='".$_POST['noakun']."' ".$where."";	
//        exit('error:'.$sDel);
        if(mysql_query($sDel))
        echo"";
        else
        echo "DB Error : ".mysql_error($conn);          
     break;
      case'getForm':
        $optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
              where tipekaryawan=0 and tanggalkeluar='0000-00-00' and karyawanid!='".$_SESSION['standard']['userid']."' order by namakaryawan asc";
        $qKary=mysql_query($sKary) or die(mysql_error($conn));
        while($rKary=mysql_fetch_assoc($qKary))
        {
            $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."</option>";
        }
        $tab.="<fieldset><legend>".$notransaksi."</legend>";
        $tab.="<table cellpadding=1 cellspacing=1 border=0>";
        $tab.="<tr><td>".$_SESSION['lang']['namakaryawan']."</td><td>:</td><td><select id=dtKary>".$optKary."</select></td></tr>";
        $tab.="<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td><textarea id=koments onkeypress=return tanpa_kutip(event)></textarea></td></tr>";
        $tab.="<tr><td colspan=3 align=center><button class=mybutton onclick=saveAjukan()>".$_SESSION['lang']['diajukan']."</button></td></tr></table>";
        $tab.="</fieldset>";
        echo $tab;

     break;
     case'appSetuju':

        $sKary="select distinct status1 from ".$dbname.".pta_ht where notransaksi='".$notransaksi."'";
        $qKary=mysql_query($sKary) or die(mysql_error($conn));
        $rKary=mysql_fetch_assoc($qKary);
        if($rKary['status1']==0)
        {
            $sUpdate="update ".$dbname.".pta_ht set persetujuan1='".$krywnId."' 
             where status1='0' and notransaksi='".$notransaksi."'";
        }
       // exit($sUpdate." Error");
        if(!mysql_query($sUpdate))
        {
            exit("DB:Error".mysql_error($conn)."__".$sUpdate);
        }
        else{
                   $to=getUserEmail($krywnId); 
                   $subject="[Notifikasi] Persetujuan PTA ";
                    $body="<html>
                            <head>
                            <body>
                            <dd>Dengan Hormat,</dd><br>
                            <br>
                             Pada hari ini karyawan A/n ".$_SESSION['empl']['name']." mengajukan persetujuan PTA 
                             No.".$notransaksi." kepada bapak/ibu, untuk menindaklanjuti silahkan click link dibawah.
                            <br>
                            <br>
                            <br>
                            Regards,<br>
                            Owl-Plantation System.
                            </body>
                            </head>
                        </html>";
                    $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;  

        }
        break;
        case'getKegiatan':
        $optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            if($_SESSION['language']=='EN'){
                $dd='namakegiatan1 as namakegiatan';
            }else{
                $dd='namakegiatan as namakegiatan';
            }
        $sKeg="select distinct kodekegiatan,".$dd." from ".$dbname.".setup_kegiatan where noakun like '%".$_POST['noakun']."%' order by kodekegiatan";
        $qKeg=mysql_query($sKeg) or die(mysql_error($conn));
        while($rKeg=mysql_fetch_assoc($qKeg))
        {
            $optKeg.="<option value='".$rKeg['kodekegiatan']."'>".$rKeg['kodekegiatan']."-".$rKeg['namakegiatan']."</option>";
        }
        echo $optKeg;
        break;
        default:
        break;	
        }
        ?>