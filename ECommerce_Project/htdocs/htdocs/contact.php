<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $query = "INSERT INTO contact_messages (first_name, last_name, email, subject, message) VALUES ('$firstname', '$lastname', '$email', '$type', '$message')";
    
    mysqli_query($conn, $query);
    mysqli_close($conn);
}
?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
    }

    body {
        background-color: #f4f7f6;
        font-family: 'Segoe UI', sans-serif;
    }

    .contact-header {
        background: var(--primary-gradient);
        color: white;
        padding: 60px 0;
        text-align: center;
        margin-bottom: -50px; /* Pulls the card up */
    }

    .contact-wrapper {
        background: var(--glass-bg);
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 50px;
    }

    .contact-info-panel {
        background: var(--primary-gradient);
        color: white;
        padding: 40px;
        height: 100%;
    }

    .contact-form-panel {
        padding: 40px;
    }

    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
    }

    .info-item i {
        font-size: 20px;
        margin-right: 15px;
        background: rgba(255,255,255,0.2);
        padding: 10px;
        border-radius: 50%;
    }

    .form-control {
        border-radius: 8px;
        padding: 12px;
        border: 1px solid #dee2e6;
    }

    .btn-send {
        background: #764ba2;
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-send:hover {
        background: #5a3a7d;
        transform: translateY(-2px);
        color: white;
    }

    .follow > p >a{
        color: white;
        text-decoration: none;
    }
</style>

<section class="contact-header">
    <div class="container">
        <h1 class="fw-bold">Get In Touch</h1>
        <p>Have a question about a product? We're here to help!</p>
    </div>
</section>

<main class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="contact-wrapper">
                <div class="row g-0">
                    <div class="col-md-5 contact-info-panel">
                        <h3>Contact Information</h3>
                        <p class="mb-5">Fill out the form and our team will get back to you within 24 hours.</p>

                        <div class="info-item">
                            <span>📍 Jaipur, Rajasthan, India - 302012</span>
                        </div>
                        <div class="info-item">
                            <span>📞 +91 12345 67890</span>
                        </div>
                        <div class="info-item">
                            <span>✉️ support@hkstore.com</span>
                        </div>

                        <div class="mt-5 follow">
                            <h5>Follow Us</h5>
                            <p><a href="https://x.com/Hk__Hacker">Twitter</a> | <a href="https://www.linkedin.com/in/harsh-khandal-4941522b1/">LinkedIn</a> | <a href="https://github.com/Hk-Hacker-Harsh">Github</a></p>
                        </div>
                    </div>

                    <div class="col-md-7 contact-form-panel">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" placeholder="John" name="firstname" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" placeholder="Doe" name="lastname" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" placeholder="name@example.com" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <select class="form-select form-control" name="type">
                                    <option>General Inquiry</option>
                                    <option>Product Support</option>
                                    <option>Order Status</option>
                                    <option>Partnership</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" rows="4" placeholder="How can we help you?" name="message" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-send w-100">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
require_once __DIR__ . '/includes/footer.php';
?>