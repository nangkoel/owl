<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=Javascript1.2 type=text/javascript src=js/transactionPeriod.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();
echo OPEN_THEME('Setup Transaction Period:');

$opt='';
for($x=0;$x<=30;$x++)
{
	$per=date('m-Y',mktime(0,0,0,date('m')-$x,15,date('Y')));
	$val=date('Y-m',mktime(0,0,0,date('m')-$x,15,date('Y')));
	$opt.="<option value='".$val."'>".$per."</option>";
}

$str="select * from ".$dbname.".org order by emplname";
$res=mysql_query($str);
echo"<br>Setup each organitation's transaction period:<br>
<table class=sortable width=100% cellspacing=1 border=0>
         <thead>
		     <tr>
			 <td>Num</td>
			 <td>Org.Code</td>
			 <td>Org.Code</td>
			 <td>Type</td>
			 <td>Transaction.Period</td>
			 </tr>
		 </thead>
		 <tbody>";

while($bar=mysql_fetch_object($res))
{
      if($bar->active_period!='')
	  {
	  	$op="<option value='".$bar->active_period."'>".substr($bar->active_period,4,2)."-".substr($bar->active_period,0,4)."</option>";
	  }
	  else{
	  	$op="<option value=''></option>";
	  }
	  $op.=$opt;
	echo"	 <tr class=rowcontent>
			 <td class=firsttd>".$bar->num."</td>
			 <td>".$bar->code."</td>
			 <td>".$bar->emplname."</td>
			 <td>".$bar->type."</td>
			 <td>
			    <select id=period onchange=savePeriode(this.options[this.selectedIndex].value,'".$bar->code."',this);>".$op."</select>  
			 </td>
			 </tr>";
}
echo"</tbody>
     </table>
	 <br>
	 ";
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();
?>
