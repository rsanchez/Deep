<?php

namespace rsanchez\Deep\EE;

use CI_Controller;

class Template
{
    /**
     * A copy of EE's TMPL object
     * @var EE_Template
     */
    protected $TMPL;

    /**
     * Variables collected from parse_variables or parse_variables row
     * @var array
     */
    public $variables = array();

    public function __construct(CI_Controller $ee)
    {
        // Store a local reference to the "real" TMPL object, so it can be restored on __destruct
        $this->TMPL =& $ee->TMPL;

        // Override the "real" TMPL object
        $ee->TMPL =& $this;
    }

    public function __call($name, $args)
    {
        $output = call_user_func_array(array($this->TMPL, $name), $args);

        if ($name === 'parse_variables' || $name === 'parse_variables_row') {
            $this->variables = isset($args[1]) ? $args[1] : array();
        }
        
        return $output;
    }

    public function __destruct()
    {
        // Restore the "real" TMPL object
        $ee->TMPL =& $this->TMPL;
    }
}
