<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}
//bikin array tanggal 
function dates_inbetween($date1, $date2){

    $day = 60*60*24;

    $date1 = strtotime($date1);
    $date2 = strtotime($date2);

    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between

    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);
   
    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }

    $dates_array[] = date('Y-m-d',$date2);
    if($date1==$date2){
        $dates_array = array();
        $dates_array[] = date('Y-m-d',$date1);        
    }
    return $dates_array;
}
//$arr="##periode##tipeIntex##unit";
$periode=$_POST['periode'];
$tipeIntex=$_POST['tipeIntex'];
$unit=$_POST['unit'];
$kodeOrg=$_POST['kodeOrg'];
$brsKe=$_POST['brsKe'];
$tgl_1=tanggalsystem($_POST['tgl_1']);
$tgl_2=tanggalsystem($_POST['tgl_2']);
$kdBlok=$_POST['kdBlok'];
$nospb=$_POST['nospb'];
$kdPabrik=$_POST['kdPabrik'];
$pilTamp=$_POST['pilTamp'];
$optSupp=makeOption($dbname, 'log_5supplier', 'kodetimbangan,namasupplier');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$dateDt=dates_inbetween($tgl_1,$tgl_2);
$intex=array('2'=>'Internal','1'=>'Afiliasi','0'=>'External');
if($tipeIntex=='3'&&$unit==''){
    function daysBetween($s, $e)
    {
            $s = strtotime($s);
            $e = strtotime($e);

            return ($e - $s)/ (24 *3600);
    }
    $erd=explode("-",$_POST['tgl_1']);
    $erd2=explode("-",$_POST['tgl_2']);
    $tgl1=$erd[2]."-".$erd[1]."-".$erd[0];
    $tgl2=$erd2[2]."-".$erd2[1]."-".$erd2[0];
    $archeck=daysBetween($tgl1,$tgl2);
    if($archeck>5)
    {
        exit("error: max 5 days");
    }
}
 
switch($proses)
{
	case'preview':
	
	if($unit!="")
	{
               if($tipeIntex==0)
               {
                    $where= "and kodecustomer='".$unit."'";
               }
               else
               {
                    $where= "and substr(nospb,9,6) like '%".$unit."%'";
               }
	}
	if($kdPabrik!='')
	{
		$where.=" and millcode='".$kdPabrik."'";
	}        
	if(($tgl_1!='')&&($tgl_2!=''))
	{
		
                 $where.=" and tanggal >= ".$tgl_1."000001 and tanggal<=".$tgl_2."235959";
	}
	else
	{
		echo"warning: Date required";
		exit();
	}
	
         if($tipeIntex==3)
         {
              $gr=" group by kodeorg,kodecustomer,left(tanggal,10),intex";
         }
         elseif($tipeIntex==0)
          {
               $gr=" group by kodecustomer,left(tanggal,10),intex";
               $whr.="and intex='".$tipeIntex."'";
          }
		  else
		  {
			  //exit("Error:MASUK");
			   $gr=" group by kodeorg,left(tanggal,10)"; 
			   $whr.="and intex='".$tipeIntex."'";
		  }
		
 //$whr="and intex='2'";
        echo $_SESSION['lang']['rPenerimaanTbs'].", ".$_SESSION['lang']['periode']." :".$_POST['tgl_1']." s.d. ".$_POST['tgl_2'];
         //notransaksi, tanggal, kodeorg, kodecustomer, bjr, jumlahtandan1, kodebarang, jammasuk, beratmasuk, jamkeluar, beratkeluar, nokendaraan, supir, nospb, petugassortasi, timbangonoff, statussortasi, nokontrak, nodo, intex, nosipb, thntm1, thntm2, thntm3, jumlahtandan2, jumlahtandan3, brondolan, username, millcode, beratbersih
        $sData="select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih) as netto,kodecustomer,substr(tanggal,1,10) as tanggal,
                supir,nokendaraan,nospb,thntm1,intex,kgpotsortasi
                from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." ".$whr."  order by substr(tanggal,1,10) asc";
       //  echo $sData;
        //echo "warning".$sData;exit();
         $qData=mysql_query($sData) or die(mysql_error());
         $brs=mysql_num_rows($qData);
            if($brs>0)
            {
				//exit("Error:MASUK");

                if($pilTamp!=1)
                {
					
                        echo"<table cellspacing=1 border=0 class=sortable>
                        <thead class=rowheader>
                        <tr>
                                <td>No.</td>
                                <td>".$_SESSION['lang']['tanggal']."</td>
                                <td>".$_SESSION['lang']['namasupplier']."/".$_SESSION['lang']['unit']."</td>
                                <td>".$_SESSION['lang']['noTiket']."</td>
                                <td>".$_SESSION['lang']['kodenopol']."</td>
                                <td>".$_SESSION['lang']['beratBersih']."</td>
                                <td>".$_SESSION['lang']['potongankg']."</td>
                                <td>".$_SESSION['lang']['beratnormal']."</td>
                                <td>".$_SESSION['lang']['sopir']."</td>
                                <td>".$_SESSION['lang']['nospb']."</td>
                                <td>".$_SESSION['lang']['jmlhTandan']."</td>
                                <td>".$_SESSION['lang']['tahuntanam']."</td>
                        </tr>
                        </thead>
                        <tbody>";


                                $dtIntex="";
                                while($rData=mysql_fetch_assoc($qData))
                                {	
                                        $no+=1;
                                        if($dtIntex!=$rData['intex'])
                                        {
                                            $dtIntex=$rData['intex'];
                                            $sData2="select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih) as netto,kodecustomer,substr(tanggal,1,10) as tanggal,
                                            supir,nokendaraan,nospb,thntm1,intex
                                            from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." ".$whr." and intex='".$rData['intex']."' ".$gr."  order by intex desc";
                                            //echo $sData2;
											//exit("error:".$sData2);
                                            $qData2=mysql_query($sData2) or die(mysql_error($conn));
                                            $rowData=mysql_num_rows($qData2);
                                            $rd=0;
                                        }
                                         if($rData['intex']!=0)
                                         {
                                              $nm=$optNm[$rData['kodeorg']];
                                         }
                                         else
                                         {
                                              $nm=$optSupp[$rData['kodecustomer']];
                                         }
                                         $brtNormal=$rData['netto']-$rData['kgpotsortasi'];
                                         $bgwarna="";
                                         if($rData['nospb']!=''){
                                            $scek="select distinct * from ".$dbname.".kebun_spbdt where nospb='".$rData['nospb']."' and substr(nospb,9,6)<>left(blok,6)";
                                            $qcek=mysql_query($scek) or die(mysql_error($conn));
                                            $rcek=mysql_num_rows($qcek);
                                            if($rcek==1){
                                                $bgwarna="bgcolor=yellow title='ada buah dari afdeling lain'";
                                            }
                                         }
                                        echo"
                                        <tr class=rowcontent>
                                        <td>".$no."</td>
                                        <td>".tanggalnormal($rData['tanggal'])."</td>
                                        <td>".$nm."</td>
                                        <td>".$rData['notransaksi']."</td>
                                        <td>".$rData['nokendaraan']."</td>
                                        <td  align=right>".number_format($rData['netto'],0)."</td>
                                        <td  align=right>".number_format($rData['kgpotsortasi'],0)."</td>
                                        <td  align=right>".number_format($brtNormal,0)."</td>
                                        <td>".$rData['supir']."</td>
                                        <td ".$bgwarna.">".$rData['nospb']."</td>
                                        <td align=right>".number_format($rData['jjg'],0)."</td>
                                        <td>".$rData['thntm1']."</td>
                                        </tr>";
                                        $subtota+=$rData['netto'];
                                        $subTnandn+=$rData['jjg'];
                                        $sbTotaJjg+=$rData['jjg'];
                                        $subTotNett+=$rData['netto'];
                                         $subBrtNor+=$brtNormal;
                                        $subBrtPot+=$rData['kgpotsortasi'];
                                         $rd+=1;
                                         if($rowData==$rd)
                                         {
                                             $tab.="<tr class=rowcontent><td colspan=5>".$intex[$rData['intex']]."</td>";
                                             $tab.="<td align=right>".number_format($subTotNett,0)."</td>";
                                             $tab.="<td align=right>".number_format($subBrtPot,0)."</td>";
                                             $tab.="<td align=right>".number_format($subBrtNor,0)."</td>";
                                             $tab.="<td colspan=2>&nbsp;</td>";
                                             $tab.="<td align=right>".number_format($sbTotaJjg,0)."</td>";
                                             $tab.="<td>&nbsp;</td></tr>";
                                             $sbTotaJjg=0;
                                             $subTotNett=0;
                                        }
                                       $brtNormal=0;
                                }
                                echo"<tr class=rowcontent >
										<td colspan=5 align=right>Total (KG)</td>
										<td align=right>".number_format($subtota,0)."</td>
										<td align=right>".$subBrtPot."</td>
										<td align=right>".$subBrtNor."</td>
										<td></td>
										<td></td>
										
										<td align=right>".number_format($subTnandn)."</td>
										<td>&nbsp;</td>
									</tr>";

                       
                }
                else
                {
                    $dateDt="";
                    $dateDt=array();
                    //exit("error:".$sData);
                    $sData="select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih-kgpotsortasi) as netto,kodecustomer,substr(tanggal,1,10) as tanggal,
                    supir,nokendaraan,nospb,thntm1,intex
                    from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." ".$whr." 
                    HAVING jjg >0 AND netto >0
                        order by substr(tanggal,1,10) asc";
                   // echo $sData;
					//exit("error:".$sData);
                    $qData=mysql_query($sData) or die(mysql_error($conn));
                    while($rData=mysql_fetch_assoc($qData))
                    {
                           $dateDt[$rData['tanggal']]=$rData['tanggal'];
                            if($rData['intex']>0)
                            {
                                $dtSupp[$rData['intex'].$rData['kodeorg']]=$rData['kodeorg'];
                                $dtData[$rData['intex']][$rData['kodeorg'].$rData['tanggal']]+=$rData['netto'];
                                $dtDataJg[$rData['intex']][$rData['kodeorg'].$rData['tanggal']]+=$rData['jjg'];
                            }
                            else
                            {
                                $dtSupp[$rData['intex'].$rData['kodecustomer']]=$rData['kodecustomer'];
                                $dtData2[$rData['intex']][$rData['kodecustomer'].$rData['tanggal']]+=$rData['netto'];
                                $dtDataJg2[$rData['intex']][$rData['kodecustomer'].$rData['tanggal']]+=$rData['jjg'];
                            }
                       
                    }
                    
                    $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
                    $tab.="<tr><td rowspan=2>".$_SESSION['lang']['namasupplier']."/".$_SESSION['lang']['unit']."</td>";
                    array_multisort($dtSupp);
                    array_multisort($dateDt);
                    foreach($dateDt as $ar => $isi)
                    {
                            $qwe=date('D', strtotime($isi));
                            $tab.="<td align=center colspan=2>";
                            if($qwe=='Sun')$tab.="<font color=red>".substr($isi,8,2)."</font>"; else $tab.=(substr($isi,8,2)); 
                            $tab.="</td>";
                    }
                     $tab.="<td align=center colspan=2>".$_SESSION['lang']['total']."</td>";
                    $tab.="</tr><tr>";
                    foreach($dateDt as $ar => $isi)
                    {
                        $tab.="<td>".$_SESSION['lang']['beratBersih']." (Kg)</td>";
                        $tab.="<td>".$_SESSION['lang']['jmlhTandan']." (JJG)</td>";
                    }
                    $tab.="<td>".$_SESSION['lang']['beratBersih']." (Kg)</td>";
                    $tab.="<td>".$_SESSION['lang']['jmlhTandan']." (JJG)</td>";
                    $tab.="</tr></thead><tbody>";
                
                    foreach($intex as $lstIntex=>$isiTex){
                        foreach($dtSupp as $lsdtSup){
                             if($dtSupp[$lstIntex.$lsdtSup]!='')
                             {
                            if($lstIntex==0)
                             {
                                 $dtData=$dtData2;
                                 $dtDataJg=$dtDataJg2;
                             }
                             
                             if($lstIntex!=0)
                             {
                                  $nm=$optNm[$dtSupp[$lstIntex.$lsdtSup]];
                             }
                             else
                             {
                                  $nm=$optSupp[$dtSupp[$lstIntex.$lsdtSup]];
                             }
                             
                            $tab.="<tr class=rowcontent><td>".$nm."</td>";
                            foreach($dateDt as $ar => $isi)
                            {
                                $tab.="<td align=right>".number_format($dtData[$lstIntex][$lsdtSup.$isi],0)."</td>";
                                $tab.="<td align=right>".number_format($dtDataJg[$lstIntex][$lsdtSup.$isi],0)."</td>";
                                $totKg[$isi]+=$dtData[$lstIntex][$lsdtSup.$isi];
                                $totJjg[$isi]+=$dtDataJg[$lstIntex][$lsdtSup.$isi];
                                $totsmpngkg[$lstIntex.$lsdtSup]+=$dtData[$lstIntex][$lsdtSup.$isi];
                                $totsmpngjjg[$lstIntex.$lsdtSup]+=$dtDataJg[$lstIntex][$lsdtSup.$isi];
                                $totInKg[$lstIntex.$isi]+=$dtData[$lstIntex][$lsdtSup.$isi];
                                $totInJjg[$lstIntex.$isi]+=$dtDataJg[$lstIntex][$lsdtSup.$isi];
                            }
                            $tab.="<td align=right>".number_format($totsmpngkg[$lstIntex.$lsdtSup],0)."</td>";
                            $tab.="<td align=right>".number_format($totsmpngjjg[$lstIntex.$lsdtSup],0)."</td>";
                            $tab.="</tr>";
                            $totkgsmpng[$lstIntex]+=$totsmpngkg[$lstIntex.$lsdtSup];
                            $totjjgsmpng[$lstIntex]+=$totsmpngjjg[$lstIntex.$lsdtSup];
                            }
                        }
                        if($drt!=$lstIntex)
                        {
                            $drt=$lstIntex;
                            $tab.="<tr bgcolor=darkblue><td><font color=white>".$intex[$lstIntex]."</font></td>";
                            foreach($dateDt as $ar => $isi)
                            {
                                $tab.="<td align=right bgcolor=MediumBlue><font color=white>".number_format($totInKg[$lstIntex.$isi],0)."</font></td>";
                                $tab.="<td align=right bgcolor=darkblue><font color=white>".number_format($totInJjg[$lstIntex.$isi],0)."</font></td>";
                            }
                            $tab.="<td align=right bgcolor=MediumBlue><font color=white>".number_format($totkgsmpng[$lstIntex],0)."</font></td>";
                            $tab.="<td align=right><font color=white>".number_format($totjjgsmpng[$lstIntex],0)."</font></td>";
                            $tab.="</tr>";
                        }  
                        $totSmaKg+=$totkgsmpng[$lstIntex];
                        $totSmaJjg+=$totjjgsmpng[$lstIntex];
                    }
                    $tab.="<tr bgcolor=DarkGreen><td><font color=white>".$_SESSION['lang']['total']."</font></td>";
                    foreach($dateDt as $ar => $isi)
                    {
                        $tab.="<td align=right bgcolor=Green><font color=white>".number_format($totKg[$isi],0)."</font></td>";
                        $tab.="<td align=right><font color=white>".number_format($totJjg[$isi],0)."</font></td>";
                    }
                    $tab.="<td align=right bgcolor=Green><font color=white>".number_format($totSmaKg,0)."</font></td>";
                    $tab.="<td align=right><font color=white>".number_format($totSmaJjg,0)."</font></td>";
                    $tab.="</tr></tbody></table>";
                    echo $tab;

                }
            }
            else
            {
                echo"<tr class=rowcontent><td colspan=10 align=center>Data empty</td></tr>";
            }
	break;
	case'pdf':
	$periode=$_GET['periode'];
	$tipeIntex=$_GET['tipeIntex'];
	$unit=$_GET['unit'];
	$tglPeriode=explode("-",$periode);
	$tanggal=$tglPeriode[1]."-".$tglPeriode[0];
	$tgl_1=tanggalsystem($_GET['tgl_1']);
	$tgl_2=tanggalsystem($_GET['tgl_2']);
	$kdPabrik=$_GET['kdPabrik'];
	$pilTamp=$_GET['pilTamp'];
	
	 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $tipeIntex;
				global $periode;
				global $unit;
				global $kdPabrik;
				global $tgl_2;
				global $tgl_1;
				global $tglPeriode;
				global $tanggal;
				global $rNamaSupp;
				
				
				
				$tglPeriode=explode("-",$periode);
				$tanggal=$tglPeriode[1]."-".$tglPeriode[0];
                # Alamat & No Telp
       /*         $query = selectQuery($dbname,'organisasi','namaorganisasi,alamat,telepon',
                    "kodeorganisasi='".$kdPt."'");
                $orgData = fetchData($query);*/
				$sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
				$qAlamat=mysql_query($sAlmat) or die(mysql_error());
				$rAlamat=mysql_fetch_assoc($qAlamat);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 11;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$rAlamat['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$rAlamat['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$rAlamat['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();	
                $this->Ln();
				$this->Ln();
                $this->SetFont('Arial','B',11);
                $this->Cell($width,$height, $_SESSION['lang']['rPenerimaanTbs'],0,1,'C');	
			 	$this->SetFont('Arial','',8);
				$sNm="select namasupplier,kodetimbangan from ".$dbname.".log_5supplier order by namasupplier asc";
				$qNm=mysql_query($sNm) or die(mysql_error());
				while($rNm=mysql_fetch_assoc($qNm))
				{
					$rNamaSupp[$rNm['kodetimbangan']]=$rNm;
				}
				$sBrg="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang='400'";
				$qBrg=mysql_query($sBrg) or die(mysql_error($conn));
				while($rBrg=mysql_fetch_assoc($qBrg))
				{
					$rNmBrg[$rBrg['kodebarang']]=$rBrg;
				}
				if(($kdPabrik!='')&&($unit!=''))
				{
				$this->Cell($width,$height, $_SESSION['lang']['terimaTbs']." : ".$kdPabrik." atas ".$rNmBrg[40000003]['namabarang']." ".$_SESSION['lang']['dari']." ".$rNamaSupp[$unit]['namasupplier']." ".$_SESSION['lang']['periode']." :".$tgl_1."-".$tgl_2,0,1,'C');	
				}
				else
				{
					$this->Cell($width,$height, $_SESSION['lang']['terimaTbs']." : ".$kdPabrik." atas ".$rNmBrg[40000003]['namabarang']." ".$_SESSION['lang']['dari']." : ".$_SESSION['lang']['all'].", ".$_SESSION['lang']['periode']." :".tanggalnormal($tgl_1)." - ".tanggalnormal($tgl_2),0,1,'C');						
				}
				$this->Ln();$this->Ln();
                $this->SetFont('Arial','B',5);	
                $this->SetFillColor(220,220,220);
				
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);		
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['namasupplier'],1,0,'C',1);		
				$this->Cell(7/100*$width,$height,$_SESSION['lang']['noTiket'],1,0,'C',1);	
				$this->Cell(9/100*$width,$height,$_SESSION['lang']['kodenopol'],1,0,'C',1);	
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['beratBersih'],1,0,'C',1);	
                                $this->Cell(8/100*$width,$height,$_SESSION['lang']['potongankg'],1,0,'C',1);	
                                $this->Cell(8/100*$width,$height,$_SESSION['lang']['beratnormal'],1,0,'C',1);	
				$this->Cell(7/100*$width,$height,$_SESSION['lang']['sopir'],1,0,'C',1);			
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['nospb'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['jmlhTandan'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['tahuntanam'],1,1,'C',1);	            
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('P','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 9;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',5);
                if($pilTamp==1)
                {
                    exit("Error: Not privided for PDF");
                }
	if($tipeIntex!='')
	{
		$where.=" and intex='".$tipeIntex."'";
	}
	else
	{
		echo"warning: Choose FFB source";
		exit();
	}
	if($unit!="")
	{
		if($tipeIntex==0)
		{
			$where.=" and kodecustomer='".$unit."'";
		}
		elseif($tipeIntex!=0)
		{
			$where.=" and kodeorg='".$unit."' ";
		}
	}
	if(($tgl_1!='')&&($tgl_2!=''))
	{
		$where.=" and tanggal >= ".$tgl_1."000001 and tanggal<=".$tgl_2."235959";
	}
	else
	{
		echo"warning: Date required";
		exit();
	}
	
	if($kdPabrik!='')
	{
		$where.=" and millcode='".$kdPabrik."'";
		
	}		
		$sList="select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih) as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nospb,thntm1,kgpotsortasi 
                        from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where;
		$qList=mysql_query($sList) or die(mysql_error());
		while($rData=mysql_fetch_assoc($qList))
		{			
			if($tipeIntex!=0)
			{
				$sNm="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
				$qNm=mysql_query($sNm) or die(mysql_error());
				$rNm=mysql_fetch_assoc($qNm);
				$nm=$rNm['namaorganisasi'];
				$kd=$rData['kodeorg'];
			}
			else
			{
				/*$sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
				$qNm=mysql_query($sNm) or die(mysql_error());
				$rNm=mysql_fetch_assoc($qNm);*/
				$nm=$rNamaSupp[$rData['kodecustomer']]['namasupplier'];	
					
			}
			$no+=1;
                        $pdf->SetFont('Arial','',6);
                        $brtNormal=$rData['netto']-$rData['kgpotsortasi'];
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
			$pdf->Cell(8/100*$width,$height,tanggalnormal($rData['tanggal']),1,0,'C',1);		
			$pdf->Cell(15/100*$width,$height,$nm,1,0,'L',1);	
                       
			$pdf->Cell(7/100*$width,$height,$rData['notransaksi'],1,0,'L',1);	
			$pdf->Cell(9/100*$width,$height,$rData['nokendaraan'],1,0,'L',1);
                       
			$pdf->Cell(8/100*$width,$height,number_format($rData['netto']),1,0,'R',1);	
                        $pdf->Cell(8/100*$width,$height,number_format($rData['kgpotsortasi']),1,0,'R',1);
                        $pdf->Cell(8/100*$width,$height,number_format($brtNormal),1,0,'R',1);
                        $pdf->SetFont('Arial','',5);
			$pdf->Cell(7/100*$width,$height,$rData['supir'],1,0,'L',1);	
                        $pdf->SetFont('Arial','',6);
			$pdf->Cell(15/100*$width,$height,$rData['nospb'],1,0,'L',1);
                        
			$pdf->Cell(8/100*$width,$height,number_format($rData['jjg'],2),1,0,'R',1);
			$pdf->Cell(8/100*$width,$height,$rData['thntm1'],1,1,'C',1);
			/*$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
			$pdf->Cell(18/100*$width,$height,$nm,1,0,'C',1);		
			$pdf->Cell(12/100*$width,$height,tanggalnormal($rData['tanggal']),1,0,'C',1);		
			$pdf->Cell(10/100*$width,$height,number_format($rData['jjg']),1,0,'R',1);			
			$pdf->Cell(15/100*$width,$height,number_format($rData['netto'],2),1,1,'R',1);*/
			$subtota+=$rData['netto'];
			$subjjg+=$rData['jjg'];
                        $subbrtpot+=$rData['kgpotsortasi'];
                        $subbrtnor+=$brtNormal;
		}
		$pdf->Cell(42/100*$width,$height,"Total",1,0,'R',1);
                $pdf->SetFont('Arial','',6);
		$pdf->Cell(8/100*$width,$height,number_format($subtota),1,0,'R',1);
                $pdf->Cell(8/100*$width,$height,number_format($subbrtpot),1,0,'R',1);
                $pdf->Cell(8/100*$width,$height,number_format($subbrtnor),1,0,'R',1);
		$pdf->Cell(22/100*$width,$height,"",1,0,'C',1);
		$pdf->Cell(8/100*$width,$height,number_format($subjjg),1,0,'R',1);
		$pdf->Cell(8/100*$width,$height,'',1,1,'R',1);
			
    $pdf->Output();
	break;
	case'excel':
	$periode=$_GET['periode'];
	$tipeIntex=$_GET['tipeIntex'];
	$unit=$_GET['unit'];
	$tglPeriode=explode("-",$periode);
	$tanggal=$tglPeriode[1]."-".$tglPeriode[0];
	$tgl_1=tanggalsystem($_GET['tgl_1']);
	$tgl_2=tanggalsystem($_GET['tgl_2']);
	$kdPabrik=$_GET['kdPabrik'];
        $pilTamp=$_GET['pilTamp'];
        $dateDt=dates_inbetween($tgl_1,$tgl_2);
	if($unit!="")
	{
               if($tipeIntex==0)
               {
                    $where= "and kodecustomer='".$unit."'";
               }
               else
               {
                    $where= "and substr(nospb,9,6) like '%".$unit."%'";
               }
	}
	if($kdPabrik!='')
	{
		$where.=" and millcode='".$kdPabrik."'";
	}        
	if(($tgl_1!='')&&($tgl_2!=''))
	{
		
                 $where.=" and tanggal >= ".$tgl_1."000001 and tanggal<=".$tgl_2."235959";
	}
	else
	{
		echo"warning: Date required";
		exit();
	}
         if($tipeIntex==3)
         {
              $gr=" group by kodeorg,kodecustomer,left(tanggal,10),intex";
         }
         elseif($tipeIntex==0)
          {
               $gr=" group by kodecustomer,left(tanggal,10),intex";
               $whr.="and intex='".$tipeIntex."'";
          }
              else
              {
                   $gr=" group by kodeorg,left(tanggal,10)"; 
                   $whr.="and intex='".$tipeIntex."'";
              }
            
	
	$sBrg="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang='400'";
	$qBrg=mysql_query($sBrg) or die(mysql_error($conn));
	while($rBrg=mysql_fetch_assoc($qBrg))
	{
		$rNmBrg[$rBrg['kodebarang']]=$rBrg;
	}
	
	$tab.="<table cellspacing=\"1\" border=0><tr><td colspan=10 align=center>".$_SESSION['lang']['rPenerimaanTbs']."</td></tr>
	";
	if(($kdPabrik!='')&&($unit!=''))
	{
		$tab.="<tr><td colspan=2 align=right>".$_SESSION['lang']['terimaTbs']."</td><td colspan=8>".$kdPabrik." atas ".$rNmBrg[40000003]['namabarang']." ".$_SESSION['lang']['dari']." ".$rNamaSupp[$unit]['namasupplier']." ".$_SESSION['lang']['periode']." :".$tgl_1." s.d. ".$tgl_2."</td></tr>";
	}
	else
	{
		$tab.="<tr><td colspan=2 align=right>".$_SESSION['lang']['terimaTbs']."</td><td colspan=8>".$kdPabrik." atas ".$rNmBrg[40000003]['namabarang']." ".$_SESSION['lang']['dari']." ".$_SESSION['lang']['all']." ".$_SESSION['lang']['periode']." :".tanggalnormal($tgl_1)." s.d. ".tanggalnormal($tgl_2)."</td></tr>";
	}
	$tab.="</table>";
         $sData="select notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,substr(tanggal,1,10) as tanggal,
                supir,nokendaraan,nospb,thntm1,intex,kgpotsortasi
                from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." ".$whr."  order by substr(tanggal,1,10) asc";
        
         //echo "warning".$sData;exit();
         $qData=mysql_query($sData) or die(mysql_error());

         $brs=mysql_num_rows($qData);
         if($brs>0)
         {
			

                    if($pilTamp!=1)
                    {
                                $tab.="<table cellspacing=1 border=1 class=sortable>
                                <thead class=rowheader>
                                <tr>
                                        <td bgcolor=#DEDEDE>No.</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['tanggal']."</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['namasupplier']."/".$_SESSION['lang']['unit']."</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['noTiket']."</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['kodenopol']."</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['beratBersih']."</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['potongankg']."</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['beratnormal']."</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['sopir']."</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['nospb']."</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['jmlhTandan']."</td>
                                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['tahuntanam']."</td>
                                </tr>
                                </thead>
                                <tbody>";
                                $dtIntex="";
                                while($rData=mysql_fetch_assoc($qData))
                                {	
                                        $no+=1;
                                        if($dtIntex!=$rData['intex'])
                                        {
                                            $dtIntex=$rData['intex'];
                                            $sData2="select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih)  as netto,kodecustomer,substr(tanggal,1,10) as tanggal,
                                            supir,nokendaraan,nospb,thntm1,intex
                                            from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." ".$whr." and intex='".$rData['intex']."'  order by intex desc";
                                            //exit("error:".$sData2);
                                            $qData2=mysql_query($sData2) or die(mysql_error($conn));
                                            $rowData=mysql_num_rows($qData2);
                                            $rd=0;
                                        }
                                         if($rData['intex']!=0)
                                         {
                                              $nm=$optNm[$rData['kodeorg']];
                                         }
                                         else
                                         {
                                              $nm=$optSupp[$rData['kodecustomer']];
                                         }
                                         $brtNormal=$rData['netto']-$rData['kgpotsortasi'];
                                         $bgwarna="";
                                         if($rData['nospb']!=''){
                                            $scek="select distinct * from ".$dbname.".kebun_spbdt where nospb='".$rData['nospb']."' and substr(nospb,9,6)<>left(blok,6)";
                                            $qcek=mysql_query($scek) or die(mysql_error($conn));
                                            $rcek=mysql_num_rows($qcek);
                                            if($rcek==1){
                                                $bgwarna="bgcolor=yellow";
                                            }
                                         }
                                        $tab.="
                                        <tr class=rowcontent>
                                        <td>".$no."</td>
                                        <td>".$rData['tanggal']."</td>
                                        <td>".$nm."</td>
                                        <td>".$rData['notransaksi']."</td>
                                        <td>".$rData['nokendaraan']."</td>
                                        <td  align=right>".number_format($rData['netto'],0)."</td>
                                        <td  align=right>".number_format($rData['kgpotsortasi'],0)."</td>
                                        <td  align=right>".number_format($brtNormal,0)."</td>
                                        <td>".$rData['supir']."</td>
                                        <td ".$bgwarna.">".$rData['nospb']."</td>
                                        <td align=right>".number_format($rData['jjg'],0)."</td>
                                        <td>".$rData['thntm1']."</td>
                                        </tr>";
                                        $subtota+=$rData['netto'];
                                        $subTnandn+=$rData['jjg'];
                                        $sbTotaJjg+=$rData['jjg'];
                                        $subTotNett+=$rData['netto'];
                                        $subBrtNor+=$brtNormal;
                                        $subBrtPot+=$rData['kgpotsortasi'];
                                         $rd+=1;
                                         if($rowData==$rd)
                                         {
                                             $tab.="<tr class=rowcontent><td colspan=5>".$intex[$rData['intex']]."</td>";
                                             $tab.="<td align=right>".number_format($subTotNett,0)."</td>";
                                             $tab.="<td align=right>".number_format($subBrtPot,0)."</td>";
                                             $tab.="<td align=right>".number_format($subBrtNor,0)."</td>";
                                             $tab.="<td colspan=2>&nbsp;</td>";
                                             $tab.="<td align=right>".number_format($sbTotaJjg,0)."</td>";
                                             $tab.="<td>&nbsp;</td></tr>";
                                             $sbTotaJjg=0;
                                             $subTotNett=0;
                                        }
                                       $brtNormal=0;
                                }
                                $tab.="<tr class=rowcontent ><td colspan=5 align=right>Total (KG)</td><td align=right>".number_format($subtota,0)."</td><td colspan=4 align=right>Total (JJG)</td><td align=right>".number_format($subTnandn,2)."</td><td>&nbsp;</td></tr>";

                    }
                    else
                    {
                    $dateDt="";
                    $dateDt=array();
                    //exit("error:".$sData);
                    $sData="select notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,substr(tanggal,1,10) as tanggal,
                    supir,nokendaraan,nospb,thntm1,intex
                    from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." ".$whr."
                    HAVING jjg >0 AND netto >0
                        order by substr(tanggal,1,10) asc";
                     //exit("error:".$sData);
                    $qData=mysql_query($sData) or die(mysql_error($conn));
                    while($rData=mysql_fetch_assoc($qData))
                    {
                           $dateDt[$rData['tanggal']]=$rData['tanggal'];
                            if($rData['intex']>0)
                            {
                                $dtSupp[$rData['intex'].$rData['kodeorg']]=$rData['kodeorg'];
                                $dtData[$rData['intex']][$rData['kodeorg'].$rData['tanggal']]+=$rData['netto'];
                                $dtDataJg[$rData['intex']][$rData['kodeorg'].$rData['tanggal']]+=$rData['jjg'];
                            }
                            else
                            {
                                $dtSupp[$rData['intex'].$rData['kodecustomer']]=$rData['kodecustomer'];
                                $dtData2[$rData['intex']][$rData['kodecustomer'].$rData['tanggal']]+=$rData['netto'];
                                $dtDataJg2[$rData['intex']][$rData['kodecustomer'].$rData['tanggal']]+=$rData['jjg'];
                            }
                       
                    }
                    
                    array_multisort($dtSupp);
                    array_multisort($dateDt);
                            $tab.="<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>";
                            $tab.="<tr><td bgcolor=#DEDEDE rowspan=2>".$_SESSION['lang']['namasupplier']."/".$_SESSION['lang']['unit']."</td>";
                            foreach($dateDt as $ar => $isi)
                            {
                                    $qwe=date('D', strtotime($isi));
                                    $tab.="<td align=center bgcolor=#DEDEDE colspan=2>";
                                    if($qwe=='Sun')$tab.="<font color=red>".substr($isi,8,2)."</font>"; else $tab.=(substr($isi,8,2)); 
                                    $tab.="</td>";
                            }
                            $tab.="<td align=center bgcolor=#DEDEDE colspan=2>".$_SESSION['lang']['total']."</td>";
                            $tab.="</tr><tr>";
                            foreach($dateDt as $ar => $isi)
                            {
                                $tab.="<td bgcolor=#DEDEDE >".$_SESSION['lang']['beratBersih']." (Kg)</td>";
                                $tab.="<td bgcolor=#DEDEDE >".$_SESSION['lang']['jmlhTandan']." (JJG)</td>";
                            }
                            $tab.="<td bgcolor=#DEDEDE >".$_SESSION['lang']['beratBersih']." (Kg)</td>";
                            $tab.="<td bgcolor=#DEDEDE >".$_SESSION['lang']['jmlhTandan']." (JJG)</td>";
                            $tab.="</tr></thead><tbody>";
                            
                    foreach($intex as $lstIntex=>$isiTex){
                        foreach($dtSupp as $lsdtSup){
                             if($dtSupp[$lstIntex.$lsdtSup]!='')
                             {
                            if($lstIntex==0)
                             {
                                 $dtData=$dtData2;
                                 $dtDataJg=$dtDataJg2;
                             }
                             
                             if($lstIntex!=0)
                             {
                                  $nm=$optNm[$dtSupp[$lstIntex.$lsdtSup]];
                             }
                             else
                             {
                                  $nm=$optSupp[$dtSupp[$lstIntex.$lsdtSup]];
                             }
                             
                            $tab.="<tr class=rowcontent><td>".$nm."</td>";
                            foreach($dateDt as $ar => $isi)
                            {
                                $tab.="<td align=right>".number_format($dtData[$lstIntex][$lsdtSup.$isi],0)."</td>";
                                $tab.="<td align=right>".number_format($dtDataJg[$lstIntex][$lsdtSup.$isi],0)."</td>";
                                $totKg[$isi]+=$dtData[$lstIntex][$lsdtSup.$isi];
                                $totJjg[$isi]+=$dtDataJg[$lstIntex][$lsdtSup.$isi];
                                $totsmpngkg[$lstIntex.$lsdtSup]+=$dtData[$lstIntex][$lsdtSup.$isi];
                                $totsmpngjjg[$lstIntex.$lsdtSup]+=$dtDataJg[$lstIntex][$lsdtSup.$isi];
                                $totInKg[$lstIntex.$isi]+=$dtData[$lstIntex][$lsdtSup.$isi];
                                $totInJjg[$lstIntex.$isi]+=$dtDataJg[$lstIntex][$lsdtSup.$isi];
                            }
                            $tab.="<td align=right>".number_format($totsmpngkg[$lstIntex.$lsdtSup],0)."</td>";
                            $tab.="<td align=right>".number_format($totsmpngjjg[$lstIntex.$lsdtSup],0)."</td>";
                            $tab.="</tr>";
                            $totkgsmpng[$lstIntex]+=$totsmpngkg[$lstIntex.$lsdtSup];
                            $totjjgsmpng[$lstIntex]+=$totsmpngjjg[$lstIntex.$lsdtSup];
                            }
                        }
                        if($drt!=$lstIntex)
                        {
                            $drt=$lstIntex;
                            $tab.="<tr bgcolor=darkblue><td><font color=white>".$intex[$lstIntex]."</font></td>";
                            foreach($dateDt as $ar => $isi)
                            {
                                $tab.="<td align=right bgcolor=MediumBlue><font color=white>".number_format($totInKg[$lstIntex.$isi],0)."</font></td>";
                                $tab.="<td align=right bgcolor=darkblue><font color=white>".number_format($totInJjg[$lstIntex.$isi],0)."</font></td>";
                            }
                            $tab.="<td align=right bgcolor=MediumBlue><font color=white>".number_format($totkgsmpng[$lstIntex],0)."</font></td>";
                            $tab.="<td align=right><font color=white>".number_format($totjjgsmpng[$lstIntex],0)."</font></td>";
                            $tab.="</tr>";
                        }  
                        $totSmaKg+=$totkgsmpng[$lstIntex];
                        $totSmaJjg+=$totjjgsmpng[$lstIntex];
                    }
                    $tab.="<tr bgcolor=darkgreen><td><font color=white>".$_SESSION['lang']['total']."</font></td>";
                    foreach($dateDt as $ar => $isi)
                    {
                        $tab.="<td align=right bgcolor=Green><font color=white>".number_format($totKg[$isi],0)."</font></td>";
                        $tab.="<td align=right><font color=white>".number_format($totJjg[$isi],0)."</font></td>";
                    }
                    $tab.="<td align=right bgcolor=Green><font color=white>".number_format($totSmaKg,0)."</font></td>";
                    $tab.="<td align=right><font color=white>".number_format($totSmaJjg,0)."</font></td>";
                    $tab.="</tr></tbody></table>";
                    
                           
                       }
         }
        else
        {
                $tab.="<tr class=rowcontent><td colspan=10 align=center>Data empty</td></tr>";
        }
	
			
			//echo "warning:".$strx;
			//=================================================

			
			$tab.="</tbody></table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
			$tglSkrg=date("Ymd");
                        $qwe=date("Hms");
			$nop_="LaporanPenerimaanTbs".$tglSkrg."__".$qwe;
if(strlen($tab)>0)
{
    $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
    gzwrite($gztralala, $tab);
    gzclose($gztralala);
    echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}                            
//			if(strlen($tab)>0)
//			{
//			if ($handle = opendir('tempExcel')) {
//			while (false !== ($file = readdir($handle))) {
//			if ($file != "." && $file != "..") {
//			@unlink('tempExcel/'.$file);
//			}
//			}	
//			closedir($handle);
//			}
//			$handle=fopen("tempExcel/".$nop_.".xls",'w');
//			if(!fwrite($handle,$tab))
//			{
//			echo "<script language=javascript1.2>
//			parent.window.alert('Can't convert to excel format');
//			</script>";
//			exit;
//			}
//			else
//			{
//			echo "<script language=javascript1.2>
//			window.location='tempExcel/".$nop_.".xls';
//			</script>";
//			}
//			closedir($handle);
//			}
	break;
	default:
	break;
}
?>