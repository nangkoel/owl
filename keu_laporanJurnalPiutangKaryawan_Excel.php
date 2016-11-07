<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$tanggalmulai=$_GET['tanggalmulai'];
$tanggalsampai=$_GET['tanggalsampai']; 
$noakun=$_GET['noakun'];
$kodeo=$_GET['kodeorg'];

//1180100 Uang Muka Pengadaan Barang = only kary
//2111103 Accrue Hutang Supplier = only supp

$pt=makeOption($dbname,'organisasi','kodeorganisasi,induk');

if($tanggalmulai==''){ echo "warning: silakan mengisi tanggal"; exit; }
if($tanggalsampai==''){ echo "warning: silakan mengisi tanggal"; exit; }
if($noakun==''){ echo "warning: silakan memilih no akun"; exit; }

$qwe=explode("-",$tanggalmulai); $tanggalmulai=$qwe[2]."-".$qwe[1]."-".$qwe[0];
$qwe=explode("-",$tanggalsampai); $tanggalsampai=$qwe[2]."-".$qwe[1]."-".$qwe[0];

if($kodeo=='') {
	$ind=''; //         $wherekar="";
} else {
	$ind="and a.kodeorg in( select kodeorganisasi from ".$dbname.".organisasi where induk ='".$kodeo."')"; //         $wherekar=" kodeorganisasi='".$kodeo."' ";
}

$str="select distinct nik from ".$dbname.".keu_jurnaldt_vw a
  where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."'  and noakun = '".$noakun."' and nik!='' and nik is not null ".$ind;
$res=mysql_query($str);
$whrKary="''";
while($bar=mysql_fetch_object($res)){
    $whrKary.=",'".$bar->nik."'";
}
$whrKary="karyawanid in (".$whrKary.")";
$nmKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan',$whrKary);
$nmSup=makeOption($dbname,'log_5supplier','supplierid,namasupplier');
//exit("Error:$kodeorg");
//exit("Error:$ind");
//=================================================
$stream="<table border=1>
             <thead>
                    <tr>
                          <td align=center width=50>".$_SESSION['lang']['nourut']."</td>
                          <td align=center>".$_SESSION['lang']['organisasi']."</td>
                          <td align=center>".$_SESSION['lang']['noakun']."</td>
                          <td align=center>".$_SESSION['lang']['namaakun']."</td>
                          <td align=center>".$_SESSION['lang']['karyawanid']."/".$_SESSION['lang']['kodesupplier']."</td>
                          <td align=center>".$_SESSION['lang']['karyawan']."/".$_SESSION['lang']['supplier']."</td>
                          <td align=center>".$_SESSION['lang']['saldoawal']."</td>                             
                          <td align=center>".$_SESSION['lang']['debet']."</td>
                          <td align=center>".$_SESSION['lang']['kredit']."</td>
                          <td align=center>".$_SESSION['lang']['saldoakhir']."</td>                               
                        </tr>  
                 </thead>
                 <tbody id=container>";

    
     #ambil saldo awal  karyawan
    $str="select sum(a.debet-a.kredit) as sawal,a.noakun, b.namaakun,a.nik,a.kodeorg from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' and a.nik!='' and a.nik is not null AND a.nik!=0
       ".$ind." group by a.nik";//,a.kodeorg
    //echo $str;

    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $no+=1;
        if(strlen($bar->nik)<10)
        {
            $bar->nik=  addZero($bar->nik, 10);
        }
        
        
        if($bar->nik==0)
        {
             $sawal['lain']+=$bar->sawal;
        }
        else
        {
            $sawal[$bar->nik]=$bar->sawal;
            $supplier[$bar->nik]=$nmKar[$bar->nik];   
            $akun[$bar->noakun]=$bar->namaakun;  
        }
        $kodeorg[$no]=$bar->kodeorg;    
    }


    $str="select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.nik,a.kodeorg from ".$dbname.".keu_jurnaldt_vw a
         left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
         where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."'  
         and a.noakun = '".$noakun."' and a.nik!='' and a.nik is not null AND a.nik!=0
         ".$ind." group by a.nik ";//,a.kodeorg
  
   $res=mysql_query($str);
   while($bar=mysql_fetch_object($res))
   {
       if(strlen($bar->nik)<10)
       {
           $bar->nik=  addZero($bar->nik, 10);
       }
        $no+=1;
        
       if($bar->nik==0)
       {
           $debet['lain']+=$bar->debet;
           $kredit['lain']+=$bar->kredit; 
       } 
       else
       {
            $debet[$bar->nik]=$bar->debet;
            $kredit[$bar->nik]=$bar->kredit;
            $supplier[$bar->nik]=$nmKar[$bar->nik];   
       }
        
             
       $akun[$bar->noakun]=$bar->namaakun;

           $kodeorg[$no]=$bar->kodeorg;
   }


####################################################################################################################
####################################################################################################################   

    #ambil saldo awal supplier
     $str="select sum(a.debet-a.kredit) as sawal,a.noakun, b.namaakun,a.kodesupplier as nik,a.kodeorg from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' and (a.kodesupplier!='' and (a.kodesupplier IS NOT NULL AND (a.nik=0 OR a.nik=''))) 
       ".$ind." group by a.kodesupplier";//,a.kodeorg
    //echo $str;

    
     
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $no+=1;
        if($bar->nik==''  or $bar->nik=='0')
        {
           $sawal['lain']+=$bar->sawal;
        }
       
        $sawal[$bar->nik]=$bar->sawal;
        $supplier[$bar->nik]=$nmSup[$bar->nik];
        $akun[$bar->noakun]=$bar->namaakun;

            $kodeorg[$no]=$bar->kodeorg;
    }
    
    #ambil  transaksi dalam periode supplier
   $str="select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.kodesupplier as nik,a.kodeorg from ".$dbname.".keu_jurnaldt_vw a
         left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
         where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."'  
         and a.noakun = '".$noakun."' and (a.kodesupplier!='' or (a.kodesupplier IS NOT NULL AND (a.nik=0 OR a.nik='')))  
         ".$ind." group by a.kodesupplier ";//,a.kodeorg
    
  
   $res=mysql_query($str) or die (mysql_error($conn));
   while($bar=mysql_fetch_object($res))
   {
       if($bar->nik=='' or $bar->nik=='0')
        {
           $debet['lain']+=$bar->debet;
           $kredit['lain']+=$bar->kredit;
        }
           $no+=1;
       $debet[$bar->nik]=$bar->debet;
       $kredit[$bar->nik]=$bar->kredit;
       $supplier[$bar->nik]=$nmSup[$bar->nik];      
       $akun[$bar->noakun]=$bar->namaakun;

           $kodeorg[$no]=$bar->kodeorg;
   }
    
    



/*echo"<pre>";
print_r($kodeorg);
echo"</pre>";*/

//=================================================
$no=0;
if($supplier<1)
{
        $stream.="<tr class=rowcontent><td colspan=9>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
}
else
{
   if(!empty($supplier))
       foreach($supplier as $kdsupp =>$val){
        $nama=(substr($kdsupp,0,1)=='0')?"'".$kdsupp:$kdsupp;
        if($val!='lain' && $val!=''){
            $no+=1;
           $stream.="<tr>
                  <td align=center width=20>".$no."</td>
                  <td align=center>".$kodeo."</td>
                  <td>".$noakun."</td>
                  <td>".$akun[$noakun]."</td>
                  <td>".$nama."</td>
                  <td nowrap>".$val."</td>
                  <td align=right width=100>".number_format($sawal[$kdsupp],2)."</td>   
                  <td align=right width=100>".number_format($debet[$kdsupp],2)."</td>
                  <td align=right width=100>".number_format($kredit[$kdsupp],2)."</td>
                  <td align=right width=100>".number_format($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp],2)."</td>
                 </tr>"; 
          $tsa+=$sawal[$kdsupp];
          $td+=$debet[$kdsupp];
          $tk+=$kredit[$kdsupp];
          $tak+=($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp]);                    
        }
    }	
            $no+=1;
            $stream.="<tr >
                  <td align=center width=20>".$no."</td>
                  <td align=center>".$kodeo."</td>
                  <td>".$noakun."</td>
                  <td>".$akun[$noakun]."</td>
                  <td>Lainnya</td>
                  <td>Lainnya</td>
                   <td align=right width=100>".number_format($sawal['lain'],2)."</td>   
                  <td align=right width=100>".number_format($debet['lain'],2)."</td>
                  <td align=right width=100>".number_format($kredit['lain'],2)."</td>
                  <td align=right width=100>".number_format($sawal['lain']+$debet['lain']-$kredit['lain'],2)."</td>
                 </tr>"; 
          $tsa+=$sawal['lain'];
          $td+=$debet['lain'];
          $tk+=$kredit['lain'];
          $tak+=($sawal['lain']+$debet['lain']-$kredit['lain']);                    
     
} 
$stream.="<tr class=rowcontent>
      <td align=center colspan=6>Total</td>
       <td align=right width=100>".number_format($tsa,2)."</td>   
      <td align=right width=100>".number_format($td,2)."</td>
      <td align=right width=100>".number_format($tk,2)."</td>
      <td align=right width=100>".number_format($tak,2)."</td>
     </tr>"; 

//exit("Error:$stream");
$stream.="</tbody></table>";
$qwe=date("YmdHms");
$nop_="LP_JRNL_Hutang Dan Piutang_".$noakun."_".$qwe;
if(strlen($stream)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}
?>