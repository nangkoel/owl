<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_payrollHO.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
include('master_mainMenu.php');





$iHo="select id,name from ".$dbname.".sdm_ho_component
      where plus=1 order by id";
$nHo=  mysql_query($iHo) or die (mysql_error($conn));
while($dHo=  mysql_fetch_assoc($nHo))
{
    $id[$dHo['id']]=$dHo['id'];
    $nmKom[$dHo['id']]=$dHo['name'];
}

$iPph="select id,status,regional from ".$dbname.".sdm_5komponenpph
      where regional='".$_SESSION['empl']['regional']."' order by id";
$nPph=  mysql_query($iPph) or die (mysql_error($conn));
while($dPph=  mysql_fetch_assoc($nPph))
{
    $id[$dPph['id']]=$dPph['id'];
    $reg[$dPph['id']]=$dPph['regional'];
    $st[$dPph['id']]=$dPph['status'];
}




$optKom=  makeOption($dbname, 'sdm_ho_component', 'id,name');

	OPEN_BOX('','<b>PPn21 COMPONENT</b>');
		echo"<div id=EList>";
		echo OPEN_THEME('Component Gaji yang dikenai PPh21:')."<br>";

/*$str="select id,name,pph21 from ".$dbname.".sdm_ho_component
      where plus=1 order by id";*/

$va="Beri tanda check(V) pada komponen yang kena pajak.
     <table class=sortable cellspacing=1 border=0 width=500px>
      <thead>
	  <tr class=rowheader>
	    <td>ID.</td>
            <td align=center>Nama.Komponen</td>
            <td align=center>Yes/No</td>
	  </tr>	
	  </thead>
	  <tbody>";


foreach($id as $lst)
{
    if($st[$lst]==1)
    {
        $ch='checked';
    }
    else
    {
       $ch=''; 
    }
    $va.="<tr class=rowcontent>";
        $va.="<td>".$lst."</td>";
        $va.="<td>".$nmKom[$lst]."</td>";
        $va.="<td align=center><input type=checkbox id=ch".$lst." ".$ch." onclick=savePPh21Component(this,this.value) value=".$lst."></td>";
    $va.="</tr>";
}

/*while($bar=mysql_fetch_object($res))
{
    if($bar->status==1)
    {
        $ch='checked';
    }
    else
    {
       $ch=''; 
    }
	    
	$va.="<tr class=rowcontent>
	        <td class=firsttd align=center>".$bar->id."</td>
			<td>".$optKom[$bar->id]."</td>
			<td align=center><input type=checkbox id=ch".$bar->id." ".$ch." onclick=savePPh21Component(this,this.value) value=".$bar->id."></td>
	    </tr>"; 
}*/	  
$va.="</tbody><tfoot></tfoot></table>";	  	  
$hfrm[0]='Komponen Gaji';
$frm[0]="<br>".$va."<br>
		";

drawTab('FRM',$hfrm,$frm,150,600);  	  			 
		echo"</div>";
		echo CLOSE_THEME();		
	CLOSE_BOX();
	echo close_body();	
?>