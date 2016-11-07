<?php
require_once('master_validation.php');
require_once('config/connection.php');
?>
	 <form id=frmUpload enctype=multipart/form-data method=post action=sdm_slave_simpan_photo_karyawan.php target=frame>	
	 <input type=hidden name=karyawanid id=karyawanid value=''>
	 <input type=hidden name=MAX_FILE_SIZE value=51000>
	 <input name=photo type=file id=photo size=35>
     </form>
