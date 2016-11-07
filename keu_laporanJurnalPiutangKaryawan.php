<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$tanggalmulai=$_POST['tanggalmulai'];
$tanggalsampai=$_POST['tanggalsampai'];
$noakun=$_POST['noakun'];
$kodeo=$_POST['kodeorg'];

//1180100 Uang Muka Pengadaan Barang = only kary
//2111103 Accrue Hutang Supplier = only supp

$pt=makeOption($dbname,'organisasi','kodeorganisasi,induk');

if($tanggalmulai==''){ echo "warning: silakan mengisi tanggal"; exit; }
if($tanggalsampai==''){ echo "warning: silakan mengisi tanggal"; exit; }
if($noakun==''){ echo "warning: silakan memilih no akun"; exit; }

$qwe=explode("-",$tanggalmulai); $tanggalmulai=$qwe[2]."-".$qwe[1]."-".$qwe[0];
$qwe=explode("-",$tanggalsampai); $tanggalsampai=$qwe[2]."-".$qwe[1]."-".$qwe[0];

if($kodeo=='') {
	$ind='';
} else {
	$ind="and a.kodeorg in( select kodeorganisasi from ".$dbname.".organisasi where induk ='".$kodeo."')";
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
    //echo $str;
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
            $supplier[$bar->nik]=$nmKar[$bar->nik]; //."[karyawan]";   
       }
        
             
       $akun[$bar->noakun]=$bar->namaakun;

           $kodeorg[$no]=$bar->kodeorg;
   }




   
   ###################KHUSUS YANG KARYAWAN ID 0 / UNTUK SUPPLIER
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
       $supplier[$bar->nik]=$nmSup[$bar->nik]; //."[supplier]";      
       $akun[$bar->noakun]=$bar->namaakun;

           $kodeorg[$no]=$bar->kodeorg;
   }
    


//echo"<pre>";
//print_r($supplier);
//echo"</pre>";
//=================================================
$no=0;
if($supplier<1)
{
        echo"<tr class=rowcontent><td colspan=9>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
}
else
{
    if(!empty($supplier)){
        echo "<div style='overflow:scroll;height:370px;width:100%;display:fixed;'>
             <table cellspacing=1 border=0 class=sortable style='width:100%'>
             <thead class=rowheader></thead><tbody>";
        foreach($supplier as $kdsupp =>$val){
        if($val!='lain' && $val!=''){
            $no+=1;
                echo"<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=lihatDetailHutang('".$kdsupp."','".$noakun."','".$tanggalmulai."','".$tanggalsampai."','".$kodeo."','".str_replace(" ","_",$val)."',event)>
                      <td align=center width=30>".$no."</td>
                      <td align=center width=60>".$kodeo."</td>
                      <td width=50>".$noakun."</td>
                      <td width=150>".$akun[$noakun]."</td>
                      <td width=150>".$val."</td>
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
        echo"<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=lihatDetailHutang('','".$noakun."','".$tanggalmulai."','".$tanggalsampai."','".$kodeo."','Lain-Lain',event)>
              <td align=center width=30>".$no."</td>
              <td align=center width=40>".$kodeo."</td>
              <td width=50>".$noakun."</td>
              <td width=150>".$akun[$noakun]."</td>
              <td width=150>Lain-Lain</td>
              <td align=right width=100>".number_format($sawal['lain'],2)."</td>   
              <td align=right width=100>".number_format($debet['lain'],2)."</td>
              <td align=right width=100>".number_format($kredit['lain'],2)."</td>
              <td align=right width=100>".number_format($sawal['lain']+$debet['lain']-$kredit['lain'],2)."</td>
             </tr>"; 
        echo"</tbody><tfoot></tfoot></table></div>";
      $tsa+=$sawal['lain'];
      $td+=$debet['lain'];
      $tk+=$kredit['lain'];
      $tak+=($sawal['lain']+$debet['lain']-$kredit['lain']);                    
    }
} 	
?>