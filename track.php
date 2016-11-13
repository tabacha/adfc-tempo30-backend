<?php
$log=sprintf("%d %s\n",time(),$_SERVER['QUERY_STRING']);
echo $log;
file_put_contents('/var/log/tempo30/track_'.date("Y-n-j").'.log', $log, FILE_APPEND);