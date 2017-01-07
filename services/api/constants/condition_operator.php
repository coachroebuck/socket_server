<?php

// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

abstract class condition_operator
{
    const Equal 				= 0;
    const NotEqual 				= 1;
    const GreaterThan 			= 2;
    const LessThan 				= 3;
    const GreaterThanOrEqual 	= 4;
    const LessThanOrEqual 		= 5;
    const Between 				= 6;
    const Like 					= 7;
    const In 					= 8;
    const NotNull               = 9;
    const Is                    = 10;
    const IsNot                 = 11;
    const RegExp                = 12;
    const NotRegExp             = 13;
    const RegExpLike            = 14;
}

?>