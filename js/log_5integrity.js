/**
 * @author repindra.ginting
 */
function getNotSync()
{
        periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
        kodeorg=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
        param='periode='+periode+'&kodeorg='+kodeorg+'&preview=true';
        tujuan='log_slave_5integrity.php';
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
                                                        document.getElementById('container').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}

function saveNotSync()
{
        periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
        kodeorg=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
        param='periode='+periode+'&kodeorg='+kodeorg+'&method=save';
        tujuan='log_slave_5integrity.php';
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
                                                        document.getElementById('container').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}
