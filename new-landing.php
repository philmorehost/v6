<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VTU Platform - Your One-Stop Solution for Digital Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('/landing-assets/images/slider-bg.png');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
        }
        .feature-icon {
            font-size: 3rem;
            color: #0d6efd;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="/assets-2/img/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                VTU Platform
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-lg-3" href="/web/Login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary ms-lg-2" href="/web/Register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero text-center">
        <div class="container">
            <h1 class="display-4">The Easiest Way to Top Up and Pay Bills</h1>
            <p class="lead">Join thousands of users who enjoy seamless and instant digital services on our platform.</p>
            <a href="/web/Register.php" class="btn btn-primary btn-lg">Get Started for Free</a>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Why Choose Us?</h2>
                <p class="lead">We provide the best services with reliability and speed.</p>
            </div>
            <div class="row">
                <div class="col-md-4 text-center">
                    <i class="bi bi-shield-check feature-icon mb-3"></i>
                    <h3>Secure Payments</h3>
                    <p>Your transactions are safe with our industry-standard security measures.</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-lightning-charge-fill feature-icon mb-3"></i>
                    <h3>Instant Delivery</h3>
                    <p>Get instant value for all your transactions, 24/7.</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-headset feature-icon mb-3"></i>
                    <h3>24/7 Support</h3>
                    <p>Our dedicated support team is always available to help you.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Get Started in 3 Simple Steps</h2>
            </div>
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="p-4">
                        <i class="bi bi-person-plus-fill feature-icon mb-3"></i>
                        <h4>1. Create an Account</h4>
                        <p>Sign up for a free account in just a few minutes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4">
                        <i class="bi bi-wallet2 feature-icon mb-3"></i>
                        <h4>2. Fund Your Wallet</h4>
                        <p>Easily fund your wallet using any of our secure payment methods.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4">
                        <i class="bi bi-cart-check-fill feature-icon mb-3"></i>
                        <h4>3. Start Transacting</h4>
                        <p>Enjoy seamless transactions for airtime, data, and more.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Contact Us</h2>
                <p class="lead">Have any questions? We'd love to hear from you.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p>&copy; 2025 VTU Platform. All Rights Reserved.</p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
