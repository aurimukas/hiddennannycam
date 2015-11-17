<?php
if($_POST) {

    // Use PHP To Detect An Ajax Request
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

        // Exit script for the JSON data
        $output = json_encode(
        array(
            'type'=> 'error',
            'text' => 'Request must come from Ajax'
        ));

        die($output);
    }

    // Checking if the $_POST vars well provided, Exit if there is one missing
    if(!isset($_POST["userName"]) || !isset($_POST["userEmail"]) || !isset($_POST["userSubject"]) || !isset($_POST["userMessage"])) {

        $output = json_encode(array('type'=>'error', 'text' => '<i class="icon ion-close-round"></i> Input fields are empty!'));
        die($output);
    }

    // PHP validation for the fields required
    if(empty($_POST["userName"])) {
        $output = json_encode(array('type'=>'error', 'text' => '<i class="icon ion-close-round"></i> We are sorry but your name is too short or not specified.'));
        die($output);
    }

    if(!filter_var($_POST["userEmail"], FILTER_VALIDATE_EMAIL)) {
        $output = json_encode(array('type'=>'error', 'text' => '<i class="icon ion-close-round"></i> Please enter a valid email address.'));
        die($output);
    }

    // To avoid the spammy bots, you can change the value of the minimum characters required. Here it's <20
    if(strlen($_POST["userMessage"])<20) {
        $output = json_encode(array('type'=>'error', 'text' => '<i class="icon ion-close-round"></i> Too short message! Take your time and write a few words.'));
        die($output);
    }

    // Proceed with PHP email
    /*$headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type:text/html;charset=UTF-8' . "\r\n";
    $headers .= 'From: Hidden Nanny Cam <info@hidden-nanny-com.com>' . "\r\n";
    $headers .= 'Reply-To: '.$_POST["userEmail"]."\r\n";

    $headers .= 'X-Mailer: PHP/' . phpversion();*/

    // Body of the Email received in your Mailbox
    $emailcontent = 'Hey! You have received a new message from the visitor <strong>'.$_POST["userName"].'</strong><br/><br/>'. "\r\n" .
                'His message: <br/> <em>'.$_POST["userMessage"].'</em><br/><br/>'. "\r\n" .
                '<strong>Feel free to contact '.$_POST["userName"].' via email at : '.$_POST["userEmail"].'</strong>' . "\r\n" ;

    //require __DIR__ . '/vendor/autoload.php';

    require_once __DIR__ . '/vendor/swiftmailer/swiftmailer/lib/swift_required.php';

    $message = Swift_Message::newInstance("[Contact Us Form] " . $_POST["userSubject"]);
    $message->setBody($emailcontent, 'text/html', 'UTF-8');
    $message->setTo(array(
      'aurimas@hidden-nanny-cam.com' => 'Aurimas',
      'lee@hidden-nanny-cam.com' => 'Lee',
    ));
    $message->setFrom(array('info@hidden-nanny-cam.com' => $_POST["userName"]))
        ->setReturnPath('aurimas@hidden-nanny-cam.com');

    $transport = Swift_SendmailTransport::newInstance();
    $mailer = Swift_Mailer::newInstance($transport);

    $numSent = $mailer->send($message);

    //$Mailsending = @mail($to_Email, "[Contact Us Form] " . $_POST["userSubject"], $emailcontent, $headers);

    //if(!$Mailsending) {
    if(!$numSent) {

        //If mail couldn't be sent output error. Check your PHP email configuration (if it ever happens)
        $output = json_encode(array('type'=>'error', 'text' => '<i class="icon ion-close-round"></i> Oops! Looks like something went wrong, please check your PHP mail configuration.'));
        die($output);

    } else {
        $output = json_encode(array('type'=>'message', 'text' => '<i class="icon ion-checkmark-round"></i> Hello '.$_POST["userName"] .', Your message has been sent, we will get back to you asap !'));
        die($output);
    }
}
?>
