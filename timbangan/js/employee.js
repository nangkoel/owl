nForm = '';   //to store original tab caption
cForm = '';   //to store input original Employee input form when the form changed

function tryFind(event){
    key = getKey(event);
    if (key == 13)
        searchEmployee();
}

function searchEmployee(){
    findtext = document.getElementById('texttosearch').value;
    if (findtext.lastIndexOf('%') > -1)
        alert('% is invalid character');
    else
        if (findtext.lastIndexOf(';') > -1)
            alert('; is invalid character');
        else {
            switch (findtext) {
                case '':
                    alert('No text to find');
                    break;
                case '?':
                    alert('? is invalid character');
                    break;
                default:
                    param = 'texttofind=' + findtext;
                    post_response_text('cari_karyawan.php', param, respon);
            }
        }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('searchresult').innerHTML = con.responseText;
                    document.getElementById('searchresult').style.display = '';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function closeSearch(){
    document.getElementById('searchresult').innerHTML = '';
    document.getElementById('searchresult').style.display = 'none';
}

function previewData(userid, evt){
    param = 'userid=' + userid;
    post_response_text('hr_slave_showEmployeeDetail.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    pos = new Array();
                    pos = getMouseP(evt);
                    document.getElementById('dynamic').innerHTML = con.responseText;
                    document.getElementById('dynamic').style.top = pos[1] + 'px';
                    document.getElementById('dynamic').style.left = '75px';
                    document.getElementById('dynamic').style.display = '';
                    //displayDetail(con.responseText,evt);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function showList(id){
    hideById('EList');
    hideById('newForm');
    //show the last
    showById(id);

}

function isSaveAble(textbox, btn){
    if (trim(textbox.value) != '') {
        document.getElementById(btn).disabled = false;
    }
    else {
        document.getElementById(btn).disabled = true;
    }
}

function rubah(){	if(document.getElementById('check').checked==false){		document.getElementById('note').innerHTML = 'TIDAK';	}
	else
		document.getElementById('note').innerHTML = 'YA';}

function muat(key,field){
    param = 'key='+key+'&field='+field;
    //alert(param);
    post_response_text('load_group.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                	ss=con.responseText.split(",")
                    document.getElementById('jammasuk').value = ss[0];
                    document.getElementById('jamkeluar').value = ss[1];
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function saveEmployee(){
    nik = trim(document.getElementById('nik').value);
    nama = trim(document.getElementById('nama').value);
    sex = trim(document.getElementById('sex').options[document.getElementById('sex').selectedIndex].value);
    titel = trim(document.getElementById('titel').options[document.getElementById('titel').selectedIndex].value);
    tglmasuk = trim(document.getElementById('tglmasuk').value);
    jenisid = trim(document.getElementById('jenisid').options[document.getElementById('jenisid').selectedIndex].text);
    noid = trim(document.getElementById('noid').value);
    tmplahir = trim(document.getElementById('tmplahir').value);
    tgllahir = trim(document.getElementById('tgllahir').value);
    belumkawin = trim(document.getElementById('belumkawin').options[document.getElementById('belumkawin').selectedIndex].text);
    istrisuami = trim(document.getElementById('istrisuami').value);
    tmplahir2 = trim(document.getElementById('tmplahir2').value);
    tgllahir2 = trim(document.getElementById('tgllahir2').value);
    ortuwali = trim(document.getElementById('ortuwali').value);
    pendidikan = trim(document.getElementById('pendidikan').options[document.getElementById('pendidikan').selectedIndex].value);
    divisi = trim(document.getElementById('divisi').options[document.getElementById('divisi').selectedIndex].value);
	dept = trim(document.getElementById('dept').options[document.getElementById('dept').selectedIndex].value);
	group = trim(document.getElementById('group').options[document.getElementById('group').selectedIndex].value);
	kelompok = trim(document.getElementById('kelompok').options[document.getElementById('kelompok').selectedIndex].value);
	bagian = trim(document.getElementById('bagian').options[document.getElementById('bagian').selectedIndex].value);
	jabatan = trim(document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value);
    stkerja = trim(document.getElementById('stkerja').options[document.getElementById('stkerja').selectedIndex].value);
	shift = trim(document.getElementById('shift').options[document.getElementById('shift').selectedIndex].value);
	jammasuk = trim(document.getElementById('jammasuk').value);
	jamkeluar = trim(document.getElementById('jamkeluar').value);
	gajiterima = trim(document.getElementById('gajiterima').value);
	tipegaji = trim(document.getElementById('tipegaji').options[document.getElementById('tipegaji').selectedIndex].text);
	hobi = trim(document.getElementById('hobi').value);
	orpol = trim(document.getElementById('orpol').value);
    pengalaman = trim(document.getElementById('pengalaman').value);
    alasan = trim(document.getElementById('alasan').value);
    krjakhir = trim(document.getElementById('krjakhir').value);
    ketlain = trim(document.getElementById('ketlain').value);
    pengalaman = trim(document.getElementById('pengalaman').value);
    notelp = trim(document.getElementById('notelp').value);
	nohp = trim(document.getElementById('nohp').value);
	if(document.getElementById('check').checked==false){
		document.getElementById('note').value = 'TIDAK';
	}
	else
		document.getElementById('note').value = 'YA';

    note = document.getElementById('note').value;
    var ichars =  /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;

	if (eName == '' || eSex == '' || eBirthday == '' || ePlaceofbirth == '' || eNationality == '' || eMstatus == '' || eTitle == '' || eOrg == '' || ePoh == '' || eSigndate == '' || eEmpltype == '' || eUsercode == '' || eLevel == '' || eLoc == '') {
        alert('Each obligatory field must satisfied');
    }
    else {
        if (confirm('Are you sure ?')) {
            param = 'userid=' + userid + '&name=' + eName;
            param += '&poh=' + ePoh + '&signdate=' + eSigndate;
            param += '&bday=' + eBirthday + '&bloc=' + ePlaceofbirth;
            param += '&idnum=' + eIdnum + '&mail=' + eMail;
            param += '&national=' + eNationality + '&phone=' + ePhone;
            param += '&religion=' + eReligion + '&ethnic=' + eEthnic;
            param += '&usercode=' + eUsercode + '&level=' + eLevel;
            param += '&emplloc=' + eLoc + '&addr=' + eAdd;
            param += '&sex=' + eSex + '&payroll=' + ePayroll;
            param += '&idtype=' + eIdtype + '&empltype=' + eEmpltype;
            param += '&mstatus=' + eMstatus + '&dept=' + edept;
            param += '&title=' + eTitle + '&org=' + eOrg;
			param += '&payrollstart='+ePystart+'&home='+eHome;
			param += '&city='+eCity+'&province='+eProvince;
			param += '&zipcode='+eZipcode+'&addr1='+eAdd1;
			param += '&city1='+eCity1+'&province1='+eProvince1;
			param += '&zipcode1='+eZipcode1+'&etax='+eTax;
			param += '&nicname='+eNicname+'&hobby='+eHobby;
			param += '&height='+eHeight;
 			post_response_text('hr_slave_saveNewEmployee.php', param, respon);
        }

    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    ////FRM is tge name of the tab
                    document.getElementById('userid').value = trim(con.responseText);
                    alert('Saved');
					if(nForm=='')
					   nForm = document.getElementById('tabFRM0').innerHTML;
                    document.getElementById('tabFRM0').innerHTML = eName.substr(0,15);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function cekWaktu1(){
	//re = /^\d{1,2}:\d{2}([ap]m)?$/;
	//re =  /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;  //email validation
	re = /^[a-zA-Z]+\.$/;

	if(document.getElementById('nik').value != '' && !document.getElementById('nik').value.match(re))
	{
		alert("Format Jam Yang Anda Masukkan Salah!!!" + document.getElementById('nik').value);
		document.getElementById('nik').focus();
		return false;
	}
	return true;
}
function isNik() {
    if (document.getElementById('nik').value.search(/^\w+( \w+)?$/) != -1)
        return true;
    else
        return false;
}