<?php
require_once('master_validation.php');
require_once('config/connection.php');


$SIMPAN=$_POST['SIMPAN'];
$MILLCODE=strtoupper($_POST['MILLCODE']);
$MILLNAME=strtoupper($_POST['MILLNAME']);
$COMPCODE=$_POST['COMPCODE'];
$COMPNAME=$_POST['COMPNAME'];
$MNGRNAME=$_POST['MNGRNAME'];
$KTUNAME=$_POST['KTUNAME'];
$KRANINAME=$_POST['KRANINAME'];
$TIMEVEH=$_POST['TIMEVEH'];
$USERID=$_SESSION['USERID'];

if($SIMPAN==1)
{
	$str="update ".$dbname.".mssystem set
		  MILLCODE='".$MILLCODE."',
		  MILLNAME='".$MILLNAME."',
		  COMPCODE='".$COMPCODE."',
		  COMPNAME='".$COMPNAME."',
		  MNGRNAME='".$MNGRNAME."',
		  KTUNAME='".$KTUNAME."',
		  KRANINAME='".$KRANINAME."',
		  TIMEVEH=".$TIMEVEH.",
		  USERID='".$_SESSION['standard']['username']."'
		  where COMPCODE='".$COMPCODE."'";
//echo "Gagal ".$str;
}

if(mysql_query($str))
{
	//printf("Updated Records: %d\n", mysql_affected_rows());
	//if($rc>0)
	echo"0";
}
else
{
  echo "Error: ".addslashes(mysql_error($conn));	
}
?>
