<?php

/*
 * View defintions for budget settings page
 */

 class pageBudget extends view {
   protected function preprocess() {
     $footer = new footer($this->user);
     $this->data["footer"] = $footer->show(get_class($this));
  }
 }