<?php
/**
 * Created by PhpStorm.
 * User: mivanov
 * Date: 4/13/18
 * Time: 1:59 PM
 */

namespace Mivanov\LazyJira;


use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\IssueService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListIssuesCommand extends Command
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
        $this->addOption("owner", "o", InputOption::VALUE_REQUIRED, "Assignee", "currentUser()");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $owner = $input->getOption("owner");
        $jql   = "project in (ADE)  and assignee = {$owner}";

        $ret = $this->jiraClient->search($jql);
        /** @var Issue $issue */
        foreach ($ret->getIssues() as $issue) {
            $output->writeln($issue->key);
        }
    }
}
