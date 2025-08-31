<?php
/**
 * Admin page for Game Description Editor plugin
 * Provides a standalone interface to edit game descriptions and instructions
 */

if(!has_admin_access()){
    exit;
}

// Get plugin slug
$plugin_slug = basename(dirname(__FILE__));

// Initialize variables
$game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 1;
$game = null;
$message = '';
$message_type = '';

// Handle form submissions
if(isset($_POST['action'])) {
    // Handle game data fetch
    if($_POST['action'] == 'fetch_game' && !empty($_POST['game_id'])) {
        $game_id = intval($_POST['game_id']);
        $game = Game::getById($game_id);
        
        if(!$game) {
            $message = 'Game not found with ID: ' . $game_id;
            $message_type = 'warning';
        }
    }
    
    // Handle game update
    if($_POST['action'] == 'update_game' && !empty($_POST['game_id'])) {
        $game_id = intval($_POST['game_id']);
        $game = Game::getById($game_id);
        
        if($game) {
            // Update game data
            $game->description = $_POST['description'] ?? $game->description;
            $game->instructions = $_POST['instructions'] ?? $game->instructions;
            $game->editor_type = 'tinymce';
            $game->update();
            
            $message = 'Game information updated successfully!';
            $message_type = 'success';
            
            // Refresh game data after update
            $game = Game::getById($game_id);
        } else {
            $message = 'Game not found with ID: ' . $game_id;
            $message_type = 'warning';
        }
    }
}

// Display alert message if set
if(!empty($message)) {
    show_alert($message, $message_type);
}
?>

<!-- TinyMCE from CDN -->
<script src="https://api.cloudarcade.net/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>

<div class="section">
    <div class="bs-callout bs-callout-info">
        <h4>Game Description Editor</h4>
        <p>Edit game descriptions and instructions with a powerful rich text editor.</p>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <!-- Game Search Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Select Game to Edit</h5>
                </div>
                <div class="card-body">
                    <form method="post" id="game-select-form">
                        <input type="hidden" name="action" value="fetch_game">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <div class="mb-0">
                                    <label for="game_id" class="form-label">Game ID</label>
                                    <input type="number" class="form-control" id="game_id" name="game_id" value="<?php echo $game_id; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-0">
                                    <button type="submit" class="btn btn-primary">Load Game</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($game): ?>
    <!-- Game Editor -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Editing: <?php echo htmlspecialchars($game->title); ?> (ID: <?php echo $game->id; ?>)</h5>
                    <div>
                        <a href="<?php echo get_permalink('game', $game->slug); ?>" target="_blank" class="btn btn-sm btn-outline-success me-2">
                            <i class="fas fa-eye"></i> Preview Game
                        </a>
                        <a href="dashboard.php?viewpage=gamelist&slug=edit&id=<?php echo $game->id; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-edit"></i> Open in Game Editor
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" id="game-editor-form">
                        <input type="hidden" name="action" value="update_game">
                        <input type="hidden" name="game_id" value="<?php echo $game->id; ?>">
                        
                        <!-- Description Editor -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Game Description</label>
                            <textarea class="form-control tinymce-editor" id="description" name="description" rows="6"><?php echo htmlspecialchars($game->description); ?></textarea>
                        </div>
                        
                        <!-- Instructions Editor -->
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Game Instructions</label>
                            <textarea class="form-control tinymce-editor" id="instructions" name="instructions" rows="6"><?php echo htmlspecialchars($game->instructions); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '.tinymce-editor',
        height: 360,
        menubar: false,
        plugins: 'link lists image table code forecolor backcolor',
        toolbar: 'undo redo | blocks | fontsize | bold italic underline strikethrough link image | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | code',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
        entity_encoding: 'raw',
        convert_urls: false,
        relative_urls: false
    });
});
</script>