<?php
/**
 * Created by PhpStorm.
 * User: mivanov
 * Date: 4/13/18
 * Time: 1:59 PM
 */

namespace Mivanov\LazyJira;


use JiraRestApi\Issue\IssueService;
use Mivanov\LazyJira\Helper\ConsoleFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ViewIssueCommand extends Command
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

    public function __construct(string $name = null, ConsoleFormatter $consoleFormatter, IssueService $jiraClient)
    {
        parent::__construct($name);

        $this->jiraClient       = $jiraClient;
        $this->consoleFormatter = $consoleFormatter;
    }

    protected function configure()
    {
        $this->setDescription("Shows issue information");
        $this->addArgument("issueId", InputArgument::REQUIRED);
    }


    protected
    function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $io      = new SymfonyStyle($input, $output);
        $issueId = $input->getArgument("issueId");
        $issue   = $this->jiraClient->get($issueId);
        $output->writeln("Issue: " . $this->consoleFormatter->issueKeyColored($issue->key));
        $output->writeln("Name: {$issue->fields->summary}");
        $output->writeln("Reporter: {$issue->fields->reporter->name}");
        if (isset($issue->fields->assignee)) {
            $output->writeln("Assignee: " . $issue->fields->assignee->name);
        }
        if (isset($issue->fields->description)) {
            $output->writeln("Description: \n\n" . $this->consoleFormatter->replaceJiraMarkup($issue->fields->description));
        }
        if (isset($issue->fields->comment)) {
            $output->writeln("<fg=green>Comments:</>");
            foreach ($issue->fields->comment->comments as $comment) {
                $output->writeln("{$comment->author->name} wrote: \n\n" . $this->consoleFormatter->replaceJiraMarkup($comment->body));
                $io->newLine();
            }
        }
    }
}
