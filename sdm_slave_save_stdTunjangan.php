<?php
require_once('master_validation.php');
require_once('config/connection.php');
                                    
$kodejabatan=$_POST['kodejabatan'];
$lokasi     =$_POST['lokasi'];
$tjjabatan  =$_POST['tjjabatan'];
$tjkota     =$_POST['tjkota'];
$tjtransport=$_POST['tjtransport'];
$tjmakan    =$_POST['tjmakan'];
$tjsdaerah  =$_POST['tjsdaerah'];
$tjmahal    =$_POST['tjmahal'];
$tjpembantu =$_POST['tjpembantu'];

//del first
$str="delete from ".$dbname.".sdm_5stdtunjangan where jabatan=".$kodejabatan." and penempatan='".$lokasi."'";
mysql_query($str);

//insert
$str="insert into ".$dbname.".sdm_5stdtunjangan (
      jabatan, penempatan, tjjabatan, tjkota, 
      tjtransport, tjmakan, tjsdaerah, tjmahal, 
      tjpembantu)
      values(
      ".$kodejabatan.",
      '".$lokasi."',
      ".$tjjabatan.",
      ".$tjkota.",
      ".$tjtransport.",
      ".$tjmakan.",
      ".$tjsdaerah.",
      ".$tjmahal.",
      ".$tjpembantu.");";
 if(mysql_query($str))
 {
     
 }
 else
  {
   echo "Error ".addslashes(mysql_error($conn)); 
   exit();
  }
  
// default, display content
$str="select a.*,b.namajabatan from ".$dbname.".sdm_5stdtunjangan a left join ".$dbname.".sdm_5jabatan b on a.jabatan=b.kodejabatan order by penempatan,jabatan";
$res=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    echo "<tr class=rowcontent>
          <td>".$no."</td>
          <td>".$bar->namajabatan."</td>
          <td>".$bar->penempatan."</td>
          <td>".$bar->tjjabatan."</td>
          <td>".$bar->tjkota."</td>
          <td>".$bar->tjtransport."</td>
          <td>".$bar->tjmakan."</td>
          <td>".$bar->tjsdaerah."</td>
          <td>".$bar->tjmahal."</td>
          <td>".$bar->tjpembantu."</td>
          <td><img class='resicon' onclick=\"fillField('".$bar->jabatan."','".$bar->penempatan."','".$bar->tjjabatan."','".$bar->tjkota."','".$bar->tjtransport."','".$bar->tjmakan."','".$bar->tjsdaerah."','".$bar->tjmahal."','".$bar->tjpembantu."');\" title='Edit' src='images/application/application_edit.png'></td>
          </tr>";
}   
  
?>