<?php
require_once('master_validation.php');
require_once('config/connection.php');

$parent=$_POST['id_parent'];
$caption=$_POST['caption'];
$caption2=$_POST['caption2'];
$caption3=$_POST['caption3'];
$action=$_POST['action'];
$class=$_POST['class'];
$createFile=$_POST['create'];

//check menu deep. max 6
$nex_parent=$parent;
$deep=0;
for($x=0;$x<8;$x++)
{
	$st="select parent from ".$dbname.".menu where id=".$nex_parent;
	$re=mysql_query($st);
	if(mysql_num_rows($re)>0)
	{
	  $deep+=1;
	  while($ba=mysql_fetch_array($re))
	      {
	      	$nex_parent=$ba[0];
		  }
		   	
	}
	else
	{
		break;
	}
}

if($deep>6)
{
	echo " Warning: Menu to deep(max 6 child)";
}
else
{
if($parent==0)
  $type='master';
else
  $type='list';  

if($class=='devider')
   {
   	$caption='';
	$action='';
   }
if($class=='title')
   {
   	$action='';
   }   
   
$str="select max(urut) from ".$dbname.".menu where parent=".$parent;
$res=mysql_query($str);

while($bar=mysql_fetch_array($res))
{
	$urut=$bar[0];
}

if(!isset($urut))
 {$urut=0;}

  $nex_urut=$urut+1;
	$str="insert into ".$dbname.".menu (
		  type,
		  class,
		  caption,
                                          caption2,
                                          caption3,
		  action,
		  parent,
		  urut,
		  hide,
		  lastuser)
			  values(
		      '".$type."',
			  '".$class."',
			  '".$caption."',
                                                              '".$caption2."',
                                                              '".$caption3."',     
			  '".$action."',
			   ".$parent.",
			   ".$nex_urut.",
			  1,
			  '".$_SESSION['standard']['username']."'
			  )";
    if(mysql_query($str))
	{
		//set type as parent where id EQ $parent
		if($parent!=0)
		{
			$str1="update ".$dbname.".menu set type='parent'
			        where id=".$parent." and type='list'";
			mysql_query($str1);	
			
		}
		//create file
		if($createFile=='yes')
		{
			$filename=$action.".php";
				if (file_exists($filename)) {
				    //do nothing
				} else {
				    //write file
					$defaulContent="<?php //@Copy nangkoelframework
?>";
					$handle=fopen($filename,'w');
					 if(!fwrite($handle,$defaulContent))
					 {					 	
					 }
					 else
					 {
					 }
					 fclose($handle);
				}
			
		}
		
		//ambil id terakhir
		$str2="select max(id) from ".$dbname.".menu";
		$res2=mysql_query($str2);
		while($bar2=mysql_fetch_array($res2))
		{
			$max=$bar2[0];
		}
		if($deep>5)
		   echo $max.",stop";
		else
		   echo $max.",available";		
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
}
?>
