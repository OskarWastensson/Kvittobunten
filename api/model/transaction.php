<?php

/*
 */

class transaction extends Model {
  public $vars = array(
    'id' => 'i', 
    'user' => 'i',
    'account' => 'i',
    'exec_date' => 's',
    'amount' => 'i',
    'created' => 's'
  );
  
  public $table = "transaction";
  
  public function interval($from = "LASTDAY(CURDATE() - INTERVALL 1 MONTH)", $to = "CURDATE()") {
    return parent::search("exec_date > $from AND exec_date < $to");
  }
  
  public function selectQuery($id) {
    return <<<SQL
    SELECT 
      t.id AS id,
      t.user AS uid,
      t.amount AS amount,
      t.exec_date AS exec_date,
      a.id AS account,
      a.name AS accountName
    FROM transaction AS t
      LEFT JOIN account AS a
        ON t.account = a.id
    WHERE t.id = $id AND a.user = {$this->uid}
SQL;
    
  }
  
  protected function searchQuery($whereClause) {
    return <<<SQL
    SELECT 
      t.id AS id,
      t.user AS uid,
      t.amount AS amount,
      t.exec_date AS exec_date,
      a.id AS account,
      a.name AS accountName
    FROM transaction AS t
      LEFT JOIN account AS a
        ON t.account = a.id
    WHERE a.user = {$this->uid} $whereClause
SQL;
    
  }
}
