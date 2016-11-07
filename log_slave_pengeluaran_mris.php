<?php
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optNmKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSatBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$whrind="char_length(kodeorganisasi)=4";
$optInduk=makeOption($dbname, 'organisasi', 'kodeorganisasi,induk',$whrind);
$param=$_POST;

if(isTransactionPeriod()){//check if transaction period is normal
            
    switch($param['proses']){
        case'getAfd':
            $optKbn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $skbn="select distinct untukunit as kodeorg from ".$dbname.".log_mrisht 
                   where left(untukunit,4)='".$param['divisiId']."'";
            $qkbn=mysql_query($skbn) or die(mysql_error($conn));
            while($rkbn=  mysql_fetch_assoc($qkbn)){
                $optKbn.="<option value='".$rkbn['kodeorg']."'>".$optNmOrg[$rkbn['kodeorg']]."</options>";
            }
            echo $optKbn;
        break;
        case'getPrd':
            $optKbn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $skbn="select distinct left(tanggal,7) as periode from ".$dbname.".log_mrisht 
                   where untukunit='".$param['afdId']."' order by tanggal desc";
            //exit("error:".$skbn);
            $qkbn=mysql_query($skbn) or die(mysql_error($conn));
            while($rkbn=  mysql_fetch_assoc($qkbn)){
                $optKbn.="<option value='".$rkbn['periode']."'>".$rkbn['periode']."</options>";
            }
            echo $optKbn;
        break;
        case'getHeader':
            if($param['kbnId']!=''){
                $whr.="and untukunit like '".$param['kbnId']."%'";
            }
            if($param['afdId']!=''){
                $whr="";
                $whr.="and untukunit='".$param['afdId']."'"; 
            }
            if($param['periode']!=''){
                $whr.="and tanggal like '".$param['periode']."%'"; 
            }
            if($param['nomris']!=''){
                $whr="";
                $whr.=" and  notransaksi like '".$param['nomris']."%'";
            }
            $sdata="select * from ".$dbname.".log_mrisht where notransaksi!='' ".$whr." ";
            //echo $sdata;
            $qdata=mysql_query($sdata) or die(mysql_error($conn));
            while($rdata=  mysql_fetch_assoc($qdata)){
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$rdata['notransaksi']."</td>";
                $tab.="<td>".$rdata['tanggal']."</td>";
                $tab.="<td>".substr($rdata['untukunit'],0,4)."</td>";
                $tab.="<td>".$rdata['untukunit']."</td>";
                $tab.="<td>".$optNmKary[$rdata['dibuat']]."</td>
                <td align=center><img src='images/addplus.png' style='cursor:pointer' onclick=getDetail('".$rdata['notransaksi']."') title='".$_SESSION['lang']['detail']." ".$rdata['notransaksi']."' /></td></tr>";
                
            }
        echo $tab;
        break;
        case'getDetail':
            $sht="select distinct untukunit as kebun,tanggal,kodegudang from ".$dbname.".log_mrisht where notransaksi='".$param['notransaksi']."'";
            $qht=mysql_query($sht) or die(mysql_error($conn));
            $rht=mysql_fetch_assoc($qht);
            
            $sPrdStr="select distinct tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi 
                      where tutupbuku=0 and kodeorg='".$rht['kodegudang']."'";
            $qPrsStr=mysql_query($sPrdStr) or die(mysql_error($conn));
            $rPrdStr=mysql_fetch_assoc($qPrsStr);
            
            
            $sDet="select distinct * from ".$dbname.".log_mrisdt where notransaksi='".$param['notransaksi']."'";
            $qDet=mysql_query($sDet) or die(mysql_error($conn));
            while($rDet=  mysql_fetch_assoc($qDet)){
                $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td id=kdBrg_".$no.">".$rDet['kodebarang']."</td>";
            $tab.="<td id=nmBrg_".$no.">".$optNmBrg[$rDet['kodebarang']]."</td>";
            $tab.="<td id=satBrg_".$no.">".$optSatBrg[$rDet['kodebarang']]."</td>";
            $tab.="<td id=kdBlok_".$no.">".$rDet['kodeblok']."</td>";
            $tab.="<td id=kdMesin_".$no.">".$rDet['kodemesin']."</td>
            <td id=jmlh_".$no." align=right>".number_format($rDet['jumlah'],2)."</td>";
            $tab.="<td id=realisasiSblm_".$no."  align=right>".number_format($rDet['jumlahrealisasi'],2)."</td>";
            $tab.="<td  align=right><input type=text  id=jmlhPengeluara_".$no." onkeypress='return angka_doang(event)' onblur=cekIsi(".$no.") style=width:100px class=myinputtextnumber />
                   <input type=hidden  id=kegId_".$no." value='".$rDet['kodekegiatan']."' />
                   </td>";
            $tab.="<td align=center><img src=images/save.png class=resicon style='cursor:pointer' onclick=saveDt(".$no.",'".$param['notransaksi']."') title='".$_SESSION['lang']['save']." ".$optNmBrg[$rDet['kodebarang']]."' /></td></tr>";
            }
            
            echo $tab."####".$rht['kebun']."####".tanggalnormal($rht['tanggal'])."####".$param['notransaksi']."####".$rht['kodegudang']."####".tanggalnormal($rPrdStr['tanggalmulai'])."####".tanggalnormal($rPrdStr['tanggalsampai'])."####".tanggalsystem(tanggalnormal($rPrdStr['tanggalmulai']))."####".tanggalsystem(tanggalnormal($rPrdStr['tanggalsampai']));
        break;
        case'saveData':
            $tipetransaksi=5;
            $post=0;
            $sheader="select * from ".$dbname.".log_mrisht where notransaksi='".$param['notransaksi']."' and post=1";
            $qheader=mysql_query($sheader) or die(mysql_error($conn));
            if(mysql_num_rows($qheader)==0){
                exit("error: No.MRIS :".$param['notransaksi']." need to post");
            }
            $rheader=mysql_fetch_assoc($qheader);
            $gudang	=$rheader['kodegudang'];
            #cek nomris sudah ada atau belum di ht
                $smris="select distinct notransaksi from ".$dbname.".log_transaksiht 
                        where nomris='".$param['notransaksi']."' and 
                        tanggal='".tanggaldgnbar($param['tanggal'])."' and post=0 and kodegudang='".$gudang."'";
                //exit("error:".$smris);
                $qmris=mysql_query($smris) or die(mysql_error($conn));
                if(mysql_num_rows($qmris)>0){//cek dah pernah mrisnya kesimpen
                    $rmris=mysql_fetch_assoc($qmris);
                    $nodok=$rmris['notransaksi'];
                }else{
                  $ngantri=0;  
                  $num=1;//default value 
                  while($ngantri==0) {
                      $str="select max(notransaksi) notransaksi from ".$dbname.".log_transaksiht where tipetransaksi>4 and tanggal>=".$_SESSION['gudang'][$gudang]['start']." and tanggal<=".$_SESSION['gudang'][$gudang]['end']."
                            and kodegudang='".$gudang."' order by notransaksi desc limit 1";	
                      if($res=mysql_query($str)){
                            while($bar=mysql_fetch_object($res)){
                                    $num=$bar->notransaksi;
                                    if($num!=''){
                                            $num=intval(substr($num,6,5))+1;
                                    }	
                                    else{
                                            $num=1;
                                    }
                            }
                            if($num<10)
                               $num='0000'.$num;
                            else if($num<100)
                               $num='000'.$num;
                            else if($num<1000)
                               $num='00'.$num;
                            else if($num<10000)
                               $num='0'.$num;
                            else
                       $num=$num;
                       $num=$_SESSION['gudang'][$gudang]['tahun'].$_SESSION['gudang'][$gudang]['bulan'].$num."-GI-".$gudang;	
                      }
                        $nodok		=$num;
                        $scek="select distinct notransaksi,user from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
                        $qcek=mysql_query($scek) or die(mysql_error());
                        $rcek=mysql_fetch_assoc($qcek);
                        if(mysql_num_rows($qcek)==1){
                           $ngantri=0;
                        }else{
                            $ngantri=1;
                        }
                  }
                    
                }
                
                    
            //pastikan kodeblok terisi$optInduksubstr
            if($blok=='')
               $blok=$subunit;
            if($blok=='')
               $blok=$untukunit;
            $tanggal	=tanggalsystem($_POST['tanggal']);
            $kodebarang	=$_POST['kdBarag'];
            $penerima	=$rheader['mengetahui'];
            $satuan		=$_POST['satuan'];
            $qty		=$_POST['jmlhKeluar'];
            $blok		=$_POST['kdblok'];
            $mesin		=$_POST['kdMesin'];
            $untukunit	=substr($_POST['afdeling'],0,4);
            $subunit	=$_POST['afdeling'];
            $gudang		=$rheader['kodegudang'];
            $catatan	="Pengeluaran Melalui MRIS dengan No.MRIS :".$rheader['notransaksi'];
            $kegiatan	=$_POST['kegiatan'];
            
            $pemilikbarang=$rheader['kodept'];        
            $user		=$_SESSION['standard']['userid'];

            //1 cek apakah sudah terekan di header
            //status=0 belum ada apa2
            //status=1 ada header
            //status=2 ada detail dan header
            //status=3 sudah di posting
            //status=4 kode pt penerima barang tidak ada
            //status=5 delete item
            //status=6 display only
            //status=7 sudah ada yang diposting pada tanggal yang lebih besar dengan barang yang sama dan pt yang sama

             $status=0;
             $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
             $res=mysql_query($str);
             if(mysql_num_rows($res)==1){
                 $wher="kodebarang='".$kodebarang."' and notransaksi='".$nodok."' and kodemesin='".$mesin."' and kodeblok='".$blok."'";
                 $optCek=makeOption($dbname, 'log_transaksidt', 'notransaksi,kodebarang',$wher);
                 if($optCek[$nodok]==''){
                    $status=1;
                 }else{
                     $status=2;
                 }
             }
 
            if(isset($_POST['delete']))
            {
                    $status=5;
            }	

             $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'
                   and post=1";
             if(mysql_num_rows(mysql_query($str))>0)
             {
                    $status=3;
             }	
    //===================================	 
    //ambil PT peminta barang
       $ptpemintabarang='';
       $stre=" select induk from ".$dbname.".organisasi where kodeorganisasi='".$untukunit."'";
       $rese=mysql_query($stre);
       while($bare=mysql_fetch_object($rese))
       {
             //cek if tipe=PT
               $strf="select tipe from ".$dbname.".organisasi where kodeorganisasi='".$bare->induk."'";
               $resf=mysql_query($strf);
               while($barf=mysql_fetch_object($resf))
               {
                  if($barf->tipe=='PT')
                         $ptpemintabarang=$bare->induk;//ini memang bare
               }
       } 
       //if $ptpemintabarang=='', ambil dari default alokasi pada holding;
        if($ptpemintabarang=='')
            {
               $strf="select alokasi from ".$dbname.".organisasi where kodeorganisasi='".$untukunit."' and alokasi<>''";
               $resf=mysql_query($strf);
               while($barf=mysql_fetch_object($resf))
               {
                         $ptpemintabarang=$barf->alokasi;
               }		
                if($ptpemintabarang=='')
                    {
                            $status=4;
                    }
            } 
            if(isset($_POST['displayonly']))
            {
                    $status=6;
            }

    //==================ambil jumlah lalu====================
         $jumlahlalu=0;
             $str="select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi,a.waktutransaksi 
                from ".$dbname.".log_transaksidt a,
                     ".$dbname.".log_transaksiht b
                       where a.notransaksi=b.notransaksi 
                   and a.kodebarang='".$kodebarang."'
                       and a.notransaksi<='".$nodok."'
                       and b.tipetransaksi>4 
                       and b.kodegudang='".$gudang."'
                       order by notransaksi desc, waktutransaksi desc limit 1";   
                    $res=mysql_query($str);
                    while($bar=mysql_fetch_object($res))
                    {
                            $jumlahlalu=$bar->jumlah;
                    }	    		  
            //ambil pemasukan barang yang belum di posting
                    $qtynotpostedin=0;
                    $str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
                   b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$kodebarang."' 
                               and a.tipetransaksi<5
                               and a.kodegudang='".$gudang."'
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
               b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$kodebarang."' 
                       and a.tipetransaksi>4
                       and a.kodegudang='".$gudang."'
                       and a.post=0		   
                       group by kodebarang";
            $res2=mysql_query($str2);
            while($bar2=mysql_fetch_object($res2))
            {
                    $qtynotposted=$bar2->jumlah;
            }
            if($qtynotposted=='')
               $qtynotposted=0;

    //ambil saldo qty===============================================
       $saldoqty=0;
       $strs="select saldoqty from ".$dbname.".log_5masterbarangdt where kodebarang='".$kodebarang."'
              and kodeorg='".$pemilikbarang."'
                      and kodegudang='".$gudang."'";
       //exit("error:".$strs);
       $ress=mysql_query($strs);
       while($bars=mysql_fetch_object($ress))
       {
            $saldoqty=$bars->saldoqty;
       }  		  

     //==================periksa kecukupan saldo
      if($status==0 or $status==1)
      {
            if(($qty+$qtynotposted)>($saldoqty+$qtynotpostedin))
              {
                    echo " Error: ".$_SESSION['lang']['saldo']." ".$_SESSION['lang']['tidakcukup'];
                    $status=6;//status ngeles
                    exit(0);		
              }
      } 
      else if($status==2)
      {
            //ambil jumlah lama dan bandingkan dengan qty kemudian bandingkan dengan saldo
            $jlhlama=0;
            $strt="select jumlah from ".$dbname.".log_transaksidt where notransaksi='".$nodok."'
                   and kodebarang='".$kodebarang."' and kodeblok='".$blok."'";
            
            $rest=mysql_query($strt);
            while($bart=mysql_fetch_object($rest))
            {
                    $jlhlama=$bart->jumlah;
            }	
            if(($saldoqty+$jlhlama+$qtynotpostedin)<($qty+$qtynotposted))
            {
                    echo " Error: ".$_SESSION['lang']['saldo']." ".$_SESSION['lang']['tidakcukup'];
                    $status=6;//status ngeles
                    exit(0);
            }   
      } 

      //periksa apakah sudah ada status 7

      if($status==0 or $status==1 or $status==2)
      {
            $stro="select a.post from ".$dbname.".log_transaksiht a
                   left join ".$dbname.".log_transaksidt b
                       on a.notransaksi=b.notransaksi
                   where a.tanggal>".$tanggal." and a.kodept='".$pemilikbarang."'
                       and b.kodebarang='".$kodebarang."' and kodegudang='".$kodegudang."'
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
            if($status==0)
            {
                    $str="insert into ".$dbname.".log_transaksiht (
                              `tipetransaksi`,`notransaksi`,
                              `tanggal`,`kodept`,
                              `untukpt`,`keterangan`,
                              `kodegudang`,`user`,
                              `namapenerima`,`untukunit`,`post`,`nomris`)
                    values(".$tipetransaksi.",'".$nodok."',
                           ".$tanggal.",'".$pemilikbarang."',
                              '".$ptpemintabarang."','".$catatan."',
                              '".$gudang."',".$user.",
                              '".$penerima."','".$untukunit."',".$post.",'".$rheader['notransaksi']."'
                    )";	
                    //exit("error:".$str);
                    if(mysql_query($str))//insert hedaer
                    {
                            $str="insert into ".$dbname.".log_transaksidt (
                              `notransaksi`,`kodebarang`,
                              `satuan`,`jumlah`,`jumlahlalu`,
                              `kodeblok`,`updateby`,`kodekegiatan`,
                              `kodemesin`,`nomris`)
                              values('".$nodok."','".$kodebarang."',
                              '".$satuan."',".$qty.",".$jumlahlalu.",
                              '".$blok."','".$user."','".$kegiatan."',
                              '".$mesin."','".$rheader['notransaksi']."')";
                            if(mysql_query($str))//insert detail
                            {	
                                //update PO jumlah masuk
                                $hwr="kodebarang='".$kodebarang."' and notransaksi='".$rheader['notransaksi']."' "
                                   . "and kodemesin='".$mesin."' and kodeblok='".$blok."'";
                                $optJmlhLalu=makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlahrealisasi',$hwr);
                                $optJmlhMinta=makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlah',$hwr);
                                $jmlh=$qty+$optJmlhLalu[$kodebarang];
                                if($jmlh>$optJmlhMinta[$kodebarang]){
                                    exit("error:Amount of expenditures bigger then demand for goods");
                                }
                                $supdate="update ".$dbname.".log_mrisdt set
                                          jumlahrealisasi='".$jmlh."' where ".$hwr."";
                                if(!mysql_query($supdate)){
                                    echo " Gagal, (update status on log_mrisdt)".addslashes(mysql_error($conn));
                                    exit(0);
                                }

                            }   
                            else
                            {
                             echo " Gagal, (insert detail on status 0)".addslashes(mysql_error($conn));
                             exit(0);
                            }	
                    }
                    else
                            {
                         echo " Gagal,  (insert header on status 0)".addslashes(mysql_error($conn));
                             exit(0);
                            }		
            }
    //============================
    //status=1
            if($status==1)
            {
                $scek="select * from ".$dbname.".log_transaksiht 
                       where notransaksi='".$nodok."' and nomris='".$rheader['notransaksi']."'";
                $qcek=mysql_query($scek) or die(mysql_error($conn));
                $rcek=mysql_num_rows($qcek);
                if($rcek==0){
                    exit('Error: This transaction belongs to other user, please reload and start over');
                }
                
                            $str="insert into ".$dbname.".log_transaksidt (
                              `notransaksi`,`kodebarang`,
                              `satuan`,`jumlah`,`jumlahlalu`,
                              `kodeblok`,`updateby`,`kodekegiatan`,
                              `kodemesin`,`nomris`)
                              values('".$nodok."','".$kodebarang."',
                              '".$satuan."',".$qty.",".$jumlahlalu.",
                              '".$blok."','".$user."','".$kegiatan."',
                              '".$mesin."','".$rheader['notransaksi']."')";
                            if(mysql_query($str)){//insert detail
                                //update PO jumlah masuk
                                $hwr="kodebarang='".$kodebarang."' and notransaksi='".$rheader['notransaksi']."' "
                                   . "and kodemesin='".$mesin."' and kodeblok='".$blok."'";
                                $optJmlhLalu=makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlahrealisasi',$hwr);
                                $optJmlhMinta=makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlah',$hwr);
                                $jmlh=$qty+$optJmlhLalu[$kodebarang];
                                if($jmlh>$optJmlhMinta[$kodebarang]){
                                    exit("error:Amount of expenditures bigger then demand for goods");
                                }
                                $supdate="update ".$dbname.".log_mrisdt set
                                          jumlahrealisasi='".$jmlh."' where ".$hwr."";
                                if(!mysql_query($supdate)){
                                    echo " Gagal, (update status on log_mrisdt)".addslashes(mysql_error($conn));
                                    exit(0);
                                }
                            }   
                            else
                            {
                         echo " Gagal, (insert detail on status 1)".addslashes(mysql_error($conn));
                             exit(0);
                            }	
            }	
    //============================update detail
    //status=2
      if($status==2){
                $hwr="kodebarang='".$kodebarang."' and notransaksi='".$nodok."'";
                $optJmlhTrDt=makeOption($dbname, 'log_transaksidt', 'kodebarang,jumlah',$hwr);
                $hwr2="kodebarang='".$kodebarang."' and notransaksi='".$param['notransaksi']."'";
                $optJmlhLalu=makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlahrealisasi',$hwr2);
                $optJmlh=makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlah',$hwr2);
                $jmlhIni=($optJmlhLalu[$kodebarang]-$optJmlhTrDt[$kodebarang])+$qty;
                if($jmlhIni>$optJmlh[$kodebarang]){
                    exit("error:Amount of expenditures bigger then demand for goods");
                }
               
                    $str="update ".$dbname.".log_transaksidt set
                          `jumlah`=".$qty.",
                              `updateby`=".$user.",
                              `kodekegiatan`='".$kegiatan."',
                              `kodemesin`='".$mesin."'
                              where `notransaksi`='".$nodok."'
                              and `kodebarang`='".$kodebarang."'
                              and `kodeblok`='".$blok."'";
                    //exit("error:".$str);
                    mysql_query($str);//insert detail
                    if(mysql_affected_rows($conn)<1)
                    {	
                       echo " Gagal, (update detail on status 2)".addslashes(mysql_error($conn));
                       exit(0);
                    }else{
                        $hwr="kodebarang='".$kodebarang."' and notransaksi='".$rheader['notransaksi']."' "
                                   . "and kodemesin='".$mesin."' and kodeblok='".$blok."'";
                     $supdate="update ".$dbname.".log_mrisdt set jumlahrealisasi='".$jmlhIni."' where 
                               ".$hwr."";
                                if(!mysql_query($supdate)){
                                    echo " Gagal, (update detail on status 2)".addslashes(mysql_error($conn));
                                    exit(0);
                                }

                    }	
       }
    //============================return message
    //status=3
            if($status==3)
            {	
               echo " Gagal: Data has been posted";
               exit(0);
            }
    //============================return message
    //status=4
            if($status==4)
            {	
               echo " Gagal: Company code of the Recipient is not defined";
               exit(0);
            }
    //===========delete ==========================
    //status=5
            if($status==5)
            { //delete item not header		   	 
               $str="delete from ".$dbname.".log_transaksidt where kodebarang='".$kodebarang."'
                     and notransaksi='".$nodok."' and kodeblok='".$blok."'";	 
               mysql_query($str);
               if(mysql_affected_rows($conn)>0)
               {		
               }		 
            }

        break;
		
		#ind update
        case'detailLog':
            //limit/page
            $limit=20;
            $page=0;
            $add="right(nomris,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
                       and tipetransaksi=5";
             if($param['tex']!=''){
                    $notransaksi=$_POST['tex']."%";
					//$notransaksi=$_POST['tex']."%-";
                    $add.=" and notransaksi like '".$notransaksi."'";
              } 
              if($param['kdGudng']!=''){
                  $add.=" and kodegudang= '".$param['kdGudng']."'";
              }
			  
			 // echo $add;
			  
            //ambil jumlah baris dalam tahun ini
            $str="select count(*) as jlhbrs from ".$dbname.".log_transaksiht where ".$add."		
                  order by jlhbrs desc";
           
            $res=mysql_query($str);
            while($bar=mysql_fetch_object($res)){
                    $jlhbrs=$bar->jlhbrs;
            }		
            //==================
              if(isset($_POST['page'])){
                            $page=$_POST['page'];
                        if($page<0)
                              $page=0;
              }
              $offset=$page*$limit;

            $tab.="<table class=sortable cellspacing=1 border=0><thead>
                   <tr class=rowheader>
                   <td>No.</td>
                   <td>".$_SESSION['lang']['sloc']."</td>
                   <td>".$_SESSION['lang']['tipe']."</td>
                   <td>".$_SESSION['lang']['momordok']."</td>
                   <td>".$_SESSION['lang']['nomris']."</td>
                   <td>".$_SESSION['lang']['tanggal']."</td>
                   <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
                   <td>".$_SESSION['lang']['untukunit']."</td>	  	 
                   <td>".$_SESSION['lang']['dbuat_oleh']."</td>
                   <td>".$_SESSION['lang']['posted']."</td>
                   <td></td></tr></head><tbody>";
                $sdta="select * from ".$dbname.".log_transaksiht where ".$add."
                       order by notransaksi desc limit ".$offset.",20";
				
				
					   
                $qdta=mysql_query($sdta) or die(mysql_error($conn));
                while($rdta=mysql_fetch_assoc($qdta)){
                    $no+=1;
                $tab.="<tr class=rowcontent>
                       <td>".$no."</td>
                       <td>".$rdta['kodegudang']."</td>
                       <td>".$rdta['tipetransaksi']."</td>
                       <td>".$rdta['notransaksi']."</td>
                       <td>".$rdta['nomris']."</td>
                       <td>".$rdta['tanggal']."</td>
                       <td>".$rdta['kodept']."</td>
                       <td>".$rdta['untukunit']."</td>	  	 
                       <td>".$optNmKary[$rdta['user']]."</td>
                       <td>".$optNmKary[$rdta['posted']]."</td>";
                       $er="";
                       if($rdta['post']==0){
                           $er="<img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delXBapb('".$rdta['notransaksi']."','".$rdta['nomris']."');\">&nbsp ";
                       }
                  $tab.="<td>".$er." <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewBast('".$rdta['notransaksi']."',event);\"> </td>
                        </tr>";
            }
            
                   
            $tab.="</tbody>
                   <tfoot>
                    <tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>
                   </tfoot>
                   </table>";
            echo $tab;
        break;
        case'delData':
           $sdet="select distinct kodebarang,jumlah from ".$dbname.".log_transaksidt where notransaksi='".$param['notransaksi']."'";
           $qdet=mysql_query($sdet) or die(mysql_error());
           while($rdata=  mysql_fetch_assoc($qdet)){
               $wr="kodebarang='".$rdata['kodebarang']."' and notransaksi='".$param['nomris']."'";
               $optJmlh=makeOption($dbname, 'log_mrisdt', 'kodebarang,jumlahrealisasi', $wr);
               $er=$optJmlh[$rdata['kodebarang']]-$rdata['jumlah'];
               $sup="update ".$dbname.".log_mrisdt set jumlahrealisasi=".$er." where ".$wr."";
               if(!mysql_query($sup)){
                   exit("error: db error".mysql_error()."___".$sup);
               }
           }
           $sdel="delete from ".$dbname.".log_transaksidt where notransaksi='".$param['notransaksi']."'";
           if(!mysql_query($sdel)){
                   exit("error: db error".mysql_error()."___".$sdel);
           }else{
               $sdel="delete from ".$dbname.".log_transaksiht where notransaksi='".$param['notransaksi']."'";
               if(!mysql_query($sdel)){
                   exit("error: db error".mysql_error()."___".$sdel);
               }
           }  
        break;
        case'getPostDt': 
//            $whr="tanggal='".tanggaldgnbar($param['tanggal'])."' and nomris='".$param['notransaksi']."'";
//            $optNotran=makeOption($dbname, 'log_transaksiht', 'nomris,notransaksi',$whr);
//            if($optNotran[$param['notransaksi']]!=''){
//                $sDet="select distinct * from ".$dbname.".log_transaksidt where notransaksi='".$optNotran[$param['notransaksi']]."'";
//            }else{
                $sDet="select distinct * from ".$dbname.".log_mrisdt where notransaksi='".$param['notransaksi']."'";
           // }
            $qDet=mysql_query($sDet) or die(mysql_error($conn));
            while($rDet=  mysql_fetch_assoc($qDet)){

                $sht="select distinct jumlahrealisasi,jumlah from ".$dbname.".log_mrisdt 
                      where notransaksi='".$param['notransaksi']."' and kodebarang='".$rDet['kodebarang']."' "
                        . "and kodemesin='".$rDet['kodemesin']."' and kodeblok='".$rDet['kodeblok']."'";
                $qht=mysql_query($sht) or die(mysql_error($conn));
                $rht=mysql_fetch_assoc($qht);
                $no+=1;
                $tab.="<tr class=rowcontent>";
                $tab.="<td id=kdBrg_".$no.">".$rDet['kodebarang']."</td>";
                $tab.="<td id=nmBrg_".$no.">".$optNmBrg[$rDet['kodebarang']]."</td>";
                $tab.="<td id=satBrg_".$no.">".$optSatBrg[$rDet['kodebarang']]."</td>";
                $tab.="<td id=kdBlok_".$no.">".$rDet['kodeblok']."</td>";
                $tab.="<td id=kdMesin_".$no.">".$rDet['kodemesin']."</td>
                <td id=jmlh_".$no." align=right>".number_format($rht['jumlah'],2)."</td>";
                $tab.="<td id=realisasiSblm_".$no."  align=right>".number_format($rht['jumlahrealisasi'],2)."</td>";
                if($optNotran[$param['notransaksi']]!=''){
                    $rDet['jumlah']=$rDet['jumlah'];
                }else{
                    $rDet['jumlah']=0;
                }
                $tab.="<td  align=right>
                       <input type=text  id=jmlhPengeluara_".$no." onkeypress='return angka_doang(event)' onblur=cekIsi(".$no.") style='width:100px' value='".$rDet['jumlah']."' class=myinputtextnumber />
                       <input type=hidden  id=kegId_".$no." value='".$rDet['kodekegiatan']."' />
                       </td>";
                $tab.="<td align=center><img src=images/save.png class=resicon style='cursor:pointer' onclick=saveDt(".$no.",'".$param['notransaksi']."') title='".$_SESSION['lang']['save']." ".$optNmBrg[$rDet['kodebarang']]."' /></td></tr>";
            }
            echo $tab;
        break;
    }
}else{
     echo " Error: Transaction Period missing";
}  
?>