<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script>function submitFile(){
    if(confirm('Are you sure..?')){
    document.getElementById('frm').submit();
    }
}
</script>
<?php
$arr="##listTransaksi##pilUn_1##unitId##method";
include('master_mainMenu.php');
OPEN_BOX();
echo"  <fieldset><legend>Form</legend>
                     <div id=uForm>
                     	<span id=sample><b>".$_SESSION['lang']['absensi']." Uploader. This form must be preceded by a header on the first line</b> <a href=tool_slave_getExample.php?form=ABSENSI target=frame>Click here for example</a></span><br><br>
                                         (File type support only CSV).
                                        <form id=frm name=frm enctype=multipart/form-data method=post action=tool_slave_uploadData.php target=frame>	
                                        <input type=hidden name=jenisdata id=jenisdata value='ABSENSI'>
                                        <input type=hidden name=MAX_FILE_SIZE value=1024000>
                                        File:<input name=filex type=file id=filex size=25 class=mybutton>
                                        Field separated by<select name=pemisah>
                                        <option value=','>, (comma)</option>
                                        <option value=';'>; (semicolon)</option>
                                        <option value=':'>: (two dots)</option>
                                        <option value='/'>/ (devider)</option>
                                        </select>
                                        <input type=button class=mybutton  value=".$_SESSION['lang']['save']." title='Submit this File' onclick=submitFile()>
                                    </form>
 
                                    <iframe frameborder=0 width=800px height=200px name=frame>
                                    </iframe>
                     </div>
                    </fieldset>";

CLOSE_BOX();
 
echo close_body();
?>