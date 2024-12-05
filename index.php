    <?php include 'includes/header.php'; ?>
    <?php include 'includes/navbar.php'; ?>

    <main>
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'homepage';
        $allowedPages = ['homepage', 'about', 'academics', 'admission','contact', 'profile', 'logout', 'dashboard', 'subject'];
        
        if (in_array($page, $allowedPages)) {
            include $page . '.php';
        } else {
            include 'homepage.php';
        }
        ?>
    </main>
    <?php include 'includes/footer.php'; ?>
    
</body>
</html>