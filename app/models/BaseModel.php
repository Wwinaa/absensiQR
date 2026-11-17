<?php
/**
 * Base Model
 * Can be extended by all other models to inherit DB instance
 */
class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }
}
