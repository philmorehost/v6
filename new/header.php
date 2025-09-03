<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<header class="navbar navbar-light sticky-top bg-white flex-md-nowrap p-0 shadow-sm">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">
        <img src="/assets-2/img/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
        VTU Platform
    </a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav ms-auto">
        <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="/asset/boy-icon.png" alt="mdo" width="32" height="32" class="rounded-circle">
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                <li><a class="dropdown-item" href="#">API Docs</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Logout</a></li>
            </ul>
        </div>
    </div>
</header>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">
                            <i data-feather="home" class="align-text-bottom"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                             <i data-feather="dollar-sign" class="align-text-bottom"></i>
                            Fund Wallet
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                             <i data-feather="arrow-up-right" class="align-text-bottom"></i>
                            Fund Transfer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                             <i data-feather="smartphone" class="align-text-bottom"></i>
                            Buy Airtime
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                             <i data-feather="wifi" class="align-text-bottom"></i>
                            Buy Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                             <i data-feather="tv" class="align-text-bottom"></i>
                            Cable TV
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                             <i data-feather="zap" class="align-text-bottom"></i>
                            Electricity
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                             <i data-feather="activity" class="align-text-bottom"></i>
                            Transactions
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Account</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                             <i data-feather="user" class="align-text-bottom"></i>
                            Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                             <i data-feather="settings" class="align-text-bottom"></i>
                            Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                             <i data-feather="log-out" class="align-text-bottom"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
