<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

function loadEnv($path) {
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

function logMessage($message) {
    $file = __DIR__ . '/mail.log';
    $time = date("Y-m-d H:i:s");
    file_put_contents($file, "[$time] $message\n", FILE_APPEND);
}

loadEnv(__DIR__ . '/.env');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    logMessage("Form submitted");

    $hcaptcha = $_POST['h-captcha-response'] ?? '';

    if (!$hcaptcha) {
        logMessage("Captcha Incomplete");
        die("Captcha not completed.");
    }

    $secretKey = $_ENV['HCAPTCHA_SECRET'];

    $data = [
        'secret' => $secretKey,
        'response' => $hcaptcha,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($verify);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        logMessage("Captcha failed");
        die("Captcha verification failed.");
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');


    if (!$name || !$email || !$message) {
        logMessage("fields missing");
        die("Required fields missing.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logMessage("Invaid email format");
        die("Invalid email format.");
    }


    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];
        $mail->Port       = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['FROM_EMAIL'], $_ENV['FROM_NAME']);

        $mail->addAddress('ar.miteshbhojani@studio9architects.in');
        $mail->addAddress('studio9architectsoffice@gmail.com');

        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission';

        $mail->Body = "
            <h3>New Contact Message</h3>
            <p><b>Name:</b> $name</p>
            <p><b>Email:</b> $email</p>
            <p><b>Phone:</b> $phone</p>
            <p><b>Message:</b> $message</p>
        ";

        logMessage("Preparing admin email");
        $mail->send();
        logMessage("Admin mail sent");

    } catch (Exception $e) {
        logMessage("Admin email failed: " . $mail->ErrorInfo);
        die("Mailer Error (Admin): " . $mail->ErrorInfo);
    }

    try {
        $mail2 = new PHPMailer(true);

        $mail2->isSMTP();
        $mail2->Host       = $_ENV['SMTP_HOST'];
        $mail2->SMTPAuth   = true;
        $mail2->Username   = $_ENV['SMTP_USERNAME'];
        $mail2->Password   = $_ENV['SMTP_PASSWORD'];
        $mail2->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];
        $mail2->Port       = $_ENV['SMTP_PORT'];

        $mail2->setFrom($_ENV['FROM_EMAIL'], $_ENV['FROM_NAME']);
        $mail2->addAddress($email, $name);

        $mail2->isHTML(true);
        $mail2->Subject = 'We received your message';

        $mail2->Body = "
            <h3>Thank you, $name!</h3>
            <p>We have received your message and will get back to you soon.</p>
            <br>
            <p><b>Your Message:</b></p>
            <p>$message</p>
        ";

        logMessage("Preparing USER email");
        $mail2->send();
        logMessage("User email sent to: $email");

    } catch (Exception $e) {
        logMessage("USER email failed: " . $mail2->ErrorInfo);
        die("Mailer Error (User): " . $mail2->ErrorInfo);
    }

    echo "success";
}
?>


<!DOCTYPE html>
<!-- saved from url=(0061)https://demo.awaikenthemes.com/html-preview/inspaire/404.html -->
<html lang="zxx">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta -->

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Awaiken">
    <!-- Page Title -->
    <title>Studio 9 Architecture</title>
    <!-- Favicon Icon -->
    <!--<link rel="shortcut icon" type="image/x-icon" href="https://demo.awaikenthemes.com/html-preview/inspaire/images/favicon.png">-->
    <!-- Google Fonts Css-->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link href="./assets/css/css2" rel="stylesheet">
    <!-- Bootstrap Css -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!-- SlickNav Css -->
    <link href="./assets/css/slicknav.min.css" rel="stylesheet">
    <!-- Swiper Css -->
    <link rel="stylesheet" href="./assets/css/swiper-bundle.min.css">
    <!-- Font Awesome Icon Css-->
    <link href="./assets/css/all.min.css" rel="stylesheet" media="screen">
    <!-- Animated Css -->
    <link href="./assets/css/animate.css" rel="stylesheet">
    <!-- Magnific Popup Core Css File -->
    <link rel="stylesheet" href="./assets/css/magnific-popup.css">
    <!-- Mouse Cursor Css File -->
    <link rel="stylesheet" href="./assets/css/mousecursor.css">
    <!-- Main Custom Css -->
    <link href="./assets/css/custom.css" rel="stylesheet" media="screen">
    <!-- Fa Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./assets/css/t_styles.css">
    <link rel="stylesheet" href="./assets/css/m_styles.css">
</head>

<body>

    <!-- Preloader Start -->
    <div class="preloader" style="display: none;">
        <div class="loading-container">
            <div class="loading"></div>
            <div id="loading-icon"><img src="./assets/img/loader.svg" alt=""></div>
        </div>
    </div>
    <!-- Preloader End -->

    <!-- Header Start -->
    <header class="main-header">
        <div class="header-sticky">
            <nav class="container">
                <div class="navbar navbar-expand-lg">
                    <!-- Logo Start -->
                    <a class="navbar-brand" href="index.html">
                        <img src="./assets/img/newAssets/S9 LOGO.png" alt="Logo" class="logo_size">

                    </a>
                    <!-- Logo End -->

                    <!-- Main Menu Start -->
                    <div class="collapse navbar-collapse main-menu">
                        <div class="nav-menu-wrapper">
                            <ul class="navbar-nav mr-auto" id="menu">
                                <li class="nav-item"><a class="nav-link" href="index.html">Home</a>
                                    <!-- <ul>
                                        <li class="nav-item"><a class="nav-link" href="index.html">Home - Image</a></li>
                                        <li class="nav-item"><a class="nav-link" href="HomePageVideo.html">Home - Video</a></li>
                                        <li class="nav-item"><a class="nav-link" href="HomePageOneImage.html">Home - Slider</a></li>
                                    </ul> -->
                                </li>
                                <li class="nav-item"><a class="nav-link" href="aboutus.html">About Us</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="Services.html">Services</a></li>
                                <li class="nav-item"><a class="nav-link" href="Projects.html">Projects</a></li>
                                <li class="nav-item"><a class="nav-link" href="Products.html">Products</a></li>
                                <!-- <li class="nav-item"><a class="nav-link" href="Blog.html">Blog</a></li>
                                <li class="nav-item submenu"><a class="nav-link" href="index.html#">Pages</a>
                                    <ul>                                        
                                        <li class="nav-item"><a class="nav-link" href="servicedetails.html">Service Details</a></li>
                                        <li class="nav-item"><a class="nav-link" href="projectdetails.html">Project Details</a></li>
                                        <li class="nav-item"><a class="nav-link" href="Blogdetails.html">Blog Details</a></li>
                                        <li class="nav-item"><a class="nav-link" href="ImageGallery.html">Image Gallery</a></li>
                                        <li class="nav-item"><a class="nav-link" href="Faqs.html">FAQs</a></li>
                                        <li class="nav-item"><a class="nav-link" href="404.html">404</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="ContactUs.php">Contact Us</a></li>                              -->
                            </ul>
                        </div>
                        <!-- Header Btn Start -->
                        <div class="header-btn d-inline-flex">
                            <a href="ContactUs.php" class="btn-default">get in touch</a>
                        </div>
                        <!-- Header Btn End -->
                    </div>
                    <!-- Main Menu End -->
                    <div class="navbar-toggle"><a href="index.html#" aria-haspopup="true" role="button" tabindex="0"
                            class="slicknav_btn slicknav_collapsed" style="outline: none;"><span
                                class="slicknav_menutxt"></span><span class="slicknav_icon slicknav_no-text"><span
                                    class="slicknav_icon-bar"></span><span class="slicknav_icon-bar"></span><span
                                    class="slicknav_icon-bar"></span></span></a></div>
                </div>
            </nav>
            <div class="responsive-menu">
                <div class="slicknav_menu">
                    <ul class="slicknav_nav slicknav_hidden" aria-hidden="true" role="menu" style="display: none;">
                        <li class="nav-item submenu slicknav_collapsed slicknav_parent"><a href="index.html#"
                                role="menuitem" aria-haspopup="true" tabindex="-1" class="slicknav_item slicknav_row"
                                style="outline: none;"><a class="nav-link" href="index.html" tabindex="-1">Home</a>
                                <span class="slicknav_arrow">►</span></a>
                            <ul role="menu" class="slicknav_hidden" aria-hidden="true" style="display: none;">
                                <li class="nav-item"><a class="nav-link" href="index.html" role="menuitem"
                                        tabindex="-1">Home - Image</a></li>
                                <li class="nav-item"><a class="nav-link" href="HomePageVideo.html" role="menuitem"
                                        tabindex="-1">Home - Video</a></li>
                                <li class="nav-item"><a class="nav-link" href="HomePageOneImage.html" role="menuitem"
                                        tabindex="-1">Home - Slider</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="aboutus.html" role="menuitem" tabindex="-1">About
                                Us</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="Services.html" role="menuitem"
                                tabindex="-1">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="Projects.html" role="menuitem"
                                tabindex="-1">Projects</a></li>
                        <li class="nav-item"><a class="nav-link" href="Blog.html" role="menuitem" tabindex="-1">Blog</a>
                        </li>
                        <li class="nav-item submenu slicknav_collapsed slicknav_parent"><a href="index.html#"
                                role="menuitem" aria-haspopup="true" tabindex="-1" class="slicknav_item slicknav_row"
                                style="outline: none;"><a class="nav-link" href="index.html#" tabindex="-1">Pages</a>
                                <span class="slicknav_arrow">►</span></a>
                            <ul role="menu" class="slicknav_hidden" aria-hidden="true" style="display: none;">
                                <li class="nav-item"><a class="nav-link" href="servicedetails.html" role="menuitem"
                                        tabindex="-1">Service Details</a></li>
                                <li class="nav-item"><a class="nav-link" href="projectdetails.html" role="menuitem"
                                        tabindex="-1">Project Details</a></li>
                                <li class="nav-item"><a class="nav-link" href="Blogdetails.html" role="menuitem"
                                        tabindex="-1">Blog Details</a></li>
                                <li class="nav-item"><a class="nav-link" href="ImageGallery.html" role="menuitem"
                                        tabindex="-1">Image Gallery</a></li>
                                <li class="nav-item"><a class="nav-link" href="Faqs.html" role="menuitem"
                                        tabindex="-1">FAQs</a></li>
                                <li class="nav-item"><a class="nav-link" href="404.html" role="menuitem"
                                        tabindex="-1">404</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="ContactUs.php" role="menuitem"
                                tabindex="-1">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Page Header Start -->
    <div class="page-header parallaxie"
        style="background-image: url('./assets/img/newAssets/PROJECTS/OBEROI SKY CITY/1b.png'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed; background-position: center -97.3793px;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-2" data-cursor="-opaque">
                            <div style="position:relative;display:inline-block;">
                                <div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    C</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    o</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    n</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    t</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    a</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    c</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    t</div>
                            </div>
                            <div style="position:relative;display:inline-block;">
                                <div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    u</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    s</div>
                            </div>
                        </h1>
                        <nav class="wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">contact us</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Page Contact Us Start -->
    <div class="page-contact-us">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <!-- Contact Us Image Start -->
                    <div class="contact-us-image">
                        <figure class="image-anime reveal"
                            style="transform: translate(0px, 0px); opacity: 1; visibility: inherit;">
                            <img src="./assets/img/newAssets/PROJECTS/VILE PARLE/17.png" alt=""
                                style="transform: translate(0px, 0px);">
                        </figure>
                    </div>
                    <!-- Contact Us Image End -->
                </div>

                <div class="col-lg-6">
                    <!-- Contact Us Form Start -->
                    <div class="contact-us-form">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">contact form
                            </h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">
                                <div style="position:relative;display:inline-block;">
                                    <div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        W</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        e</div>
                                </div>
                                <div style="position:relative;display:inline-block;">
                                    <div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        w</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        o</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        u</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        l</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        d</div>
                                </div>
                                <div style="position:relative;display:inline-block;">
                                    <div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        l</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        o</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        v</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        e</div>
                                </div>
                                <div style="position:relative;display:inline-block;">
                                    <div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        t</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        o</div>
                                </div>
                                <div style="position:relative;display:inline-block;">
                                    <div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        h</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        e</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        a</div><div
                                        style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                        r</div>
                                </div> <span>
                                    <div style="position:relative;display:inline-block;">
                                        <div
                                            style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                            f</div><div
                                            style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                            r</div><div
                                            style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                            o</div><div
                                            style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                            m</div>
                                    </div>
                                    <div style="position:relative;display:inline-block;">
                                        <div
                                            style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                            y</div><div
                                            style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                            o</div><div
                                            style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                            u</div>
                                    </div>
                                </span>
                            </h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s"
                                style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">Your email
                                address will not be published. Required fields are marked *</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- Contact Form Start -->
                        <div class="contact-form">
                            <!-- Contact Form Start -->
                            <form id="MycontactForm" onsubmit="return true;" action="ContactUs.php" method="POST" 
                                class="wow fadeInUp" data-wow-delay="0.4s" 
                                style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                                <div class="row">
                                    <div class="form-group col-md-6 mb-4">
                                        <input type="text" name="name" class="form-control" id="name"
                                            placeholder="Name*" required="">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <input type="email" name="email" class="form-control" id="email"
                                            placeholder="Email Address*" required="">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-12 mb-4">
                                        <input type="text" name="phone" class="form-control" id="phone"
                                            placeholder="Your Phone" required="">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-12 mb-5">
                                        <textarea name="message" class="form-control" id="message" rows="4"
                                            placeholder="Your Message"></textarea>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <div class="h-captcha" data-sitekey="144a89ac-e82e-4d7c-a118-649413e4188c"></div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn-default">submit</button>
                                        <div id="msgSubmit" class="h3 hidden"></div>
                                    </div>
                                </div>
                            </form>
                            <!-- Contact Form End -->
                        </div>
                        <!-- Contact Form End -->
                    </div>
                    <!-- Contact Us Form End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Contact Us End -->

    <!-- Google Map Section Start -->
    <div class="google-map">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp" style="visibility: hidden; animation-name: none;">Our contact</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">
                            <div style="position:relative;display:inline-block;">
                                <div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    G</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    e</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    t</div></div>
                            <div style="position:relative;display:inline-block;">
                                <div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    i</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    n</div>
                            </div>
                            <div style="position:relative;display:inline-block;">
                                <div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    t</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    o</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    u</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    c</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    h</div>
                            </div>
                            <div style="position:relative;display:inline-block;">
                                <div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    w</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    i</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    t</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    h</div>
                            </div>
                            <div style="position:relative;display:inline-block;">
                                <div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    u</div><div
                                    style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                    s</div>
                            </div>
                        </h2>
                        <p class="wow fadeInUp" data-wow-delay="0.2s"
                            style="visibility: hidden; animation-delay: 0.2s; animation-name: none;">Get in touch to
                            discuss your employee wellbeing needs today. Please give us a call, drop us an email or fill
                            out the contact form and we'll get back to you.</p>
                    </div>
                    <!-- Section Title End -->
                </div>

                <div class="col-lg-12">
                    <!-- Google Map IFrame Start -->
                    <div class="google-map-iframe">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3760.095295682453!2d72.84932841534204!3d19.204717187038786!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7b6d8a01bffff%3A0x5ed0fef9f5bb2c45!2sSTUDIO%209%20ARCHITECT%20AND%20INTERIOR%20DESIGNER!5e0!3m2!1sen!2sin!4v1756812345678!5m2!1sen!2sin"
                            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>

                        <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14010.126019378475!2d72.86476966944933!3d19.14815830097553!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7b7c09b1e91c1%3A0x81b054f5b6bec1be!2sNew%20Zealand%20Hostel%20Cricket%20%26%20Volleyball%20Ground!5e0!3m2!1sen!2sin!4v1752663531418!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>                    </div>
                    Google Map IFrame End -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <!-- Contact Info Box Start -->
                        <div class="contact-info-box">
                            <!-- Conatct Info Item Start -->
                            <div class="contact-info-item wow fadeInUp"
                                style="visibility: hidden; animation-name: none;">
                                <!-- Icon Box Start -->
                                <div class="icon-box">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <!-- Icon Box End -->

                                <!-- Contact Info Content Start -->
                                <div class="contact-info-content">
                                    <h3>phone number</h3>
                                    <p>+91 8080083888 / +91 2228052434</p>

                                </div>
                                <!-- Contact Info Content End -->
                            </div>
                            <!-- Conatct Info Item End -->

                            <!-- Conatct Info Item Start -->
                            <div class="contact-info-item wow fadeInUp" data-wow-delay="0.2s"
                                style="visibility: hidden; animation-delay: 0.2s; animation-name: none;">
                                <!-- Icon Box Start -->
                                <div class="icon-box">
                                    <i class="fa-regular fa-envelope"></i>
                                </div>
                                <!-- Icon Box End -->

                                <!-- Contact Info Content Start -->
                                <div class="contact-info-content">
                                    <h3>e-mail support</h3>
                                    <p>bhojanistudio9@hotmail.com</p>
                                </div>
                                <!-- Contact Info Content End -->
                            </div>
                            <!-- Conatct Info Item End -->

                            <!-- Conatct Info Item Start -->
                            <!--<div class="contact-info-item wow fadeInUp" data-wow-delay="0.4s" style="visibility: hidden; animation-delay: 0.4s; animation-name: none;">
                             Icon Box Start -->
                            <!--<div class="icon-box">
                                <i class="fa-solid fa-house"></i>
                            </div>-->
                            <!-- Icon Box End -->

                            <!-- Contact Info Content Start -->
                            <!--<div class="contact-info-content">
                                <h3>headquarter</h3>
                                <p>abcd</p>
                            </div>-->
                            <!-- Contact Info Content End
                        </div>-->
                            <!-- Conatct Info Item End -->
                        </div>
                        <!-- Contact Info Box End -->
                    </div>
                </div>
            </div>
        </div>
    </div>    
        <!-- Google Map Section End -->

        <!-- Footer Start -->
        <footer class="main-footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Footer Header Start -->
                        <div class="footer-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <!-- Footer Logo Start -->
                                    <div class="footer-logo">
                                        <img src="./assets/img/newAssets/S9 LOGO.png" alt="Logo" class="logo_size">
                                    </div>
                                    <!-- Footer Logo End -->
                                </div>

                                <div class="col-md-6">
                                    <!-- Footer Social Link Start -->
                                    <div class="footer-social-links">
                                        <div class="footer-social-link-title">
                                            <h3>follow our socials</h3>
                                        </div>
                                        <ul>
                                            <li><a href="https://www.facebook.com/Studio9architect"><i
                                                        class="fa-brands fa-facebook-f"></i></a></li>
                                            <!--<li><a href="#"><i class="fa-brands fa-dribbble"></i></a></li>-->
                                            <li><a href="https://www.instagram.com/studio9architects/"><i
                                                        class="fa-brands fa-instagram"></i></a></li>
                                        </ul>
                                    </div>
                                    <!-- Footer Social Link End -->
                                </div>
                            </div>
                        </div>
                        <!-- Footer Header End -->
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <!-- Footer Links Start -->
                        <div class="footer-links">
                            <h3>information</h3>
                            <ul>
                                <li><a href="aboutus.html">about our company</a></li>
                                <li><a href="Services.html">view our service</a></li>
                                <li><a href="Projects.html">our latest projects</a></li>
                                <li><a href="ContactUs.php">contact us</a></li>
                            </ul>
                        </div>
                        <!-- Footer Links End -->
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <!-- Footer Links Start -->
                        <div class="footer-links">
                            <h3>portfolio</h3>
                            <ul>
                                <li><a href="./Residential_design.html">luxury home design</a></li>
                                <li><a href="./Residential.html">residential interior design</a></li>
                                <li><a href="./Commercial.html">commercial space design</a></li>
                                <li><a href="./Healthcare.html">healthcare design</a></li>
                            </ul>
                        </div>
                        <!-- Footer Links End -->
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <!-- Footer Contact Box Start -->
                        <div class="footer-contact-box footer-links">
                            <h3>contact us</h3>
                            <!-- Footer Contact Item Start -->
                            <div class="footer-contact-item">
                                <div class="icon-box">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="footer-contact-content">
                                    <p>+91-8080083888</p>
                                    <p>+91-2228052434</p>
                                </div>
                            </div>
                            <!-- Footer Contact Item End -->

                            <!-- Footer Contact Item Start -->
                            <div class="footer-contact-item">
                                <div class="icon-box">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="footer-contact-content">
                                    <p>bhojanistudio9@hotmail.com</p>
                                </div>
                            </div>
                            <!-- Footer Contact Item End -->

                            <!-- Footer Contact Item Start -->
                            <div class="footer-contact-item">
                                <div class="icon-box">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="footer-contact-content">
                                    <p>Shop No 2, Kesar Kunj, Vasanji Lalji Rd, near Railway Station, Kandivali, Jethava Nagar, Kandivali West, Mumbai, Maharashtra 400067</p>
                                </div>
                            </div>
                            <!-- Footer Contact Item End -->
                        </div>
                        <!-- Footer Contact Box End -->
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <!-- Footer Newsletter Form Start -->
                        <div class="footer-latest-news footer-links">
                            <h3>get the latest trending news</h3>

                            <div class="footer-newsletter-form">
                                <p>Your Dream Space Starts Here Get Exclusive Design Straight Your Inbox!</p>

                                <form id="newslettersForm" action="404.html#" method="POST">
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control" id="mail"
                                            placeholder="Enter your email" required="">
                                        <button type="submit"><i class="fa-solid fa-arrow-right-long"></i> </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Footer Newsletter Form End -->
                    </div>
                </div>

                <!-- Footer Copyright Section Start -->
                <div class="footer-copyright">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Footer Copyright Start -->
                            <div class="footer-copyright-text">
                                <p>Copyright © 2026 All Rights Reserved.</p>
                            </div>
                            <!-- Footer Copyright End -->
                        </div>
                    </div>
                </div>
                <!-- Footer Copyright Section End -->
            </div>
        </footer>
        <!-- Footer End -->
        <!-- Jquery Library File -->
        <script src="./assets/js/jquery-3.7.1.min.js"></script>
        <!-- Bootstrap js file -->
        <script src="./assets/js/bootstrap.min.js"></script>
        <!-- Validator js file -->
        <script src="./assets/js/validator.min.js"></script>
        <!-- SlickNav js file -->
        <script src="./assets/js/jquery.slicknav.js"></script>
        <!-- Swiper js file -->
        <script src="./assets/js/swiper-bundle.min.js"></script>
        <!-- Counter js file -->
        <script src="./assets/js/jquery.waypoints.min.js"></script>
        <script src="./assets/js/jquery.counterup.min.js"></script>
        <!-- Isotop js file -->
        <script src="./assets/js/isotope.min.js"></script>
        <!-- Magnific js file -->
        <script src="./assets/js/jquery.magnific-popup.min.js"></script>
        <!-- SmoothScroll -->
        <script src="./assets/js/SmoothScroll.js"></script>
        <!-- Sweet Alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Parallax js -->
        <script src="./assets/js/parallaxie.js"></script>
        <!-- MagicCursor js file -->
        <script src="./assets/js/gsap.min.js"></script>
        <script src="./assets/js/magiccursor.js"></script>
        <div class="cb-cursor -active" style="transform: translate3d(1073px, 467px, 0px);">
            <div class="cb-cursor-text"></div>
        </div>
        <!-- Text Effect js file -->
        <script src="./assets/js/SplitText.js"></script>
        <script src="./assets/js/ScrollTrigger.min.js"></script>
        <!-- YTPlayer js File -->
        <script src="./assets/js/jquery.mb.YTPlayer.min.js"></script>
        <!-- Wow js file -->
        <script src="./assets/js/wow.min.js"></script>
        <!-- Main Custom js file -->
        <!-- <script src="./assets/js/function.js"></script> -->

        <!-- Adding hcaptcha  -->
        <script src="https://hcaptcha.com/1/api.js" async defer></script>


        <!-- disable right click on images -->
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
    <script>
        document.querySelectorAll('img').forEach(img => {
            img.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });
        });
    </script>
    <script>
        document.querySelectorAll('img').forEach(img => {
            img.setAttribute('draggable', false);
        });
    </script>
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && (e.key === 's' || e.key === 'u')) {
                e.preventDefault();
            }
        });
    </script>
    <script>
        document.getElementById("contactForm").addEventListener("submit", function(e) {
            e.preventDefault();

            let form = this;
            let formData = new FormData(form);

            fetch("ContactUs.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Message Sent!',
                        text: 'We will get back to you soon.'
                    });
                    form.reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong!'
                });
            });
        });
    </script>


</body>

</html>