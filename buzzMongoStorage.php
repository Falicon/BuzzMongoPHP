<?php

class buzzMongoStorage extends buzzStorage {
  private $db;
  private $dbname = 'YOUR_MONGO_INSTANCE_NAME';
  private $coll;
  private $collection_name = 'YOUR_MONGO_COLLECTION_NAME';

  public function __construct() {
    $this->conn = new Mongo();
    $this->conn->connect();
    if (!$this->db = $this->conn->selectDB($this->dbname)) {
      throw new buzzStorageException("Could not create mongo link");
    }
    $this->coll = $this->db->selectCollection($this->collection_name);
  }

  public function __destruct() {
    // you don't current need a destruct action for Mongo (but the buzz library might attempt to call it, so we define it just in case)
  }

  public function get($key, $expiration = false) {
    // query the storage db for the key
    $rec = $this->coll->findOne(array('key' => $key));
    if (isset($rec['value'])) {
      return unserialize($rec['value']);
    }
    return false;
  }

  public function set($key, $val) {
    $filter = array('key' => $key, 'value' => serialize($val));
    $this->coll->insert($filter, false);
    return;
  }

  public function delete($key) {
    $this->coll->remove(array('key' => $key), true);
    return;
  }
}
?>