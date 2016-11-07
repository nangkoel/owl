


function batal()
{
	//document.getElementById('per').value='';	
	document.getElementById('printContainer').innerHTML='';
        //document.reload();
        //document.location.reload();
}



maxf=0
sekarang=1;
function saveAll(maxRow)
{  
    maxf=maxRow;
    loopsave(1,maxRow);
}

function loopsave(currRow,maxRow)
{
    tk=document.getElementById('tk').value;
    karyawanid=trim(document.getElementById('karyawanid'+currRow).innerHTML);
    
    
            param='karyawanid='+karyawanid+'&tk='+tk;
            param+="&proses=savedata";

           // alert(param);return;
            tujuan = 'sdm_slave_save_prosesAbs.php';//sdm_slave_save_prosesAbs
            post_response_text(tujuan, param, respog);
            document.getElementById('row'+currRow).style.backgroundColor='cyan';
            //lockScreen('wait');

    function respog(){
            if (con.readyState == 4) {
                if (con.status == 200) {
                        busy_off();
                        if (!isSaveResponse(con.responseText)) {
                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                document.getElementById('row'+currRow).style.backgroundColor='red';
                           unlockScreen();
                        }
                        else {
                                document.getElementById('row'+currRow).style.display='none';
                                currRow+=1;
                                sekarang=currRow;
                                if(currRow>maxRow)
                                {
                                        alert('Done');
                                        //document.location.reload();	
                                        batal();
                                        unlockScreen();
                                }  
                                else
                                {
                                        loopsave(currRow,maxRow);
                                }
                        }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
            }
    }		
	
}

