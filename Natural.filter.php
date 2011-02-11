<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Filter the code to make it natural with programmers doing
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 * @category   PHP
 * @package PHP_Beautifier
 * @subpackage Filter
 * @author Jun Harada <harajune@gijutsuya.jp>
 * @copyright  2011-2011 Jun Harada
 * @link     http://www.gijutsuya.jp/
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 */
/**
 * Require PEAR_Config
 */
require_once ('PEAR/Config.php');

class PHP_Beautifier_Filter_Natural extends PHP_Beautifier_Filter
{
    protected $aSettings = array('add_header' => false, 'newline_class' => true, 'newline_function' => true, 'switch_without_indent'=> true);
    protected $sDescription = 'Filter the code to make it natural with human';
    private $bEqual = false;
    private $bDot = false;

    function t_close_tag($sTag)
    {
        $this->oBeaut->removeWhitespace();
        $count = preg_match_all("/\n/", $this->oBeaut->getPreviousWhitespace(), $aMatches);
        if ($count) {
            for ($i=0; $i<$count; $i++) {
                $this->oBeaut->addNewLine();
            }
            $this->oBeaut->add($sTag);
        } else {
            $this->oBeaut->add(" " . $sTag);
        }
    }

    function t_whitespace($sTag)
    {
        $count = preg_match_all("/\n/", $sTag, $aMatches);
        if ($count > 0) {
            $this->oBeaut->removeWhitespace();

            for ($i = 0; $i<$count-1; $i++) {
                $this->oBeaut->addNewLine();
            }
            $this->oBeaut->addNewLineIndent();
        }
    }

    function t_catch($sTag)
    {
        $this->oBeaut->removeWhitespace();
        $this->oBeaut->add(" ".$sTag." ");
    }

    function t_dot($sTag)
    {
        $ptoken = $this->oBeaut->getToken($this->oBeaut->iCount - 1);
        $ntoken = $this->oBeaut->getToken($this->oBeaut->iCount + 1);
        $this->oBeaut->removeWhitespace();
        if (preg_match("/\n/", $ptoken[1])) {
            if (!$this->bDot) {
                $this->oBeaut->incIndent();
                $this->bDot = true;
            }
            $this->oBeaut->addNewLineIndent();
            $this->oBeaut->add($sTag);
        } elseif(preg_match("/\n/", $ntoken[1])) {
            if (!$this->bDot) {
                $this->oBeaut->incIndent();
                $this->bDot = true;
            }
            $this->oBeaut->add($sTag);
            //$this->oBeaut->addNewLineIndent();
        } else {
            $this->oBeaut->add(" ".$sTag." ");
        }
    }
    function t_logical($sTag)
    {
        $ptoken = $this->oBeaut->getToken($this->oBeaut->iCount - 1);
        $this->oBeaut->removeWhitespace();
        if (preg_match("/\n/", $ptoken[1])) {
            $this->oBeaut->addNewLineIndent();
            $this->oBeaut->add($sTag." ");
        } else {
            $this->oBeaut->add(" ".$sTag." ");
        }
    }
    function t_comment($sTag)
    {
        $this->oBeaut->add(trim($sTag));
        $this->oBeaut->addNewLineIndent();
    }
    function t_parenthesis_open($sTag) 
    {
        $this->oBeaut->add($sTag);
        if ($this->oBeaut->getControlParenthesis() == T_ARRAY) {
            $this->oBeaut->incIndent();
        }
    }
    function t_parenthesis_close($sTag) 
    {
        $this->oBeaut->removeWhitespace();
        if ($this->oBeaut->getControlParenthesis() == T_ARRAY) {
            $this->oBeaut->decIndent();
            $this->oBeaut->add($sTag . ' ');
        } else {
            $this->oBeaut->add($sTag . ' ');
        }
    }
    function t_assigment($sTag)
    {
        $ptoken = $this->oBeaut->getToken($this->oBeaut->iCount + 1);
        if (preg_match("/\n/", $ptoken[1])) {
            $this->oBeaut->add(" ".$sTag);
            $this->oBeaut->incIndent();
            $this->bEqual = true;
        } else {
            $this->oBeaut->add(" ".$sTag." ");
        }
    }
    function t_semi_colon($sTag)
    {
        if ($this->bEqual) {
            $this->oBeaut->decIndent();
            $this->bEqual = false;
        }
        if ($this->bDot) {
            $this->oBeaut->decIndent();
            $this->bDot = false;
        }
        $this->oBeaut->removeWhitespace();
        $this->oBeaut->add($sTag);
    }

}

