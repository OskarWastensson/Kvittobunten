<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

 class pageBrowse extends view {
   protected function preprocess() {
     $footer = new footer($this->user);
     $this->data["footer"] = $footer->show(get_class($this));
  }
 }