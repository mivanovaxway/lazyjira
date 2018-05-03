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


}
