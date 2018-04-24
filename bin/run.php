<?php

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use Mivanov\{
    LazyJira\Helper\ConsoleFormatter, LazyJira\ListIssuesCommand, LazyJira\MoveToDoneCommand, LazyJira\ViewIssueCommand
};
use Symfony\Component\Console\Application;

require_once(__DIR__ . "/../vendor/autoload.php");

$application = new Application();
$iss         = new IssueService(new ArrayConfiguration(
    array(
        'jiraHost'          => 'https://techweb.axway.com/jira',
        // for basic authorization:
        'jiraUser'          => 'mivanov',
        'jiraPassword'      => trim(file_get_contents(__DIR__ . "/../password")),
        // to enable session cookie authorization (with basic authorization only)
        'cookieAuthEnabled' => false,
    )
));

$consoleFormatter = new ConsoleFormatter();
$application->add(new ListIssuesCommand("ls", $consoleFormatter, $iss, ['ADE']));
$application->add(new ViewIssueCommand("view", $consoleFormatter, $iss));
$application->add(new MoveToDoneCommand("done", $iss));
$application->run();
