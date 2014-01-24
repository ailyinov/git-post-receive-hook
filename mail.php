<!DOCTYPE html>
<html>
<head>
    <title>git push</title>
</head>
<body>
    <p><strong><?= basename($payload->ref); ?></strong></p>
    <?php foreach($payload->commits as $commit) { ?>
        <p>
            <?= nl2br(ticket_num_to_link($commit->message)); ?><br/>
            <?php if (!empty($commit->added)) { ?><span style="background-color: #42D692;"><?= join('<br />', $commit->added); ?></span><br/><?php } ?>
            <?php if (!empty($commit->removed)) { ?><span style="background-color: #FB4C2F;"><?= join('<br />', $commit->removed); ?></span><br/><?php } ?>
            <?php if (!empty($commit->modified)) { ?><span style="background-color: #B6CFF5;"><?= join('<br />', $commit->modified); ?></span><br/><?php } ?>
            <?= format_commit_url($commit->url); ?><br/>
            <span id="author" style="color: #898989; font-size: 11px;">
                <?= $commit->author->name; ?> &lt;<?= $commit->author->email; ?>&gt;
            </span>
        </p>
    <?php } ?>
</body>
</html>

<?php
function ticket_num_to_link($message) {
    return preg_replace('/#(\d+)/im',
        ' <a href="http://tasks.ws.pho.to/public/index.php?path_info=projects/sharephoto/tasks/$1">#$1</a> ',
        $message);
}

function format_commit_url($url) {
    return sprintf('<a href="%s">%s</a>', $url, basename($url));
}