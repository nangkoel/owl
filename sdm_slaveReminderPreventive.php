<?php
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$nmGudang=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',"tipe like 'gudang%'");
#default Message:
$mess="<html>
               <head>
               </head>
               <body>
               Hardaya Plantations Group Preventive Maintenance, remind you for the folowing task:<br>";

# TRAKSI
#1. Ambil barang per masing 
$str="select a.kodebarang,b.namabarang,a.jumlah,a.id,b.satuan from ".$dbname.".schedulerdt a left join 
          ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
            left join ".$dbname.".schedulerht c on a.id=c.id";
$res=mysql_query($str);
$detail=Array();
while($bar=mysql_fetch_object($res))
{
    $detail[$bar->id]['namabarang'][]=$bar->namabarang;
    $detail[$bar->id]['jumlah'][]=$bar->jumlah;
    $detail[$bar->id]['kodebarang'][]=$bar->kodebarang;
    $detail[$bar->id]['satuan'][]=$bar->satuan;
}
#ambil value terakhir
$str="SELECT max(tanggal) as tanggal, id, nilai FROM ".$dbname.".scheduler_aksi group by id";
$res=mysql_query($str);
$lastReminder=Array();
while($bar=mysql_fetch_object($res))
{
    $lastReminder[$bar->id]=$bar->nilai;
}

#2. Ambil HM Traksi Terakhir
$kmAhir=Array();
$str="select * from ".$dbname.".vhc_kmhmakhir_vw order by kodevhc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $kmAhir[$bar->kodevhc]=$bar->kmhmakhir;
}

#3. Ambil HM MESIN PKS;
$str="select sum(hmmesin) as hm, mesin as kodevhc from ".$dbname.".pabrik_hmmesin_vw group by mesin order by mesin";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $kmAhir[$bar->kodevhc]=$bar->hm;
}

$str="select * from ".$dbname.".schedulerht  order by batasreminder asc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $resetdong[$bar->kodemesin]=$bar->resethmkm;
    
    if($bar->batasreminder==0 or $bar->batasreminder=='')
    {
        #reminder yang sekali tanpa perulangan
        if(date('Y-m-d')==$bar->setiaptanggal and $bar->sekali==2)
        {
                $subject="Hardaya Plantations Group Preventive Maintenance ".$bar->kodemesin;
                $mess.="<table>
                                <thead>
                                </thead>
                                <tbody>
                                     <tr><td>Task name</td><td>:".$bar->namatugas."</td></tr>
                                     <tr><td>Object</td><td>:".$bar->kodemesin."</td></tr>
                                     <tr><td>Note</td><td>:".$bar->ketrangan."</td></tr>    
                                     <tr><td>Warning On</td><td>:".tanggalnormal($bar->setiaptanggal)."</td></tr>    
                                </tbody>
                                 </table>";
                if(count($detail[$bar->id]['namabarang'])>0){
                        $mess.="Detail:<br><table border=1><tr><td>Kodebarang</td><td>Nama Barang</td><td>Jumlah</td></tr>";
                           foreach($detail[$bar->id]['namabarang'] as $detil =>$val)
                            {
                                 $mess.="<tr><td>".$detail[$bar->id]['kodebarang'][$detil]."</td><td>".$detail[$bar->id]['namabarang'][$detil]."</td><td>".$detail[$bar->id]['jumlah'][$detil]." ".$detail[$bar->id]['satuan'][$detil]."</td></tr>";                             
                            }
                        $mess.="</table>";   
                }
                $mess.="<br><br>Regards,<br>OWL-Plantation System</body></html>";
                $to=$bar->email;
                if($to!=''){ 
                       $kirim=kirimEmail($to,$subject,$mess);#this has return but disobeying;     
               }
                #update table
                $stru="update ".$dbname.".schedulerht set lastreminder='".date('Y-m-d')."' where id=".$bar->id;
                mysql_query($stru);
                $stri="insert into ".$dbname.".scheduler_aksi(id, tanggal, kodeorg, keterangan, pic, selesai, updateby, nilai)
                            values(".$bar->id.",
                                       '".date('Y-m-d')."',
                                       '".$bar->kodeorg."',
                                       '".$bar->ketrangan."',
                                       '".$bar->email."',0,0,'')";
                mysql_query($stri); 
        }
        #reminder yang  perulangan
        else if(date('m-d')==substr($bar->setiaptanggal,5,5) and $bar->sekali==1)
        {
                $subject="Hardaya Plantations Group Preventive Maintenance ".$bar->kodemesin;
                $mess.="<table>
                                <thead>
                                </thead>
                                <tbody>
                                     <tr><td>Task name</td><td>:".$bar->namatugas."</td></tr>
                                     <tr><td>Object</td><td>:".$bar->kodemesin."</td></tr>
                                     <tr><td>Note</td><td>:".$bar->ketrangan."</td></tr>    
                                     <tr><td>Warning On</td><td>:".tanggalnormal($bar->setiaptanggal)."</td></tr>    
                                </tbody>
                                 </table>";
                if(count($detail[$bar->id]['namabarang'])>0){
                        $mess.="Detail:<br><table border=1><tr><td>Kodebarang</td><td>Nama Barang</td><td>Jumlah</td></tr>";
                           foreach($detail[$bar->id]['namabarang'] as $detil =>$val)
                            {
                                 $mess.="<tr><td>".$detail[$bar->id]['kodebarang'][$detil]."</td><td>".$detail[$bar->id]['namabarang'][$detil]."</td><td>".$detail[$bar->id]['jumlah'][$detil]." ".$detail[$bar->id]['satuan'][$detil]."</td></tr>";                             
                            }
                        $mess.="</table>";   
                }
                $mess.="<br><br>Regards,<br>OWL-Plantation System</body></html>";
                $to=$bar->email;
                if($to!=''){ 
                       $kirim=kirimEmail($to,$subject,$mess);#this has return but disobeying;     
               }
                #update table
                $stru="update ".$dbname.".schedulerht set lastreminder='".date('Y-m-d')."' where id=".$bar->id;
                mysql_query($stru);
                $stri="insert into ".$dbname.".scheduler_aksi(id, tanggal, kodeorg, keterangan, pic, selesai, updateby, nilai)
                            values(".$bar->id.",
                                       '".date('Y-m-d')."',
                                       '".$bar->kodeorg."',
                                       '".$bar->ketrangan."',
                                       '".$bar->email."',0,0,'')";
                mysql_query($stri); 
        }        
    }
    else
    {
        if($bar->tastreminder!='0000-00-00' and $bar->sekali==2)
        {
            #diabaikan karena bukan perulangan dan sudah pernah direminder
        }
        else
        {
            $batasAtas=$bar->batasatas;
            $peringatan=$bar->batasreminder;
            @$saatIni=$kmAhir[$bar->kodemesin]-$resetdong[$bar->kodemesin];
            if($saatIni=='')
                $saatIni=0;            
            @$peringatanTerakhir=  $lastReminder[$bar->id];            
            if($peringatanTerakhir=='')
                $peringatanTerakhir=0;
            
            #rumus
            @$z=$saatIni%$batasAtas;
             if($z=='')
                 $z=0;
            $akumulasi=$saatIni-$z;
            $sisa=$z;
                      
            if($sisa>=$peringatan and $peringatanTerakhir<$akumulasi)
            {
               $subject="Hardaya Plantations Group Preventive Maintenance ".$bar->kodemesin;
                $mess.="<table>
                <thead>
                </thead>
                <tbody>
                     <tr><td>Task name</td><td>:".$bar->namatugas."</td></tr>
                     <tr><td>Object</td><td>:".$bar->kodemesin."</td></tr>
                     <tr><td>Note</td><td>:".$bar->ketrangan."</td></tr>    
                     <tr><td>Warning On</td><td>:".$saatIni." ".$bar->satuan."</td></tr>    
                </tbody>
                 </table>";
                if(count($detail[$bar->id]['namabarang'])>0){
                        $mess.="Detail:<br><table border=1><tr><td>Kodebarang</td><td>Nama Barang</td><td>Jumlah</td></tr>";
                           foreach($detail[$bar->id]['namabarang'] as $detil =>$val)
                            {
                                 $mess.="<tr><td>".$detail[$bar->id]['kodebarang'][$detil]."</td><td>".$detail[$bar->id]['namabarang'][$detil]."</td><td>".$detail[$bar->id]['jumlah'][$detil]." ".$detail[$bar->id]['satuan'][$detil]."</td></tr>";                             
                            }
                        $mess.="</table>";   
                }
                $mess.="<br><br>Regards,<br>OWL-Plantation System</body></html>";
                $to=$bar->email;
                if($to!=''){ 
                      $kirim=kirimEmail($to,$subject,$mess);#this has return but disobeying;     
               }
                #update table
                $stru="update ".$dbname.".schedulerht set lastreminder='".date('Y-m-d')."' where id=".$bar->id;
                mysql_query($stru);
                $stri="insert into ".$dbname.".scheduler_aksi(id, tanggal, kodeorg, keterangan, pic, selesai, updateby, nilai)
                            values(".$bar->id.",
                                       '".date('Y-m-d')."',
                                       '".$bar->kodeorg."',
                                       '".$bar->ketrangan."',
                                       '".$bar->email."',0,0,'".$saatIni."')";
                mysql_query($stri);       
            }          
        }
    }
    
}	

#reminder  stok minimum
//$str="select b.kodept,b.kodebarang,a.namabarang,a.satuan,b.minstok from ".$dbname.".log_5stokminimum b left join ".$dbname.
//        ".log_5masterbarang a on a.kodebarang=b.kodebarang where b.minstok>0 order by b.kodebarang";
//$res=mysql_query($str);
//while($bar=mysql_fetch_object($res)){
//    $barang[$bar->kodebarang]=$bar->kodebarang;
//    $namabarang[$bar->kodebarang]=$bar->namabarang;
//    $satuan[$bar->kodebarang]=$bar->satuan;
//    $minstok[$bar->kodebarang]=$bar->minstok;
//}

#ambil email dari reminder stok
$strEmail="select * from ".$dbname.".setup_parameterappl where kodeparameter='LOGRM'";
$resEmail=mysql_query($strEmail);
while($barEmail=mysql_fetch_object($resEmail)){
    $to1=$barEmail->nilai;
    $kdgudang=$barEmail->kodeorg;
    if ($kdgudang=='H0HO')
        $kdgudang.="01";
    else
        $kdgudang.="WH";

    #ambil saldo per PT per gudang
    $str="select a.kodebarang,namabarang,satuan,kodeorg,kodegudang,SUM(saldoqty) AS saldo,c.minstok from ".$dbname.".log_5masterbarangdt a
              left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
              left join ".$dbname.".log_5stokminimum c on a.kodebarang=c.kodebarang and a.kodeorg=c.kodept where c.minstok>0 and kodegudang='".$kdgudang."'
              group by a.kodeorg,a.kodebarang,a.kodegudang
              having (saldo <= c.minstok)";
    $res=mysql_query($str);
    $mess1="<html>
                   <head>
                   </head>
                   <body>";

    if(mysql_num_rows($res)>0)
    {
        $subject=" Hardaya Plantations minimum stock reminder (On: ".date('d-m-Y H:i:s').")";
                        $mess1.="Dear All,<br>Berikut ini adalah Material pada gudang ".$kdgudang." (".$nmGudang[$kdgudang].") yang sudah mencapai batas minimum. Segera lakukan pengadaan untuk barang:
                            <table border=1 cellspacing=0>
                            <thead>
                             <tr><td>No.</td>
                             <td>PT</td>
                             <td>Kodebarang</td>
                             <td>Nama Barang</td>
                             <td>Satuan</td>
                             <td>Saldo Saat Ini</td>
                             <td>Min.Saldo</td>
                             </tr>   
                            </thead>  
                        <tbody>";
                  $no=0;      
                  while($bar=mysql_fetch_object($res))
                  {

                     $no+=1;
                      $mess1.="<tr><td>".$no."</td><td>".$bar->kodeorg."</td><td>".$bar->kodebarang."</td>
                                      <td>".$bar->namabarang."</td><td>".$bar->satuan."</td>
                                       <td align=right>".number_format($bar->saldo,0)."</td><td align=right>".number_format($bar->minstok,0)."</td>
                                        </tr>";   
                   } 
                 $mess1.="</tbody><tfoot></tfoot></table><br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";

              #kirim email   
            if($to1!=''){ 
                 $kirim=kirimEmail($to1,$subject,$mess1);#this has return but disobeying;     
            }
    }
}



?>