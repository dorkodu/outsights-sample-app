<?php
  require_once 'loot/loom-weaver.php';

  /*
    require_once 'outsights/bootstrap.php';
  */

  use Outsights\Outsettler\Outsettler;

  $setly = new Outsettler();
  $setly->switchEnvironment("DEV");
  $setly->set("a", "b");
  $setly->get("a");