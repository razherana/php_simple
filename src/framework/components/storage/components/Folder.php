<?php

namespace framework\components\storage\components;

use framework\components\storage\exceptions\FileException;
use framework\components\storage\exceptions\UnexistantFileException;

class Folder
{
  /**
   * The full path of the file
   * @var string $path
   */
  protected $path = "";

  /**
   * Make a file
   * @param string $path The path of the folder, relative or absolute
   * @param bool $relative Tells if the path is relative or absolute
   */
  public function __construct($path, $relative = true)
  {
    if ($relative) $path = ___DIR___ . '/' . trim($path, '\\/');
    else $path = rtrim($path, '\\/');

    $this->path = $path;
  }

  /**
   * Checks if it is a file
   */
  public static function is($path, $relative = true)
  {
    return is_file((new self($path, $relative))->path());
  }

  public function list(): array
  {
    if (!$this->exists())
      throw new UnexistantFileException($this->path());
    $files = scandir($this->path());
    $all = [];

    foreach (
      // We remove .. and . folder
      array_filter($files, function ($e) {
        return !in_array($e, ['.', '..']);
      }) as $file
    ) {
      $fullpath = $this->path() . "/$file";

      if (is_file($fullpath))
        $all[] = new File($fullpath, false);
      else
        $all[] = new self($fullpath, false);
    }

    return $all;
  }

  /**
   * Get the path of this folder
   */
  public function path(): string
  {
    return $this->path;
  }

  /**
   * Deletes the folder
   */
  public function delete(): bool
  {
    if (!$this->exists())
      throw new UnexistantFileException($this->path());

    $files = $this->list();
    if (!empty($files))
      foreach ($files as $file)
        $file->delete();

    return rmdir($this->path());
  }

  /**
   * Tells if the file exists or not 
   */
  public function exists(): bool
  {
    return file_exists($this->path());
  }

  /**
   * Tells if the directory is empty or not
   */
  public function empty()
  {
    return count($this->list()) == 0;
  }

  /**
   * Creates the directory
   */
  public function create($permission = 0777, $recursive = false)
  {
    if ($this->exists())
      throw new FileException("This directory already exists");
    return mkdir($this->path(), $permission, $recursive);
  }
}
