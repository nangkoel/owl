<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');


        $pt=$_POST['pt'];
        
        if($_POST['method']=='getUnit'){
            $optUnit="<option value=''>".$_SESSION['lang']['all']."</option>";
            $sUnit="select distinct unit from ".$dbname.".aging_sch_vw where kodeorg='".$pt."' and unit is not null";
            $qUnit=  mysql_query($sUnit) or die(mysql_error($conn));
            while($rUnit=  mysql_fetch_assoc($qUnit)){
                $whpt="kodeorganisasi='".$rUnit['unit']."'";
                $optNmOrg=  makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whpt);
                $optUnit.="<option value='".$rUnit['unit']."'>".$rUnit['unit']."-".$optNmOrg[$rUnit['unit']]."</option>";
            }
            echo $optUnit;
        }else{
            if($pt==''){
                $pt=$_SESSION['org']['kodeorganisasi'];
            }
        $gudang=$_POST['gudang'];
        $tanggal=$_POST['tanggal'];
        $tanggal=tanggaldgnbar($tanggal);
        $tanggalpivot=$_POST['tanggalpivot'];
        $tanggaljttempo=tanggaldgnbar($tanggalpivot);
	if($tanggalpivot==''){
		exit("error: Date can't empty");
	}
       // $whr=" and substr(novp,2,4) = '".$_SESSION['empl']['lokasitugas']."' ";
if($gudang!=''){
                /*$str="select distinct(namasupplier) as namasupplier from ".$dbname.".aging_sch_vw
                where tanggal > '2012-12-31' and kodeorg = '".$gudang."' and (nilaiinvoice > dibayar or dibayar is NULL) order by namasupplier
                ";*/
		$whr.=" and unit = '".$gudang."' ";
}elseif($pt!='')
{
                /*$str="select distinct(namasupplier) as namasupplier from ".$dbname.".aging_sch_vw
                where tanggal > '2012-12-31' and kodeorg = '".$pt."' and (nilaiinvoice > dibayar or dibayar is NULL) order by namasupplier
                ";*/
     //           $whr="";
		$whr.=" and kodeorg = '".$pt."'";
}
//echo $str;
//$str="select * from ".$dbname.".aging_sch_vw
//      where tanggal > '".$tanggal."' and tanggalvp<='".$tanggaljttempo."' and (nilaiinvoice+nilaippn > dibayar or dibayar is NULL) and novp IS NOT NULL
//      ".$whr." order by namasupplier asc";
$str="select * from ".$dbname.".aging_sch_vw
      where tanggal > '".$tanggal."' and tanggalvp<='".$tanggaljttempo."' and novp IS NOT NULL
      ".$whr." order by namasupplier asc";
 //echo $str;
//=================================================
        $res=mysql_query($str);
        $no=0;
        $idx=0;
        if(@mysql_num_rows($res)<1)
        {
                echo"<tr class=rowcontent><td colspan=13>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
        }
        else
        {
            $grantotal0=$grantotal30=$grandtotal60=$grantotal90=$grantotal100=$grantotaldibayar=0;
            $totalinvoice=$totalinvoice2=0;
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
                        $lstInv[$bar['noinvoice'].$bar['novp']]=$bar['noinvoice']; 
                        $dtInv[$bar['kodesupplier'].$not]=$bar['noinvoice'];
                        $dtNoVP[$bar['kodesupplier'].$not]=$bar['novp'];
                        $dtTgl[$bar['noinvoice'].$bar['novp']]=$bar['tanggal'];
                        $dtJthTmp[$bar['noinvoice'].$bar['novp']]=$bar['jatuhtempo'];
                        $dtNopo[$bar['noinvoice'].$bar['novp']]=$bar['nopo'];
                        $dtKurs[$bar['noinvoice'].$bar['novp']]=$bar['kurs'];
                        $dtMtUang[$bar['noinvoice'].$bar['novp']]=$bar['matauang'];
                        $dtNilaipo[$bar['noinvoice'].$bar['novp']]=$bar['nilaipo'];
                        $dtNilaikontrak[$bar['noinvoice'].$bar['novp']]=$bar['nilaikontrak'];
                        $dtNilaiinvoice[$bar['noinvoice'].$bar['novp']]=($bar['nilaiinvoice']+$bar['nilaippn']);
                            $str="select sum(abs(jumlah)) as jumlah from ".$dbname.".keu_vpdt where novp='".$bar['novp']."' and noakun in (2111101,2111102,2111104,2111201,2111202)";
                            $resvp=mysql_query($str);
                            while($barvp=mysql_fetch_assoc($resvp)){
                                $dtNilaiinvoice[$bar['noinvoice'].$bar['novp']]=($barvp['jumlah']);
                            }
                        $dtDibayar[$bar['noinvoice'].$bar['novp']]=$dbayar;
                    }
                }

                foreach($lstSupp as $dtSupplier){
                               for($awal=1;$awal<=$rowd[$dtSupplier];$awal++){
                                $idx+=1;
                                if($awal==1){
						//0-30, 31-60 61-90 diatas 90
						
						$rowddata=$rowd[$dtSupplier];
						$no=1;
						$subTotInvoice=0;
						$subTotInvoiceIdr=0;
						$total0=0;
						$total30=0;
						$total60=0;
						$total90=0;
						$total100=0;
                                                $subtotaldibayar=0;
						$row=0;
					}
                               
                                //exit("error:".$rowddata);
                            $namasupplier	=$lstNmSupp[$dtSupplier];
                            //if($namasupplier=='')$namasupplier='&nbsp;';
                            $noinvoice	=$dtInv[$dtSupplier.$awal];
                            $novp	=$dtNoVP[$dtSupplier.$awal];
                            $tanggal	=$dtTgl[$noinvoice.$novp]; 
                            $jatuhtempo =$dtJthTmp[$noinvoice.$novp];
                            $nopokontrak    =$dtNopo[$noinvoice.$novp];
                            $nilaipo        =$dtNilaipo[$noinvoice.$novp];
                            $nilaikontrak   =$dtNilaikontrak[$noinvoice.$novp];
                            $nilaiinvoice   =$dtNilaiinvoice[$noinvoice.$novp];
                            
                            //$totalinvoice+=$nilaiinvoice;
                            $dibayar 	=$dtDibayar[$noinvoice.$novp];


                            $nilaiinvoiceIdr=$nilaiinvoice;
                            if($dtMtUang[$noinvoice.$novp]!='IDR'){
                                  $nilaiinvoiceIdr=$nilaiinvoice*$dtKurs[$noinvoice.$novp];
                            }

                            $sisainvoice=$nilaiinvoice-$dibayar;
                            if($dtMtUang[$noinvoice.$novp]!='IDR'){
                                $sisainvoice=$sisainvoice*$dtKurs[$noinvoice.$novp];
                                $dibayar=$dibayar*$dtKurs[$noinvoice.$novp];
                            }
                            $nilaipokontrak =$nilaipo;
                            if($nilaikontrak>0)$nilaipokontrak=$nilaikontrak;
    //			$date1=date('Y-m-d');
                            $date1=tanggaldgnbar($tanggalpivot);

                                                    if($jatuhtempo=='0000-00-00')
                                                    {
                                                            $jatuhtempo=$date1;
                                                    }

                            $diff =(strtotime($jatuhtempo)-strtotime($date1));
                            $outstd =floor(($diff)/(60*60*24));
                            //if($outstd<1)$outstd=0;
                            $flag0=$flag30=$flag60=$flag90=$flag100=0;
                                                    if($outstd!=0)$outstd*=-1;
                            if($outstd<=0)$flag0=1; 
                            if(($outstd>=1)and($outstd<=30))$flag30=1;
                            if(($outstd>=31)and($outstd<=60))$flag60=1;
                            if(($outstd>=61)and($outstd<=90))$flag90=1;
                            if($outstd>90)$flag100=1;

                            if($flag0==1){$total0+=$sisainvoice;}
                            if($flag30==1){$total30+=$sisainvoice;}
                            if($flag60==1){$total60+=$sisainvoice;}
                            if($flag90==1){$total90+=$sisainvoice;}
                            if($flag100==1){$total100+=$sisainvoice;}
                            if($jatuhtempo=='0000-00-00'){ $outstd=''; $jatuhtempo=''; }else{ $jatuhtempo=tanggalnormal($jatuhtempo); }

                    echo"<tr class=rowcontent>
                                      <td align=center width=20>".$no."</td>
                                      <td nowrap>".tanggalnormal($tanggal)."</td> 
                                      <td nowrap>".$noinvoice."</td>
                                      <td nowrap>".$namasupplier."</td>
                                      <td id=novp_".$idx." nowrap style='cursor:pointer;' title='Click untuk melihat VP' onclick='printVp(".$idx.",event)'>".$novp."</td>";
                    $rowdty="";
                    echo"<td  ".$rowdty." nowrap>".$dtMtUang[$noinvoice.$novp]."</td>";
                                      echo"<td nowrap  align=right>".$dtKurs[$noinvoice.$novp]."</td>
                                     ";
                                                                     // echo $jatuhtempo.___.$date1;
//                                                                      if($jatuhtempo==tanggalnormal($date1)) {
//                                                                       echo"<td align=center></td>";
//                                                                      } else {
//                                                                            echo"<td align=center nowrap>".$jatuhtempo."</td>";
//                                                                      }
                                                                      echo"<td align=center nowrap>".$jatuhtempo."</td>";

                                      if($dtMtUang[$noinvoice.$novp]!='IDR' && $sisainvoice<15000){
                                            $sisainvoice=$sisainvoice*$dtKurs[$noinvoice.$novp];
                                      }
                                      echo"<td align=center>".$nopokontrak."</td>
                                      <td align=right>".number_format($nilaipokontrak,2)."</td>
                                      <td align=right>".number_format($nilaiinvoice,2)."</td>
                                      <td align=right>".number_format($nilaiinvoiceIdr,2)."</td>
                                      <td align=right>";
                                      if($flag0==1)echo number_format($sisainvoice,2); else echo number_format(0,2); echo"</td>
                                      <td align=right>";
                                      if($flag30==1)echo number_format($sisainvoice,2); else echo number_format(0,2); echo"</td>
                                      <td align=right>";
                                      if($flag60==1)echo number_format($sisainvoice,2); else echo number_format(0,2); echo"</td>
                                      <td align=right>";
                                      if($flag90==1)echo number_format($sisainvoice,2); else echo number_format(0,2); echo"</td>
                                      <td align=right>";
                                      if($flag100==1)echo number_format($sisainvoice,2); else echo number_format(0,2); echo"</td>
                                      <td align=right width=100>".number_format($dibayar,2)."</td>
                                      <td align=right>".$outstd."</td></tr>";
                                      
//                            echo"<tr class=rowcontent>
//                                      <td nowrap>".$namasupplier."</td>";
//                                      if($dtMtUang[$noinvoice]!='IDR'){
//                                            $totInvAll[$dtSupplier]+=$nilaiinvoice*$dtKurs[$noinvoice];
//                                            $totInv=$nilaiinvoice*$dtKurs[$noinvoice];
//                                             echo"<td nowrap>IDR</td>
//                                            <td align=right>".number_format($totInv,2)."</td>";
//                                      }
//                            echo"</tr>";	
                                                    $subTotInvoice+=$nilaiinvoice;
                                                    $subTotInvoiceIdr+=$nilaiinvoiceIdr;
                                                    $subtotaldibayar+=$dibayar;

                                            //	echo $total0;
                                    $no+=1;
                                    $row+=1;
                                    if($row==$rowddata){
                                     //      
                                    echo"<thead>";
                                       echo"<tr>
                                            <td colspan=10 align=center width=20><b>".$_SESSION['lang']['subtotal']." ".$namasupplier."</b></td>
                                            <td align=right><b>";
                                            echo number_format($subTotInvoice,2); echo"</td>
                                            <td align=right><b>";
                                            echo number_format($subTotInvoiceIdr,2); echo"</td>
                                            <td align=right><b>";
                                            echo number_format($total0,2); echo"</td>
                                            <td align=right><b>";
                                            echo number_format($total30,2); echo"</td>
                                            <td align=right><b>";
                                            echo number_format($total60,2); echo"</td>
                                            <td align=right><b>";
                                            echo number_format($total90,2); echo"</td>
                                            <td align=right><b>";
                                            echo number_format($total100,2); echo"</td>
                                            <td align=right width=100><b>".number_format($subtotaldibayar,2)."</td>
                                            <td align=right>&nbsp;</td>
                                            </tr>";
//                                            if(!empty($totInvAll[$dtSupplier])){
//                                                if($total0!=0){
//                                                    $total0=$totInvAll[$dtSupplier];
//                                                }
//                                                if($total30!=0){
//                                                    $total30=$totInvAll[$dtSupplier];
//                                                }
//                                                if($total60!=0){
//                                                    $total60=$totInvAll[$dtSupplier];
//                                                }
//                                                if($total90!=0){
//                                                    $total90=$totInvAll[$dtSupplier];
//                                                }
//                                                if($total100!=0){
//                                                    $total100=$totInvAll[$dtSupplier];
//                                                }
//                                                echo"<tr>
//                                                <td colspan=10 align=center width=20>&nbsp;</td>
//                                                <td align=right><b>";
//                                                echo number_format($totInvAll[$dtSupplier],2); echo"</td>
//                                                <td align=right><b>";
//                                                echo number_format($total0,2); echo"</td>
//                                                <td align=right><b>";
//                                                echo number_format($total30,2); echo"</td>
//                                                <td align=right><b>";
//                                                echo number_format($total60,2); echo"</td>
//                                                <td align=right><b>";
//                                                echo number_format($total90,2); echo"</td>
//                                                <td align=right><b>";
//                                                echo number_format($total100,2); echo"</td>
//                                                <td align=right width=100><b>".number_format($subtotaldibayar,2)."</td>
//                                                <td align=right>&nbsp;</td>
//                                                </tr>";
//                                            }
                                    echo"</thead>";
                                    $totalinvoice+=$subTotInvoice;
                                    $totalinvoice2+=$subTotInvoiceIdr;
                                    $grantotaldibayar+=$subtotaldibayar;
                                    $grantotal0+=$total0;
                                    $grantotal30+=$total30;
                                    $grantotal60+=$total60;
                                    $grantotal90+=$total90;
                                    $grantotal100+=$total100;
                                    //break;
                                    }


                            }	//array invoice
						
			}	//array supplier
                echo"<tr class=rowtitle bgcolor=#0066FF>
                                  <td colspan=10 align=center width=20><b>".$_SESSION['lang']['grnd_total']."</b></td>
                                  <td align=right><b>";
                                  echo number_format($totalinvoice,2); echo"</td>
                                  <td align=right><b>";
                                  echo number_format($totalinvoice2,2); echo"</td>
                                  <td align=right><b>";
                                  echo number_format($grantotal0,2); echo"</td>
                                  <td align=right><b>";
                                  echo number_format($grantotal30,2); echo"</td>
                                  <td align=right><b>";
                                  echo number_format($grantotal60,2); echo"</td>
                                  <td align=right><b>";
                                  echo number_format($grantotal90,2); echo"</td>
                                  <td align=right><b>";
                                  echo number_format($grantotal100,2); echo"</td>
                                  <td align=right width=100><b>".number_format($grantotaldibayar,2)."</td>
                                  <td align=right>&nbsp;</td>
                        </tr>";
        }
    }
?>