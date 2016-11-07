<?php
// file creator: dhyaz aug 10, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$str="select regional, tahunbudget, sumberharga from ".$dbname.".bgt_masterbarang
    group by regional, tahunbudget";
$res=mysql_query($str);
$no=1;
while($bar= mysql_fetch_object($res))
{
    echo"<tr class=rowcontent>
        <td align=center>".$no."</td>
	<td align=center><label id=tahun2_".$no.">".$bar->tahunbudget."</label></td>
	<td align=center><label id=reg2_".$no.">".$bar->regional."</label></td>";
    if($_SESSION['empl']['bagian']!='AGR' or $_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    echo"<td align=center><button class=mybutton onclick=listHarga(".$no.")>".$_SESSION['lang']['list']."</button></td>
        <td align=center><button class=mybutton onclick=deleteHarga(".$bar->tahunbudget.",'".$bar->regional."')>".$_SESSION['lang']['delete']."</button></td>
        <td align=center><button class=mybutton onclick=hargaKeExcel(event,".$no.")>Excel</button></td>
	<td align=center><button id=edit_".$no." class=mybutton onclick=tampilkanHarga(".$bar->tahunbudget.",'".$bar->regional."','".$bar->sumberharga."')>".$_SESSION['lang']['edit']."</button></td>
	<td align=center><button disabled=true id=close_".$no." class=mybutton onclick=TutupHarga(1,".$no.")>".$_SESSION['lang']['close']."</button></td>";
    }else{
        echo"<td colspan=5>&nbsp;</td>";
    }
    echo"</tr>";
    $no+=1;
}
    
    
