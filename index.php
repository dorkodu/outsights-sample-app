<?php
  require_once 'loot/loom-weaver.php';
  /*
    require_once 'outsights/bootstrap.php';
  */

  use Outsights\PageWeaver\PageWeaver;

  $pw = new PageWeaver();
  $pageContent = $pw->composeStaticPage("about");
  var_dump($pageContent);
  # $pw->render($pw->composeStaticPage("about"));
  
  