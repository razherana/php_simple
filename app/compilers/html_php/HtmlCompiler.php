<?php

namespace compilers\html_php;

use compilers\html_php\components\HtmlAuth;
use compilers\html_php\components\HtmlBlock;
use compilers\html_php\components\HtmlComment;
use compilers\html_php\components\HtmlElse;
use compilers\html_php\components\HtmlElseIf;
use compilers\html_php\components\HtmlEndBlock;
use compilers\html_php\components\HtmlEndElse;
use compilers\html_php\components\HtmlEndFor;
use compilers\html_php\components\HtmlEndIf;
use compilers\html_php\components\HtmlFor;
use compilers\html_php\components\HtmlGuest;
use compilers\html_php\components\HtmlIf;
use compilers\html_php\components\HtmlInclude;
use compilers\html_php\components\HtmlJoin;
use compilers\html_php\components\HtmlTemplate;
use compilers\html_php\components\HtmlUse;
use compilers\html_php\components\HtmlUseTemplate;
use framework\view\compiler\AbstractCompiler;

class HtmlCompiler extends AbstractCompiler
{
  protected function get_compiler_name(): string
  {
    return "html_php";
  }

  protected function get_extensions(): array
  {
    return ["hphp"];
  }

  protected function get_components(): array
  {
    return [
      HtmlComment::class,
      HtmlIf::class,
      HtmlEndIf::class,
      HtmlAuth::class,
      HtmlGuest::class,
      HtmlElseIf::class,
      HtmlElse::class,
      HtmlEndElse::class,
      HtmlFor::class,
      HtmlEndFor::class,
      HtmlUse::class,
      HtmlUseTemplate::class,
      HtmlInclude::class,
      HtmlJoin::class,
      HtmlBlock::class,
      HtmlEndBlock::class,
      HtmlTemplate::class,
    ];
  }

  public function get_view_var_class(): string
  {
    return HtmlViewVars::class;
  }
}
