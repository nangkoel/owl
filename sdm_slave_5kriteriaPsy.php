<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

$jabatan=$_POST['jabatan'];
$jabatan2=$_POST['jabatan2'];
$kriteria=$_POST['kriteria'];
$deskripsi=$_POST['deskripsi'];
$method=$_POST['method'];

if($method==''){
    $method=$_GET['method'];
    $jabatan=$_GET['jabatan'];
    $kriteria=$_GET['kriteria'];
    $jabatan2=$_GET['jabatan2'];
}

$optJabat="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sJabat="select distinct * from ".$dbname.".sdm_5jabatan order by kodejabatan asc";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    if($rJabat['kodejabatan']==$jabatan2)$pilih=' selected'; else $pilih='';
    $kamusJabat[$rJabat['kodejabatan']]=$rJabat['namajabatan'];
    $optJabat.="<option value='".$rJabat['kodejabatan']."'".$pilih.">".$rJabat['namajabatan']."</option>";
}



?>
<?php

switch($method)
{
case 'lihat':
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?php
    $str1="select * from ".$dbname.". sdm_5kriteriapsy where kodejabatan like '%".$jabatan."%' and kriteria like '%".$kriteria."%' order by kodejabatan, kriteria";
    $res1=mysql_query($str1);
    echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
         <thead>
         <tr class=rowheader>
            <td>".$_SESSION['lang']['jabatan']."</td>
            <td>".$_SESSION['lang']['kriteria']."</td>
            <td>".$_SESSION['lang']['deskripsi']."</td>
         </tr></thead>
         <tbody>";
    while($bar1=mysql_fetch_object($res1))
    {
        echo"<tr class=rowcontent>
            <td>".$kamusJabat[$bar1->kodejabatan]."</td>
            <td>".$bar1->kriteria."</td>
            <td>".str_replace("\n", "</br>",$bar1->penjelasan)."</td>
        </tr>";
    }	 
    echo"</tbody>
        <tfoot>
        </tfoot>
        </table>";
    exit;
break;    
case 'update':	
    $str="update ".$dbname.".sdm_5kriteriapsy set penjelasan='".$deskripsi."'
        where kodejabatan='".$jabatan."' and kriteria='".$kriteria."'";
    if(mysql_query($str))
    {
        
    }
    else
    {
        echo " Gagal, ".addslashes(mysql_error($conn));   
        exit;
    }
break;
case 'insert':
    $str="insert into ".$dbname.".sdm_5kriteriapsy (kodejabatan,kriteria,penjelasan)
        values('".$jabatan."','".$kriteria."','".$deskripsi."')";
    if(mysql_query($str))
    {
        
    }
    else
    {
        echo " Gagal, ".addslashes(mysql_error($conn));    
        exit;
    }	
break;
case 'delete':
    $str="delete from ".$dbname.".sdm_5kriteriapsy
    where kodejabatan='".$jabatan."' and kriteria='".$kriteria."'";
    if(mysql_query($str))
    {
        
    }
    else
    {
        echo " Gagal, ".addslashes(mysql_error($conn));
        exit;
    }
break;
case 'pdf':
    //=================================================
    class PDF extends FPDF {
        function Header() {
            global $jabatan;
            global $kriteria;
            $this->SetFont('Arial','B',11);
            $this->Cell(190,6,strtoupper($_SESSION['lang']['kriteria'].' '.$_SESSION['lang']['psikologi']),0,1,'C');
            $this->Ln();
            $this->SetFont('Arial','',10);
            $this->Cell(60,6,$_SESSION['lang']['jabatan'],1,0,'C');
            $this->Cell(30,6,$_SESSION['lang']['kriteria'],1,0,'C');	
            $this->Cell(100,6,$_SESSION['lang']['deskripsi'],1,0,'C');	
            $this->Ln();						
        }
    }
    //================================
    $pdf=new PDF('P','mm','A4');
    $pdf->AddPage();
    
    $str1="select * from ".$dbname.". sdm_5kriteriapsy where kodejabatan like '%".$jabatan2."%' order by kodejabatan, kriteria";
    $res1=mysql_query($str1);
    while($bar1=mysql_fetch_object($res1))
    {
        $pdf->Cell(60,6,$kamusJabat[$bar1->kodejabatan],0,0,'L');
        $pdf->Cell(30,6,$bar1->kriteria,0,0,'L');	
        $pdf->MultiCell(100, 6, $bar1->penjelasan, 0, 'L', false);
    }	 
    $pdf->Output();		
    exit;
break;
default:
break;					
}

echo "<table><tr>
        <td>".$_SESSION['lang']['kodejabatan']."</td>
        <td><select id=jabatan2 onchange=pilihjabatan()>".$optJabat."</select> <img class=\"resicon\" src=\"images/pdf.jpg\" title=\"PDF\" onclick=\"lihatpdf(event,'sdm_slave_5kriteriaPsy.php')\"></td>
    </tr></table>";

$str1="select * from ".$dbname.". sdm_5kriteriapsy where kodejabatan like '%".$jabatan2."%' order by kodejabatan, kriteria";
$res1=mysql_query($str1);
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
     <thead>
     <tr class=rowheader>
        <td>".$_SESSION['lang']['nourut']."</td>
        <td>".$_SESSION['lang']['jabatan']."</td>
        <td>".$_SESSION['lang']['kriteria']."</td>
        <td>".$_SESSION['lang']['deskripsi']."</td>
        <td width=100>".$_SESSION['lang']['action']."</td>
     </tr></thead>
     <tbody>";
$no=0;
while($bar1=mysql_fetch_object($res1))
{
    $no+=1;
    echo"<tr class=rowcontent>
        <td align=right>".$no."</td>
        <td>".$kamusJabat[$bar1->kodejabatan]."</td>
        <td>".$bar1->kriteria."</td>
        <td>".substr(str_replace("\n", "</br>",$bar1->penjelasan),0,75)."</td>
        <td align=center>
            <img src=images/application/application_view_list.png class=resicon  caption='Preview' onclick=\"lihat('".$bar1->kodejabatan."','".$bar1->kriteria."',event);\">
            <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodejabatan."','".$bar1->kriteria."','".str_replace("\n", "\\n",$bar1->penjelasan)."');\">
            <img src=images/application/application_delete.png class=resicon  caption='Edit' onclick=\"hapus('".$bar1->kodejabatan."','".$bar1->kriteria."');\">
        </td>
    </tr>";
}	 
echo"</tbody>
    <tfoot>
    </tfoot>
    </table>";

?>
