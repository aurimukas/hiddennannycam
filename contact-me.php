<?php
if($_POST) {

    $to_Email = "aurimas.navickas@gmail.com"; // Write your email here

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

    // Proceed with SendGrid email

    require 'vendor/autoload.php';
    $sendgrid = new SendGrid('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');


    // Body of the Email received in your Mailbox
    $emailcontent = 'Hey! You have received a new message from the visitor <strong>'.$_POST["userName"].'</strong><br/><br/>'. "\r\n" .
                'His message: <br/> <em>'.$_POST["userMessage"].'</em><br/><br/>'. "\r\n" .
                '<strong>Feel free to contact '.$_POST["userName"].' via email at : '.$_POST["userEmail"].'</strong>' . "\r\n" ;

    $message = new SendGrid\Email();
    $message->addTo($to_Email)->
              setFrom($_POST["userEmail"])->
              setFromName($_POST["userName"])->
              setSubject('Hidden-Nanny-Cam contact form: ' . $_POST["userSubject"]))->
              setText(strip_tags($emailcontent))->
              setHtml($emailcontent);
    $response = $sendgrid->send($message);

    if($response->getCode() != 200) {

        //If mail couldn't be sent output error. Check your PHP email configuration (if it ever happens)
        $output = json_encode(array('type'=>'error', 'text' => '<i class="icon ion-close-round"></i> Oops! Looks like something went wrong, please check your PHP mail configuration.'));
        die($output);

    } else {
        $output = json_encode(array('type'=>'message', 'text' => '<i class="icon ion-checkmark-round"></i> Hello '.$_POST["userName"] .', Your message has been sent, we will get back to you asap !'));
        die($output);
    }
}
?>
