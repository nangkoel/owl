<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

	$pt=$_POST['pt'];
	$tipe=$_POST['tipe'];

$hasil='';
if($tipe=='bb'){
    //ambil namapt
if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
        $str="select kodeorganisasi,namaorganisasi,namaalias from ".$dbname.".organisasi
                        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL' or tipe = 'TRAKSI'
                        or tipe='HOLDING')  and induk!='' and induk = '".$pt."'
                        ";
                $hasil.="<option value=''>".$_SESSION['lang']['all']."</option>";    
}
else
if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
        $str="select kodeorganisasi,namaorganisasi,namaalias from ".$dbname.".organisasi
                        where induk='".$pt."' and length(kodeorganisasi)=4 and kodeorganisasi not like '%HO'";
        if($_SESSION['empl']['regional']=='SULAWESI'){
                $hasil.="<option value=''>".$_SESSION['lang']['all']."</option>";    
        }
}
else
        $str="select kodeorganisasi,namaorganisasi,namaalias from ".$dbname.".organisasi
                        where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'  and induk!=''";
        $res=mysql_query($str);
//        $hasil="";
        while($bar=mysql_fetch_object($res))
        {
                $hasil.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi." - ".$bar->namaalias."</option>";

        }    
}else{
//ambil namapt
$str="select kodeorganisasi,namaorganisasi,namaalias from ".$dbname.".organisasi 
      where induk='".$pt."'";
$res=mysql_query($str);
	$hasil='<option value="">'.$_SESSION['lang']['all'].'</option>';
while($bar=mysql_fetch_object($res))
{
	$hasil.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi." - ".$bar->namaalias."</option>";
}
    if($pt=='')$hasil='<option value="">'.$_SESSION['lang']['pilihdata'].'</option>';
}

echo $hasil;
?>