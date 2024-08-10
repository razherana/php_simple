<?php

namespace framework\base;

/**
 * Base component of the framework
 */
interface Component
{
  /**
   * Initializes the component
   */
  public function initialize();

  /**
   * Executes the component
   */
  public function execute();
}
