<?php
$query = urlencode($_GET['q'] ?? '');
$html = '';

if ($query) {
    $url = "https://html.duckduckgo.com/html/?q=$query";
    $context = stream_context_create(['http' => ['header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36']]);
    $html = file_get_contents($url, false, $context);

    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $xpath = new DOMXPath($dom);
    $results = $xpath->query('//a[@class="result__a"]');

    echo "<ul>";
    foreach ($results as $a) {
        $href = $a->getAttribute('href');
        $title = $a->nodeValue;
        echo "<li><a href='$href'>$title</a></li>";
    }
    echo "</ul>";
}
?>
