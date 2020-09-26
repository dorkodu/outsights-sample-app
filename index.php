<?php
  require_once 'loot/loom-weaver.php';
  /*
    require_once 'outsights/bootstrap.php';
  */

  use Outsights\PageWeaver\PageWeaver;
  use Outsights\PageWeaver\Page;

  
  $pw = new PageWeaver();

  $cont = $pw->composePage("front", array("description" => "This is the desc", "keywords" => "sample, page", "content" => "This is the content", "owner" => "owner : Doruk Dorkodu", "title" => "Titlerrr"));
  echo $cont;