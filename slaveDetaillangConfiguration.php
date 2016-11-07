<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$langname	=$_POST['langname'];
	$search='';
    $limit=' limit 100';
	if(isset($_POST['findlang']))
	{
		$limit='';
		$search=" where legend like '%".$_POST['findlang']."%' or location like '%".$_POST['findlang']."%' ";
	}
//add column to lang table
    $str="select idx,legend,location,".$langname." from ".$dbname.".bahasa ".$search." order by legend ".$limit;

   if($res=mysql_query($str)){
   	echo"<table class=data border=0 cellspacing=1>
	     <thead>
		 <tr class=rowheader><td>".$_SESSION['lang']['legend']."</td><td>".$_SESSION['lang']['location']."</td><td>".$_SESSION['lang']['text']."</td><td></td></tr>
		 </thead>
         <tbody>  "; 
	     
	  while($bar=mysql_fetch_assoc($res))
	  	 { 
	  	 	echo"<tr class=rowcontent><td>
			            ".$bar['legend']."
                     </td>
					 <td>
                        <input type=text class=myinputtext id='".$bar['idx']."location' value='".$bar['location']."' onkeypress=\"return tanpa_kutip(event);\" size=35>
					 </td>
					 <td><input type=text class=myinputtext id='".$bar['idx']."caption' value='".$bar[$_POST['langname']]."'  onkeypress=\"return tanpa_kutip(event);\" size=65></td>
				     <td><button class=mybutton onclick=\"updateCaption('".$bar['idx']."','".$bar['idx']."location','".$bar['idx']."caption','".$_POST['langname']."')\">".$_SESSION['lang']['save']."</button></td>
				 </tr>";
	  	 }
	echo"</tbody>
	     <tfoot></tfoot>
		 </table>";

   }
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>
