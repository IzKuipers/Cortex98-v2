<?php

require_once(__DIR__ . "/../../config.php");

function LegalNotice()
{
    return <<<HTML
    <p style="color: gray;">
        The administrators of this web site reserve the right to remove inappropriate or illegal content. <a
            href="<?= WEB_ROOT ?>/legal.php">Legal</a>
    </p>
    HTML;
}
