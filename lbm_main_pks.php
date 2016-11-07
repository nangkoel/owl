<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src=js/zMaster.js></script> 
<script language=javascript src=js/zSearch.js></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/lbm_main_pks.js'></script>
<script language=javascript>

</script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
//echo "qwe";
//exit;

echo"<table>
     <thead>
     </thead>
        <tbody>
        <tr>
            <td valign='top'>";
            OPEN_BOX('','LBM-PKS');
            echo"<fieldset><legend>".$_SESSION['lang']['navigasi']."</legend>
                 <div id='navcontainer' style='width:200px;height:500px;overflow:scroll;background-color:#FFFFFF;'>";
                if($_SESSION['language']=='ID'){
                  $x=readCountry('config/lbm_pks.lst');
                 }
                else{
                   $x=readCountry('config/lbm_pks_en.lst'); 
                }
                foreach($x as $bar=>$val)
                 {                    
                     echo "<a onmouseover=ubah(this) onmouseout=ubah(this) style='font-size:10px;cursor:pointer;' onclick=\"lempar('".$val[1]."','".$val[2]."');\" title='".$val[2]."'>".$val[0]."</a><br>";               
                 }
                echo"</div>
                    </fieldset>";
            CLOSE_BOX();   
            
        echo"</td><td>";
            OPEN_BOX('','');
            echo"<fieldset><legend>".$_SESSION['lang']['form']."</legend>
                 <div id='formcontainer' style='width:900px;height:150px;overflow:scroll'></div> 
                 </fieldset>";            
            CLOSE_BOX();  
            OPEN_BOX('','');
            echo"<fieldset><legend>".$_SESSION['lang']['list']." <span id=isiJdlBawah></span></legend>
                 <div id='reportcontainer' style='width:900px;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
                    <div id='lyrSatu' style='overflow:auto; height:350px; max-width:1220px;'>
                    </div>
                    <div id='lyrDua' style='overflow:auto; height:350px; max-width:1220px;'>
                    <div>
                 </fieldset>";            
            CLOSE_BOX();              
        echo"</td>
        </tr>
        </tbody>
     <tfoot>
     </tfoot>
     </table>";
echo close_body();
?>