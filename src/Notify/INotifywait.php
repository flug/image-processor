<?php


namespace Clooder\Notify;


use Symfony\Component\Process\Process;

class INotifywait
{
    private $path;

    public function __construct()
    {
        $process = new Process('which inotifywait');
        $process->run();
        $this->path = trim($process->getOutput());
    }

    public function getNotify(): string
    {
        return $this->path;
    }

    public function isPresent(): bool
    {
        return is_executable($this->path);
    }
}
