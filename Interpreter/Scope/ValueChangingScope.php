<?php
namespace PBasic\Interpreter\Scope;

/* Experimental scope: add behavior for special vars. 
 * In real world this could be used to enforce stricter enforcement at 
 * runtimge (e.g. disallow adding ints to string, adding doubles to int asf 
 * when using the original a&, a$ and a# syntax of basic).
 */

class ValueChangingScope extends GlobalScope
{

    // Note: You must _not_ use that variables for string comparison.
    private $interestingVars = array(
        "haz", // extends strings containing "beer"
        "rai", // can output "hö" and really extend the language with stack-behaviour (not implemented by default)
        "nes", // does not like strings containing "schätzungsmeeting" -- slows down execution significantly. 
        "vec", // can revert suddenly to a value set prior than the current one. Can throw Exceptions. Can change values in a subtle manner.
        "sdp", // can decorate strings with spans, font colors asf.
        "edh",  // can display alerts to say you should like that page when combined with "pulse", "jessica", "style", "international"
    );

    public function setVar($name, $value)
    {
        if ($this->isSpecialVar($name) && !$this->getGlobalVar($name, false)) {
            // because those variables are so important they affect the global space.
            $this->initSpecialVar($name, $value);
        } else if($this->isSpecialVar($name)) {
            echo "transform";
            $this->transformSpecialVar($name, $value);
        } else {
            parent::setVar($name, $value);
        }
    }

    protected function initSpecialVar($name, $value) {
        $call = "initValue". ucfirst($name);
        $this->transform($call, $name, $value); 
    }

    protected function transformSpecialVar($name, $value) {
        $call = "changeValue". ucfirst($name);
        $this->transform($call, $name, $value);
    }

    protected function transform($call, $name, $value) {
        if (method_exists($this, $call)) {
            $transformedValue = call_user_func_array(array($this, $call), array($value));
            $this->setGlobalVar($name, $transformedValue);
        }
    }

    protected function isSpecialVar($name) {
        return in_array($name, $this->interestingVars);
    }

    // no time to factor out those behaviours to its separate class hö
    protected function initValueEdh($value) {
        if (is_string($value)) {
            return "<script>alert('Go and like pulse!');</script>" . $value;
        }
    }
    protected function initValueHaz($value) {
        if (stripos($value, 'beer') !== false) {
            // with beer Luzius is more talkative.
            return str_repeat($value, 2);
        }
    }

    protected function changeValueHaz($value) {
        if (stripos($value, 'beer') !== false) {
            return $value . "(Prost)";
        } 

        return $value;
    }
}

