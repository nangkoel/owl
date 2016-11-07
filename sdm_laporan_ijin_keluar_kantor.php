<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_laporan_ijin_keluar_kantor.js'></script>
<script>
    tolak="<?php echo $_SESSION['lang']['ditolak'];?>";
    </script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['list']." ".$_SESSION['lang']['izinkntor']).'</b>');

$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optJenis=$optKary;
//$sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan where alokasi=1 order by namakaryawan asc";

$sOrg="select distinct c.kodeorganisasi,c.namaalias from ".$dbname.".sdm_ijin a 
       left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
       left join ".$dbname.".organisasi c on b.lokasitugas=c.kodeorganisasi";
$sKary="select distinct a.karyawanid,b.namakaryawan,b.nik from ".$dbname.".sdm_ijin a 
       left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
       left join ".$dbname.".organisasi c on b.lokasitugas=c.kodeorganisasi";
if($_SESSION['empl']['tipelokasitugas']!='HOLDING') {
        $sOrg.=" where tipe!='HOLDING' and induk='".$_SESSION['empl']['kodeorganisasi']."'";
        $sKary.=" where tipe!='HOLDING' and induk='".$_SESSION['empl']['kodeorganisasi']."'";
}
$sOrg.=" order by namaorganisasi asc";
$sKary.=" order by namakaryawan asc";
$optOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['kodeorganisasi']." - ".$rOrg['namaalias']."</option>";
}

$qKary=mysql_query($sKary) or die(mysql_error($sKary));
while($rKary=mysql_fetch_assoc($qKary))
{
    $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']." - ".$rKary['nik']."</option>";
}
                $arrijin=getEnum($dbname,'sdm_ijin','jenisijin');
                foreach($arrijin as $kei=>$fal)
                {
                    if($_SESSION['language']=='ID'){
                        $optJenis.="<option value='".$kei."'>".$fal."</option>";
                    }else{
                        switch($fal){
                            case 'TERLAMBAT':
                                $fal='Late for work';
                                break;
                            case 'KELUAR':
                                $fal='Out of Office';
                                break;         
                            case 'PULANGAWAL':
                                $fal='Home early';
                                break;     
                            case 'IJINLAIN':
                                $fal='Other purposes';
                                break;   
                            case 'CUTI':
                                $fal='Leave';
                                break;       
                            case 'MELAHIRKAN':
                                $fal='Maternity';
                                break;           
                            default:
                                $fal='Wedding, Circumcision or Graduation';
                                break;                              
                        }
                        $optJenis.="<option value='".$kei."'>".$fal."</option>";       
                    }                    
                }  
echo"
     <img onclick=detailExcel(event,'sdm_slave_laporan_ijin_meninggalkan_kantor.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
     &nbsp;".$_SESSION['lang']['namakaryawan'].": <select id=karyidCari style=width:150px onchange=getCariDt()>".$optKary."</select>&nbsp;
     ".$_SESSION['lang']['jeniscuti'].": <select id=jnsCuti style=width:150px onchange=getCariDt()>".$optJenis."</select>&nbsp;"
      .$_SESSION['lang']['lokasitugas'].": <select id=kdOrg style=width:150px onchange=getCariDt()>".$optOrg."</select>&nbsp;
         <button class=mybutton onclick=dtReset()>".$_SESSION['lang']['cancel']."</button>
         <div style='width:100%;height:600px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0>
             <thead>
                    <tr>
                          <td align=center rowspan=2>No.</td>
                          <td align=center rowspan=2>".$_SESSION['lang']['tanggal']."</td>
                          <td align=center rowspan=2>".$_SESSION['lang']['nama']."</td>
                          <td align=center rowspan=2>".$_SESSION['lang']['lokasitugas']."</td>
                          <td align=center rowspan=2>".$_SESSION['lang']['keperluan']."</td>
                          <td align=center rowspan=2>".$_SESSION['lang']['jenisijin']."</td>  
                          <td align=center rowspan=2>".$_SESSION['lang']['persetujuan']."</td>    
                          
			  <td align=center rowspan=2>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['mulai']."</td>  
                          <td align=center rowspan=2>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['selesai']."</td>  
                          <td align=center rowspan=2>".$_SESSION['lang']['jumlahhk']." ".$_SESSION['lang']['diambil']."</td>
                          <td align=center rowspan=2>".$_SESSION['lang']['periodecuti']."</td>  
                          <td align=center rowspan=2>".$_SESSION['lang']['cuti']." ".$_SESSION['lang']['sisa']."</td>
                          <td align=center colspan=3>".$_SESSION['lang']['status']." ".$_SESSION['lang']['persetujuan']."</td>
                          <td align=center rowspan=2>".$_SESSION['lang']['ganti']."</td>  
			  <td align=center rowspan=2>".$_SESSION['lang']['print']."</td>    
                    </tr>  
                    <tr>
                          <td align=center>".$_SESSION['lang']['atasan']."</td>
			  <td align=center>".$_SESSION['lang']['atasan']." ".$_SESSION['lang']['dari']." ".$_SESSION['lang']['atasan']."</td> 
			  <td align=center>".$_SESSION['lang']['hrd']."</td> 
                    </tr>
                 </thead>
                 <tbody id=container><script>loadData()</script>
                 </tbody>

           </table>
     </div>";
CLOSE_BOX();
close_body();
?>