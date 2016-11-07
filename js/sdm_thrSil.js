function getPer()
{
    tahun=document.getElementById('tahun').value;
    agama=document.getElementById('agama').value;
    param='proses=getPer'+'&tahun='+tahun+'&agama='+agama;
    tujuan='sdm_slave_save_thrSil.php';
    post_response_text(tujuan, param, respog);

    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200)
            {
               // alert(con.responseText);
                arr=con.responseText.split("###");
                busy_off();
                if (!isSaveResponse(con.responseText)) 
                {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else 
                {
                    document.getElementById('per').value=arr[0];
                    document.getElementById('tgl').value=arr[1];
                  
                }
            }
            else 
            {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
}




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
    
    karyawanid=trim(document.getElementById('karyawanid'+currRow).innerHTML);
    kdorg=trim(document.getElementById('kdorg'+currRow).innerHTML);
    per=document.getElementById('per').value;
    jumlah=trim(document.getElementById('jumlah'+currRow).innerHTML);
    tk=document.getElementById('tk').value;
    jumlah=remove_comma_var(jumlah);
    
            param='karyawanid='+karyawanid+'&kdorg='+kdorg+'&per='+per+'&jumlah='+jumlah+'&tk='+tk;
            param+="&proses=savedata";

           // alert(param);return;
            tujuan = 'sdm_slave_save_thrSil.php';
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

