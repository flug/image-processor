<?php


namespace Clooder\Notify;


use Symfony\Component\Process\Process;

class FileNotify
{
    private $notifywait;

    public function __construct(INotifywait $notifywait)
    {
        if (!$notifywait->isPresent()) {
            throw new \RuntimeException('You schould install inotify-tools');
        }
        $this->notifywait = $notifywait;
    }

    public function loop($directory, callable $callback)
    {
        $process = $this->build($directory);
        $process->start();
        $process->wait(function ($type, $buffer) use ($callback) {
            if (Process::ERR !== $type) {
                $callback($this->format($buffer));
            }


        });
    }

    private function build($directory)
    {
        $processBuilder = new Process(strtr('%notifyBin% -m -e close_write,moved_to,create,modify  --format \'%w%f##%e\'  %directoryWatching%  -r',
            [
                '%notifyBin%' => $this->notifywait->getNotify(),
                '%directoryWatching%' => $directory
            ]));

        $processBuilder->setTimeout(0);
        return $processBuilder;
    }


    private function format($buffer)
    {
        $notify = explode("##", trim($buffer));

        return [
            'path' => $notify[0],
            'events' => array_map('trim', explode(',', $notify[1]))
        ];
    }

}
