<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

        $pt=$_GET['pt'];
        if($pt==''){
            $pt=$_SESSION['org']['kodeorganisasi'];
        }
        $gudang=$_GET['gudang'];
        $tanggal=$_GET['tanggal'];
        $tanggalpivot=$_GET['tanggalpivot'];
        $tanggaljttempo=tanggaldgnbar($tanggalpivot);
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='Seluruhnya';
//exit("error:".$str);
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $namapt=strtoupper($bar->namaorganisasi);
}
    $stream='';

if($gudang!='')
{
//                $str="select distinct(namasupplier) as namasupplier from ".$dbname.".aging_sch_vw
//                where tanggal > '2011-12-31' and kodeorg = '".$gudang."'and (nilaiinvoice > dibayar or dibayar is NULL) order by namasupplier
//                ";
                  $whr.=" and unit = '".$gudang."' ";
}else
if($pt!='')
{
                /*$str="select distinct(namasupplier) as namasupplier from ".$dbname.".aging_sch_vw
                where tanggal > '2011-12-31' and kodeorg = '".$pt."'and (nilaiinvoice > dibayar or dibayar is NULL) order by namasupplier
                ";*/
//				 $str="select distinct(namasupplier) as namasupplier from ".$dbname.".aging_sch_vw
//                where tanggal > '2012-12-31' and kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')
//				 and (nilaiinvoice > dibayar or dibayar is NULL) order by namasupplier
//                ";
			$whr.=" and kodeorg = '".$pt."'";	
}
//else
//{
//                $str="select distinct(namasupplier) as namasupplier from ".$dbname.".aging_sch_vw
//                where tanggal > '2011-12-31' and (nilaiinvoice > dibayar or dibayar is NULL) order by namasupplier
//                ";
//}
$tanggal=tanggaldgnbar($tanggal);
$str="select * from ".$dbname.".aging_sch_vw
      where tanggal > '".$tanggal."' and tanggalvp<='".$tanggaljttempo."' and novp IS NOT NULL
      ".$whr." order by namasupplier asc";
function tanggalbiasa($_q)
{
 $_q=str_replace("-","",$_q);
 $_retval=substr($_q,4,4)."-".substr($_q,2,2)."-".substr($_q,0,2);
 return($_retval);
}

//=================================================
        $res=mysql_query($str);
        $no=0;
        if(@mysql_num_rows($res)<1)
        {
                echo"<tr class=rowcontent><td colspan=13>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
        }
        else
        {
			
			
			
                $stream.="<table border=1>
                    <tr>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nourut']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tanggal']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['noinvoice']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['novp']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['matauang']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['kurs']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['namasupplier']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['jatuhtempo']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nopokontrak']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nilaipokontrak']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nilaivp']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nilaivp']." (IDR)</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['belumjatuhtempo']."</td>
                          <td nowrap align=center colspan=4 bgcolor=#CCCCCC>".$_SESSION['lang']['sudahjatuhtempo']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['dibayar']."</td>
                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['jmlh_hari_outstanding']."</td>
                        </tr>  
                    <tr>
                          <td bgcolor=#CCCCCC nowrap align=center>1-30 ".$_SESSION['lang']['hari']."</td>
                          <td bgcolor=#CCCCCC nowrap align=center>31-60 ".$_SESSION['lang']['hari']."</td>
                          <td bgcolor=#CCCCCC nowrap align=center>61-90 ".$_SESSION['lang']['hari']."</td>
                          <td bgcolor=#CCCCCC nowrap align=center>over 100 ".$_SESSION['lang']['hari']."</td>
                        </tr>";  
                        $grantotal0=$grantotal30=$grandtotal60=$grantotal90=$grantotal100=$grantotaldibayar=0;
                        $totalinvoice=0;
                           while($bar=mysql_fetch_assoc($res)){
                            //filter hanya yang sudah dibayar per tanggal cetak
                            if ($bar['dibayar']==NULL) {
                                $dbayar=0;
                            } else {
                                if ($bar['tanggalbayar']<=$tanggaljttempo){
                                    $dbayar=$bar['dibayar'];
                                } else {
                                    $dbayar=0;
                                }
                            }

                            if (($bar['nilaiinvoice']+$bar['nilaippn'])>$dbayar)
                            {
                               
                                if($suppid!=$bar['kodesupplier']){
                                    $suppid=$bar['kodesupplier'];
                                    $not=1;	
                                }else{
                                    $not+=1;  
                                }
                                $rowd[$bar['kodesupplier']]=$not;
                                $lstSupp[$bar['kodesupplier']]=$bar['kodesupplier'];
                                $lstNmSupp[$bar['kodesupplier']]=$bar['namasupplier'];
                                $lstInv[$bar['noinvoice']]=$bar['noinvoice']; 
                                $dtInv[$bar['kodesupplier'].$not]=$bar['noinvoice'];
                                $dtTgl[$bar['noinvoice']]=$bar['tanggal'];
                                $dtJthTmp[$bar['noinvoice']]=$bar['jatuhtempo'];
                                $dtNopo[$bar['noinvoice']]=$bar['nopo'];
                                $dtKurs[$bar['noinvoice']]=$bar['kurs'];
                                $dtMtUang[$bar['noinvoice']]=$bar['matauang'];
                                $dtNilaipo[$bar['noinvoice']]=$bar['nilaipo'];
                                $dtNilaikontrak[$bar['noinvoice']]=$bar['nilaikontrak'];
                                $dtNilaiinvoice[$bar['noinvoice']]=($bar['nilaiinvoice']+$bar['nilaippn']);
                                    $str="select sum(jumlah) as jumlah from ".$dbname.".keu_vpdt where novp='".$bar['novp']."' and noakun in (2111101,2111102,2111104,2111201,2111202)";
                                    $resvp=mysql_query($str);
                                    while($barvp=mysql_fetch_assoc($resvp)){
                                        $dtNilaiinvoice[$bar['noinvoice']]=($barvp['jumlah']);
                                    }
                                $dtDibayar[$bar['noinvoice']]=$dbayar;
                                $dtNoVP[$bar['noinvoice']]=$bar['novp'];
                            }
                           }
                           
                           $totInvSb=array();
                           foreach($lstSupp as $dtSupplier){
                               for($awal=1;$awal<=$rowd[$dtSupplier];$awal++){
                                if($awal==1){
						//0-30, 31-60 61-90 diatas 90
						
						$rowddata=$rowd[$dtSupplier];
						$no=1;
						$subTotInvoice=0;
						$total0=0;
						$total30=0;
						$total60=0;
						$total90=0;
						$total100=0;
						$row=0;
					}
                        $namasupplier	=$lstNmSupp[$dtSupplier];
                            //if($namasupplier=='')$namasupplier='&nbsp;';
                            $noinvoice	=$dtInv[$dtSupplier.$awal];
                            $tanggal	=$dtTgl[$noinvoice]; 
                            $jatuhtempo =$dtJthTmp[$noinvoice];
                            $nopokontrak    =$dtNopo[$noinvoice];
                            $nilaipo        =$dtNilaipo[$noinvoice];
                            $nilaikontrak   =$dtNilaikontrak[$noinvoice];
                            $nilaiinvoice   =$dtNilaiinvoice[$noinvoice];
                            
                            //$totalinvoice+=$nilaiinvoice;
                            $dibayar 	=$dtDibayar[$noinvoice];
                        $sisainvoice    =$nilaiinvoice-$dibayar;
                        if($dtMtUang[$noinvoice]!='IDR'){
                            $sisainvoice=$sisainvoice*$dtKurs[$noinvoice];
                            $dibayar=$dibayar*$dtKurs[$noinvoice];
                        }
                        $nilaipokontrak =$nilaipo;
                        if($nilaikontrak>0)$nilaipokontrak=$nilaikontrak;
//			$date1=date('Y-m-d');
                        $date1=$tanggalpivot;
                        $diff =(strtotime($jatuhtempo)-strtotime($date1));
                        $outstd =floor(($diff)/(60*60*24));
//			if($outstd<1)$outstd=0;
                       $flag0=$flag30=$flag60=$flag90=$flag100=0;
                        if($outstd!=0)$outstd*=-1;
                        if($outstd<=0)$flag0=1; 
                        if(($outstd>=1)and($outstd<=30))$flag30=1;
                        if(($outstd>=31)and($outstd<=60))$flag60=1;
                        if(($outstd>=61)and($outstd<=90))$flag90=1;
                        if($outstd>90)$flag100=1;
						
                        if($flag0==1)$total0+=$sisainvoice;
                        if($flag30==1)$total30+=$sisainvoice;
                        if($flag60==1)$total60+=$sisainvoice;
                        if($flag90==1)$total90+=$sisainvoice;
                        if($flag100==1)$total100+=$sisainvoice;
                      //  $totaldibayar+=$dibayar;
                        if($jatuhtempo=='0000-00-00'){ $outstd=''; $jatuhtempo=''; }
//			if($dibayar>=$nilaiinvoice)continue;
                       $totInv=$dtKurs[$noinvoice]*$nilaiinvoice;
                       $totInvSb[$dtSupplier]+=$dtKurs[$noinvoice]*$nilaiinvoice;
                        $stream.="<tr>
                                  <td nowrap align=center>".$no."</td>
                                  <td nowrap align=center>".$tanggal."</td>
                                  <td nowrap align=left nowrap>&nbsp;".$noinvoice."</td> 
                                  <td nowrap align=left nowrap>&nbsp;".$dtNoVP[$noinvoice]."</td> 
                                  <td nowrap align=left nowrap>&nbsp;".$dtMtUang[$noinvoice]."</td> 
                                  <td nowrap align=right nowrap>&nbsp;".$dtKurs[$noinvoice]."</td> 
                                  <td nowrap align=left nowrap>".$namasupplier."</td> 
                                  <td nowrap align=center>".$jatuhtempo."</td>
                                  <td nowrap align=center>".$nopokontrak."</td>
                                  <td nowrap align=right>".number_format($nilaipokontrak,2)."</td>
                                  <td nowrap align=right>".number_format($nilaiinvoice,2)."</td>
                                  <td nowrap align=right>".number_format($totInv,2)."</td>
                                  <td nowrap align=right>";
                                  if($dtMtUang[$noinvoice]!='IDR' && $sisainvoice<15000){
                                        $sisainvoice=$sisainvoice*$dtKurs[$noinvoice];
                                  }
                                  if($flag0==1)$stream.=number_format($sisainvoice,2); else $stream.=number_format(0,2); $stream.="</td>
                                  <td nowrap align=right>";
                                  if($flag30==1)$stream.=number_format($sisainvoice,2); else $stream.=number_format(0,2); $stream.="</td>
                                  <td nowrap align=right>";
                                  if($flag60==1)$stream.=number_format($sisainvoice,2); else $stream.=number_format(0,2); $stream.="</td>
                                  <td nowrap align=right>";
                                  if($flag90==1)$stream.=number_format($sisainvoice,2); else $stream.=number_format(0,2); $stream.="</td>
                                  <td nowrap align=right>";
                                  if($flag100==1)$stream.=number_format($sisainvoice,2); else $stream.=number_format(0,2); $stream.="</td>
                                  <td nowrap align=right>".number_format($dibayar,2)."</td>
                                  <td nowrap align=right>".$outstd."</td>
                                </tr>";
                                    $subTotInvoice+=$nilaiinvoice;
                                    $subtotaldibayar[$dtSupplier]+=$dibayar;
						
					//	echo $total0;
				$no+=1;
				$row+=1;
				if($row==$rowddata){
						
				 $stream.="<thead>";
                                 $stream.="<tr>
                                  <td colspan=10 align=center><b>".$_SESSION['lang']['subtotal']." ".$namasupplier."</b></td>
                                  <td align=right><b>".number_format($subTotInvoice,2)."</td>
                                  <td align=right><b>".number_format($totInvSb[$dtSupplier],2)."</td>
                                  <td align=right><b>".number_format($total0,2)."</td>
                                  <td align=right><b>".number_format($total30,2)."</td>
                                  <td align=right><b>".number_format($total60,2)."</td>
                                  <td align=right><b>".number_format($total90,2)."</td>
                                  <td align=right><b>".number_format($total100,2)."</td>
                                  <td align=right><b>".number_format($subtotaldibayar[$dtSupplier],2)."</td>
                                  <td align=right>&nbsp;</td>
                                  </tr>";
                        $stream.="</thead>";
				$totalinvoice+=$subTotInvoice;
				$grantotaldibayar+=$subtotaldibayar[$dtSupplier];
				$grantotal0+=$total0;
				$grantotal30+=$total30;
				$grantotal60+=$total60;
				$grantotal90+=$total90;
				$grantotal100+=$total100;
                                $totIdrInv+=$totInvSb[$dtSupplier];
				}
						
						
						
                               }		
						
				
			}	
                $stream.="<tr class=rowtitle bgcolor=#0066FF>
                                  <td colspan=10 align=center><b>".$_SESSION['lang']['grnd_total']."</b></td>
                                  <td align=right><b>".number_format($totalinvoice,2)."</td>
                                  <td align=right><b>".number_format($totIdrInv,2)."</td>
                                  <td align=right><b>".number_format($grantotal0,2)."</td>
                                  <td align=right><b>".number_format($grantotal30,2)."</td>
                                  <td align=right><b>".number_format($grantotal60,2)."</td>
                                  <td align=right><b>".number_format($grantotal90,2)."</td>
                                  <td align=right><b>".number_format($grantotal100,2)."</td>
                                  <td align=right><b>".number_format($grantotaldibayar,2)."</td>
                                  <td align=right>&nbsp;</td>
                        </tr>";    
          $stream.="</table>";	
        }
		//exit("Error:$stream");
$stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];

$nop_="DaftarUsiaHutang";
if(strlen($stream)>0)
{
if ($handle = opendir('tempExcel')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            @unlink('tempExcel/'.$file);
        }
    }	
   closedir($handle);
}
 $handle=fopen("tempExcel/".$nop_.".xls",'w');
 if(!fwrite($handle,$stream))
 {
  echo "<script language=javascript1.2>
        parent.window.alert('Can't convert to excel format');
        </script>";
   exit;
 }
 else
 {
  echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls';
        </script>";
 }
closedir($handle);
}
?>