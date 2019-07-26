<?php

namespace BarrelDirectory\Classes\Db;

if ( ! defined( 'WPINC' ) ) {
  die;
}

interface Db_Interface {
  public function insert ();
  public function update ();
  public function delete ();
  public function find ();
  public function findAll ();
}