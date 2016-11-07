<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//====================================
//default setting on database 
//1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi 
$tipetransaksi=3;
//=============================================

if(isTransactionPeriod())//check if transaction period is normal
{
        $nodok=$_POST['nodok'];
            $kodebarang=$_POST['kodebarang'];
            $tanggal=tanggalsystem($_POST['tanggal']);	
            $gudangx=$_POST['gudangx'];		
            $satuan=$_POST['satuan'];
            $jumlah=$_POST['jumlah'];		
            $kodegudang=$_POST['kodegudang'];
            $referensi=$_POST['referensi'];				
            $pemilikbarang=$_POST['pemilikbarang'];
            $post=0;
            $user=$_SESSION['standard']['userid'];
	//1 cek apakah sudah terekan di header
	//status=0 belum ada apa2
	//status=1 ada header
	//status=2 ada detail dan header
	//status=3 sudah di posting
	//status=7 sudah ada yang diposting pada tanggal yang lebih besar dengan barang yang sama dan pt yang sama
	
	  $status=0;
	 $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
	 $res=mysql_query($str);
	 if(mysql_num_rows($res)==1)
	 {
	 	$status=1;
	 }
	 
	 $str="select * from ".$dbname.".log_transaksidt where notransaksi='".$nodok."'
	       and kodebarang='".$kodebarang."'";
	 if(mysql_num_rows(mysql_query($str))>0)
	 {
	 	$status=2;
	 }	 
	 
	 $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'
	       and post=1";
	 if(mysql_num_rows(mysql_query($str))>0)
	 {
	 	$status=3;
	 }	
	 	 
	//get other data 

//harga satuan ==============================

   $strx="select a.hargarata from ".$dbname.".log_transaksidt a
            left join ".$dbname.".log_transaksiht b 
            on a.notransaksi=b.notransaksi
            where a.kodebarang='".$kodebarang."'
            and a.notransaksi='".$referensi."'
            and  b.tipetransaksi=7
            order by a.notransaksi desc limit 1";  
   		          
   $hargasatuan=0;
   $resx=mysql_query($strx);
   while($barx=mysql_fetch_object($resx))
   {
        $hargasatuan=$barx->hargarata;
   }

   if($hargasatuan==0 or $hargasatuan=='')
   {
   	  echo " Error: Price is 0 on :".$referensi;
	  exit(0);
   }

	   
//==================ambil jumlah lalu====================
     $jumlahlalu=0;
        $str="select a.jumlah as jumlah,a.notransaksi as notransaksi 
                from ".$dbname.".log_transaksidt a,
                ".$dbname.".log_transaksiht b
                where a.notransaksi=b.notransaksi
                and a.kodebarang='".$kodebarang."'
                and a.notransaksi<='".$nodok."'
                and b.kodegudang='".$kodegudang."'
                order by notransaksi desc limit 1";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                        $jumlahlalu=$bar->jumlah;
                }	   

//=============================start input/update	
//status=0
    if($status==0)
    {
            //get kode pt penerima barang
            $sKdPt="select distinct induk from ".$dbname.".organisasi where kodeorganisasi='".substr($kodegudang,0,4)."'";
            $qKdPt=mysql_query($sKdPt) or die(mysql_error($sKdPt));
            $rKdpt=mysql_fetch_assoc($qKdPt);
            if($rKdpt['induk']=='')
            {
                exit("Kode PT Penerima Kosong");
            }
                $str="insert into ".$dbname.".log_transaksiht (
                        `tipetransaksi`,`notransaksi`,`tanggal`,
                        `kodept`,`kodegudang`,`user`,
                        `gudangx`,`notransaksireferensi`,`post`)
                values(".$tipetransaksi.",'".$nodok."',".$tanggal.",
                        '".$rKdpt['induk']."','".$kodegudang."',".$user.",
                            '".$gudangx."','".$referensi."',".$post."
                )";	
                if(mysql_query($str))//insert hedaer
                {
                        //update sumber pada pengeluaran mutasi
                        $str="update ".$dbname.".log_transaksiht 
                                set notransaksireferensi='".$nodok."'
                                    where notransaksi='".$referensi."'
                                    and kodegudang='".$gudangx."'";   
                        if(mysql_query($str))
                        {}
                        else
                        {
                                echo " Gagal, (update reference on status 0)".addslashes(mysql_error($conn));
                                $str="delete from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
                                mysql_query($str);
                        }	   

                        $str="insert into ".$dbname.".log_transaksidt (
                            `notransaksi`,`kodebarang`,
                            `satuan`,`jumlah`,`jumlahlalu`,hargasatuan)
                            values('".$nodok."','".$kodebarang."',
                            '".$satuan."',".$jumlah.",".$jumlahlalu.",".$hargasatuan.")";
                        if(mysql_query($str))//insert detail
                        {	
                            //update PO jumlah masuk pada posting

                        }   
                        else
                        {
                            echo " Gagal, (insert detail on status 0)".addslashes(mysql_error($conn));
                           $str="delete from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
                           mysql_query($str);
                        }	
                }
                else
                        {
                            echo " Gagal,  (insert header on status 0)".addslashes(mysql_error($conn));
                           $str="delete from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
                           mysql_query($str);
                        }		
	}
//============================
//status=1
	if($status==1)
	{
                    $str="insert into ".$dbname.".log_transaksidt (
                        `notransaksi`,`kodebarang`,
                        `satuan`,`jumlah`,`jumlahlalu`,hargasatuan)
                        values('".$nodok."','".$kodebarang."',
                        '".$satuan."',".$jumlah.",".$jumlahlalu.",".$hargasatuan.")";
                    if(mysql_query($str))//insert detail
                    {	
                    }   
                    else
                    {
                        echo " Gagal, (insert detail on status 1)".addslashes(mysql_error($conn));
                        $str="delete from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
                           mysql_query($str);
                    }	
	}	

//============================return message
//status=3
	if($status==3)
	{	
	   echo " Gagal: Data has been posted";
	}	
}
else
{
	echo " Error: Transaction Period missing";
}
?>