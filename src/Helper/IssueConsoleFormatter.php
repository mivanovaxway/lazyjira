<?php
/**
 * Created by PhpStorm.
 * User: mivanov
 * Date: 5/3/18
 * Time: 1:39 PM
 */

namespace Mivanov\LazyJira\Helper;


use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\Version;

class IssueConsoleFormatter
{
    /**
     * @var Issue
     */
    private $issue;

    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
    }

    public function getTypeName(): string
    {
        $issueType = mb_strtolower($this->issue->fields->getIssueType()->name);

        switch ($issueType) {
            case "internal bug":
            case "customer defect":
                $issueType = "<bg=red>" . $issueType . "</>";
                break;
            case "story":
                $issueType = "<bg=yellow>" . $issueType . "</>";
                break;
            case "task":
                $issueType = "<bg=blue>" . $issueType . "</>";
                break;
            case "sub-task":
                $issueType = "<bg=cyan>" . $issueType . "</>";
                break;
            case "preventive action":
                $issueType = "<bg=green>" . $issueType . "</>";
                break;
        }

        return $issueType;
    }

    public function getVersions(): string
    {
        if (!isset($this->issue->fields->versions)) {
            return "N/A";
        }
        $versions = array_map(
            function (Version $version) {
                return $version->name;
            },
            $this->issue->fields->versions
        );
        if (empty($versions)) {
            return "N/A";
        } else {
            return
                implode(
                    ", ", $versions

                );
        }
    }

    public function getKey(): string
    {
        $issueKey = $this->issue->key;

        return "<bg=black;fg=green;options=bold>{$issueKey}</>";
    }

    public function getSummary(): string
    {
        $summary = $this->issue->fields->summary;

        return "<bg=white;fg=black;options=bold>{$summary}</>";
    }

    public function getAssignee(): string
    {
        $assignee = $this->issue->fields->assignee->displayName;

        return "{$assignee}";
    }

    public function getReporter(): string
    {
        $reporter = $this->issue->fields->reporter->displayName;

        return "{$reporter}";
    }

    public function getStatus(): string
    {
        $issueStatus = mb_strtolower($this->issue->fields->status->name);
        switch ($issueStatus) {
            case "done":
            case "closed":
            case "fixed":
                $issueStatus = "<bg=green;options=bold>" . $issueStatus . "</>";
                break;
            case "resolved":
                $issueStatus = "<bg=green>" . $issueStatus . "</>";
                break;
            case "rejected":
                $issueStatus = "<bg=red>" . $issueStatus . "</>";
                break;
            case "new":
                $issueStatus = "<bg=blue>" . $issueStatus . "</>";
                break;
            case "open":
                $issueStatus = "<bg=blue;options=bold>" . $issueStatus . "</>";
                break;
            case "dev in progress":
                $issueStatus = "<bg=magenta>" . $issueStatus . "</>";
                break;
            case "review in progress":
                $issueStatus = "<bg=magenta;options=bold>" . $issueStatus . "</>";
                break;
            case "ready to test":
                $issueStatus = "<bg=yellow>" . $issueStatus . "</>";
                break;
            case "test in progress":
                $issueStatus = "<bg=yellow;options=bold>" . $issueStatus . "</>";
                break;
        }

        return $issueStatus;
    }

    public function hasDescription(): bool
    {
        return isset($this->issue->fields->description);
    }

}
