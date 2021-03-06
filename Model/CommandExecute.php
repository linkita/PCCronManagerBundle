<?php
namespace Trovit\CronManagerBundle\Model;

use Trovit\CronManagerBundle\Exception\CommandNotExistsException;
use Symfony\Component\Process\Process;

/**
 * Class that allows to execute symfony2 commands in a process appart
 *
 * @package Trovit\CronManagerBundle\Model
 */
class CommandExecute
{
    /**
     * @var string
     */
    private $_consoleCommand;
    /**
     * @var CommandValidator
     */
    private $_commandValidator;
    /**
     * @var string
     */
    private $_environment;

    /**
     * CommandExecute constructor.
     *
     * @param CommandValidator $commandValidator
     * @param                  $consoleCommand
     * @param                  $environment
     */
    public function __construct(CommandValidator $commandValidator, $consoleCommand, $environment)
    {
        $this->_consoleCommand = $consoleCommand;
        $this->_commandValidator = $commandValidator;
        $this->_environment = $environment;
    }

    /**
     * @param $command
     * @return int
     */
    public function executeCommand($command)
    {
        $process = new Process($this->_getCommandString($command));
        $process->run();

        return $process->getPid();
    }

    /**
     * @param $command
     * @return int|null
     */
    public function executeBackgroundCommand($command)
    {
        $process = new Process($this->_getCommandString($command). ' > /dev/null &');
        $process->start();

        return $process->getPid();
    }

    /**
     * @param $command
     * @return string
     * @throws CommandNotExistsException
     */
    private function _getCommandString($command)
    {
        if (!$this->_commandValidator->commandExists($command)) {
            throw new CommandNotExistsException($command);
        }
        return $this->_consoleCommand.' '.$command . ' --env=' . $this->_environment;
    }
}