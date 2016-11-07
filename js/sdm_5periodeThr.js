//JS 

function cariBast(num)
{
    param='method=loadData';
    param+='&page='+num;
    tujuan = 'sdm_slave_5periodeThr.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    //displayList();

                                    document.getElementById('container').innerHTML=con.responseText;
                                    //loadData();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}

function simpan()
{
    tahun=document.getElementById('tahun').value;
    perSampai=document.getElementById('perSampai').value;
    perMulai=document.getElementById('perMulai').value;
    perBayar=document.getElementById('perBayar').value;
    agama=document.getElementById('agama').value;
    tgl=document.getElementById('tgl').value;
    method=document.getElementById('method').value;

    if(tahun=='' || perSampai=='' || perMulai==''  || agama==''  || perBayar==''  || tgl=='')
    {
            alert('Field Was Empty');
            return;
    }

    param='tahun='+tahun+'&perSampai='+perSampai+'&perMulai='+perMulai+'&perBayar='+perBayar+'&agama='+agama+'&tgl='+tgl+'&method='+method;
    tujuan='sdm_slave_5periodeThr.php';
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
							cancel();
                                                        loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}
					


function cancel()
{
    document.getElementById('agama').value='Islam';
    document.getElementById('perBayar').value='';
    document.getElementById('tahun').value='';
    document.getElementById('perSampai').value='';
    document.getElementById('perMulai').value='';
    document.getElementById('tgl').value='';
    document.getElementById('method').value='insert';
    document.getElementById('tahun').disabled=false;
    document.getElementById('agama').disabled=false;
}




function loadData () 
{
	param='method=loadData';
	tujuan='sdm_slave_5periodeThr.php';
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
                                   // alert(con.responseText);
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

function edit(tahun,perMulai,perSampai,agama,perBayar,tgl)
{
    document.getElementById('tahun').value=tahun;
    document.getElementById('tahun').disabled=true;
    document.getElementById('agama').value=agama;
    document.getElementById('agama').disabled=true;
     document.getElementById('tgl').value=tgl;
    document.getElementById('perMulai').value=perMulai;
    document.getElementById('perSampai').value=perSampai;
    document.getElementById('perBayar').value=perBayar;
    document.getElementById('method').value='update';
}



function del(kode)
{
	param='method=delete'+'&tahun='+tahun+'&agama='+agama;
	tujuan='sdm_slave_5periodeThr.php';
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
					else 
					{
						loadData();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}




