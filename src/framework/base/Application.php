<?php

namespace framework\base;

use BadMethodCallException;

final class Application
{
  /**
   * Array of the components of the Application
   * @var Component[] $components
   */
  public $components = [];

  /**
   * The Application itself
   */
  private static self $application;

  private function __construct()
  {
  }

  public static function get(): self
  {
    return self::$application ?? self::$application = new self();
  }

  /**
   * Add a component to the current application
   * @param Component|Component[] $component
   */
  public function add_component($component): void
  {
    if ($component instanceof Component)
      $this->components[] = $component;

    if (is_array($component)) foreach ($component as $c) {
      if ($c instanceof Component)
        $this->components[] = $c;
      else
        throw new BadMethodCallException("The argument inside the array of Component" . $c::class . " is not a component", 1);
    }
    // This should not happen
    else throw new BadMethodCallException("The argument " . $component::class . " is nor a Component or an array of Component", 1);
  }

  /**
   * Initialize all the components
   */
  public function initialize(): void
  {
    foreach ($this->components as $component)
      $component->initialize();
  }

  /**
   * Execute all the components
   */
  public function execute(): void
  {
    foreach ($this->components as $component)
      $component->execute();
  }
}
