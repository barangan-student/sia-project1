<?php
session_start();
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/friends.php';

// Live Search Endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search') {
    $searchTerm = $_GET['term'] ?? '';
    $friends = readFriends($pdo, $searchTerm);
    
    if (empty($friends)) {
        echo '<tr><td colspan="5" class="text-center text-muted">No friends found matching your search.</td></tr>';
    } else {
        foreach ($friends as $friend) {
            echo '<tr id="friend-' . e($friend['id']) . '">';
            echo '<td>' . e($friend['name']) . '</td>';
            echo '<td>' . e($friend['email']) . '</td>';
            echo '<td>' . e($friend['phone']) . '</td>';
            echo '<td>';
            if (!empty($friend['url'])) {
                echo '<a href="' . e($friend['url']) . '" target="_blank" rel="noopener noreferrer">' . e($friend['url']) . '</a>';
            } else {
                echo '&mdash;';
            }
            echo '</td>';
            echo '<td class="text-end">';
            echo '<button class="btn btn-sm btn-outline-secondary editBtn" data-id="' . e($friend['id']) . '" data-name="' . e($friend['name']) . '" data-email="' . e($friend['email']) . '" data-phone="' . e($friend['phone']) . '" data-url="' . e($friend['url']) . '">Edit</button>';
            echo ' '; // space between buttons
            echo '<button class="btn btn-sm btn-outline-danger deleteBtn" data-id="' . e($friend['id']) . '" data-name="' . e($friend['name']) . '">Delete</button>';
            echo '</td>';
            echo '</tr>';
        }
    }
    exit;
}


// API-like endpoint for AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            echo createFriend($pdo, $_POST);
            break;
        case 'update':
            echo updateFriend($pdo, $_POST);
            break;
        case 'delete':
            $id = (int)($_POST['id'] ?? 0);
            echo deleteFriend($pdo, $id);
            break;
        default:
            echo json_encode(['error' => 'Invalid action.']);
            break;
    }
    exit;
}

// Initial page load (GET request)
$friends = readFriends($pdo);

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bryan Barangan’s Friends</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Friends List</h1>
            <button class="btn btn-primary" id="addFriendBtn">Add Friend</button>
        </div>

        <div class="input-group mb-4">
            <span class="input-group-text">Search</span>
            <input type="text" id="liveSearchInput" class="form-control" placeholder="Start typing to filter...">
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Website</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($friends)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No friends found. Add one to get started!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($friends as $friend): ?>
                            <tr id="friend-<?= e($friend['id']) ?>">
                                <td><?= e($friend['name']) ?></td>
                                <td><?= e($friend['email']) ?></td>
                                <td><?= e($friend['phone']) ?></td>
                                <td>
                                    <?php if (!empty($friend['url'])): ?>
                                        <a href="<?= e($friend['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($friend['url']) ?></a>
                                    <?php else: ?>
                                        &mdash;
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary editBtn" 
                                            data-id="<?= e($friend['id']) ?>"
                                            data-name="<?= e($friend['name']) ?>"
                                            data-email="<?= e($friend['email']) ?>"
                                            data-phone="<?= e($friend['phone']) ?>"
                                            data-url="<?= e($friend['url']) ?>">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger deleteBtn" 
                                            data-id="<?= e($friend['id']) ?>"
                                            data-name="<?= e($friend['name']) ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Friend Modal -->
    <div class="modal fade" id="friendModal" tabindex="-1" aria-labelledby="friendModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="friendModalLabel">Add Friend</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="friendForm">
                        <input type="hidden" name="id" id="friendId">
                        <input type="hidden" name="action" id="formAction">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label">Website URL (optional)</label>
                            <input type="text" class="form-control" id="url" name="url" placeholder="https://example.com">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="friendForm">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
