<?php

namespace vendor\main\view\Herine;

use vendor\main\view\Herine\components\Add;
use vendor\main\view\Herine\components\Block;
use vendor\main\view\Herine\components\Get;
use vendor\main\view\Herine\components\Join;

class Compiler
{
  public const COMPILED_FILE_DIR = ___DIR___ . '/storage/cache/views',
    VIEW_DIR = ___DIR___ . '/resources';
  public $file_contents = "", $file_name = "";

  public const OPEN_PHP_TAG = ['<<', '<?php '],
    CLOSE_PHP_TAG = ['>>', ' ?>'],
    CLOSE_PHP_TAG2 = ['/>>', ' ?>'],
    OPEN_ECHO_HTML_TAG = ['<<=', '<?= e('],
    CLOSE_ECHO_HTML_TAG = ['=>>', ')?>'],
    CLOSE_ECHO_HTML_TAG2 = ['= />>', ')?>'],
    OPEN_CURLY_BRACES_TAG = [':>>', ' { ?>'],
    OPEN_CURLY_BRACES_TAG2 = [': />>', ' { ?>'],
    CLOSE_CURLY_BRACES_TAG = ['<<:>>', '<?php } ?>'],
    CLOSE_CURLY_BRACES_TAG2 = ['<<: />>', '<?php } ?>'],
    OPEN_WITH_CURLY_BRACES_TAG = ['<<:', '<?php }'];

  private const EXTRACT_VARS = '<<extract(dataViewVars())>>';

  public function __construct($herine_file_name)
  {
    $this->file_name = $herine_file_name;
    $this->file_contents = self::EXTRACT_VARS . "\n" . file_get_contents(self::VIEW_DIR . "/$herine_file_name.h.php");
  }

  public function compile()
  {
    $csrf = ['<<csrf>>', '<input type="hidden" name="___csrf_token___" value="<<= \app\Singleton\CsrfToken::get() =>>" />'];
    $csrf2 = ['<<csrf />>', '<input type="hidden" name="___csrf_token___" value="<<= \app\Singleton\CsrfToken::get() =>>" />'];
    $this->file_contents = str_replace($csrf[0], $csrf[1], $this->file_contents);
    $this->file_contents = str_replace($csrf2[0], $csrf2[1], $this->file_contents);

    $this->file_contents = Get::compile($this->file_contents);
    $this->file_contents = Add::compile($this->file_contents);
    $this->file_contents = Block::compile($this->file_contents);
    $this->file_contents = Join::compile($this->file_contents);

    $this->file_contents = str_replace(self::CLOSE_CURLY_BRACES_TAG[0], self::CLOSE_CURLY_BRACES_TAG[1], $this->file_contents);
    $this->file_contents = str_replace(self::CLOSE_CURLY_BRACES_TAG2[0], self::CLOSE_CURLY_BRACES_TAG2[1], $this->file_contents);

    $this->file_contents = str_replace(self::OPEN_WITH_CURLY_BRACES_TAG[0], self::OPEN_WITH_CURLY_BRACES_TAG[1], $this->file_contents);

    $this->file_contents = str_replace(self::OPEN_CURLY_BRACES_TAG[0], self::OPEN_CURLY_BRACES_TAG[1], $this->file_contents);
    $this->file_contents = str_replace(self::OPEN_CURLY_BRACES_TAG2[0], self::OPEN_CURLY_BRACES_TAG2[1], $this->file_contents);

    $this->file_contents = str_replace(self::OPEN_ECHO_HTML_TAG[0], self::OPEN_ECHO_HTML_TAG[1], $this->file_contents);

    $this->file_contents = str_replace(self::CLOSE_ECHO_HTML_TAG[0], self::CLOSE_ECHO_HTML_TAG[1], $this->file_contents);
    $this->file_contents = str_replace(self::CLOSE_ECHO_HTML_TAG2[0], self::CLOSE_ECHO_HTML_TAG2[1], $this->file_contents);

    $this->file_contents = str_replace(self::OPEN_PHP_TAG[0], self::OPEN_PHP_TAG[1], $this->file_contents);

    $this->file_contents = str_replace(self::CLOSE_PHP_TAG[0], self::CLOSE_PHP_TAG[1], $this->file_contents);
    $this->file_contents = str_replace(self::CLOSE_PHP_TAG2[0], self::CLOSE_PHP_TAG2[1], $this->file_contents);
  }

  public function save()
  {
    $file_to_save = str_replace('/', '_', $this->file_name);
    $file = fopen(self::COMPILED_FILE_DIR . "/$file_to_save.php", "w");
    fwrite($file, $this->file_contents);
    fclose($file);
    return $this->file_contents;
  }

  public static function compile_and_save($herine_file_name)
  {
    $a = new static($herine_file_name);
    $a->compile();
    return $a->save();
  }
}
