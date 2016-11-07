<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/kpiData.js'></script>
<script language=javascript1.2 src='js/zTools.js'></script>
<script language=javascript1.2 src='js/zReport.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['kpi']." ".$_SESSION['lang']['input']." ".$_SESSION['lang']['data']);
echo"<fieldset style='width:300px;'>
             <legend>".$_SESSION['lang']['form']."</legend>
              ".$_SESSION['lang']['tahun'].":<input type=text id=tahun class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlenngth=4 size=8>
              <button class=mybutton onclick=getKPIdata()>".$_SESSION['lang']['preview']."</button>  
               <button onclick=\"zExcel(event,'sdm_slave_kpiData.php','##tahun')\" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>   
         </fieldset>";
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend>
          <div id=container></div>
          </fieldset>";
CLOSE_BOX();
echo close_body();
?>