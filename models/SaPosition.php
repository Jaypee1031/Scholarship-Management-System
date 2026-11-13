<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class SaPosition extends BaseModel {
    protected $table = "sa_positions";
    public function getActive() { return $this->findAll(["is_active" => 1], "office ASC, title ASC"); }
    public function getAll() { return $this->findAll([], "office ASC, title ASC"); }
}