<?php

namespace App\Command;

use App\Service\NextDrawDayService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Carbon\Carbon;

#[AsCommand(
    name: 'app:next:valid:draw:date',
    description: 'Gets the next day the Irish Lottery draw will take place.',
    hidden: false,
)]
class NextDrawDayCommand extends Command
{

    private NextDrawDayService $nextDrawDayService;

    public function __construct(
        NextDrawDayService $nextDrawDayServic,
    ) {
        $this->nextDrawDayService = $nextDrawDayServic;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('date', InputArgument::OPTIONAL, 'Date');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $requestData['date'] = $input->getArgument('date') ? $input->getArgument('date') : Carbon::now()->toATOMString();

        $status = $this->nextDrawDayService->validateSingleValue($requestData['date']);

        if ($status['status'] == false) {
            $io->error($status['message'] . ' Value => ' . $requestData['date']);
            return Command::INVALID;
        }

        $request = $this->nextDrawDayService->nextLotteryDate($requestData['date']);

        $io->success($request);
        return Command::SUCCESS;
    }
}
