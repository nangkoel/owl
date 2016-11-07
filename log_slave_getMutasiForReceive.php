<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
//========================
 
  $notransaksi=$_POST['notransaksi'];
  $gudang=$_POST['gudang'];
  $jlhbaris=0;
  $str="select a.tipetransaksi,a.notransaksi,a.tanggal,a.kodept,a.kodegudang,
         b.kodebarang,b.satuan,b.jumlah   
         from ".$dbname.".log_transaksiht a 
         left join ".$dbname.".log_transaksidt b on
		 a.notransaksi=b.notransaksi
		 where a.notransaksi='".$notransaksi."'
        and a.tipetransaksi =7";
  echo "<table class=sortable cellspacing=1 border=0>
        <thead>
		   <tr>
		      <td>No.</td>
			  <td>".$_SESSION['lang']['notransaksi']."</td>
			  <td>".$_SESSION['lang']['tipe']."</td>
			  <td>".$_SESSION['lang']['kodebarang']."</td>
			  <td>".$_SESSION['lang']['namabarang']."</td>
			  <td>".$_SESSION['lang']['satuan']."</td>
			  <td>".$_SESSION['lang']['jumlah']."</td>
			  <td>".$_SESSION['lang']['diterimasebelumnya']."</td>
			  <td>".$_SESSION['lang']['jumlahditerima']."</td>
			  <td>".$_SESSION['lang']['kodept']."</td>
			  <td>".$_SESSION['lang']['kodeorgpengirim']."</td>
			  <td>".$_SESSION['lang']['penerima']."</td>
		   </tr>
		 </thead>
		 <tbody>  	  
			  ";	
$no=0;
  $res=mysql_query($str);
  $jlhbaris=mysql_num_rows($res);
  while($bar=mysql_fetch_object($res))
  {
    //ambil namabarang
	$stru="select namabarang from ".$dbname.".log_5masterbarang 
	      where kodebarang='".$bar->kodebarang."'";
	$resu=mysql_query($stru);
	$namabarang='';
	while($baru=mysql_fetch_object($resu))
	{
		$namabarang=$baru->namabarang;
	}
	$strSebelum="select sum(jumlah) as jumlah from ".$dbname.".log_transaksi_vw where kodebarang='".$bar->kodebarang.
                "' and notransaksireferensi='".$bar->notransaksi."' and tipetransaksi=3";
	$resu=mysql_query($strSebelum);
	$jmlSebelum=0;
	while($baru=mysql_fetch_object($resu))
	{
		$jmlSebelum=$baru->jumlah;
	}
	$sisa=floatval($bar->jumlah)-floatval($jmlSebelum);
        if ($sisa>0){
            $no+=1;	  
            echo"<tr class=rowcontent id=row".$no.">
              <td>".$no."</td>
              <td id=notransaksi".$no.">".$bar->notransaksi."</td>
              <td>".$bar->tipetransaksi."</td>
              <td id=kodebarang".$no.">".$bar->kodebarang."</td>	  
              <td id=namabarang".$no.">".$namabarang."</td>
              <td id=satuan".$no.">".$bar->satuan."</td>
              <td id=jumlah".$no.">".$bar->jumlah."</td>
              <td id=sebelum".$no.">".$jmlSebelum."</td>
              <td><input type=text id=diterima".$no." size=10 onkeypress=\"return angka_doang(event)\" value=".$sisa." class=myinputtextnumber /></td>
              <td id=kodept".$no.">".$bar->kodept."</td>			  
              <td id=asalgudang".$no.">".$bar->kodegudang."</td>
              <td id=gudang".$no.">".$gudang."</td>
              </tr>";
        }
  }
  echo"</tbody><tfoot></tfoot></table>
  	   <button onclick=mulaiSimpan(".$jlhbaris.") class=mybutton>".$_SESSION['lang']['save']."</button>
  ";
}
else
{
	echo " Error: Transaction Period missing";
}
?>