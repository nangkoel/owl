<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
  $nopo	=$_POST['nopo'];
  $gudang=$_POST['gudang'];
  $datatype=$_POST['tipedata'];

//                echo "warning: (".$nopo.">>".$gudang.")"; exit;    

  //cek pemilik PO
                $str="select induk from ".$dbname.".organisasi where kodeorganisasi = '".substr($gudang,0,4)."'";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))$ptgudang=$bar->induk;
//                $str="select induk from ".$dbname.".organisasi where kodeorganisasi = '".$ptgudang."'";
//                $res=mysql_query($str);
//                while($bar=mysql_fetch_object($res))$ptgudang=$bar->induk;

                $str="select kodeorg,stat_release from ".$dbname.".log_poht where nopo = '".$nopo."'";
                //exit("error:".$str);
                $res=mysql_query($str);
                $bar=mysql_fetch_object($res);
                $ptPO=$bar->kodeorg;
                $statReleasePO=$bar->stat_release;
                
               //echo "warning: (".$ptgudang.">>".$ptPO.")";
                if(($ptgudang!=$ptPO)and($ptgudang!='')){ 
                    echo "warning: belongs to other company (storage:".$ptgudang." << PO:".$ptPO.")"; $exit = exit; 
                }
                if($statReleasePO==0){
                    exit("error: This Nopo : ".$nopo." need release, please contact purchasing dept".$bar->stat_release);
                }


  //cek PO apakah sudah status OK(1) disetujui
  $statuspo='x';
  $str="select statuspo,kodesupplier from ".$dbname.".log_poht where nopo='".$nopo."'";
  $res=mysql_query($str);
  if(mysql_num_rows($res)>0)
  {
                  while($bar=mysql_fetch_object($res))
                  {
                        $statuspo=$bar->statuspo;
                        $kodesupplier=$bar->kodesupplier;
                  }

                  if($statuspo>0)
                  {
                        $str="select * from ".$dbname.".log_poht where nopo='".$nopo."'";
                        $res=mysql_query($str);
                        if($datatype=='supplier')
                          echo $kodesupplier;
                        else if($datatype=='data')
                          {
                          createForm($nopo);
                          }
                        else if($datatype=='edit')
                        {
                          $notransaksi=$_POST['notransaksi'];
                          createForm($nopo,$notransaksi);	
                        }  
                  }
                  else
                 {
                         echo" Error: Purchase order no.".$nopo.". not released";
                  }
  }
  else
  {
        echo" Error: Purchase order no.".$nopo.". not found";
  }
}
else
{
        echo " Error: Transaction Period missing";
}

function createForm($nopo,$notransaksi='')
{
        //no transaksi terisi hanya pada saat edit
        global $dbname;
        global $conn;
        echo"<table class=sortable cellspacing=1 border=0>
             <thead>
                 <tr class=rowheader>
                   <td>No.</td>
                   <td>".$_SESSION['lang']['nopp']."</td>
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
         $strh="select jumlah from ".$dbname.".log_transaksidt where 
                notransaksi='".$notransaksi."'
                        and kodebarang='".$bar->kodebarang."'";
         $resh=mysql_query($strh);
         while($barh=mysql_fetch_object($resh))
          {
                $jumlahedit=$barh->jumlah;
          }		 

         if($notransaksi!='')
           {//khusus untuk edit, jumlah lalu tidak termasuk jumlah yg di edit
                 $sddt=" and a.notransaksi!='".$notransaksi."' ";
           }
//++++++++++++++++++++++++++++++
         $strx="select sum(a.jumlah) as jumlah,a.kodebarang as kodebarang 
            from ".$dbname.".log_transaksidt a,
                 ".$dbname.".log_transaksiht b
                   where a.notransaksi=b.notransaksi 
                   and b.nopo='".$nopo."' 
               and a.kodebarang='".$bar->kodebarang."' and nopp='".$bar->nopp."'
                   ".$sddt."
                   group by kodebarang";

                $resx=mysql_query($strx);
                while($barx=mysql_fetch_object($resx))
                {
                        $jumlahlalu=$barx->jumlah;
                }			 

                if($notransaksi!='')//jika proses edit
                   $sisa=$jumlahedit;//tampilkan value data yang di edit
                else  
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
                $xyz=$jumlah-$jumlahlalu;
                 echo"<tr class=rowcontent>
                   <td>".$no."</td>
                   <td id=nopp_".$bar->nopp.">".$bar->nopp."</td>
                   <td>".$bar->kodebarang."</td>
                   <td>".$namabarang."</td>
                   <td id='sat".$bar->kodebarang."_".$bar->nopp."'>".$satuan."</td>
                   <td align=right>".number_format($jumlahlalu,2,'.',',')."</td>
                   <td align=right>".number_format($jumlah,2,'.',',')."</td>
                   <td><input type=text ".$disab." class=myinputtextnumber id='qty".$bar->kodebarang."_".$bar->nopp."' onkeypress=\"return angka_doang(event);\" value='".$sisa."' size=7 maxlength=12 onblur=cekButton(this,'btn".$bar->kodebarang."')></td>
                   <td>".$bar->catatan."</td>
                   <td><button class=mybutton id='btn".$bar->kodebarang."_".$bar->nopp."' onclick=saveItemPo('".$bar->kodebarang."',$xyz,'".$bar->nopp."') ".$disab.">".$_SESSION['lang']['save']."</button>";"
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

        echo"</tbody>
             <tfoot>
                 <tr>
                   <td colspan=9 align=center>
                   <button onclick=selesaiBapb() class=mybutton>".$_SESSION['lang']['done']."</button>
                   </td>
                 </tr>
                 </tfoot>
                 </table>
                 ";	  

}
?>