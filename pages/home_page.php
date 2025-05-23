<?php
session_start();
require_once('../connect_database.php');
include('../includes/header_view.php');
include('../includes/slider_bar.php');
require_once '../services/get_count_users.php';
// Empêcher la mise en cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Vérifier la connexion
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header('Location: login.php');
    exit;
}

// Données de démonstration pour un système éducatif
$niveaux = [
    ['id' => 'CE1', 'nom' => 'CE1', 'description' => 'Niveau élémentaire débutant', 'nb_lecons' => 24, 'icone' => '📘'],
    ['id' => 'CE2', 'nom' => 'CE2', 'description' => 'Niveau élémentaire intermédiaire', 'nb_lecons' => 28, 'icone' => '📗'],
    ['id' => 'CM1', 'nom' => 'CM1', 'description' => 'Niveau élémentaire avancé', 'nb_lecons' => 32, 'icone' => '📕'],
    ['id' => 'CM2', 'nom' => 'CM2', 'description' => 'Préparation collège', 'nb_lecons' => 36, 'icone' => '📓']
];

$eleves = [
    ['id' => 'E-2023-01', 'nom' => 'Léa Dupont', 'niveau' => 'CE2', 'progression' => 68, 'avatar' => '👧'],
    ['id' => 'E-2023-02', 'nom' => 'Marc Lambert', 'niveau' => 'CM1', 'progression' => 42, 'avatar' => '👦'],
    ['id' => 'E-2023-03', 'nom' => 'Sophie Martin', 'niveau' => 'CE1', 'progression' => 87, 'avatar' => '👩']
];

$ressources = [
    ['id' => 'T-001', 'type' => 'texte', 'titre' => 'La forêt enchantée', 'niveau' => 'CE1', 'utilisations' => 142],
    ['id' => 'T-002', 'type' => 'exercice', 'titre' => 'Conjugaison présent', 'niveau' => 'CE2', 'utilisations' => 98],
    ['id' => 'T-003', 'type' => 'évaluation', 'titre' => 'Test de fin de période', 'niveau' => 'CM1', 'utilisations' => 76]
];

$stats = [
    'exercices_completes' => ['value' => 1248, 'evolution' => '+15%'],
    'eleves_actifs' => ['value' => 342, 'evolution' => '+8'],
    'moyenne_scores' => ['value' => '78%', 'evolution' => '+5%']
];

$users = new Users();
$countUser = $users->GetCountUserRegistrer();
$countUserA = $users->GetUsersAtifs();
$users_actifs = $countUserA == 0 ? 'Aucun utilisateur actif' : $countUserA;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Pédagogique | Démo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
               :root {
            --primary: #5e72e4;
            --secondary: #f5365c;
            --success: #2dce89;
            --info: #11cdef;
            --warning: #fb6340;
            --light: #f8f9fa;
            --dark: #32325d;
            --gray: #adb5bd;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: #f0f2f5;
        }
        
        .dashboard-container {
            padding: 2rem;
            margin-left: 250px;
            transition: all 0.3s;
            margin-top: 150px;
        }
        
        .section-title {
            font-size: 1.4rem;
            color: var(--dark);
            margin: 1.5rem 0 1rem;
            position: relative;
            padding-left: 1rem;
        }
        
        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--primary);
            border-radius: 4px;
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(.25,.8,.25,1);
            border: none;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
            padding: 1.25rem 1.5rem;
            background: var(--primary);
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .card-header i {
            font-size: 1.25rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.75rem;
        }
        
        .card-text {
            color: var(--gray);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1.25rem;
        }
        
        .progress-container {
            margin: 1.5rem 0 0.5rem;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.25rem;
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        .progress {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(to right, var(--primary), var(--info));
            border-radius: 4px;
        }
        
        .stats-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(94, 114, 228, 0.1);
            color: var(--primary);
        }
        
        .stats-badge i {
            margin-right: 5px;
            font-size: 0.7rem;
        }
        
        .stats-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0.5rem 0;
        }
        
        .stats-comparison {
            font-size: 0.85rem;
            color: var(--gray);
        }
        
        .stats-comparison.positive {
            color: var(--success);
        }
        
        .stats-comparison.negative {
            color: var(--warning);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(94, 114, 228, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-right: 12px;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .user-level {
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        @media (max-width: 992px) {
            .dashboard-container {
                margin-left: 0;
                padding: 1rem;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }
        .badge-niveau {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 8px;
        }
        
        .CE1 { background: #ffdfba; color: #8a4f00; }
        .CE2 { background: #baffc9; color: #006622; }
        .CM1 { background: #bae1ff; color: #003366; }
        .CM2 { background: #e2baff; color: #4b0082; }
        
        .ressource-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        
        .texte { border-color: #4CAF50; }
        .exercice { border-color: #2196F3; }
        .evaluation { border-color: #FF9800; }
        
        .type-indicator {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-right: 8px;
        }

        .demo-blur:hover {
        filter: blur(0);
        opacity: 1;
        transform: translateY(-5px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(251, 99, 64, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(251, 99, 64, 0); }
            100% { box-shadow: 0 0 0 0 rgba(251, 99, 64, 0); }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Bannière démo -->
        <div class="demo-alert" style="...">
            <i class="fas fa-chalkboard-teacher" style="font-size: 1.5rem; margin-right: 15px;"></i>
            <div>
                <h3 style="margin: 0 0 5px 0;">Tableau de bord</h3>
                <p style="margin: 0; opacity: 0.9; font-size: 0.9rem;">
                   
                </p>
            </div>
        </div>

        <!-- Section Statistiques -->
        <h2 class="section-title">Indicateurs</h2>
        <div class="cards-grid demo-mode">
            <div class="demo-watermark"></div>
            <div class="card demo-blur">
                <div class="card-body">
                    <div class="stats-badge">
                        <i class="fas fa-user-graduate"></i>
                       Inscrits
                    </div>
                    <div class="stats-value"><?= $countUser ?></div>
                    <div class="stats-comparison positive">
                        <i class="fas fa-arrow-up"></i> 
                    </div>
                </div>
            </div>
            
            <div class="card demo-blur">
                <div class="card-body">
                    <div class="stats-badge">
                        <i class="fas fa-user-graduate"></i>
                        Nombre des parties
                    </div>
                    <div class="stats-value"><?= $users_actifs ?></div>
                    <div class="stats-comparison positive">
                        <i class="fas fa-user-plus"></i> 
                    </div>
                </div>
            </div>
            
            <div class="card demo-blur">
                <div class="card-body">
                    <div class="stats-badge">
                        <i class="fas fa-star"></i>
                        Moyenne des scores
                    </div>
                    <div class="stats-value"><?= $stats['moyenne_scores']['value'] ?></div>
                    <div class="stats-comparison positive">
                        <i class="fas fa-arrow-up"></i> <?= $stats['moyenne_scores']['evolution'] ?> progression
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section Niveaux -->
        <h2 class="section-title">Programmes par Niveau</h2>
        <div class="cards-grid demo-mode">
            <div class="demo-watermark"></div>
            <?php foreach ($niveaux as $niveau): ?>
            <div class="card demo-blur">
                <div class="card-header">
                    <span style="font-size: 1.5rem;"><?= $niveau['icone'] ?></span>
                    <?= htmlspecialchars($niveau['nom']) ?>
                </div>
                <div class="card-body">
                    <h3 class="card-title"><?= htmlspecialchars($niveau['description']) ?></h3>
                    <p class="card-text"><?= $niveau['nb_lecons'] ?> leçons disponibles</p>
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Couverture du programme</span>
                            <span><?= rand(65, 95) ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?= rand(65, 95) ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Section Élèves -->
        <h2 class="section-title">Progression</h2>
        <div class="cards-grid demo-mode">
            <div class="demo-watermark"></div>
            <?php foreach ($eleves as $eleve): ?>
            <div class="card demo-blur">
                <div class="card-body">
                    <div style="display: flex; align-items: center;">
                        <div class="user-avatar">
                            <?= $eleve['avatar'] ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name">
                                <?= htmlspecialchars($eleve['nom']) ?>
                                <span class="badge-niveau <?= $eleve['niveau'] ?>"><?= $eleve['niveau'] ?></span>
                            </div>
                            <div class="user-level">Dernière activité: <?= rand(1, 7) ?> j</div>
                        </div>
                    </div>
                    <div class="progress-container" style="margin-top: 1.5rem;">
                        <div class="progress-label">
                            <span>Progression annuelle</span>
                            <span><?= $eleve['progression'] ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?= $eleve['progression'] ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Section Ressources -->
        <h2 class="section-title">Au grenier</h2>
        <div class="cards-grid demo-mode">
            <div class="demo-watermark"></div>
            <?php foreach ($ressources as $ressource): ?>
            <div class="card demo-blur ressource-card <?= $ressource['type'] ?>">
                <div class="card-body">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <span class="type-indicator" style="background: <?= 
                            $ressource['type'] === 'texte' ? '#4CAF50' : 
                            ($ressource['type'] === 'exercice' ? '#2196F3' : '#FF9800') 
                        ?>">
                            <?= $ressource['type'] ?>
                        </span>
                        <span class="badge-niveau <?= $ressource['niveau'] ?>"><?= $ressource['niveau'] ?></span>
                    </div>
                    <h3 class="card-title"><?= htmlspecialchars($ressource['titre']) ?></h3>
                    <p class="card-text">
                        <i class="fas fa-users"></i> Utilisé <?= $ressource['utilisations'] ?> fois
                    </p>
                    <div style="margin-top: 1rem;">
                        <button style="padding: 6px 12px; background: #f0f0f0; border: none; border-radius: 4px;">
                            <i class="fas fa-eye"></i> Prévisualiser
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Animation des éléments au chargement
        document.addEventListener('DOMContentLoaded', function() {
            // Animation des barres de progression
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                    bar.style.transition = 'width 1.5s ease-out';
                }, 100);
            });
            
            // Effet de soulignement pour les titres
            const titles = document.querySelectorAll('.section-title');
            titles.forEach(title => {
                title.style.opacity = '0';
                title.style.transform = 'translateX(-20px)';
                title.style.transition = 'all 0.5s ease';
                setTimeout(() => {
                    title.style.opacity = '1';
                    title.style.transform = 'translateX(0)';
                }, 300);
            });
        });
    </script>
</body>
</html>