<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src="js/zTools.js"></script>
<script language=javascript1.2>
function hapusGaji()
{
    periode=getValue('periode');
    karyawanid=getValue('karyawanid');
    komponen=getValue('komponen');
    tipekaryawan=getValue('tipekaryawan');
    param='periode='+periode+'&karyawanid='+karyawanid+'&komponen='+komponen+'&tipekaryawan='+tipekaryawan;
    tujuan='sdm_slave_hapusSlyip.php';
    if(confirm("Delete ?"))
       post_response_text(tujuan, param, respog);		

	
	function respog()
	{
              if(con.readyState==4)
              {
                if (con.status == 200) {
                        busy_off();
                        if (!isSaveResponse(con.responseText)) {
                                alert('ERROR TRANSACTION,\n' + con.responseText);
                        }
                        else {
                                alert(con.responseText);

                        }
                    }
                    else {
                            busy_off();
                           error_catch(con.status);
                    }
              }	 
        }
}
</script>
<?php
include('master_mainMenu.php');
OPEN_BOX('');
$str="select distinct periode from ".$dbname.".sdm_5periodegaji 
     where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc";
$res=mysql_query($str);
$optper='';
while($bar=mysql_fetch_object($res))
{
    $optper.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}
$str="select namakaryawan,karyawanid,subbagian from ".$dbname.".datakaryawan 
    where lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan";
$res=mysql_query($str);
$optkar='<option value=all>'.$_SESSION['lang']['all'].'</option>';
while($bar=mysql_fetch_object($res))
{
    $optkar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan." [".$bar->subbagian."]</option>";
}
#ambil komponen
$str="select id,name from ".$dbname.".sdm_ho_component order by name";
$res=mysql_query($str);
$optkom='<option value=all>'.$_SESSION['lang']['all'].'</option>';
while($bar=mysql_fetch_object($res))
{
    $optkom.="<option value='".$bar->id."'>".$bar->name."</option>";
}
echo"<fieldset style='width:500px;'><legend>Delete Slyp</legend>
    <table>
     <tr><td>".$_SESSION['lang']['namakaryawan']."</td><td><select id=karyawanid>".$optkar."</select></td></tr>
     <tr><td>".$_SESSION['lang']['periode']."</td><td><select id=periode>".$optper."</select></td></tr>
     <tr><td>".$_SESSION['lang']['namakomponen']."</td><td><select id=komponen>".$optkom."</select></td></tr>  
     <tr><td>".$_SESSION['lang']['sistemgaji']."</td><td><select id=tipekaryawan><option value='all'>".$_SESSION['lang']['all']."</option><option value='Bulanan'>".$_SESSION['lang']['bulanan']."</option><option value='Harian'>".$_SESSION['lang']['harian']."</option></select></td></tr>    
     </table>
	
	 <button class=mybutton onclick=hapusGaji()>".$_SESSION['lang']['delete']."</button>
	 </fieldset>";
CLOSE_BOX();
echo close_body();
?>