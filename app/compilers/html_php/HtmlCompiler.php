<?php

namespace compilers\html_php;

use compilers\html_php\components\HtmlBlock;
use compilers\html_php\components\HtmlEndBlock;
use compilers\html_php\components\HtmlEndFor;
use compilers\html_php\components\HtmlFor;
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
      HtmlBlock::class,
      HtmlEndBlock::class,
      HtmlUse::class,
      HtmlInclude::class,
      HtmlTemplate::class,
      HtmlUseTemplate::class,
      HtmlJoin::class,
      HtmlFor::class,
      HtmlEndFor::class,
    ];
  }

  public function get_view_var_class(): string
  {
    return HtmlViewVars::class;
  }
}
