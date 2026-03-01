<?php

/**
 * Clase para manejar códigos HTML
 *
 * @package SoluGenerateHTML
 */
class SoluHtmlCode
{
  public $id;
  public $name_group;
  public $code;
  public $created_at;
  public $update_at;

  public function __construct($id = null, $name_group = '', $code = '', $created_at = '', $update_at = '')
  {
    $this->id = $id;
    $this->name_group = $name_group;
    $this->code = $code;
    $this->created_at = $created_at;
    $this->update_at = $update_at;
  }

  public function toArray()
  {
    return array(
      'id' => $this->id,
      'name_group' => $this->name_group,
      'code' => $this->code,
      'created_at' => $this->created_at,
      'update_at' => $this->update_at
    );
  }
}
