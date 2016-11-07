<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include('lib/zFunction.php');
require_once('lib/fpdf.php');
if(isset($_GET['proses'])!=''){
    $_POST=$_GET;
}
$param=$_POST;
 
$optPend=makeOption($dbname,'sdm_5pendidikan','levelpendidikan,pendidikan');
$optJbtn=makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optNmlowongan=makeOption($dbname, 'sdm_permintaansdm', 'notransaksi,namalowongan');
$optNmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
switch($param['proses'])
{
    case'getData':        
	$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sprd="select distinct idpermintaan,namalowongan from ".$dbname.".`sdm_testcalon` 
               where periodetest='".$_POST['periodeTest']."' order by periodetest desc";
	$qprd=mysql_query($sprd) or die(mysql_error($conn));
	while($rprd=mysql_fetch_assoc($qprd)){
            $optPeriode.="<option value='".$rprd['idpermintaan']."'>".$rprd['namalowongan']."</option>";
	}
        echo $optPeriode;
    break;
    case'loadData':
        $saks="select distinct * from ".$dbname.".setup_remotetimbangan 
               where lokasi='HRDJKRT'";			
        $qaks=mysql_query($saks) or die(mysql_error($conn));
        $jaks=mysql_fetch_assoc($qaks);
        $uname2=$jaks['username'];
        $passwd2=$jaks['password'];
        $dbserver2=$jaks['ip'];
        $dbport2=$jaks['port'];
        $dbdt=$jaks['dbname'];
      
        //$conn2=mysql_connect($dbserver2.":".$dbport2,$uname2,$passwd2) or die("Error/Gagal :Unable to Connect to database ".$dbserver2);
        //$conn2=mysql_connect('192.168.1.204','root','dbdev');
        $conn2=mysql_connect($dbserver2,$uname2,$passwd2);
        if (!$conn2)
          {
          die('Could not connect: ' . mysql_error());
          }

	if(($param['periodeTest']=='')||($param['nmLowongan']=='')){
            exit("error: Semua Field Tidak Boleh Kosong");
	}
	echo"<input type=hidden id=nopermintaan value='".$param['nmLowongan']."' />";
	echo"
        <table border=0><tr><td valign=top>    
        <table cellpadding=2 cellspacing=1 border=0 class=sortable>
               <thead>
	       <tr class=rowheader>";
	echo"<td>No</td>";
	echo"<td>".$_SESSION['lang']['email']."</td>";
        echo"<td>".$_SESSION['lang']['nama']."</td>";
	echo"<td>".$_SESSION['lang']['pendidikan']."</td>";
	echo"<td>".$_SESSION['lang']['action']."</td>";
	echo"</tr></thead><tbody id=listData>"; 
        $sdt="select distinct * from ".$dbname.".sdm_testcalon where 
              hasilpsy in ('Recomended','ToBeConsidred') 
              and idpermintaan='".$param['nmLowongan']."'  order by email asc";
        //exit("error:".$sdt);
        $qdt=mysql_query($sdt,$conn) or die(mysql_error($conn));
        while($rdt=  mysql_fetch_assoc($qdt)){
            $nor+=1;
            $sdt2="select distinct namacalon from ".$dbdt.".datacalon where email='".$rdt['email']."'";
            $qdt2=mysql_query($sdt2,$conn2) or die(mysql_error($conn2));
            $rdt2=mysql_fetch_assoc($qdt2);
            
            $sdt3="select distinct levelpendidikan from ".$dbdt.".pendidikan where email='".$rdt['email']."'  order by levelpendidikan desc ";
            $qdt3=mysql_query($sdt3,$conn2) or die(mysql_error($conn2));
            $rdt3=mysql_fetch_assoc($qdt3);
            $adert="##nmLowongan##emailDt_".$nor."";
            echo"<tr class=rowcontent>";
            echo"<td>".$nor."</td>";
            echo"<td id=emailDt_".$nor." value='".$rdt['email']."'>".$rdt['email']."</td>";
            echo"<td id=namaDt_".$nor.">".$rdt2['namacalon']."</td>";
            echo"<td>".$optPend[$rdt3['levelpendidikan']]."</td>";
            echo"<td>
            <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"zPdf('sdm_slave_finalDecison','".$adert."','".$nor."','contentData')\">       
            <button class='mybutton' onclick='getFormPenilaian(".$nor.")' >Penilaian</button>
            </td>";
            echo"</tr>";
          }
	
	echo"</tbody></table></td><td valign=top><div id=dtForm></div><div id=formPen style='display:none'></div><td></tr></table>";
	 
	break;
        
        case'getForm':
        
        
        $dert="select distinct * from ".$dbname.".sdm_testcalon where 
               email='".$param['emailDt']."' and  idpermintaan='".$param['idPermintaan']."'";
        $qdert=mysql_query($dert) or die(mysql_error($conn));
        $rdert=mysql_fetch_assoc($qdert);
            $derjam=explode(" ",$rdert['tglpsy']);
            $jamd=substr($derjam[1],0,2);
            $menm=substr($derjam[1],3,2);
            for($i=0;$i<24;)
            {
                    if(strlen($i)<2)
                    {
                            $i="0".$i;
                    }
               $jm.="<option value=".$i." ".($jamd==$i?"selected":"").">".$i."</option>";
               $i++;
            }
            for($i=0;$i<60;)
            {
                    if(strlen($i)<2)
                    {
                            $i="0".$i;
                    }
               $mnt.="<option value=".$i."  ".($menm==$i?"selected":"").">".$i."</option>";
               $i++;
            }
        $arrenum=getEnum($dbname,'sdm_testcalon','hasilmedical');
        foreach($arrenum as $key=>$val)
        {
                $optGoldar.="<option value='".$key."' ".($rdert['hasilmedical']==$key?"selected":"").">".$val."</option>";
        }
        $arrenum2=getEnum($dbname,'sdm_testcalon','hasilakhir');
        foreach($arrenum2 as $key=>$val)
        {
                $optGoldar2.="<option value='".$key."' ".($rdert['hasilakhir']==$key?"selected":"").">".$val."</option>";
        }
        
        $dFrom="<div style=\"background-color:#CCCCCC\">
                <fieldset><legend>".$_SESSION['lang']['form']." ".$_SESSION['lang']['nilai']." Hasil Akhir </legend>
                <table cellpadding=1 cellspacing=1 border=0>";
        $dFrom.="<tr><td>".$_SESSION['lang']['nama']."</td><td><input type=text class=myinputtext value='".$param['namacalon']."' style='width:150px' disabled />";
        $dFrom.="</td></tr>";
        $dFrom.="<tr><td>".$_SESSION['lang']['hasil']."</td>";
        $dFrom.="<td><select id=hasilIntview style=width:150px;>".$optGoldar."</select></td</tr>";
        $dFrom.="<tr><td>".$_SESSION['lang']['catatan'].'</td>';
        $dFrom.="<td><textarea id=catatan>".$rdert['keteranganmedical']."</textarea></td></tr>";
        $dFrom.="<tr><td>".$_SESSION['lang']['tanggal']." Medical</td>";
        $dFrom.="<td><input type=text class=myinputtext id=tglinterview 
                    onmousemove=setCalendar(this.id) onkeypress=return false;  size=18 maxlength=10 value='".tanggalnormal($rdert['tglmedical'])."' /></td></tr>";
        $dFrom.="<tr><td>".$_SESSION['lang']['namaprovider']."</td><td>
             <input type=text id=nmprovider class=myinputtext value='".$rdert['namaprovidermedical']."' style='width:150px' maxlength='45' onkeypress='return tanpa_kutip(event)' />";
        $dFrom.="</td></tr>"; 
        $dFrom.="</table><button class=mybutton onclick=saveView()>".$_SESSION['lang']['save']."</button></fieldset>
                 <input type=hidden id=emailDt value='".$param['emailDt']."' />";
        $dFrom.="</div>";
        echo $dFrom;
            
        break;
        case'insrData':
            if($param['nmprovider']==''){
                exit("error: ".$_SESSION['lang']['namaprovider']." Tidak Boleh Kosong!!");
            }
            
        $sinsrt="update ".$dbname.".sdm_testcalon set `tglmedical`='".tanggalsystem($param['tglMedical'])."',hasilmedical='".$param['hasilMedical']."',
                 keteranganmedical='".$param['cttnMedical']."',namaprovidermedical='".$param['nmprovider']."'
                 where email='".$param['emailDt']."' and idpermintaan='".$param['idpermintaan']."'";
         if(!mysql_query($sinsrt)){
             die(mysql_error($conn))."___".$sdel;
         }
        break;
         case'zpdf':
         $idek=$param['idKebrp'];
         $param['emailDt_'.$idek];
         
class PDF extends FPDF
{
	function Header()
	{
	if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
	    $this->Image($path,15,2,40);	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetY(22);   
	        $this->Cell(60,5,  strtoupper($_SESSION['org']['namaorganisasi']),0,1,'C');	 
		 
		$this->SetFont('Arial','',6); 
		$this->SetY(30);
		$this->SetX(163);
                $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
		$this->Line(10,32,200,32);	   
	}
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}
}


	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
	
	$pdf->SetFont('Arial','B',10);		
	$pdf->Cell(25,5,strtoupper($_SESSION['lang']['interviewer'])." :",0,1,'L');
	$pdf->SetFont('Arial','',7);												
    $sdrTest="select distinct * from ".$dbname.".sdm_testcalon where 
              email='".$param['emailDt_'.$idek]."' and idpermintaan='".$param['nmLowongan']."'";
    $qdrTest=mysql_query($sdrTest) or die(mysql_error($conn));
    $rdrTest=mysql_fetch_assoc($qdrTest);
    $pdf->SetFillColor(220,220,220);
    $pdf->Cell(30,4,$_SESSION['lang']['interviewer'],1,0,'C',1);
    $pdf->Cell(30,4,$_SESSION['lang']['tanggal'],1,0,'C',1);	
    $pdf->Cell(35,4,$_SESSION['lang']['keputusan'],1,0,'C',1);		
    $pdf->Cell(95,4,$_SESSION['lang']['catatan'],1,1,'C',1);
//loop isinya
	 $str="select * from ".$dbname.".sdm_interview 
               where  email='".$param['emailDt_'.$idek]."' order by email";
	 $res=mysql_query($str);
	 $no=0;
	 $mskerja=0;
	 while($bar=mysql_fetch_object($res))
	 {
	    $pdf->Cell(30,4,$optNmKar[$bar->interviewer],0,0,'L',0);
	    $pdf->Cell(30,4,tanggalnormal($rdrTest['tglivew']),0,0,'L',0);	
	    $pdf->Cell(35,4,$bar->hasil,0,0,'L',0);		
	    $pdf->MultiCell(95, 4,$bar->catatan,0,'J');
	 }	
         $pdf->Cell(30,4,$_SESSION['lang']['keputusan']." ".$_SESSION['lang']['interviewer'],1,0,'L',0);
         $pdf->Cell(160,4,$rdrTest['hasiliview'],1,1,'L',0);		
 
        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',10);		
        $pdf->Cell(25,5,strtoupper($_SESSION['lang']['psikotest'])." :",0,1,'L');
        $pdf->SetFont('Arial','',7);												
        $pdf->SetFillColor(220,220,220);
        $pdf->Cell(30,4,$_SESSION['lang']['tanggal'],1,0,'C',1);
        $pdf->Cell(25,4,$_SESSION['lang']['provider'],1,0,'C',1);
        $pdf->Cell(25,4,$_SESSION['lang']['namaprovider'],1,0,'C',1);
        $pdf->Cell(35,4,$_SESSION['lang']['hasil'],1,0,'C',1);		
        $pdf->Cell(75,4,$_SESSION['lang']['catatan'],1,1,'C',1);
         
        $pdf->Cell(30,4,tanggalnormald($rdrTest['tglpsy']),'B',0,'L',0);
        $pdf->Cell(25,4,$rdrTest['provider'],'B',0,'L',0);	
        $pdf->Cell(25,4,$rdrTest['namaprovider'],'B',0,'L',0);	
        $pdf->Cell(35,4,$rdrTest['hasilpsy'],'B',0,'L',0);		
        $pdf->MultiCell(75, 4,$rdrTest['keteranganpsy'],'B','J');
        
        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',10);		
        $pdf->Cell(25,5,strtoupper("medical")." :",0,1,'L');
        $pdf->SetFont('Arial','',7);												
        $pdf->SetFillColor(220,220,220);
        $pdf->Cell(30,4,$_SESSION['lang']['tanggal'],1,0,'C',1);
        $pdf->Cell(30,4,$_SESSION['lang']['provider'],1,0,'C',1);	
        $pdf->Cell(35,4,$_SESSION['lang']['hasil'],1,0,'C',1);		
        $pdf->Cell(95,4,$_SESSION['lang']['catatan'],1,1,'C',1);
        //loop isinya
         
        $pdf->Cell(30,4,tanggalnormal($rdrTest['tglmedical']),'B',0,'L',0);
        $pdf->Cell(30,4,$rdrTest['namaprovidermedical'],'B',0,'L',0);	
        $pdf->Cell(35,4,$rdrTest['hasilmedical'],'B',0,'L',0);		
        $pdf->MultiCell(95, 4,$rdrTest['keteranganmedical'],'B','J');
        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',10);	
        $pdf->Cell(30,8,$_SESSION['lang']['keputusan'],0,0,'L',0);
        $pdf->Cell(5,8,":",0,0,'L',0);
        $pdf->Cell(80,8,$rdrTest['hasilakhir'],'B',1,'L',0);
        $pdf->Cell(30,8,$_SESSION['lang']['catatan'],0,0,'L',0);
        $pdf->Cell(5,8,":",0,0,'L',0);
        $pdf->MultiCell(80,8,$rdrTest['catatanakhir'],'B','J');
	$pdf->Output();	
    break;
}
?>