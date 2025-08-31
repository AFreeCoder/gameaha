<?php
/**
 * AJAX Handler for Game Description Editor plugin
 */

if(!has_admin_access()){
    exit('Forbidden');
}

session_start();

// Required configuration and initialization files for the CMS
require_once('../../../config.php');
require_once('../../../init.php');
require_once('../../../includes/commons.php');

// Get the plugin slug
$plugin_slug = basename(dirname(__FILE__));

// Process AJAX requests
if (isset($_POST['action'])) {
    // Quick game lookup by title for autocomplete
    if ($_POST['action'] == 'search_games' && !empty($_POST['query'])) {
        $query = trim($_POST['query']);
        $conn = open_connection();
        
        // Search games by title or ID
        $sql = "SELECT id, title FROM games WHERE title LIKE :query OR id = :id ORDER BY id DESC LIMIT 10";
        $st = $conn->prepare($sql);
        $st->bindValue(":query", '%' . $query . '%', PDO::PARAM_STR);
        $st->bindValue(":id", is_numeric($query) ? $query : 0, PDO::PARAM_INT);
        $st->execute();
        
        $games = $st->fetchAll(PDO::FETCH_ASSOC);
        
        // Return results as JSON
        header('Content-Type: application/json');
        echo json_encode($games);
        exit;
    }
    
    // Get game details by ID
    if ($_POST['action'] == 'get_game_details' && !empty($_POST['game_id'])) {
        $game_id = intval($_POST['game_id']);
        $game = Game::getById($game_id);
        
        if ($game) {
            $result = [
                'success' => true,
                'id' => $game->id,
                'title' => $game->title,
                'description' => $game->description,
                'instructions' => $game->instructions
            ];
        } else {
            $result = [
                'success' => false,
                'message' => 'Game not found'
            ];
        }
        
        // Return results as JSON
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    
    // Quick save game content (without page reload)
    if ($_POST['action'] == 'quick_save' && !empty($_POST['game_id'])) {
        $game_id = intval($_POST['game_id']);
        $game = Game::getById($game_id);
        
        if ($game) {
            // Update only the fields that were provided
            if (isset($_POST['description'])) {
                $game->description = $_POST['description'];
            }
            
            if (isset($_POST['instructions'])) {
                $game->instructions = $_POST['instructions'];
            }
            
            // Save changes
            $game->update();
            
            $result = [
                'success' => true,
                'message' => 'Game updated successfully'
            ];
        } else {
            $result = [
                'success' => false,
                'message' => 'Game not found'
            ];
        }
        
        // Return results as JSON
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}

// If no valid action or not an AJAX request
echo 'Invalid request';
exit;
?>