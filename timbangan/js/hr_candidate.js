/*
 * @uthor:nangkoel@gmail.com
 * Indonesia 2009
 */
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//title/jabatan
nForm = '';//to store original tab caption
cForm = '';//to store input original Employee input form when the form changed
//===========new Candidate
function showList(id){
    hideById('EList');
    hideById('newForm');
    //show the last	
    showById(id);
    
}

function loadCandidateList(id)
{
	getCandidateList(id);
	showList(id);
}

function newCandidate(id)
{
   clearFrm();
   showList(id);	
}

function getCandidateList(id)
{
	param='layer='+id;
	post_response_text('hr_slave_GetCandidateList.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
					document.getElementById(id).innerHTML=con.responseText;
				}
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}
function goTo(x,id)
{
	param='layer='+id;
	post_response_text('hr_slave_GetCandidateList.php?offset='+x, param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
					document.getElementById(id).innerHTML=con.responseText;
				}
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}
function isSaveAble(textbox, btn){
    if (trim(textbox.value) != '') {
        document.getElementById(btn).disabled = false;
    }
    else {
        document.getElementById(btn).disabled = true;
    }
}

function saveCandidate(){
    userid = trim(document.getElementById('candid').value);
    eName  = trim(document.getElementById('eName').value);
    eBirthday = trim(document.getElementById('eBirthday').value);
    ePlaceofbirth = trim(document.getElementById('ePlaceofbirth').value);
    eReference    =trim(document.getElementById('eReference').value);
	eReferenceRel =trim(document.getElementById('eReference_relation').value);
	eLetterdate   =trim(document.getElementById('eLetterdate').value);
	eIdnum = trim(document.getElementById('eIdnum').value);
    eMail  = trim(document.getElementById('eMail').value);
    eNationality  = trim(document.getElementById('eNationality').value);
    ePhone = trim(document.getElementById('ePhone').value);
    eReligion = trim(document.getElementById('eReligion').value);
    eEthnic   = trim(document.getElementById('eEthnic').value);
    eLevel = trim(document.getElementById('eLevel').value);
    eAdd   = trim(document.getElementById('eAdd').value);
    eHome   = trim(document.getElementById('eHome').value);
	
    eSex     = trim(document.getElementById('eSex').options[document.getElementById('eSex').selectedIndex].value);
    eIdtype  = trim(document.getElementById('eIdtype').options[document.getElementById('eIdtype').selectedIndex].text);
    eEmpltype= trim(document.getElementById('eEmpltype').options[document.getElementById('eEmpltype').selectedIndex].text);
    eMstatus = trim(document.getElementById('eMstatus').options[document.getElementById('eMstatus').selectedIndex].value);
    eDept    = trim(document.getElementById('eDept').options[document.getElementById('eDept').selectedIndex].value);
    eTitle   = trim(document.getElementById('eTitle').options[document.getElementById('eTitle').selectedIndex].value);
    
	eNicname = trim(document.getElementById('eNicname').value);
	eHobby   = trim(document.getElementById('eHobby').value);
	eXpsal   = trim(document.getElementById('eXpsal').value);
	eHeight   = trim(document.getElementById('eHeight').value);
	
    eCity  = trim(document.getElementById('eCity').value);
    eProvince   = trim(document.getElementById('eProvince').value);
    eZipcode  = trim(document.getElementById('eZipcode').value);
	
    eCity1  = trim(document.getElementById('eCity1').value);
    eProvince1   = trim(document.getElementById('eProvince1').value);
    eZipcode1  = trim(document.getElementById('eZipcode1').value);
    eAdd1  = trim(document.getElementById('eAdd1').value);	
	eTax   = trim(document.getElementById('npwp').value);			
    emerdate=trim(document.getElementById('merdate').value);
	emerdate=emerdate==''?'00-00-0000':emerdate;
    idexpire=trim(document.getElementById('idexpire').value);
	idexpire=idexpire==''?'00-00-0000':idexpire;	
    eRecname  = trim(document.getElementById('eRecname').options[document.getElementById('eRecname').selectedIndex].value);	
	ePrefloc='';
	try {
		ePrefloc = trim(document.getElementById('ePrefloc').options[document.getElementById('ePrefloc').selectedIndex].value);
	}
	catch(e)
	{
		alert('Prefered Test location is obligatory');
	}
					   
	if (idexpire=='00-00-0000' ||ePrefloc=='' || eName == '' || eAdd == '' || ePhone == '' || eBirthday == '' || eLetterdate == '' || eIdnum == '') {
        alert('Each obligatory field must satisfied');
    }
    else {
        if (confirm('Are you sure ?')) {
            param = 'userid=' + userid + '&name=' + eName;
            param += '&letterdate=' + eLetterdate;
            param += '&bday=' + eBirthday + '&bloc=' + ePlaceofbirth;
            param += '&idnum=' + eIdnum + '&mail=' + eMail;
            param += '&national=' + eNationality + '&phone=' + ePhone;
            param += '&religion=' + eReligion + '&ethnic=' + eEthnic;
            param += '&level=' + eLevel+'&title=' + eTitle;
            param += '&reference=' + eReference + '&addr=' + eAdd;
            param += '&sex=' + eSex + '&reference_relation=' + eReferenceRel;
            param += '&idtype=' + eIdtype+'&empltype='+eEmpltype;
            param += '&mstatus=' + eMstatus + '&dept=' + eDept;
            param += '&city=' + eCity+'&province='+eProvince;
			param += '&zipcode=' + eZipcode+'&addr1='+eAdd1;
            param += '&city1=' + eCity1+'&province1='+eProvince1;
			param += '&zipcode1=' + eZipcode1+'&npwp='+eTax;
			param += '&home='+eHome+'&nicname='+eNicname;
			param += '&hobby='+eHobby+'&expsal='+eXpsal;
			param += '&height='+eHeight+'&emerdate='+emerdate;
			param += '&ploc='+ePrefloc+'&recname='+recname;
			param += '&idexpire='+idexpire;
		   //alert(param);
		    post_response_text('hr_slave_saveNewCandidate.php', param, respon);
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
                    document.getElementById('candid').value = trim(con.responseText);
                    alert('Saved');
					if (nForm == '') {
						nForm = document.getElementById('tabFRM0').innerHTML;
					}
                      document.getElementById('tabFRM0').innerHTML =  eName.substr(0,15);
				}
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearFrm(){
	document.getElementById('tabFRM0').innerHTML = 'NewCandidate';
    if (cForm != '') {
        //FRM is the name of tab
        document.getElementById('contentFRM0').innerHTML = cForm;
        //tabFRM0 is tab caption
    }

        document.frm1.reset();
        document.getElementById('candid').value = '';//makesure userid is reset
        document.getElementById('saveBtn1').disabled = true;
        document.frm2.reset();
        document.getElementById('saveBtn2').disabled = true;
        document.getElementById('experiencelist').innerHTML = '';
        document.frm3.reset();
        document.getElementById('saveBtn3').disabled = true;
        document.getElementById('schoolslist').innerHTML = '';
        document.frm4.reset();
        document.getElementById('saveBtn4').disabled = true;
        document.getElementById('courselist').innerHTML = '';
        document.frm5.reset();
        document.getElementById('saveBtn5').disabled = true;
        document.getElementById('famlist').innerHTML = '';
        document.frm6.reset();
        document.getElementById('frameF').src = '';
    
}

function saveExperience(){
    userid = trim(document.getElementById('candid').value);
    if (userid == '') {
        alert('Please choose Employee');
    }
    else {
        lastcom = trim(document.getElementById('excom').value);
        lastitle = trim(document.getElementById('extitle').value);
        startm = trim(document.getElementById('startm').options[document.getElementById('startm').selectedIndex].value);
        starty = trim(document.getElementById('starty').options[document.getElementById('starty').selectedIndex].value);
        endm = trim(document.getElementById('endm').options[document.getElementById('endm').selectedIndex].value);
        endy = trim(document.getElementById('endy').options[document.getElementById('endy').selectedIndex].value);
        exnote = trim(document.getElementById('exnote').value);
		exphone = trim(document.getElementById('exphone').value);
        startdate = starty + '-' + startm + '-01';
        enddate = endy + '-' + endm + '-28';
        if (enddate <= startdate) {
            alert('Periode is incorrect');
        }
        else {
            if (confirm('Are you sure...?')) {
                param = 'company=' + lastcom + '&title=' + lastitle;
                param += '&startdate=' + startdate + '&endate=' + enddate;
                param += '&note=' + exnote + '&userid=' + userid;
				param += '&phone='+exphone;
                post_response_text('hr_slave_saveCandidateExperince.php', param, respon);
            }
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
                    document.getElementById('experiencelist').innerHTML = document.getElementById('eName').value + ' Experience(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function saveSchools(){
    userid = trim(document.getElementById('candid').value);
    if (userid == '') {
        alert('Please choose Employee');
    }
    else {
        school = trim(document.getElementById('school').value);
        address = trim(document.getElementById('address').value);
        startm = trim(document.getElementById('scstartm').options[document.getElementById('scstartm').selectedIndex].value);
        starty = trim(document.getElementById('scstarty').options[document.getElementById('scstarty').selectedIndex].value);
        endm = trim(document.getElementById('scendm').options[document.getElementById('scendm').selectedIndex].value);
        endy = trim(document.getElementById('scendy').options[document.getElementById('scendy').selectedIndex].value);
        faculty = trim(document.getElementById('faculty').options[document.getElementById('faculty').selectedIndex].value);
        grade = trim(document.getElementById('grade').options[document.getElementById('grade').selectedIndex].value);
 		major = trim(document.getElementById('major').options[document.getElementById('major').selectedIndex].value);
		scorr = trim(document.getElementById('scorr').value);
        startdate = starty + '-' + startm + '-01';
        enddate = endy + '-' + endm + '-28';
        if (enddate <= startdate) {
            alert('Period is incorrect');
        }
        else 
            if (school == '' || address == '' || grade == '') {
                alert('Obligatory fields must setisfied');
            }
            else {
                if (confirm('Are you sure...?')) {
                    param = 'school=' + school + '&add=' + address;
                    param += '&startdate=' + startdate + '&endate=' + enddate;
                    param += '&faculty=' + faculty + '&userid=' + userid;
					param += '&grade='+grade+'&major='+major+'&scorr='+scorr;
					post_response_text('hr_slave_saveCandidateSchool.php', param, respon);
                }
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
                    document.getElementById('schoolslist').innerHTML = document.getElementById('eName').value + ' School(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function saveCourses(){
    userid = trim(document.getElementById('candid').value);
    if (userid == '') {
        alert('Please choose Employee');
    }
    else {
        provider = trim(document.getElementById('provider').value);
        _location = trim(document.getElementById('location').value);
        subject = trim(document.getElementById('subject').value);
        sponsor = trim(document.getElementById('sponsor').value);
        startm = trim(document.getElementById('curstartm').options[document.getElementById('curstartm').selectedIndex].value);
        starty = trim(document.getElementById('curstarty').options[document.getElementById('curstarty').selectedIndex].value);
        endm = trim(document.getElementById('curendm').options[document.getElementById('curendm').selectedIndex].value);
        endy = trim(document.getElementById('curendy').options[document.getElementById('curendy').selectedIndex].value);
        certified = trim(document.getElementById('certified').options[document.getElementById('certified').selectedIndex].value);
        startdate = starty + '-' + startm + '-01';
        enddate = endy + '-' + endm + '-28';
        if (enddate <= startdate) {
            alert('Period is incorrect');
        }
        else 
            if (provider == '' || _location == '' || subject == '') {
                alert('Obligatory fields must setisfied');
            }
            else {
                if (confirm('Are you sure...?')) {
                    param = 'provider=' + provider + '&location=' + _location;
                    param += '&subject=' + subject + '&sponsor=' + sponsor;
                    param += '&startdate=' + startdate + '&endate=' + enddate;
                    param += '&certified=' + certified + '&userid=' + userid;
                    post_response_text('hr_slave_saveCandidateCourse.php', param, respon);
                }
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
                    document.getElementById('courselist').innerHTML = document.getElementById('eName').value + ' Course(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

//family
function saveFamily(){
    userid = trim(document.getElementById('candid').value);
    if (userid == '') {
        alert('Please choose Employee');
    }
    else {
        famname = trim(document.getElementById('famname').value);
        famrelation = trim(document.getElementById('famrelation').options[document.getElementById('famrelation').selectedIndex].value);
        faminsurance = trim(document.getElementById('faminsurance').options[document.getElementById('faminsurance').selectedIndex].value);
        famsex = trim(document.getElementById('famsex').options[document.getElementById('famsex').selectedIndex].value);
        fambirthdate = trim(document.getElementById('fambirthdate').value);
        famadd = trim(document.getElementById('famadd').value);
        famphone = trim(document.getElementById('famphone').value);
        currentjob = trim(document.getElementById('currentjob').value);
        if (famname == '' || famrelation == '' || faminsurance == '' || fambirthdate == '') {
            alert('Obligatory fields must setisfied');
        }
        else {
            if (confirm('Are you sure...?')) {
                param = 'famname=' + famname + '&famrelation=' + famrelation;
                param += '&faminsurance=' + faminsurance + '&famsex=' + famsex;
                param += '&fambirthdate=' + fambirthdate + '&famadd=' + famadd;
                param += '&famphone=' + famphone + '&userid=' + userid+'&currentjob+='+currentjob;
                post_response_text('hr_slave_saveCandidateFamily.php', param, respon);
            }
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
                    document.getElementById('famlist').innerHTML = document.getElementById('eName').value + ' Families: <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function savePhoto(){
    obj = document.getElementById('fileField1');
    if (obj.value == '') {
        alert('Please pick up a photo');
    }
    else 
        if (obj.value.toLowerCase().lastIndexOf('.jp') == -1) {
            alert('Filetype does not support');
        }
        else {
            userid = trim(document.getElementById('candid').value);
            document.getElementById('puserid').value = userid;
            document.getElementById('frm6').submit();
        }
}

function uploadSelesai(){
    document.getElementById('frameF').style.height = '150px';
    alert('Photo Changed');
}

function uploadGagal(x){
    alert('Upload Failed' + x);
    document.getElementById('frm6').reset();
}

function previewData(userid, evt){
    param = 'userid=' + userid;
    post_response_text('hr_slave_showCandidateDetail.php', param, respon);
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
function printPDF(userid, evt){
    param = 'userid=' + userid;
    pos = new Array();
    pos = getMouseP(evt);
    post_response_text('hr_slaveCandidateImagePDF.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
					if(trim(con.responseText)!='')
					  path=trim(con.responseText);
					else
					  path='';
					    
					win="<img src=images/closebig.gif align=right onclick=hideById('pdf'); title='Close detail' class=closebtn onmouseover=\"this.src='images/closebigon.gif';\" onmouseout=\"this.src='images/closebig.gif';\"><br><br>";
					win+="<iframe height=450px width=100%  frameborder=0 src='hr_slave_showCandidatePrintPDF.php?userid="+userid+"&path="+path+"'></iframe>"
				    document.getElementById('pdf').innerHTML = win;
				    document.getElementById('pdf').style.top = pos[1] + 'px';
				    document.getElementById('pdf').style.left = '75px';
				    document.getElementById('pdf').style.display = '';              }
		          }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	

}


function closeDetail(){
    document.getElementById('dynamic').innerHTML = '';
    document.getElementById('dynamic').style.display = 'none';
}

function editDetail(userid, name){
    param = 'userid=' + userid;
    if(cForm=='')
	    cForm = document.getElementById('contentFRM0').innerHTML;

    post_response_text('hr_slave_getCandidateForEdit.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    //FRM is tge name of the tab
                    document.getElementById('contentFRM0').innerHTML = con.responseText;
                    document.getElementById('tabFRM0').innerHTML = name;
                    showList('newForm');
                    loadFam(userid);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function loadFam(userid){
    param = 'userid=' + userid;
    post_response_text('hr_slave_saveCandidateFamily.php', param, respon);
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('famlist').innerHTML = document.getElementById('eName').value + ' Families: <hr>' + con.responseText;
                    loadExper(userid);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function loadExper(userid){
    param = 'userid=' + userid;
    post_response_text('hr_slave_saveCandidateExperince.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('experiencelist').innerHTML = document.getElementById('eName').value + ' Experience(s): <hr>' + con.responseText;
                    loadEducation(userid);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function loadEducation(userid){
    param = 'userid=' + userid;
    post_response_text('hr_slave_saveCandidateSchool.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('schoolslist').innerHTML = document.getElementById('eName').value + ' School(s): <hr>' + con.responseText;
                    loadCourse(userid);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function loadCourse(userid){
    param = 'userid=' + userid;
    post_response_text('hr_slave_saveCandidateCourse.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('courselist').innerHTML = document.getElementById('eName').value + ' Course(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

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
                    post_response_text('hr_slave_searchCandidate.php', param, respon);
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

function deleteCourse(candid,provider,subject)
{
	param='userid='+candid+'&provider='+provider+'&subject='+subject+'&del=true';
    if(confirm('Are you sure..?'))
	   post_response_text('hr_slave_saveCandidateCourse.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('courselist').innerHTML = document.getElementById('eName').value + ' Course(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}

function deleteExpr(candid,corp,startx)
{
	param='userid='+candid+'&company='+corp+'&startdate='+startx+'&del=true';
    if(confirm('Are you sure..?'))
	   post_response_text('hr_slave_saveCandidateExperince.php', param, respon);
   function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('experiencelist').innerHTML = document.getElementById('eName').value + ' Experience(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}

function deleteSchool(candid,school,startx,grade)
{
	param='userid='+candid+'&school='+school+'&startdate='+startx+'&grade='+grade+'&del=true';
    if(confirm('Are you sure..?'))
	   post_response_text('hr_slave_saveCandidateSchool.php', param, respon);
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('schoolslist').innerHTML = document.getElementById('eName').value + ' School(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}

function deleteFamily(candid,relation,name)
{
	param='userid='+candid+'&famrelation='+relation+'&famname='+name+'&del=true';
	if(confirm('Are you sure..?'))
	   post_response_text('hr_slave_saveCandidateFamily.php', param, respon);
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('famlist').innerHTML = document.getElementById('eName').value + ' School(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function saveRecShedule()
{
	perx=document.getElementById('period');
	period  =trim(perx.options[perx.selectedIndex].value);
	recname =trim(document.getElementById('name').value);
	city	=trim(document.getElementById('city').value);
	tgl		=trim(document.getElementById('tgl').value);
	active  =document.getElementById('active').checked?1:0;
	//alert(active);
	if(period=='')
	  alert('Period is empty');
	else if(recname=='') 
	  alert('Recruitment name is empty'); 
	else if(city=='')
	  alert('Place/City is empty'); 
	else if(tgl=='')
	  alert('Date is empty');
	else{
		param='period='+period+'&recname='+recname;
		param+='&city='+city+'&tgl='+tgl+'&active='+active;
		//alert(param);
		post_response_text('hr_slave_saveRecSchedule.php', param, respon);		
	} 		   
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    perx.disabled=true;
					document.getElementById('name').disabled=true;
					document.getElementById('active').disabled=true;
					document.getElementById('result').innerHTML = 'Scedule(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
          }
    	 }    
}
function deleteSchedule(period,recname,city)
{
		param='period='+period+'&recname='+recname;
		param+='&city='+city+'&del=true';
		if(confirm('Are you sure..?'))
		post_response_text('hr_slave_saveRecSchedule.php', param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
					document.getElementById('result').innerHTML = 'Scedule(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
          }
    	 }    
}

function clearFormRecShedule()
{
	perx=document.getElementById('period');
	perx.options[perx.selectedIndex].value='';
	document.getElementById('name').value='';	
	tabAction(document.getElementById('tabT0'),0,'T',1);
    perx.disabled=false;
	document.getElementById('name').disabled=false;
	document.getElementById('active').disabled=false;
	document.getElementById('city').value='';

}

function saveFacancies()
{
	Orecname=document.getElementById('recname');
	Otitle=document.getElementById('title');
	Iremark=document.getElementById('remark');
	
	recname=Orecname.options[Orecname.selectedIndex].text;
	title  =Otitle.options[Otitle.selectedIndex].value;
	remark =Iremark.value;
	
	Ograde		=document.getElementById('gradeComp');
	OgradeOpt	=document.getElementById('gradeOpt');
	Ofaculty	=document.getElementById('facultyComp');
	OfacultyOpt	=document.getElementById('facultyOpt');	
	Omajor		=document.getElementById('majorComp');	
	OmajorOpt	=document.getElementById('majorOpt');
	Iscore		=document.getElementById('scoreComp');
	IscoreOpt	=document.getElementById('scoreOpt');
	Iheight		=document.getElementById('heightComp');
	IheightOpt	=document.getElementById('heightOpt');
	Osex		=document.getElementById('sexComp');
	OsexOpt		=document.getElementById('sexOpt');
	Iage		=document.getElementById('ageComp');
	IageOpt		=document.getElementById('ageOpt');
	Icity		=document.getElementById('cityComp');
	IcityOpt	=document.getElementById('ePrefloc');
	Omstatus	=document.getElementById('mstatusComp');
	OmstatusOpt	=document.getElementById('mstatusOpt');	
	
	_Ograde    = Ograde.options[Ograde.selectedIndex].value;	
	_OgradeOpt = OgradeOpt.options[OgradeOpt.selectedIndex].value;	
	_Omajor    = Omajor.options[Omajor.selectedIndex].value;	
	_OmajorOpt = OmajorOpt.options[OmajorOpt.selectedIndex].value;	
	_Ofaculty    = Ofaculty.options[Ofaculty.selectedIndex].value;	
	_OfacultyOpt = OfacultyOpt.options[OfacultyOpt.selectedIndex].value;	
	_Iscore     = Iscore.options[Iscore.selectedIndex].value;	
	_IscoreOpt  = IscoreOpt.value;	
	_Iheight    = Iheight.options[Iheight.selectedIndex].value;	
	_IheightOpt = IheightOpt.value;	
	_Osex       = Osex.options[Osex.selectedIndex].value;	
	_OsexOpt    = OsexOpt.options[OsexOpt.selectedIndex].value;	
	_Iage    	= Iage.options[Iage.selectedIndex].value;	
	_IageOpt 	= IageOpt.value;	
	_Icity    	= Icity.options[Icity.selectedIndex].value;	
	_IcityOpt 	= IcityOpt.options[IcityOpt.selectedIndex].value;	
	_Omstatus   = Omstatus.options[Omstatus.selectedIndex].value;	
	_OmstatusOpt= OmstatusOpt.options[OmstatusOpt.selectedIndex].value;	
   
	   if(trim(recname)=='' || trim(title)=='')
	   {
		   	alert('Title and Recruitment name is undefined');
	   }
	   else
	   {
		   	param='position='+title+'&grade='+_OgradeOpt;
		  	param+='&faculty='+_OfacultyOpt;
		  	param+='&scorr='+_IscoreOpt;
		  	param+='&height='+_IheightOpt;
		  	param+='&sex='+_OsexOpt;
		  	param+='&age='+_IageOpt;
		  	param+='&city='+_IcityOpt;
		  	param+='&mstatus='+_OmstatusOpt;
		  	param+='&recruitment_name='+recname;
		  	param+='&remark='+remark;
		  	param+='&major='+_OmajorOpt;
		  	param+='&i_grade='+_Ograde;
		  	param+='&i_faculty='+_Ofaculty;
		  	param+='&i_scorr='+_Iscore;
		  	param+='&i_height='+_Iheight;
		  	param+='&i_sex='+_Osex;
		  	param+='&i_age='+_Iage;
		  	param+='&i_city='+_Icity;
		  	param+='&i_mstatus='+_Omstatus;
		  	param+='&i_major='+_Omajor;
			
		  if(confirm('Are you sure'))
		  {
		    post_response_text('hr_slave_saveNewVacancies.php', param, respon);		  	
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
					document.getElementById('positionDisplay').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
             }
           }
     }    
}

function deletePosition(id,recname)
{
	param='id='+id+'&del=true'+'&recruitment_name='+recname;
	  if(confirm('Are you sure'))
	  {
		post_response_text('hr_slave_saveNewVacancies.php', param, respon);		  	
	  }	
	  function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
					document.getElementById('positionDisplay').innerHTML = con.responseText;
                    document.getElementById('result').innerHTML='';
				}
            }
            else {
                busy_off();
                error_catch(con.status);
             }
           }
     }	
}

function changDisplay(recname)
{
	param='recruitment_name='+recname;
	document.getElementById('capt').innerHTML='<b>'+recname+'</b>';
	post_response_text('hr_slave_saveNewVacancies.php', param, respon);		  	
	  function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
					document.getElementById('positionDisplay').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
             }
           }
     }	
	loadOptCity(recname); 
}

function loadOptCity(recname)
{
	_title=document.getElementById('title');
	title=_title.options[_title.selectedIndex].value;
	param='recname='+recname+'&title='+title;
	post_response_text('hr_slave_getRecruitmentCity.php', param, respon);		  	
	  function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
					document.getElementById('citydisp').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
             }
           }
     }		
}

function showCriteria(id)
{
	param='id='+id;
	post_response_text('hr_slave_showVacancyRequirement.php', param, respon);		  	
	  function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
					document.getElementById('result').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
             }
           }
     }	
}

function showCandidate()
{
	recname =document.getElementById('recname');
	title	=document.getElementById('title');
	status	=document.getElementById('status');
	city	=document.getElementById('city');
 _recname=recname.options[recname.selectedIndex].value;
 _title  =title.options[title.selectedIndex].value;
 _status =status.options[status.selectedIndex].value;
 _city   =city.options[city.selectedIndex].value;
 if(document.getElementById('check').checked)
    _check='true';
 else
    _check='false';	  
 
 if(_recname=='' || _title=='')
 {
 	alert('Recruitment Name & Title are Obligatory');
 }
 else
 {
 	param='title='+_title+'&recname='+_recname;	
	param+='&city='+_city+'&status='+_status+'&check='+_check;
	//alert(param);
	post_response_text('hr_slave_candidateAdministrationFilter.php', param, respon);				      
 }
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
 					document.getElementById('result').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
          }
    	 }  
}
function loadCity()
{
	recname = document.getElementById('eRecname').options[document.getElementById('eRecname').selectedIndex].value;
	try {
	  title  =document.getElementById('eTitle').options[document.getElementById('eTitle').selectedIndex].value;
	}
	catch(e)
	{}
	if(title=='')
	   	  title='0';
	param='recname='+trim(recname)+'&title='+trim(title);
	   post_response_text('hr_slave_getRecruitmentCity.php', param, respon);
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('ePloc').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}
//decline application
function decline(idnum,image,name)
{
	param='idnum='+idnum;
	   if (confirm('Are you sure decline '+name+' Application ..?')) {
	   	post_response_text('hr_slave_declineApplication.php', param, respon);
	   }
	   function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    image.style.display = 'none';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }			
}

function hidefreesms()
{
	document.getElementById('freesms').style.display='none';
}

function freesms(phone,evt)
{
   	pos = new Array();
	pos = getMouseP(evt);
	document.getElementById('phnum').innerHTML=phone;
	document.getElementById('freesms').style.display='';
	document.getElementById('freesms').style.top=(pos[1]-500)+'px';	
	document.getElementById('freesms').style.left=(pos[0]-350)+'px';
	document.getElementById('freemsg').value='';
}

function hitungChar(obj,target)
{
	a=obj.value.length;
	b=160-a;
	document.getElementById(target).innerHTML=b;
}

function directCall(idnum,image,name)
{
	param='idnum='+idnum;
	   if (confirm('This candidate has been contacted separately ..?')) {
	   	post_response_text('hr_slave_candidateDirectCall.php', param, respon);
	   }
	   function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    image.style.display = 'none';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}
