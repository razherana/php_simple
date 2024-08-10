<?php return '<?php

namespace app\\Http\\Controllers;

use <<config3>>;
use DateTime;
use vendor\\main\\cache\\sessions\\Session;
use vendor\\main\\database\\model\\auth\\Auth;
use vendor\\main\\request\\validating\\Rule;
use vendor\\main\\request\\validating\\Validator;
use vendor\\main\\util\\Message;

class AuthController
{

  public function login()
  {
    return view_herine(\'auth/login\');
  }

  public function register()
  {
    return view_herine(\'auth/register\');
  }

  public function do_login()
  {
    $datas = Validator::post(request())->validate(
      [
        Rule::field(\'<<config1>>\')->required(),
        Rule::field(\'<<config2>>\')->required(),
      ],
      \'auth.login\',
      true
    );

    $res = Auth::attempt($datas);
    if ($res) {
      Session::regenerate();
      return to_route(\'mouvement.index\');
    }
    Message::set(\'<<config1>>\', \'<<config1>> et/ou <<config2>> erroné\', \'error\');
    return to_route(\'auth.login\');
  }

  public function do_register()
  {
    $datas = Validator::post(request())->validate(
      [
        Rule::field(\'<<config1>>\')->required(),
        Rule::field(\'<<config2>>\')->required(),
        Rule::field(\'confirm_<<config2>>\')->required()->equal(Rule::valueof(\'<<config2>>\')),
      ],
      \'auth.register\',
      true
    );
    unset($datas[\'confirm_<<config2>>\']);

    $user = <<config4>>::where(\'<<config1>>\', \'=\', $datas[\'<<config1>>\'])->get();

    if(!empty($user)) {
      Message::set(\'<<config1>>\', \'Cet <<config1>> existe déjà\', \'error\');
      return to_route(\'auth.register\');
    }

    $res = Auth::register($datas);

    if ($res) {
      return to_route(\'auth.login\');
    }

    Message::set(\'<<config1>>\', \'Veuillez réessayer\', \'error\');
    return to_route(\'auth.register\');
  }
}
'; 