<?php

/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty replace modifier plugin
 * Type:     modifier
 * Name:     replace
 * Purpose:  simple search/replace
 *
 * @link   http://smarty.php.net/manual/en/language.modifier.replace.php replace (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @author Uwe Tews
 *
 * @param string $string  input string
 * @param string $search  text to search for
 * @param string $replace replacement text
 *
 * @return string
 */
function smarty_modifier_getimpot($iResultatFiscal, $sTypeImpot)
{
    $iRet = 0;
    
    if ($sTypeImpot == "IS") {
        if ($iResultatFiscal > 38120) {
            if ($iResultatFiscal <= 75000) {
                $iRet = round(38120 * 0.15) + round(($iResultatFiscal - 38120) * 0.28);
            } else {
                $iRet = round(38120 * 0.15) + round(36880 * 0.28) + round(($iResultatFiscal - 75000) * 0.3333);
            }
        } else {
            $iRet = round($iResultatFiscal * 0.15);
        }
    } else {
        $tranche1 = 26764 - 9691;
        $tranche2 = 71754 - 26765;
        $tranche3 = 151956 - 71755;

        if ($iResultatFiscal > 151956)
            $iRet = round(($iResultatFiscal - 151956) * 0.45) + round($tranche3 * 0.41) + round($tranche2 * 0.3) + round($tranche1 * 0.14);
        else if ($iResultatFiscal <= 151956 && $iResultatFiscal >= 71755)
            $iRet = round(($iResultatFiscal - 71754) * 0.41) + round($tranche2 * 0.3) + round($tranche1 * 0.14);
        else if ($iResultatFiscal <= 71754 && $iResultatFiscal >= 26765)
            $iRet = round(($iResultatFiscal - 26764) * 0.3) + round($tranche1 * 0.14);
        else if ($iResultatFiscal <= 26764 && $iResultatFiscal >= 9691)
            $iRet = round(($iResultatFiscal - 9690) * 0.14);
    }

    return ceil($iRet);
}
