<?php
declare(strict_types=1);

namespace Studio24\MampPHP\Service;

use Studio24\MampPHP\Exception\ExecException;

/**
 * Class to manage running CLI commands
 *
 * @package MampPHP\Service
 */
class CommandRunner
{
    /**
     * Array of the command output, one line per array row
     *
     * @var array
     */
    protected $output = [];

    /**
     * Run a command through exec
     *
     * Throws an exception on error
     *
     * @param string $command
     * @param string $changeDirectory Directory to change to before running command
     *
     * @return string The last line from the result of the command, get all output via $this->getOutput()
     * @throws ExecException
     * @throws \Exception
     */
    public function exec(string $command, string $changeDirectory = null) : string
    {
        if ($changeDirectory !== null) {
            chdir($changeDirectory);
        }

        $this->output = [];
        $results = exec($command, $this->output, $return);

        if ($return != 0) {
            $message = "Command '$command' returned error code: " . $return;
            throw new ExecException($message);
        }

        return $results;
    }

    /**
     * Return full command output as an array
     *
     * @return array
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Return first line of the output
     *
     * @return string
     */
    public function getOutputFirstLine() : string
    {
        return $this->output[0];
    }


}