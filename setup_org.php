<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
  
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Organisasi Hirarki</font></b></u></p>
<?php
#======Select Prep======
# Get Data
$query = selectQuery($dbname,'setup_org','kodeorg,namaorganisasi');
$data = fetchData($query);
$parentOpt = array();
foreach($data as $row) {
  $parentOpt[$row['kodeorg']] = $row['namaorganisasi'];
}
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','text','',array('style'=>'width:50px','maxlength'=>'8'))
);
$els[] = array(
  makeElement('namaorganisasi','label',$_SESSION['lang']['namaorganisasi']),
  makeElement('namaorganisasi','text','',array('style'=>'width:250px','maxlength'=>'40'))
);
$els[] = array(
  makeElement('parent','label',$_SESSION['lang']['parent']),
  makeElement('parent','select','',array(),$parentOpt)
);
$els[] = array(
  makeElement('detail','label',$_SESSION['lang']['detail']),
  makeElement('detail','select',1,array('style'=>'width:50px'),array('Tidak','Ya'))
);
$els[] = array(
  makeElement('propinsiid','label',$_SESSION['lang']['propinsi_id']),
  makeElement('propinsiid','select','',array('style'=>'width:100px'),array('1'=>'Jawa Barat','2'=>'Jawa Timur'))
);
$els[] = array(
  makeElement('alamat','label',$_SESSION['lang']['alamat']),
  makeElement('alamat','text','',array('style'=>'width:250px','maxlength'=>'60'))
);
$els[] = array(
  makeElement('wilayahkota','label',$_SESSION['lang']['wilayahkota']),
  makeElement('wilayahkota','text','',array('style'=>'width:250px','maxlength'=>'30'))
);
$els[] = array(
  makeElement('telepon','label',$_SESSION['lang']['telepon']),
  makeElement('telepon','text','',array('style'=>'width:250px','maxlength'=>'30'))
);
$els[] = array(
  makeElement('email','label',$_SESSION['lang']['email']),
  makeElement('email','text','',array('style'=>'width:250px','maxlength'=>'60'))
);

# Button
$els['btn'] = array(
  genFormBtn('##kodeorg##namaorganisasi##parent##detail##propinsiid##alamat##wilayahkota##telepon##email',
    'setup_org',"##kodeorg",'setup_slave_org')
);

# Generate Field
echo genElement($els);
echo "</div>";
#=======End Form============

#=======Table============
# Get Data
$query = selectQuery($dbname,'setup_org');
$data = fetchData($query);

# Set Header
$head = array();
$head[] = $_SESSION['lang']['kodeorg'];
$head[] = $_SESSION['lang']['namaorganisasi'];
$head[] = $_SESSION['lang']['parent'];
$head[] = $_SESSION['lang']['detail'];
$head[] = $_SESSION['lang']['propinsi_id'];
$head[] = $_SESSION['lang']['alamat'];
$head[] = $_SESSION['lang']['wilayahkota'];
$head[] = $_SESSION['lang']['telepon'];
$head[] = $_SESSION['lang']['email'];

# Display Table
echo masterTable($dbname,'setup_org','*');
#=======End Table============
?>
<!--FORM NAME = "Pinjaman">
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Organisasi Hirarki</font></b></u></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="67%" id="AutoNumber1" height="176">
  <tr>
    <td width="25%" height="22"><b><font face="Verdana" size="2">Kode Organisasi</font></b></td>
    <td width="56%" height="22"><font face="Fixedsys"> <input type=text size="7" name="kodeorg"> </font></td>
    <td width="1%" height="22"><font face="Fixedsys">&nbsp;</font></td>
    <td width="37%" height="22">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="22"><b><font face="Verdana" size="2">Nama Organisasi</font></b></td>
    <td width="56%" height="22"><font face="Fixedsys"> <input type=text size="57" name="namaorg"></font></td>
    <td width="1%" height="22">&nbsp;</td>
    <td width="37%" height="22">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="22"><b><font face="Verdana" size="2">Parent</font></b></td>
    <td width="56%" height="22"><select size="1" name="parent">
    </select></td>
    <td width="8%" height="22">&nbsp;</td>
    <td width="54%" height="22">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="22"><b><font face="Verdana" size="2">Detail</font></b></td>
    <td width="56%" height="22"><font face="Fixedsys"> <input type="checkbox" name="detail" value="ON"></font></td>
    <td width="1%" height="22">&nbsp;</td>
    <td width="37%" height="22">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="22"><b><font face="Verdana" size="2">Alamat</font></b></td>
    <td width="56%" height="22"><font face="Fixedsys"> 
    <input type=text size="57" name="alamat"></font></td>
    <td width="8%" height="22">&nbsp;</td>
    <td width="54%" height="22">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="22"><b><font face="Verdana" size="2">Propinsi</font></b></td>
    <td width="56%" height="22"><select size="1" name="propinsi">
    <option>Jawa Barat</option>
    <option>Jawa Tengah</option>
    </select></td>
    <td width="1%" height="22">&nbsp;</td>
    <td width="37%" height="22">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="19"><b><font face="Verdana" size="2">Kota</font></b></td>
    <td width="56%" height="19"><font face="Fixedsys">  
    <input type=text size="57" name="kota"></font></td>
    <td width="1%" height="19">&nbsp;</td>
    <td width="37%" height="19">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="22"><b><font face="Verdana" size="2">Telepon</font></b></td>
    <td width="56%" height="22"><font face="Fixedsys"> 
    <input type=text size="57" name="telepon"></font></td>
    <td width="1%" height="19">&nbsp;</td>
    <td width="37%" height="19">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="1"><b><font face="Verdana" size="2">email</font></b></td>
    <td width="56%" height="1"><font face="Fixedsys"> 
    <input type=text size="57" name="email"></font></td>
    <td width="1%" height="1"></td>
    <td width="37%" height="1"></td>
  </tr>
  <tr>
    <td width="25%" height="19">&nbsp;</td>
    <td width="56%" height="19">&nbsp;</td>
    <td width="1%" height="19">&nbsp;</td>
    <td width="37%" height="19">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" height="19">&nbsp;</td>
    <td width="56%" height="19">&nbsp;</td>
    <td width="1%" height="19">&nbsp;</td>
    <td width="37%" height="19">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
<table id="Table" border="1" width="738">
  <tr>
    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Kode Org</font></td>
    <td width="375" bgcolor="#C0C0C0"><font face="Fixedsys">Nama Organisasi</font></td>
    <td width="364" bgcolor="#C0C0C0"><font face="Fixedsys">Parent</font></td>
    <td width="364" bgcolor="#C0C0C0"><font face="Fixedsys">Detail</font></td>
   </tr>
  <tr>
    <td width="72"><font face="Fixedsys">
    <input type=text size="10" name="kodeorg"></font></td>
    <td width="375"><font face="Fixedsys">
    <input type=text size="51" name="namaorganisasi"></font></td>
    <td width="364"><font face="Fixedsys"-->
    <!--webbot bot="Validation" s-data-type="Number" s-number-separators=".," --><!--input type=text size="37" name="namaparent"></font></td>
    <td width="364"><font face="Fixedsys"> <input type="checkbox" name="detail1" value="ON"><input type="submit" value="Lihat Detail  " name="Lihat Detail"></font></td>
  </tr>
</table>
<p><font face="Fixedsys"><input type="button" value="Simpan" name="ModifDtl">&nbsp;
<input type="button" value="   Batal   " name="DeleteDtl"></font></p>
<p><font face="Fixedsys">&nbsp;&nbsp; &nbsp;</font></p-->
<?php
CLOSE_BOX();
echo close_body();
?>