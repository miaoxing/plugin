<?php

namespace Miaoxing\Plugin\Controller\Cli;

use miaoxing\plugin\BaseController;
use services\Migration;

/**
 * @property Migration $migration
 */
class Migrations extends BaseController
{
    public function indexAction()
    {
        return $this->migrateAction();
    }

    public function migrateAction()
    {
        return $this->migration->migrate();
    }

    public function rollbackAction()
    {
        return $this->migration->rollback();
    }

    public function makeAction($req)
    {
        $this->v();
        var_dump($this->flags);
        var_dump($this->pa());
        var_dump($this->parseArguments());die;
        return $this->migration->make($req);
    }

    public $flags;
    public $args;
    public function v(  ) {
        $argv = $_SERVER['argv'];
        $this->flags = array();
        $this->args  = array();
        for ( $i = 0; $i < count( $argv ); $i++ ) {
            $str = $argv[$i];
            // --foo
            if ( strlen( $str ) > 2 && substr( $str, 0, 2 ) == '--' ) {
                $str = substr( $str, 2 );
                $parts = explode( '=', $str );
                $this->flags[$parts[0]] = true;
                // Does not have an =, so choose the next arg as its value
                if ( count( $parts ) == 1 && isset( $argv[$i + 1] ) && preg_match( '/^--?.+/', $argv[$i + 1] ) == 0 ) {
                    $this->flags[$parts[0]] = $argv[$i + 1];
                } elseif ( count( $parts ) == 2 ) {
                    // Has a =, so pick the second piece
                    $this->flags[$parts[0]] = $parts[1];
                }
                // -a
            } elseif ( strlen( $str ) == 2 && $str[0] == '-' ) {
                $this->flags[$str[1]] = true;
                if ( isset( $argv[$i + 1] ) && preg_match( '/^--?.+/', $argv[$i + 1] ) == 0 )
                    $this->flags[$str[1]] = $argv[$i + 1];
                // -abcdef
            } elseif ( strlen( $str ) > 1 && $str[0] == '-' ) {
                for ( $j = 1; $j < strlen( $str ); $j++ )
                    $this->flags[$str[$j]] = true;
            }
        }
        // Any arguments after the last - or --
        // FIXME: This ignores arguments before and between options
        // So in "$ php file.php foo bar --verbose baz --force quux qux"
        // it will silently ignore foo, bar and baz.
        for ( $i = count( $argv ) - 1; $i >= 0; $i-- ) {
            if ( preg_match( '/^--?.+/', $argv[$i] ) == 0 )
                $this->args[] = $argv[$i];
            else
                break;
        }
        $this->args = array_reverse( $this->args );
    }
    public function flag( $name ) {
        return isset( $this->flags[$name] ) ? $this->flags[$name] : false;
    }

    function pa()
    {
        $argv = $_SERVER['argv'];
        array_shift($argv);
        $out = array();
        foreach($argv as $arg)
        {
            if(substr($arg, 0, 2) == '--')
            {
                $eqPos = strpos($arg, '=');
                if($eqPos === false)
                {
                    $key = substr($arg, 2);
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                }
                else
                {
                    $key = substr($arg, 2, $eqPos - 2);
                    $out[$key] = substr($arg, $eqPos + 1);
                }
            }
            else if(substr($arg, 0, 1) == '-')
            {
                if(substr($arg, 2, 1) == '=')
                {
                    $key = substr($arg, 1, 1);
                    $out[$key] = substr($arg, 3);
                }
                else
                {
                    $chars = str_split(substr($arg, 1));
                    foreach($chars as $char)
                    {
                        $key = $char;
                        $out[$key] = isset($out[$key]) ? $out[$key] : true;
                    }
                }
            }
            else
            {
                $out[] = $arg;
            }
        }
        return $out;
    }

    function parseArguments()
    {
        $my_arg = $argv = $_SERVER['argv'];
        $cmd_args = array();
        $skip = array();

        global $argv;
        $new_argv = is_null( $my_arg ) ? $argv : $my_arg;

        if ( is_null( $my_arg ) ) {
            array_shift( $new_argv ); // skip arg 0 which is the filename
        }

        foreach ( $new_argv as $idx => $arg ) {
            if ( in_array( $idx, $skip ) ) {
                continue;
            }

            $arg = preg_replace( '#\s*\=\s*#si', '=', $arg );
            $arg = preg_replace( '#(--+[\w-]+)\s+[^=]#si', '${1}=', $arg );

            if (substr($arg, 0, 2) == '--') {
                $eqPos = strpos($arg, '=');

                if ($eqPos === false) {
                    $key = trim($arg, '- ');
                    $val = isset($cmd_args[$key]);

                    // We handle case: --user-id 123 -> this is a long option with a value passed.
                    // the actual value comes as the next element from the array.
                    // We check if the next element from the array is not an option.
                    if ( isset( $new_argv[ $idx + 1 ] ) && ! preg_match('#^-#si', $new_argv[ $idx + 1 ] ) ) {
                        $cmd_args[$key] = trim( $new_argv[ $idx + 1 ] );
                        $skip[] = $idx;
                        $skip[] = $idx + 1;
                        continue;
                    }

                    $cmd_args[$key] = $val;
                } else {
                    $key = substr($arg, 2, $eqPos - 2);
                    $cmd_args[$key] = substr($arg, $eqPos + 1);
                }
            } else if (substr($arg, 0, 1) == '-') {
                if (substr($arg, 2, 1) == '=') {
                    $key = substr($arg, 1, 1);
                    $cmd_args[$key] = substr($arg, 3);
                } else {
                    $chars = str_split(substr($arg, 1));

                    foreach ($chars as $char) {
                        $key = $char;
                        $cmd_args[$key] = isset($cmd_args[$key]) ? $cmd_args[$key] : true;
                    }
                }
            } else {
                $cmd_args[] = $arg;
            }
        }

        return $cmd_args;
    }
}
