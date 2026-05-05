<?php
/**
 * HAH Healthcare Experts - Contact Form Handler
 * This script processes the contact form and sends emails
 */

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Configuration - Update these email addresses
$config = [
    'admin_email' => 'info@hahexperts.pk',  // Main recipient
    'from_email' => 'noreply@hahexperts.pk', // From address
    'from_name' => 'HAH Healthcare Experts Website',
    
    // Service-specific emails (optional routing)
    'service_emails' => [
        'regulatory' => 'compliance@hahexperts.pk',
        'biomedical' => 'engineering@hahexperts.pk',
        'legal' => 'legal@hahexperts.pk',
        'procurement' => 'info@hahexperts.pk',
        'management' => 'info@hahexperts.pk',
        'audit' => 'compliance@hahexperts.pk',
        'other' => 'info@hahexperts.pk'
    ]
];

// Get form data
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : 'Not provided';
$facility = isset($_POST['facility']) ? trim(strip_tags($_POST['facility'])) : 'Not provided';
$service = isset($_POST['service']) ? trim(strip_tags($_POST['service'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($service)) {
    $errors[] = 'Please select a service';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// Return errors if validation fails
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit();
}

// Get recipient based on service
$to_email = $config['admin_email'];
if (isset($config['service_emails'][$service])) {
    $to_email = $config['service_emails'][$service];
}

// Service labels for display
$service_labels = [
    'regulatory' => 'Regulatory Compliance',
    'biomedical' => 'Biomedical Engineering',
    'legal' => 'Legal Advisory',
    'procurement' => 'Procurement Services',
    'management' => 'Healthcare Management',
    'audit' => 'Audits & Inspections',
    'other' => 'Other'
];
$service_display = isset($service_labels[$service]) ? $service_labels[$service] : $service;

// Build email subject
$subject = "New Inquiry: {$service_display} - from {$name}";

// Build email body (HTML)
$email_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e3a5f; color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #1e3a5f; }
        .message-box { background: white; padding: 15px; border-left: 4px solid #2563eb; margin-top: 10px; }
        .footer { text-align: center; padding: 15px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
        </div>
        <div class='content'>
            <div class='field'>
                <span class='label'>Name:</span> {$name}
            </div>
            <div class='field'>
                <span class='label'>Email:</span> <a href='mailto:{$email}'>{$email}</a>
            </div>
            <div class='field'>
                <span class='label'>Phone:</span> {$phone}
            </div>
            <div class='field'>
                <span class='label'>Healthcare Facility:</span> {$facility}
            </div>
            <div class='field'>
                <span class='label'>Service Interested In:</span> {$service_display}
            </div>
            <div class='field'>
                <span class='label'>Message:</span>
                <div class='message-box'>{$message}</div>
            </div>
        </div>
        <div class='footer'>
            This email was sent from the HAH Healthcare Experts website contact form.
        </div>
    </div>
</body>
</html>
";

// Email headers
$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    "From: {$config['from_name']} <{$config['from_email']}>",
    "Reply-To: {$name} <{$email}>",
    'X-Mailer: PHP/' . phpversion()
];

// Send email
$mail_sent = mail($to_email, $subject, $email_body, implode("\r\n", $headers));

// Also send a copy to admin if different from service email
if ($to_email !== $config['admin_email']) {
    mail($config['admin_email'], "[CC] " . $subject, $email_body, implode("\r\n", $headers));
}

// Send confirmation email to user
$user_subject = "Thank you for contacting HAH Healthcare Experts";
$user_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e3a5f; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { text-align: center; padding: 15px; font-size: 12px; color: #666; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>HAH Healthcare Experts</h2>
        </div>
        <div class='content'>
            <p>Dear {$name},</p>
            <p>Thank you for contacting HAH Healthcare Experts. We have received your inquiry regarding <strong>{$service_display}</strong>.</p>
            <p>Our team will review your message and get back to you within 24-48 hours.</p>
            <p>If you have any urgent questions, please don't hesitate to reach out to us directly.</p>
            <br>
            <p>Best regards,</p>
            <p><strong>HAH Healthcare Experts Team</strong></p>
        </div>
        <div class='footer'>
            <p>Email: info@hahexperts.pk</p>
            <p>&copy; 2024-2026 HAH Healthcare Experts. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
";

$user_headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    "From: {$config['from_name']} <{$config['from_email']}>",
    'X-Mailer: PHP/' . phpversion()
];

mail($email, $user_subject, $user_body, implode("\r\n", $user_headers));

// Return response
if ($mail_sent) {
    echo json_encode([
        'success' => true, 
        'message' => 'Thank you for your message! We will contact you within 24-48 hours.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error sending your message. Please try again or email us directly at info@hahexperts.pk'
    ]);
}
?>
