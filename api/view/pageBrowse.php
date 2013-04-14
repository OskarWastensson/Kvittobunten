<?php

/*
 * View definition for page for browsing records
 */

 class pageBrowse extends view {
   protected function preprocess() {
     $footer = new footer($this->user);
     $this->data["footer"] = $footer->show(get_class($this));
  }
 }