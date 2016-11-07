<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');

 $mid= $_POST['mid'];      
 $unit= $_POST['unit'];      
 $pemilik= $_POST['pemilik'];      
 $lokasi= $_POST['lokasi'];      
 $luas= $_POST['luas'];      
 $bisaditanam= $_POST['bisaditanam'];      
 $blok= $_POST['blok'];      
 $batastimur= $_POST['batastimur'];      
 $batasbarat= $_POST['batasbarat'];      
 $batasutara=$_POST['batasutara'];      
 $batasselatan= $_POST['batasselatan'];   
 
 $rptanaman= $_POST['rptanaman']==''?0:$_POST['rptanaman'];      
 $rptanah= $_POST['rptanah']==''?0:$_POST['rptanah'];        
 $biayakades= $_POST['biayakades']==''?0:$_POST['biayakades'];  
 $biayacamat= $_POST['biayacamat']==''?0:$_POST['biayacamat'];    
 $biayamatrai= $_POST['biayamatrai']==''?0:$_POST['biayamatrai'];  
 
 $statuspermintaandana= $_POST['statuspermintaandana'];      
 $statuspermbayaran= $_POST['statuspermbayaran'];      
 $statuskades= $_POST['statuskades'];      
 $statuscamat= $_POST['statuscamat'];      
 $nosurat=$_POST['nosurat'];         
 $keterangan= $_POST['keterangan'];      
 if($_POST['tanggalpermintaan']=='')
      $_POST['tanggalpermintaan']='00-00-0000';
 if($_POST['tanggalbayar']=='')
      $_POST['tanggalbayar']='00-00-0000';
 if($_POST['tanggalkades']=='')
      $_POST['tanggalkades']='00-00-0000';
 if($_POST['tanggalcamat']=='')
      $_POST['tanggalcamat']='00-00-0000';
 
 $tanggalpermintaan= tanggalsystem($_POST['tanggalpermintaan']);      
 $tanggalbayar= tanggalsystem($_POST['tanggalbayar']);      
 $tanggalkades= tanggalsystem($_POST['tanggalkades']);      
 $tanggalcamat= tanggalsystem($_POST['tanggalcamat']);      
  
$method=$_POST['method'];
if($method==''){
    $method=$_GET['method'];
    $idlahan=$_GET['idlahan'];    
    $pemilik=$_GET['pemilik'];    
}
switch($method)
{
case 'pdf':

        $str1="select padid, nama from ".$dbname.".pad_5masyarakat"; 
        $res1=mysql_query($str1);
        while($bar1=mysql_fetch_object($res1))
        {
            $kamuspemilik[$bar1->padid]=$bar1->nama;
        }
            
    class PDF extends FPDF {
        function Header() {
            global $idlahan;
            global $pemilik;
            global $kamuspemilik;
            $this->SetFont('Arial','B',9); 
            $this->Cell(20,3,$namapt,'',1,'L');
            $this->SetFont('Arial','B',12);
            $this->Cell(190,3,strtoupper($_SESSION['lang']['pembebasan']." ".$_SESSION['lang']['lahan']),0,1,'C');
            $this->SetFont('Arial','',7);
            $this->Cell(150,3,' ','',0,'R');
            $this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
            $this->Cell(2,3,':','',0,'L');
            $this->Cell(35,3,date('d-m-Y H:i'),0,1,'L');
            
            $this->Cell(28,3,$_SESSION['lang']['id'],'',0,'L');
            $this->Cell(2,3,':','',0,'L');
            $this->Cell(120,3,$idlahan,0,0,'L');
            
            $this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
            $this->Cell(2,3,':','',0,'L');
            $this->Cell(35,3,$this->PageNo(),'',1,'L');
            
            $this->Cell(28,3,$_SESSION['lang']['nama']." ".$_SESSION['lang']['namapemilik']." ".$_SESSION['lang']['lahan'],'',0,'L');
            $this->Cell(2,3,':','',0,'L');
            $this->Cell(120,3,$kamuspemilik[$pemilik],0,0,'L');
            
            $this->Cell(15,3,'User','',0,'L');
            $this->Cell(2,3,':','',0,'L');
            $this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
            $this->Ln();
  					
            $this->Ln();						
        }
    }
    //================================
    $pdf=new PDF('P','mm','A4');
    $pdf->AddPage();    
    
        $str1="select a.*,b.nama,b.alamat,b.desa,c.namakaryawan from ".$dbname.".pad_lahan a
            left join ".$dbname.".pad_5masyarakat b on a.pemilik=b.padid 
            left join ".$dbname.".datakaryawan c on a.updateby=c.karyawanid    
            where idlahan = '".$idlahan."'"; 
        $res1=mysql_query($str1);
        while($bar1=mysql_fetch_object($res1))
        {
         $stdana=$bar1->statuspermintaandana==1?tanggalnormal($bar1->tanggalpengajuan):"";
         
         if($bar1->statuspermbayaran==1){
                 $stbayar=tanggalnormal($bar1->tanggalbayar)." Belum Lunas";
         }else if($bar1->statuspermbayaran==0){
                 $stbayar='Belum Bayar';
         }else if($bar1->statuspermbayaran==2){
                  $stbayar=tanggalnormal($bar1->tanggalbayar)." Lunas";
         }
         $stkades=$bar1->statuskades==1?tanggalnormal($bar1->tanggalkades):"";
         $stcamat=$bar1->statuscamat==1?tanggalnormal($bar1->tanggalcamat):"";            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(100,5,"1.".$_SESSION['lang']['namapemilik']." ".$_SESSION['lang']['lahan'],0,0,'L');
            $pdf->Cell(100,5,"2.".$_SESSION['lang']['biaya']."-".$_SESSION['lang']['biaya']." dan ".$_SESSION['lang']['status']."-".$_SESSION['lang']['dokumen'],0,0,'L');
            $pdf->Ln();						
            
            $pdf->SetFont('Arial','',7);
            $pdf->Cell(35,5,$_SESSION['lang']['id'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(63,5,$bar1->idlahan,0,0,'L');
            $pdf->Cell(35,5,$_SESSION['lang']['biaya']." ".$_SESSION['lang']['tanamtumbuh'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(33,5,number_format($bar1->rptanaman,0),0,0,'R');
            $pdf->Ln();						
            $pdf->Cell(35,5,$_SESSION['lang']['kebun'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(63,5,$bar1->unit,0,0,'L');
            $pdf->Cell(35,5,$_SESSION['lang']['biaya']." ".$_SESSION['lang']['gantilahan'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(33,5,number_format($bar1->rptanah,0),0,0,'R');
            $pdf->Ln();						
            $pdf->Cell(35,5,$_SESSION['lang']['nama']." ".$_SESSION['lang']['namapemilik']." ".$_SESSION['lang']['lahan'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(63,5,$bar1->nama,0,0,'L');
            $pdf->Cell(35,5,$_SESSION['lang']['biaya']." ".$_SESSION['lang']['kepala']." ".$_SESSION['lang']['desa'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(33,5,number_format($bar1->biayakades,0),0,0,'R');
            $pdf->Ln();						
            $pdf->Cell(35,5,$_SESSION['lang']['keterangan']." ".$_SESSION['lang']['lokasi']."(No.Persil)",0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(63,5,$bar1->lokasi,0,0,'L');
            $pdf->Cell(35,5,$_SESSION['lang']['biaya']." ".$_SESSION['lang']['camat'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(33,5,number_format($bar1->biayacamat,0),0,0,'R');
            $pdf->Ln();						
            $pdf->Cell(35,5,$_SESSION['lang']['luas'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(23,5,$bar1->luas.' Ha.',0,0,'R'); $pdf->Cell(40,5,'',0,0,'R');
            $pdf->Cell(35,5,$_SESSION['lang']['biaya']." Matrai",0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(33,5,number_format($bar1->biayamatrai,0),0,0,'R');
            $pdf->Ln();						
            $pdf->Cell(35,5,$_SESSION['lang']['luas']." ".$_SESSION['lang']['bisaditanam'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(23,5,$bar1->luasdapatditanam.' Ha.',0,0,'R'); $pdf->Cell(40,5,'',0,0,'R');
            $pdf->Cell(35,5,$_SESSION['lang']['status']." ".$_SESSION['lang']['permintaandana'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(6333,5,$stdana,0,0,'L');
            $pdf->Ln();						
            $pdf->Cell(35,5,$_SESSION['lang']['lokasi']." ".$_SESSION['lang']['kodeblok'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(63,5,$bar1->kodeblok,0,0,'L');
            $pdf->Cell(35,5,$_SESSION['lang']['status']." ".$_SESSION['lang']['pembayaran'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(33,5,$stbayar,0,0,'L');
            $pdf->Ln();						
            $pdf->Cell(35,5,$_SESSION['lang']['batastimur'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(63,5,$bar1->batastimur,0,0,'L');
            $pdf->Cell(35,5,$_SESSION['lang']['status']." ".$_SESSION['lang']['kepala']." ".$_SESSION['lang']['desa'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(33,5,$stkades,0,0,'L');
            $pdf->Ln();						
            $pdf->Cell(35,5,$_SESSION['lang']['batasbarat'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(63,5,$bar1->batasbarat,0,0,'L');
            $pdf->Cell(35,5,$_SESSION['lang']['status']." ".$_SESSION['lang']['camat'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(33,5,$stcamat,0,0,'L');
            $pdf->Ln();						
            $pdf->Cell(35,5,$_SESSION['lang']['batasutara'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(63,5,$bar1->batasutara,0,0,'L');
            $pdf->Cell(35,5,$_SESSION['lang']['nomor']." ".$_SESSION['lang']['dokumen'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(33,5,$bar1->nosurat,0,0,'L');
            $pdf->Ln();						
            $pdf->Cell(35,5,$_SESSION['lang']['batasselatan'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(63,5,$bar1->batasselatan,0,0,'L');
            $pdf->Cell(35,5,$_SESSION['lang']['keterangan'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(33,5,$bar1->keterangan,0,0,'L');
            $pdf->Ln();						
            $pdf->Ln();						
        }
        $str1="select * from ".$dbname.".pad_photo
            where idlahan = '".$idlahan."'"; 
        $res1=mysql_query($str1);
        while($bar1=mysql_fetch_object($res1))
        {
            $pdf->Cell(13,5,$_SESSION['lang']['photo'],0,0,'L'); $pdf->Cell(2,5,":",0,0,'L'); 
            $pdf->Cell(73,5,$bar1->filename,0,0,'L');
            $pdf->Ln();		
            $yey=$pdf->GetY();
            $path='filepad/'.$bar1->filename;
            $pdf->Image($path,25,$yey,70);
            $pdf->SetY($yey+80);
            $pdf->Ln();		
        }    
    $pdf->Output();	
    
    exit;
break;    
case 'update':	
          $str="update ".$dbname.".pad_lahan 
         set pemilik=".$pemilik.", 
         unit='".$unit."', 
         lokasi='".$lokasi."', 
         luas=".$luas.", 
         luasdapatditanam=".$bisaditanam.", 
         rptanaman=".$rptanaman.", 
         rptanah=".$rptanah.", 
         totalgantirugi=".($rptanaman+$rptanah).", 
         statuspermintaandana=".$statuspermintaandana.", 
         statuspermbayaran=".$statuspermbayaran.", 
         kodeblok='".$blok."', 
         statuskades=".$statuskades.", 
         statuscamat=".$statuscamat.", 
         tanggalpengajuan=".$tanggalpermintaan.", 
         tanggalbayar=".$tanggalbayar.", 
         tanggalkades=".$tanggalkades.", 
         tanggalcamat=".$tanggalcamat.", 
         updateby=".$_SESSION['standard']['userid'].", 
         biayakades=".$biayakades.", 
         biayacamat=".$biayacamat.", 
         biayamatrai=".$biayamatrai.", 
         keterangan='".$keterangan."', 
         nosurat='".$nosurat."', 
         batastimur='".$batastimur."', 
         batasbarat='".$batasbarat."', 
         batasutara='".$batasutara."', 
         batasselatan='".$batasselatan."'
        where idlahan=".$mid;
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn));exit();}
        break;
case 'insert':
        $str="insert into ".$dbname.".pad_lahan (
              pemilik, 
              unit, 
              lokasi, 
              luas, 
              luasdapatditanam, 
              rptanaman, 
              rptanah, 
              totalgantirugi, 
              statuspermintaandana, 
              statuspermbayaran, 
              kodeblok, 
              statuskades, 
              statuscamat, 
              tanggalpengajuan, 
              tanggalbayar, 
              tanggalkades, 
              tanggalcamat, 
              updateby,  
              biayakades, 
              biayacamat, 
              biayamatrai, 
              keterangan, 
              nosurat, 
              batastimur, 
              batasbarat, 
              batasutara, 
              batasselatan)
              values(
              ".$pemilik.",
              '".$unit."',  
              '".$lokasi."',
              ".$luas.",
              ".$bisaditanam.",
              ".$rptanaman.", 
              ".$rptanah.", 
              ".($rptanaman+$rptanah).",
              ".$statuspermintaandana.", 
              ".$statuspermbayaran.",   
              '".$blok."', 
              ".$statuskades.",    
              ".$statuscamat.",
              ".$tanggalpermintaan.",
              ".$tanggalbayar.",
              ".$tanggalkades.",
              ".$tanggalcamat.",
              ".$_SESSION['standard']['userid'].",
              ".$biayakades.",
              ".$biayacamat.", 
              ".$biayamatrai.",
              '".$keterangan."', 
              '".$nosurat."', 
             '".$batastimur."',
             '".$batasbarat."', 
             '".$batasutara."', 
             '".$batasselatan."'    
              )";
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn)).$str;exit();}	
        break;
case 'delete':
        $str="delete from ".$dbname.".pad_lahan
        where idlahan='".$mid."'";
        if(mysql_query($str))
        {}
        else
        {echo " Gagal,".addslashes(mysql_error($conn));exit();}
        break;
case 'getPemilik':
        $str="select padid,nama,desa from ".$dbname.".pad_5masyarakat 
        where desa in (select namadesa from ".$dbname.".pad_5desa where unit='".$_POST['unit']."')";
        $optpemilik='';
        if($res=mysql_query($str))
        {
            while($bar=mysql_fetch_object($res))
            {
                $optpemilik.="<option value='".$bar->padid."'>".$bar->nama."-".$bar->desa."</option>";
            }
            if($optpemilik!=''){
               echo $optpemilik;
            }
            else
            {
                echo "Error: Masyarakat pemilik belum ada, silahkan daftar dari menu setup";
            }
            exit();//jangan dihapus exit ini
        }
        else
        {echo " Gagal,".addslashes(mysql_error($conn));exit();}
        break;     
case 'getBlok':
        $str="select kodeorganisasi,namaorganisasi  from ".$dbname.".organisasi 
        where tipe='BLOK' and kodeorganisasi like '".$_POST['unit']."%'";
        $optblok="<option value=''>Undefined</option>";
        if($res=mysql_query($str))
        {
            while($bar=mysql_fetch_object($res))
            {
                $optblok.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
            }
           echo $optblok;
            exit();//jangan dihapus exit ini
        }
        else
        {echo " Gagal,".addslashes(mysql_error($conn));exit();}
        break;     
case 'posting':
        $str="update ".$dbname.".pad_lahan set posting=1 where idlahan=".$mid;
        if($res=mysql_query($str))
        {}
        else{
            echo " Gagal,".addslashes(mysql_error($conn));exit();            
            }   
default:
   break;					
}

//==============Free
        $str1="select a.*,b.nama,b.alamat,b.desa,c.namakaryawan from ".$dbname.".pad_lahan a
            left join ".$dbname.".pad_5masyarakat b on a.pemilik=b.padid 
            left join ".$dbname.".datakaryawan c on a.updateby=c.karyawanid    
            where posting=0 and unit='".$unit."' order by b.nama,b.desa limit 500"; 
if($res1=mysql_query($str1))
{
 echo"<table class=sortable cellspacing=1 border=0 width=2500px>
         <thead>
                <tr class=rowheader>
               <td style='width:30px;' rowspan=2>*</td>                
                <td rowspan=2>".$_SESSION['lang']['id']."</td>
                <td rowspan=2>".$_SESSION['lang']['unit']."</td>                     
                <td rowspan=2>".$_SESSION['lang']['pemilik']."</td>
                <td rowspan=2>".$_SESSION['lang']['lokasi']."/(No.Persil)</td>                       
                <td rowspan=2>".$_SESSION['lang']['desa']."</td>               
                <td rowspan=2>".$_SESSION['lang']['luas']."</td>    
                <td rowspan=2>".$_SESSION['lang']['bisaditanam']."</td> 
                <td rowspan=2>".$_SESSION['lang']['blok']."</td>    
                <td colspan=4 align=center>".$_SESSION['lang']['batas']."</td> 
                <td colspan=7 align=center>".$_SESSION['lang']['biaya']."-".$_SESSION['lang']['biaya']."</td>  
                <td colspan=4 align=center>".$_SESSION['lang']['status']."</td>    
                <td rowspan=2>".$_SESSION['lang']['nomor']." ".$_SESSION['lang']['dokumen']."</td>
                <td rowspan=2>".$_SESSION['lang']['keterangan']."</td> 
                 <td rowspan=2>".$_SESSION['lang']['updateby']."</td>   
                 </tr><tr class=rowheader>   
                <td>".$_SESSION['lang']['batastimur']."</td>                      
                <td>".$_SESSION['lang']['batasbarat']."</td>  
                <td>".$_SESSION['lang']['batasutara']."</td>
                <td>".$_SESSION['lang']['batasselatan']."</td> 
                    
                <td>".$_SESSION['lang']['tanamtumbuh']." (Rp)</td> 
                <td>".$_SESSION['lang']['gantilahan']." (Rp)</td> 
                <td>".$_SESSION['lang']['total']."<br>".$_SESSION['lang']['gantilahan']." (Rp)</td>    
                <td>".$_SESSION['lang']['biaya']."<br>".$_SESSION['lang']['camat']." (Rp)</td> 
                <td>".$_SESSION['lang']['biaya']."<br>".$_SESSION['lang']['kades']." (Rp)</td>
                <td>".$_SESSION['lang']['biaya']."<br>Matrai (Rp)</td>
                <td>".$_SESSION['lang']['total']."<br>".$_SESSION['lang']['biaya']." (Rp)</td>     
                    
                <td>".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['permintaandana']."</td>
                <td>".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['pembayaran']."</td>
                <td>".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['desa']."</td>
                <td>".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['camat']."</td>
                </tr></thead>
                <tbody>";
 while($bar1=mysql_fetch_object($res1))
        {
         $stdana=$bar1->statuspermintaandana==1?tanggalnormal($bar1->tanggalpengajuan):"";
         
         if($bar1->statuspermbayaran==1){
                 $stbayar=tanggalnormal($bar1->tanggalbayar)." Belum Lunas";
         }else if($bar1->statuspermbayaran==0){
                 $stbayar='Belum Bayar';
         }else if($bar1->statuspermbayaran==2){
                  $stbayar=tanggalnormal($bar1->tanggalbayar)." Lunas";
         }
         $stkades=$bar1->statuskades==1?tanggalnormal($bar1->tanggalkades):"";
         $stcamat=$bar1->statuscamat==1?tanggalnormal($bar1->tanggalcamat):"";
                echo"<tr class=rowcontent>                 
                          <td width='100px;'>
                               <img src='images/application/application_view_gallery.png' class='resicon' title='Upload Document' onclick=uploadDocument('".$bar1->idlahan."','".$bar1->pemilik."',event)>
                               <img src='images/skyblue/pdf.jpg' class='resicon' onclick=\"ptintPDF('".$bar1->idlahan."','".$bar1->pemilik."',event);\" title='Print Data Detail'>
                               <img src='images/skyblue/edit.png' class=resicon  caption='Edit' onclick=\"fillField('".$bar1->idlahan."','".$bar1->pemilik."','".$bar1->unit."','".$bar1->lokasi."','".$bar1->luas."','".$bar1->luasdapatditanam."','".$bar1->rptanaman."','".$bar1->rptanah."','".$bar1->statuspermintaandana."','".$bar1->statuspermbayaran."','".$bar1->kodeblok."','".$bar1->statuskades."','".$bar1->statuscamat."','".tanggalnormal($bar1->tanggalpengajuan)."','".tanggalnormal($bar1->tanggalbayar)."','".tanggalnormal($bar1->tanggalkades)."','".tanggalnormal($bar1->tanggalcamat)."','".$bar1->biayakades."','".$bar1->biayacamat."','".$bar1->biayamatrai."','".$bar1->keterangan."','".$bar1->nosurat."','".$bar1->batastimur."','".$bar1->batasbarat."','".$bar1->batasutara."','".$bar1->batasselatan."');\">
                                <img src='images/skyblue/posting.png' class='resicon' onclick=\"postingData('".$bar1->idlahan."','".$bar1->unit."')\" title='Posting'>
                                <img src='images/skyblue/delete.png' class='resicon' onclick=\"deleteData('".$bar1->idlahan."','".$bar1->unit."');\" title='Delete'>
                           </td>
                           <td>".$bar1->idlahan."</td>
                           <td>".$bar1->unit."</td>
                           <td>".$bar1->nama."</td>
                           <td>".$bar1->lokasi."</td>                                 
                           <td>".$bar1->desa."</td>
                           <td align=right>".$bar1->luas."</td>  
                           <td align=right>".$bar1->luasdapatditanam."</td>
                           <td>".$bar1->kodeblok."</td>    
                           <td>".$bar1->batastimur."</td>
                           <td>".$bar1->batasbarat."</td>
                           <td>".$bar1->batasutara."</td>
                           <td>".$bar1->batasselatan."</td>  
                           <td align=right>".number_format($bar1->rptanaman,0)."</td>    
                           <td align=right>".number_format($bar1->rptanah,0)."</td>
                           <td align=right>".number_format($bar1->totalgantirugi,0)."</td>    
                           <td align=right>".number_format($bar1->biayakades,0)."</td>
                           <td align=right>".number_format($bar1->biayacamat,0)."</td>
                           <td align=right>".number_format($bar1->biayamatrai,0)."</td>
                           <td align=right>".number_format(($bar1->totalgantirugi+$bar1->biayakades+$bar1->biayacamat+$bar1->biayamatrai),0)."</td>
                            <td>".$stdana."</td>
                           <td>".$stbayar."</td>
                           <td>".$stkades."</td>
                           <td>".$stcamat."</td>        
                           <td>".$bar1->nosurat."</td>  
                           <td>".$bar1->keterangan."</td>   
                           <td>".$bar1->namakaryawan."</td>                                
                            </td></tr>";
        }	 
        echo"	 
                 </tbody>
                 <tfoot>
                 </tfoot>
                 </table>";
}
?>
