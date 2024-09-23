<?php

namespace framework\components\storage\components;

use framework\components\storage\exceptions\UnexistantFileException;

/**
 * This class contains methods for file
 */
class File
{
  /**
   * The full path of the file
   * @var string $path
   */
  protected $path = "";

  /**
   * Cached content
   * @var ?string $cached_content
   */
  protected $cached_content = null;

  /**
   * Make a file
   * @param string $path The path of the file, relative or absolute
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

  /**
   * Tells if the file exists or not 
   */
  public function exists(): bool
  {
    return file_exists($this->path());
  }

  /**
   * Deletes the file
   */
  public function delete(): bool
  {
    if (!$this->exists())
      throw new UnexistantFileException($this->path());
    return unlink($this->path());
  }

  /**
   * Get the bytes of the file
   */
  public function bytes(): string
  {
    if (!$this->exists())
      throw new UnexistantFileException($this->path());
    if (!is_null($this->cached_content))
      return $this->cached_content;
    return file_get_contents($this->cached_content = $this->path());
  }

  /**
   * Get the path of this file
   */
  public function path(): string
  {
    return $this->path;
  }

  /**
   * This functions writes data to the file
   * It overwrites all of the data
   * @param string $data
   * @param int $flags
   */
  public function write($data, $flags = 0): bool
  {
    return file_put_contents($this->path(), $data, $flags) !== false;
  }

  /**
   * Copy this file to a location
   * @param string $new_path_filename
   */
  public function copy($new_path_filename)
  {
    if (!$this->exists())
      throw new UnexistantFileException($this->path());
    return copy($this->path(), $new_path_filename);
  }

  /**
   * Get the mime type of a file
   */
  public function mime(): string
  {
    if (!$this->exists())
      throw new UnexistantFileException($this->path());
    return mime_content_type($this->path());
  }
}
