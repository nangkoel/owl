/* Function getInv
 * Fungsi untuk pop up window, untuk memilih barang
 * I : id target
 * O : Pop up window untuk pencarian barang
 */
function getInv(event,id) {
    var cont = "<fieldset><legend><b>Search</b></legend>";
    cont += "<input id='invSearch' type='text' onkeypress=\"if(getKey(event)==13){searchInv('invSearch','"+id+"');} else {return tanpa_kutip(event)}\" />";
    cont += "<img src='images/search.png' onclick=\"searchInv('invSearch','"+id+"')\" style='cursor:pointer'>";
    cont += "</fieldset>";
    
    cont += "<fieldset><legend><b>Result</b></legend><div id='sResult' style='height:315px;overflow:auto'>";
    cont += "</div></fieldset>";
    showDialog2('Search Inventory',cont,'500','400',event);
}

/* Function searchInv
 * Fungsi untuk mencari barang
 * I : id search text, id target
 * O : Tampilkan hasil pencarian
 */
function searchInv(id,targetId) {
    var sText = document.getElementById(id);
    
    if(sText.value=='' || sText.value.length<3) {
        alert('Kata yang dicari minimal 3 karakter');
        exit;
    }
    
    var param = "keyword="+sText.value;
    param += "&target="+targetId;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById('sResult');
                    res.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('slave_search_barang.php', param, respon);
}

/* Function passValue
 * Fungsi untuk mengirim nilai ke element tertentu
 * I : nilai, id target
 * O : nilai terupdate
 */
function passValue(val,target) {
    var tTarget = document.getElementById(target);
    
    tTarget.value = val;
    closeDialog2();
}