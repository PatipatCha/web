<?php

include ('barcode.php');

$barcode = new Barcode(221000004295, 4);

$barcode->display();