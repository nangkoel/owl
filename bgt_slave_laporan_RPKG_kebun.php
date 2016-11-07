<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$thnbudget=$_POST['thnbudget'];
$jenis=$_POST['jenis'];
#ambil luas kebun
$luas=0;
$produksi=0;
$str="select sum(kgsetahun) as produksi from ".$dbname.".bgt_produksi_kbn_kg_vw 
      where tahunbudget=".$thnbudget." and kodeunit='".$kodeorg."'";
//exit("Error".$str);
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{

    $produksi=$bar->produksi;
}

$luastm=0;
$luastbm=0;
$luasxtm=0;
$luasxtbm=0;
$luasbibitan=0;
$luaslc=0;

     $str="select hathnini as luas,statusblok from ".$dbname.".bgt_blok 
          where tahunbudget=".$thnbudget." and kodeblok like '".$kodeorg."%'";
    // exit("Error".$str);
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        if($bar->statusblok=='TM')
        {       
            $luasxtm+=$bar->luas;
        }
        else{
            $luasxtbm+=$bar->luas;
        }   
    }  


if($jenis=='LANGSUNG'){

    //tambahan jamhari, untuk biaya langsung luasan disesuaikan per noakun atau kegiatan
    $str="select sum(hathnini) as luas,thntnm from ".$dbname.".bgt_blok where 
      kodeblok like '".$kodeorg."%' and statusblok in ('TBM','TB')";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $luastbm=$bar->luas;
}


//jumlah pokok
 $str="select sum(pokokthnini) as luas,thntnm from ".$dbname.".bgt_blok where 
      kodeblok like '".$kodeorg."%' and statusblok ='BBT'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $luaslpkk=$bar->luas;
}

//jumlah tm
$str="select sum(hathnini) as luas,thntnm from ".$dbname.".bgt_blok where 
      kodeblok like '".$kodeorg."%' and statusblok='TM'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $luastm=$bar->luas;
}
//jumlah LC
 $str="select sum(lcthnini) as luas,thntnm from ".$dbname.".bgt_blok where 
      kodeblok like '".$kodeorg."%' and lcthnini!=''";

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $luaslc=$bar->luas;
}
 
}
else if($jenis!='LANGSUNG')
{
    
//    $str="select sum(hathnini)+sum(hanonproduktif) as luas from ".$dbname.".bgt_areal_per_afd_vw 
//          where tahunbudget=".$thnbudget." and afdeling like '".$kodeorg."%'";
      $str="select sum(hathnini)+sum(hanonproduktif) as luas ,thntnm from ".$dbname.".bgt_blok where 
        kodeblok like '".$kodeorg."%'";
     //exit("Error".$str);
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $luastm=$bar->luas;
        $luastbm=$bar->luas;
    } 
        //tambahan jamhari, untuk biaya langsung luasan disesuaikan per noakun atau kegiatan



        //jumlah pokok
        $str="select sum(pokokthnini) as luas,thntnm from ".$dbname.".bgt_blok where 
        kodeblok like '".$kodeorg."%' and statusblok ='BBT'";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
        $luaslpkk=$bar->luas;
        }

       
        //jumlah LC
        $str="select sum(lcthnini) as luas,thntnm from ".$dbname.".bgt_blok where 
        kodeblok like '".$kodeorg."%' and lcthnini!=''";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
        $luaslc=$bar->luas;
        }
        //echo"dari sini ___".$luastm."___".$luastbm;
} 

//echo $luastm."-".$luastbm."-".$luasxtm."-".$luasxtbm."<br>";
//exit('Error');

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
      and tipebudget='ESTATE'    
      group by a.noakun";
}
else if($jenis=='LANGSUNG')
{
 $str="select $adq,b.namaakun as namaakun from ".$dbname.".bgt_budget_detail a left join
      ".$dbname.".keu_5akun b on a.noakun=b.noakun
      where a.kodebudget<>'UMUM' and tahunbudget=".$thnbudget." and a.kodeorg like '".$kodeorg."%'
      and tipebudget='ESTATE'    
      group by a.noakun";   
}
else
{
 $str="select $adq,b.namaakun as namaakun from ".$dbname.".bgt_budget_detail a left join
      ".$dbname.".keu_5akun b on a.noakun=b.noakun
      where  tahunbudget=".$thnbudget." and a.kodeorg like '".$kodeorg."%'
      and tipebudget='ESTATE'    
      group by a.noakun";      
}   

//Ambil nilai kapital
$strx="select * from ".$dbname.".bgt_kapital_vw where tahunbudget='".$thnbudget."' and
       kodeunit like '".$kodeorg."%' order by namatipe";
$resx1=mysql_query($strx);

echo"<fieldset><legend>".$_SESSION['lang']['produksi']."</legend>
     <table class=sortable cellspacing=1 border=0>
     <thead>
         <tr class=rowheader>
           <td align=center>".$_SESSION['lang']['luas']." TM(Ha)</td>
            <td align=center>".$_SESSION['lang']['luas']." TBM(Ha)</td>    
           <td align=center>".$_SESSION['lang']['totalkg']."(Kg)</td>
           <td align=center>Ton/Ha</td>    
         </tr>
     </thead>
     <tbody>
         <tr class=rowcontent>
           <td align=right>".number_format($luasxtm,2,".",",")."</td>
           <td align=right>".number_format($luasxtbm,2,".",",")."</td>   
           <td align=right>".number_format($produksi,0,".",",")."</td>
           <td align=right>".@number_format($produksi/1000/$luasxtm,2,".",",")."</td>    
         </tr>     
     </tbody>
     <tfoot></tfoot>
     </table>
     </fieldset>";
	
 

echo"<fieldset><legend>".$_SESSION['lang']['list'].": ".$jenis."
    Result:
    <span id=\"printPanel\">
    <img onclick=\"fisikKeExcelRPKG(event,'bgt_laporan_RPKG_kebun_excel.php')\" src=\"images/excel.jpg\" class=\"resicon\" title=\"MS.Excel\"> 
     <img onclick=\"fisikKePDFRPKG(event,'bgt_laporan_RPKG_kebun_pdf.php')\" title=\"PDF\" class=\"resicon\" src=\"images/pdf.jpg\">
    </span>
    </legend>
     Unit:".$kodeorg." Tahun Budget:".$thnbudget."
     <table class=sortable cellspacing=1 border=0 style='width:1600px;'>
     <thead>
         <tr class=rowheader>
           <td align=center>".$_SESSION['lang']['nourut']."</td>
           <td align=center>".$_SESSION['lang']['noakun']."</td>
           <td align=center>".$_SESSION['lang']['namaakun']."</td>
           <td align=center>".$_SESSION['lang']['luas']."</td>
           <td align=center>".$_SESSION['lang']['jumlahrp']."</td>
           <td align=center>".$_SESSION['lang']['rpperkg']."</td>      
           <td align=center>".$_SESSION['lang']['rpperha']."</td>  
           <td align=center>".$_SESSION['lang']['jan']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['peb']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['mar']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['apr']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['mei']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['jun']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['jul']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['agt']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['sep']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['okt']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['nov']."<br>(Rp)</td>
           <td align=center>".$_SESSION['lang']['dec']."<br>(Rp)</td>
         </tr>
         </thead>
         <tbody>"; 
        
$res=mysql_query($str);
$res1=mysql_query($str);
$res2=mysql_query($str);
$res3=mysql_query($str);
$nobibitan=1;
$rpperha=0;
$ttrp=0;

//Biaya Bibitan
while($bar=mysql_fetch_object($res3))
{
       @$rpperkg=0;
       @$rpperha=0;
        
    if(substr($bar->noakun,0,3)=='128'){
        @$rpperha=$bar->rupiah/$luasbibitan;
        @$rpperhabbt+=$bar->rupiah/$luasbibitan;
        @$rpperkgtbm=0;
        $tt01tbm[bbt][01]+=$bar->rp01;
        $tt02tbm[bbt][02]+=$bar->rp02;
        $tt03tbm[bbt][03]+=$bar->rp03;
        $tt04tbm[bbt][04]+=$bar->rp04;
        $tt05tbm[bbt][05]+=$bar->rp05;
        $tt06tbm[bbt][06]+=$bar->rp06;
        $tt07tbm[bbt][07]+=$bar->rp07;
        $tt08tbm[bbt][08]+=$bar->rp08;
        $tt09tbm[bbt][09]+=$bar->rp09;
        $tt10tbm[bbt][10]+=$bar->rp10;
        $tt11tbm[bbt][11]+=$bar->rp11;
        $tt12tbm[bbt][12]+=$bar->rp12;
        $ttrptbm[bbt][total]+=$bar->rupiah;
    

   
    
    echo"<tr class=rowcontent>
           <td>".$nobibitan."</td>
           <td>".$bar->noakun."</td>
           <td>".$bar->namaakun."</td>
           <td align=right>".number_format($luasbibitan,2,'.',',')."</td>
           <td align=right>".number_format($bar->rupiah,0,'.',',')."</td>
           <td align=right>".number_format(0,2,'.',',')."</td>     
           <td align=right>".number_format($rpperha,0,'.',',')."</td>    
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
 
 //$gtt
        $gttperha+=0;
        $gttperkg+=0;
        $gtt01+=$tt01tbm[bbt][01];
        $gtt02+=$tt02tbm[bbt][02];
        $gtt03+=$tt03tbm[bbt][03];
        $gtt04+=$tt04tbm[bbt][04];
        $gtt05+=$tt05tbm[bbt][05];
        $gtt06+=$tt06tbm[bbt][06];
        $gtt07+=$tt07tbm[bbt][07];
        $gtt08+=$tt08tbm[bbt][08];
        $gtt09+=$tt09tbm[bbt][09];
        $gtt10+=$tt10tbm[bbt][10];
        $gtt11+=$tt11tbm[bbt][11];
        $gtt12+=$tt12tbm[bbt][12];
        $gtt+=$bar->rupiah;
    $nobibitan+=1;
    }    
}
 echo"<tr class=rowheader>
           <td colspan=4 align=right>Total Biaya BBT</td>
           <td align=right>".number_format($ttrptbm[bbt][total],0,'.',',')."</td>
           <td align=right>".@number_format(0,0,'.',',')."</td>     
           <td align=right>".number_format($rpperhabbt,0,'.',',')."</td>   
           <td align=right>".number_format($tt01tbm[bbt][01],0,'.',',')."</td>
           <td align=right>".number_format($tt02tbm[bbt][02],0,'.',',')."</td>
           <td align=right>".number_format($tt03tbm[bbt][03],0,'.',',')."</td>
           <td align=right>".number_format($tt04tbm[bbt][04],0,'.',',')."</td>
           <td align=right>".number_format($tt05tbm[bbt][05],0,'.',',')."</td>
           <td align=right>".number_format($tt06tbm[bbt][06],0,'.',',')."</td>
           <td align=right>".number_format($tt07tbm[bbt][07],0,'.',',')."</td>
           <td align=right>".number_format($tt08tbm[bbt][08],0,'.',',')."</td>
           <td align=right>".number_format($tt09tbm[bbt][09],0,'.',',')."</td>
           <td align=right>".number_format($tt10tbm[bbt][10],0,'.',',')."</td>
           <td align=right>".number_format($tt11tbm[bbt][11],0,'.',',')."</td>
           <td align=right>".number_format($tt12tbm[bbt][12],0,'.',',')."</td>
         </tr>";
        
//
////TBM
//  $notbm=1;
while($bar=mysql_fetch_object($res))
{
       @$rpperkg=0;
       @$rpperha=0;

    if(substr($bar->noakun,0,3)=='126'){
        if(substr($bar->noakun,0,5)=='12601')
        {
            $luasxtbm2=$luaslc;
            @$rpperha=$bar->rupiah/$luasxtbm2;
            @$rpperhatbm+=$bar->rupiah/$luasxtbm2;
        }
        else if(substr($bar->noakun,0,5)>='12602')
        {
            $luasxtbm2=$luasxtbm;
            @$rpperha=$bar->rupiah/$luasxtbm2;
            @$rpperhatbm+=$bar->rupiah/$luasxtbm2;
        }

        @$rpperkgtbm=0;
        $tt01tbm[tbm][01]+=$bar->rp01;
        $tt02tbm[tbm][02]+=$bar->rp02;
        $tt03tbm[tbm][03]+=$bar->rp03;
        $tt04tbm[tbm][04]+=$bar->rp04;
        $tt05tbm[tbm][05]+=$bar->rp05;
        $tt06tbm[tbm][06]+=$bar->rp06;
        $tt07tbm[tbm][07]+=$bar->rp07;
        $tt08tbm[tbm][08]+=$bar->rp08;
        $tt09tbm[tbm][09]+=$bar->rp09;
        $tt10tbm[tbm][10]+=$bar->rp10;
        $tt11tbm[tbm][11]+=$bar->rp11;
        $tt12tbm[tbm][12]+=$bar->rp12;
        $ttrptbm[tbm][total]+=$bar->rupiah;
//        $tt01tbm+=$bar->rp01;
//        $tt02tbm+=$bar->rp02;
//        $tt03tbm+=$bar->rp03;
//        $tt04tbm+=$bar->rp04;
//        $tt05tbm+=$bar->rp05;
//        $tt06tbm+=$bar->rp06;
//        $tt07tbm+=$bar->rp07;
//        $tt08tbm+=$bar->rp08;
//        $tt09tbm+=$bar->rp09;
//        $tt10tbm+=$bar->rp10;
//        $tt11tbm+=$bar->rp11;
//        $tt12tbm+=$bar->rp12;
//        $ttrptbm+=$bar->rupiah;
//
//
//
//
    echo"<tr class=rowcontent>
           <td>".$notbm."</td>
           <td>".$bar->noakun."</td>
           <td>".$bar->namaakun."</td>
           <td align=right>".number_format($luasxtbm2,2,'.',',')."</td>
           <td align=right>".number_format($bar->rupiah,0,'.',',')."</td>
           <td align=right>".number_format($rpperkg,2,'.',',')."</td>
           <td align=right>".number_format($rpperha,0,'.',',')."</td>
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
//
////GTT
//        $gttperha+=$rpperha;;
//        $gttperkg+=0;
//        $gtt01+=$tt01tbm;
//        $gtt02+=$tt02tbm;
//        $gtt03+=$tt03tbm;
//        $gtt04+=$tt04tbm;
//        $gtt05+=$tt05tbm;
//        $gtt06+=$tt06tbm;
//        $gtt07+=$tt07tbm;
//        $gtt08+=$tt08tbm;
//        $gtt09+=$tt09tbm;
//        $gtt10+=$tt10tbm;
//        $gtt11+=$tt11tbm;
//        $gtt12+=$tt12tbm;
//        $gtt+=$ttrptbm;
            $gtt01+=$tt01tbm[tbm][01];
            $gtt02+=$tt02tbm[tbm][02];
            $gtt03+=$tt03tbm[tbm][03];
            $gtt04+=$tt04tbm[tbm][04];
            $gtt05+=$tt05tbm[tbm][05];
            $gtt06+=$tt06tbm[tbm][06];
            $gtt07+=$tt07tbm[tbm][07];
            $gtt08+=$tt08tbm[tbm][08];
            $gtt09+=$tt09tbm[tbm][09];
            $gtt10+=$tt10tbm[tbm][10];
            $gtt11+=$tt11tbm[tbm][11];
            $gtt12+=$tt12tbm[tbm][12];
            $gtt+=$bar->rupiah;
        $notbm+=1;
    }
}
 echo"<tr class=rowheader>
           <td colspan=4 align=right>Total Biaya TBM</td>
           <td align=right>".number_format($ttrptbm[tbm][total],2,'.',',')."</td>
           <td align=right>".@number_format(0,0,'.',',')."</td>
           <td align=right>".@number_format($rpperhatbm,0,'.',',')."</td>
           <td align=right>".number_format($tt01tbm[tbm][01],0,'.',',')."</td>
           <td align=right>".number_format($tt02tbm[tbm][02],0,'.',',')."</td>
           <td align=right>".number_format($tt03tbm[tbm][03],0,'.',',')."</td>
           <td align=right>".number_format($tt04tbm[tbm][04],0,'.',',')."</td>
           <td align=right>".number_format($tt05tbm[tbm][05],0,'.',',')."</td>
           <td align=right>".number_format($tt06tbm[tbm][06],0,'.',',')."</td>
           <td align=right>".number_format($tt07tbm[tbm][07],0,'.',',')."</td>
           <td align=right>".number_format($tt08tbm[tbm][08],0,'.',',')."</td>
           <td align=right>".number_format($tt09tbm[tbm][09],0,'.',',')."</td>
           <td align=right>".number_format($tt10tbm[tbm][10],0,'.',',')."</td>
           <td align=right>".number_format($tt11tbm[tbm][11],0,'.',',')."</td>
           <td align=right>".number_format($tt12tbm[tbm][12],0,'.',',')."</td>
         </tr>";



// //TM
 $notm=1;
 while($bar=mysql_fetch_object($res1))
  {
       @$rpperkg=0;
       @$rpperha=0;


    if(substr($bar->noakun,0,1)=='6'){//tm
        @$rpperkg=$bar->rupiah/$produksi;
        @$rpperha=$bar->rupiah/$luasxtm;
        @$rpperhatm+=$bar->rupiah/$luasxtm;
        @$rpperkgtm+=$bar->rupiah/$produksi;
//        $tt01tm+=$bar->rp01;
//        $tt02tm+=$bar->rp02;
//        $tt03tm+=$bar->rp03;
//        $tt04tm+=$bar->rp04;
//        $tt05tm+=$bar->rp05;
//        $tt06tm+=$bar->rp06;
//        $tt07tm+=$bar->rp07;
//        $tt08tm+=$bar->rp08;
//        $tt09tm+=$bar->rp09;
//        $tt10tm+=$bar->rp10;
//        $tt11tm+=$bar->rp11;
//        $tt12tm+=$bar->rp12;
//        $ttrptm+=$bar->rupiah;
          $tt01tbm[tm][01]+=$bar->rp01;
        $tt02tbm[tm][02]+=$bar->rp02;
        $tt03tbm[tm][03]+=$bar->rp03;
        $tt04tbm[tm][04]+=$bar->rp04;
        $tt05tbm[tm][05]+=$bar->rp05;
        $tt06tbm[tm][06]+=$bar->rp06;
        $tt07tbm[tm][07]+=$bar->rp07;
        $tt08tbm[tm][08]+=$bar->rp08;
        $tt09tbm[tm][09]+=$bar->rp09;
        $tt10tbm[tm][10]+=$bar->rp10;
        $tt11tbm[tm][11]+=$bar->rp11;
        $tt12tbm[tm][12]+=$bar->rp12;
        $ttrptbm[tm][total]+=$bar->rupiah;
 echo"<tr class=rowcontent>
           <td>".$notm."</td>
           <td>".$bar->noakun."</td>
           <td>".$bar->namaakun."</td>
           <td align=right>".number_format($luasxtm,2,'.',',')."</td>
           <td align=right>".number_format($bar->rupiah,0,'.',',')."</td>
           <td align=right>".number_format($rpperkg,2,'.',',')."</td>
           <td align=right>".number_format($rpperha,0,'.',',')."</td>
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


// //$gtt
//        $gttperha+=$rpperha;;
//        $gttperkg+=$rpperkg;
//        $gtt01+=$tt01tm;
//        $gtt02+=$tt02tm;
//        $gtt03+=$tt03tm;
//        $gtt04+=$tt04tm;
//        $gtt05+=$tt05tm;
//        $gtt06+=$tt06tm;
//        $gtt07+=$tt07tm;
//        $gtt08+=$tt08tm;
//        $gtt09+=$tt09tm;
//        $gtt10+=$tt10tm;
//        $gtt11+=$tt11tm;
//        $gtt12+=$tt12tm;
//        $gtt+=$ttrptm;
            $gtt01+=$tt01tbm[tm][01];
            $gtt02+=$tt02tbm[tm][02];
            $gtt03+=$tt03tbm[tm][03];
            $gtt04+=$tt04tbm[tm][04];
            $gtt05+=$tt05tbm[tm][05];
            $gtt06+=$tt06tbm[tm][06];
            $gtt07+=$tt07tbm[tm][07];
            $gtt08+=$tt08tbm[tm][08];
            $gtt09+=$tt09tbm[tm][09];
            $gtt10+=$tt10tbm[tm][10];
            $gtt11+=$tt11tbm[tm][11];
            $gtt12+=$tt12tbm[tm][12];
            $gtt+=$bar->rupiah;
        $notm+=1;
     }
}
//
 echo"<tr class=rowheader>
           <td colspan=4 align=right>Total Biaya TM</td>
           <td align=right>".number_format($ttrptbm[tm][total],0,'.',',')."</td>
           <td align=right>".@number_format($rpperkgtm,2,'.',',')."</td>
           <td align=right>".@number_format($rpperhatm,0,'.',',')."</td>
           <td align=right>".number_format($tt01tbm[tm][01],0,'.',',')."</td>
           <td align=right>".number_format($tt02tbm[tm][02],0,'.',',')."</td>
           <td align=right>".number_format($tt03tbm[tm][03],0,'.',',')."</td>
           <td align=right>".number_format($tt04tbm[tm][04],0,'.',',')."</td>
           <td align=right>".number_format($tt05tbm[tm][05],0,'.',',')."</td>
           <td align=right>".number_format($tt06tbm[tm][06],0,'.',',')."</td>
           <td align=right>".number_format($tt07tbm[tm][07],0,'.',',')."</td>
           <td align=right>".number_format($tt08tbm[tm][08],0,'.',',')."</td>
           <td align=right>".number_format($tt09tbm[tm][09],0,'.',',')."</td>
           <td align=right>".number_format($tt10tbm[tm][10],0,'.',',')."</td>
           <td align=right>".number_format($tt11tbm[tm][11],0,'.',',')."</td>
           <td align=right>".number_format($tt12tbm[tm][12],0,'.',',')."</td>
         </tr>";


//BY UMUM
$noumum=1;
 while($bar=mysql_fetch_object($res2))
{
       @$rpperkg=0;
       @$rpperha=0;

    if(substr($bar->noakun,0,1)>'6'){//umum
        @$rpperkg=$bar->rupiah/$produksi;
        @$rpperha=$bar->rupiah/($luasxtbm+$luasxtm);
        @$rpperhaum+=$bar->rupiah/($luasxtbm+$luasxtm);
        $rpperkgum+=$rpperkg;
//        $tt01um+=$bar->rp01;
//        $tt02um+=$bar->rp02;
//        $tt03um+=$bar->rp03;
//        $tt04um+=$bar->rp04;
//        $tt05um+=$bar->rp05;
//        $tt06um+=$bar->rp06;
//        $tt07um+=$bar->rp07;
//        $tt08um+=$bar->rp08;
//        $tt09um+=$bar->rp09;
//        $tt10um+=$bar->rp10;
//        $tt11um+=$bar->rp11;
//        $tt12um+=$bar->rp12;
//        $ttrpum+=$bar->rupiah;
            $tt01tbm[um][01]+=$bar->rp01;
            $tt02tbm[um][02]+=$bar->rp02;
            $tt03tbm[um][03]+=$bar->rp03;
            $tt04tbm[um][04]+=$bar->rp04;
            $tt05tbm[um][05]+=$bar->rp05;
            $tt06tbm[um][06]+=$bar->rp06;
            $tt07tbm[um][07]+=$bar->rp07;
            $tt08tbm[um][08]+=$bar->rp08;
            $tt09tbm[um][09]+=$bar->rp09;
            $tt10tbm[um][10]+=$bar->rp10;
            $tt11tbm[um][11]+=$bar->rp11;
            $tt12tbm[um][12]+=$bar->rp12;
            $ttrptbm[um][total]+=$bar->rupiah;
 echo"<tr class=rowcontent>
           <td>".$noumum."</td>
           <td>".$bar->noakun."</td>
           <td>".$bar->namaakun."</td>
           <td align=right>".number_format($luasxtbm+$luasxtm,2,'.',',')."</td>
           <td align=right>".number_format($bar->rupiah,0,'.',',')."</td>
           <td align=right>".number_format($rpperkg,2,'.',',')."</td>
           <td align=right>".number_format($rpperha,0,'.',',')."</td>
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
//
// //$gtt
//        $gttperha+=$rpperha;;
//        $gttperkg+=$rpperkg;
//        $gtt01+=$tt01um;
//        $gtt02+=$tt02um;
//        $gtt03+=$tt03um;
//        $gtt04+=$tt04um;
//        $gtt05+=$tt05um;
//        $gtt06+=$tt06um;
//        $gtt07+=$tt07um;
//        $gtt08+=$tt08um;
//        $gtt09+=$tt09um;
//        $gtt10+=$tt10um;
//        $gtt11+=$tt11um;
//        $gtt12+=$tt12um;
//        $gtt+=$ttrpum;
            $gtt01+=$tt01tbm[um][01];
            $gtt02+=$tt02tbm[um][02];
            $gtt03+=$tt03tbm[um][03];
            $gtt04+=$tt04tbm[um][04];
            $gtt05+=$tt05tbm[um][05];
            $gtt06+=$tt06tbm[um][06];
            $gtt07+=$tt07tbm[um][07];
            $gtt08+=$tt08tbm[um][08];
            $gtt09+=$tt09tbm[um][09];
            $gtt10+=$tt10tbm[um][10];
            $gtt11+=$tt11tbm[um][11];
            $gtt12+=$tt12tbm[um][12];
            $gtt+=$bar->rupiah;
 $noumum+=1;
 }

}

 echo"<tr class=rowheader>
           <td colspan=4 align=right>Total Biaya UMUM</td>
           <td align=right>".number_format($ttrptbm[um][total],0,'.',',')."</td>
           <td align=right>".@number_format($rpperkgum,2,'.',',')."</td>
           <td align=right>".@number_format($rpperhaum,0,'.',',')."</td>
           <td align=right>".number_format($tt01tbm[um][01],0,'.',',')."</td>
           <td align=right>".number_format($tt02tbm[um][02],0,'.',',')."</td>
           <td align=right>".number_format($tt03tbm[um][03],0,'.',',')."</td>
           <td align=right>".number_format($tt04tbm[um][04],0,'.',',')."</td>
           <td align=right>".number_format($tt05tbm[um][05],0,'.',',')."</td>
           <td align=right>".number_format($tt06tbm[um][06],0,'.',',')."</td>
           <td align=right>".number_format($tt07tbm[um][07],0,'.',',')."</td>
           <td align=right>".number_format($tt08tbm[um][08],0,'.',',')."</td>
           <td align=right>".number_format($tt09tbm[um][09],0,'.',',')."</td>
           <td align=right>".number_format($tt10tbm[um][10],0,'.',',')."</td>
           <td align=right>".number_format($tt11tbm[um][11],0,'.',',')."</td>
           <td align=right>".number_format($tt12tbm[um][12],0,'.',',')."</td>
         </tr>";

//
//BY Kapital
$nokapital=1;
 while($bar=mysql_fetch_object($resx1))
{
       @$rpperkg=0;
       @$rpperha=0;

        @$rpperkg=$bar->harga/$produksi;
        @$rpperha=$bar->harga/($luasxtbm+$luasxtm);
        @$rpperhakap+=$bar->harga/($luasxtbm+$luasxtm);
        $rpperkgkap+=$rpperkg;
//        $tt01kap+=$bar->k01;
//        $tt02kap+=$bar->k02;
//        $tt03kap+=$bar->k03;
//        $tt04kap+=$bar->k04;
//        $tt05kap+=$bar->k05;
//        $tt06kap+=$bar->k06;
//        $tt07kap+=$bar->k07;
//        $tt08kap+=$bar->k08;
//        $tt09kap+=$bar->k09;
//        $tt10kap+=$bar->k10;
//        $tt11kap+=$bar->k11;
//        $tt12kap+=$bar->k12;
//        $ttrpkap+=$bar->harga;
            $tt01tbm[kp][01]+=$bar->k01;
            $tt02tbm[kp][02]+=$bar->k02;
            $tt03tbm[kp][03]+=$bar->k03;
            $tt04tbm[kp][04]+=$bar->k04;
            $tt05tbm[kp][05]+=$bar->k05;
            $tt06tbm[kp][06]+=$bar->k06;
            $tt07tbm[kp][07]+=$bar->k07;
            $tt08tbm[kp][08]+=$bar->k08;
            $tt09tbm[kp][09]+=$bar->k09;
            $tt10tbm[kp][10]+=$bar->k10;
            $tt11tbm[kp][11]+=$bar->k11;
            $tt12tbm[kp][12]+=$bar->k12;
            $ttrptbm[kp][total]+=$bar->harga;
 echo"<tr class=rowcontent>
           <td>".$nokapital."</td>
           <td>".$bar->noakun."</td>
           <td>".$bar->namatipe."</td>
           <td align=right>".number_format($luasxtbm+$luasxtm,2,'.',',')."</td>
           <td align=right>".number_format($bar->harga,0,'.',',')."</td>
           <td align=right>".number_format($rpperkg,2,'.',',')."</td>
           <td align=right>".number_format($rpperha,0,'.',',')."</td>
           <td align=right>".number_format($bar->k01,0,'.',',')."</td>
           <td align=right>".number_format($bar->k02,0,'.',',')."</td>
           <td align=right>".number_format($bar->k03,0,'.',',')."</td>
           <td align=right>".number_format($bar->k04,0,'.',',')."</td>
           <td align=right>".number_format($bar->k05,0,'.',',')."</td>
           <td align=right>".number_format($bar->k06,0,'.',',')."</td>
           <td align=right>".number_format($bar->k07,0,'.',',')."</td>
           <td align=right>".number_format($bar->k08,0,'.',',')."</td>
           <td align=right>".number_format($bar->k09,0,'.',',')."</td>
           <td align=right>".number_format($bar->k10,0,'.',',')."</td>
           <td align=right>".number_format($bar->k11,0,'.',',')."</td>
           <td align=right>".number_format($bar->k12,0,'.',',')."</td>
         </tr>";
 //$gtt
        $gttperha+=$rpperha;
        $gttperkg+=$rpperkg;
//        $gtt01+=$tt01kap;
//        $gtt02+=$tt02kap;
//        $gtt03+=$tt03kap;
//        $gtt04+=$tt04kap;
//        $gtt05+=$tt05kap;
//        $gtt06+=$tt06kap;
//        $gtt07+=$tt07kap;
//        $gtt08+=$tt08kap;
//        $gtt09+=$tt09kap;
//        $gtt10+=$tt10kap;
//        $gtt11+=$tt11kap;
//        $gtt12+=$tt12kap;
        //$gtt+=$ttrpkap;
        $gtt01+=$tt01tbm[kp][01];
        $gtt02+=$tt02tbm[kp][02];
        $gtt03+=$tt03tbm[kp][03];
        $gtt04+=$tt04tbm[kp][04];
        $gtt05+=$tt05tbm[kp][05];
        $gtt06+=$tt06tbm[kp][06];
        $gtt07+=$tt07tbm[kp][07];
        $gtt08+=$tt08tbm[kp][08];
        $gtt09+=$tt09tbm[kp][09];
        $gtt10+=$tt10tbm[kp][10];
        $gtt11+=$tt11tbm[kp][11];
        $gtt12+=$tt12tbm[kp][12];
        $gtt+=$bar->harga;
        $nokapital+=1;
}

 echo"<tr class=rowheader>
           <td colspan=4 align=right>Total KAPITAL</td>
           <td align=right>".number_format($ttrptbm[kp][total],0,'.',',')."</td>
           <td align=right>".@number_format($rpperkgkap,2,'.',',')."</td>
           <td align=right>".@number_format($rpperhakap,0,'.',',')."</td>
           <td align=right>".number_format($tt01tbm[kp][01],0,'.',',')."</td>
           <td align=right>".number_format($tt02tbm[kp][02],0,'.',',')."</td>
           <td align=right>".number_format($tt03tbm[kp][03],0,'.',',')."</td>
           <td align=right>".number_format($tt04tbm[kp][04],0,'.',',')."</td>
           <td align=right>".number_format($tt05tbm[kp][05],0,'.',',')."</td>
           <td align=right>".number_format($tt06tbm[kp][06],0,'.',',')."</td>
           <td align=right>".number_format($tt07tbm[kp][07],0,'.',',')."</td>
           <td align=right>".number_format($tt08tbm[kp][08],0,'.',',')."</td>
           <td align=right>".number_format($tt09tbm[kp][09],0,'.',',')."</td>
           <td align=right>".number_format($tt10tbm[kp][10],0,'.',',')."</td>
           <td align=right>".number_format($tt11tbm[kp][11],0,'.',',')."</td>
           <td align=right>".number_format($tt12tbm[kp][12],0,'.',',')."</td>
         </tr>";
////GTT=======================================================================
//
        $gttperkg=$gtt/$produksi;
        $gttperha=$gtt/($luasxtbm+$luasxtm); 
 echo"<tr class=rowheader>
           <td colspan=4 align=right>GRAND TOTAL</td>
           <td align=right>".number_format($gtt,0,'.',',')."</td>
           <td align=right>".@number_format($gttperkg,2,'.',',')."</td>
           <td align=right>".@number_format($gttperha,0,'.',',')."</td>
           <td align=right>".number_format($gtt01,0,'.',',')."</td>
           <td align=right>".number_format($gtt02,0,'.',',')."</td>
           <td align=right>".number_format($gtt03,0,'.',',')."</td>
           <td align=right>".number_format($gtt04,0,'.',',')."</td>
           <td align=right>".number_format($gtt05,0,'.',',')."</td>
           <td align=right>".number_format($gtt06,0,'.',',')."</td>
           <td align=right>".number_format($gtt07,0,'.',',')."</td>
           <td align=right>".number_format($gtt08,0,'.',',')."</td>
           <td align=right>".number_format($gtt09,0,'.',',')."</td>
           <td align=right>".number_format($gtt10,0,'.',',')."</td>
           <td align=right>".number_format($gtt11,0,'.',',')."</td>
           <td align=right>".number_format($gtt12,0,'.',',')."</td>
         </tr>";
 
echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table></fieldset>";
?>