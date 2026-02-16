<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'BNGRC - Système de Gestion des Dons' ?></title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/bootstrap-icons/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
            --danger-gradient: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            --warning-gradient: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
            --info-gradient: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: var(--primary-gradient) !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            margin: 0 0.25rem;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.2);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            border-radius: 12px;
            padding: 0.5rem;
            animation: fadeInDown 0.3s ease;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dropdown-item {
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .dropdown-item:hover {
            background: var(--primary-gradient);
            color: white;
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            font-size: 1.2rem;
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.18);
        }
        
        .card-header {
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1.25rem 1.5rem;
        }
        
        .btn {
            border-radius: 50px;
            padding: 0.6rem 1.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        
        .btn-primary {
            background: var(--primary-gradient);
        }
        
        .btn-success {
            background: var(--success-gradient);
        }
        
        .btn-danger {
            background: var(--danger-gradient);
        }
        
        .btn-warning {
            background: var(--warning-gradient);
            color: #fff;
        }
        
        .btn-info {
            background: var(--info-gradient);
            color: #fff;
        }
        
        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.875rem;
        }
        
        .table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .table thead {
            background: var(--primary-gradient);
            color: white;
        }
        
        .table thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(102, 126, 234, 0.04);
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .content-wrapper {
            min-height: calc(100vh - 76px - 100px);
            padding: 2.5rem 0;
        }
        
        .page-header {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .page-header h1 {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            margin: 0;
            font-size: 2.5rem;
        }
        
        .alert {
            border: none;
            border-radius: 15px;
            padding: 1.25rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dons">
                <i class="bi bi-heart-fill"></i> BNGRC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') == 'dons' ? 'active' : '' ?>" href="/dons">
                            <i class="bi bi-house-heart"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') == 'history' ? 'active' : '' ?>" href="/dons/history">
                            <i class="bi bi-clock-history"></i> Historique
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= in_array($currentPage ?? '', ['villes', 'besoins', 'types']) ? 'active' : '' ?>" 
                           href="#" id="navbarDropdownGestion" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear-fill"></i> Gestion
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownGestion">
                            <li>
                                <a class="dropdown-item" href="/villes">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    <span>Villes</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/types-besoin">
                                    <i class="bi bi-tag-fill text-info"></i>
                                    <span>Types de Besoins</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/besoins">
                                    <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                    <span>Besoins</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-calendar3 me-2"></i>
                    <small><?= date('d/m/Y') ?></small>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="content-wrapper">
        <div class="container-fluid">
