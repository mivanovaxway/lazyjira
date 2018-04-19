<?php

namespace Mivanov\LazyJira\Helper;

class ConsoleFormatter
{
    public function replaceJiraMarkup(string $string): string
    {
        $string = str_replace("{quote}", "", $string);
        $string = str_replace("_+*", "", $string);
        $string = str_replace("*+_", "", $string);

        return $string;
    }

    public function intervalEveryN(string $string, int $rowLength, int $maxRows): array
    {
        $text          = [];
        $string        = str_replace(["\n", "\r", "   ", "  "], " ", $string);
        $words         = explode(" ", $string);
        $currWordIndex = 0;
        $currRowIndex  = 0;

        while ($currRowIndex < $maxRows) {
            $currRow = "";
            if ($currWordIndex === count($words)) {
                break;
            }
            while ($currWordIndex < count($words)) {
                $currWord = $words[$currWordIndex];
                if (mb_strlen($currRow) + mb_strlen($currWord) < $rowLength) {
                    $currRow .= " " . $currWord;
                    $currWordIndex++;
                } else {
                    //no need for $currWordIndex++ as we still want this word on the next row
                    break;
                }
            }
            $text[] = $currRow;
            $currRowIndex++;
        }
        if ($currWordIndex < count($words)) {
            $text[$currRowIndex - 1] .= "...";
        }

        return ["rowSpan" => $currRowIndex, "text" => implode("\n", $text)];
    }

    public function issueTypeColored(string $issueType): string
    {
        $issueType = mb_strtolower($issueType);
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

    public function issueStatusColored(string $issueStatus): string
    {
        $issueStatus = mb_strtolower($issueStatus);
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
}
