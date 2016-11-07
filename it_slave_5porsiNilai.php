<?php
require_once('master_validation.php');
require_once('config/connection.php');
//$arr="##kode##nilKode##ket##method";
$kode=$_POST['kode'];
$jmlhPorsi=$_POST['jmlhPorsi'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".it_presentasenilai set jumlah='".$jmlhPorsi."',
	       where kode='".$kode."'";
            //exit("Error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
    $sCek="select distinct * from ".$dbname.".it_presentasenilai where kode='".$kode."'";
   // exit("Error".$sCek);
    $qCek=mysql_query($sCek) or die(mysql_error($conn));
    $rRow=mysql_num_rows($qCek);
    if($rRow>0)
    {
        $sdel="delete from ".$dbname.".it_presentasenilai where kode='".$kode."'";
        if(mysql_query($sdel))
        {
        $sCek="select distinct sum(jumlah) as jumlah from ".$dbname.".it_presentasenilai ";
        $qCek=mysql_query($sCek) or die(mysql_error($conn));
        $rRow=mysql_fetch_assoc($qCek);
        $jmlhCek=$rRow['jumlah']+$jmlhPorsi;
        if($jmlhCek>100)
        {
            $jmlhPorsi=100-$rRow['jumlah'];
        }
        
	$str="insert into ".$dbname.".it_presentasenilai (kode,jumlah)
	      values('".$kode."','".$jmlhPorsi."')";
        //exit("Error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
        }
    }
    else
    {
        $sCek="select distinct sum(jumlah) as jumlah from ".$dbname.".it_presentasenilai ";
        $qCek=mysql_query($sCek) or die(mysql_error($conn));
        $rRow=mysql_fetch_assoc($qCek);
        $jmlhCek=$rRow['jumlah']+$jmlhPorsi;
        if($jmlhCek>100)
        {
            $jmlhPorsi=100-$rRow['jumlah'];
        }
        
	$str="insert into ".$dbname.".it_presentasenilai (kode,jumlah)
	      values('".$kode."','".$jmlhPorsi."')";
        //exit("Error:".$str);
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
     }
    
	break;
case 'delete':
	$str="delete from ".$dbname.".it_presentasenilai
	where kode='".$kode."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
        case'loadData':
        $str1="select * from ".$dbname.".it_presentasenilai order by kode asc";
if($res1=mysql_query($str1))
{
	echo"<table class=sortable cellspacing=1 border=0 style='width:350px;'>
	     <thead>
		 <tr class=rowheader>
                 <td style='width:150px;'>".$_SESSION['lang']['kodeabs']."</td>
                 <td>".$_SESSION['lang']['jumlah']."</td>
                 <td style='width:70px;'>*</td></tr>
		 </thead>
		 <tbody>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
                     <td align=left>".$bar1->kode."</td>
                     <td>".$bar1->jumlah."</td>
                     <td align=center><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->jumlah."');\"> </td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
}
        break;
default:
   break;					
}

?>
