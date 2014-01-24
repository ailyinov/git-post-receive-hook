<?php
require_once 'Shell.php';
require_once 'Deploy.php';
require_once 'Deploy/Api.php';
require_once 'Deploy/WebDev.php';
require_once 'Deploy/WebProd.php';

use Deploy\Api as DeployApi;
use Deploy\WebDev as DeployWebDev;
use Deploy\WebProd as DeployWebProd;

if (!array_key_exists('payload', $_POST)) {
    write_log(['no payload in POST']);
    return 1;
}

$payload = json_decode($_REQUEST['payload']);

if (empty($payload->commits)) {
    write_log(['no commits pushed']);
    return 2;
}

$deploy = null;
switch ($payload->repository->name) {
    case DeployWebDev::REPO:
    case DeployWebProd::REPO:
        $deploy = basename($payload->ref) == 'master' ? new DeployWebProd($payload) : new DeployWebDev($payload);
        break;

    case DeployApi::REPO:
        $deploy = new DeployApi($payload);
        break;

    default:
        write_log([$payload]);
}
if ($deploy) {
    $shell = new Shell(Deploy::BASEDIR);
    $out = $deploy->run($shell);
    write_log([$payload, $shell->getCommands(), $out]);
}

send_mail_html($payload, 'a.ilynov@pho.to, zverev@vicman.net');
return 0;


function send_mail($payload, $receivers, $subject = '', $user_message = '') {
    $headers[] = "From: {$payload->pusher->name} <{$payload->pusher->email}>";
    $headers[] = "Reply-To: {$payload->pusher->name} <{$payload->pusher->email}>";
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    $subject = $subject ?: "git push {$payload->repository->name}";

    $message[] = "branch: $payload->ref";
    $message[] = $user_message;
    foreach ($payload->commits as $commit) {
        $message[] = $commit->message;
        $message[] = $commit->url;
        $message[] = "\n";
    }

    mail(trim($receivers), $subject, wordwrap(join($message, "\n"), 70, "\n", true), join("\r\n", $headers));
}

function send_mail_html($payload, $receivers, $subject = '') {
    $headers[] = "From: {$payload->pusher->name} <{$payload->pusher->email}>";
    $headers[] = "Reply-To: {$payload->pusher->name} <{$payload->pusher->email}>";
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html';

    $subject = $subject ?: "git push {$payload->repository->name}";

    ob_start();
    require_once 'mail.php';
    $message = ob_get_contents();

    mail(trim($receivers), $subject, $message, join("\r\n", $headers));
}

function write_log($messages) {
    $log_entry[] = sprintf('%s [%s] %s', str_repeat('=', 20), date('Y-m-d H:i:s'), str_repeat('=', 20));
    foreach ($messages as $msg) {
        $log_entry[] = print_r($msg, true);
        $log_entry[] = str_repeat('-', 40);
    }
    $log_entry[] = "\n";

    file_put_contents('deploy.log', join("\n", $log_entry), FILE_APPEND);
}

