<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
//====================================
//default setting on database 
//1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,
//5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi 
$tipetransaksi=5;
//=============================================

if(isTransactionPeriod())//check if transaction period is normal
{
		$nodok		=$_POST['nodok'];
		$tanggal	=tanggalsystem($_POST['tanggal']);
		$kodebarang	=$_POST['kodebarang'];
		$penerima	=$_POST['penerima'];
		$supplier	=$_POST['supplier'];
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
		$post=0;
	//pastikan kodeblok terisi
	if($blok=='')
	   $blok=$subunit;
	if($blok=='')
	   $blok=$untukunit;
	    			

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
         $user1=$_SESSION['standard']['userid'];
         if($_POST['statInputan']=='0'){
                 $antri=0;
                 while($antri==0){
                     $str="select user from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
                     $res=mysql_query($str) or die(mysql_error($conn));
                     if(mysql_num_rows($res)==1){
                         $antri=1;
                         $num=1;//default value 
                         $str="select max(notransaksi) notransaksi from ".$dbname.".log_transaksiht where tipetransaksi>4 and tanggal>=".$_SESSION['gudang'][$gudang]['start']." and tanggal<=".$_SESSION['gudang'][$gudang]['end']."
                                and kodegudang='".$gudang."' order by notransaksi desc limit 1";	
                         if($_SESSION['empl']['tipelokasitugas']=='KEBUN'){
                              $str="";
                              $str="select max(notransaksi) notransaksi from ".$dbname.".log_transaksiht where tipetransaksi>4 and tanggal>=".$_SESSION['gudang'][$gudang]['start']." and tanggal<=".$_SESSION['gudang'][$gudang]['end']."
                                    and kodegudang='".$gudang."' and substr( `notransaksi` , 7, 1 ) not like '%M%' order by notransaksi desc limit 1";	
                          }
                          if($res=mysql_query($str)){
                             while($bar=mysql_fetch_object($res)){
                                 $num=$bar->notransaksi;
                                 if($num!=''){
                                    $num=intval(substr($num,6,5))+1;
                                 }	else{
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
                            $nodok=$_SESSION['gudang'][$gudang]['tahun'].$_SESSION['gudang'][$gudang]['bulan'].$num."-GI-".$gudang;

                          }
                     }else{
                         $antri=1;
                     }
                 }
         }else{
             $status=1;
         }
         
//	 $str="select user from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'"; //script sblm di update jamhari
//	 $res=mysql_query($str);
//	 if(mysql_num_rows($res)==1)
//	 {
//                while($bar=mysql_fetch_object($res)){
//                  $user1=$bar->user;                  
//                }
//          if($_SESSION['standard']['userid']==$user1){
//            $status=1;
//          }      
//          else{
//            exit('Error: This transaction belongs to other user, please reload and start over');
//          }
//                
//	 }
	 
//	 $str="select * from ".$dbname.".log_transaksidt where notransaksi='".$nodok."'
//	       and kodebarang='".$kodebarang."'
//		   and kodeblok='".$blok."'";
//	 if(mysql_num_rows(mysql_query($str))>0)
//	 {
	 	if($method=='update')
		   $status=2;
//	 }	 
	 
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

#update ind drari pak gin
 if($status==5 or $status==2 or $status==1){
        $str="select * from ".$dbname.".log_transaksidt where notransaksi='".$nodok."' and statussaldo=1";
        if(mysql_num_rows(mysql_query($str))>0){
           $status=3;  
           exit(" Error, transaksi sudah dalam proses posting");
        }
    }
#tutup update ind	 	

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

        //pengecekan kuota bensin
        if ($kodebarang=='01000001'){
            $strkuota="select jumlah from ".$dbname.".log_5kuotabensin
                   where karyawanid='".$penerima."'";
            $kuota=fetchData($strkuota);

            if ($kuota[0]['jumlah']!='' and $kuota[0]['jumlah']>0){
                $strPer="select * from ".$dbname.".setup_periodeakuntansi
                       where kodeorg='".$gudang."' and tanggalmulai<='".$tanggal."' and tanggalsampai>='".$tanggal."'";
                $per=fetchData($strPer);
                $strcek="select sum(jumlah) as jumlah from ".$dbname.".log_transaksi_vw
                       where kodebarang='01000001' and namapenerima='".$penerima."' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."'";
                $terpakai=fetchData($strcek);
                $tpakai=($terpakai[0]['jumlah']!='')?$terpakai[0]['jumlah']:0;

                $totalbensin=$qty+$tpakai;
                if (($totalbensin>$kuota[0]['jumlah']) and substr($kegiatan,0,1)=='7'){
                     echo "Error\n\r";
                     echo "Nama penerima: ".getNamaKaryawan($penerima)."\n";
                     echo "Kuota setiap bulan: ".$kuota[0]['jumlah']." LITER\n";
                     echo "Pemakaian bensin bulan ini: ".$tpakai." LITER\n";
                     echo "Bensin yang akan terpakai (".$totalbensin." LITER) telah melebihi kuota\n";
                     echo "Gunakan jenis kegiatan PINJAMAN BBM DAN ALAT KERJA";
                     exit(0);
                }
            }
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
			  `namapenerima`,`idsupplier`,`untukunit`,`post`)
		values(".$tipetransaksi.",'".$nodok."',
		       ".$tanggal.",'".$pemilikbarang."',
			  '".$ptpemintabarang."','".$catatan."',
			  '".$gudang."',".$user.",
			  '".$penerima."','".$supplier."','".$untukunit."',".$post."
		)";	
		if(mysql_query($str))//insert hedaer
		{
			$str="insert into ".$dbname.".log_transaksidt (
			  `notransaksi`,`kodebarang`,
			  `satuan`,`jumlah`,`jumlahlalu`,
			  `kodeblok`,`updateby`,`kodekegiatan`,
			  `kodemesin`)
			  values('".$nodok."','".$kodebarang."',
			  '".$satuan."',".$qty.",".$jumlahlalu.",
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
            $scek="select distinct * from ".$dbname.".log_transaksiht where notransaksi='".$nodok."' and tipetransaksi=5";
            $qcek=mysql_query($scek) or die(mysql_error($conn));
            $rcek=mysql_num_rows($qcek);
            if($rcek==0){
                  exit('Error: This transaction belongs to other user, please reload and start over');
            }
			$str="insert into ".$dbname.".log_transaksidt (
			  `notransaksi`,`kodebarang`,
			  `satuan`,`jumlah`,`jumlahlalu`,
			  `kodeblok`,`updateby`,`kodekegiatan`,
			  `kodemesin`)
			  values('".$nodok."','".$kodebarang."',
			  '".$satuan."',".$qty.",".$jumlahlalu.",
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
			$str="update ".$dbname.".log_transaksidt set
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
		       echo $str." Gagal, (update detail on status 2)".addslashes(mysql_error($conn));
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
//===========delete ==========================
//status=5
	if($status==5)
	{ //delete item not header		   	 
	   $str="delete from ".$dbname.".log_transaksidt where kodebarang='".$kodebarang."'
	         and notransaksi='".$nodok."' and kodeblok='".$blok."' and kodemesin='".$_POST['kdmesin']."'";	 
	   mysql_query($str);
	   if(mysql_affected_rows($conn)>0)
	   {		
	   }		 
	}
	
//ambil data untuk ditampilkan
 $strj="select a.*,b.untukpt as pt,
        b.untukunit as unit from ".$dbname.".log_transaksidt a 
		left join  ".$dbname.".log_transaksiht b
		on a.notransaksi=b.notransaksi
        where a.notransaksi='".$nodok."'";	
 $resj=mysql_query($strj);
 $no=0;
 while($barj=mysql_fetch_object($resj))
   {
	$no+=1;
	//ambil namabarang
	$namabarangk='';
	$strk="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$barj->kodebarang."'";
	$resk=mysql_query($strk);
	while($bark=mysql_fetch_object($resk))
	{
		$namabarangk=$bark->namabarang;
	}
	//ambil kegiatan
	$namakegiatan='';
	$strk="select namakegiatan from ".$dbname.".setup_kegiatan where kodekegiatan='".$barj->kodekegiatan."'";
	$resk=mysql_query($strk);
	while($bark=mysql_fetch_object($resk))
	{
		$namakegiatan=$bark->namakegiatan;
	}	
	$stream.="<tr class=rowcontent>
		    <td>".$no."</td>
			<td>".$barj->kodebarang."</td>
			<td>".$namabarangk."</td>
			<td>".$barj->satuan."</td>
			<td align=right>".number_format($barj->jumlah,2,'.',',')."</td>
			<td>".$barj->pt."</td>
			<td>".$barj->unit."</td>
			<td>".$barj->kodeblok."</td>
			<td>".$namakegiatan."</td>
			<td>".$barj->kodemesin."</td>
			<td>
			    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editBast('".$barj->kodebarang."','".$namabarangk."','".$barj->satuan."','".$barj->jumlah."','".$barj->kodeblok."','".$barj->kodekegiatan."','".$barj->kodemesin."');\">
		        &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delBast('".$nodok."','".$barj->kodebarang."','".$barj->kodeblok."','".$barj->kodemesin."');\">
			</td>
 		   </tr>";
   }
   if(($status==6)||($status==5)){
    echo $stream;    
   }else{
    echo $stream."####".$nodok;
   }
}
else
{
	echo " Error: Transaction Period missing";
}
?>