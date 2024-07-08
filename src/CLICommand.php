<?php

declare(strict_types=1);

namespace Pop;

use Generator;
use Exception;

/**
 * Takes an executable and some arguments
 * and runs it, capturing everything from
 * stderr and stdout to use.
 */
class CLICommand
{
    public function __construct(
        public string $executable,
        public array $commandArgs,
        public ?string $workingDir = null,
        public array $environment = [],
        protected array $descriptors = [],
        protected array $pipes = [],
    ) {
        //^^
        $this->descriptors = [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['pipe', 'w']
        ];

        if (!isset($_SERVER['PATH'])) {
            throw new Exception(
                'We can\'t run a CLI command, the \'PATH\' variable is missing.'
            );
        }

        $this->environment = array_merge(
            [
                'PATH' => $_SERVER['PATH']
            ],
            $this->environment
        );
    }

    public function run(): Generator
    {
        $command = escapeshellarg($this->executable);

        foreach ($this->commandArgs as $arg) {
            $command .= ' ' . escapeshellarg($arg);
        }

        $proc = proc_open(
            $command,
            $this->descriptors,
            $this->pipes,
            $this->workingDir,
            $this->environment
        );

        // If we couldn't start for any reason, stop here.
        if (!is_resource($proc)) {
            throw new Exception(
                sprintf(
                    '"%s" did not start correctly.',
                    $command
                )
            );
        }

        fclose($this->pipes[0]);

        while (true) {
            $status = proc_get_status($proc);

            if ($status['running'] === false) {
                break;
            }

            yield stream_get_contents($this->pipes[1]);
            yield stream_get_contents($this->pipes[2]);
        }

        fclose($this->pipes[1]);
        fclose($this->pipes[2]);

        proc_close($proc);

        if ($status['exitcode'] !== 0) {
            throw new Exception(
                sprintf(
                    '"%s" returned a failed exit code: "%s".',
                    $command,
                    (string)$status['exitcode']
                )
            );
        }

        yield $status;
    }

    public function go(): int
    {
        foreach ($this->run() as $output) {
            if (is_string($output)) {
                echo $output;
            } else {
                echo PHP_EOL;
                return $output['exitcode'];
            }
        }

        return -1;
    }
}
