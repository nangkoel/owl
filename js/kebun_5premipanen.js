// JavaScript Document
function cariBast(num)
{
		param='method=loadData';
		param+='&page='+num;
		tujuan = 'kebun_slave_5premipanen.php';
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

function loadData()
{
    param='method=loadData';
    tujuan='kebun_slave_5premipanen';
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
    id=document.getElementById('dataid').value;
    kodeorg=document.getElementById('kodeorg').value;
    hasil=document.getElementById('hasil').value;
    lebihbasis=document.getElementById('lebihbasis').value;
    rupiah=document.getElementById('rupiah').value;
    premirajin=document.getElementById('premirajin').value;
    method=trim(document.getElementById('method').value);

    param='id='+id+'&kodeorg='+kodeorg+'&hasil='+hasil+'&lebihbasis='+lebihbasis+'&rupiah='+rupiah+'&premirajin='+premirajin+'&method='+method;
    tujuan = 'kebun_slave_5premipanen.php';
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

function fillField(id,kodeorg,hasil,lebihbasis,rupiah,premirajin)
{
    document.getElementById('dataid').value=id;
    document.getElementById('kodeorg').value=kodeorg;
    document.getElementById('hasil').value=hasil;
    document.getElementById('lebihbasis').value=lebihbasis;
    document.getElementById('rupiah').value=rupiah;
    document.getElementById('premirajin').value=premirajin;
    document.getElementById('method').value="update";
}

function cancelIsi()
{
    document.getElementById('dataid').value='';
    document.getElementById('kodeorg').value='';
    document.getElementById('hasil').value='0';
    document.getElementById('lebihbasis').value='0';
    document.getElementById('rupiah').value='0';
    document.getElementById('premirajin').value='0';
    document.getElementById('method').value="insert";
}

function del(id)
{
	param='method=delete'+'&id='+id;
	//alert(param);
	tujuan='kebun_slave_5premipanen.php';
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
