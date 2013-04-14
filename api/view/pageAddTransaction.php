<?php
/*
 * View definition for page for adding transactions
 */

 class pageAddTransaction extends view {
   protected $single = true;
   protected $components = Array(
     'formTransaction',
     'footer',
     'header');
   
  protected function init() {
    // A list of transactions added today
    $transactions = new transaction($this->user);
    $transactions->search('AND DATE(created) = CURDATE()');
    if(count($transactions->data)) {
      $rowView = new rowTransaction($this->user, $transactions);
      $this->addValue('transactions', $rowView->showAll());
    } else {
      $this->addValue('transactions', Array());
    }
  }
  
  
 }