<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$thnbudget=$_POST['thnbudget'];
$jenis=$_POST['jenis'];
#ambil produksi pabrik
$kgolah=0;
$str="select sum(kgolah) as kgolah,sum(kgcpo) as kgcpo,sum(kgkernel) as kgkernel from ".$dbname.".bgt_produksi_pks_vw 
      where tahunbudget=".$thnbudget." and millcode='".$kodeorg."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $kgolah=$bar->kgolah;
    $kgcpo=$bar->kgcpo;
    $kgkernel=$bar->kgkernel;
}
$kgoil=$kgcpo+$kgkernel;

$adq="a.noakun, sum(a.rupiah) as rupiah,sum(a.rp01) as rp01,
      sum(a.rp02) as rp02,sum(a.rp03) as rp03,
      sum(a.rp04) as rp04,sum(a.rp05) as rp05,
      sum(a.rp06) as rp06,sum(a.rp07) as rp07,
      sum(a.rp08) as rp08,sum(a.rp09) as rp09,
      sum(a.rp10) as rp10,sum(a.rp11) as rp11,
      sum(a.rp12) as rp12";
if($jenis=='UMUM'){
$str="select $adq,b.namaakun as namaakun from ".$dbname.".bgt_budget_detail a left join
      ".$dbname.".keu_5akun b on a.noakun=b.noakun
      where a.kodebudget='UMUM' and tahunbudget=".$thnbudget." and a.kodeorg like '".$kodeorg."%'
          and tipebudget='MILL'
      group by a.noakun";
}
else if($jenis=='LANGSUNG')
{
 $str="select $adq,b.namaakun as namaakun from ".$dbname.".bgt_budget_detail a left join
      ".$dbname.".keu_5akun b on a.noakun=b.noakun
      where a.kodebudget<>'UMUM' and tahunbudget=".$thnbudget." and a.kodeorg like '".$kodeorg."%'
          and tipebudget='MILL'
      group by a.noakun"; 
}
else
{
 $str="select $adq,b.namaakun as namaakun from ".$dbname.".bgt_budget_detail a left join
      ".$dbname.".keu_5akun b on a.noakun=b.noakun
      where  tahunbudget=".$thnbudget." and a.kodeorg like '".$kodeorg."%'
          and tipebudget='MILL'
      group by a.noakun";  

}    

echo"<fieldset><legend>".$_SESSION['lang']['produksi']."</legend>
     <table class=sortable cellspacing=1 border=0>
     <thead>
         <tr class=rowheader>
           <td align=center>Palm Product(Ton)</td>
           <td align=center>".$_SESSION['lang']['cpo']."(Ton)</td>
           <td align=center>".$_SESSION['lang']['kernel']."(Ton)</td> 
           <td align=center>".$_SESSION['lang']['tbs']."(Ton)</td>    
         </tr>
     </thead>
     <tbody>
         <tr class=rowcontent>
           <td align=right>".@number_format(($kgoil)/1000,0,".",",")."</td>
           <td align=right>".@number_format($kgcpo/1000,0,".",",")."</td>
           <td align=right>".@number_format($kgkernel/1000,0,".",",")."</td>
           <td align=right>".@number_format($kgolah/1000,0,".",",")."</td>    
         </tr>     
     </tbody>
     <tfoot></tfoot>
     </table>
     </fieldset>";

echo"<fieldset><legend>".$_SESSION['lang']['list']." : ".$jenis."
    Result:
    <span id=\"printPanel\">
    <img onclick=\"fisikKeExcelRPKG(event,'bgt_laporan_RPKG_pks_excel.php')\" src=\"images/excel.jpg\" class=\"resicon\" title=\"MS.Excel\"> 
     <img onclick=\"fisikKePDFRPKG(event,'bgt_laporan_RPKG_pks_pdf.php')\" title=\"PDF\" class=\"resicon\" src=\"images/pdf.jpg\">
    </span>
    </legend>
     ".$_SESSION['lang']['unit']." : ".$kodeorg." ".$_SESSION['lang']['tahunbudget']." : ".$thnbudget."
     <table class=sortable cellspacing=1 border=0 width=100%>
     <thead>
         <tr class=rowheader>
           <td align=center>".$_SESSION['lang']['nourut']."</td>
           <td align=center>".$_SESSION['lang']['noakun']."</td>
           <td align=center>".$_SESSION['lang']['namaakun']."</td>
           <td align=center>".$_SESSION['lang']['jumlahrp']."</td>
           <td align=center>".$_SESSION['lang']['rpperkg']."-PP</td>
           <td align=center>".$_SESSION['lang']['rpperkg']."-TBS</td>    
		   <td align=center width=40>".substr($_SESSION['lang']['jan'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['peb'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['mar'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['apr'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['mei'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['jun'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['jul'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['agt'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['sep'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['okt'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['nov'],0,3)."</td>
		   <td align=center width=40>".substr($_SESSION['lang']['dec'],0,3)."</td>
         </tr>
         </thead>
         <tbody>"; 
        
$res=mysql_query($str);
$no=0;
$rpperha=0;
$ttrp=0;
while($bar=mysql_fetch_object($res))
{
    @$rpperkg=$bar->rupiah/$kgoil;
    @$rpperkgtbs=$bar->rupiah/$kgolah;
    $no+=1;
    echo"<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$bar->noakun."</td>
           <td>".$bar->namaakun."</td>
           <td align=right>".number_format($bar->rupiah,0,'.',',')."</td>
           <td align=right>".number_format($rpperkg,3,'.',',')."</td>  
           <td align=right>".number_format($rpperkgtbs,3,'.',',')."</td>     
           <td align=right>".number_format($bar->rp01,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp02,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp03,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp04,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp05,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp06,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp07,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp08,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp09,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp10,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp11,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp12,0,'.',',')."</td>
         </tr>";
    $tt01+=$bar->rp01;
    $tt02+=$bar->rp02;
    $tt03+=$bar->rp03;
    $tt04+=$bar->rp04;
    $tt05+=$bar->rp05;
    $tt06+=$bar->rp06;
    $tt07+=$bar->rp07;
    $tt08+=$bar->rp08;
    $tt09+=$bar->rp09;
    $tt10+=$bar->rp10;
    $tt11+=$bar->rp11;
    $tt12+=$bar->rp12;
    $ttrp+=$bar->rupiah;
    
}
@$ttrpperkgolah=$ttrp/$kgoil;
@$ttrpperkgtbs=$ttrp/$kgolah;
 echo"<tr class=rowheader>
           <td colspan=3>Total</td>
           <td align=right>".number_format($ttrp,0,'.',',')."</td>
           <td align=right>".@number_format($ttrpperkgolah,3,'.',',')."</td>
           <td align=right>".@number_format($ttrpperkgtbs,3,'.',',')."</td>     
           <td align=right>".number_format($tt01,0,'.',',')."</td>
           <td align=right>".number_format($tt02,0,'.',',')."</td>
           <td align=right>".number_format($tt03,0,'.',',')."</td>
           <td align=right>".number_format($tt04,0,'.',',')."</td>
           <td align=right>".number_format($tt05,0,'.',',')."</td>
           <td align=right>".number_format($tt06,0,'.',',')."</td>
           <td align=right>".number_format($tt07,0,'.',',')."</td>
           <td align=right>".number_format($tt08,0,'.',',')."</td>
           <td align=right>".number_format($tt09,0,'.',',')."</td>
           <td align=right>".number_format($tt10,0,'.',',')."</td>
           <td align=right>".number_format($tt11,0,'.',',')."</td>
           <td align=right>".number_format($tt12,0,'.',',')."</td>
         </tr>";
echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table></fieldset>";
?>