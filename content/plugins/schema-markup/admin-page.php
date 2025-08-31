<?php

// Dynamically set the plugin slug by getting the plugin folder name
$plugin_slug = basename(dirname(__FILE__)); // 'sample-plugin'

// Handle form submission based on 'action'
if (isset($_POST['action'])) {

    if ($_POST['action'] == 'author') {
        // Save author preference
        $author_enabled = isset($_POST['author_enabled']) ? 'true' : 'false';
        set_plugin_pref($plugin_slug, 'author_enabled', $author_enabled);
        $author_name = $_POST['author_name'] ?? '';
        set_plugin_pref($plugin_slug, 'author_name', $author_name);
        $author_type = $_POST['author_type'] ?? 'Person';
        set_plugin_pref($plugin_slug, 'author_type', $author_type);
        show_alert('Author settings saved!', 'success');
    }
    
    if ($_POST['action'] == 'publisher') {
        // Save publisher preference
        $publisher_enabled = isset($_POST['publisher_enabled']) ? 'true' : 'false';
        set_plugin_pref($plugin_slug, 'publisher_enabled', $publisher_enabled);
        $publisher_name = $_POST['publisher_name'] ?? '';
        set_plugin_pref($plugin_slug, 'publisher_name', $publisher_name);
        show_alert('Publisher settings saved!', 'success');
    }
    
    if ($_POST['action'] == 'offers') {
        // Save offers preference (only to show or hide the offers section, game is always free)
        $offers_enabled = isset($_POST['offers_enabled']) ? 'true' : 'false';
        set_plugin_pref($plugin_slug, 'offers_enabled', $offers_enabled);
        show_alert('Offers settings saved!', 'success');
    }
}
?>

<div class="section">
    <div class="bs-callout bs-callout-info">
        Schema Markup Plugin: Configure author, publisher, and offers settings for your schema markup.
    </div>
    <p><a href="https://www.semrush.com/blog/schema-markup/" target="_blank">What is Schema Markup ?</a></p>
    <!-- Author Configuration -->
    <h4>Author Settings</h4>
    <form method="post">
        <input type="hidden" name="action" value="author">
        <?php
        $author_enabled = get_plugin_pref_bool($plugin_slug, 'author_enabled', false);
        $author_name = get_plugin_pref($plugin_slug, 'author_name', '');
        $author_type = get_plugin_pref($plugin_slug, 'author_type', 'Person');
        ?>
        <div class="form-check">
            <input type="checkbox" name="author_enabled" class="form-check-input" id="author_enabled" <?php echo $author_enabled ? 'checked' : ''; ?>>
            <label class="form-check-label" for="author_enabled">Show Author</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Author Name:</label>
            <input type="text" class="form-control" name="author_name" value="<?php echo htmlspecialchars($author_name); ?>">
            <small class="form-text text-muted">Current value: <?php echo !empty($author_name) ? htmlspecialchars($author_name) : 'No author name set'; ?></small>
        </div>
        <div class="mb-3">
            <label class="form-label">Author Type:</label>
            <select class="form-select" name="author_type">
                <option value="Person" <?php echo $author_type == 'Person' ? 'selected' : ''; ?>>Person</option>
                <option value="Organization" <?php echo $author_type == 'Organization' ? 'selected' : ''; ?>>Organization</option>
            </select>
            <small class="form-text text-muted">Current type: <?php echo htmlspecialchars($author_type); ?></small>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
    <hr>

    <!-- Publisher Configuration -->
    <h4>Publisher Settings</h4>
    <form method="post">
        <input type="hidden" name="action" value="publisher">
        <?php
        $publisher_enabled = get_plugin_pref_bool($plugin_slug, 'publisher_enabled', false);
        $publisher_name = get_plugin_pref($plugin_slug, 'publisher_name', '');
        ?>
        <div class="form-check">
            <input type="checkbox" name="publisher_enabled" class="form-check-input" id="publisher_enabled" <?php echo $publisher_enabled ? 'checked' : ''; ?>>
            <label class="form-check-label" for="publisher_enabled">Show Publisher</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Publisher Name:</label>
            <input type="text" class="form-control" name="publisher_name" value="<?php echo htmlspecialchars($publisher_name); ?>">
            <small class="form-text text-muted">Current value: <?php echo !empty($publisher_name) ? htmlspecialchars($publisher_name) : 'No publisher name set'; ?></small>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
    <hr>

    <!-- Offers Configuration -->
    <h4>Offers Settings (Game is Free)</h4>
    <form method="post">
        <input type="hidden" name="action" value="offers">
        <?php
        $offers_enabled = get_plugin_pref_bool($plugin_slug, 'offers_enabled', true);
        ?>
        <div class="form-check">
            <input type="checkbox" name="offers_enabled" class="form-check-input" id="offers_enabled" <?php echo $offers_enabled ? 'checked' : ''; ?>>
            <label class="form-check-label" for="offers_enabled">Show Offers (Game is always free)</label>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
    <hr>
</div>