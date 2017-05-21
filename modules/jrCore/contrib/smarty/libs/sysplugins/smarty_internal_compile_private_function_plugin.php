<?php
/**
 * Smarty Internal Plugin Compile Function Plugin
 * Compiles code for the execution of function plugin
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Function Plugin Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Private_Function_Plugin extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array();

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('_any');

    /**
     * Compiles code for the execution of function plugin
     *
     * @param  array                                $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                $parameter array with compilation parameter
     * @param  string                               $tag       name of function plugin
     * @param  string                               $function  PHP function name
     *
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter, $tag, $function)
    {
        global $_mods;

        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        unset($_attr[ 'nocache' ]);
        // convert attributes into parameter array string
        $_paramsArray = array();
        foreach ($_attr as $_key => $_value) {
            if (is_int($_key)) {
                $_paramsArray[] = "$_key=>$_value";
            } else {
                $_paramsArray[] = "'$_key'=>$_value";
            }
        }
        $_params = 'array(' . implode(",", $_paramsArray) . ')';

        // Modified by brian@jamroom.net to check if function exists and module is active
        $module = substr($function, 16);
        $module = substr($module, 0, strpos($module, '_'));
        if (isset($_mods[$module]) && !jrCore_module_is_active($module)) {
            return '';
        }

        // compile code
        return "<?php if (function_exists('{$function}')) { echo {$function}({$_params},\$_smarty_tpl); } ?>";
    }
}
