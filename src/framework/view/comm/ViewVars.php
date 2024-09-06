<?php

namespace framework\view\comm;

class ViewVars
{
  /**
   * Contains the data for the view
   * name_of_var => value
   * @var array<string, mixed> $data
   */
  protected $data = [];

  /** @param array<string, mixed>|static $data */
  public function __construct($data = [])
  {
    // Make it able to be constructed from himself
    if ($data instanceof ViewVars) {
      $data = $data->data;
    }

    $this->data = $data;

    if (!isset($this->data['___vars___']))
      $this->data += ['___vars___' => $this];
  }

  /**
   * Get all the data
   * @return array<string, mixed>
   */
  public function get_data()
  {
    return $this->data;
  }
}
