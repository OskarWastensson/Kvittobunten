<?php

/*
 */

class transactionList extends view {
  protected $modelName = 'transaction'; 
  
  protected function preprocess() {
    $this->model->list( Array (
        'from' => isset($_GET['from']) ? "'". make_nice($_GET['from'])."'" : NULL,
        'to' => isset($_GET['to']) ? "'". make_nice($_GET['to'])."'" : NULL));
  }
}



/*
  private function fieldListView() {
    $from = isset($_GET['from']) ? "'".make_nice($_GET['from'])."'" : NULL;
    $to = isset($_GET['to']) ? "'".make_nice($_GET['to'])."'" : NULL;
    
    $data = $this->fetchIntervall($from, $to);
    $list = '';
    if(is_array($data)) foreach($data as $row) {
      $list .= $this->render('fieldListItem', $row); 
    }
    return $this->render('fieldList', array('content' => $list));
  }
  
 */