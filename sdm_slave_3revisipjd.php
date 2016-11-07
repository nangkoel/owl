<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$notransaksi=$_POST['notransaksi'];
$notransaksi2=$_POST['notransaksi2'];
$tanggal=tanggalsystem($_POST['tanggal']);
$jenisby=$_POST['jenisby'];
$jumlahhrd=$_POST['jumlahhrd']; 
$kodeOrg=$_POST['kodeOrg'];
$jumlah=$_POST['jumlah']; 


$proses=$_POST['proses'];


switch($proses){
    
    case'getData':
     $kd=substr($notransaksi,0,4);
    $sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
           where char_length(kodeorganisasi)='4' order by namaorganisasi asc";    
    $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
    while($rOrg=  mysql_fetch_assoc($qOrg)){
        $optOrg.="<option value='".$rOrg['kodeorganisasi']."' ".($kd==$rOrg['kodeorganisasi']?"selected":"").">".$rOrg['namaorganisasi']."</option>";
    }
    $str="select notransaksi from ".$dbname.".sdm_pjdinasht where  notransaksi like '%".$notransaksi."%'";
    $res=mysql_query($str);
    if (mysql_num_rows($res)==1){
        
    echo"<tr class=rowcontent><td colspan=4></td><td>Ganti No Transaksi</td>";
    echo"<td colspan=2><select id=kdOrg onchange=getNotrans()>".$optOrg."</select>";
    echo"<img src='images/save.png' title='Save' class=resicon onclick=saveNotrans()></td>";
    echo"</tr>";
       
    echo"<tr class=rowcontent><td colspan=4></td><td>Hapus Transaksi</td>";
    echo"<td colspan=2><img src='images/Delete.png' title='Hapus' class=resicon onclick=hapusNotrans()></td>";
    echo"</tr>";
       
    $str="select a.*,b.keterangan as jns,b.id as bid from ".$dbname.".sdm_pjdinasdt a
          left join ".$dbname.".sdm_5jenisbiayapjdinas b on a.jenisbiaya=b.id
              where a.notransaksi='".$notransaksi."'";
    $res=mysql_query($str);
    $no=0;
    $total=0;
    while($bar=mysql_fetch_object($res))
    {
            $no+=1;
            echo"<tr class=rowcontent>
                    <td>".$no."</td>
                        <td>".$bar->jns."</td>
                            <td>".tanggalnormal($bar->tanggal)."</td>
                            <td id=ket_".$no.">".$bar->keterangan."</td>
                            <td align=right>".number_format($bar->jumlah,2,'.','.')."</td>
                            <td align=right>
                            <img src='images/puzz.png' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('jumlahhrd".$bar->bid.$no."').value='".$bar->jumlah."'\">
                            <input type=text id='jumlahhrd".$bar->bid.$no."' class=myinputtextnumber size=15 onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='".number_format($bar->jumlahhrd,2,'.',',')."'>
                            <img src='images/save.png' title='Save' class=resicon onclick=saveApprvPJD('".$bar->bid."','".$bar->notransaksi."','".tanggalnormal($bar->tanggal)."','".$bar->jumlah."','".$no."')></td>
                            </tr>";
            $total+=$bar->jumlah;
            $totalhrd+=$bar->jumlahhrd;
    }
            echo"<tr class=rowcontent>
                    <td colspan=4 align=center>TOTAL</td>
                            <td align=right>".number_format($total,2,'.','.')."</td>
                            <td align=right>".number_format($totalhrd,2,'.','.')."</td>
                        <td></td>
                            </tr>";
    }
    break;
    case'getNotrans':
        $orge=substr($notransaksi,0,4);
        if($kodeOrg==$orge)
        {
            exit("Error:Kodeorganisasi Yang Sama");
        }
    $potSK=$kodeOrg.date('Y');
    $str="select notransaksi from ".$dbname.".sdm_pjdinasht
      where  notransaksi like '".$potSK."%'
          order by notransaksi desc limit 1";

    $notrx=0;
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $notrx=substr($bar->notransaksi,10,5);
    }
    $notrx=intval($notrx);
    $notrx=$notrx+1;
    $notrx=str_pad($notrx, 5, "0", STR_PAD_LEFT);
    $notrx=$potSK.$notrx;
    echo $notrx;
    break;
    case'saveNotrans':
        $orge=substr($notransaksi,0,4);
        if($kodeOrg==$orge)
        {
            exit("Error:Kodeorganisasi Yang Sama");
        }
        $supd="update ".$dbname.".sdm_pjdinasht set notransaksi='".$notransaksi2."',kodeorg='".$kodeOrg."' where notransaksi='".$notransaksi."'";
        if(!mysql_query($supd))
        {
            echo " Gagal:".addslashes(mysql_error($conn))."__".$supd;	 
        }
        echo $notransaksi2;
    break;
    case'hapusNotrans':
        $supd="delete from ".$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
        if(!mysql_query($supd))
        {
            echo " Gagal:".addslashes(mysql_error($conn))."__".$supd;	 
        }
        echo $notransaksi2;
    break;
}





























//if($jumlahhrd=='')
//  $jumlahhrd=0;
//
//
//if($method=='update')
//{
//	$str="update ".$dbname.".sdm_pjdinasdt
//	       set jumlahhrd=".$jumlahhrd."
//	      where jenisbiaya=".$jenisby." and notransaksi='".$notransaksi."'
//		  and tanggal=".$tanggal." and jumlah='".$jumlah."'"; 
//	//echo "Error:".$str;	  
//	if(mysql_query($str))
//		{}
//	else
//   		{
//		 echo " Gagal:".addslashes(mysql_error($conn));	 
//		 exit(0);
//		}
//}
//if($method=='finish')
//{
//	$str="update ".$dbname.".sdm_pjdinasht
//	       set statuspertanggungjawaban=1
//	      where  notransaksi='".$notransaksi."'"; 
//	if(mysql_query($str))
//		{}
//	else
//   		{
//   			echo " Gagal:".addslashes(mysql_error($conn));	 
//		 exit(0);
//		}	
//}
//


?>