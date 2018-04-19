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
use Mivanov\LazyJira\Helper\ConsoleFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListIssuesCommand extends Command
{
    /**
     * @var IssueService
     */
    private $jiraClient;
    /**
     * @var string
     */
    private $defaultProjects;
    /**
     * @var ConsoleFormatter
     */
    private $consoleFormatter;

    public function __construct(
        string $name = null,
        ConsoleFormatter $consoleFormatter,
        IssueService $jiraClient,
        array $defaultProjects
    ) {
        parent::__construct($name);

        $this->jiraClient       = $jiraClient;
        $this->defaultProjects  = $defaultProjects;
        $this->consoleFormatter = $consoleFormatter;
    }

    protected function configure()
    {
        $this->setDescription("Lists issues(by default your own)");
        $this->addOption("owner", "o", InputOption::VALUE_REQUIRED, "Assignee", "currentUser()");
    }


    protected
    function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $owner           = $input->getOption("owner");
        $projectImploded = implode(',', $this->defaultProjects);
        $jql             = "project in ({$projectImploded})  and assignee = {$owner}";

        $ret = $this->jiraClient->search($jql);
        /** @var Issue $issue */


        $results = [];
        foreach ($ret->getIssues() as $issue) {
            $issueRow   = [];
            $issueRow[] = "<bg=green;fg=black>{$issue->key}</>";
            $issueRow[] = "<bg=black;options=bold>{$issue->fields->summary}</>";
            $issueRow[] = "<bg=black>{$issue->fields->assignee->name}</>";
            $issueRow[] = $this->consoleFormatter->issueStatusColored($issue->fields->status->name);
            $issueRow[] = $this->consoleFormatter->issueTypeColored($issue->fields->getIssueType()->name);
            $results[]  = $issueRow;
            if (isset($issue->fields->description)) {
                $descData  = $this->consoleFormatter->intervalEveryN($issue->fields->description, 150, 3);
                $descRow   = [
                    new TableCell("<fg=yellow>" . $descData["text"] . "</>",
                        ['rowspan' => $descData["rowSpan"], 'colspan' => 5])
                ];
                $results[] = [
                    new TableCell("",
                        ['colspan' => 5])
                ];
                $results[] = $descRow;
            }
            $results[] = [
                new TableCell("",
                    ['colspan' => 5])
            ];
            $results[] = new TableSeparator();
        }
        unset($results[count($results) - 1]);
        $table = new Table($output);
        $table
            ->setHeaders(['Issue', 'Title', 'Owner', 'Status', 'Type'])
            ->setRows($results);
        $table->render();
    }
}
