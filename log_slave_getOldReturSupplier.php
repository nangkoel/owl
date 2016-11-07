<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$nomorlama=$_POST['nomorlama'];
$kodebarang=$_POST['kodebarang'];
$kodegudang=$_POST['kodegudang'];

$str="select a.tipetransaksi,a.kodept,a.untukpt,a.untukunit,b.jumlah,b.satuan,b.hargasatuan,a.nopo,c.namasupplier,c.supplierid 
        from ".$dbname.".log_transaksidt b 
        left join ".$dbname.".log_transaksiht a on. a.notransaksi=b.notransaksi
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid    
        where a.tipetransaksi=1 and b.kodebarang='".$kodebarang."'
        and a.notransaksi='".$nomorlama."'
        and a.notransaksi like '%".$kodegudang."%'
        limit 1";
$res=mysql_query($str);
if(mysql_num_rows($res)>0)
{
    while($bar=mysql_fetch_object($res))
    {
            $namabarang='';
            //ger namabarang
            $strf="select namabarang from ".$dbname.".log_5masterbarang
                where kodebarang='".$kodebarang."'";
            $resf=mysql_query($strf);
            while($barf=mysql_fetch_object($resf))
            {
                    $namabarang=$barf->namabarang;
            }	 
            //ambil jumlah barang yang di retur ontuk PO yang sama dan barang yang sama
            $stam="select sum(jumlah) as jum from ".$dbname.".log_transaksi_vw where nopo='".$bar->nopo."'
                        and kodebarang='".$kodebarang."' and kodegudang='".$kodegudang."' and notransaksireferensi = '".$nomorlama."'
                        and tipetransaksi=6";

            $jam=0;
            $rem=mysql_query($stam);
            while($bam=mysql_fetch_object($rem))
            {
                $jam=$bam->jum;
            }
            $sis=$bar->jumlah-$jam;
            echo"<?xml version='1.0' ?>
                <oldoc>
                            <jumlah>".$sis."</jumlah>
                            <satuan>".($bar->satuan!=""?$bar->satuan:"*")."</satuan>
                            <namabarang>".($namabarang!=""?$namabarang:"*")."</namabarang>
                            <hargasatuan>".($bar->hargasatuan!=""?$bar->hargasatuan:"*")."</hargasatuan>
                            <kodept>".($bar->kodept!=""?$bar->kodept:"*")."</kodept>
                            <untukpt>".($bar->untukpt!=""?$bar->untukpt:"*")."</untukpt>
                            <untukunit>".($bar->untukunit!=""?$bar->untukunit:"*")."</untukunit>
                            <nopo>".($bar->nopo!=""?$bar->nopo:"*")."</nopo>
                            <namasupplier>".($bar->namasupplier!=""?$bar->namasupplier:"*")."</namasupplier>   
                            <kodesupplier>".($bar->supplierid!=""?$bar->supplierid:"*")."</kodesupplier>    
                        </oldoc>";	   		 	
    }
}
else
{
	echo " Gagal,Previous transaction not found";
}
?>