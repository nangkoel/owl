<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
require_once('lib/fpdf.php');
 
$param = $_POST;
$proses = $_POST['proses'];
$optNmdept=makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$optNmpend=makeOption($dbname, 'sdm_5pendidikan', 'idpendidikan,kelompok');
$optNmorg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNm=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
if($proses==''){
    $proses=$_GET['proses'];
}
$arrstat=array('0'=>$_SESSION['lang']['wait_approval '],"1"=>$_SESSION['lang']['disetujui']);
function daysBetween($s, $e)
    {
            $s = strtotime($s);
            $e = strtotime($e);

            return ($e - $s)/ (24 *3600);
    }
function selisihHari($tglAwal, $tglAkhir)
{
    // list tanggal merah selain hari minggu
    // memecah string tanggal awal untuk mendapatkan
    // tanggal, bulan, tahun
    $pecah1 = explode("-",$tglAwal);
    $date1 = $pecah1[2];
    $month1 = $pecah1[1];
    $year1 = $pecah1[0];

    // memecah string tanggal akhir untuk mendapatkan
    // tanggal, bulan, tahun
    $pecah2 = explode("-",$tglAkhir);
    $date2 = $pecah2[2];
    $month2 = $pecah2[1];
    $year2 =  $pecah2[0];
    //exit("error:".$tglAwal."___".$tglAkhir);
    // mencari selisih hari dari tanggal awal dan akhir
    $jd1 = GregorianToJD($month1, $date1, $year1);
    $jd2 = GregorianToJD($month2, $date2, $year2);

    $selisih = $jd2 - $jd1;

    // menghitung selisih hari yang bukan tanggal merah dan hari minggu
    return $selisih;
}
$tglHrini=date("Y-m-d");

switch($proses) {
    # Daftar Header
    case 'loadData':
	echo"<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>";
        echo"<td>".$_SESSION['lang']['namalowongan']."</td>";
        echo"<td>".$_SESSION['lang']['unit']." Peminta</td>";
        echo"<td>".$_SESSION['lang']['unit']." ".$_SESSION['lang']['penempatan']."</td>";
        echo"<td>".$_SESSION['lang']['tanggal']."</td>";
        echo"<td>".$_SESSION['lang']['tgldibutuhkan']."</td>";       
        echo"<td>".$_SESSION['lang']['kotapenempatan']."</td>";
        echo"<td>".$_SESSION['lang']['pendidikan']."</td>";
        echo"<td>".$_SESSION['lang']['jurusan']."</td>";
        echo"<td colspan=2>".$_SESSION['lang']['action']."</td>";
        echo"</tr></thead><tbody>";
        $limit=3;
        $page=0;
        if(isset($_POST['page']))
        {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
        }
        if($_POST['page2']!=''){
         $page=$_POST['page2']-1;   
        }
        if($param['tahun']!=''){
            $whr="where tanggal like '".$param['tahun']."%'";
        }
        $offset=$page*$limit;
        $sdata="select distinct * from ".$dbname.".sdm_permintaansdm   ".$whr."
                order by tanggal desc limit ".$offset.",".$limit." ";
        //echo $sdata;
        $qdata=mysql_query($sdata,$conn) or die(mysql_error($conn));
        $rowdata=mysql_num_rows($qdata);
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
        while($rdata=  mysql_fetch_assoc($qdata)){
            $sdt="select tglakhirdisplay from ".$dbdt.".sdm_lowongan where nopermintaan='".$rdata['notransaksi']."'";
            $qdt=mysql_query($sdt,$conn2) or die(mysql_error());
            $rdt=mysql_fetch_assoc($qdt);
            if($rdt['tglakhirdisplay']==''){
                $rdt['tglakhirdisplay']=$rdata['tgldibutuhkan'];
            }
            
            $slisihhari=selisihHari($tglHrini,$rdt['tglakhirdisplay']);
            echo"<tr class=rowcontent>";
            echo"<td>".$rdata['namalowongan']."</td>";
            echo"<td>".$rdata['kodeorg']."</td>";
            echo"<td>".$rdata['penempatan']."</td>";
            echo"<td>".tanggalnormal($rdata['tanggal'])."</td>";
            echo"<td>".tanggalnormal($rdata['tgldibutuhkan'])."</td>";
            echo"<td>".$rdata['kotapenempatan']."</td>";
            echo"<td>".$optNmpend[$rdata['pendidikan']]."</td>";
            echo"<td>".$rdata['jurusan']."</td>";
            if($slisihhari>0){
                if(($rdata['persetujuan1']==$_SESSION['standard']['userid'])||($rdata['persetujuan2']==$_SESSION['standard']['userid'])){
                    echo"<td colspan=2><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;";
                    if(($rdata['stpersetujuan1']==0)&&($rdata['persetujuan1']==$_SESSION['standard']['userid'])){
                        echo"<button onclick=\"procDt('".$rdata['notransaksi']."','1')\" class=\"mybutton\" >".$_SESSION['lang']['konfirm']."</button>";
                    }
                    if(($rdata['stpersetujuan2']==0)&&($rdata['persetujuan2']==$_SESSION['standard']['userid'])){
                        echo"<button onclick=\"procDt('".$rdata['notransaksi']."','2')\" class=\"mybutton\" >".$_SESSION['lang']['konfirm']."</button>";

                    }
                    echo"</td>";
                } else if($rdata['persetujuanhrd']==$_SESSION['standard']['userid']){
                    if(($rdata['stpersetujuanhrd']==0)&&($rdata['persetujuanhrd']==$_SESSION['standard']['userid'])){
                        echo"<td><input type=text class='myinputtext'  onmousemove=setCalendar(this.id) onkeypress=return false;   style='width:150px;' id='tglsmp_".$rdata['notransaksi']."' /></td>";
                        echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;<button onclick=\"procDt2('".$rdata['notransaksi']."')\" class=\"mybutton\" >".$_SESSION['lang']['konfirm']."</button></td>";
                    }else{
                        echo"<td colspan=2><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\"></td>";
                    }

                }else{
                    echo"<td colspan=2><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;</td>";
                }
            }else{
                if(($rdata['persetujuanhrd']==$_SESSION['standard']['userid'])){
                        echo"<td><input type=text class='myinputtext'  onmousemove=setCalendar(this.id) onkeypress=return false;   style='width:150px;' id='tglsmp_".$rdata['notransaksi']."' /></td>";
                        echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;<button onclick=\"procDt2('".$rdata['notransaksi']."')\" class=\"mybutton\" >".$_SESSION['lang']['renew']."</button></td>";
                    }else{
                        echo"<td colspan=2><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\"></td>";
                    }
            }
            echo"</tr>";
        }
        echo"</tbody><tfoot>";
        echo"<tr>";
        echo"<td colspan=10 align=center>";
        echo"<img src=\"images/skyblue/first.png\" onclick='loadData(0)' style='cursor:pointer'>";
        echo"<img src=\"images/skyblue/prev.png\" onclick='loadData(".($page-1).")'  style='cursor:pointer'>";
        
        $spage="select distinct * from ".$dbname.".sdm_permintaansdm  ".$whr." order by tanggal desc";
        //echo $spage;
        $qpage=mysql_query($spage) or die(mysql_error($conn));
        $rpage=mysql_num_rows($qpage);
        echo"<input type=text />";
        $dert.="<select id='pages' style='width:50px' onchange='loadData(1.1)'>";
        @$totalPage=ceil($rpage/10);
        for($starAwal=1;$starAwal<=$totalPage;$starAwal++)
        {
            $_POST['page']=='1.1'?$_POST['page']=$_POST['page2']:$_POST['page']=$_POST['page'];
            $dert.="<option value='".$starAwal."' ".($starAwal==$_POST['page']?'selected':'').">".$starAwal."</option>";
        }
        $dert.="</select>";
        echo $dert;
        echo"<img src=\"images/skyblue/next.png\" onclick='loadData(".($page+1).")'  style='cursor:pointer'>";
        echo"<img src=\"images/skyblue/last.png\" onclick='loadData(".intval($totalPage).")'  style='cursor:pointer'>";
        echo"</td></tr></tfoot>";
        
        echo"</table>";
	
	break;
        
   case'update':

        $saks="select distinct * from ".$dbname.".setup_remotetimbangan 
               where lokasi='HRDJKRT'";		
        //exit("error:".$saks);
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
          
   $scek2="select distinct `persetujuan".$param['urut']."`,`stpersetujuan".$param['urut']."`,tgldibutuhkan from ".$dbname.".sdm_permintaansdm 
           where notransaksi='".$param['notransaksi']."'";
   //exit("error:".$scek2);
   $qcek2=mysql_query($scek2,$conn) or die(mysql_error($conn));
   $rcek2=mysql_fetch_assoc($qcek2);
    $sdt="select tglakhirdisplay from ".$dbdt.".sdm_lowongan where nopermintaan='".$param['notransaksi']."'";
    //exit("error: ".$sdt);
    $qdt=mysql_query($sdt,$conn2) or die(mysql_error());
    $rdt=mysql_fetch_assoc($qdt);
    $qdata=mysql_query($sdata,$conn) or die(mysql_error($conn));
    $rowdata=mysql_num_rows($qdata);
        
          if($rdt['tglakhirdisplay']==''){
              $rdt['tglakhirdisplay']=$rcek2['tgldibutuhkan'];
          }
      $slisihhari=selisihHari($tglHrini,$rdt['tglakhirdisplay']);
//      exit("error:".$slisihhari);
      if($slisihhari>0){
           if($rcek2['stpersetujuan'.$param['urut']]!=0){
               exit("error: Data Sudah Ada Perrsetujuan");
           }
      }
       
       $sins="update ".$dbname.".sdm_permintaansdm  set `stpersetujuan".$param['urut']."`='1' 
           where notransaksi='".$param['notransaksi']."' and `persetujuan".$param['urut']."`='".$rcek2['persetujuan'.$param['urut']]."'";
            if(!mysql_query($sins,$conn)){
            exit("error:".mysql_error($conn)."__".$sins);
            }
     
   break;
   case'updateDt':
   $scek2="select distinct * from ".$dbname.".sdm_permintaansdm 
           where notransaksi='".$param['notransaksi']."'";
   //exit("error:".$scek2);
   $qcek2=mysql_query($scek2) or die(mysql_error($conn));
   $rcek2=mysql_fetch_assoc($qcek2);
   if($rcek2['stpersetujuanhrd']!=0){
       exit("error: Data Sudah Ada Perrsetujuan");
   }
  
    $tgl=explode("-",$param['tglTakhir']);
    $param['tglTakhir']=$tgl[2]."-".$tgl[1]."-".$tgl[0];
    
//     exit("error:".$param['tglTakhir']);
    $slisihhari=daysBetween($rcek2['tgldibutuhkan'],$param['tglTakhir']);
//    exit("error:".$slisihhari);
    if($slisihhari<0){
       exit("Error: Tanggal Display Lowongan Tidak Boleh Lebih Kecil dari Tanggal Di butuhkan");
    }
    #koneksi dtbase
    $sdtip="select distinct * from ".$dbname.".setup_remotetimbangan where lokasi='HRDJKRT'";
    $qdtip=mysql_query($sdtip) or die(mysql_error($conn));
    $rdtip=mysql_fetch_assoc($qdtip);
                $dbserver2=$rdtip['ip'];
                $dbport2  =$rdtip['port'];
                $dbname2  =$rdtip['dbname'];
                $uname2   =$rdtip['username'];
                $passwd2  =$rdtip['password'];
               //print_r($rdtip);
                $conn2=mysql_connect($dbserver2.":".$dbport2,$uname2,$passwd2) or die("Error/Gagal :Unable to Connect to database ".$dbserver2);
                $sed="select distinct * from ".$dbname2.".`sdm_lowongan` where nopermintaan='".$rcek2['notransaksi']."'";
                $qed=mysql_query($sed,$conn2) or die(mysql_error($conn2));
                $red=mysql_num_rows($qed);
                if($red==0){
                    $sinsert="insert into ".$dbname2.".`sdm_lowongan` 
                              (`notransaksi`,`namalowongan`, `nopermintaan`,`departemen`, `tanggal`, `tgldibutuhkan`, `kotapenempatan`, `pendidikan`, `jurusan`, `pengalaman`, `kompetensi`, `deskpekerjaan`, `maxumur`, `tglakhirdisplay`, `updateby`)
                              values
                              (NULL,'".$rcek2['namalowongan']."','".$rcek2['notransaksi']."','".$rcek2['departemen']."','".$rcek2['tanggal']."','".$rcek2['tgldibutuhkan']."','".$rcek2['kotapenempatan']."','".$rcek2['pendidikan']."','".$rcek2['jurusan']."','".$rcek2['pengalaman']."','".$rcek2['kompetensi']."','".$rcek2['deskpekerjaan']."','".$rcek2['maxumur']."','".$param['tglTakhir']."','".$_SESSION['standard']['userid']."')";
                    if(!mysql_query($sinsert, $conn2)){
                        exit("error:".$sinsert."__".mysql_error($conn2));
                    }else{
                        $sins="update ".$dbname.".sdm_permintaansdm  set `stpersetujuanhrd`='1' 
                        where notransaksi='".$param['notransaksi']."' ";
                        if(!mysql_query($sins,$conn)){
                        exit("error: ".$sins."___".mysql_error($conn));
                        }
                    }
                }else{
                    $sinsert="update ".$dbname2.".`sdm_lowongan` set `namalowongan`='".$rcek2['namalowongan']."',`tanggal`='".$rcek2['tanggal']."', 
                             `tgldibutuhkan`='".$rcek2['tgldibutuhkan']."', `kotapenempatan`='".$rcek2['kotapenempatan']."',
                             `pendidikan`='".$rcek2['pendidikan']."', `jurusan`='".$rcek2['jurusan']."', `pengalaman`='".$rcek2['pengalaman']."', 
                             `kompetensi`='".$rcek2['kompetensi']."', `deskpekerjaan`='".$rcek2['deskpekerjaan']."', `maxumur`='".$rcek2['maxumur']."', 
                             `tglakhirdisplay`='".$param['tglTakhir']."', `updateby`='".$_SESSION['standard']['userid']."' where `nopermintaan`='".$rcek2['notransaksi']."',";
                    if(!mysql_query($sinsert, $conn2)){
                        exit("error:".$sinsert."__".mysql_error($conn2));
                    }
                }
            
   break;
  
   case'pdfDt':
  
class PDF extends FPDF
{
	
	function Header()
	{
 	global $conn;
	global $dbname;
        global $userid;
	global $notransaksi;
	global $kodevhc;
	global $posting;
        global $bar;
        global $arrstat;
	
			
			$kodevhc=$test[1];
			
			 	
			//ambil nama pt
			   $str1="select * from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."' and tipe='PT'"; 
			   $res1=mysql_query($str1);
			   while($bar1=mysql_fetch_object($res1))
			   {
			   	 $namapt=$bar1->namaorganisasi;
				 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
				 $telp=$bar1->telepon;				 
			   }    
	  
	
		if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,15,5,40);	
                $this->SetFont('Arial','B',10);
                $this->SetFillColor(255,255,255);	
                $this->SetX(55);   
                $this->Cell(60,5,$namapt,0,1,'L');	 
                $this->SetX(55); 		
                $this->Cell(60,5,$alamatpt,0,1,'L');	
                $this->SetX(55); 			
                $this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->SetY(35);
                $this->Cell(190,5,"Detail Permintaan Tenaga Kerja",0,1,'C');		
                $this->SetFont('Arial','',6); 
                $this->SetY(27);
                $this->SetX(163);
                $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
                $this->Line(10,27,200,27);	
                $this->Ln();
	
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
        $test=explode(',',$_GET['column']);
        $notransaksi=$test[0];
	$str="select * from ".$dbname.".".$_GET['table']."  where notransaksi='".$notransaksi."' ";
        $qstr=mysql_query($str) or die(mysql_error($conn));
        $bar=mysql_fetch_assoc($qstr);
        $pdf->SetFont('Arial','',9); 
        $pdf->Cell(30,7,$_SESSION['lang']['unit']." Peminta",0,0,'L');
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,$optNmorg[$bar['kodeorg']]." [".$bar['kodeorg']."]",0,1,'L'); 
        $pdf->Cell(30,7,$_SESSION['lang']['unit']." ".$_SESSION['lang']['penempatan'],0,0,'L');
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,$optNmorg[$bar['penempatan']]." [".$bar['penempatan']."]",0,1,'L');
        $pdf->Cell(30,7,$_SESSION['lang']['departemen'],0,0,'L');
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,$optNmdept[$bar['departemen']] ,0,1,'L');
        
        $pdf->Cell(30,7,$_SESSION['lang']['tanggal'],0,0,'L'); 
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,tanggalnormal($bar['tanggal']),0,1,'L'); 
        $pdf->Cell(30,7,$_SESSION['lang']['tgldibutuhkan'],0,0,'L'); 
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,tanggalnormal($bar['tgldibutuhkan']),0,1,'L'); 
        $pdf->Cell(30,7,$_SESSION['lang']['kotapenempatan'],0,0,'L'); 
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,$bar['kotapenempatan'],0,1,'L'); 				
        $pdf->Cell(30,7,$_SESSION['lang']['pendidikan'],0,0,'L');
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,$optNmpend[$bar['pendidikan']] ,0,1,'L');
        $pdf->Cell(30,7,$_SESSION['lang']['jurusan'],0,0,'L'); 
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,$bar['jurusan'],0,1,'L'); 
        $pdf->Cell(30,7,$_SESSION['lang']['maxumur'],0,0,'L'); 
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,$bar['maxumur'],0,1,'L');
        $pdf->Cell(30,7,"Jumlah Kebutuhan",0,0,'L'); 
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,$bar['jumlah_kebutuhan']." Orang",0,1,'L');
        $pdf->Cell(30,7,$_SESSION['lang']['pengalamankerja'],0,0,'L'); 
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->Cell(40,7,$bar['pengalaman'],0,1,'L');
        $pdf->Cell(30,7,$_SESSION['lang']['kompetensi'],0,0,'L'); 
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->MultiCell(155,7,$bar['kompetensi'],'J');
        $pdf->Cell(30,7,$_SESSION['lang']['deskpekerjaan'],0,0,'L'); 
        $pdf->Cell(3,7," : ",0,0,'L'); 
        $pdf->MultiCell(155,7,$bar['deskpekerjaan'],'J');
        $pdf->Ln(8);
        $pdf->SetFillColor(220,220,220);
        $pdf->Cell(45,7,$_SESSION['lang']['persetujuan']." 1",1,0,'L',1);	
        $pdf->Cell(45,7,$_SESSION['lang']['persetujuan']." 2",1,0,'L',1);	
        $pdf->Cell(45,7,$_SESSION['lang']['persetujuan']." HRD",1,1,'L',1);	
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(45,7,$optNm[$bar['persetujuan1']],1,0,'L',1);	
        $pdf->Cell(45,7,$optNm[$bar['persetujuan2']],1,0,'L',1);	
        $pdf->Cell(45,7,$optNm[$bar['persetujuanhrd']],1,1,'L',1);
	$pdf->Cell(45,7,$arrstat[$bar['stpersetujuan1']],1,0,'L',1);	
        $pdf->Cell(45,7,$arrstat[$bar['stpersetujuan2']],1,0,'L',1);	
        $pdf->Cell(45,7,$arrstat[$bar['stpersetujuanhrd']],1,1,'L',1);		
//footer================================
 
	
	$pdf->Output();
   break;
    
}
?>