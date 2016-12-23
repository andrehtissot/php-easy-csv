<?php
class EasyCSV {
    public static function generate($rowsWithColumns, $delimiter = ';', $enclosure = '"', $encloseAll = false,
        $nullAsPrimitive = false) {
        $lines = '';
        foreach ($rowsWithColumns as $row)
          $lines .= self::generateRow($row, $delimiter, $enclosure, $encloseAll, $nullAsPrimitive);
        return substr($lines, 0, -1);
    }

    public static function generateRow($fields, $delimiter = ';', $enclosure = '"', $encloseAll = false,
        $nullAsPrimitive = false) {
        $delimiterEsc = preg_quote($delimiter, '/');
        $enclosureEsc = preg_quote($enclosure, '/');
        $output = array();
        foreach ($fields as $field) {
            if ($field === null && $nullAsPrimitive) {
                $output[] = 'NULL';
                continue;
            }
            $output[] = (($encloseAll || preg_match( "/(?:${delimiterEsc}|${enclosureEsc}|\s)/", $field))
              ? ($enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure) : $field);
        }
        return utf8_decode(implode($delimiter, $output)."\n");
    }

    public function stream($csv, $filename = ''){
        $filenameTag = empty($filename) ? '' : "filename={$filename}";
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; $filenameTag");
        header("Pragma: no-cache");
        header("Expires: 0");
        exit($csv);
    }

    public static function parseFile($filepath, $delimiter = ';', $enclosure = '"'){
        $linesCSV = array();
        $fileToOpen = fopen($filepath,'r');
        while(!feof($fileToOpen)){
            $csvLine = fgetcsv($fileToOpen, null, $delimiter, $enclosure);
            if($csvLine !== false)
                $linesCSV[] = $csvLine;
        }
        fclose($fileToOpen);
        for ($line=0; $line < count($linesCSV); $line++)
            for($column=0; $column < count($linesCSV[$line]); $column++)
                $linesCSV[$line][$column] = utf8_encode($linesCSV[$line][$column]);
        return $linesCSV;
    }
}
