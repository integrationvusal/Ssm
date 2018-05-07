<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty to_money modifier plugin
 *
 * Type:     modifier<br>
 * Name:     to_money<br>
 * Purpose:  to money change
 *
*/
 
function smarty_modifiercompiler_to_money($params, $compiler)
{
	return "number_format((float){$params[0]}, 2, '.', '')";
}

?>