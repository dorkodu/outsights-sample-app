<?php
	interface DBInterface {
		public function setConnection(ConnectionInterface $conn);
		public function query($dbQueryString);
	}
?>
