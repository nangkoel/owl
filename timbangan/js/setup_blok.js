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
        exit;
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
        exit;
    }
    
    if(afdeling.options.length>0) {
        var param = "kebun="+kebun.options[kebun.options.selectedIndex].value+
            "&afdeling="+afdeling.options[afdeling.options.selectedIndex].value;
    } else {
        alert('Tidak ada afdeling pada kebun tersebut');
        exit;
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