<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include_once('lib/zLib.php');

$proses=$_GET['proses'];
$kodeorg=$_POST['kodeorg'];
$tahun=$_POST['tahun'];
if(($proses=='excel')or($proses=='pdf')){
    $kodeorg=$_GET['kodeorg'];
    $tahun=$_GET['tahun'];
}

if(($proses=='preview')or($proses=='excel')){
    if($kodeorg==''){
        echo"Error: Unit tidak boleh kosong."; exit;
    }	
    if($tahun==''){
        echo"Error: Tahun tidak boleh kosong."; exit;
    }	
}
        
if($proses=='excel')
    $stream.="<table border='1'>";
else {
    $stream.="<table cellspacing='1' border='0' class='sortable' width=100%>";
}
$stream.="<thead>
<tr class=rowheader>
<td align=center>".$_SESSION['lang']['nomor']."</td>
<td align=center>".$_SESSION['lang']['kodeorg']."</td>    
<td align=center>".$_SESSION['lang']['id']."</td>
<td align=center>".$_SESSION['lang']['namakaryawan']."</td>            
<td align=center>".$_SESSION['lang']['tipekaryawan']."</td>
<td align=center>".$_SESSION['lang']['statuspajak']."</td>
<td align=center>".$_SESSION['lang']['npwp']."</td>  
<td align=center>".$_SESSION['lang']['tahun']."</td>
<td align=center>Penghasilan01</td>
<td align=center>Gaji01</td>   
<td align=center>Tunj01</td>  
<td align=center>".$_SESSION['lang']['pph12'].".01</td>    
<td align=center>Penghasilan02</td>
<td align=center>Gaji02</td>   
<td align=center>Tunj02</td>      
<td align=center>".$_SESSION['lang']['pph12'].".02</td>    
<td align=center>Penghasilan03</td>
<td align=center>Gaji03</td>   
<td align=center>Tunj03</td>      
<td align=center>".$_SESSION['lang']['pph12'].".03</td>    
<td align=center>Penghasilan04</td>
<td align=center>Gaji04</td>   
<td align=center>Tunj04</td>      
<td align=center>".$_SESSION['lang']['pph12'].".04</td>    
<td align=center>Penghasilan05</td>
<td align=center>Gaji05</td>   
<td align=center>Tunj05</td>      
<td align=center>".$_SESSION['lang']['pph12'].".05</td>    
<td align=center>Penghasilan06</td>
<td align=center>Gaji06</td>   
<td align=center>Tunj06</td>      
<td align=center>".$_SESSION['lang']['pph12'].".06</td>    
<td align=center>Penghasilan07</td>
<td align=center>Gaji07</td>   
<td align=center>Tunj07</td>      
<td align=center>".$_SESSION['lang']['pph12'].".07</td>    
<td align=center>Penghasilan08</td>
<td align=center>Gaji08</td>   
<td align=center>Tunj08</td>      
<td align=center>".$_SESSION['lang']['pph12'].".08</td>    
<td align=center>Penghasilan09</td>
<td align=center>Gaji09</td>   
<td align=center>Tunj09</td>      
<td align=center>".$_SESSION['lang']['pph12'].".09</td>    
<td align=center>Penghasilan10</td>
<td align=center>Gaji10</td>   
<td align=center>Tunj10</td>      
<td align=center>".$_SESSION['lang']['pph12'].".10</td>    
<td align=center>Penghasilan11</td>
<td align=center>Gaji11</td>   
<td align=center>Tunj11</td>      
<td align=center>".$_SESSION['lang']['pph12'].".11</td>    
<td align=center>Penghasilan12</td>
<td align=center>Gaji12</td>   
<td align=center>Tunj12</td>      
<td align=center>".$_SESSION['lang']['pph12'].".12</td>    
<td align=center>".$_SESSION['lang']['total']."</td>
<td align=center>GajiTOT</td>   
<td align=center>TunjTOT</td>      
<td align=center>PPh21 Tahunan</td>    
</tr>   
</thead>
<tbody>";

// kamus tipe karyawan
$str="select id, tipe from ".$dbname.".sdm_5tipekaryawan
    ";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $kamusTipe[$bar->id]=$bar->tipe;
    }

// kamus data karyawan
    $kamusKar=Array();
$str="select nik, karyawanid, namakaryawan, tipekaryawan, statuspajak, lokasitugas, subbagian,npwp from ".$dbname.".datakaryawan 
    where lokasitugas like '".$kodeorg."%' ";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $kamusKar[$bar->karyawanid]['nik']=$bar->nik;
        $kamusKar[$bar->karyawanid]['nama']=$bar->namakaryawan;
        $kamusKar[$bar->karyawanid]['tipe']=$bar->tipekaryawan;
        $kamusKar[$bar->karyawanid]['status']=$bar->statuspajak;
        $kamusKar[$bar->karyawanid]['lokasi']=$bar->lokasitugas;
        $kamusKar[$bar->karyawanid]['bagian']=$bar->subbagian;
        $kamusKar[$bar->karyawanid]['npwp']=str_replace(" ","",str_replace(".","",$bar->npwp));
        if (!is_numeric($kamusKar[$bar->karyawanid]['npwp'])) {
            $kamusKar[$bar->karyawanid]['npwp']='';
        }
        else if(intval($kamusKar[$bar->karyawanid]['npwp'])>0 and strlen(intval($kamusKar[$bar->karyawanid]['npwp'])>12))
        {
            
        }
        else
        {
           $kamusKar[$bar->karyawanid]['npwp']=$bar->npwp; 
        }   
    }
//ambil porsi JMS dari perusahaan yang kena pajak
    $plusJMS=0;
    $str="select value from ".$dbname.".sdm_ho_hr_jms_porsi where id='pph21'";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
      $plusJMS=$bar->value;
    }    
//ambil biaya jabatan    
    $jabPersen=0;
    $jabMax=0;
    $str="select persen,max from ".$dbname.".sdm_ho_pph21jabatan";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $jabPersen=$bar->persen/100;
        $jabMax=$bar->max*12;
    }    
    
//Ambil PTKP:
    $ptkp=Array();
    $str="select id,value from ".$dbname.".sdm_ho_pph21_ptkp";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $ptkp[$bar->id]=$bar->value;
    } 
    
//ambil tarif pph21
  $pphtarif=Array();  
  $pphpercent=Array();  
  $str="select level,percent,upto from ".$dbname.".sdm_ho_pph21_kontribusi order by level";
  $res=mysql_query($str);    
  $urut=0;
  while($bar=mysql_fetch_object($res))
    {
        $pphtarif[$urut]    =$bar->upto;
        $pphpercent[$urut]  =$bar->percent/100;      
        $urut+=1;  
    }   
//ambil gaji pokok yang akan dikali dengan porsi jms dari perusahaan
$str="select sum(jumlah) as gaji, karyawanid, substr(periodegaji,6,2) as bulan from ".$dbname.".sdm_gaji 
    where idkomponen=1 and periodegaji like '".$tahun."%'
    and kodeorg like '".$kodeorg."%' group by karyawanid, periodegaji order by karyawanid";
$res=mysql_query($str);        
$dJMS=Array();  
while($bar=mysql_fetch_object($res))
{
    $dJMS[$bar->karyawanid][$bar->bulan]=$bar->gaji*$plusJMS/100;
     $dJMS[$bar->karyawanid]['gapok'][$bar->bulan]=$bar->gaji;
    $dJMS[$bar->karyawanid]['gptahunan']+=$bar->gaji;//gaji pokok tahunan
} 

    
// total gaji yang kena pph
$str="select sum(jumlah) as gaji, karyawanid, substr(periodegaji,6,2) as bulan from ".$dbname.".sdm_gaji 
    where idkomponen in (select id from ".$dbname.".sdm_ho_component where pph21=1)
    and periodegaji like '".$tahun."%'
    and kodeorg like '".$kodeorg."%' group by karyawanid, periodegaji order by karyawanid";

//echo $str;
$res=mysql_query($str);        
$dzKar=Array();  
$dzArr=Array();  
while($bar=mysql_fetch_object($res))
{
    $dzKar[$bar->karyawanid]=$bar->karyawanid;
    $dzArr[$bar->karyawanid]['karyawanid']=$bar->karyawanid;
    $dzArr[$bar->karyawanid][$bar->bulan]=$bar->gaji;
    $dzArr[$bar->karyawanid]['total']+=$bar->gaji;
    //hitung PPH21====================================================
    //penghasilan disetahunkan
    $dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan]=(($bar->gaji+$dJMS[$bar->karyawanid][$bar->bulan])*12);//disetahunkan
    
    //periksa By jab dan kurangkan
    $dzArr[$bar->karyawanid]['byjab'][$bar->bulan]=$jabPersen*$dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan];
    if($dzArr[$bar->karyawanid]['byjab'][$bar->bulan]>$jabMax){//jika lebih dari max maka dibatasi sebesar max
        $dzArr[$bar->karyawanid]['byjab'][$bar->bulan]=$jabMax;
    }    
    //penghasilan setela kurang By Jabatan
    $dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan]=$dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan]-$dzArr[$bar->karyawanid]['byjab'][$bar->bulan];
    //kurangi dengan PTKP sehingga menghasilkan pkp:
    $dzArr[$bar->karyawanid]['pkp'][$bar->bulan]=$dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan]-$ptkp[str_replace("K","",$kamusKar[$bar->karyawanid]['status'])]; 
    $zz=0;
    $sisazz=0;

    if($dzArr[$bar->karyawanid]['pkp'][$bar->bulan]>0){         
    #tahap 1: 
    if($dzArr[$bar->karyawanid]['pkp'][$bar->bulan]<$pphtarif[0])
    {
        $zz+=$pphpercent[0]*$dzArr[$bar->karyawanid]['pkp'][$bar->bulan];
        $sisazz=0; 
    }
    else if($dzArr[$bar->karyawanid]['pkp'][$bar->bulan]>=$pphtarif[0])
    {
        $zz+=$pphpercent[0]*$pphtarif[0];
        $sisazz=$dzArr[$bar->karyawanid]['pkp'][$bar->bulan]-$pphtarif[0];
        #level 2
            if($sisazz<($pphtarif[1]-$pphtarif[0]))
            {
                $zz+=$pphpercent[1]*$sisazz;
                $sisazz=0;        
            }    
            else if($sisazz>=($pphtarif[1]-$pphtarif[0]))
            {
                $zz+=$pphpercent[1]*($pphtarif[1]-$pphtarif[0]);
                $sisazz=$dzArr[$bar->karyawanid]['pkp'][$bar->bulan]-$pphtarif[1]; 
                #level 3   
                    if($sisazz<($pphtarif[2]-$pphtarif[1]))
                    {
                        $zz+=$pphpercent[2]*$sisazz;
                        $sisazz=0;        
                    }    
                    else if($sisazz>=($pphtarif[2]-$pphtarif[1]))
                    {
                        $zz+=$pphpercent[2]*($pphtarif[2]-$pphtarif[1]);
                        $sisazz=$dzArr[$bar->karyawanid]['pkp'][$bar->bulan]-$pphtarif[2];
                         // print_r($sisazz);exit();
                            if($sisazz>0){
                            #level 4  sisanya kali 30% 
                                $zz+=$pphpercent[3]*$sisazz;  
                            }                          
                    } 
            }   
                   
    }
    }
    
    //masukkan ke array utama
    $dzArr[$bar->karyawanid]['pph21'][$bar->bulan]=$zz/12;
    //jika tidak memiliki NPWP maka tambahkan 20% dari PPh yang ada
    if($kamusKar[$bar->karyawanid]['npwp']=='')
    {
         $dzArr[$bar->karyawanid]['pph21'][$bar->bulan]= $dzArr[$bar->karyawanid]['pph21'][$bar->bulan]+ ($dzArr[$bar->karyawanid]['pph21'][$bar->bulan]*20/100);
    }
    // end hitungan PPh 21 bulanan===================================================================================
   
}

//==========================================================================
$no=0;
// display data
if(!empty($dzKar))foreach($dzKar as $karid){
    $no+=1;
    $stream.="<tr class=rowcontent>
    <td align=right>".$no."</td>";
    if($kamusKar[$karid]['bagian']!='')$stream.="<td align=left>".$kamusKar[$karid]['bagian']."</td>"; else $stream.="<td align=left>".$kamusKar[$karid]['lokasi']."</td>";
    $stream.="<td align=left>".$kamusKar[$karid]['nik']."</td>
    <td align=left>".$kamusKar[$karid]['nama']."</td>
    <td align=left>".$kamusTipe[$kamusKar[$karid]['tipe']]."</td>
    <td align=left>".$kamusKar[$karid]['status']."</td>
    <td align=left>".$kamusKar[$karid]['npwp']."</td>
    <td align=center>".$tahun."</td>
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['01'],0)."</td>
    <td align=right style='color:#0000FF;'>".number_format($dJMS[$karid]['gapok']['01'],0)."</td>
    <td align=right style='color:#0000FF;'>".number_format(($dzArr[$karid]['01']-$dJMS[$karid]['gapok']['01']),0)."</td>    
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['pph21']['01'])."</td>        
    <td align=right>".number_format($dzArr[$karid]['02'])."</td>
    <td align=right>".number_format($dJMS[$karid]['gapok']['02'],0)."</td>
    <td align=right>".number_format(($dzArr[$karid]['02']-$dJMS[$karid]['gapok']['02']),0)."</td>         
    <td align=right>".number_format($dzArr[$karid]['pph21']['02'])."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['03'])."</td>
    <td align=right style='color:#0000FF;'>".number_format($dJMS[$karid]['gapok']['03'],0)."</td>
    <td align=right style='color:#0000FF;'>".number_format(($dzArr[$karid]['03']-$dJMS[$karid]['gapok']['03']),0)."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['pph21']['03'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['04'])."</td>
    <td align=right>".number_format($dJMS[$karid]['gapok']['04'],0)."</td>
    <td align=right>".number_format(($dzArr[$karid]['04']-$dJMS[$karid]['gapok']['04']),0)."</td>         
    <td align=right>".number_format($dzArr[$karid]['pph21']['04'])."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['05'])."</td>
    <td align=right style='color:#0000FF;'>".number_format($dJMS[$karid]['gapok']['05'],0)."</td>
    <td align=right style='color:#0000FF;'>".number_format(($dzArr[$karid]['05']-$dJMS[$karid]['gapok']['05']),0)."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['pph21']['05'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['06'])."</td>
    <td align=right>".number_format($dJMS[$karid]['gapok']['06'],0)."</td>
    <td align=right>".number_format(($dzArr[$karid]['06']-$dJMS[$karid]['gapok']['06']),0)."</td>         
    <td align=right>".number_format($dzArr[$karid]['pph21']['06'])."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['07'])."</td>
    <td align=right style='color:#0000FF;'>".number_format($dJMS[$karid]['gapok']['07'],0)."</td>
    <td align=right style='color:#0000FF;'>".number_format(($dzArr[$karid]['07']-$dJMS[$karid]['gapok']['07']),0)."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['pph21']['07'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['08'])."</td>
    <td align=right>".number_format($dJMS[$karid]['gapok']['08'],0)."</td>
    <td align=right>".number_format(($dzArr[$karid]['08']-$dJMS[$karid]['gapok']['08']),0)."</td>         
    <td align=right>".number_format($dzArr[$karid]['pph21']['08'])."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['09'])."</td>
    <td align=right style='color:#0000FF;'>".number_format($dJMS[$karid]['gapok']['09'],0)."</td>
    <td align=right style='color:#0000FF;'>".number_format(($dzArr[$karid]['09']-$dJMS[$karid]['gapok']['09']),0)."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['pph21']['09'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['10'])."</td>
    <td align=right>".number_format($dJMS[$karid]['gapok']['10'],0)."</td>
    <td align=right>".number_format(($dzArr[$karid]['10']-$dJMS[$karid]['gapok']['10']),0)."</td>         
    <td align=right>".number_format($dzArr[$karid]['pph21']['10'])."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['11'])."</td>
    <td align=right style='color:#0000FF;'>".number_format($dJMS[$karid]['gapok']['11'],0)."</td>
    <td align=right style='color:#0000FF;'>".number_format(($dzArr[$karid]['11']-$dJMS[$karid]['gapok']['11']),0)."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['pph21']['11'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['12'])."</td>
    <td align=right>".number_format($dJMS[$karid]['gapok']['12'],0)."</td>
    <td align=right>".number_format(($dzArr[$karid]['12']-$dJMS[$karid]['gapok']['12']),0)."</td>         
    <td align=right>".number_format($dzArr[$karid]['pph21']['12'])."</td>         
    <td align=right style='color:#0000FF;'>".number_format($dzArr[$karid]['total'])."</td>
    <td align=right style='color:#0000FF;'>".number_format($dJMS[$karid]['gptahunan'],0)."</td>
    <td align=right style='color:#0000FF;'>".number_format(($dzArr[$karid]['total']-$dJMS[$karid]['gptahunan']),0)."</td>";
            
    //pph 21 tahunan (setelah setahun)============================================================
        $dzArr[$karid]['tpenghasilan']=($dzArr[$karid]['total']+($dJMS[$karid]['gptahunan']*$plusJMS/100));
    //periksa By jab dan kurangkan
    $dzArr[$karid]['tbyjab']=$jabPersen*$dzArr[$karid]['tpenghasilan'];
    if($dzArr[$karid]['tbyjab']>$jabMax){//jika lebih dari max maka dibatasi sebesar max
        $dzArr[$karid]['tbyjab']=$jabMax;
    }    
    //penghasilan setela kurang By Jabatan
    $dzArr[$karid]['tpenghasilan']=$dzArr[$karid]['tpenghasilan']-$dzArr[$karid]['tbyjab'];
    //kurangi dengan PTKP sehingga menghasilkan pkp:
    $dzArr[$karid]['tpkp']=$dzArr[$karid]['tpenghasilan']-$ptkp[str_replace("K","",$kamusKar[$karid]['status'])]; 
    $zz=0;
    $sisazz=0;
    if($dzArr[$karid]['tpkp']>0){
    #tahap 1:
    if($dzArr[$karid]['tpkp']<$pphtarif[0])
    {
        $zz+=$pphpercent[0]*$dzArr[$karid]['tpkp'];
        $sisazz=0;
    }
    else if($dzArr[$karid]['tpkp']>=$pphtarif[0])
    {
        $zz+=$pphpercent[0]*$pphtarif[0];
        $sisazz=$dzArr[$karid]['tpkp']-$pphtarif[0];
            #level 2
            if($sisazz<($pphtarif[1]-$pphtarif[0]))
            {
                $zz+=$pphpercent[1]*$sisazz;
                $sisazz=0;        
            }    
            else if($sisazz>=($pphtarif[1]-$pphtarif[0]))
            {
                $zz+=$pphpercent[1]*($pphtarif[1]-$pphtarif[0]);
                $sisazz=$dzArr[$karid]['tpkp']-$pphtarif[1];        
                    #level 3   
                    if($sisazz<($pphtarif[2]-$pphtarif[1]))
                    {
                        $zz+=$pphpercent[2]*$sisazz;
                        $sisazz=0;        
                    }    
                    else if($sisazz>=($pphtarif[2]-$pphtarif[1]))
                    {
                        $zz+=$pphpercent[2]*($pphtarif[2]-$pphtarif[1]);
                        $sisazz=$dzArr[$karid]['tpkp']-$pphtarif[2];  
                            if($sisazz>0){
                            #level 4  sisanya kali 30% 
                                $zz+=$pphpercent[3]*$sisazz; 
                            }
                    } 
            }          
    }
    }
    //masukkan ke array utama
    $dzArr[$karid]['tpph21']=$zz;
        //jika tidak memiliki NPWP maka tambahkan 20% dari PPh yang ada
    if($kamusKar[$bar->karyawanid]['npwp']=='')
    {
          $dzArr[$karid]['tpph21']=  $dzArr[$karid]['tpph21']+ ( $dzArr[$karid]['tpph21']*20/100);
    }
    //================================end pph tahunan===================================================================
    
    
    $stream.="<td align=right  style='color:#0000FF;'>".number_format($dzArr[$karid]['tpph21'])."</td>
    </tr>";
    $total['pph01']+=$dzArr[$karid]['pph21']['01'];
    $total['pph02']+=$dzArr[$karid]['pph21']['02'];
    $total['pph03']+=$dzArr[$karid]['pph21']['03'];
    $total['pph04']+=$dzArr[$karid]['pph21']['04'];
    $total['pph05']+=$dzArr[$karid]['pph21']['05'];
    $total['pph06']+=$dzArr[$karid]['pph21']['06'];
    $total['pph07']+=$dzArr[$karid]['pph21']['07'];
    $total['pph08']+=$dzArr[$karid]['pph21']['08'];
    $total['pph09']+=$dzArr[$karid]['pph21']['09'];
    $total['pph10']+=$dzArr[$karid]['pph21']['10'];
    $total['pph11']+=$dzArr[$karid]['pph21']['11'];
    $total['pph12']+=$dzArr[$karid]['pph21']['12'];
    
    $total['01']+=$dzArr[$karid]['01'];
    $total['02']+=$dzArr[$karid]['02'];
    $total['03']+=$dzArr[$karid]['03'];
    $total['04']+=$dzArr[$karid]['04'];
    $total['05']+=$dzArr[$karid]['05'];
    $total['06']+=$dzArr[$karid]['06'];
    $total['07']+=$dzArr[$karid]['07'];
    $total['08']+=$dzArr[$karid]['08'];
    $total['09']+=$dzArr[$karid]['09'];
    $total['10']+=$dzArr[$karid]['10'];
    $total['11']+=$dzArr[$karid]['11'];
    $total['12']+=$dzArr[$karid]['12'];
    $total['total']+=$dzArr[$karid]['total'];
    $total['pph']+=$dzArr[$karid]['tpph21'];
    
    $tgapok['01']+=$dJMS[$karid]['gapok']['01'];
    $tgapok['02']+=$dJMS[$karid]['gapok']['02'];
    $tgapok['03']+=$dJMS[$karid]['gapok']['03'];
    $tgapok['04']+=$dJMS[$karid]['gapok']['04'];
    $tgapok['05']+=$dJMS[$karid]['gapok']['05'];
    $tgapok['06']+=$dJMS[$karid]['gapok']['06'];
    $tgapok['07']+=$dJMS[$karid]['gapok']['07'];
    $tgapok['08']+=$dJMS[$karid]['gapok']['08'];
    $tgapok['09']+=$dJMS[$karid]['gapok']['09'];
    $tgapok['10']+=$dJMS[$karid]['gapok']['10'];
    $tgapok['11']+=$dJMS[$karid]['gapok']['11'];
    $tgapok['12']+=$dJMS[$karid]['gapok']['12']; 
    $tgapok['total']+=$dJMS[$karid]['gptahunan'];

    $ttj['01']+=($dzArr[$karid]['01']-$dJMS[$karid]['gapok']['01']);
    $ttj['02']+=($dzArr[$karid]['02']-$dJMS[$karid]['gapok']['02']);
    $ttj['03']+=($dzArr[$karid]['03']-$dJMS[$karid]['gapok']['03']);
    $ttj['04']+=($dzArr[$karid]['04']-$dJMS[$karid]['gapok']['04']);
    $ttj['05']+=($dzArr[$karid]['05']-$dJMS[$karid]['gapok']['05']);
    $ttj['06']+=($dzArr[$karid]['06']-$dJMS[$karid]['gapok']['06']);
    $ttj['07']+=($dzArr[$karid]['07']-$dJMS[$karid]['gapok']['07']);
    $ttj['08']+=($dzArr[$karid]['08']-$dJMS[$karid]['gapok']['08']);
    $ttj['09']+=($dzArr[$karid]['09']-$dJMS[$karid]['gapok']['09']);
    $ttj['10']+=($dzArr[$karid]['10']-$dJMS[$karid]['gapok']['10']);
    $ttj['11']+=($dzArr[$karid]['11']-$dJMS[$karid]['gapok']['11']);
    $ttj['12']+=($dzArr[$karid]['12']-$dJMS[$karid]['gapok']['12']); 
    $ttj['total']+=($dzArr[$karid]['total']-$dJMS[$karid]['gptahunan']);    
}

// total
$stream.="<tr class=title>
<td colspan=8 align=center>Total</td>
<td align=right>".number_format($total['01'])."</td>
<td align=right>".number_format($tgapok['01'])."</td>    
<td align=right>".number_format($ttj['01'])."</td>     
<td align=right>".number_format($total['pph01'])."</td>    
<td align=right>".number_format($total['02'])."</td>   
<td align=right>".number_format($tgapok['02'])."</td>    
<td align=right>".number_format($ttj['02'])."</td>       
<td align=right>".number_format($total['pph02'])."</td>     
<td align=right>".number_format($total['03'])."</td>
<td align=right>".number_format($tgapok['03'])."</td>    
<td align=right>".number_format($ttj['03'])."</td>       
<td align=right>".number_format($total['pph03'])."</td>     
<td align=right>".number_format($total['04'])."</td>
<td align=right>".number_format($tgapok['04'])."</td>    
<td align=right>".number_format($ttj['04'])."</td>       
<td align=right>".number_format($total['pph04'])."</td>     
<td align=right>".number_format($total['05'])."</td>
<td align=right>".number_format($tgapok['05'])."</td>    
<td align=right>".number_format($ttj['05'])."</td>       
<td align=right>".number_format($total['pph05'])."</td>     
<td align=right>".number_format($total['06'])."</td>
<td align=right>".number_format($tgapok['06'])."</td>    
<td align=right>".number_format($ttj['06'])."</td>       
<td align=right>".number_format($total['pph06'])."</td>     
<td align=right>".number_format($total['07'])."</td>
<td align=right>".number_format($tgapok['07'])."</td>    
<td align=right>".number_format($ttj['07'])."</td>       
<td align=right>".number_format($total['pph07'])."</td>     
<td align=right>".number_format($total['08'])."</td>
<td align=right>".number_format($tgapok['08'])."</td>    
<td align=right>".number_format($ttj['08'])."</td>       
<td align=right>".number_format($total['pph08'])."</td>     
<td align=right>".number_format($total['09'])."</td>
<td align=right>".number_format($tgapok['09'])."</td>    
<td align=right>".number_format($ttj['09'])."</td>       
<td align=right>".number_format($total['pph09'])."</td>     
<td align=right>".number_format($total['10'])."</td>
<td align=right>".number_format($tgapok['10'])."</td>    
<td align=right>".number_format($ttj['10'])."</td>       
<td align=right>".number_format($total['pph10'])."</td>     
<td align=right>".number_format($total['11'])."</td>
<td align=right>".number_format($tgapok['11'])."</td>    
<td align=right>".number_format($ttj['11'])."</td>       
<td align=right>".number_format($total['pph11'])."</td>     
<td align=right>".number_format($total['12'])."</td>
<td align=right>".number_format($tgapok['12'])."</td>    
<td align=right>".number_format($ttj['12'])."</td>       
<td align=right>".number_format($total['pph12'])."</td>     
<td align=right>".number_format($total['total'])."</td>
<td align=right>".number_format($tgapok['total'])."</td>    
<td align=right>".number_format($ttj['total'])."</td>        
<td align=right>".number_format($total['pph'])."</td></tr>";
$stream.="</tbody></table>";

if($proses=='preview'){
    echo $stream;    
}

if($proses=='excel'){
    $stream.="</table><br>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHms");
    $nop_="pph21_".$kodeorg."_".$tahun."_".$dte;
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";            
}

    
?>