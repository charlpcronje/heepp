<?php
namespace core\extension\ui;
use core\Heepp;

class Tidy extends Heepp {
    public $config = [
        'show-body-only' => false,
        'clean' => false,
        'char-encoding' => 'utf8',
        'add-xml-decl' => false,
        'add-xml-space' => false,
        'output-html' => true,
        'output-xml' => false,
        'output-xhtml' => false,
        'numeric-entities' => false,
        'ascii-chars' => false,
        'doctype' => 'html',
        'bare' => false,
        'fix-uri' => false,
        'indent' => true,
        'indent-spaces' => 4,
        'tab-size' => 4,
        'wrap-attributes' => true,
        'wrap' => 1,
        'indent-attributes' => false,
        'join-classes' => false,
        'join-styles' => false,
        'enclose-block-text' => true,
        'fix-bad-comments' => false,
        'fix-backslash' => false,
        'replace-color' => false,
        'wrap-asp' => false,
        'wrap-jste' => false,
        'wrap-php' => false,
        'write-back' => true,
        'drop-proprietary-attributes' => false,
        'hide-comments' => false,
        'hide-endtags' => false,
        'literal-attributes' => false,
        'drop-empty-paras' => false,
        'enclose-text' => true,
        'quote-ampersand' => true,
        'quote-marks' => true,
        'quote-nbsp' => false,
        'vertical-space' => true,
        'wrap-script-literals' => false,
        'tidy-mark' => false,
        'merge-divs' => false,
        'repeated-attributes' => 'keep-all',
        'break-before-br' => false,
    ];
    public $whatToTidy;
    private $tidy;
    
    function __construct ($whatToTidy = null) {
        parent::__construct () ;
        
        $this->tidy = new \tidy();
        
        if (isset($whatToTidy)) {
            $this->whatToTidy = $whatToTidy;
        }
    }
    
    function tidy() {
        return $this->tidy->repairString($this->whatToTidy,$this->config,'UTF8');
    }
}
