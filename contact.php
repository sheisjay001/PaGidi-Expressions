<?php
header('Content-Type: application/json');

// Configuration
$to_email = "pagidiexpressions@gmail.com"; // Replace with your actual email address
$subject_prefix = "[PaGidi Website Contact] ";

// Response array
$response = [
    'success' => false,
    'message' => ''
];

// Check request method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input
    $name = filter_var(trim($_POST["name"] ?? ''), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = filter_var(trim($_POST["message"] ?? ''), FILTER_SANITIZE_STRING);

    // Validate input
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'Please fill in all fields.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please provide a valid email address.';
        echo json_encode($response);
        exit;
    }

    // Email Content
    $subject = $subject_prefix . "New message from $name";
    
    $email_content = "Name: $name\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Message:\n$message\n";

    // Email Headers
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send Email
    if (mail($to_email, $subject, $email_content, $headers)) {
        $response['success'] = true;
        $response['message'] = 'Thank you! Your message has been sent successfully.';
    } else {
        $response['message'] = 'Oops! Something went wrong and we couldn\'t send your message. Please try again later.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
