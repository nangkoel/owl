<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language="javascript1.2">
    function getReport(){
        periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
        kodeorg=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
        jenis=document.getElementById('jenis').options[document.getElementById('jenis').selectedIndex].value;
        param='jenis='+jenis+'&periode='+periode+'&kodeorg='+kodeorg;
        tujuan='sdm_slave_2PremiMandorPanen.php';
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
                                                //alert(con.responseText);
                                                document.getElementById('printContainer').innerHTML=con.responseText;
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
OPEN_BOX('','Premi Mandor/Chenker/Rocorder:');
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 order by namaorganisasi";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optOrg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
$str="select distinct jabatan from ".$dbname.".kebun_premikemandoran order by jabatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optJabatan.="<option value='".$bar->jabatan."'>".$bar->jabatan."</option>";
}
    $str="select distinct periode from ".$dbname.".sdm_5periodegaji  order by periode desc";
    $res=mysql_query($str);
    while($bar=  mysql_fetch_object($res)){
        $optPeriode.="<option value='".$bar->periode."'>".$bar->periode."</option>";
    }
echo"<table>
           <tr><td>".$_SESSION['lang']['periode']."</td><td>:<select id=periode>".$optPeriode."</select></td></tr>
           <tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:<select id=kodeorg>".$optOrg."</select></td></tr>
            <tr><td>".$_SESSION['lang']['jenis']."</td><td>:<select id=jenis>".$optJabatan."</select></td></tr>
             </table>
             <button class=mybutton onclick=getReport()>".$_SESSION['lang']['proses']."</button>";
CLOSE_BOX();
OPEN_BOX('','Result:');

echo"<fieldset><legend>List</legend>
         <div id='printContainer' style='width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
     </fieldset>"; 
CLOSE_BOX();
close_body();
?>