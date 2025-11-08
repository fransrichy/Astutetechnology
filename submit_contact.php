<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $company = trim($_POST['company']);
    $service = $_POST['service'];
    $budget = $_POST['budget'];
    $message = trim($_POST['message']);
    
    // Validation
    $errors = [];
    
    if (empty($first_name)) {
        $errors[] = "First name is required";
    }
    
    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($service)) {
        $errors[] = "Please select a service";
    }
    
    if (empty($message)) {
        $errors[] = "Please provide project details";
    }
    
    // If there are errors, return them
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }
    
    // Email configuration
    $to = "info@astutetechnologies.na"; // Replace with your email
    $subject = "New Contact Form Submission - Astute Technologies";
    
    // Email headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Email body
    $email_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #dc2626; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9fafb; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #374151; }
            .value { color: #6b7280; }
            .footer { background: #e5e7eb; padding: 15px; text-align: center; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>New Contact Form Submission</h1>
                <p>Astute Technologies Website</p>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Name:</div>
                    <div class='value'>$first_name $last_name</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'>$email</div>
                </div>
                <div class='field'>
                    <div class='label'>Phone:</div>
                    <div class='value'>$phone</div>
                </div>
                <div class='field'>
                    <div class='label'>Company:</div>
                    <div class='value'>" . ($company ? $company : 'Not provided') . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Service Interested In:</div>
                    <div class='value'>" . ucfirst(str_replace('-', ' ', $service)) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Budget Range:</div>
                    <div class='value'>" . ($budget ? ucfirst(str_replace('-', ' ', $budget)) : 'Not specified') . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Project Details:</div>
                    <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>This email was sent from the contact form on Astute Technologies website.</p>
                <p>Received on: " . date('Y-m-d H:i:s') . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Send email
    if (mail($to, $subject, $email_body, $headers)) {
        // Also send a confirmation email to the user
        $user_subject = "Thank you for contacting Astute Technologies";
        $user_headers = "From: info@astutetechnologies.na\r\n";
        $user_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $user_email_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc2626; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9fafb; }
                .footer { background: #e5e7eb; padding: 15px; text-align: center; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Thank You for Contacting Us</h1>
                </div>
                <div class='content'>
                    <p>Dear $first_name,</p>
                    <p>Thank you for reaching out to Astute Technologies. We have received your inquiry and one of our technology experts will contact you within 24 hours.</p>
                    <p><strong>Here's a summary of your inquiry:</strong></p>
                    <ul>
                        <li><strong>Service:</strong> " . ucfirst(str_replace('-', ' ', $service)) . "</li>
                        <li><strong>Submitted:</strong> " . date('F j, Y \a\t g:i A') . "</li>
                    </ul>
                    <p>If you need immediate assistance, please don't hesitate to call our 24/7 support line at <strong>+264 81 852 9722</strong>.</p>
                    <p>Best regards,<br>The Astute Technologies Team</p>
                </div>
                <div class='footer'>
                    <p>Astute Technologies - Your Trusted Technology Partner</p>
                    <p>Windhoek, Khomas Region, Namibia | +264 81 852 9722</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Send confirmation email to user
        mail($email, $user_subject, $user_email_body, $user_headers);
        
        // Save to database (optional - uncomment if you have database setup)
        // saveToDatabase($first_name, $last_name, $email, $phone, $company, $service, $budget, $message);
        
        echo json_encode([
            'success' => true,
            'message' => 'Thank you! Your message has been sent successfully. We will contact you within 24 hours.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Sorry, there was an error sending your message. Please try again or contact us directly at +264 81 852 9722.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}

// Optional: Function to save to database
function saveToDatabase($first_name, $last_name, $email, $phone, $company, $service, $budget, $message) {
    /*
    // Database configuration - update with your database details
    $servername = "localhost";
    $username = "your_username";
    $password = "your_password";
    $dbname = "astute_technologies";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "INSERT INTO contact_submissions (first_name, last_name, email, phone, company, service, budget, message, submitted_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$first_name, $last_name, $email, $phone, $company, $service, $budget, $message]);
        
    } catch(PDOException $e) {
        // Log error but don't show to user
        error_log("Database error: " . $e->getMessage());
    }
    */
}
?>