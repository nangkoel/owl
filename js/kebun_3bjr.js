maxf=0
sekarang=1;
function saveAll(maxRow)
{  
    maxf=maxRow;
    loopsave(1,maxRow);
}

function loopsave(currRow,maxRow)
{
    
    kdBlok=trim(document.getElementById('kdBlok'+currRow).innerHTML);
    bjr=document.getElementById('bjr').value;
    tahun=document.getElementById('tahun').value;
    param='kdBlok='+kdBlok+'&bjr='+bjr+'&tahun='+tahun;
    param+="&proses=savedata";
    tujuan = 'kebun_slave_save_3bjr.php';
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
                                        document.getElementById('printContainer').innerHTML='';
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

