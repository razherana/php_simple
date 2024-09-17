<?php

namespace commands;

use framework\base\config\ConfigReader;
use framework\components\console\ConsoleCommand;
use framework\components\console\exceptions\ConsoleExecutionException;

class MakeCommand extends ConsoleCommand
{
  public function get(): string
  {
    return "make";
  }

  public function execute($args): void
  {
    $args = array_slice($args, 1);

    $params = array_filter($args, function ($e) {
      return preg_match("/\\-\\w/", $e);
    });

    $other = array_values(array_diff($args, $params));

    foreach ($params as $arg)
      switch ($arg) {
        case "-m":
          $this->model($other);
          break;
        case "-c":
          $this->controller($other);
          break;
        case "-env":
          $this->reset_env();
          break;
      }
  }

  public function reset_env()
  {
    $content = file_get_contents(___DIR___ . "/env.example.php");
    $hash = base64_encode(random_bytes(4096));

    $content = str_replace('<<hash_code>>', $hash, $content);

    file_put_contents(___DIR___ . "/env.php", $content);
  }

  protected function controller($other)
  {
    if (!isset($other[0]))
      throw new ConsoleExecutionException("Missing controller's name");

    $controller_name = $other[0];

    $content = file_get_contents(ConfigReader::get('template', 'controller'));
    $content = str_replace("template_controller_name", $controller_name, $content);

    if (!is_dir($dir = ___DIR___ . "/app/http/controllers"))
      mkdir($dir, 0777, true);
    file_put_contents("$dir/$controller_name.php", $content);
  }

  protected function model($other)
  {
    if (!isset($other[0]))
      throw new ConsoleExecutionException("Missing model's name");

    $model_name = $other[0];

    if (!isset($other[1]))
      throw new ConsoleExecutionException("Missing model's table name");

    $model_table = $other[1];

    $primary_key = 'id';

    if (isset($other[2]))
      $primary_key = $other[2];

    $content = file_get_contents(ConfigReader::get('template', 'model'));
    $content = str_replace("template_model_name", $model_name, $content);
    $content = str_replace("template_model_table", $model_table, $content);
    $content = str_replace("template_model_primary_key", $primary_key, $content);

    if (!is_dir($dir = ___DIR___ . "/app/models"))
      mkdir($dir, 0777, true);
    file_put_contents("$dir/$model_name.php", $content);
  }

  public function help(): string
  {
    return "Make a new element\nAvailable parameters : \n\t-m !model_name! !model_table! !primary_key!\n\t-c !controller_name!\n\t-env Resets the env.php var";
  }
}
