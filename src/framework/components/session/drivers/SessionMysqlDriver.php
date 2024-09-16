<?php

namespace framework\components\session\drivers;

use framework\components\database\orm\mysql\executers\conversions\ModelConversion;
use framework\components\database\orm\mysql\executers\MysqlQueryExecuter;
use framework\components\database\orm\mysql\models\instances\DefaultModelInstance;
use framework\components\database\orm\mysql\queries\SortedQueryMaker;
use framework\components\session\exceptions\SessionMysqlDriverException;

class SessionMysqlDriver extends SessionDriver
{
  private $session_table;
  private $session_query_create;

  /**
   * @param string $key
   * @param string $session_table
   * @param string $session_query_create
   */
  public function __construct($key, $session_table, $session_query_create)
  {
    $this->key = $key;
    $this->session_table = $session_table;
    $this->session_query_create = $session_query_create;
  }

  public function open(string $path, string $name): bool
  {
    // The table creation is set during the mysql_run
    return true;
  }

  public function close(): bool
  {
    return true;
  }

  public function read($id): string|false
  {
    /** @var DefaultModelInstance $model */
    $model = ModelConversion::to_single_model_query_type(
      (new SortedQueryMaker)
        ->select(['id', 'HEX(content)' => "hex_cont"])
        ->from($this->session_table)
        ->where('id_session', '=', "$id")
        ->decode_query(),
      DefaultModelInstance::class
    );

    // If false, cannot read the file
    if (!$model->offsetExists('id')) {
      return "";
    }
    return $this->decrypt(hex2bin($model->hex_cont));
  }

  public function write(string $id, string $data): bool
  {
    /** @var DefaultModelInstance $model */
    $model = ModelConversion::to_single_model_query_type(
      (new SortedQueryMaker)
        ->select(['id'])
        ->from($this->session_table)
        ->where('id_session', '=', "$id")
        ->decode_query(),
      DefaultModelInstance::class
    );

    $encrypted_data = bin2hex($this->encrypt($data));

    // If false, cannot read the file
    if (!$model->offsetExists('id')) {
      $result = MysqlQueryExecuter::run(
        (new SortedQueryMaker)::insert_into($this->session_table, [
          "id_session" => "$id",
          "created_at" => date("Y-m-d H:i:s"),
          "content" => "UNHEX('$encrypted_data')"
        ])->decode_query()
      );

      if (!$result)
        throw new SessionMysqlDriverException("Cannot create the session data");
    } else {
      $result = MysqlQueryExecuter::run(
        (new SortedQueryMaker)::update_set($this->session_table, [
          "content" => "UNHEX('$encrypted_data')",
          "created_at" => (string) date("Y-m-d H:i:s"),
        ])
          ->where('id_session', '=', "$id")
          ->decode_query()
      );

      if (!$result)
        throw new SessionMysqlDriverException("Cannot modify the session data");
    }

    return true;
  }

  public function destroy(string $id): bool
  {
    return MysqlQueryExecuter::run(
      (new SortedQueryMaker)
        ->delete()
        ->from($this->session_table)
        ->where('id_session', "=", "$id")
        ->decode_query()
    );
  }

  public function gc(int $max_lifetime): int|false
  {
    return MysqlQueryExecuter::run(
      (new SortedQueryMaker)
        ->delete()
        ->from($this->session_table)
        // 86400s = 60s * 60m * 24h
        ->where('created_at', '<', date("Y-m-d H:i:s", time() - $max_lifetime))
        ->decode_query()
    );
  }
}
