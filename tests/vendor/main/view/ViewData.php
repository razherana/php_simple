<?php

namespace vendor\main\view;

use vendor\main\cache\sessions\Session;
use vendor\main\view\Herine\components\Block;
use vendor\main\view\Herine\components\Join;

class ViewData
{
  private $data = [];

  /**
   * @var Block[] $blocks
   */
  private $blocks = [];

  /**
   * @var ?Join $join
   */
  private $join = null;

  public function __construct($data)
  {
    if (isset($data['___blocks___'])) {
      $this->blocks = array_merge($this->blocks, $data['___blocks___']);
      unset($data['___blocks___']);
    }
    $data['___thisView___'] = $this;
    $this->data = $data;
    $this->save();
  }

  private function save()
  {
    Session::save('___viewVar___', $this);
  }

  public function getAll()
  {
    return $this->data;
  }

  public function get($name)
  {
    foreach ($this->blocks as $block) {
      if ($block->name == $name) {
        echo $block->content;
        break;
      }
    }
  }

  public function add(string $name, $args = [])
  {
    return View::herine($name, $args);
  }

  public function __get($name)
  {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }
    return null;
  }

  public function startBlock(string $name): void
  {
    $this->blocks[] = new Block($name);
    ob_start();
  }

  private function updateJoinBlocks($block)
  {
    if ($this->join !== null) {
      $this->join->blocks[] = $block;
    }
  }

  public function endBlock()
  {
    $content = ob_get_clean();
    $this->blocks[array_key_last($this->blocks)]->content = $content;
    $this->updateJoinBlocks($this->blocks[array_key_last($this->blocks)]);
  }

  public function join(string $view_name, $vars = [])
  {
    $this->join = new Join($view_name, $this->blocks, $vars);
  }

  public function callJoin()
  {
    return View::herine($this->join->viewName, $this->join->vars + ['___blocks___' => $this->blocks]);
  }
}
