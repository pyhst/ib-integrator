<?php

require_once __DIR__ . '/includes.php';

print_r(ErrorResult(new \ErrorException, 'test'));
exit();