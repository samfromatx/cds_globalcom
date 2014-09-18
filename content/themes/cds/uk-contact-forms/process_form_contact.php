<?php
// Process only from my form (change if html has different name)
if (stristr($_SERVER['HTTP_REFERER'], '/about/contact/') === FALSE) die('No direct script access permitted');
// Mail header removal
function remove_headers($string) { $headers = array( "/to\:/i", "/from\:/i", "/bcc\:/i", "/cc\:/i", "/Content\-Transfer\-Encoding\:/i", "/Content\-Type\:/i", "/Mime\-Version\:/i" ); $string = preg_replace($headers, '', $string); return strip_tags($string);
}
// Pick up the cleaned form data

$country = remove_headers($_POST['country']);
$bizname = remove_headers($_POST['bizname']);
$biztype = remove_headers($_POST['biztype']);
$firstname = remove_headers($_POST['firstName']);
$lastname = remove_headers($_POST['lastName']);
$email = remove_headers($_POST['emailAddress']);
$phone = remove_headers($_POST['phone']);
$comments = remove_headers($_POST['comments']);
$emailinquiry = remove_headers($_POST['emailinquiry']);
if ($emailinquiry == "Career Inquiries") {
    $toemail = "humanresources@cdsglobal.co.uk";
    $subject = "Message from cdsglobal.co.uk contact us web form";
    $message = "Name: $firstname $lastname
E-mail: $email
Inquiry: $emailinquiry
Comments: $comments";
} elseif ($emailinquiry == "Customer Service") {
    $toemail = "subs@subscription.cdsglobal.co.uk";
    $subject = "Message from cdsglobal.co.uk contact us web form";
    $message = "Name: $firstname $lastname
E-mail: $email
Inquiry: $emailinquiry
Comments: $comments";
} else {
    if ($country == "au") {
        $toemail = "sales@cdsglobal.com.au";
        $subject = "Message from Australia business request web form";
    } else {
        $toemail = "reception1@cdsglobal.co.uk,reception2@cdsglobal.co.uk";
        $subject = "Message from UK business request web form";
    }
    $message = "Name: $firstname $lastname
Business Region: $country
E-mail: $email
Phone: $phone
Business Name: $bizname
Business Type: $biztype
Inquiry: $emailinquiry
Comments: $comments";
}
// Build the email (replace the address in the $to section with your own)
$to = "$toemail";
//$to = "swhite@cds-global.com";
$headers = "From: $email";

// Send the mail using PHPs mail() function
mail($to, $subject, $message, $headers);

// Redirect
//header("Location: /about/contact/");
?>