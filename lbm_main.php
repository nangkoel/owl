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
<script language=javascript src='js/zReport.js'></script>
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
function getAfd(obj)
{
       unt=obj.options[obj.selectedIndex].value;
       param='unit='+unt;
       //alert(param);
       tujuan='lbm_slave_sampul.php';
        post_response_text(tujuan+'?proses=getAfdl', param, respog);
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
                        document.getElementById('afdId').innerHTML=con.responseText;
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
	 }  
}
function getKbn(obj)
{
       tipe=obj.options[obj.selectedIndex].value;
       param='tipe='+tipe;
       //alert(param);
       tujuan='lbm_slave_sampul.php';
        post_response_text(tujuan+'?proses=getKbn', param, respog);
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
                        document.getElementById('unit').innerHTML=con.responseText;
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
	 }  
}
function getKbn2(obj)
{
       tipe=obj.options[obj.selectedIndex].value;
       param='tipe='+tipe;
       //alert(param);
       tujuan='lbm_slave_sampul.php';
        post_response_text(tujuan+'?proses=getKbn2', param, respog);
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
                        document.getElementById('unit').innerHTML=con.responseText;
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
	 }  
}
function getKbn3(obj)
{
       tipe=obj.options[obj.selectedIndex].value;
       param='tipe='+tipe;
       //alert(param);
       tujuan='lbm_slave_sampul.php';
        post_response_text(tujuan+'?proses=getKbn3', param, respog);
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
                        document.getElementById('unit').innerHTML=con.responseText;
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
            OPEN_BOX('','LBM');
            echo"<fieldset><legend>".$_SESSION['lang']['navigasi']."</legend>
                 <div id='navcontainer' style='width:200px;height:500px;overflow:scroll;background-color:#FFFFFF;'>";
                if($_SESSION['language']=='ID'){
                  $x=readCountry('config/lbm.lst');
                }
                else{
                   $x=readCountry('config/lbm_en.lst'); 
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
                 <div id='formcontainer' style='width:900px;height:160px;overflow:scroll'></div> 
                 </fieldset>";            
            CLOSE_BOX();  
            OPEN_BOX('','');
            echo"<fieldset><legend>".$_SESSION['lang']['list']." <span id=isiJdlBawah></span></legend>
                 <div id='reportcontainer' style='width:900px;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
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