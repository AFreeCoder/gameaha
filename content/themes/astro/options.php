<?php
if (!has_admin_access()) {
    die('Error 12');
}

// Path to the JSON file
$json_file = ABSPATH . TEMPLATE_PATH . '/includes/category-icons.json';

// Handle form submission
if (isset($_POST['action']) && $_POST['action'] == 'update-category-icons') {
    $icons_data = [];
    
    foreach ($_POST['icon'] as $index => $icon) {
        if (!empty($icon) && !empty($_POST['slugs'][$index])) {
            // Split slugs by comma and trim whitespace
            $slugs = array_map('trim', explode(',', $_POST['slugs'][$index]));
            $icons_data[$icon] = array_filter($slugs); // Remove empty values
        }
    }
    
    // Save to JSON file
    file_put_contents($json_file, json_encode($icons_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    show_alert('Category icons updated successfully!', 'success');
}

// Read current mappings
$category_icons = json_decode(file_get_contents($json_file), true) ?: [];

// Handle form submission
if (isset($_POST['action']) && $_POST['action'] == 'update-hero-content') {
    $hero_fields = ['title', 'highlight', 'description'];
    
    foreach ($hero_fields as $field) {
        if (isset($_POST['hero_' . $field])) {
            set_theme_pref(THEME_NAME, 'hero_' . $field, $_POST['hero_' . $field]);
        }
    }
    show_alert('Hero content updated successfully!', 'success');
}

// Get current values
$hero_title = get_theme_pref(THEME_NAME, 'hero_title') ?: 'Play Instant';
$hero_highlight = get_theme_pref(THEME_NAME, 'hero_highlight') ?: 'Browser Games';
$hero_description = get_theme_pref(THEME_NAME, 'hero_description') ?: 'Discover and play thousands of free online games instantly. No downloads required.';
?>

<div class="mb-4">
    <h4><?php _e('Hero Section Settings') ?></h4>
    <p class="text-muted"><?php _e('Customize your homepage hero section content.') ?></p>
</div>

<form method="post" class="needs-validation" novalidate>
    <input type="hidden" name="action" value="update-hero-content">
    
    <!-- Hero Title -->
    <div class="mb-3">
        <label for="hero_title" class="form-label"><?php _e('Main Title') ?></label>
        <input type="text" 
               class="form-control"
               id="hero_title"
               name="hero_title"
               value="<?php echo htmlspecialchars($hero_title) ?>"
               required>
        <div class="form-text"><?php _e('The main title text before the highlight (e.g., "Play Instant")') ?></div>
    </div>

    <!-- Hero Highlight -->
    <div class="mb-3">
        <label for="hero_highlight" class="form-label"><?php _e('Highlighted Text') ?></label>
        <input type="text"
               class="form-control"
               id="hero_highlight"
               name="hero_highlight"
               value="<?php echo htmlspecialchars($hero_highlight) ?>"
               required>
        <div class="form-text"><?php _e('The highlighted text that appears with gradient color (e.g., "Browser Games")') ?></div>
    </div>

    <!-- Hero Description -->
    <div class="mb-3">
        <label for="hero_description" class="form-label"><?php _e('Description') ?></label>
        <textarea class="form-control"
                  id="hero_description"
                  name="hero_description"
                  rows="3"
                  required><?php echo htmlspecialchars($hero_description) ?></textarea>
        <div class="form-text"><?php _e('A brief description that appears below the title') ?></div>
    </div>

    <!-- Preview -->
    <div class="mb-4 p-4 bg-light rounded">
        <h5 class="mb-3"><?php _e('Preview') ?></h5>
        <div class="preview-area">
            <h2 class="h4">
                <span class="preview-title"></span>
                <span class="preview-highlight text-primary"></span>
            </h2>
            <p class="preview-description text-muted small"></p>
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><?php _e('Save Changes') ?></button>
</form>

<hr class="mt-4">

<div class="mb-4 mt-4">
    <h4><?php _e('Category Icons Settings') ?></h4>
    <p class="text-muted"><?php _e('Map category slugs to icons. Multiple slugs can share the same icon.') ?></p>
</div>

<form method="post" id="category-icons-form">
    <input type="hidden" name="action" value="update-category-icons">
    
    <div class="mb-3">
        <div class="row mb-2">
            <div class="col-2">
                <label class="form-label"><?php _e('Icon') ?></label>
            </div>
            <div class="col-10">
                <label class="form-label"><?php _e('Category Slugs (comma-separated)') ?></label>
            </div>
        </div>
        
        <div id="icon-mappings">
            <?php foreach ($category_icons as $icon => $slugs): ?>
            <div class="row mb-2 mapping-row">
                <div class="col-2">
                    <input type="text" 
                           class="form-control"
                           name="icon[]" 
                           value="<?php echo htmlspecialchars($icon) ?>"
                           placeholder="🎮">
                </div>
                <div class="col-9">
                    <input type="text" 
                           class="form-control"
                           name="slugs[]" 
                           value="<?php echo htmlspecialchars(implode(', ', $slugs)) ?>"
                           placeholder="slug1, slug2, slug3">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger remove-row">×</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-3">
            <button type="button" class="btn btn-secondary" id="add-mapping">
                <?php _e('Add New Mapping') ?>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><?php _e('Save Changes') ?></button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live preview functionality
    function updatePreview() {
        document.querySelector('.preview-title').textContent = document.getElementById('hero_title').value + ' ';
        document.querySelector('.preview-highlight').textContent = document.getElementById('hero_highlight').value;
        document.querySelector('.preview-description').textContent = document.getElementById('hero_description').value;
    }

    // Add input event listeners
    document.getElementById('hero_title').addEventListener('input', updatePreview);
    document.getElementById('hero_highlight').addEventListener('input', updatePreview);
    document.getElementById('hero_description').addEventListener('input', updatePreview);

    // Initial preview
    updatePreview();

    // Form validation
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
	//
	const container = document.getElementById('icon-mappings');
    
    // Add new mapping row
    document.getElementById('add-mapping').addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'row mb-2 mapping-row';
        row.innerHTML = `
            <div class="col-2">
                <input type="text" class="form-control" name="icon[]" placeholder="🎮">
            </div>
            <div class="col-9">
                <input type="text" class="form-control" name="slugs[]" placeholder="slug1, slug2, slug3">
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-danger remove-row">×</button>
            </div>
        `;
        container.appendChild(row);
    });
    
    // Remove mapping row
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.mapping-row').remove();
        }
    });
    
    // Add initial row if empty
    if (!container.querySelector('.mapping-row')) {
        document.getElementById('add-mapping').click();
    }
});
</script>