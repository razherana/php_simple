<?php

namespace vendor\main\filing;

use app\Models\File;

class UploadedFile
{
  public $fieldName = '';
  private $datas = [];
  private $uuid = '';
  private $extension = '';

  private function __construct($fieldName, $datas)
  {
    $this->fieldName = $fieldName;
    $this->datas = $datas;
    $this->uuid = bin2hex(random_bytes(16));
    $this->takeExtension();
  }

  public function uuid()
  {
    return $this->uuid . '.' . $this->getExtension();
  }

  public static function fileExist($fieldName)
  {
    return isset($_FILES[$fieldName]) && $_FILES[$fieldName]['name'] !== '';
  }

  public static function name($fieldName = '')
  {
    if (!self::fileExist($fieldName)) throw new \Exception("The file doesn't exist", 1);
    $el = new self($fieldName, $_FILES[$fieldName]);
    return $el;
  }

  public function __get($name)
  {
    if (in_array($name, array_keys($this->datas))) {
      return $this->datas[$name];
    }

    if (stripos('uuid', $name) !== false) {
      return $this->uuid;
    }

    return null;
  }

  public function checkExtension($extensions = [])
  {
    return (in_array($this->extension, $extensions));
  }

  public function getExtension()
  {
    return $this->extension;
  }

  private function takeExtension()
  {
    $a = explode('.', basename($this->datas['name']));
    $this->extension = $a[count($a) - 1];
  }

  public function save($folder = '')
  {
    if (move_uploaded_file($this->tmp_name, File::UPLOADS_FOLDER . $folder . $this->uuid . '.' . $this->extension))
      return $this;
    return false;
  }
}
