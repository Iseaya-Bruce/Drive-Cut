<?php


$to = "iseayabrucedolieveira@gmail.com";
$subject = "Test Email";
$message = "This is a test email from PHP.";
$headers = "From: no-reply@example.com";

if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}
?>
