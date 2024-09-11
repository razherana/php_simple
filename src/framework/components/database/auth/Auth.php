<?php

namespace framework\components\database\auth;

use framework\base\config\ConfigurableElement;
use framework\components\database\auth\exceptions\AuthDefinitionException;
use framework\components\database\orm\mysql\models\BaseModel;
use framework\components\session\SessionManager;
use framework\components\session\interfaces\SessionReservedKeywordsInterface;

class Auth extends ConfigurableElement implements SessionReservedKeywordsInterface
{
  /**
   * Contains the model to use
   * @var string $model
   */
  protected $model = '';

  /**
   * Contains the id_columns
   * @var string[] $id_columns
   */
  protected $id_columns = [];

  /**
   * Contains the pass_columns
   * @var ?string $pass_column
   */
  protected $pass_column = null;

  /**
   * Contains the hash_method
   * @var \Closure $hash_method
   */
  protected $hash_method = null;

  /**
   * Contains the password_verify
   * @var \Closure $password_verify
   */
  protected $password_verify = null;

  /**
   * Contains the session_manager
   * @var SessionManager $session
   */
  protected $session = null;

  public function config_file(): string
  {
    return 'auth';
  }

  public static function get_session_reserved_keywords(): array
  {
    return [
      (new Auth)->read_config('session_key_name')
    ];
  }

  /**
   * Constructs a new Auth
   * @param string $model The Model::class, if $mode === "ignore" it skips checkings and settings
   * @param string[]|string $id_columns
   * @param ?string $pass_column
   * @param ?\Closure $hash_method
   * @param ?\Closure $password_verify
   * @return self
   */
  public function __construct($model = "ignore", $id_columns = null, $pass_column = null, $hash_method = null, $password_verify = null)
  {
    if ($model === "ignore") return;

    if (!is_a($model, BaseModel::class, true))
      throw new AuthDefinitionException("This class is not a model : " . $model, $this);

    $this->session = new SessionManager;
    $this->model = $model;

    if (is_string($id_columns))
      $id_columns = [$id_columns];

    $this->id_columns = $id_columns;
    $this->pass_column = $pass_column;
    $this->hash_method = $hash_method;
    $this->password_verify = $password_verify;

    if ($this->hash_method === null)
      $this->hash_method = $this->read_cached_config('default_hash_method');

    if ($this->password_verify === null)
      $this->password_verify = $this->read_cached_config('default_password_verify');
  }

  /**
   * Get an instance of Auth from a defined definition in config
   * @param string $name_of_auth
   * @return self The Auth instance
   */
  public static function from_config($name_of_auth)
  {
    $auths = (new Auth)->read_config('auths');

    if (!isset($auths[$name_of_auth]))
      throw new AuthDefinitionException("This auth doesn't exist in config : $name_of_auth");

    $config = $auths[$name_of_auth];

    return new Auth(...$config);
  }

  // Auth functions starts here

}
