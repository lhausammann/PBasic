<?php
class Input extends AbstractStatement
{
    protected $input;
    protected $value = '';
    protected $message = '';
    public function parse(Parser $parser, $basic)
    {
        $lexer = $parser->getLexer();
        $first = $lexer->next();
        if ($first->type == Token::STRING) {
            $this->message = $first->value;
            $parser->next();
        } else {
            $lexer->setNext($first); // put it back
        }

        $this->input = $parser->matchExpression($lexer);

    }

    public function execute($basic)
    {
        $name =$this->input->token->value;
        if ($this->message) {
            $label = $this->message;
        } else {
            $label = $name;
        }

        if (isset($_GET[$name])) {
            $value = $this->value = $_GET[$name];
            $basic->setVar($name, $value);
        } else {

            echo "<form  method='get' id='bInput' style='color:" . $basic->getForegroundColor() . ";background-color:" .$basic->getBackgroundColor() . "'>
                    <input type='hidden' name='currentInstruction' value='{$this->nr}' />
                    <label><pre>{$label}</pre></label><input type='text' name='{$name}' style='background-color: " . $basic->getBackgroundColor() .";color: ".$basic->getForegroundColor().";border:none;'  />

                    </form>
                    <script>
                    window.setTimeout(function () {
                        document.getElementById('bInput').{$name}.focus()
                        }, 100);
                        ;</script>
                    ";
        }
    }

    public function next($basic)
    {
        if ((isset($_GET['currentInstruction']) && $_GET['currentInstruction']) == $this->nr) {
            $basic->loadScope();
            $basic->loadCallStack(); // restore the call stack
            $basic->setForegroundColor($basic->getScope()->getVar('__foreground'));
            $basic->setBackgroundColor($basic->getScope()->getVar('__background'));
            $this->execute($basic);

            unset($_GET['currentInstruction']);
            unset($_GET[$this->input->token->value]);
        } else {
            $basic->setVar('__foreground', $basic->getForegroundColor(true));
            $basic->setVar('__background', $basic->getBackgroundColor(true));
            $basic->saveCallStack(); // store all return addresses in case input is called in a SUB.
            $basic->saveScope();

            return null; // HALT
        }
        $next = parent::next($basic);

        return $next;
    }
}
