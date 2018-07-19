<?php
    $success = 0;
    $uploadedFile = '';
    
    //File upload path
 
    $uploadPath = '../cam-images/'.$_POST["projectUpload"].'/';

    $targetPath = $uploadPath . basename( $_FILES['myfile']['name']);

    if(@move_uploaded_file($_FILES['myfile']['tmp_name'], $targetPath)){
        $success = 1;
        $uploadedFile = $targetPath;
    }
    
    sleep(1);
?>
<script type="text/javascript">window.top.window.stopUpload(<?php echo $success; ?>,'<?php echo $uploadedFile; ?>');</script>