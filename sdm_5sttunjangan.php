<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript1.2 src='js/sdm_stdTunjangan.js'></script>
<?php
$str="select * from ".$dbname.".sdm_5jabatan order by namajabatan";
$res=mysql_query($str);
$optjab='';
while($bar=mysql_fetch_object($res))
{
    $optjab.="<option value='".$bar->kodejabatan."'>".$bar->namajabatan."</option>";
}
OPEN_BOX();
echo "<fieldset><legend>".$_SESSION['lang']['stdtunjangan']."</legend>";
echo "<table>
      <tr>
       <td>".$_SESSION['lang']['kodejabatan']."</td>
           <td>:<select id=kodejabatan>".$optjab."</select></td></tr>
       <td>".$_SESSION['lang']['lokasi']."</td><td>:<select id=lokasi><option value='LOKASI'>LOKASI</option><option value='KOTA'>KOTA</option></select></td></tr>
       <td>Tunj.Jabatan</td><td>:Rp.<input type=text class=myinputtextnumber id=tjjabatan size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr> 
       <td>Tunj.Staff Kota</td><td>:Rp.<input type=text class=myinputtextnumber id=tjkota size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>  
       <td>Tunj.Transport</td><td>:Rp.<input type=text class=myinputtextnumber id=tjtransport size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>  
       <td>Tunj.Makan</td><td>:Rp.<input type=text class=myinputtextnumber id=tjmakan size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>  
       <td>Tunj.Staff Daerah</td><td>:Rp.<input type=text class=myinputtextnumber id=tjsdaerah size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>  
       <td>Tunj.Kemahalan</td><td>:Rp.<input type=text class=myinputtextnumber id=tjmahal size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>  
       <td>Tunj.Pembantu</td><td>:Rp.<input type=text class=myinputtextnumber id=tjpembantu size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>        
      </tr>
      </table>
      <button class=mybutton onclick=simpanStdjabatan()>".$_SESSION['lang']['save']."</button>
      <button class=mybutton onclick=cancelStdTun()>".$_SESSION['lang']['clear']."</button>
      ";      
echo"</fieldset>";

echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend>";
      echo "<table class=sortable border=0 cellspacing=1>
            <thead>
              <tr class=rowheader>
               <td>".$_SESSION['lang']['nomor']."</td>
               <td>".$_SESSION['lang']['kodejabatan']."</td>
               <td>".$_SESSION['lang']['lokasi']."</td>
               <td>Tunj.Jabatan</td> 
               <td>Tunj.Staff Kota</td> 
               <td>Tunj.Transport</td> 
               <td>Tunj.Makan</td> 
               <td>Tunj.Staff Daerah</td> 
               <td>Tunj.Kemahalan</td> 
               <td>Tunj.Pembantu</td> 
               <td>".$_SESSION['lang']['aksi']."</td>
              </tr>
            </thead>
            <tbody id=container>";
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
echo"</tbody><tfoot></tfoot></table>";      
echo "</fieldset>";
CLOSE_BOX();
echo close_body();
?>