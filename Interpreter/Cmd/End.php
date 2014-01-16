<?php

class End extends AbstractStatement
{
    public function parse(Parser $parser, $basic)
    {
        // nothing to do
    }

    public function execute($basic)
    {
        // exit;
        // is handled by next which will return null as next statement.
    }

    public function next($basic)
    {
        $this->parent->terminateAll($basic); // shutdown all open blocks
        // end of statement stream reached.
        return null;
    }
}
