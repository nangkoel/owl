<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');

//==================================================================================================================================================================
 echo"<div>
     <fieldset style='width:200px;color:#333399;'>
         <legend>[Info] ".$_SESSION['lang']['menulevel'].":</legend>
         ".$_SESSION['lang']['menuleveldesc']."
         </fieldset><br>
         <input type=button value='".$_SESSION['lang']['apply']."' class=mybutton onclick=window.location.reload()>
     <input type=button value='".$_SESSION['lang']['close']."' class=mybutton onclick=\"hideDetailForm('ctr','ctrmenu');hideThis('lab2');\">
         <hr>";


$opt='<option>0</option>';
for($d=1;$d<25;$d++)
{
        $opt.="<option>".$d."</option>";
}

$str="select a.*,b.namakaryawan,b.lokasitugas from ".$dbname.".user a
          left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid order by namauser";
$res=mysql_query($str);

echo "<table width=100% cellspacing=1 border=0 class=data>
      <thead>
          <tr><td>Uname</td>
              <td>KaryawanId</td>
              <td>Nama</td>
              <td>Lokasi.Tugas</td>
                  <td>UserStatus</td>
                  <td>Access Level</td>
          </tr>	  
          </thead>
          <tbody>
          ";
        while($bar=mysql_fetch_object($res))
        {
          echo"<tr class=rowcontent>
                 <td class=firsttd>".$bar->namauser."</td>
                  <td>".$bar->karyawanid."</td>
                  <td>".$bar->namakaryawan."</td>
                  <td>".$bar->lokasitugas."</td>";
           if($bar->status==1)
             echo"<td><font color=#00AA00><b>Active</b></td>"; 
           else
                 echo"<td>Inactive</td>";  			 

         echo"	 <td align=right>
                           <select id=\"select".$bar->namauser."\" onchange=\"setAccessLevel(this,'".$bar->namauser."',this.options[this.selectedIndex].text)\">
                             <option>".$bar->hak."</option"
                                 .$opt."
                           </select>
             </td>	 
                 </tr>";
        }
echo"</tbody></table><br>";	  		
echo "
<input type=button value='".$_SESSION['lang']['apply']."' class=mybutton onclick=window.location.reload()>
<input type=button value='".$_SESSION['lang']['close']."' class=mybutton onclick=\"hideDetailForm('ctr','ctrmenu');hideThis('lab2');\">
<br><br>";
?>
