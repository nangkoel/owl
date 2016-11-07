<?php //@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');
include('lib/zLib.php');


switch($_POST['method']){
    case'getDetailPP':
$str="select * from ".$dbname.".pabrik_produksi
      where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tgl']."'";
//echo $str;
$res=mysql_query($str) or die(mysql_error($conn));
$rdata=mysql_fetch_assoc($res);
echo "<fieldset style='width:700px;'>
        <legend>".$_SESSION['lang']['data'].":</legend>
		<table><tr><td>
		
		<table>
		   <tr>
		     <td>
			    ".$_SESSION['lang']['kodeorganisasi']."
			 </td>
		     <td>".$rdata['kodeorg']."
			 </td>
		   </tr>
		   <tr> 
			 <td>".$_SESSION['lang']['tanggal']."</td>
			 <td>".tanggalnormal($rdata['tanggal'])."
			 </td>	
		     <td>		 
		 </tr>
		   <tr>
		     <td>
			    ".$_SESSION['lang']['sisatbskemarin']."
			 </td>
		     <td>".number_format($rdata['sisatbskemarin'],0)."
			 </td>
		   </tr>
		   <tr> 
		     <td>
			    ".$_SESSION['lang']['tbsmasuk']."
			 </td>
			 <td>
			    ".number_format($rdata['tbsmasuk'],0)."
			 </td>	 		 
		 </tr>		
		 <tr>
		     <td>
			    ".$_SESSION['lang']['tbsdiolah']."
			 </td>
		     <td>
			    ".number_format($rdata['tbsdiolah'],0)."
			 </td>		 
		 </tr>
		 <tr>
		     <td>
			    ".$_SESSION['lang']['sisa']."
			 </td>
			 <td>   ".number_format($rdata['sisahariini'],0)."
			 </td>		 
		 </tr>	";
               echo" <tr>
		     <td>% USB Before Crusher
			 </td>
		     <td>".$rdata['usbbefore']." %
			 </td>		 
		 </tr>	  
                  <tr>
		     <td>% USB After Crusher
			 </td>
		     <td>".$rdata['usbafter']." %
			 </td>		 
		 </tr>	
                  <tr>
		     <td>% Oil Diluted Crude Oil
			 </td>
		     <td>".$rdata['oildiluted']." %
			 </td>		 
		 </tr>	
                  <tr>
		     <td>% Oil in underflow (CST)
			 </td>
		     <td>".$rdata['oilin']." %
			 </td>		 
		 </tr>	
                  <tr>
		     <td>% Oil in Heavy Phase - S/D
			 </td>
		     <td>".$rdata['oilinheavy']." % 
			 </td>		 
		 </tr>	
                  <tr>
		     <td>CaCO3
			 </td>
		     <td>".$rdata['caco']." KG
			 </td>		 
		 </tr>	";
	  echo"</table>	  
	  </td>
	  <td valign=top>  
  	<table>
		<tr>
		<td> 
		 <fieldset><legend>".$_SESSION['lang']['cpo']."</legend>
		 <table>
		 <tr><td>".$_SESSION['lang']['cpo']."(Kg)
			 </td>
			 <td>
			   ".$rdata['oer']."
			 </td>
		  </tr>
                  <tr><td>".$_SESSION['lang']['oer']."
			 </td>
			 <td>
			   ".(@number_format($rdata['oer']/$rdata['tbsdiolah']*100,2,'.',','))."
			 </td>
		  </tr>
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kotoran']."
			 </td>
		     <td>
			  ".$rdata['kadarkotoran']."%
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kadarair']."
			 </td>
			 <td>
			   ".$rdata['kadarair']."%.
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    FFa
			 </td>
		     <td>
			  ".$rdata['ffa']." %. 
			 </td>			 
		 </tr>	
                  <tr>
		     <td>
			    Dobi
			 </td>
		     <td>
			  ".$rdata['dobi']." %. 
			 </td>			 
		 </tr>	
		</table>
		</fieldset>
		
		</td>
		</tr>
                
<tr>
		<td> 
		 <fieldset><legend>".$_SESSION['lang']['cpo']." Loses</legend>
		 <table>
		 <tr><td>USB.
			 </td>
			 <td>
			    ".$rdata['fruitineb']." KG/TON
			 </td>
		  </tr>
		 <tr>
		     <td>EB. Stalk 
			 </td>
		     <td>".$rdata['ebstalk']."
			 </td>
		 </tr>	
		 <tr>
		     <td> Fibre From Press Cake
			 </td>
			 <td>".$rdata['fibre']."
			 </td>
		 </tr>	
		 <tr>
		     <td>Nut From Press Cake
			 </td>
		     <td>".$rdata['nut']."
			 </td>			 
		 </tr>	
                  <tr>
		     <td>Effluent
			 </td>
		     <td>".$rdata['effluent']."
			 </td>			 
		 </tr>	
                   <tr>
		     <td>Solid Decanter
			 </td>
		     <td>".$rdata['soliddecanter']."
			 </td>			 
		 </tr>	
		</table>
		</fieldset>
		
		</td>
		</tr>
		</table>	
    </td>
	<td valign=top>
  	<table>
		<tr>
		<td> 
		 <fieldset><legend>".$_SESSION['lang']['kernel']."</legend>
		 <table>
		 <tr><td>
			    ".$_SESSION['lang']['kernel']."(Kg)
			 </td>
			 <td>
			    ".$rdata['oerpk']." Kg.
			 </td>
		  </tr>
                  <tr><td>
			    ".$_SESSION['lang']['oerpk']."
			 </td>
			 <td>
			    ".(@number_format($rdata['oerpk']/$rdata['tbsdiolah']*100,2,'.',','))." 
			 </td>
		  </tr>
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kotoran']."
			 </td>
		     <td>".$rdata['kadarkotoranpk']." %
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kadarair']."
			 </td>
			 <td>".$rdata['kadarairpk']." %. 
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    FFa
			 </td>
		     <td>".$rdata['ffapk']." %.
			 </td>			 
		 </tr>	
		 
			<tr>
		     <td>
			    Inti Pecah
			 </td>
		     <td>".$rdata['intipecah']." %.
			 </td>			 
		 </tr>
                 <tr>
		     <td>
			  Batu
			 </td>
		     <td>".$rdata['batu']." %.
			 </td>			 
		 </tr>
                 
		</table>
		</fieldset>
		
		</td>
		</tr>
                <tr>
		<td> 
		 <fieldset><legend>".$_SESSION['lang']['kernel']." Loses</legend>
		 <table>
		 <tr><td>USB.

			 </td>
			 <td>".$rdata['fruitinebker']." KG/TON
			 </td>
		  </tr>
		 <tr>
		     <td>Fibre Cyclone
			 </td>
		     <td>".$rdata['cyclone']."
			 </td>
		 </tr>	
		 <tr>
		     <td>LTDS
			 </td>
			 <td>".$rdata['ltds']."
			 </td>
		 </tr>	
		 <tr>
		     <td>Claybath
			 </td>
		     <td>".$rdata['claybath']."
			 </td>			 
		 </tr>	
                 
		</table>
		</fieldset>
		
		</td>
		</tr>
		</table>	
			
	
	</td>
	</tr>	  
	  
	</table>	
	  </fieldset>
	 ";
      break;
      case'getCpo':
//            $tglSystemHr=date('Ymd');//tgl hari ini
//            $tglInputan=tanggalsystem($_POST['tanggal']);
//            if($tglInputan>=$tglSystemHr){
//                exit("error: Use date before :".date("d-m-Y"));
//            }
            $tglck=tanggaldgnbar($_POST['tanggal']);
            $tglShari = nambahHari($_POST['tanggal'],'1','1');
            //exit("error:".$tglShari);
            
          if($_SESSION['empl']['lokasitugas']=='H01M'){
                #pengiriman cpo hari ini
                $sHrini="select sum(beratbersih) as jmlhCpoKirim from ".$dbname.".pabrik_timbangan 
                         where millcode='".$_SESSION['empl']['lokasitugas']."' 
                         and left(tanggal,10)='".$tglck."' and kodebarang='40000007'";
                $qhrIni=mysql_query($sHrini) or die(mysql_error($conn));
                $rHrini=mysql_fetch_assoc($qhrIni);

                $sIsiTangkiKmrnA1="select kuantitas as jmlhCpoKmrn from ".$dbname.".pabrik_masukkeluartangki where 
                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglck."'
                                 and kodetangki='ST01' order by tanggal desc limit 0,1";
                $qIsiTangkiKmrnA1=mysql_query($sIsiTangkiKmrnA1) or die(mysql_error($conn));
                $rIsiTangkiKmrnA1=mysql_fetch_assoc($qIsiTangkiKmrnA1);
                
                $sIsiTangkiKmrnA2="select kuantitas as jmlhCpoKmrn from ".$dbname.".pabrik_masukkeluartangki where 
                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglck."'
                                 and kodetangki='ST02' order by tanggal desc limit 0,1";
                $qIsiTangkiKmrnA2=mysql_query($sIsiTangkiKmrnA2) or die(mysql_error($conn));
                $rIsiTangkiKmrnA2=mysql_fetch_assoc($qIsiTangkiKmrnA2);
                $A=$rIsiTangkiKmrnA1['jmlhCpoKmrn']+$rIsiTangkiKmrnA2['jmlhCpoKmrn'];

                $sIsiTangkiKmrn2B1="select kuantitas as jmlhCpoKmrn from ".$dbname.".pabrik_masukkeluartangki where 
                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglShari."'
                                 and kodetangki='ST01'order by tanggal desc limit 0,1";
                $qIsiTangkiKmrn2B1=mysql_query($sIsiTangkiKmrn2B1) or die(mysql_error($conn));
                $rIsiTangkiKmrn2B1=mysql_fetch_assoc($qIsiTangkiKmrn2B1);
                
                $sIsiTangkiKmrn2B2="select kuantitas as jmlhCpoKmrn from ".$dbname.".pabrik_masukkeluartangki where 
                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglShari."'
                                 and kodetangki='ST02'order by tanggal desc limit 0,1";
                $qIsiTangkiKmrn2B2=mysql_query($sIsiTangkiKmrn2B2) or die(mysql_error($conn));
                $rIsiTangkiKmrn2B2=mysql_fetch_assoc($qIsiTangkiKmrn2B2);
                
                $B=$rIsiTangkiKmrn2B1['jmlhCpoKmrn']+$rIsiTangkiKmrn2B2['jmlhCpoKmrn'];
                
                
                $cpo=0;
                $hslTambah=0;
                if(($A=='')||($B=='')){
                    $cpo=1;
                }
                //$hslTambah=$rIsiTangkiKmrn2['jmlhCpoKmrn']-$rIsiTangkiKmrn['jmlhCpoKmrn']+$rIsiTangkiKmrn['jmlhCpoKmrn'];
                $hslTambah=$B-$A+$rHrini['jmlhCpoKirim'];
           }else if($_SESSION['empl']['lokasitugas']=='L01M'){
               $sHrini="select sum(beratbersih) as jmlhCpoKirim from ".$dbname.".pabrik_timbangan 
                         where millcode='".$_SESSION['empl']['lokasitugas']."' 
                         and left(tanggal,10)='".$tglck."' and kodebarang='40000001'";
                //exit("error:".$sHrini);
                $qhrIni=mysql_query($sHrini) or die(mysql_error($conn));
                $rHrini=mysql_fetch_assoc($qhrIni);
                
                $sIsiTangkiKmrnA1="select kuantitas as jmlhCpoKmrn from ".$dbname.".pabrik_masukkeluartangki where 
                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglck."'
                                 and kodetangki='ST01' order by tanggal desc limit 0,1";
                $qIsiTangkiKmrnA1=mysql_query($sIsiTangkiKmrnA1) or die(mysql_error($conn));
                $rIsiTangkiKmrnA1=mysql_fetch_assoc($qIsiTangkiKmrnA1);
                
                $sIsiTangkiKmrnA2="select kuantitas as jmlhCpoKmrn from ".$dbname.".pabrik_masukkeluartangki where 
                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglck."'
                                 and kodetangki='ST02' order by tanggal desc limit 0,1";
                $qIsiTangkiKmrnA2=mysql_query($sIsiTangkiKmrnA2) or die(mysql_error($conn));
                $rIsiTangkiKmrnA2=mysql_fetch_assoc($qIsiTangkiKmrnA2);
                $sIsiTangkiKmrnA3="select kuantitas as jmlhCpoKmrn from ".$dbname.".pabrik_masukkeluartangki where 
                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglck."'
                 and kodetangki='ST03' order by tanggal desc limit 0,1";
                $qIsiTangkiKmrnA3=mysql_query($sIsiTangkiKmrnA3) or die(mysql_error($conn));
                $rIsiTangkiKmrnA3=mysql_fetch_assoc($qIsiTangkiKmrnA3);

                $A=$rIsiTangkiKmrnA1['jmlhCpoKmrn']+$rIsiTangkiKmrnA2['jmlhCpoKmrn']+$rIsiTangkiKmrnA3['jmlhCpoKmrn'];

                $sIsiTangkiKmrn2B1="select kuantitas as jmlhCpoKmrn from ".$dbname.".pabrik_masukkeluartangki where 
                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglShari."'
                                 and kodetangki='ST01'order by tanggal desc limit 0,1";
                $qIsiTangkiKmrn2B1=mysql_query($sIsiTangkiKmrn2B1) or die(mysql_error($conn));
                $rIsiTangkiKmrn2B1=mysql_fetch_assoc($qIsiTangkiKmrn2B1);
                
                $sIsiTangkiKmrn2B2="select kuantitas as jmlhCpoKmrn from ".$dbname.".pabrik_masukkeluartangki where 
                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglShari."'
                                 and kodetangki='ST02'order by tanggal desc limit 0,1";
                $qIsiTangkiKmrn2B2=mysql_query($sIsiTangkiKmrn2B2) or die(mysql_error($conn));
                $rIsiTangkiKmrn2B2=mysql_fetch_assoc($qIsiTangkiKmrn2B2);
                
                $sIsiTangkiKmrn2B3="select kuantitas as jmlhCpoKmrn from ".$dbname.".pabrik_masukkeluartangki where 
                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglShari."'
                                 and kodetangki='ST03'order by tanggal desc limit 0,1";
                $qIsiTangkiKmrn2B3=mysql_query($sIsiTangkiKmrn2B3) or die(mysql_error($conn));
                $rIsiTangkiKmrn2B3=mysql_fetch_assoc($qIsiTangkiKmrn2B3);
                
                $B=$rIsiTangkiKmrn2B1['jmlhCpoKmrn']+$rIsiTangkiKmrn2B2['jmlhCpoKmrn']+$rIsiTangkiKmrn2B3['jmlhCpoKmrn'];
                $cpo=0;
                $hslTambah=0;
                if(($A=='')||($B=='')){
                    $cpo=1;
                }
                $hslTambah=$B-$A+$rHrini['jmlhCpoKmrn'];
                //$hslTambah=$rIsiTangkiKmrn2['jmlhCpoKmrn']-$rIsiTangkiKmrn['jmlhCpoKmrn']+$rIsiTangkiKmrn['jmlhCpoKmrn'];
           }
            echo $cpo."####".$hslTambah;
      break;
	  case'getFormDet':
	  if($_SESSION['empl']['regional']=='SULAWESI'){
		$sTangki="select kodetangki,keterangan from ".$dbname.".pabrik_5tangki where kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki in ('ST01','ST02','BKL01')";
	  }else{
		$sTangki="select kodetangki,keterangan from ".$dbname.".pabrik_5tangki where kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki in ('ST01','ST02','BKL01')";
	  }
	  $qTangki=mysql_query($sTangki) or die(mysql_error($conn));
	  while($rTangki=mysql_fetch_assoc($qTangki)){
		$optTangki.="<option value='".$rTangki['kodetangki']."'>".$rTangki['keterangan']."</option>";
	  }
		
		
		$tab.="<fieldset><legend>".$_SESSION['lang']['detail']." ".$_POST['tgl']."</legend>";
		$tab.="<table cellspacing=1 cellpadding=1 border=0>";
		$tab.="<thead><tr>";
		$tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
		$tab.="<td>".$_SESSION['lang']['kodetangki']."</td>";
		$tab.="<td>".$_SESSION['lang']['jumlah']."</td>";
		$tab.="<td>".$_SESSION['lang']['keterangan']."</td>";
		$tab.="<td>".$_SESSION['lang']['action']."</td>";
		$tab.="</tr></thead><tbody>";
		$tab.="<tr class=rowcontent>";
		$tab.="<td><input type=text id=tglId disabled class=myinputtext value='".$_POST['tgl']."' /></td>";
		
		$tab.="<td><select id=kdTangki>".$optTangki."</select></td>";
		$tab.="<td><input type=text id=jmlhDet  class=myinputtextnumber value='' onkeypress='return angka_doang(event)' /></td>";
		$tab.="<td><input type=text id=ketDet  class=myinputtext value='' onkeypress='return tanpa_kutip(event)' /></td>";
		$tab.="<td><img id='detail_add' title='Simpan' style='cursor:pointer' class=zImgBtn onclick=\"addDtDetail('".$_SESSION['empl']['lokasitugas']."')\" src='images/save.png'/></td>";
		$tab.="</tr>";
		$tab.="</tbody></table></fieldset>";
		$tab.="<fieldset><legend>".$_SESSION['lang']['detail']." ".$_POST['tgl']."</legend>";
		$tab.="<div id=detailData style='overflow:auto; width:650px; height:220px;'>";
		
		$tab.="</div>";
		$tab.="</fieldset>";
		echo $tab;
	  break;
	  case'detailData':
		$tab.="<table cellspacing=1 cellpadding=1 border=0>";
		$tab.="<thead><tr>";
		$tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
		$tab.="<td>".$_SESSION['lang']['kodetangki']."</td>";
		$tab.="<td>".$_SESSION['lang']['jumlah']."</td>";
		$tab.="<td>".$_SESSION['lang']['keterangan']."</td>";
		$tab.="<td>".$_SESSION['lang']['action']."</td>";
		$tab.="</tr></thead><tbody>";
		$str="select * from ".$dbname.".pabrik_produksidetail
		  where kodeorg='".$_POST['kdorg']."' and tanggal='".$_POST['tgl']."'";
		//echo $str;
		$res=mysql_query($str) or die(mysql_error($conn));
		while($rdata=mysql_fetch_assoc($res)){
			$tab.="<tr class=rowcontent>";
			$tab.="<td>".$rdata['tanggal']."</td>";
			$tab.="<td>".$rdata['kodetangki']."</td>";
			$tab.="<td>".$rdata['jumlah']."</td>";
			$tab.="<td>".$rdata['keterangan']."</td>";
			$tab.="<td><img id='detail_add' title='Simpan'  class=resicon onclick=\"deleteDet('".$rdata['tanggal']."','".$rdata['kodeorg']."','".$rdata['kodetangki']."')\" src='images/application/application_delete.png'/></td>";
			$tab.="</tr>";
		}
		$tab.="</tbody></table>";
		echo $tab;
	  break;
	  case'addDetailDt':
		$str="select * from ".$dbname.".pabrik_produksidetail
		  where kodeorg='".$_SESSION['empl']['lokasitugas']."' and 
		  kodetangki='".$_POST['kdTangki']."' and tanggal='".$_POST['tgl']."'";
		$qcek=mysql_query($str) or die(mysql_error($conn));
		$rcek=mysql_num_rows($qcek);
		if($rcek==1){
			$sdel="delete from ".$dbname.".pabrik_produksidetail where  kodeorg='".$_SESSION['empl']['lokasitugas']."' and 
		           kodetangki='".$_POST['kdTangki']."' and tanggal='".$_POST['tgl']."'";
		    if(mysql_query($sdel)){
				$sInser="insert into ".$dbname.".pabrik_produksidetail (kodeorg,tanggal,kodetangki,jumlah,keterangan) values ('".$_SESSION['empl']['lokasitugas']."','".$_POST['tgl']."','".$_POST['kdTangki']."','".$_POST['jmlhDet']."','".$_POST['ketDet']."')";
			}else{
				exit("gagal:".mysql_error($conn)."____".$sdel);
			} 
		}else{
			$sInser="insert into ".$dbname.".pabrik_produksidetail (kodeorg,tanggal,kodetangki,jumlah,keterangan) values ('".$_SESSION['empl']['lokasitugas']."','".$_POST['tgl']."','".$_POST['kdTangki']."','".$_POST['jmlhDet']."','".$_POST['ketDet']."')";
		}
		if(!mysql_query($sInser)){
			exit("gagal:".mysql_error($conn)."____".$sInser);
		}else{
			$ProdHarianCpo=0;
			$ProdHarianKer=0;
			$sSum="select sum(jumlah) as jumlah,kodetangki from ".$dbname.".pabrik_produksidetail where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tgl']."' group by kodetangki";
			$qSum=mysql_query($sSum) or die(mysql_error($conn));
			while($rSum=mysql_fetch_assoc($qSum)){
				$whrKomoditi="kodetangki='".$rSum['kodetangki']."'";
				$optKomoditi=makeOption($dbname,'pabrik_5tangki','kodetangki,komoditi',$whrKomoditi);
				if(strtoupper($optKomoditi[$rSum['kodetangki']])=='CPO'){
						$ProdHarianCpo+=$rSum['jumlah'];
				}else{
						$ProdHarianKer+=$rSum['jumlah'];
				}
			}
			$ar1=0;
			$ar2=0;
			if($ProdHarianCpo!=0){
				$ar1=1;
				$whrg="oer='".$ProdHarianCpo."'";
			}
			if($ProdHarianKer!=0){
				$ar2=1;
				$whrg="oerpk='".$ProdHarianKer."'";
			}
			if(($ar1+$ar2)==2){
				$whrg="oer='".$ProdHarianCpo."',oerpk='".$ProdHarianKer."'";
			}
			$sup="update ".$dbname.".pabrik_produksi set ".$whrg." where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tgl']."'";
			if(!mysql_query($sup)){
				exit("warning:".mysql_error($conn)."____".$sup);
			}
		}
	  break;
	  case'deleteDet':
		$sdel="delete from ".$dbname.".pabrik_produksidetail where  kodeorg='".$_POST['kodeorg']."' and 
			   kodetangki='".$_POST['kdTangki']."' and tanggal='".$_POST['tgl']."'";
		if(!mysql_query($sdel)){
			 exit("gagal:".mysql_error($conn)."____".$sdel);
		}else{
			$ProdHarianCpo=0;
			$ProdHarianKer=0;
			$sSum="select sum(jumlah) as jumlah,kodetangki from ".$dbname.".pabrik_produksidetail where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tgl']."' group by kodetangki";
			$qSum=mysql_query($sSum) or die(mysql_error($conn));
			while($rSum=mysql_fetch_assoc($qSum)){
				$whrKomoditi="kodetangki='".$rSum['kodetangki']."'";
				$optKomoditi=makeOption($dbname,'pabrik_5tangki','kodetangki,komoditi',$whrKomoditi);
				if(strtoupper($optKomoditi[$rSum['kodetangki']])=='CPO'){
						$ProdHarianCpo+=$rSum['jumlah'];
				}else{
						$ProdHarianKer+=$rSum['jumlah'];
				}
			}
			$sCek="select oer,oerpk from ".$dbname.".pabrik_produksi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tgl']."'";
			$qCek=mysql_query($sCek) or die(mysql_error($conn));
			$rCek=mysql_fetch_assoc($qCek);
			if(substr($_POST['tgl'],0,4)=='2014'){//tahun di 2014 di lewatkan, form ini di mulai dthn 2015 awal
				break;
			}
			$ar1=0;
			$ar2=0;
			if($ProdHarianCpo!=0){
				$ar1=1;
				$whrg="oer='".$ProdHarianCpo."'";
			}
			if($ProdHarianKer!=0){
				$ar2=1;
				$whrg="oerpk='".$ProdHarianKer."'";
			}
			if(($ar1+$ar2)==2){
				$whrg="oer='".$ProdHarianCpo."',oerpk='".$ProdHarianKer."'";
			}
			$sup="update ".$dbname.".pabrik_produksi set ".$whrg." where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tgl']."'";
			if(!mysql_query($sup)){
				exit("warning:".mysql_error($conn)."____".$sup);
			}
		} 
	  break;
}


?>