/* Function genTabelKary
 * Fungsi untuk menampilkan list karyawan sesuai dengan hasil pencarian
 * I : ID Container tujuan
 * P : Ajax untuk ekstraksi data dan generate tabel
 * O : Tabel ditampilkan pada container yang dituju
 */
function genTabelKary(targetId) {
    var targetDiv = document.getElementById(targetId);
    var sNIK = document.getElementById('sNIK');
    var sNama = document.getElementById('sNama');
    
    var param = "nik="+sNIK.value+"&nama="+sNama.value;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    targetDiv.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_karyawanadmin.php', param, respon);
}

/* Function showManage
 * Fungsi untuk pop up form manajemen table
 * I : mode awal manajemen
 * P : Ajax menyiapkan keseluruhan halaman manajemen table
 * O : Halaman manajemen table
 */
function showManage(mode,num,event) {
    var idK = document.getElementById('karyawanid_'+num);
    var param = "mode="+mode+"&num="+num+"&karyawanid="+idK.value;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    showDialog1('Manajemen Personalia',con.responseText,'800','400',event);
                    var dialog = document.getElementById('dynamic1');
                    dialog.style.top = '10%';
                    dialog.style.left = '15%';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_karyawanadmin_detail.php', param, respon);
}