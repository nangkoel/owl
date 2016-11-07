<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//====================================
//default setting on database 
//1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi 
$tipetransaksi=6;
//=============================================

if(isTransactionPeriod())//check if transaction period is normal
{
            $nodok=$_POST['nodok'];
            $nomorlama=$_POST['nomorlama'];
            $tanggal=tanggalsystem($_POST['tanggal']);
            $nofaktur='';
            $nosj='';
            $qty=$_POST['jlhretur'];
            $kodebarang=$_POST['kodebarang'];
            $kodegudang=$_POST['gudang'];
            $kodept=$_POST['kodept'];
            $untukunit=$_POST['untukunit'];
            $hargasatuan=$_POST['hargasatuan'];
            $post=0;
            $keterangan=$_POST['keterangan'];
            $user=$_SESSION['standard']['userid'];
            $satuan=$_POST['satuan'];//satuan pada master barang
            $supplierid=$_POST['supplierid'];
            $nopo=$_POST['nopo'];

    //1 cek apakah sudah terekan di header
    //status=0 belum ada apa2
    //status=1 ada header
    //status=2 ada detail dan header
    //status=3 sudah di posting
    //status=7 sudah ada yang diposting pada tanggal yang lebih besar dengan barang yang sama dan pt yang sama

   
	 	 
if($hargasatuan=='')
   $hargasatuan=0;
	   
	 		  
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

//INSERT
    $str="insert into ".$dbname.".log_transaksiht (
        tipetransaksi, 
        notransaksi, 
        tanggal, 
        kodept,  
        nopo, 
        keterangan, 
        statusjurnal, 
        kodegudang, 
        user, 
        idsupplier, 
        post, 
        postedby, 
        notransaksireferensi)
        values(
        ".$tipetransaksi.",    
        '".$nodok."', 
        ".$tanggal.",
        '".$kodept."',
        '".$nopo."',
        '".$keterangan."',  
        0,
        '".$kodegudang."',
        ".$user.",
        '".$supplierid."',         
        0,
        0,        
        '".$nomorlama."'           
        );";
    
       $str2="insert into ".$dbname.".log_transaksidt (
        notransaksi, 
        kodebarang, 
        satuan, 
        jumlah, 
        jumlahlalu, 
        hargasatuan, 
        updateby, 
        statussaldo, 
        hargarata)
            values(
        '".$nodok."',         
        '".$kodebarang."',    
        '".$satuan."', 
        '".$qty."',    
        0,
        '".$hargasatuan."',    
        ".$user.",
        0,
        0)";	
   if(mysql_query($str))
   {
        if(mysql_query($str2))
        {

        }
        else {//hapus header maka detail juga terhapus
            echo "Error detail ".addslashes(mysql_error($conn));
            $str="delete from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
            mysql_query($str);
        }
   }
   else
   {//hapus header maka detail juga terhapus
            echo "Error header ".addslashes(mysql_error($conn)).$str;
    }   
}
else
{
	echo " Error: Transaction Period missing";
}
?>