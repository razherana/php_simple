<?php

namespace vendor\main\filing;

use app\Models\File;
use vendor\main\database\model\ModelInstance;
use vendor\main\filing\ruling\FileRule;
use vendor\main\util\Message;

class FileManager
{
  public $file;

  private function __construct($file)
  {
    if ($file instanceof ModelInstance && $file->modelName !== File::class) {
      throw new \Exception("This ModelInstance isn't a File", 1);
    }
    $this->file = $file;
  }

  public static function getUploadedFile($fieldName)
  {
    if (!UploadedFile::fileExist($fieldName)) {
      throw new \Exception("This field doesn't exist", 1);
    }
    $file = UploadedFile::name($fieldName);
    return new static($file);
  }

  public static function getExistingFile($file_uuid)
  {
    $file_thing = File::getFile($file_uuid);
    if ($file_thing === false) {
      throw new \Exception("This field doesn't exist", 1);
    }
    return new static($file_thing);
  }

  public function save($owner = File::PUBLIC_OWNER, $rule = null, $json = false)
  {
    if ($this->file instanceof UploadedFile) {

      $fileRule = FileRule::file($this->file);

      if ($rule !== null)
        $rule($fileRule);

      if (!empty($fileRule->errors)) {
        if ($json)
          return [0 => false, 'type' => 'error', 'from' => $this->file->fieldName, 'message' => $fileRule->errors[0]];
        else
          Message::set($this->file->fieldName, $fileRule->errors[0], 'error');
        return false;
      }

      $this->file->save();
      File::create(['ownerId' => $owner, 'name' => ($this->file->uuid . "." . $this->file->getExtension()), 'mime' => $this->file->type]);
      $res = File::getFile(($this->file->uuid . "." . $this->file->getExtension()));
      if ($res instanceof ModelInstance)
        return $res;
      return false;
    }
    throw new \Exception("File already exist", 1);
  }

  /**
   * @param string[] $message
   */
  public function validate($rule, $message = [])
  {
    $fileRule = FileRule::file($this->file);

    $fileRule = $rule($fileRule);

    if (!empty($fileRule->errors)) {
      Message::set($this->file->fieldName, empty($message) ? $fileRule->errors[0] : $message[0], 'error');
      return false;
    }
    return true;
  }

  /**
   * Please verify name, this method doesn't sanitize the new name
   */
  public function rename($name)
  {
    $name = str_replace('/', '_', $name);

    if ($this->file instanceof UploadedFile)
      throw new \Exception("This file isn't saved", 1);

    rename($this->file->name, File::UPLOADS_FOLDER . $name);
    $this->file->name = $name;

    $res = $this->file->save();

    if ($res) {
      $this->file = File::getFile($name);
      return $this;
    }

    throw new \Exception("The rename didn't work", 1);
  }

  public static function delete($file)
  {
    return \unlink($file);
  }
}
