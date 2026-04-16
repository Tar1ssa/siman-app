<?php
$output = file_get_contents('storage/pest_output.txt');
$lines = explode("\n", $output);
$matches = [];

foreach($lines as $line) {
    $line = trim($line);
    if (preg_match('/^(✓|⨯)\s+(.+)/u', $line, $match)) {
        $matches[] = $match;
        echo 'MATCH: ' . $line . PHP_EOL;
    }
}

echo 'Total matches: ' . count($matches) . PHP_EOL;
echo 'Passed count: ' . substr_count($output, '✓') . PHP_EOL;
echo 'Failed count: ' . substr_count($output, '⨯') . PHP_EOL;
?>