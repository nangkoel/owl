<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=$_POST['id'];

	$str="select caption, action, class as cl,type from ".$dbname.".menu 
	        where id=".$id;
	$res=mysql_query($str);	
	if(mysql_num_rows($res)<1)
	{
		
		echo " Gagal, Item menu tsb sudah dihapus";
	}
	else
	{
		while($bar=mysql_fetch_object($res))
		{
			$caption=$bar->caption;
			$action=$bar->action;
			$class=$bar->cl;
			$type=$bar->type;
		}
	if($class=='devider')
	  {
	  	echo " Gagal, Devider tidak dapat di ganti/edit";
	  }
	  else
	  {
	  	if($class=='title' or $type=='master')
			$disabled='disabled';
		else
		    $disabled='';	
		echo"<span style='text-align:center;'>
		  <input type=text value='".$caption."'  maxlength=40 class=myinputtext title='Text to be shown on menu' id=editcaption".$id." size=12 onkeypress=\"return tanpa_kutip(event);\" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>
	      <input type=text value='".$action."'  maxlength=40 class=myinputtext title='Filename (without extension) that will be execute when menu clicked' id=editaction".$id." size=12 onkeypress=\"return tanpa_kutip(event);\" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this) ".$disabled.">
		  <input type=button class=mybutton value=Save onclick=saveEditedMenu('".$id."');>
		  <input type=button class=mybutton value=Close onclick=\"clearFormEdit('edit".$id."');\">
		  </span>";      
	  }	
	}
?>
