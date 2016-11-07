<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/setup_gantiLokasiTugas.js></script>
<?php
include('master_mainMenu.php');

$a="select * from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$_SESSION['standard']['userid']."'";	
$b=mysql_query($a) or die (mysql_error($conn));
$c=mysql_fetch_assoc($b);
	$lokasi=$c['kodeorg'];
	$jum=count($lokasi);
$whereMake="kodeorganisasi='".$lokasi."'";
$tipeLok=makeOption($dbname,'organisasi','kodeorganisasi,tipe',$whereMake);	
	
$whereRo="kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and right(kodeunit,2)='RO')";
$whereUnit="kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment 
			where regional='".$_SESSION['empl']['regional']."' and right(kodeunit,2)!='HO')";
$whereTemp="kodeorganisasi='".$lokasi."'";		
$whereHo="tipe='HOLDING' and length(kodeorganisasi)=4 ";
	
	//echo $jum.___.$lokasi;
if($jum>0)
{
	if($tipeLok[$lokasi]=='KANWIL')
	{
		$str="select kodeorganisasi,namaorganisasi,namaalias,alokasi,tipe from ".$dbname.".organisasi where ".$whereUnit." ";
	}
	else
	{
		$str="select kodeorganisasi,namaorganisasi,namaalias,alokasi,tipe from ".$dbname.".organisasi where (".$whereRo.") or (kodeorganisasi='".$lokasi."')";
	}
	
}
else
{
    if($tipeLok[$lokasi]!=''){
        if(($tipeLok[$lokasi]=='KEBUN')||($tipeLok[$lokasi]=='PABRIK')){
            $str="select kodeorganisasi,namaorganisasi,namaalias,alokasi,tipe from ".$dbname.".organisasi where ".$whereRo." ";
        }else{
            if($tipeLok[$lokasi]=='KANWIL'){
                $str="select kodeorganisasi,namaorganisasi,namaalias,alokasi,tipe from ".$dbname.".organisasi where ".$whereUnit." ";
            }
        }
    }else{
         if(($_SESSION['empl']['tipelokasitugas']=='KEBUN')||($_SESSION['empl']['tipelokasitugas']=='PABRIK')){
            $str="select kodeorganisasi,namaorganisasi,namaalias,alokasi,tipe from ".$dbname.".organisasi where ".$whereRo." ";
        }else{
            if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
                $str="select kodeorganisasi,namaorganisasi,namaalias,alokasi,tipe from ".$dbname.".organisasi where ".$whereUnit." ";
            }
        }
    }
	 
}



if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$str="select kodeorganisasi,namaorganisasi,namaalias,alokasi,tipe from ".$dbname.".organisasi where ".$whereHo." ";
}

if($_SESSION['empl']['bagian']=='IT'){
    $str="select kodeorganisasi,namaorganisasi,namaalias,alokasi,tipe from ".$dbname.".organisasi 
      where length(kodeorganisasi)=4";
}
$str.=" order by namaorganisasi";
//print_r($_SESSION['empl']);

//echo $str;


$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    if ($_SESSION['empl']['lokasitugas']!=$bar->kodeorganisasi) {
        if($bar->tipe=='KEBUN' && substr($bar->namaorganisasi,0,6)=='DIVISI'){
            $opt.="<option value='".$bar->alokasi."'>".$bar->kodeorganisasi." - ".$bar->namaalias."</option>";
        } else {
            $opt.="<option value='".$bar->alokasi."'>".$bar->kodeorganisasi." - ".$bar->namaorganisasi."</option>";
        }
    }
}
OPEN_BOX('');



echo "<fieldset><legend>".$_SESSION['lang']['pindahtugas']."</legend><input type=hidden maxlength=4  id=lokasilama value=".$_SESSION['empl']['lokasitugas'].">
<br><br>".$_SESSION['lang']['youareon'].": <b>".$_SESSION['empl']['lokasitugas']." - ".getNamaOrganisasi($_SESSION['empl']['lokasitugas'])."</b><br><br> ".$_SESSION['lang']['tujuan']."
      <select id=tjbaru onkeypress=\"return validat(event);\">".$opt."</select><br><br>
	  <button class=mybutton onclick=gantiLokasitugas()>".$_SESSION['lang']['save']."</button>
	  </fieldset>";
CLOSE_BOX();
echo close_body();
?>
