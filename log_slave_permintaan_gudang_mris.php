<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


if(isTransactionPeriod()){//check if transaction period is normal
    $param=$_POST;
    switch($param['proses']){
        case'getNotrans':
              $gudang	=$param['gudang'];
              $num=1;//default value 
              $str="select max(notransaksi) as notransaksi from ".$dbname.".log_mrisht where tipetransaksi>4 and tanggal>=".$_SESSION['gudang'][$gudang]['start']." and tanggal<=".$_SESSION['gudang'][$gudang]['end']."
                   and right(notransaksi,4)='".$_SESSION['empl']['lokasitugas']."'
                   order by notransaksi desc limit 1";	
              if($res=mysql_query($str))
              {
                    while($bar=mysql_fetch_object($res))
                    {
                            $num=$bar->notransaksi;
                            if($num!='')
                            {
                                    $num=intval(substr($num,8,5))+1;
                            }	
                            else
                            {
                                    $num=1;
                            }
                    }
                    //exit("error:".$num);
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
               
                    $tgld=date('Ymd');
                    $num=$tgld.$num."-".$_SESSION['empl']['lokasitugas'];
              echo $num;
              }	
              else
                    {
                            echo " Gagal,".addslashes(mysql_error($conn));
                    }	
        break;
        case'simpan':
                    $nodok		=$_POST['nodok'];
                    $tanggal	=tanggalsystem($_POST['tanggal']);
                    $kodebarang	=$_POST['kodebarang'];
                    $penerima	=$_POST['penerima'];
                    $satuan		=$_POST['satuan'];
                    $qty		=$_POST['qty'];
                    $blok		=$_POST['blok'];
                    $mesin		=$_POST['mesin'];
                    $untukunit	=$_POST['untukunit'];
                    $subunit	=$_POST['subunit'];
                    $gudang		=$_POST['gudang'];
                    $catatan	=$_POST['catatan'];
                    $kegiatan	=$_POST['kegiatan'];
                    $method		=$_POST['method'];
                    $pemilikbarang=$_POST['pemilikbarang'];        
                    $user		=$_SESSION['standard']['userid'];
                    $whrdt="char_length(kodeorganisasi)=4";
                    $optUtk=makeOption($dbname, 'organisasi', 'kodeorganisasi,induk',$whrdt);
                    $satuanbarang=getSatuanBarang($kodebarang);
					/*if($subunit!=''){
                        $untukunit=$subunit;
                    }*/
                    
					if($subunit!=''){
						if(substr($subunit,0,2)=='AK' or substr($subunit,0,2)=='PB')
						{
							$untukunit=$untukunit;
						}
						else
						{
                        	$untukunit=$subunit;
						}
					}
					
					
					$post=0;
                    //pastikan kodeblok terisi
                    if($blok=='')
                    $blok=$subunit;
                    if($blok=='')
                    $blok=$untukunit;
                    $tipetransaksi=5;

                    //exit("error:".$status);
                    //1 cek apakah sudah terekan di header
                    //status=0 belum ada apa2
                    //status=1 ada header
                    //status=2 ada detail dan header
                    //status=3 sudah di posting
                    //status=4 kode pt penerima barang tidak ada
                    //status=5 delete item
                    //status=6 display only
                    //status=7 sudah ada yang diposting pada tanggal yang lebih besar dengan barang yang sama dan pt yang sama
                    //exit("error:".$method);
                    $status=0;
                    if($_POST['statInputan']=='0'){
                             $antri=0;
                             while($antri==0){
                                 $str="select * from ".$dbname.".log_mrisht where notransaksi='".$nodok."'";
                                 $res=mysql_query($str) or die(mysql_error($conn));
                                 if(mysql_num_rows($res)==1){
                                     $antri=1;
                                     $num=1;//default value 
                                     $str="select max(notransaksi) as notransaksi from ".$dbname.".log_mrisht where tipetransaksi>4 and tanggal>=".$_SESSION['gudang'][$gudang]['start']." and tanggal<=".$_SESSION['gudang'][$gudang]['end']."
                                           and right(notransaksi,4)='".$_SESSION['empl']['lokasitugas']."'
                                           order by notransaksi desc limit 1";	
                                     //exit("error".$str);
                                      if($res=mysql_query($str)){
                                         while($bar=mysql_fetch_object($res)){
                                                    $num=$bar->notransaksi;
                                                    if($num!='')
                                                    {
                                                            $num=intval(substr($num,8,5))+1;
                                                    }	
                                                    else
                                                    {
                                                            $num=1;
                                                    }
                                            }
                                           // exit("error:".$num);
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
                                        $tgld=date('Ymd');
                                        $nodok=$tgld.$num."-".$_SESSION['empl']['lokasitugas'];
                                        $str="select * from ".$dbname.".log_mrisht where notransaksi='".$nodok."'";
                                        $res=mysql_query($str) or die(mysql_error($conn));
                                        if(mysql_num_rows($res)==1){
                                            $antri=0;
                                        }
                                        //exit("error:".$nodok);
                                      }
                                 }else{
                                     $antri=1;
                                 }
                             }
                    }else{
                        $status=1;
                    }
                     
                   
                    if($method=='update')
                       $status=2;
                    //	 }	 

                    if(isset($_POST['delete']))
                    {
                      $status=5;
                    }	

                    $str="select * from ".$dbname.".log_mrisht where notransaksi='".$nodok."'
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
                    //$str="select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi,a.waktutransaksi 
					$str="select a.jumlah as jumlah,a.notransaksi as notransaksi,a.waktutransaksi 
                    from ".$dbname.".log_mrisdt a,
                     ".$dbname.".log_mrisht b
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
                    $str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_mrisht a left join ".$dbname.".log_mrisdt
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
                    $str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_mrisht a left join ".$dbname.".log_mrisdt
                    b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$kodebarang."' 
                       and a.tipetransaksi>4
                       and a.kodegudang='".$gudang."'
                       and a.post=0		   
                       group by kodebarang";
                    //echo $str2;
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
                            echo " Error:\n".strtoupper($_SESSION['lang']['saldo'])." ".strtoupper($_SESSION['lang']['tidakcukup'])."\n\n";
                            $str2="select a.notransaksi,jumlah FROM ".$dbname.".log_mrisht a left join ".$dbname.".log_mrisdt
                            b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$kodebarang."' 
                               and a.tipetransaksi>4
                               and a.kodegudang='".$gudang."'
                               and a.post=0";
                            $res2=mysql_query($str2);
                            if (mysql_num_rows($res2)>0){
                                echo "Berikut MRIS untuk barang yang sama yang belum diproses:\n";
                                while($bar2=mysql_fetch_object($res2))
                                {
                                    echo $bar2->notransaksi." : ".$bar2->jumlah." ".$satuanbarang."\n";
                                }
                                echo "\nHarap diproses terlebih dahulu.\n";
                            }
                            $status=6;//status ngeles
                            exit(0);		
                        }
                    } 
                    else if($status==2)
                    {
                    //ambil jumlah lama dan bandingkan dengan qty kemudian bandingkan dengan saldo
                    $jlhlama=0;
                    $strt="select jumlah from ".$dbname.".log_mrisdt where notransaksi='".$nodok."'
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
                    $stro="select a.post from ".$dbname.".log_mrisht a
                    left join ".$dbname.".log_mrisdt b
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
                    $str="insert into ".$dbname.".log_mrisht (
                              `tipetransaksi`,`notransaksi`,
                              `tanggal`,`keterangan`,
                              `kodegudang`,`kodept`,`untukpt`,`dibuat`,
                              `mengetahui`,`untukunit`,`post`)
                    values(".$tipetransaksi.",'".$nodok."',
                           ".$tanggal.",'".$catatan."',
                              '".$gudang."','".$pemilikbarang."','".$optUtk[substr($untukunit,0,4)]."','".$user."',
                              '".$penerima."','".$untukunit."',".$post."
                    )";	
                    
                    if(mysql_query($str))//insert hedaer
                    {
                            $str="insert into ".$dbname.".log_mrisdt (
                              `notransaksi`,`kodebarang`,
                              `satuan`,`jumlah`,`jumlahrealisasi`,
                              `kodeblok`,`updateby`,`kodekegiatan`,
                              `kodemesin`)
                              values('".$nodok."','".$kodebarang."',
                              '".$satuan."',".$qty.",0,
                              '".$blok."','".$user."','".$kegiatan."',
                              '".$mesin."')";
                            if(mysql_query($str))//insert detail
                            {	
                              //update PO jumlah masuk

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
                            $str="insert into ".$dbname.".log_mrisdt (
                              `notransaksi`,`kodebarang`,
                              `satuan`,`jumlah`,`jumlahrealisasi`,
                              `kodeblok`,`updateby`,`kodekegiatan`,
                              `kodemesin`)
                              values('".$nodok."','".$kodebarang."',
                              '".$satuan."',".$qty.",0,
                              '".$blok."','".$user."','".$kegiatan."',
                              '".$mesin."')";
                            if(mysql_query($str))//insert detail
                            {	
                            }   
                            else
                            {
                         echo " Gagal, (insert detail on status 1)".addslashes(mysql_error($conn));
                             exit(0);
                            }	
                    }	
                    //============================update detail
                    //status=2
                    if($status==2)
                    {
                            $str="update ".$dbname.".log_mrisdt set
                                  `jumlah`=".$qty.",
                                      `updateby`=".$user.",
                                      `kodekegiatan`='".$kegiatan."',
                                      `kodemesin`='".$mesin."'
                                      where `notransaksi`='".$nodok."'
                                      and `kodebarang`='".$kodebarang."'
                                      and `kodeblok`='".$blok."'";
                            mysql_query($str);//insert detail
                            if(mysql_affected_rows($conn)<1)
                            {	
                           echo " Gagal, (update detail on status 2)".addslashes(mysql_error($conn));
                               exit(0);
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
                   //exit("error:".$status);
                    //===========delete ==========================
                    //status=5
                    if($status==5)
                    { //delete item not header		   	 
                    $str="delete from ".$dbname.".log_mrisdt where kodebarang='".$kodebarang."'
                     and notransaksi='".$nodok."' and kodeblok='".$blok."' and kodemesin='".$_POST['kdmesin']."'";
                    //exit("error:".$str);
                    mysql_query($str);
                    if(mysql_affected_rows($conn)>0)
                    {		
                    }		 
                    }

                    //ambil data untuk ditampilkan
                    $strj="select a.*,b.untukunit as unit from ".$dbname.".log_mrisdt a 
                    left join  ".$dbname.".log_mrisht b
                    on a.notransaksi=b.notransaksi
                    where a.notransaksi='".$nodok."'";	
                     
                    $resj=mysql_query($strj);
                    $no=0;
                    while($barj=mysql_fetch_object($resj))
                    {
                    $no+=1;
                    //ambil namabarang
                    $namabarangk='';
                    $strk="select namabarang,satuan from ".$dbname.".log_5masterbarang where kodebarang='".$barj->kodebarang."'";
                    $resk=mysql_query($strk);
                    while($bark=mysql_fetch_object($resk)){
                        $namabarangk=$bark->namabarang;
                    }
                    //ambil kegiatan
                    $namakegiatan='';
                    $strk="select namakegiatan from ".$dbname.".setup_kegiatan where kodekegiatan='".$barj->kodekegiatan."'";
                    $resk=mysql_query($strk);
                    while($bark=mysql_fetch_object($resk)){
                        $namakegiatan=$bark->namakegiatan;
                    }	
                    $brt=$barj->kodeblok;
                    if($barj->kodemesin!=''){
                        $brt=$barj->kodemesin;
                    }
                      
                    $stipe="select distinct tipe from ".$dbname.".organisasi where kodeorganisasi='".$brt."'";
                    $qtipe=mysql_query($stipe) or die(mysql_error($conn));
                    $rtipe=mysql_fetch_assoc($qtipe);
                    $tab.="<tr class=rowcontent>
                        <td>".$no."</td>
                            <td>".$barj->kodebarang."</td>
                            <td>".$namabarangk."</td>
                            <td>".$barj->satuan."</td>
                            <td align=right>".number_format($barj->jumlah,2,'.',',')."</td>
                            <td>".$barj->unit."</td>
                            <td>".$barj->kodeblok."</td>
                            <td>".$namakegiatan."</td>
                            <td>".$barj->kodemesin."</td>
                            <td>
                                <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editBast('".$barj->kodebarang."','".$namabarangk."','".$barj->satuan."','".$barj->jumlah."','".$barj->kodeblok."','".$barj->kodekegiatan."','".$barj->kodemesin."','".$rtipe['tipe']."');\">
                            &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delBast('".$nodok."','".$barj->kodebarang."','".$barj->kodeblok."','".$barj->kodemesin."');\">
                            </td>
                       </tr>";
                    }		
                    
                    if(($status==6)||($status==5)){
                        echo $tab;
                    }else{
                        echo $tab."####".$nodok;
                    }
        break;
        case'loadData':
                //limit/page
                $limit=20;
                $page=0;
                //========================
                $gudang=$_POST['gudang'];
                $add='';//default serach id nothing
                if(isset($_POST['tex']))
                {
                    $add=" and notransaksi like '".$_POST['tex']."%'";
                } 
                //ambil jumlah baris dalam tahun ini
                $str="select count(*) as jlhbrs from ".$dbname.".log_mrisht where kodegudang='".$gudang."'
                and tipetransaksi =5
                and right(notransaksi,4)='".$_SESSION['empl']['lokasitugas']."'  ".$add."		
                order by jlhbrs desc";
                //echo $str;
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                $jlhbrs=$bar->jlhbrs;
                }		
                //==================

                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                  $page=0;
                }


                $offset=$page*$limit;


                $str="select * from ".$dbname.".log_mrisht where kodegudang='".$gudang."' and tipetransaksi =5
                and right(notransaksi,4)='".$_SESSION['empl']['lokasitugas']."'   ".$add."
                order by notransaksi desc limit ".$offset.",20";
                //echo $str;
                $res=mysql_query($str);
                $no=$page*$limit;
                while($bar=mysql_fetch_object($res))
                {
                $no+=1;
                //====================ambil username pembuat
                $sblok="select distinct kodeblok from ".$dbname.".log_mrisdt where notransaksi='".$bar->notransaksi."'";
                $qblok=mysql_query($sblok) or die(mysql_error($conn));
                $rblok=mysql_fetch_assoc($qblok);
                $namapembuat='';
                $stry="select namauser from ".$dbname.".user where karyawanid=".$bar->dibuat;
                $resy=mysql_query($stry);
                while($bary=mysql_fetch_object($resy))
                {
                $namapembuat=$bary->namauser;
                }   
                //====================ambil username posting
                $namaposting='Not Posted';
                if(intval($bar->postedby)!=0)
                {
                  $stry="select namauser from ".$dbname.".user where karyawanid=".$bar->postedby;
                  $resy=mysql_query($stry);
                  while($bary=mysql_fetch_object($resy))
                  {
                        $namaposting=$bary->namauser;
                  }
                }

                if($namaposting=='Not Posted' && $bar->post==1)
                {
                $namaposting=" Posted By ???";
                }
                if($bar->post<1)
                {

                //tambahkan tombol edit dan delete
                $add="<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editXBast('".$bar->notransaksi."','".substr($rblok['kodeblok'],0,6)."','".tanggalnormal($bar->tanggal)."','".$bar->mengetahui."','".$bar->keterangan."');\">";
                $add.="&nbsp<img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delXBapb('".$bar->notransaksi."');\">
                       &nbsp<img src=images/hot.png class=resicon  title='posting' onclick=\"postingData('".$bar->notransaksi."','".$bar->kodegudang."');\">";

                //	    $add.="<img src=images/application/book_icon.gif class=resicon  title='Post/Close' onclick=\"postingBapb('".$bar->notransaksi."','".$bar->nopo."');\">";
                }  
                else
                {
                $add='';
                }			     

                echo"<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$bar->kodegudang."</td>
                <td title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
                <td>".$bar->notransaksi."</td>
                <td>".tanggalnormal($bar->tanggal)."</td>
                <td>".$bar->untukunit."</td>			  
                <td>".$namapembuat."</td>
                <td>".$namaposting."</td>
                <td align=center>
                ".$add."
                <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewBast('".$bar->notransaksi."',event);\"> 
                </td>
                </tr>";
                }
                echo"<tr><td colspan=11 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
                <br>
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
        break;
        case'getKegiatan':
		//exit("Error:MASUK");
            $blok=$_POST['blok'];
            $kdkeg=$_POST['kdkegiatan'];
            if($blok!='')
            { 	
                    $str="select tipe from ".$dbname.".organisasi where kodeorganisasi='".$blok."'";

                    $res=mysql_query($str);
                    $tipe=$_POST['jenis'];//Default untuk traksi
                    while($bar=mysql_fetch_object($res))
                    {
                            $tipe=$bar->tipe;
                    }
                    if($tipe=='STENGINE' or $tipe=='STATION')
                    {
                            $optKegiatan="<option value=''></option>";
                            $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
                                   where kelompok='MIL' order by kelompok,namakegiatan";
                            $resf=mysql_query($strf);
                            while($barf=mysql_fetch_object($resf)){
                                if($kdkeg==$barf->kodekegiatan){
                                    $optKegiatan.="<option value='".$barf->kodekegiatan."' selected>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                }else{
                                     $optKegiatan.="<option value='".$barf->kodekegiatan."'>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                }
                            } 
                            echo $optKegiatan;
                    }
                    else if($tipe=='BLOK')
                    {
                        $optSta=makeOption($dbname, 'setup_blok', 'kodeorg,statusblok');
                        if($optSta[$blok]=='TM'){
                            $str="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan where (kelompok='TM' or kelompok='PNN') order by kelompok,namakegiatan";
                        }else{
                            $str="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan where kelompok='".$optSta[$blok]."' order by kelompok,namakegiatan"; 
                        }
                        $resf=mysql_query($str);
                        while($barf=mysql_fetch_object($resf)){
                            if($kdkeg==$barf->kodekegiatan){
                                $optKegiatan.="<option value='".$barf->kodekegiatan."' selected>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                            }else{
                                 $optKegiatan.="<option value='".$barf->kodekegiatan."'>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                            }
                        } 
                        echo $optKegiatan;
                    }
                    else if($tipe=='WORKSHOP'){
                            $optKegiatan="<option value=''></option>";
                            $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
                                   where kelompok='WSH' order by kelompok,namakegiatan";	   
                            $resf=mysql_query($strf);
                            while($barf=mysql_fetch_object($resf))
                            {
                                    if($kdkeg==$barf->kodekegiatan){
                                        $optKegiatan.="<option value='".$barf->kodekegiatan."' selected>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                    }else{
                                         $optKegiatan.="<option value='".$barf->kodekegiatan."'>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                    }
                            } 
                            echo $optKegiatan;			
                    }
                    else if($tipe=='SIPIL'){
                            $optKegiatan="<option value=''></option>";
                            $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
                                   where kelompok='SPL' order by kelompok,namakegiatan";	   
                            $resf=mysql_query($strf);
                            while($barf=mysql_fetch_object($resf))
                            {
                                    if($kdkeg==$barf->kodekegiatan){
                                        $optKegiatan.="<option value='".$barf->kodekegiatan."' selected>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                    }else{
                                         $optKegiatan.="<option value='".$barf->kodekegiatan."'>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                    }
                            } 
                            echo $optKegiatan;			
                    }
                                            else if($tipe=='TRAKSI'){
                            $optKegiatan="<option value=''></option>";
                            $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
                                   where kelompok='TRK' order by kelompok,namakegiatan";	   
                            $resf=mysql_query($strf);
                            while($barf=mysql_fetch_object($resf))
                            {
                                     if($kdkeg==$barf->kodekegiatan){
                                        $optKegiatan.="<option value='".$barf->kodekegiatan."' selected>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                    }else{
                                         $optKegiatan.="<option value='".$barf->kodekegiatan."'>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                    }
                            } 
                            echo $optKegiatan;			
                    }
                    else if($tipe=='BIBITAN'){
                            $optKegiatan="<option value=''></option>";
                            $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
                                   where  kelompok in ('BBT','MN','PN') order by kelompok,namakegiatan";	   
                            $resf=mysql_query($strf);
                            while($barf=mysql_fetch_object($resf))
                            {
                                     if($kdkeg==$barf->kodekegiatan){
                                        $optKegiatan.="<option value='".$barf->kodekegiatan."' selected>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                    }else{
                                         $optKegiatan.="<option value='".$barf->kodekegiatan."'>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                    }
                            } 
                            echo $optKegiatan;			
                    }
				   else if(substr($blok,0,2)=='AK' or substr($blok,0,2)=='PB')
				   {
					   $tipeasset=substr($blok,3,3);
					   $tipeasset=  str_replace("0","", $tipeasset);
					   $str="select akunak,namatipe from ".$dbname.".sdm_5tipeasset where kodetipe='".$tipeasset."'"; 
					  // exit("Error:$str");   
					   $resf=mysql_query($str);
							 if(mysql_num_rows($resf)>0)
							 {
							 
								while($barf=mysql_fetch_object($resf))
								{//exit("Error:MASUK");
								/*		 if($kdkeg==$barf->kodekegiatan){
											$optKegiatan.="<option value='".$barf->kodekegiatan."' selected>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
										}else{
											 $optKegiatan.="<option value='".$barf->kodekegiatan."'>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
									}*/
								$optKegiatan.="<option value='".$barf->akunak."'>[Project]-".$barf->namatipe."</option>";
								} 
                            echo $optKegiatan; 
						   }
						   else
						   {
							   exit(" Error: Akun aktiva dalam kontruksi belum ditentukan untuk kode ".$tipeasset);
						   }    
					}
					else
					{
							$optKegiatan="<option value=''></option>";
							$strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
								   where kelompok='KNT' order by kelompok,namakegiatan";
							$resf=mysql_query($strf);
							while($barf=mysql_fetch_object($resf))
							{
										if($kdkeg==$barf->kodekegiatan){
											$optKegiatan.="<option value='".$barf->kodekegiatan."' selected>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
										}else{
											 $optKegiatan.="<option value='".$barf->kodekegiatan."'>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
										}
							} 
							echo $optKegiatan;                    
					}    

            }
            else
            {
                                $optKegiatan="<option value=''></option>";
                                $strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
                                       where kelompok='KNT' order by kelompok,namakegiatan";
                                $resf=mysql_query($strf);
                                while($barf=mysql_fetch_object($resf))
                                {
                                    if($kdkeg==$barf->kodekegiatan){
                                        $optKegiatan.="<option value='".$barf->kodekegiatan."' selected>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                    }else{
                                         $optKegiatan.="<option value='".$barf->kodekegiatan."'>[".$barf->kelompok."]-".$barf->namakegiatan."</option>";
                                    }
                                } 
                                echo $optKegiatan;		
            }
        break;
        case'hapustrans':
        $notransaksi	=$_POST['notransaksi'];
  
          $str="select post from ".$dbname.".log_mrisht where notransaksi='".$notransaksi."'";
          $res=mysql_query($str);
          $ststus=0;
          while($bar=mysql_fetch_object($res))
          { 
                $status=$bar->post;
          }
          if($status==1)
          {
                //block if posted
                echo " Gagal/Error, Document has been posted";
          }
          else
          {
                //delete detail first
                $str="delete from ".$dbname.".log_mrisdt where notransaksi='".$notransaksi."'";
                if(mysql_query($str))
                {
                //delete header
                $str="delete from ".$dbname.".log_mrisht where notransaksi='".$notransaksi."'";
                    mysql_query($str);
                }
          }    
            
        break;
        case'postingdata':
            $whrd="notransaksi='".$param['notransaksi']."'";
            $dcek=makeOption($dbname, 'log_mrisht','notransaksi,post', $whrd);
            if($dcek[$param['notransaksi']]==0){
                $supd="update ".$dbname.".log_mrisht set post=1,postedby='".$_SESSION['standard']['userid']."' where notransaksi='".$param['notransaksi']."'";
                if(!mysql_query($supd)){
                    exit("error: ".$supd."___".mysql_query($conn));
                }
            }else{
                exit("error: Already posted");
            }
        break;
    }
    
 
}
else
{
	echo " Error: Transaction Period missing";
}
?>