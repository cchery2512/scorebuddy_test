<?php

namespace App\Command;

use App\Repository\LogRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-old-records',
    description: 'Deletes records older than 1 minute',
    hidden: false,
)]
class DeleteOldRecordsCommand extends Command
{
    private $logRepository;
    public function __construct(private LogRepository $logRepositor)
    {
        $this->logRepository = $logRepositor;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->logRepository->deleteOldLogs();
        $io->success('Old records deleted successfully.');
        return Command::SUCCESS;
    }
}
