<?php
  # routes for the Outsights application
  
  use Outsights\Router\Router;

  Router::run("/", function() {
    echo "the Index Page";
  });