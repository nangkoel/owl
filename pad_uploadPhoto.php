<?php
require_once('master_validation.php');
require_once('config/connection.php');

  echo"<center><fieldset  style='width:430px;'><legend>Upload Picture For:".$_GET['notransaksi']."</legend>
       <form id='photoqc' name='photoqc' method=post enctype='multipart/form-data'>	   	 
       <table cellapacing=1 border=0>		
        <tr>
                     <td>Photo1</td>
                     <td>
                        <input type=hidden name=MAX_FILE_SIZE value=75000>
                        <input type=file name=file[] size35>
                     </td>
        </tr><tr>				
                     <td>Photo2</td>
                     <td>
                        <input type=file name=file[] size35>
                     </td>
        </tr><tr>
        </tr><tr>				
                     <td>Photo3</td>
                     <td>
                        <input type=file name=file[] size35>
                     </td>
        </tr><tr>	           
                     <td>Photo4</td>
                     <td>
                        <input type=file name=file[] size35>
                        <input type=hidden name=notransaksi id=photox value='".$_GET['notransaksi']."'>
                     </td>						
        </tr>
    </table>

        </form>
        <center>
        Max 75Kb/File.<br>
        <button onclick=parent.simpanPhoto()>Save</button>
        </center>
        </fieldset></center><hr>";
  
  $str="select * from ".$dbname.".pad_photo where idlahan='".$_GET['notransaksi']."'";
  $res=mysql_query($str);
  $no=1;
  while($bar=mysql_fetch_object($res))
  {
      echo $no.". Filename:".$bar->filename." (".number_format($bar->filesize/1000,2)."Kb.) <a style='cursor:pointer;color:blue; title='Delete' onclick=\"parent.delPicture('".$bar->idlahan."','".$bar->filename."')\">Remove</a><br>";
           
           $ext=split('[.]', basename($bar->filename));
           $ext=$ext[count($ext)-1];
           $ext=strtolower($ext);
      if($ext=='jpg' or $ext=='jpeg' or $ext=='png' or $ext=='bmp' or $ext=='gif' or $ext=='tiff' )
      {
          echo "<img src=filepad/".$bar->filename." height=250px><br>";
      }  
       else
       {
            echo "<a href=\"filepad/".$bar->filename."\"><img src=images/preview.png></a><br>";
       }   
      $no++;
  }
?>
