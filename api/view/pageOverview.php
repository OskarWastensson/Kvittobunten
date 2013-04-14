<?php

/*
 * View definition for monthly overview page
 */

 class pageOverview extends view {
   protected function preprocess() {
     $footer = new footer($this->user);
     $this->data["footer"] = $footer->show(get_class($this));
  }
 }