<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>
	<link rel=stylesheet type=text/css href=style/generic.css>	
<?php
$tanggalmulai=$_GET['mulai'];
$tanggalsampai=$_GET['sampai'];
$noakun=$_GET['noakun'];
$kodesupplier=$_GET['kodesupplier'];
$kodeorg=$_GET['kodeorg'];

$pt=makeOption($dbname,'organisasi','kodeorganisasi,induk');
//exit("Error:$kodeorg");


//exit("Error:$kodesupplier");

if($kodesupplier=='')
{
    $suppNik="and (kodesupplier='' or kodesupplier is null) and (a.nik='' or a.nik is null or nik=0)";
}
else
{
    $suppNik="and (kodesupplier='".$kodesupplier."' or a.nik='".$kodesupplier."')";
}



if($kodeorg=='')
{
	$ind='';
}
else
{
	$ind="and a.kodeorg in( select kodeorganisasi from ".$dbname.".organisasi where induk ='".$kodeorg."')";
}


if($tanggalmulai==''){ echo "warning: silakan mengisi tanggal"; exit; }
if($tanggalsampai==''){ echo "warning: silakan mengisi tanggal"; exit; }
if($noakun==''){ echo "warning: silakan memilih no akun"; exit; }

#ambil nama karyawan
$str="select nik,namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$kodesupplier."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $nik=$bar->nik;
    $supplier=$bar->namakaryawan;
}

$str="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$kodesupplier."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $supplier=$bar->namasupplier;
}

#ambil saldo awal supplier
$str="select sum(a.debet-a.kredit) as sawal,a.noakun from ".$dbname.".keu_jurnaldt_vw a
      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' ".$suppNik."
      ".$ind." ";
$res=mysql_query($str);
$bar=mysql_fetch_object($res);
    $sawal=$bar->sawal;


echo"<div style='width:1200px;display:fixed;'>
         <table cellspacing=1 border=0 class=sortable style='width:100%'>
 <thead>
        <tr>
              <td align=center width=20>".$_SESSION['lang']['nourut']."</td>
              <td align=center width=40>".$_SESSION['lang']['kodept']."</td>
              <td align=center width=75>".$_SESSION['lang']['tanggal']."</td>    
              <td align=center width=170>".$_SESSION['lang']['nojurnal']."</td>
              <td align=center width=100>".$_SESSION['lang']['noreferensi']."</td>     
              <td align=center width=100>".$_SESSION['lang']['nodok']."</td>     
              <td align=center width=150>".$_SESSION['lang']['keterangan']."</td>
              <td align=center width=65>NIK</td>              
              <td align=center width=100>Karyawan/Supplier</td>
              <td align=center width=100>".$_SESSION['lang']['saldoawal']."</td>                             
              <td align=center width=100>".$_SESSION['lang']['debet']."</td>
              <td align=center width=100>".$_SESSION['lang']['kredit']."</td>
              <td align=center width=100>".$_SESSION['lang']['saldoakhir']."</td>                               
            </tr>  
     </thead>
     <tbody></tbody></table></div>"; 
//=================================================
//
//
//


#ambil  transaksi dalam periode supplier
$str="select a.kodeorg,a.debet  as debet, a.kredit as kredit,a.nojurnal,a.noreferensi,a.tanggal,a.noakun,a.keterangan,
         a.kodesupplier,a.nodok from ".$dbname.".keu_jurnaldt_vw a
      where a.tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' 
      and a.noakun = '".$noakun."' ".$suppNik."
      ".$ind." order by tanggal";
//echo $str;
$res=mysql_query($str);

echo "<div style='overflow:scroll;height:330px;width:1215px;display:fixed;'>
     <table cellspacing=1 border=0 class=sortable style='width:100%'>
     <thead class=rowheader></thead><tbody>";

while($bar=mysql_fetch_object($res))
{
    $no+=1;
    
    echo"<tr class=rowcontent >
          <td align=center width=20>".$no."</td>
          <td align=center width=40>".$pt[$bar->kodeorg]."</td>   
          <td align=center width=75>".tanggalnormal($bar->tanggal)."</td>                   
          <td align=center width=170>".$bar->nojurnal."</td>
          <td align=center width=100>".$bar->noreferensi."</td>     
          <td align=center width=100>".$bar->nodok."</td>     
          <td width=150>".$bar->keterangan."</td>
          <td align=center width=65>".$nik."</td>          
          <td width=100>".$supplier."</td>
          <td align=right width=100>".number_format($sawal,2)."</td>   
          <td align=right width=100>".number_format($bar->debet,2)."</td>
          <td align=right width=100>".number_format($bar->kredit,2)."</td>
          <td align=right width=100>".number_format($sawal+$bar->debet-$bar->kredit,2)."</td>
         </tr>"; 
    $totDebet+=$bar->debet;
    $totKredit+=$bar->kredit;
    $sawal=$sawal+$bar->debet-$bar->kredit;
}
 echo"<tr class=rowcontent >
     <td align=right colspan=10><b>".strtoupper($_SESSION['lang']['total'])."</b></td>
     <td align=right width=100><b>".number_format($totDebet,2)."</b></td>   
     <td align=right width=100><b>".number_format($totKredit,2)."</b></td><td></td>
</tr>";
echo"</tbody><tfoot></tfoot></table></div>";



    /*foreach($dat as $notran =>$val){
            $no+=1;
            if($debet[$notran]!=0 or $kredit[$notran]!=0){
                echo"<tr class=rowcontent >
                      <td align=center width=20>".$no."</td>
                      <td align=center>".$kodeorg."</td>   
                      <td align=center>".tanggalnormal($val)."</td>                   
                      <td align=center>".$notran."</td>
                       <td align=center>".$ref[$notran]."</td>     
                      <td>".$noakun."</td>
                      <td>".$supplier."</td>
                       <td align=right width=100>".number_format($tsa,2)."</td>   
                      <td align=right width=100>".number_format($debet[$notran],2)."</td>
                      <td align=right width=100>".number_format($kredit[$notran],2)."</td>
                      <td align=right width=100>".number_format($tsa+$debet[$notran]-$kredit[$notran],2)."</td>
                     </tr>"; 
              $tsa=$tsa+$debet[$notran]-$kredit[$notran];   
            }
    }	*/

?>