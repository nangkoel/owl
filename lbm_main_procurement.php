<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

echo open_body();
include('master_mainMenu.php');
 
?>
<script language=javascript src='js/zMaster.js'></script> 
<script language=javascript src='js/zSearch.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<script language=javascript src='js/zTools.js'></script>
<!--<script language=javascript src='js/zReport.js'></script>-->
<script language=javascript src='js/lbm_main_procurement.js'></script>
<script language=javascript src='js/log_2kalkulasi_stock.js'></script>

<script language=javascript>
function lempar(dest,title){
    	param='judul='+title;
	tujuan=dest+'.php';
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
                        document.getElementById('formcontainer').innerHTML=con.responseText;
                        document.getElementById('reportcontainer').innerHTML='';
                        document.getElementById('isiJdlBawah').innerHTML=title;
                        document.getElementById('lyrPertama').style.display='none';
                        document.getElementById('lyrKedua').style.display='none';
                        document.getElementById('mainPrint').style.display='block';
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
	 }        
}
function ubah(obj)
{
    if(obj.style.backgroundColor=='darkgreen'){
      obj.style.backgroundColor='#FFFFFF';
      obj.style.color='#000000';
      obj.style.fontWeight='normal';
    }
    else{
       obj.style.backgroundColor='darkgreen'; 
       obj.style.color='#FFFFFF';
       obj.style.fontWeight='bolder';
    }
}

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
            OPEN_BOX('','LBM-PROCUREMENT');
            echo"<fieldset><legend>".$_SESSION['lang']['navigasi']."</legend>
                 <div id='navcontainer' style='width:285px;height:500px;overflow:scroll;background-color:#FFFFFF;'>";
                 $x=readCountry('config/lbm_proc.lst');
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
                 <div id='formcontainer' style='width:900px;height:120px;overflow:scroll'></div> 
                 </fieldset>";            
            CLOSE_BOX();  
            OPEN_BOX('','');
            echo"<div id=mainPrint><fieldset><legend><span id=isiJdlBawah></span></legend>
                 <div id='reportcontainer' style='width:900px;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
                 </fieldset></div>";   
            echo"<div id=lyrPertama style=display:none;>
                 <fieldset><legend><span id=isiJdlBawah1></span></legend>
                 <div id='reportcontainer1' style='width:900px;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
                 </fieldset>
                 </div>";
            echo"<div id=lyrKedua style=display:none;>
                 <fieldset><legend><span id=isiJdlBawah2></span></legend>
                 <div id='reportcontainer2' style='width:900px;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
                 </fieldset>
                 </div>";
            CLOSE_BOX();              
        echo"</td>
        </tr>
        </tbody>
     <tfoot>
     </tfoot>
     </table>";
echo close_body();
?>