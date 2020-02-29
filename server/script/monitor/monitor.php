<?php

class Monitor
{
    const PORT = 8811;

    public function monitorPort()
    {
        $shell = 'lsof -i:8811 2>/dev/null | grep LISTEN | wc -l';
        $info = shell_exec($shell);
        if ($info != 1) {
            //TODO 短信、邮件、钉钉、微信等报警
            echo date('Ymd H:i:s') . 'error' . PHP_EOL;
        } else {
            echo date('Ymd H:i:s') . 'success' . PHP_EOL;
        }
    }
}

swoole_timer_tick(2000, function () {
    (new Monitor())->monitorPort();
});