<?php

require_once(__DIR__ . '/../env.php');
require_once(___DIR___ . '/src/autoloader.php');
require_once(___DIR___ . '/src/function_autoloader.php');

use framework\base\Application;
use framework\components\database\Database;
use framework\components\database\orm\mysql\request\MysqlQueryable;
use framework\components\database\orm\mysql\traits\DeleteTrait;
use framework\components\database\orm\mysql\traits\FromTrait;
use framework\components\database\orm\mysql\traits\InsertIntoTrait;
use framework\components\database\orm\mysql\traits\OrderTrait;
use framework\components\database\orm\mysql\traits\RawTrait;
use framework\components\database\orm\mysql\traits\SelectTrait;
use framework\components\database\orm\mysql\traits\WhereTrait;
use framework\components\debug\Debug;

global $app;

$app = Application::get();

/**
 * Initialize and Execute debug before anything
 * so it can run without other components
 */
$deb = new Debug;
$deb->initialize();
$deb->execute();

// Add components here
$components = [
  new Database,
  // new Router
];

$app->add_component($components);

$app->initialize();
$app->execute();

class QueryMaker extends MysqlQueryable
{
  use SelectTrait, WhereTrait, FromTrait, OrderTrait, RawTrait, DeleteTrait, InsertIntoTrait;

  public static function get_magic()
  {
  }

  public function decode_query(): string
  {
    $this->verify_query();
    $els = [];
    foreach ($this->elements as $e) {
      if (is_array($e))
        $els[] = self::decode_array_query($e);
      else
        $els[] = $e->decode();
    }
    return implode(' ', $els);
  }
}

dd(QueryMaker::select()->from('test')->where('id', '!=', NULL)->and_group_where(fn () => $this->where('text', '=', 10)->and_where('id', '=', 1))->decode_query());
