/* Function getAfdeling
 * Fungsi untuk mengambil data afdeling sesuai dengan kebunnya
 * I : element kebun,id elemen afdeling
 * P : Ajax untuk mengambil data yang sesuai
 * O : Drop down afdeling terisi dengan data yang sesuai
 */
function getAfdeling(currEls,targetId) {
    var kebun = currEls;
    var afdeling = document.getElementById(targetId);
    
    // If blank, quit
    if(kebun.options[kebun.options.selectedIndex].value=='') {
        return;
    }
    
    // Clear Afdeling
    afdeling.options.length=0;
    
    var param = "kebun="+kebun.options[kebun.options.selectedIndex].value+
        "&afdelingId="+targetId;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    eval(con.responseText);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_blok_afdeling.php', param, respon);
}

/* Function showData
 * Fungsi untuk menampilkan data sesuai filter
 * I : n/a
 * P : Ajax untuk mengambil data yang sesuai
 * O : Menampilkan tabel sesuai dengan data yang ada
 */
function showData() {
    var tabId = document.getElementById('blokTable');
    var kebun = document.getElementById('sKebun');
    var afdeling = document.getElementById('sAfdeling');
    var formBlok = document.getElementById('formBlok');
    
    if(kebun.options[kebun.options.selectedIndex].value=='') {
        alert('Kebun harus dipilih');
        return;
    }
    
    if(afdeling.options.length>0) {
        var param = "kebun="+kebun.options[kebun.options.selectedIndex].value+
            "&afdeling="+afdeling.options[afdeling.options.selectedIndex].value;
    } else {
        alert('Tidak ada afdeling pada kebun tersebut');
        return;
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    tabId.innerHTML = con.responseText;
                    updBlokDropdown();
                    formBlok.style.display = 'block';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_blok_data.php', param, respon);
}

function updBlokDropdown() {
    var kodeorg = document.getElementById('kodeorg');
    var afdeling = document.getElementById('sAfdeling');
    var param = "afdeling="+afdeling.options[afdeling.options.selectedIndex].value;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    kodeorg.options.length=0;
                    eval(con.responseText);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_blok_blokdd.php', param, respon);
}

/*
 Function editRow
 Fungsi untuk passing parameter dari table ke field
 num = Nomor Urut Baris
 field = list dari field dengan format '##field1##field2'
 value = list dari nilai yang di pass dengan format '##value1##value2'
*/
function editRow(num,field,value,freeze) {
	var fieldJs = field.split("##");
	// Extract Updated Value
	value = '';
	for(i=1;i<fieldJs.length;i++) {
		var tmp = document.getElementById(fieldJs[i]+"_"+num);
		if(tmp)
			value += "##"+tmp.getAttribute('value');
	}
	
	var valueJs = value.split("##");
	var add = document.getElementById('add');
	var edit = document.getElementById('edit');
	var currRow= document.getElementById('currRow');
	
	if(freeze==undefined) {
		freeze = false;
	} else if(freeze) {
		var freezed = freeze.split('##');
	}
	
	// Pass Parameter
	for(i=1;i<fieldJs.length;i++) {
		var tmp = document.getElementById(fieldJs[i]);
		if(tmp.options) {
			for(j=0;j<tmp.options.length;j++) {
				if(tmp.options[j].value==valueJs[i])
					tmp.options[j].selected=true;
			}
		} else if(tmp.getAttribute('type')=='checkbox') {
			if(valueJs[i]=='1') {
				tmp.checked = true;
			} else {
				tmp.checked = false;
			}
		} else {
			tmp.value = valueJs[i];
		}
		//alert("'"+valueJs[i]+"'");
	}
        
        // Tahun Tanam Hidden
        var ttCurr = document.getElementById('tahuntanamCurr');
        ttCurr.value = document.getElementById('tahuntanam_'+num).getAttribute('value');
	
	// Freeze Coresponden field
	if(freeze) {
		for(i in freezed) {
			document.getElementById(freezed[i]).setAttribute('disabled','disabled');
		}
	}
	
	// Update Current Edited Row
	currRow.value = num;
	
	// Display Edit & Hide Add
	add.style.display = 'none';
	edit.style.display = '';
}
function itungUnplan(){
    cad=document.getElementById('cadangan').value;//
    umu=document.getElementById('umum').value;
    klm=document.getElementById('kolam').value;
    jln=document.getElementById('jalan').value;
    pbrik=document.getElementById('pabrik').value;
    kntr=document.getElementById('kantor').value;
    rmh=document.getElementById('rumah').value;
    sngi=document.getElementById('sungai').value;
    rndhn=document.getElementById('rendahan').value;
    oksi=document.getElementById('okupasi').value;
    tot=parseFloat(cad)+parseFloat(umu)+parseFloat(klm)+parseFloat(jln)+parseFloat(pbrik)+parseFloat(kntr)+parseFloat(rmh)+parseFloat(sngi)+parseFloat(rndhn)+parseFloat(oksi);
    document.getElementById('luasareanonproduktif').value=tot;
}