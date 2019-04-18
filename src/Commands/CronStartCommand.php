<?php

namespace Tolacika\CronBundle\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CronStartCommand extends Command
{
    const PID_FILE = '.cron-bundle-pid';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron-bundle:start'
    . ' {--b|blocking : Run in blocking mode.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts cron scheduler';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        if ($this->option('blocking')) {
            $this->output->writeln(sprintf('<info>%s</info>', 'Starting cron scheduler in blocking mode.'));
            $this->scheduler(null);

            return 0;
        }
        if (!extension_loaded('pcntl')) {
            throw new \RuntimeException('This command needs the pcntl extension to run.');
        }
        $pidFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::PID_FILE;
        $this->output->writeln("Pid file: " . $pidFile);
        if (-1 === $pid = pcntl_fork()) {
            throw new \RuntimeException('Unable to start the cron process.');
        } elseif (0 !== $pid) {
            if (false === file_put_contents($pidFile, $pid)) {
                throw new \RuntimeException('Unable to create process file.');
            }
            $this->output->writeln(sprintf('<info>%s</info>', 'Cron scheduler started in non-blocking mode...'));

            return 0;
        }
        if (-1 === posix_setsid()) {
            throw new \RuntimeException('Unable to set the child process as session leader.');
        }
        $this->scheduler($pidFile);

        return 0;
    }

    /**
     * @param bool $verbose
     * @param string $pidFile
     * @throws \Exception
     */
    private function scheduler(?string $pidFile)
    {
        $command = $this->getApplication()->find("cron-bundle:execute");

        while (true) {
            $now = microtime(true);
            $ms = (60 - ($now % 60) + (int) $now - $now) * 1e6;

            if ($this->output->isDebug()) {
                $this->output->writeln(sprintf("<comment>Sleeping %d s %d ms...</comment>", floor($ms / 1e6), floor($ms / 1e3 % 1e3)));
            }

            usleep((int) $ms);

            if (null !== $pidFile && !file_exists($pidFile)) {
                $this->output->writeln("<info>Process killed</info>");
                break;
            }

            $args = [];
            if ($this->output->isDebug()) {
                $args['-vvv'] = true;
            } elseif ($this->output->isVeryVerbose()) {
                $args['-vv'] = true;
            } elseif ($this->output->isVerbose()) {
                $args['-v'] = true;
            }

            $command->run(new ArrayInput($args), $this->output->isVerbose() ? $this->output : new NullOutput());
        }
    }
}
