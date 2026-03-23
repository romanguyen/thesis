<?php

/**
 * Main template file for the metacentrum_bs5 template (Standalone Version)
 *
 * @link   https://www.dokuwiki.org/template:metacentrum_bs5
 */

if (!defined('DOKU_INC')) die();

$showSidebar = page_findnearest($conf['sidebar']);
$hasSidebar = $showSidebar && ($ACT === 'show');
?>
<!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<?php require __DIR__ . '/head.php'; ?>

<body>
    <div id="dokuwiki__site" class="d-flex min-vh-100">
        <!-- Left Sidebar Navigation -->
        <aside id="dokuwiki__sidebar" class="sidebar-nav d-none d-lg-block">
            <div class="sidebar-content">
                <!-- Sidebar Logo -->
                <div class="sidebar-logo mb-4">
                    <a href="<?php echo wl() ?>" class="text-decoration-none">
                        <h2 class="text-orange mb-1">metacentrum</h2>
                        <p class="text-white-50 mb-0 small">cesnet</p>
                    </a>
                </div>

                <!-- Sidebar Navigation -->
                <nav class="sidebar-nav-menu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">About MetaCentrum</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Get Account in VO</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Resources</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">R & D</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Results</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Projects</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">FAQ</a>
                        </li>
                    </ul>
                </nav>

                <!-- Sidebar Footer -->
                <div class="sidebar-footer mt-auto">
                    <p class="text-white-50 small">e-infrastruktura cesnet</p>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main id="dokuwiki__content" class="main-content flex-grow-1">
            <?php require __DIR__ . '/header.php'; ?>

            <!-- Mobile Sidebar -->
            <div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>
            <aside class="mobile-sidebar" id="mobileSidebar">
                <div class="sidebar-content">
                    <!-- Mobile Sidebar Logo -->
                    <div class="sidebar-logo mb-4 d-flex justify-content-between align-items-center">
                        <a href="<?php echo wl() ?>" class="text-decoration-none">
                            <h2 class="text-orange mb-1">metacentrum</h2>
                            <p class="text-white-50 mb-0 small">cesnet</p>
                        </a>
                        <button class="mobile-nav-close text-white" id="mobileNavClose">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Mobile Sidebar Navigation -->
                    <nav class="sidebar-nav-menu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link text-white" href="#">About MetaCentrum</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="#">Services</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="#">Get Account in VO</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="#">Resources</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="#">R & D</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="#">Results</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="#">Projects</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="#">FAQ</a>
                            </li>
                        </ul>
                    </nav>

                    <!-- Mobile Sidebar Footer -->
                    <div class="sidebar-footer mt-auto">
                        <p class="text-white-50 small">e-infrastruktura cesnet</p>
                    </div>
                </div>
            </aside>

            <!-- Hero Section -->
            <section class="hero-section">
                <div class="hero-content">
                    <div class="container">
                        <div class="row align-items-center min-vh-100">
                            <div class="col-lg-8">
                                <div class="hero-tagline mb-3">
                                    <span class="hero-tagline-text">EXPERIENCE, PROFESSIONALISM, TECHNOLOGY</span>
                                </div>
                                <h1 class="hero-title">
                                    <span class="d-block">WE ARE METACENTRUM</span>
                                    <span class="d-block">CONNECTING SCIENCE</span>
                                </h1>
                                <p class="hero-description">
                                    We provide advanced information and communication services for science, research, and education. We manage and develop the academic computer network, ensure secure authentication to our portfolio of services, offer an environment for demanding computations, data storage space, and communication tools for individuals and teams.
                                </p>
                                <button class="btn btn-hero">
                                    Learn More <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Wave Transition -->
                <div class="wave-transition">
                    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="white"></path>
                        <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="white"></path>
                        <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="white"></path>
                    </svg>
                </div>
            </section>

            <!-- Pushing the Boundaries Section -->
            <section class="content-section py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="text-orange mb-4">Pushing the Boundaries</h2>
                            <p class="lead text-muted mb-5">
                                We conduct research and development in the field of information and communication technologies. We bring research results into practice by expanding our portfolio of services and improving them.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Feature Blocks Section -->
            <section class="feature-blocks-section py-5 bg-light">
                <div class="container">
                    <div class="row g-4">
                        <!-- Computing -->
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-cubes"></i>
                                </div>
                                <h4 class="feature-title">Computing</h4>
                                <p class="feature-description">
                                    High-performance computing infrastructure for demanding computational tasks and research projects.
                                </p>
                            </div>
                        </div>

                        <!-- Data Storage -->
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-database"></i>
                                </div>
                                <h4 class="feature-title">Data Storage</h4>
                                <p class="feature-description">
                                    Secure and scalable data storage solutions for managing large datasets and research data.
                                </p>
                            </div>
                        </div>

                        <!-- Security -->
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h4 class="feature-title">Security</h4>
                                <p class="feature-description">
                                    Advanced security measures and secure authentication systems to protect your research.
                                </p>
                            </div>
                        </div>

                        <!-- Network -->
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <h4 class="feature-title">Network</h4>
                                <p class="feature-description">
                                    High-speed academic network infrastructure connecting research institutions across the country.
                                </p>
                            </div>
                        </div>

                        <!-- Grid Infrastructure -->
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-th"></i>
                                </div>
                                <h4 class="feature-title">Grid Infrastructure</h4>
                                <p class="feature-description">
                                    Distributed computing resources as part of the European Grid Initiative (EGI).
                                </p>
                            </div>
                        </div>

                        <!-- Cooperation -->
                        <div class="col-lg-4 col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h4 class="feature-title">Cooperation</h4>
                                <p class="feature-description">
                                    Collaborative environment supporting research teams and international projects.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Information Sections -->
            <section class="info-sections py-5">
                <div class="container">
                    <div class="row">
                        <!-- Important Information -->
                        <div class="col-lg-6 mb-5">
                            <h3 class="text-dark mb-4">Important Information</h3>
                            <div class="info-links">
                                <a href="#" class="info-link">
                                    <i class="fas fa-user-plus me-2"></i>
                                    I want to become a member
                                    <i class="fas fa-arrow-right ms-auto"></i>
                                </a>
                                <a href="#" class="info-link">
                                    <i class="fas fa-star me-2"></i>
                                    Advantages of the NGI
                                    <i class="fas fa-arrow-right ms-auto"></i>
                                </a>
                                <a href="#" class="info-link">
                                    <i class="fas fa-certificate me-2"></i>
                                    I need a certificate
                                    <i class="fas fa-arrow-right ms-auto"></i>
                                </a>
                                <a href="#" class="info-link">
                                    <i class="fas fa-book me-2"></i>
                                    Documentation and help
                                    <i class="fas fa-arrow-right ms-auto"></i>
                                </a>
                                <a href="#" class="info-link">
                                    <i class="fas fa-cloud me-2"></i>
                                    MetaVO web page
                                    <i class="fas fa-arrow-right ms-auto"></i>
                                </a>
                                <a href="#" class="info-link">
                                    <i class="fas fa-server me-2"></i>
                                    MetaCloud
                                    <i class="fas fa-arrow-right ms-auto"></i>
                                </a>
                                <a href="#" class="info-link">
                                    <i class="fas fa-list me-2"></i>
                                    List of HW
                                    <i class="fas fa-arrow-right ms-auto"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Map of HW Centres -->
                        <div class="col-lg-6 mb-5">
                            <h3 class="text-dark mb-4">Map of HW Centres</h3>
                            <div class="map-container">
                                <div class="map-placeholder">
                                    <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Interactive map of hardware centres across the Czech Republic</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MetaCentrum NGI Content -->
            <section class="ngi-content-section py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="text-dark mb-4">MetaCentrum NGI</h3>
                            <h4 class="text-muted mb-4">Welcome to MetaCentrum NGI web pages, the activity of the CESNET association.</h4>

                            <div class="ngi-content">
                                <p class="mb-3">
                                    MetaCentrum is responsible for the <strong>operation and coordination of distributed computing and data storage infrastructure</strong> for the Czech research community. Our mission is to provide researchers with access to high-performance computing resources and advanced data management solutions.
                                </p>

                                <p class="mb-3">
                                    We focus on the <strong>expansion of available computational capacities</strong> and the development of new services that support cutting-edge research. Our infrastructure is designed to handle <strong>tasks whose memory and/or CPU requirements exceed the possibility of individual single computing centers</strong>.
                                </p>

                                <p class="mb-3">
                                    Our platform can <strong>fully integrate any computing capacities</strong> from various research institutions, creating a unified environment that <strong>supports research projects</strong> across multiple disciplines. We are actively <strong>involved in many other international Grid projects</strong> and collaborate with leading research organizations worldwide.
                                </p>

                                <p class="mb-0">
                                    Through our comprehensive portfolio of services, we enable researchers to focus on their scientific work while we handle the complex infrastructure requirements. Our commitment to excellence and innovation drives us to continuously improve our services and expand our capabilities.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- DokuWiki Content Section -->
            <section class="dokuwiki-content py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="content-wrapper">
                                <?php if ($hasSidebar): ?>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div id="dokuwiki__aside" class="content-sidebar">
                                                <?php tpl_include_page($conf['sidebar'], 1, 1) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                        <?php else: ?>
                                            <div class="col-12">
                                            <?php endif; ?>
                                            <div class="<?php echo $page_class ?>">
                                                <?php tpl_flush() ?>
                                                <?php tpl_includeFile('pageheader.html') ?>
                                                <div class="page">
                                                    <?php tpl_content() ?>
                                                </div>
                                                <?php tpl_includeFile('pagefooter.html') ?>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
            </section>
        </main>
    </div>

    <?php require __DIR__ . '/footer.php'; ?>
    <?php require __DIR__ . '/scripts.php'; ?>
</body>

</html>