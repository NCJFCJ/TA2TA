<?php

namespace IAWP\Utils;

/** @internal */
class Server
{
    public static function increase_max_execution_time() : void
    {
        if (self::is_function_enabled('ignore_user_abort')) {
            @\ignore_user_abort(\true);
        }
        if (self::is_function_enabled('set_time_limit')) {
            @\set_time_limit(16000);
        }
        if (self::is_function_enabled('ini_set')) {
            @\ini_set('max_execution_time', '259200');
            @\ini_set('max_input_time', '259200');
            @\ini_set('session.gc_maxlifetime', '1200');
        }
    }
    private static function is_function_enabled(string $the_function) : bool
    {
        $disabled_functions = \explode(',', \ini_get('disable_functions'));
        $isDisabled = \in_array($the_function, $disabled_functions);
        return !$isDisabled && \function_exists($the_function);
    }
}
