<?php

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use Mivanov\{
    LazyJira\Helper\ConsoleFormatter, LazyJira\ListIssuesCommand, LazyJira\MoveToDoneCommand, LazyJira\ViewIssueCommand
};
use Symfony\Component\Console\Application;

require_once(__DIR__ . "/../vendor/autoload.php");

$consoleFormatter  = new ConsoleFormatter();
$jiraServerConfig  = new ArrayConfiguration(
    yaml_parse_file(__DIR__ . "/../jiraServerConfig.yaml")
);
$jiraProjectConfig = yaml_parse_file(__DIR__ . "/../jiraProjectConfig.yaml");
$jiraProjectNames  = array_keys($jiraProjectConfig);
$jiraClient        = new IssueService($jiraServerConfig);


$application = new Application();
$application->add(new ListIssuesCommand("ls", $consoleFormatter, $jiraClient, $jiraProjectNames));
$application->add(new ViewIssueCommand("view", $consoleFormatter, $jiraClient));
$application->add(new MoveToDoneCommand("done", $jiraClient));
$application->run();
