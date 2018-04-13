<?php
/**
 * Created by PhpStorm.
 * User: mivanov
 * Date: 4/13/18
 * Time: 1:59 PM
 */

namespace Mivanov\LazyJira;


use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Transition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MoveToDoneCommand extends Command
{
    /**
     * @var IssueService
     */
    private $jiraClient;

    public function __construct(string $name = null, IssueService $jiraClient)
    {
        parent::__construct($name);

        $this->jiraClient = $jiraClient;
    }

    protected function configure()
    {
        $this->addArgument("issueId", InputArgument::REQUIRED, "Issue name(ex. ADE-233)");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $issueId    = $input->getArgument("issueId");
        $transition = new Transition();
        try {
            $transition->setTransitionId('411');
            $this->jiraClient->transition($issueId, $transition);
        } catch (\Throwable $e) {
        }
        try {
            $transition->setTransitionId('51');
            $this->jiraClient->transition($issueId, $transition);
        } catch (\Throwable $e) {
        }
        try {
            $transition->setTransitionId('61');
            $this->jiraClient->transition($issueId, $transition);
        } catch (\Throwable $e) {
        }
        try {
            $transition->setTransitionId('71');
            $this->jiraClient->transition($issueId, $transition);
        } catch (\Throwable $e) {
        }
    }
}
