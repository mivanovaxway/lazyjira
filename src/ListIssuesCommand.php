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
use JiraRestApi\Issue\JqlQuery;
use Mivanov\LazyJira\Helper\ConsoleFormatter;
use Mivanov\LazyJira\Helper\IssueConsoleFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
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
        $this->addOption("assignee", "a", InputOption::VALUE_REQUIRED, "Assignee");
        $this->addOption("reporter", "r", InputOption::VALUE_REQUIRED, "Reporter");
        $this->addOption("mine", "m", InputOption::VALUE_NONE, "Show just mine issues");
        $this->addOption("projects", "p", InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, "Projects(array)");
        $this->addOption("types", "t", InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, "Issue types");
        $this->addArgument("offset", InputArgument::OPTIONAL, "Result offset", 0);
        $this->addArgument("maxResults", InputArgument::OPTIONAL, "Max results", 15);
    }


    protected
    function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $assignee   = $input->getOption("assignee");
        $reporter   = $input->getOption("reporter");
        $mine       = $input->getOption("mine");
        $types      = $input->getOption("types");
        $projects   = !empty($input->getOption("projects")) ? $input->getOption("projects") : $this->defaultProjects;
        $offset     = $input->getArgument("offset");
        $maxResults = $input->getArgument("maxResults");
        $jqlQuery   = new JqlQuery();

        if ($mine) {
            $jqlQuery->addAnyExpression("\"assignee\" = currentUser()");
        } else {
            if (!empty($reporter)) {
                $jqlQuery->addExpression("reporter", "=", $reporter);
            }
            if (!empty($assignee)) {
                $jqlQuery->addExpression("assignee", "=", $assignee);
            }
        }
        $jqlQuery->addInExpression("project", $projects);
        if (!empty($types)) {
            $jqlQuery->addInExpression("type", $types);
        }
        $ret     = $this->jiraClient->search($jqlQuery->getQuery(), $offset, $maxResults);
        $results = [];
        $header  = ['Issue', 'Title', 'Assignee', 'Status', 'Type', 'Versions'];
        /** @var Issue $issue */
        foreach ($ret->getIssues() as $issue) {
            echo var_export($issue->fields->getCustomFields()['customfield_11730'], true);
            echo var_export($issue->fields->versions, true);
            $formattedIssue = new IssueConsoleFormatter($issue);
            $issueRow       = [];
            $issueRow[]     = $formattedIssue->getKey();
            $issueRow[]     = $formattedIssue->getSummary();
            $issueRow[]     = $formattedIssue->getAssignee();
            $issueRow[]     = $formattedIssue->getStatus();
            $issueRow[]     = $formattedIssue->getTypeName();
            $issueRow[]     = $formattedIssue->getVersions();
            $results[]      = $issueRow;
            $consoleWidth   = intval(getenv('COLUMNS') / 1.6);
            $descMaxRows    = 3;
            if ($formattedIssue->hasDescription()) {
                $descData  = $this->consoleFormatter->intervalEveryN($issue->fields->description, $consoleWidth,
                    $descMaxRows);
                $descRow   = [
                    new TableCell($descData["text"],
                        ['rowspan' => $descData["rowSpan"], 'colspan' => count($header)])
                ];
                $results[] = [
                    new TableCell("",
                        ['colspan' => count($header)])
                ];
                $results[] = $descRow;
            }
            $results[] = [
                new TableCell("",
                    ['colspan' => count($header)])
            ];
            $results[] = new TableSeparator();
        }
        unset($results[count($results) - 1]);
        $table = new Table($output);
        $table
            ->setHeaders($header)
            ->setRows($results);
        $table->render();
    }
}
