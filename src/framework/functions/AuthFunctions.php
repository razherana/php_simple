<?php

use framework\components\database\auth\Auth;

function auth($name_of_auth)
{
  return Auth::from_config($name_of_auth);
}
