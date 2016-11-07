<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];

$kdorg=$_POST['kdorg'];
$tahun=$_POST['tahun'];
$tpkar=$_POST['tpkar'];
if($proses=='excel')
{
    $kdorg=$_GET['kdorg'];
    $tahun=$_GET['tahun'];
	$tpkar=$_GET['tpkar'];
}


if($proses=='excel')
 $stream="<table class=sortable cellspacing=1 border=1>";
else
$stream="<table class=sortable cellspacing=1>";

 $stream.="<thead class=rowheader>
    <tr class=rowheader>
       <td rowspan=3 bgcolor=#CCCCCC align=center>No</td>
       <td rowspan=3 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['kodeorganisasi']."</td>
       <td rowspan=3 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nik']."</td>
       <td rowspan=3 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['namakaryawan']."</td>
       <td rowspan=3 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tipekaryawan']."</td>
       <td rowspan=3 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['statuspajak']."</td>
       <td rowspan=3 bgcolor=#CCCCCC align=center>PTKP</td>
       <td rowspan=3 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['npwp']."</td>
       <td rowspan=3 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tahun']."</td>
       <td colspan=".(12*7)."  bgcolor=#CCCCCC align=center>".$_SESSION['lang']['bulan']."</td>
     </tr>";
 
 $stream.="<tr class=rowheader>";
 for($i=1;$i<=12;$i++)
 {
     if(strlen($i)<2)
     {
         $i="0".$i;
     }
  $stream.="<td bgcolor=#CCCCCC colspan=7 align=center>".numToMonth($i,'I','long')."</td>";
 }    
 $stream.="<tr class=rowheader>";
 for($i=1;$i<=12;$i++)
 {
    $stream.="<td bgcolor=#CCCCCC align=center>Penghasilan</td>";
    $stream.="<td bgcolor=#CCCCCC align=center>Akm. Penghasilan</td>";
    $stream.="<td bgcolor=#CCCCCC align=center>Akm. JKK</td>";
    $stream.="<td bgcolor=#CCCCCC align=center>Akm. Jht</td>";
    $stream.="<td bgcolor=#CCCCCC align=center>Biaya Jabatan</td>";
    $stream.="<td bgcolor=#CCCCCC align=center>PKP</td>";
    $stream.="<td bgcolor=#CCCCCC align=center>PPH 21</td>";
 }     
$stream.="</tr>";
$stream.="</thead>";
				

$thndepan=($tahun+1)."-01-01";
				
$iKar="select * from ".$dbname.".datakaryawan where lokasitugas='".$kdorg."' and (tanggalkeluar='0000-00-00' 
        or tanggalkeluar<='".$thndepan."') and tipekaryawan='".$tpkar."' ";
$nKar=mysql_query($iKar) or die (mysql_error($conn));
while($dKar=mysql_fetch_assoc($nKar))
{
    $karyawanId[$dKar['karyawanid']]=$dKar['karyawanid'];
    $subbagian[$dKar['karyawanid']]=$dKar['subbagian'];
    $nik[$dKar['karyawanid']]=$dKar['nik'];
    $nama[$dKar['karyawanid']]=$dKar['namakaryawan'];
    $tipe[$dKar['karyawanid']]=$dKar['tipekaryawan'];
    $status[$dKar['karyawanid']]=$dKar['statuspajak'];
    $statuspajak[$dKar['karyawanid']]=$dKar['statuspajak'];
    $npwp[$dKar['karyawanid']]=$dKar['npwp'];
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
        
$ijkk="select persen from ".$dbname.".sdm_ho_pph21jaminan where regional='".$_SESSION['empl']['regional']."' and tipe='jkk' ";
       $njkk=mysql_query($ijkk) or die (mysql_error($conn))."____".$ijkk;
       $djkk=mysql_fetch_assoc($njkk);
            $jkk=$djkk['persen'];
        
$ijht="select persen from ".$dbname.".sdm_ho_pph21jaminan where regional='".$_SESSION['empl']['regional']."' and tipe='jht' ";
$njht=mysql_query($ijht) or die (mysql_error($conn))."____".$ijht;
$djht=mysql_fetch_assoc($njht);
    $jht=$djht['persen'];   

#triger agar hanya sekali loopnya
$ar=1;
$arer=false;
$iGaji="select sum(jumlah) as gaji,a.karyawanid,periodegaji,right(periodegaji,2) as bln,idkomponen 
        from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
		where kodeorg='".$kdorg."' and periodegaji like '".$tahun."%' and tipekaryawan='".$tpkar."'
        and idkomponen in (select id from ".$dbname.".sdm_5komponenpph where regional='".$_SESSION['empl']['regional']."' and status=1) 
        group by a.karyawanid,idkomponen,periodegaji order by periodegaji asc";
		//echo $iGaji;
$nGaji=mysql_query($iGaji) or die (mysql_error($conn));
while($dGaji=mysql_fetch_assoc($nGaji)){
    $gaji[$dGaji['karyawanid']][$dGaji['periodegaji']]+=$dGaji['gaji'];
    if($blnKcl!=$dGaji['bln']){
        $blnKcl=intval($dGaji['bln']);
    }
    $lstBln[$dGaji['bln']]=$dGaji['bln'];
    for($maxBln=$blnKcl;$maxBln<=12;$maxBln++){
        $bln=$maxBln;
        if(strlen($maxBln)==1){
            $bln="0".$maxBln;
        }
        if($arer==false){
            $arPtkp[$bln]=$ar;
        }
        if(($dGaji['idkomponen']=='26')||($dGaji['idkomponen']=='71')||($dGaji['idkomponen']=='28')){
                $gajiSthnThrBns[$dGaji['karyawanid']][$bln]+=$dGaji['gaji'];
            }else{
                $gajiBruto[$dGaji['karyawanid']][$bln]+=$dGaji['gaji'];
        }
        if($maxBln!=12){
            if($arer==false){
                 $ar+=1;
            }
        }else{
            if($arer==false){
                $arer=true;
                $ar+=1;
            }
        }
       
    }  
}
        
if($_SESSION['empl']['regional']=='SULAWESI') {    
        #########HIP                                 
        #penghasilah disetahunkan 
        foreach($gajiBruto as $xid =>$jlh){
            
            foreach ($lstBln as $bln) {
        
                # code...
            #penghasilan 1thn + jhk 1thn
            //$penghasilanSetahun[$xid]=($jlh*12)+($jkk/100*$jlh*12);
            $prd=$tahun."-".$bln;
            if(strlen(intval($bln))==1){
                if(intval($bln)==1){
                    $prdsblm=($tahun-1)."-12";
                }else{
                    $prdsblm=($tahun."-0".(intval($bln)-1));
                }
            }else{
                $prdsblm=$tahun."-".(intval($bln)-1);
            }
            //$penghasilanSetahun[$xid][$prd]=$jlh[$bln]+(($jkk/100*$jlh[$bln])+($jht/100*$jlh[$bln]));
            $jkkperkar[$xid][$bln]=$jkk/100*$jlh[$bln];
            $jhtperkar[$xid][$bln]=$jht/100*$jlh[$bln];
            
            @$penghasilanSetahun[$xid][$prd]=($jlh[$bln]+$gajiSthnThrBns[$xid][$bln]+($jlh[$bln]/$arPtkp[$bln])*(12-$arPtkp[$bln])+$jkkperkar[$xid][$bln]+($jkkperkar[$xid][$bln]/$arPtkp[$bln])*(12-$arPtkp[$bln]));
        
            
            #periksa biaya jabatan=========================== 
            $biayaJab[$xid][$prd]=$penghasilanSetahun[$xid][$prd]*$jabPersen;
            //exit("error:".$biayaJab[$xid][$prd]."___".$xid."___".$jlh[$bln]."___".$bln);
            if($biayaJab[$xid][$prd]>$jabMax){ #jika lebih dari max maka dibatasi sebesar max
                $biayaJab[$xid][$prd]=$jabMax;
            }
            //$penghasilanKurangJab[$xid]=$penghasilanSetahun[$xid]-$biayaJab[$xid]-($jht/100*$jlh*12);//(($jkk/100*$jlh)+($jht/100*$jlh))
            //$penghasilanKurangJab[$xid][$prd]=$penghasilanSetahun[$xid][$prd]-$biayaJab[$xid][$prd]-(($jkk/100*$jlh[$bln])+($jht/100*$jlh[$bln]));
            @$penghasilanKurangJab[$xid][$prd]=($penghasilanSetahun[$xid][$prd]-($jhtperkar[$xid][$bln]+($jhtperkar[$xid][$bln]/$arPtkp[$bln])*(12-$arPtkp[$bln])))-$biayaJab[$xid][$prd];
            //exit("error:".$penghasilanKurangJab[$xid][$prd]."___".$ptkp[str_replace("K","",$statuspajak[$xid])]."___".$biayaJab[$xid][$prd]);
            #kurangkan dengan PTKP===============bug done by ind 
            if($penghasilanKurangJab[$xid][$prd]<$ptkp[str_replace("K","",$statuspajak[$xid])]){
                $penghasilanKurangJab[$xid][$prd]=0;
            }else{
                $penghasilanKurangJab[$xid][$prd]=(($penghasilanKurangJab[$xid][$prd]-$ptkp[str_replace("K","",$statuspajak[$xid])])/1000)*1000;
            }
            $pkp[$xid][$prd]=$penghasilanKurangJab[$xid][$prd];//$satatuspajak ambil dr array pertama di atas    
            $zz=0;
             $sisazz=0;
             if($pkp[$xid][$prd]>0){
                 if($pkp[$xid][$prd]<($pphtarif[0]+1)){
                     $zz=$pkp[$xid][$prd]*$pphpercent[0];
                 }
                 else if($pkp[$xid][$prd]<($pphtarif[1]+1)){
                     //50000000*0.05+(N17-50000000)*0.15
                     $zz=$pphtarif[0]*$pphpercent[0]+($pkp[$xid][$prd]-$pphtarif[0])*$pphpercent[1];
                     
                 }else if($pkp[$xid][$prd]<($pphtarif[2]+1)){
                     //50000000*0.05+200000000*0.15+(N6-250000000)*0.25
                     $zz=$pphtarif[0]*$pphpercent[0]+($pphtarif[1]-$pphtarif[0])*$pphpercent[1]+($pkp[$xid][$prd]-$pphtarif[1])*$pphpercent[2];
                 }else{
                     //50000000*0.05+200000000*0.15+250000000*0.25+(N6-500000000)*0.3
                     $zz=$pphtarif[0]*$pphpercent[0]+($pphtarif[1]-$pphtarif[0])*$pphpercent[1]+$pphtarif[1]*$pphpercent[2]+($pkp[$xid][$prd]-$pphtarif[2])*$pphpercent[3];
                 }
             #tahap 1: 
                 /*if($pkp[$xid][$prd]<($pphtarif[0]+1))
                 {//exit("Error:Y");
                     $zz+=$pphpercent[0]*$pkp[$xid][$prd];
                     $sisazz=0;
                 }
                 else if($pkp[$xid][$prd]>=$pphtarif[0])
                 {
                     $zz+=$pphpercent[0]*$pphtarif[0];
                     $sisazz=$pkp[$xid][$prd]-$pphtarif[0];
                     #level 2
                         if($sisazz<($pphtarif[1]-$pphtarif[0]))
                         {
                             $zz+=$pphpercent[1]*$sisazz;
                             $sisazz=0;        
                         }    
                         else if($sisazz>=($pphtarif[1]-$pphtarif[0]))
                         {
                             $zz+=$pphpercent[1]*($pphtarif[1]-$pphtarif[0]);
                             $sisazz=$pkp[$xid][$prd]-$pphtarif[1]; 
                             #level 3   
                                 if($sisazz<($pphtarif[2]-$pphtarif[1]))
                                 {
                                     $zz+=$pphpercent[2]*$sisazz;
                                     $sisazz=0;        
                                 }    
                                 else if($sisazz>=($pphtarif[2]-$pphtarif[1]))
                                 {
                                     $zz+=$pphpercent[2]*($pphtarif[2]-$pphtarif[1]);
                                     $sisazz=$pkp[$xid][$prd]-$pphtarif[2];
                                      // print_r($sisazz);exit();
                                         if($sisazz>0){
                                         #level 4  sisanya kali 30% 
                                             $zz+=$pphpercent[3]*$sisazz;  
                                         }                          
                                 } 
                         }   

                 }*/
             }
                #zz adalah PPh Setahun per karyawan
                 //$pphSetahun[$xid][$prd]=$zz/12;
                 $pphSetahun[$xid][$prd]=(round($zz)*($arPtkp[$bln]/12))-round($akmPph[$xid]);
                 if($pphSetahun[$xid][$prd]<0){
                    $pphSetahun[$xid][$prd]=0;
                 }
                 //jika tidak memiliki NPWP maka tambahkan 20% dari PPh yang ada
                 if($npwp[$xid]==''){
				     $pph[$xid][$prd]=$pphSetahun[$xid][$prd]+($pphSetahun[$xid][$prd]*20/100);
                 }else{
                     $pph[$xid][$prd]=$pphSetahun[$xid][$prd];
                 }
                 $akmPph[$xid]+=$pph[$xid][$prd];
             }           
            }   
/*echo "<pre>";
    print_r($pphSetahun);
echo "</pre>";*/
}else{
#pph
$iGaji="select jumlah,karyawanid,periodegaji from ".$dbname.".sdm_gaji where kodeorg like '".$kdorg."%' and periodegaji like '".$tahun."%' 
        and idkomponen='44' group by karyawanid,periodegaji";
$nGaji=mysql_query($iGaji) or die (mysql_error($conn));
while($dGaji=mysql_fetch_assoc($nGaji))
{
    $karyawanId[$dGaji['karyawanid']][$dGaji['periodegaji']]=$dGaji['karyawanid'];
   if ($_SESSION['empl']['regional']=='KALIMANTAN') {
       # code...
       $pph[$dGaji['karyawanid']][$dGaji['periodegaji']]=$dGaji['jumlah'];
   }
    $periodegaji[$dGaji['karyawanid']][$dGaji['periodegaji']]=$dGaji['periodegaji'];
    
}

}


$periodegaji=array("$tahun-01"=>"$tahun-01","$tahun-02"=>"$tahun-02","$tahun-03"=>"$tahun-03"
                    ,"$tahun-04"=>"$tahun-04","$tahun-05"=>"$tahun-05","$tahun-06"=>"$tahun-06"
                    ,"$tahun-07"=>"$tahun-07","$tahun-08"=>"$tahun-08","$tahun-09"=>"$tahun-09"
                    ,"$tahun-10"=>"$tahun-10","$tahun-11"=>"$tahun-11","$tahun-12"=>"$tahun-12");


$arrTpKar=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
$arrStPajak=  makeOption($dbname,'sdm_5statuspajak','kode,nama');



foreach($karyawanId as $karId)
{
    if($subbagian[$karId]=='')
        $subbagian[$karId]=$kdorg;
    
    $no+=1;
    $stream.="<tr class=rowcontent>
    <td>".$no."</td>
    <td>".$subbagian[$karId]."</td>
    <td>'".$nik[$karId]."</td>
    <td>".$nama[$karId]."</td>
    <td>".$arrTpKar[$tipe[$karId]]."</td>
    <td>".$status[$karId]."</td>
    <td align=right>".number_format($ptkp[str_replace("K","",$statuspajak[$karId])],0)."</td>
    <td>".$npwp[$karId]."</td>
    <td>".$tahun."</td>";
    
        foreach($periodegaji as $perGaji)
        {
            $bln=substr($perGaji,5,2);
            $stream.="<td align=right>".number_format($gaji[$karId][$perGaji])."</td>";
            $stream.="<td align=right>".number_format(($gajiBruto[$karId][$bln]+$gajiSthnThrBns[$karId][$bln]))."</td>";
            $stream.="<td align=right>".number_format(($jkk/100*$gajiBruto[$karId][$bln]))."</td>";
            $stream.="<td align=right>".number_format(($jht/100*$gajiBruto[$karId][$bln]))."</td>";
            $stream.="<td align=right>".number_format($biayaJab[$karId][$perGaji])."</td>";
            $stream.="<td align=right>".number_format($penghasilanKurangJab[$karId][$perGaji])."</td>";
            $stream.="<td align=right>".number_format($pph[$karId][$perGaji])."</td>";
        }
      
}


$stream.="<tbody></table>";
switch($proses)
{
######PREVIEW
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="lapora_PPH21_".$kdorg."_".$tahun;
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
		break;

###############	
#panggil PDFnya
###############
	
		case'pdf':
/*
            class PDF extends FPDF
                    {
                        function Header() {
                            global $conn;
                            global $dbname;
                            global $align;
                            global $length;
                            global $colArr;
                            global $title;
							global $kdorg;
							global $kdAfd;
							global $tgl1;
							global $tgl2;
							global $where;
							global $nmOrg;
							global $lok;
							global $notrans;
							global $bulan;
							global $ang;
							global $kar;
							global $namaang;
							global $namakar;
							

                            //$cols=247.5;
                            $query = selectQuery($dbname,'organisasi','alamat,telepon',
                                "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                            $orgData = fetchData($query);

                            $width = $this->w - $this->lMargin - $this->rMargin;
                            $height = 20;
                            $path='images/logo.jpg';
                            //$this->Image($path,$this->lMargin,$this->tMargin,50);	
							$this->Image($path,30,15,55);
                            $this->SetFont('Arial','B',9);
                            $this->SetFillColor(255,255,255);	
                            $this->SetX(90); 
							  
                            $this->Cell($width-80,12,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                            $this->SetX(90); 		
			                $this->SetFont('Arial','',9);
							$height = 12;
                            $this->Cell($width-80,$height,$orgData[0]['alamat'],0,1,'L');	
                            $this->SetX(90); 			
                            $this->Cell($width-80,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                            $this->Ln();
                            $this->Line($this->lMargin,$this->tMargin+($height*4),
                            $this->lMargin+$width,$this->tMargin+($height*4));

                            $this->SetFont('Arial','B',12);
                                            $this->Ln();
                            $height = 15;
                                            $this->Cell($width,$height,'Laporan Stock Opname','',0,'C');
                                            $this->Ln();
                            $this->SetFont('Arial','',10);
                                            
                            $this->SetFont('Arial','B',7);
                            $this->SetFillColor(220,220,220);
                                            $this->Cell(3/100*$width,15,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
                                            $this->Cell(15/100*$width,15,'Kode Barang',1,0,'C',1);
											$this->Cell(35/100*$width,15,'Nama Barang',1,0,'C',1);
											$this->Cell(15/100*$width,15,'Saldo Fisik OWL',1,0,'C',1);
											$this->Cell(15/100*$width,15,'Saldo Fisik Gudang',1,0,'C',1);
											$this->Cell(15/100*$width,15,'Selisih',1,1,'C',1);
						
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
                    $height = 15;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',7);
		

		
		$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());//tinggal tarik $res karna sudah di declarasi di atas
		$no=0;
		//$ttl=0;
		while($bar=mysql_fetch_assoc($qry))
		{	

			$no+=1;
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);	
			$pdf->Cell(15/100*$width,$height,$bar['kodebarang'],1,0,'R',1);		
			$pdf->Cell(35/100*$width,$height,$nmbarang[$bar['kodebarang']],1,0,'L',1);		
			$pdf->Cell(15/100*$width,$height,number_format($bar['saldoqty']),1,0,'R',1);
			$pdf->Cell(15/100*$width,$height,'',1,0,'R',1);		
			$pdf->Cell(15/100*$width,$height,'',1,1,'R',1);	
	
		}
		$pdf->Output();
            */
	$dompdf = new DOMPDF();
	$dompdf->load_html($stream);
	$dompdf->render();
	$dompdf->stream('laporan_test.pdf');
	break;	
	
}


?>












