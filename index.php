<?php
  require_once 'loot/loom-weaver.php';
  /*
    require_once 'outsights/bootstrap.php';
  */
  echo "<pre>";
  var_dump($_FILES);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <form action="index.php" method="post">
    <input type="file" name="photo">
    <button type="submit">Send</button>
  </form>
</body>
</html>