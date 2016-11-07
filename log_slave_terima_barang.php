<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$proses=$_POST['proses'];
$statInput=$_POST['statInput'];
$nopo=$_POST['nopo'];
$notransaksi=$_POST['notransaksi'];
    $nodok=$_POST['nodok']; 
    $idsupplier=$_POST['idsupplier'];
    $tanggal=tanggalsystem($_POST['tanggal']);
    $nopo=$_POST['nopo'];
    $penerimaId=$_POST['penerimaId'];
    $mengetahuiId=$_POST['mengetahuiId'];
    $qty=$_POST['qty'];
    $kodebarang=$_POST['kodebarang'];
    $kodegudang=$_POST['kodegudang'];
    $post=0;
    $user=$_SESSION['standard']['userid'];
    $satuan=$_POST['satuan'];//satuan pada master barang
    $arrStatus=array("0"=>"Diterima","1"=>"Dikirim");
    $optPt=makeOption($dbname, "organisasi", "kodeorganisasi,namaorganisasi","tipe='PT'");
    $optSupplier=makeOption($dbname, "log_5supplier", "supplierid,namasupplier","kodekelompok='S001'");
    $optNama=makeOption($dbname, "datakaryawan", "karyawanid,namakaryawan","lokasitugas='".$_SESSION['empl']['lokasitugas']."'");
    $tex=$_POST['tex'];


    switch($proses)
    {

        case'postingData':
            $sUpdate="update ".$dbname.".log_lpbht set post='1',tipetransaksi='1',postedby='".$_SESSION['standard']['userid']."' 
                      where notransaksi='".$notransaksi."'";
            //$qUpdate=mysql_query($sUpdate) or die(mysql_error($conn));
            if(mysql_query($sUpdate))
            {

            }

            break;
        case'listData':
        $limit=20;
        $page=0;
        if(isset($_POST['page']))
        {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
        }
        $offset=$page*$limit;
        if($tex!='')
        {
           $dddCari=" and notransaksi like '%".$tex."%'";
        }
        $sql2="select count(*) as jmlhrow from ".$dbname.".log_lpbht where gudangx='".$_SESSION['empl']['lokasitugas']."' ".$dddCari." order by notransaksi desc ";
        $query2=mysql_query($sql2) or die(mysql_error());
        while($jsl=mysql_fetch_object($query2)){
        $jlhbrs= $jsl->jmlhrow;
        }

        $sData="select distinct * from ".$dbname.".log_lpbht where gudangx='".$_SESSION['empl']['lokasitugas']."'  ".$dddCari." 
                order by notransaksi desc limit ".$offset.",20";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
            $dr++;
            $namaposting='Not Posted';
            if(intval($rData['postedby'])!=0)
            {
                  $stry="select namauser from ".$dbname.".user where karyawanid='".$rData['postedby']."'";
                  $resy=mysql_query($stry);
                  $bary=mysql_fetch_object($resy);
                  $namaposting=$bary->namauser;

            }

            if($namaposting=='Not Posted' && $rData['post']==1)
            {
                $namaposting=" Posted By ???";
            }
            if($rData['post']<1)
            {

                //tambahkan tombol edit dan delete
                $add="<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editBapb('".$rData['notransaksi']."','".$rData['nopo']."','".tanggalnormal($rData['tanggal'])."','".$rData['idsupplier']."');\">";
                $add.="&nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delBapb('".$rData['notransaksi']."');\">";
                $add.="&nbsp <img src=images/hot.png class=resicon  title='posting' onclick=\"postData('".$rData['notransaksi']."');\">";

            //	    $add.="<img src=images/application/book_icon.gif class=resicon  title='Post/Close' onclick=\"postingBapb('".$bar->notransaksi."','".$bar->nopo."');\">";
            }  
            else
            {
                $add='';
            }		
            $tab.="<tr class=rowcontent><td>".$dr."</td>";
            $tab.="<td>".$arrStatus[$rData['tipetransaksi']]."</td>";
            $tab.="<td>".$rData['notransaksi']."</td>";
            $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
            $tab.="<td>".$rData['nopo']."</td>";
            $tab.="<td>".$optSupplier[$rData['idsupplier']]."</td>";
            $tab.="<td>".$optNama[$rData['user']]."</td>";
            $tab.="<td>".$namaposting."</td>";
            $tab.="<td align=center>
             ".$add."
             <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewBapb('".$rData['notransaksi']."',event);\"> 
          </td>";
            $tab.="</tr>";
        }
        $tab.="<tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
           <br>
       <button class=mybutton onclick=cariBapb(".($page-1).");>".$_SESSION['lang']['pref']."</button>
           <button class=mybutton onclick=cariBapb(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
           </td>
           </tr>";
        echo $tab;
        break;
        case'getPo':
        //cek nopo
//            $sPocek="select distinct statuspo from ".$dbname.".log_poht where nopo='".$nopo."'";
//            $qPocek=mysql_query($sPocek) or die(mysql_error($conn));
//            $rPocek=mysql_fetch_assoc($qPocek);
//            if($rPocek['statuspo']=='3')
//            {
//                exit("Error:Nopo. ".$nopo." sudah di gudang");
//            }

        //get notransaksi
        if($statInput==0)
        {
            $arrBln=array("1"=>"I","2"=>"II","3"=>"III","4"=>"IV","5"=>"V","6"=>"VI","7"=>"VII","8"=>"VIII","9"=>"IX","10"=>"X","11"=>"XI","12"=>"XII");
            $bln=intval(date("m"));
            $thnskrng=date("Y");
            $ntrans="/".$arrBln[$bln]."/".date("Y")."/BAPB/MA/".$_SESSION['empl']['lokasitugas'];
            $sCek="select distinct notransaksi from ".$dbname.".log_lpbht where notransaksi like '%".$ntrans."%' order by notransaksi desc";
//            exit("error:".$sCek);
            $qCek=mysql_query($sCek) or die(mysql_error($conn));
            $rCek=mysql_fetch_assoc($qCek);
            $awal=substr($rCek['notransaksi'],0,3);
            $awal=intval($awal);
//            $thn=substr($rCek['notransaksi'],14,4); codiang sebelumnya
            $thn=substr($rCek['notransaksi'],-17,4);
            //exit("Error:".$thn."__".$rCek['notransaksi']);
            if($thn!=$thnskrng)
            {
            $awal=1;
            }
            else
            {
            $awal+=1;
            }
            $counter=addZero($awal,3);
            $notrans=$counter."/".$arrBln[$bln]."/".date("Y")."/BAPB/MA/".$_SESSION['empl']['lokasitugas'];

            //get nama supplier
            $sSupplier="select distinct kodesupplier from  ".$dbname.".log_poht where nopo='".$nopo."'";
            $qSupplier=mysql_query($sSupplier) or die(mysql_error($conn));
            $rSupplier=mysql_fetch_assoc($qSupplier);
        }

            $tab.="<table class=sortable cellspacing=1 border=0>
             <thead>
                 <tr class=rowheader>
                   <td>No.</td>
                   <td>".$_SESSION['lang']['kodebarang']."</td>
                   <td>".$_SESSION['lang']['namabarang']."</td>
                   <td>".$_SESSION['lang']['satuan']."</td>
                   <td>".$_SESSION['lang']['sudahditerima']."</td>
                   <td>".$_SESSION['lang']['kuantitaspo']."</td>		   
                   <td>".$_SESSION['lang']['diterima']."</td>
                   <td>".$_SESSION['lang']['keterangan']."</td>
                   <td></td>
                 </tr>
                 </thead><tbody>
                 ";
         $no=0;	 
         //get PO detail for this nopo
         $str="select * from ".$dbname.".log_podt where nopo='".$nopo."'";
         $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
         {

                 $no+=1;
                 $qtypo=$bar->jumlahpesan;
                 $jumlah=$qtypo;//default qty adalah jumlah po
                 $namabarang='';
                 $satuan='';
                 //ambil nama barang dan satuan
                 $str2="select namabarang,satuan from ".$dbname.".log_5masterbarang where kodebarang='".$bar->kodebarang."'";
                 $res2=mysql_query($str2);
                 while($bar1=mysql_fetch_object($res2))
                 {
                        $namabarang=$bar1->namabarang;
                        $satuan=$bar1->satuan;
                 }
                 //cek konversi satuan
                 if($satuan!=$bar->satuan)
                 {
                        //konversi satuan jika satuan default kodebarang tidak sama dengan satuan po
                        $str1="select jumlah from ".$dbname.".log_5stkonversi 
                               where darisatuan='".$satuan."' and satuankonversi='".$bar->satuan."'
                               and kodebarang='".$bar->kodebarang."'";

                        $res3=mysql_query($str1);
                        while($bar2=mysql_fetch_object($res3))
                        {
                                $jumlah=round($qtypo/$bar2->jumlah);//mengkonversi satuan
                        }	   
                 }

//==================ambil jumlah lalu====================
     $jumlahlalu=0;
//===========khusus untuk edit

         $sddt='';
         $jumlahedit=0;
         //ambil value transaksi
         $strh="select jumlah from ".$dbname.".log_lpbdt where 
                notransaksi='".$notransaksi."'
                        and kodebarang='".$bar->kodebarang."'";
          //echo $strh;
         $resh=mysql_query($strh);
          $barh=mysql_fetch_object($resh);
          $jumlahedit=$barh->jumlah;


//	 if($notransaksi!='')
//	   {//khusus untuk edit, jumlah lalu tidak termasuk jumlah yg di edit
//	   	 $sddt=" and a.notransaksi!='".$notransaksi."' ";
//	   }

//++++++++++++++++++++++++++++++
         $strx="select sum(a.jumlah) as jumlah,a.kodebarang as kodebarang 
            from ".$dbname.".log_lpbdt a,
                 ".$dbname.".log_lpbht b
                   where a.notransaksi=b.notransaksi 
                   and b.nopo='".$nopo."' 
               and a.kodebarang='".$bar->kodebarang."'
                   ".$sddt."
                   group by kodebarang";
                 //  echo $strx;
                $resx=mysql_query($strx);
                while($barx=mysql_fetch_object($resx))
                {
                        $jumlahlalu=$barx->jumlah;
                }			 
//		
//		if($notransaksi!='')//jika proses edit
//		   $sisa=$jumlahedit;//tampilkan value data yang di edit
//		else  
                   $sisa=$jumlah-$jumlahlalu;//jika tidak tampilkan sisa yang belum terima



                if($notransaksi!='' && $jumlahedit==0)//jika bukan barang yang termasuk dalam
                  $disab='disabled';                  //bapb yng di edit maka di disable    
                else
                {  
                if($sisa<=0)
                  $disab='disabled';
                else
                  $disab=''; 
                }
//                $sCek="select distinct a.notransaksi from ".$dbname.".log_transaksidt a 
//                    left join ".$dbname.".log_transaksiht b on a.notransaksi=b.notransaksi 
//                    where kodebarang='".$bar->kodebarang."' and b.nopo='".$bar->nopo."'";
//                //exit("Error:".$sCek);
//                $qCek=mysql_query($sCek) or die(mysql_error($conn));
//                $rCek=mysql_num_rows($qCek);
//                if($rCek>0)
//                {
//                 $disab="disabled";
//                }
                  $tab.="<tr class=rowcontent>
                   <td>".$no."</td>
                   <td>".$bar->kodebarang."</td>
                   <td>".$namabarang."</td>
                   <td id='sat".$bar->kodebarang."'>".$satuan."</td>
                   <td align=right>".number_format($jumlahlalu,2,'.',',')."</td><input type=hidden value=$jumlahlalu id='jumlal".$bar->kodebarang."'>
                   <td align=right>".number_format($jumlah,2,'.',',')."</td><input type=hidden value=$jumlah id='jumsek".$bar->kodebarang."'>
                   <td><input type=text ".$disab." class=myinputtextnumber id='qty".$bar->kodebarang."' onkeypress=\"return angka_doang(event);\" value='".$sisa."' size=7 maxlength=12 onblur=cekButton(this,'btn".$bar->kodebarang."')></td>
                   <td>".$bar->catatan."</td>
                   <td><button class=mybutton id='btn".$bar->kodebarang."' onclick=saveItemPo('".$bar->kodebarang."') ".$disab.">".$_SESSION['lang']['save']."</button>";"
                 </tr>";	 	
         }
//get karyawan yang lokasi tugas sama atau lokasi tugas sama dengan induk
  $optmengetahui="<option value=''></option>";
  $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."' or lokasitugas='".$_SESSION['org']['induk']."'";	 	 
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {
        $optmengetahui.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
  }

        $tab.="</tbody>
             <tfoot>
                 <tr>
                   <td colspan=8 align=center>
                   <button onclick=selesaiBapb() class=mybutton>".$_SESSION['lang']['done']."</button>
                   </td>
                 </tr>
                 </tfoot>
                 </table>
                 ";	
        if($statInput=='0')
        {   echo $notrans."###".$tab."###".$rSupplier['kodesupplier'];}
        else
        {
            $sData="select distinct namapenerima,mengetahui from ".$dbname.".log_lpbht where notransaksi='".$notransaksi."'";
            $qData=mysql_query($sData) or die(mysql_error($conn));
            $rData=mysql_fetch_assoc($qData);

            echo $tab."###".$rData['namapenerima']."###".$rData['mengetahui'];
        }
        break;
       case'saveData':
           
         $status=0;
         $str="select * from ".$dbname.".log_lpbht where notransaksi='".$nodok."'";
         $res=mysql_query($str);
         if(mysql_num_rows($res)==1)
         {
                $status=1;
         }

         $str="select * from ".$dbname.".log_lpbdt where notransaksi='".$nodok."'
               and kodebarang='".$kodebarang."'";
         if(mysql_num_rows(mysql_query($str))>0)
         {
                $status=2;
         }	 

         $str="select * from ".$dbname.".log_lpbht where notransaksi='".$nodok."'
               and post=1";
         if(mysql_num_rows(mysql_query($str))>0)
         {
                $status=3;
         }
        	
         $sCek="select distinct a.notransaksi from ".$dbname.".log_transaksidt a 
                    left join ".$dbname.".log_transaksiht b on a.notransaksi=b.notransaksi 
                    where kodebarang='".$kodebarang."' and b.nopo='".$nopo."'";
//                exit("Error:".$sCek);
                $qCek=mysql_query($sCek) or die(mysql_error($conn));
                $rCek=mysql_num_rows($qCek);
                if($rCek>0)
                {
//                 $disab="disabled";
                    $status=0;
                }
        //get other data 
//kode pt dan kurs===================================
        $kurs=1;// default untuk kurs sebagai pengali
        $kodept='';
        $str="select kodeorg,kurs from ".$dbname.".log_poht where nopo='".$nopo."'";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                $kodept=$bar->kodeorg;
                $kurs=$bar->kurs;
        }
//harga satuan base on conversion==============================
        $str="select hargasatuan,jumlahpesan,satuan,matauang,kodebarang from ".$dbname.".log_podt where 
              nopo='".$nopo."' and kodebarang='".$kodebarang."'";
        $res=mysql_query($str);
        $jumlahpesan='';
        $hargasatuan=0;
        $matauang='';
        while($bar=mysql_fetch_object($res))
        {
                $matauang=$bar->matauang;
                $jumlahpesan=$bar->jumlahpesan;
                $hargasatuan=$bar->hargasatuan;
                //konversi satuan jika satuan default kodebarang tidak sama dengan satuan po
                if($satuan!=$bar->satuan)
                 {
                        $jlhkonversi=1;//tidak nol untuk menhindari devide by zero
                        $str1="select jumlah from ".$dbname.".log_5stkonversi 
                               where darisatuan='".$satuan."' and satuankonversi='".$bar->satuan."'
                               and kodebarang='".$bar->kodebarang."'";
                        $res3=mysql_query($str1);
                        if(mysql_num_rows($res3)>0)
                        {
                                while($bar2=mysql_fetch_object($res3))
                                {
                                        $jlhkonversi=$bar2->jumlah;
                                }	
                        }
                        if($jlhkonversi!=0)
                        {
                         $hargasatuan=$bar->hargasatuan*$jlhkonversi;
                        }
                 }
        }

        if($kurs==0 or $matauang=='IDR')
           $kurs=1;
           $hargasatuan=$hargasatuan*$kurs;

//==================ambil jumlah lalu====================
     $jumlahlalu=0;
         $str="select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi 
            from ".$dbname.".log_lpbdt a,
                 ".$dbname.".log_lpbht b
                   where a.notransaksi=b.notransaksi and  
                   b.nopo='".$nopo."' 
                   and a.kodebarang='".$kodebarang."'
                   and a.notransaksi='".$nodok."'
                   order by notransaksi desc limit 1";
//         exit("error: .$str");
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                        $jumlahlalu=$bar->jumlah;
                }	   
//===============================================================		 		  
  //periksa apakah sudah ada status 7
  if($status==0 or $status==1 or $status==2)
  {
        $stro="select a.post from ".$dbname.".log_lpbht a
               left join ".$dbname.".log_lpbdt b
                   on a.notransaksi=b.notransaksi
               where a.tanggal>".$tanggal." and a.kodept='".$kodept."'
                   and b.kodebarang='".$kodebarang."' and gudangx='".$_SESSION['empl']['lokasitugas']."'
                   and a.post=1";
        $reso=mysql_query($stro);
        if(mysql_num_rows($reso)>0)
        {
                $status=7;
                echo " Error :".$_SESSION['lang']['tanggaltutup'];
                exit(0);
        }	   
  }

//=============================start input/update	
//status=0
//   exit("error: ".$status);
        if($status==0)
        {
                $str="insert into ".$dbname.".log_lpbht (
                        `tipetransaksi`,`notransaksi`,`tanggal`,
                        `kodept`,`nopo`,`gudangx`,`user`,
                        `idsupplier`,`post`,`namapenerima`,`mengetahui`)
                values('0','".$nodok."',".$tanggal.",
                     '".$kodept."','".$nopo."','".$_SESSION['empl']['lokasitugas']."',".$user.",
                         '".$idsupplier."',".$post.",'".$penerimaId."','".$mengetahuiId."'
                )";	
//                exit("error: ".$str);
                if(mysql_query($str))//insert hedaer
                {
                        $str="insert into ".$dbname.".log_lpbdt (
                          `notransaksi`,`kodebarang`,
                          `satuan`,`jumlah`,`jumlahlalu`)
                          values('".$nodok."','".$kodebarang."',
                          '".$satuan."',".$qty.",".$jumlahlalu.")";
//                        exit("error: ".$str);
                        if(mysql_query($str))//insert detail
                        {	
                          //update PO jumlah masuk pada posting
                           //update statuspo pada table po
                           //$str="update ".$dbname.".log_poht set statuspo=3 where nopo='".$nopo."'";
                           //mysql_query($str); 
                        }   
                        else
                        {
//                     echo " Gagal, (insert detail on status 0)".addslashes(mysql_error($conn));
                        }	
                }
                else
                        {
                     echo " Gagal,  (insert header on status 0)".addslashes(mysql_error($conn));
                        }
            }
            
       
//============================
//status=1
        else if($status==1)
        {
                        $str="insert into ".$dbname.".log_lpbdt (
                          `notransaksi`,`kodebarang`,
                          `satuan`,`jumlah`,`jumlahlalu`)
                          values('".$nodok."','".$kodebarang."',
                          '".$satuan."',".$qty.",".$jumlahlalu.")";
                        if(mysql_query($str))//insert detail
                        {	
                           //update table po statuspo
                           //$str="update ".$dbname.".log_poht set statuspo=3 where nopo='".$nopo."'";
                           //mysql_query($str); 
                        }   
                        else
                        {
                     echo " Gagal, (insert detail on status 1)".addslashes(mysql_error($conn));
                        }	
        }	
//============================update detail
//status=2
        
        else if($status==2)
        {  
                        $str="update ".$dbname.".log_lpbdt set
                              `jumlah`=".$qty.",
                                  `updateby`=".$user."
                                  where `notransaksi`='".$nodok."'
                                  and `kodebarang`='".$kodebarang."'";	  
                        mysql_query($str);//insert detail
                        if(mysql_affected_rows($conn)<1)
                        {	
                       echo " Gagal, (update detail on status 2)".addslashes(mysql_error($conn));
                        }
                        else
                        {
                                //update jumlah lalu pada transaksi berikutnya jika ada
                                //ambil no trx yg berikutnya
                                $notrxnext='';
                                $strc="select a.notransaksi as notrx from ".$dbname.".log_lpbdt a, ".$dbname.".log_lpbht b
                                      where a.notransaksi= b.notransaksi 
                                          and b.nopo='".$nopo."'
                                          and a.notransaksi>'".$nodok."'
                                          and a.kodebarang='".$kodebarang."'
                                          order by notrx asc limit 1";
                                $resc=mysql_query($strc);
                                while($barc=mysql_fetch_object($resc))	
                                {
                                        $notrxnext=$barc->notrx;
                                }  

                                if($notrxnext!='')
                                {
                                        $str="update ".$dbname.".log_lpbdt set
                                      `jumlahlalu`=".$qty.",
                                          `updateby`=".$user."
                                          where `notransaksi`='".$notrxnext."'
                                          and `kodebarang`='".$kodebarang."'";
                                        mysql_query($str);
                                        if(mysql_affected_rows($conn)<1)
                                        {	
                                             //echo " Gagal, (failed update next `jumlahlalu` on status 2)".addslashes(mysql_error($conn));
                                        }
                                }
                        }	
               
        }
         
//============================return message
//status=3
        
        if($status==3)
        {	
           echo " Gagal: Data has been posted";
        }
         
       break;
       case'deleteData':
       $sDel="delete from ".$dbname.".log_lpbht where notransaksi='".$notransaksi."'";
           if(mysql_query($sDel))
           {

           }
           else
           {
               echo " Gagal, Hapus Header ".addslashes(mysql_error($conn));
           }
       break;
        default:
        break;
    }
?>