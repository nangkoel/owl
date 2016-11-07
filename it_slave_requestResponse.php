<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['pelaksana']==''?$pelaksana=$_GET['pelaksana']:$pelaksana=$_POST['pelaksana'];
$_POST['notransaksi']==''?$notransaksi=$_GET['notransaksi']:$notransaksi=$_POST['notransaksi'];
$arrNmkary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmKeg=makeOption($dbname, 'it_standard', 'kodekegiatan,keterangan');
$arrKeputusan=array("0"=>$_SESSION['lang']['diajukan'],"1"=>$_SESSION['lang']['disetujui'],"2"=>$_SESSION['lang']['ditolak']);
$saran=$_POST['saran'];
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$arrStatus=array("0"=>"Menunggu", "1"=>"Setuju");
//exit("Error".$jmAwal);
	switch($proses)
	{
		case'loadData':
		$limit=25;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
                if($pelaksana!='')
                {
                    $where=" and pelaksana='".$pelaksana."'";
                    $ql2="select count(*) as jmlhrow from ".$dbname.".it_request 
                      where  pelaksana!=0 ".$where." order by `tanggal` desc";// echo $ql2;
                    $sCek="select * from ".$dbname.".it_request 
                        where pelaksana!=0 ".$where." 
                        order by `tanggal` desc";
                     $slvhc="select * from ".$dbname.".it_request 
                        where pelaksana!=0  ".$where." 
                        order by `tanggal` desc limit ".$offset.",".$limit." ";
                }
                else
                {
                    $ql2="select count(*) as jmlhrow from ".$dbname.".it_request 
                      where waktuselesai='0000-00-00 00:00:00' and pelaksana!=0 ".$where." order by `tanggal` desc";// echo $ql2;
                    $sCek="select * from ".$dbname.".it_request 
                        where waktuselesai='0000-00-00 00:00:00'  and pelaksana!=0 ".$where." 
                        order by `tanggal` desc";
                     $slvhc="select * from ".$dbname.".it_request 
                        where waktuselesai='0000-00-00 00:00:00'  and pelaksana!=0  ".$where." 
                        order by `tanggal` desc limit ".$offset.",".$limit." ";
                }
                
		//$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
                $qCek=mysql_query($sCek) or die(mysql_error($conn));
                $rCek=mysql_num_rows($qCek);
                if($rCek>0)
                {
		//$slvhc="select * from ".$dbname.".sdm_ijin where  karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc limit ".$offset.",".$limit." ";
               
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		$user_online=$_SESSION['standard']['userid'];
		while($rlvhc=mysql_fetch_assoc($qlvhc))
		{
		$no+=1;
                
		echo"
		<tr class=rowcontent>
                <td>".$no."</td>
                <td>".tanggalnormal($rlvhc['tanggal'])."</td>
                <td>".$optNmKeg[$rlvhc['kodekegiatan']]."</td>
                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>
                <td>".$arrStatus[$rlvhc['statusatasan']]."</td>
                <td>".$arrNmkary[$rlvhc['pelaksana']]."</td>";
                    if(($rlvhc['statusatasan']==1)&&($rlvhc['statusmanagerit']==1))
                    {
                        if($_SESSION['standard']['userid']==$rlvhc['pelaksana'])
                        {
                            if($rlvhc['nilaikomunikasi']==0||$rlvhc['nilaihasilkerja']==0)
                            {
                                echo"<td><textarea id=saranPelaksana onkeypress=tanpa_kutip(event)></textarea></td>";
                                echo"<td><button class=mybutton id=dtlForm onclick=slsi('".$rlvhc['notransaksi']."')>".$_SESSION['lang']['done']."</button></td>";
                            }
                            else
                            {
                                echo"<td>".substr($rlvhc['saranpelaksana'],0,15)."....</td>";
                                echo"<td>&nbsp;</td>";
                            }
                        }
                        else
                        {   
                            echo"<td>".substr($rlvhc['saranpelaksana'],0,15)."....</td>";
                            echo"<td>&nbsp;</td>";
                        }
                    }
                    else
                    {
                        echo"<td>".substr($rlvhc['saranpelaksana'],0,15)."....</td>";
                        echo"<td>&nbsp;</td>";
                    }
                    echo"<td align=center> <img src=images/zoom.png class=resicon  title='Print' onclick=\"detailData(event,'it_slave_requestResponse.php','".$rlvhc['notransaksi']."')\"></td>";
                }
		echo"
		</tr><tr class=rowheader><td colspan=13 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                }
                else
                {
                    echo"<tr class=rowcontent><td colspan=13>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
		break;
                
                case 'updateData':
                $sGet="select distinct karyawanid,b.keterangan,waktupelaksanaan,tanggal,deskripsi from ".$dbname.".it_request a left join
                       ".$dbname.".it_standard b on a.kodekegiatan=b.kodekegiatan where notransaksi='".$notransaksi."'";
                $qGet=mysql_query($sGet) or die(mysql_error($conn));
                $rGet=mysql_fetch_assoc($qGet);
                //ambil selisih waktu
                $tglAwal=substr($rGet['waktupelaksanaan'],0,10);
                $jmAwal=substr($rGet['waktupelaksanaan'],11,8);
                //exit("Error:".$tglAwal."___".$jmAwal);
                list($thn,$mth,$dy)=explode("-",$tglAwal);
                list($h,$m,$s) = explode(":",$jmAwal);
                $dtAwal = mktime($h,$m,$s,intval($mth),$dy,$thn);
                $tlgjmskrng=date("Y-m-d H:i:s");
                $tglAkhir=substr($tlgjmskrng,0,10);
                $jmAkhir=substr($tlgjmskrng,11,8);
                //exit("Error:".$tglAkhir."___".$jmAkhir);
                list($h,$m,$s) = explode(":",$jmAkhir);
                list($thn,$mth,$dy)=explode("-",$tglAkhir);
                $dtAkhir = mktime($h,$m,$s,intval($mth),$dy,$thn);
                $dtSelisih = $dtAkhir-$dtAwal;
                //exit("Error:".$dtSelisih."___".$dtAkhir."___".$dtAwal."___".$mth."__".$dy."__".$thn);
                $totalmenit=$dtSelisih/60;
                $jam =explode(".",$totalmenit/60);
                $sisamenit=($totalmenit/60)-$jam[0];
                $sisamenit2=$sisamenit*60;
                $jml_jam=$jam[0];
                //end ambil selisih waktu
                
                $sUpdate="update ".$dbname.".it_request set jumlah='".$jml_jam."',waktuselesai='".$tlgjmskrng."',saranpelaksana='".$saran."'
                          where notransaksi='".$notransaksi."'";
                if(mysql_query($sUpdate))
                {
                    #send an email to incharge person
                    $to=getUserEmail($rGet['karyawanid']);
                    $namakaryawan=getNamaKaryawan($rGet['karyawanid']);
                    $subject="[Notifikasi]Permintaan layanan ".$rGet['keterangan']." a/n ".$namakaryawan;
                    $body="<html>
                             <head>
                             <body>
                               <dd>Dengan Hormat,</dd><br>
                               <p align=justify>
                               Permintaan layanan ".$rGet['keterangan']." pada tanggal ".tanggalnormal($rGet['tanggal'])." oleh saudara ke departemen IT
                               dengan deskripsi ".$rGet['deskripsi'].".
                               <br>
                               <br>
                               Telah selesai dilaksanakan pada tanggal ".tanggalnormald($tlgjmskrng).", mohon kesediaan saudara untuk memberi penilaian terhadap layanan kami dari menuIT->Permintaan Layanan
                               <br>
                               <br>
                               Regards,<br>
                               Owl-Plantation System.
                             </body>
                             </head>
                           </html>
                           ";
                    $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;
                }
                break;
                case'getDetail':
                    $sData="select distinct * from ".$dbname.".it_request where notransaksi='".$notransaksi."' ";
                    $qData=mysql_query($sData) or die(mysql_error($conn));
                    $rData=mysql_fetch_assoc($qData);
                    $dataTab.="<div style=overflow:auto;width:420px;height:880px;>";
                    $dataTab.="<fieldset><legend>".$_SESSION['lang']['desc']."</legend>";
                    $dataTab.="<div align=justify>".$rData['deskripsi']."</p>";
                    $dataTab.="</fieldset><br />";
                    $dataTab.="<fieldset><legend>".$_SESSION['lang']['saran']." ".$arrNmkary[$rData['karyawanid']]." [user] </legend>";
                    $dataTab.="<div align=justify>".$rData['saranuser']."</p>";
                    $dataTab.="</fieldset><br />";
                     $dataTab.="<fieldset><legend>".$_SESSION['lang']['saran']." ".$arrNmkary[$rData['pelaksana']]." [pelaksana] </legend>";
                    $dataTab.="<div align=justify>".$rData['saranpelaksana']."</p>";
                    $dataTab.="</fieldset>";
                    $dataTab.="</div>";
                    echo $dataTab;
                break;
		case'prevPdf':

                class PDF extends FPDF
{
	
	function Header()
	{
	    if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
	    $this->Image($path,15,2,40);	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetY(22);   
	    $this->Cell(60,5,$_SESSION['org']['namaorganisasi'],0,1,'C');	 
		$this->SetFont('Arial','',15);
	    $this->Cell(190,5,'',0,1,'C');
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

  $str="select * from ".$dbname.".sdm_ijin where ".$where."";	
  //exit("Error".$str);
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {

	  	$jabatan='';
		$namakaryawan='';
		$bagian='';	
		$karyawanid='';
		 $strc="select a.namakaryawan,a.karyawanid,a.bagian,b.namajabatan 
		    from ".$dbname.".datakaryawan a left join  ".$dbname.".sdm_5jabatan b
			on a.kodejabatan=b.kodejabatan
			where a.karyawanid=".$bar->karyawanid;
      $resc=mysql_query($strc);
	  while($barc=mysql_fetch_object($resc))
	  {
	  	$jabatan=$barc->namajabatan;
		$namakaryawan=$barc->namakaryawan;
		$bagian=$barc->bagian;
		$karyawanid=$barc->karyawanid;
	  }

	  //===============================	  

		$perstatus=$bar->stpersetujuan1;
                $tgl=tanggalnormal($bar->tanggal);
		$kperluan=$bar->keperluan;
                $persetujuan=$bar->persetujuan1;
                $jns=$bar->jenisijin;
                $jmDr=$bar->darijam;
                $jmSmp=$bar->sampaijam;
                $koments=$bar->komenst1;
                $ket=$bar->keterangan;
                $periode=$bar->periodecuti;
                $sthrd=$bar->stpersetujuanhrd;
                $hk=$bar->jumlahhari;
                $hrd=$bar->hrd;
                $koments2=$bar->komenst2;
	//ambil bagian,jabatan persetujuan atasan
		$perjabatan='';
		$perbagian='';
		$pernama='';
	$strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
	       ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
		   where karyawanid=".$persetujuan;	   
	$resf=mysql_query($strf);
	while($barf=mysql_fetch_object($resf))
	{
		$perjabatan=$barf->namajabatan;
		$perbagian=$barf->bagian;
		$pernama=$barf->namakaryawan;
	}
 	//ambil bagian,jabatan persetujuan hrd
		$perjabatanhrd='';
		$perbagianhrd='';
		$pernamahrd='';
	$strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
	       ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
		   where karyawanid=".$hrd;	   
	$resf=mysql_query($strf);
	while($barf=mysql_fetch_object($resf))
	{
		$perjabatanhrd=$barf->namajabatan;
		$perbagianhrd=$barf->bagian;
		$pernamahrd=$barf->namakaryawan;
	}       
  }

	$pdf=new PDF('P','mm','A4');
	$pdf->SetFont('Arial','B',14);
	$pdf->AddPage();
	$pdf->SetY(40);
	$pdf->SetX(20);
	$pdf->SetFillColor(255,255,255); 
        $pdf->Cell(175,5,strtoupper($_SESSION['lang']['ijin']."/".$_SESSION['lang']['cuti']),0,1,'C');
	$pdf->SetX(20);
	$pdf->SetFont('Arial','',8);
	//$pdf->Cell(175,5,'NO : '.$notransaksi,0,1,'C');	

	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();	
        $pdf->SetX(20);	
    	$pdf->Cell(30,5,$_SESSION['lang']['tanggal'],0,0,'L');	
		$pdf->Cell(50,5," : ".$tgl,0,1,'L');	
        $pdf->SetX(20);			
    	$pdf->Cell(30,5,$_SESSION['lang']['nokaryawan'],0,0,'L');	
		$pdf->Cell(50,5," : ".$karyawanid,0,1,'L');	
	$pdf->SetX(20);	
    	$pdf->Cell(30,5,$_SESSION['lang']['namakaryawan'],0,0,'L');	
		$pdf->Cell(50,5," : ".$namakaryawan,0,1,'L');	
	$pdf->SetX(20);	
    	$pdf->Cell(30,5,$_SESSION['lang']['bagian'],0,0,'L');	
		$pdf->Cell(50,5," : ".$bagian,0,1,'L');	
	$pdf->SetX(20);	
    	$pdf->Cell(30,5,$_SESSION['lang']['functionname'],0,0,'L');	
		$pdf->Cell(50,5," : ".$jabatan,0,1,'L');
        $pdf->SetX(20);	
    	$pdf->Cell(30,5,$_SESSION['lang']['keperluan'],0,0,'L');	
		$pdf->Cell(50,5," : ".$kperluan,0,1,'L');	
        $pdf->SetX(20);	
    	$pdf->Cell(30,5,$_SESSION['lang']['jenisijin'],0,0,'L');	
		$pdf->Cell(50,5," : ".$jns,0,1,'L');	
        $pdf->SetX(20);	
    	$pdf->Cell(30,5,$_SESSION['lang']['keterangan'],0,0,'L');	
		$pdf->Cell(50,5," : ".$ket,0,1,'L');	
         $pdf->SetX(20);	
    	$pdf->Cell(30,5,'Periode Cuti',0,0,'L');	
		$pdf->Cell(50,5," : ".$periode,0,1,'L');               
        $pdf->SetX(20);	
    	$pdf->Cell(30,5,$_SESSION['lang']['dari'],0,0,'L');	
		$pdf->Cell(50,5," : ".$jmDr,0,1,'L');	
        $pdf->SetX(20);	
    	$pdf->Cell(30,5,$_SESSION['lang']['tglcutisampai'],0,0,'L');	
		$pdf->Cell(50,5," : ".$jmSmp,0,1,'L');	
        $pdf->SetX(20);	
    	$pdf->Cell(30,5,"Jumlah hari",0,0,'L');	
		$pdf->Cell(50,5," : ".$hk." Hari kerja",0,1,'L');	
                
                
	
	
	$pdf->Ln();	
	$pdf->SetX(20);	
	$pdf->SetFont('Arial','B',8);		
	$pdf->Cell(172,5,strtoupper($_SESSION['lang']['approval_status']),0,1,'L');	
	$pdf->SetX(30);
		$pdf->Cell(30,5,strtoupper($_SESSION['lang']['bagian']),1,0,'C');
		$pdf->Cell(50,5,strtoupper($_SESSION['lang']['namakaryawan']),1,0,'C');			
		$pdf->Cell(40,5,strtoupper($_SESSION['lang']['functionname']),1,0,'C');
		$pdf->Cell(37,5,strtoupper($_SESSION['lang']['keputusan']),1,1,'C');	 			

	$pdf->SetFont('Arial','',8);
	
	$pdf->SetX(30);
		$pdf->Cell(30,5,$perbagian,1,0,'L');
		$pdf->Cell(50,5,$pernama,1,0,'L');			
		$pdf->Cell(40,5,$perjabatan,1,0,'L');
		$pdf->Cell(37,5,$arrKeputusan[$perstatus],1,1,'L');
	$pdf->SetX(30);
		$pdf->Cell(30,5,$perbagianhrd,1,0,'L');
		$pdf->Cell(50,5,$pernamahrd,1,0,'L');			
		$pdf->Cell(40,5,$perjabatanhrd,1,0,'L');
		$pdf->Cell(37,5,$arrKeputusan[$sthrd],1,1,'L');
                
    $pdf->Ln();               

        $pdf->SetX(20);                
    	$pdf->Cell(30,5,'Alasan(Atasan)',0,0,'L');	
		$pdf->Cell(50,5," : ".$koments,0,1,'L');	
                
        $pdf->SetX(20);	
    	$pdf->Cell(30,5,'Alasan(HRD)',0,0,'L');	
		$pdf->Cell(50,5," : ".$koments2,0,1,'L');


   $pdf->Ln();	
   $pdf->Ln();	
   $pdf->Ln();	
 	
					
//footer================================
    $pdf->Ln();		
	$pdf->Output();
	
		break;
		case'getExcel':
               $tab.=" 
                <table class=sortable cellspacing=1 border=1 width=80%>
                <thead>
                <tr  >
                <td align=center bgcolor='#DFDFDF'>No.</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['tanggal']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['nama']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['keperluan']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['jenisijin']."</td>  
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['persetujuan']."</td>    
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['approval_status']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['dari']."  ".$_SESSION['lang']['jam']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['tglcutisampai']."  ".$_SESSION['lang']['jam']."</td>
                </tr>  
                </thead><tbody>";
                $slvhc="select * from ".$dbname.".sdm_ijin   order by `tanggal` desc ";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
                $no+=1;

                 $tab.="
                <tr class=rowcontent>
                <td>".$no."</td>
                <td>".$rlvhc['tanggal']."</td>
                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>
                <td>".$rlvhc['keperluan']."</td>
                <td>".$rlvhc['jenisijin']."</td>
                <td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>
                <td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>
                <td>".$rlvhc['darijam']."</td>
                <td>".$rlvhc['sampaijam']."</td>";
                }
                $tab.="</tbody></table>";
                $nop_="listizinkeluarkantor";
if(strlen($tab)>0)
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
 if(!fwrite($handle,$tab))
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
		
		default:
		break;
	}


?>