<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
echo open_body();
?>

<script language=javascript1.2 src='js/keu_5intraco.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['rekeningintraco']);

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodeorg']."</td><td>";
//	 <input type=text id=kodegolongan size=3 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext>
        //=================ambil PT;  
        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where char_length(kodeorganisasi)=4 order by kodeorganisasi";
        $res=mysql_query($str);
        $optpt="";
		$optpt.="<option value=''></option>";
        while($bar=mysql_fetch_object($res))
        {
                $optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi." - ".$bar->namaorganisasi."</option>";

        }
	 echo"<select id=kodeorg style='width:300px;'>".$optpt."</select></td></tr>
     <tr><td>".$_SESSION['lang']['jenis']."</td><td>";
//	 <tr><td>".$_SESSION['lang']['jenis']."</td><td><input type=text id=namagolongan size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>

//                $optpt="<option value='intra'>Intra</option>";
//                $optpt.="<option value='inter'>Inter</option>";
//Get status perkawinan enum
$optjenis='';
$arrjenis=getEnum($dbname,'keu_5caco','jenis');
        $optpt="";
	$optpt.="<option value=''></option>";
foreach($arrjenis as $kei=>$fal)
{
	$optpt.="<option value='".$kei."'>".$fal."</option>";
} 	 
	 echo"<select id=jenis style='width:300px;'>".$optpt."</select></td></tr>";
        //=================ambil akun;  
if($_SESSION['language']=='EN'){
    $zz="namaakun1 as namaakun";
}else{
    $zz="namaakun";
}         
        $str="select noakun,".$zz." from ".$dbname.".keu_5akun where (noakun like '221%' or noakun like '122%' or noakun like '121%') and char_length(noakun)=7 order by noakun";
        $res=mysql_query($str);
        $optpt="";
		$optakun1.="<option value=''></option>";
        while($bar=mysql_fetch_object($res))
        {
                $optakun1.="<option value='".$bar->noakun."'>".$bar->noakun." - ".$bar->namaakun."</option>";
		$namaakun[$bar->noakun]=$bar->namaakun;
        }
         echo"<tr><td>".$_SESSION['lang']['piutang']."</td><td>"; 
	 echo"<select id=akunpiutang style='width:300px;'>".$optakun1."</select></td></tr>";
         echo"<tr><td>".$_SESSION['lang']['hutang']."</td><td>"; 
	 echo"<select id=akunhutang style='width:300px;'>".$optakun1."</select></td></tr>             
     </table>
	 <input type=hidden id=kodeorgbef value=''>
	 <input type=hidden id=jenisbef value=''>
	 <input type=hidden id=noakunbef value=''>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanIntraco()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=hapusIntraco()>".$_SESSION['lang']['delete']."</button>
	 <button class=mybutton onclick=cancelIntraco()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['rekeningintraco']);
echo "<div id=container>";
	$str1="select * from ".$dbname.".keu_5caco order by kodeorg, akunpiutang";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
		 	<td style='width:100px;'>".$_SESSION['lang']['kodeorg']."</td>
			<td style='width:40px;'>".$_SESSION['lang']['jenis']."</td>
			<td>".$_SESSION['lang']['piutang']."</td>
                        <td>".$_SESSION['lang']['hutang']."</td>
			<td style='width:30px;'>*</td>
		 </tr>
		 </thead>
		 <tbody>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
			<td align=center>".$bar1->kodeorg."</td>";
			if($bar1->jenis=='inter')echo"<td>Inter</td>"; else echo"<td align=right>Intra</td>";
			echo"<td>".$bar1->akunpiutang." - ".$namaakun[$bar1->akunpiutang]."</td>
                             <td>".$bar1->akunhutang." - ".$namaakun[$bar1->akunhutang]."</td>    
			<td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->jenis."','".$bar1->akunpiutang."','".$bar1->akunhutang."');\"></td>
		</tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div>";
echo close_theme();
CLOSE_BOX();
echo close_body();
?>