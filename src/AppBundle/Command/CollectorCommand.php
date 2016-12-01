<?php
# src/AppBundle/Command/CollectorCommand.php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class CollectorCommand extends ContainerAwareCommand
{
    const TASK_UPDATE_STABLE_RELEASE = 'stable_release';
    const TASK_UPDATE_MARKET_SHARE   = 'market_share';

    protected function configure()
    {
        $this
            ->setName('brasst:collect')
            ->setDescription('Update system with important collected web data')
            ->addArgument(
                'task',
                InputArgument::REQUIRED,
                'What update task to execute?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $task = $input->getArgument('task');

        switch( $task )
        {
            case self::TASK_UPDATE_STABLE_RELEASE:
                $this->updateStableRelease();
            break;

            case self::TASK_UPDATE_MARKET_SHARE:
                $this->updateMarketShare();
            break;
        }

        $output->writeln('Done!');
    }

    private function updateStableRelease()
    {
        $_collector = $this->getContainer()->get('collector');

        try {
            $_collector->updateBrowserStableRelease();

            $this->logResult("***CollectorCommand***: Stable Release updated");
        } catch(\Exception $e) {
            $this->logResult("***CollectorCommand***: Failed to update Stable Release with exception - " . $e->getMessage());
        }
    }

    private function updateMarketShare()
    {
        $_collector = $this->getContainer()->get('collector');

        try {
            $_collector->updateBrowsersMarketShare();

            $this->logResult("***CollectorCommand***: Market Share updated");
        } catch(\Exception $e) {
            $this->logResult("***CollectorCommand***: Failed to update Market Share with exception - " . $e->getMessage());
        }
    }

    private function logResult($result)
    {
        $_logger = $this->getContainer()->get('logger');

        $_logger->info($result);
    }
}