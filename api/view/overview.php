<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class overview extends view {
   private $model = 'overview';


   public function preprocess() {
     $today = new DateTime();
     $this->data->date_suggestion = $today->format('y-m-d'); 
  }
 }