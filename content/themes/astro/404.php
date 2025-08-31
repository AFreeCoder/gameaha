<?php include TEMPLATE_PATH . "/includes/header.php" ?>

<main class="min-h-screen bg-[#0F1729] pt-24">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col items-center justify-center py-16">
            <!-- 404 Image -->
            <div class="mb-8">
                <img 
                    src="<?php echo DOMAIN . TEMPLATE_PATH . "/images/404.png" ?>" 
                    alt="404 Error"
                    class="max-w-full h-auto"
                >
            </div>

            <!-- Error Message -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    <?php _e('Page Not Found') ?>
                </h1>
                <p class="text-gray-300 text-lg mb-8">
                    <?php _e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.') ?>
                </p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="<?php echo DOMAIN ?>" 
                        class="px-6 py-3 rounded-full bg-violet-600 text-white hover:bg-violet-700 transition-colors">
                        <?php _e('Back to Home') ?>
                    </a>
                    <a href="javascript:history.back()" 
                        class="px-6 py-3 rounded-full bg-violet-600/20 text-violet-300 hover:bg-violet-600 hover:text-white transition-colors">
                        <?php _e('Go Back') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include TEMPLATE_PATH . "/includes/footer.php" ?>