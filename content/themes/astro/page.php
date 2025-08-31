<?php include TEMPLATE_PATH . "/includes/header.php" ?>

<main class="min-h-screen bg-[#0F1729] pt-24">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content Column -->
            <div class="lg:col-span-3">
                <!-- Page Container -->
                <div class="bg-[#1E2A45] rounded-2xl overflow-hidden shadow-xl mb-8">
                    <!-- Page Header -->
                    <div class="p-6 bg-[#2A364F]">
                        <h1 class="text-2xl md:text-3xl font-bold text-white">
                            <?php echo htmlspecialchars($page->title) ?>
                        </h1>
                    </div>

                    <!-- Page Content -->
                    <article class="p-6">
                        <div class="prose prose-invert prose-violet max-w-none text-gray-300">
                            <?php
                            if($page->nl2br == 1){
                                echo nl2br($page->content);
                            } else {
                                echo $page->content;
                            }
                            ?>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <?php widget_aside('sidebar-1') ?>
                </div>
            </div>
        </div>

        <?php widget_aside('bottom-content') ?>
    </div>
</main>

<?php include TEMPLATE_PATH . "/includes/footer.php" ?>