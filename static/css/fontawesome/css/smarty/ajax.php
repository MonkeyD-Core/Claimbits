<?php
if (!file_exists('uploads')) {
    mkdir('uploads', 0777);
}
  
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
  
echo "success";
die();