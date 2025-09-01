<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM cezar_friends WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Friend deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete friend.']);
        }
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $number = $_POST['number'];
        $url = $_POST['url'];

        // Basic validation
        if (empty($name) || empty($email) || empty($number)) {
            echo json_encode(['success' => false, 'message' => 'Name, email, and number are required.']);
            exit;
        }

        $stmt = $db->prepare("UPDATE cezar_friends SET name = :name, email = :email, number = :number, url = :url WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':number', $number, SQLITE3_TEXT);
        $stmt->bindValue(':url', $url, SQLITE3_TEXT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Friend updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update friend.']);
        }
        exit;
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $url = $_POST['url'];

    // Basic validation
    if (empty($name) || empty($email) || empty($number)) {
        echo json_encode(['success' => false, 'message' => 'Name, email, and number are required.']);
        exit;
    }

    // E.164 format validation for number (+639XXXXXXXXX) and length
    if (!preg_match('/^\+[1-9]\d{1,14}$/', $number) || strlen($number) < 2 || strlen($number) > 16) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number format or length. Please use E.164 format (e.g., +6391234567890) and ensure it is between 2 and 16 characters long.']);
        exit;
    }

    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    // URL format validation (if provided)
    if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid URL format.']);
        exit;
    }

    // Check for duplicate email or number
    $sql = "SELECT COUNT(*) FROM cezar_friends WHERE (email = :email OR number = :number)";
    if (isset($id)) { // If it's an update operation, exclude the current friend
        $sql .= " AND id != :id";
    }
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':number', $number, SQLITE3_TEXT);
    if (isset($id)) {
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    }
    $result = $stmt->execute();
    $count = $result->fetchArray()[0];

    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Duplicate email or number detected.']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO cezar_friends (name, email, number, url) VALUES (:name, :email, :number, :url)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':number', $number, SQLITE3_TEXT);
    $stmt->bindValue(':url', $url, SQLITE3_TEXT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Friend added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add friend.']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $db->prepare("SELECT * FROM cezar_friends WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $friend = $result->fetchArray(SQLITE3_ASSOC);
        echo json_encode($friend);
    } else if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $stmt = $db->prepare("SELECT * FROM cezar_friends WHERE name LIKE :search OR email LIKE :search OR number LIKE :search OR url LIKE :search");
        $stmt->bindValue(':search', '%' . $search . '%', SQLITE3_TEXT);
        $result = $stmt->execute();
        $friends = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $friends[] = $row;
        }
        echo json_encode($friends);
    } else {
        $result = $db->query("SELECT * FROM cezar_friends");
        $friends = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $friends[] = $row;
        }
        echo json_encode($friends);
    }
}

?>
