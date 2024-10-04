<?php

namespace framework\components\storage\upload\drivers;

use DateTime;
use framework\components\database\orm\mysql\executers\MysqlQueryExecuter;
use framework\components\database\orm\mysql\queries\SortedQueryMaker;
use framework\components\storage\Storage;
use framework\components\storage\upload\UploadManager;

class UploadMysqlDriver extends UploadDriver
{
  protected function map($file): array
  {
    $storage_folder = (new Storage)->read_cached_config('folder');
    $upload_folder = (new UploadManager(false))->read_cached_config('folder');

    $upload_folder = ___DIR___ . "/" . trim($storage_folder, '/') . "/" . trim($upload_folder, '/');

    // Make the map
    $new_map = [
      "name" => $file['name'],
      "created_at" => $created_at = (new DateTime)->format('Y-m-d H:i:s')
    ];

    $q = (new SortedQueryMaker)->insert_into((new UploadManager(false))->read_cached_config('mysql_tablename'), $new_map)->decode_query();
    MysqlQueryExecuter::run($q);

    $q = (new SortedQueryMaker)->select(['*'])
      ->from((new UploadManager(false))->read_cached_config('mysql_tablename'))
      ->where('created_at', '=', $created_at)
      ->decode_query();

    $res = MysqlQueryExecuter::do_clean($q, function ($e) {
      $res = $e->fetch_all(MYSQLI_ASSOC);
      return empty($res) ? false : $res[array_keys($res)[0]];
    });

    if ($res === false)
      return false;

    $fileid = $res['id'];

    // We return the info's
    return [
      "id" => $fileid,
      "path" => $upload_folder . "/" . $fileid . ".uploadedfile",
    ];
  }
}
