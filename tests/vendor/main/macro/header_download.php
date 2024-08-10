<?php
$extension = explode('.', $fileName);
$ext = $extension[array_key_last($extension)];
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="download.' . $ext . '"');
header("Content-Tranfer-Encoding: binary");
header("Pragma: public");
