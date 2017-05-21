<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty default modifier plugin
 * Type:     modifier<br>
 * Name:     default<br>
 * Purpose:  designate default value for empty variables
 *
 * @link   http://www.smarty.net/manual/en/language.modifier.default.php default (Smarty online manual)
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_default($params)
{
    $output = $params[ 0 ];
    if (!isset($params[ 1 ])) {
        $params[ 1 ] = "''";
    }

    array_shift($params);
    foreach ($params as $param) {
        // Modified by brian@jamroom.net to check for strlen()
        $output = '(($tmp = @' . $output . ')===null||strlen($tmp)===0||$tmp===\'\' ? ' . $param . ' : $tmp)';
    }

    return $output;
}
