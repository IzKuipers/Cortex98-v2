<?php

require_once(__DIR__ . "/components/headerbar.php");
require_once(__DIR__ . "/components/navigation.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/main.css">
    <title>Legal - Cortex 98</title>
</head>

<body>
    <center>
        <?= Headerbar() ?>
        <?= NavigationBar() ?>
        <br>
        <table bgcolor="#ffdddd" cellpadding="2" cellspacing="2" width="700">
            <tr>
                <td bgcolor="#ffbbbb">
                    <b>Legal Notices</b>
                </td>
            </tr>
            <tr>
                <td>
                    <p>This site is in no way affiliated with any existing company, corporation, foundation or
                        organization. It is provided free of charge by retro enthusiasts and open-source developers. We
                        cannot verify the content placed on this web site originated from legitimate sources, and was
                        not obtained illegally. Download the content at your own risk. All content and assets on this
                        web site belong to their respective authors.</p>
                    <p>The administrators of this web site reserve the right to remove any illegal or inappropriate
                        content. If you have questions or concerns, please send an email to
                        <b>izaak.kuipers@gmail.com</b>. You may also use this email address to apply for administrative
                        privileges in the form of moderation.
                    </p>
                    <p>The copyright notices found on this web site are put there for aesthetical purposes and do not
                        hold any legal value whatsoever. Furthermore, this web site did not exist back in 1999, its
                        development began in 2025.</p>

                    <p>
                        This project is open-source and available on <a
                            href="https://github.com/IzKuipers/Cortex98-v2">GitHub</a>.
                    </p>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>