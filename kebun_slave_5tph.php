<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$kodeorg=$_POST['kodeorg'];
$notph=$_POST['notph'];
$keterangan=$_POST['keterangan'];

switch($_POST['aksi']){
    case 'save':
          $str="insert into ".$dbname.".kebun_5tph(kode,kodeorg,keterangan) values('".$notph."','".$kodeorg."','".$keterangan."')"; 
         if(mysql_query($str))
          {
              
          }
          else
          {
              echo " Error:".addslashes(mysql_error($conn));
          }
        
        break;
    case 'edit':
         $str="update ".$dbname.".kebun_5tph set keterangan='".$keterangan."' where kodeorg='".$kodeorg."'
               and kode='".$notph."'";
         if(mysql_query($str))
          {
              
          }
          else
          {
              echo " Error:".addslashes(mysql_error($conn));
          }         
        break;
    case 'del':
         $str="delete from ".$dbname.".kebun_5tph  where kodeorg='".$kodeorg."'
               and kode='".$notph."'";
         if(mysql_query($str))
          {
              
          }
          else
          {
              echo " Error:".addslashes(mysql_error($conn));
          }         
        break;
        case 'list':
         break;
    default:
        break;
}

$str="select * from ".$dbname.".kebun_5tph where kodeorg='".$kodeorg."' order by kode";
$res=mysql_query($str);
echo"<table class=sortable cellspacing=1 border=0>
     <thead>
       <tr class=rowheader>
           <td>".$_SESSION['lang']['no']."</td>
           <td>".$_SESSION['lang']['kodeorg']."</td>
           <td>".$_SESSION['lang']['notph']."</td>    
           <td>".$_SESSION['lang']['keterangan']."</td>
           <td>".$_SESSION['lang']['action']."</td>    
       </tr>
     </thead>
     <tbody>";
while($bar=mysql_fetch_object($res))
{
    $no+=1;

 echo"<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$bar->kodeorg."</td>
           <td>".$bar->kode."</td>    
           <td>".$bar->keterangan."</td>
           <td>
               <img id='detail_edit' title='dedit data' class=zImgBtn onclick=\"editData('".$bar->kodeorg."','".$bar->kode."','".$bar->keterangan."')\" src='images/application/application_edit.png'/>    
               <img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteData('".$bar->kodeorg."','".$bar->kode."')\" src='images/application/application_delete.png'/>
           </td>    
           
       </tr> ";    
}
echo"</tbody>
    <tfoot></tfoot></table>";
?>
