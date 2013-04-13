<?php

/*
 * 
 *  
 */
 class formTransaction extends view {
   protected $modelName = 'transaction';
   protected $single = true;
   
   protected function init() {
     // 
     $accounts = new accounts($this->user);
     $accounts->search("");
     $this->addValue('accounts', $accounts->data);
     
     
     $today = new DateTime();
     $this->addValue('defaultDate', $today->format('y-m-d'));
     
  }
 }
