<?php

ignore_user_abort(true);
//即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
set_time_limit(0);
do {
$time = date('Y-m-d H:i:s', time()) . "\t";
file_put_contents('./test.txt', $time, FILE_APPEND);
sleep(60);
} while (true);
echo 'ok';

?>