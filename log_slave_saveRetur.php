<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//====================================
//default setting on database 
//1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi 
$tipetransaksi=2;
//=============================================

if(isTransactionPeriod())//check if transaction period is normal
{
        $nodok=$_POST['nodok'];
        $nomorlama=$_POST['nomorlama'];
        $idsupplier=$_POST['untukpt'];
        $tanggal=tanggalsystem($_POST['tanggal']);
        $nofaktur='';
        $nosj='';
        $qty=$_POST['jlhretur'];
        $kodebarang=$_POST['kodebarang'];
        $kodegudang=$_POST['gudang'];
        $kodept=$_POST['kodept'];
        $untukunit=$_POST['untukunit'];
        $hargasatuan=$_POST['hargasatuan'];
        $kodeblok=$_POST['kodeblok'];
        $post=0;
        $keterangan=$_POST['keterangan'];
        $user=$_SESSION['standard']['userid'];
        $satuan=$_POST['satuan'];//satuan pada master barang
	   
//==================ambil jumlah lalu====================
     $jumlahlalu=0;   
//===============================================================		 		  
  //periksa apakah sudah ada status 
    $stro="select * from ".$dbname.".setup_periodeakuntansi 
            where kodeorg='".$kodegudang."' and periode='".substr($tanggal,0,7)."'
            and tutupbuku=1"; 
    $reso=mysql_query($stro);
    if(mysql_num_rows($reso)>0)
    {
            $status=7;
            echo " Error :".$_SESSION['lang']['tanggaltutup'];
            exit(0);
    }	   


//ambil kegiatan
  $str="select kodekegiatan,kodemesin from ".$dbname.".log_transaksidt where notransaksi='".$nomorlama."' 
        and kodebarang='".$kodebarang."' and kodeblok='".$kodeblok."'";
  $res=mysql_query($str);
  $kodekegiatan='';
  $kodemesin='';  
 while($bar=mysql_fetch_object($res))
 {
     $kodekegiatan=$bar->kodekegiatan;
     $kodemesin=$bar->kodemesin;
 }
  
//=============================start input/update	

        $str="insert into ".$dbname.".log_transaksiht (
                `tipetransaksi`,`notransaksi`,`tanggal`,
                `kodept`,`nopo`,`nosj`,`kodegudang`,`user`,
                `idsupplier`,`nofaktur`,`post`,`untukunit`,
                `keterangan`,`notransaksireferensi`)
        values(".$tipetransaksi.",'".$nodok."',".$tanggal.",
                '".$kodept."','','".$nosj."','".$kodegudang."',".$user.",
                    '".$idsupplier."','".$nofaktur."',".$post.",'".$untukunit."',
                    '".$keterangan."','".$nomorlama."'
        )";	
        if(mysql_query($str))//insert hedaer
        {
                $str="insert into ".$dbname.".log_transaksidt (
                    `notransaksi`,`kodebarang`,
                    `satuan`,`jumlah`,`jumlahlalu`,
                    `hargasatuan`,`updateby`,`kodeblok`,
                    `hargarata`,kodemesin,kodekegiatan)
                    values('".$nodok."','".$kodebarang."',
                    '".$satuan."',".$qty.",".$jumlahlalu.",
                    0,".$user.",
                    '".$kodeblok."',0,'".$kodemesin."','".$kodekegiatan."')";
                if(mysql_query($str))//insert detail
                {	
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
                }		
  
	
}
else
{
    echo " Error: Transaction Period missing";
}
?>