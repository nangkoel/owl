// JavaScript Document

function loadData()
{
    param='method=loadData';
    tujuan='pabrik_slave_5ketetapansuhu';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}

function simpan() {
    kodeorg=document.getElementById('kodeorg').value;
    kodetangki=document.getElementById('kodetangki').value;
    suhu=document.getElementById('suhu').value;
    kepadatan=document.getElementById('kepadatan').value;
    ketetapan=document.getElementById('ketetapan').value;
    method=trim(document.getElementById('method').value);

    param='kodeorg='+kodeorg+'&kodetangki='+kodetangki+'&suhu='+suhu+'&kepadatan='+kepadatan+'&ketetapan='+ketetapan+'&method='+method;
    tujuan = 'pabrik_slave_5ketetapansuhu.php';
    post_response_text(tujuan, param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    loadData();
                    cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(fileTarget+'.php', param, respon);
}

function fillField(kodeorg,kodetangki,suhu,kepadatan,ketetapan)
{
    document.getElementById('kodeorg').value=kodeorg;
    document.getElementById('kodeorg').disabled=true;
    if (document.getElementById('kodetangki').value==""){
        getTangki();
    }
    document.getElementById('kodetangki').value=kodetangki;
    document.getElementById('kodetangki').disabled=true;
    document.getElementById('suhu').value=suhu;
    document.getElementById('suhu').disabled=true;
    document.getElementById('kepadatan').value=kepadatan;
    document.getElementById('ketetapan').value=ketetapan;
    document.getElementById('method').value="update";
}

function cancelIsi()
{
    document.getElementById('kodeorg').value='';
    document.getElementById('kodeorg').disabled=false;
    document.getElementById('kodetangki').value='';
    document.getElementById('kodetangki').disabled=false;
    document.getElementById('suhu').value='0';
    document.getElementById('suhu').disabled=false;
    document.getElementById('kepadatan').value='0';
    document.getElementById('ketetapan').value='0';
    document.getElementById('method').value="insert";
}

function del(kodeorg,kodetangki,suhu)
{
	param='method=delete'+'&kodeorg='+kodeorg+'&kodetangki='+kodetangki+'&suhu='+suhu;
	//alert(param);
	tujuan='pabrik_slave_5ketetapansuhu.php';
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

function getTangki()
{
	kodeorg=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
	param='kodeorg='+kodeorg+'&method=getTangki';
	tujuan='pabrik_slave_5ketetapansuhu.php';
	post_response_text(tujuan+'?method=getTangki', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('kodetangki').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
