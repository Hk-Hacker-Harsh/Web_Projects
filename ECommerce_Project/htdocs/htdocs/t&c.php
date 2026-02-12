<?php
require_once __DIR__ . '/includes/header.php';
?>

<style>
    :root {
        --primary-color: #764ba2;
        --text-dark: #2d3436;
        --text-muted: #636e72;
    }

    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        color: var(--text-dark);
        line-height: 1.6;
    }

    .terms-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
        text-align: center;
        }
    
    .terms-header p{
        color:white;
    }

    .terms-container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 40px;
        margin-bottom: 80px;
    }

    h2 {
        color: var(--primary-color);
        font-weight: 700;
        margin-top: 30px;
        border-left: 5px solid var(--primary-color);
        padding-left: 15px;
        font-size: 1.5rem;
    }

    p, li {
        color: var(--text-muted);
        font-size: 1.05rem;
        margin-bottom: 15px;
    }

    .last-updated {
        font-style: italic;
        color: #a0a0a0;
        margin-bottom: 30px;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: white;
        text-decoration: none;
        opacity: 0.8;
    }

    .back-link:hover {
        opacity: 1;
        color: white;
    }
</style>

<header class="terms-header">
    <div class="container">
        <a href="signup.php" class="back-link">← Back to Signup</a>
        <h1 class="display-4 fw-bold">Terms & Conditions</h1>
        <p class="lead">Please read these terms carefully before using Hk Store.</p>
    </div>
</header>

<main class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9 terms-container">
            <p class="last-updated">Last Updated: January 31, 2026</p>

            <section>
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing and using Hk Store, you agree to be bound by these Terms and Conditions and all applicable laws and regulations. If you do not agree with any of these terms, you are prohibited from using or accessing this site.</p>
            </section>

            <section>
                <h2>2. User Accounts</h2>
                <p>When you create an account with us, you must provide information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>
                <ul>
                    <li>You are responsible for safeguarding your password.</li>
                    <li>You agree not to disclose your password to any third party.</li>
                    <li>You must notify us immediately upon becoming aware of any breach of security.</li>
                </ul>
            </section>

            <section>
                <h2>3. Digital Products & Refunds</h2>
                <p>Since Hk Store provides digital cybersecurity products and services, all sales are final. Once a product key or digital download has been accessed, we generally do not offer refunds unless the product is proven to be defective.</p>
            </section>

            <section>
                <h2>4. Prohibited Uses</h2>
                <p>You may use our service only for lawful purposes. You agree not to use the Service:</p>
                <ul>
                    <li>In any way that violates any applicable national or international law.</li>
                    <li>To transmit, or procure the sending of, any advertising or promotional material.</li>
                    <li>To impersonate or attempt to impersonate Hk Store, an employee, or another user.</li>
                </ul>
            </section>

            <section>
                <h2>5. Limitation of Liability</h2>
                <p>In no event shall Hk Store, nor its directors, employees, or partners, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, or other intangible losses.</p>
            </section>

            <section class="mt-5 text-center">
                <p>If you have any questions about these Terms, please contact us at <strong>support@hkstore.com</strong></p>
                <a href="signup.php" class="btn btn-primary px-5 mt-3" style="background: #764ba2; border: none;">I Understand</a>
            </section>
        </div>
    </div>
</main>

<?php 
require_once __DIR__ . '/includes/footer.php';
?>