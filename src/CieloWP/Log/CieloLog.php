<?php

/*
Class CieloLog
Author Camilo da Silveira
Site silveiracamilo.com.br
*/

namespace CieloWP\Log;

use Psr\Log\AbstractLogger;
// use Illuminate\Support\Facades\Log;

class CieloLog extends AbstractLogger
{
    public function log($level, $message, array $context = array()) 
    {
        // Log::debug("CIELO::".$message);
        // Log::debug($context);
    }
}