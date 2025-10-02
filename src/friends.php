<?php
// src/friends.php

/**
 * Reads friends from the database, optionally filtering by a search term.
 *
 * @param PDO $pdo The database connection object.
 * @param string $search The search term to filter by (optional).
 * @return array An array of friend records.
 */
function readFriends(PDO $pdo, string $search = ''): array {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM barangan_friends WHERE name LIKE :search OR email LIKE :search OR phone LIKE :search OR url LIKE :search ORDER BY name ASC");
        $stmt->execute([':search' => '%' . $search . '%']);
    } else {
        $stmt = $pdo->query("SELECT * FROM barangan_friends ORDER BY name ASC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Validates and normalizes friend data.
 *
 * @param array $data The raw friend data from the form.
 * @return array An array containing the sanitized data or an error message.
 */
function validateAndNormalize(array $data): array {
    $name = trim($data['name'] ?? '');
    $email = strtolower(trim($data['email'] ?? ''));
    $phone = preg_replace('/\D/', '', $data['phone'] ?? ''); // Digits only
    $url = strtolower(trim($data['url'] ?? ''));

    if (empty($name)) {
        return ['error' => 'Name is required.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'Invalid email format.'];
    }
    if (!empty($url)) {
        if (strpos($url, 'http://') === 0) {
            return ['error' => 'HTTP URLs are not allowed; use HTTPS.'];
        }
        if (strpos($url, 'https://') !== 0) {
            $url = 'https://' . $url;
        }
    }

    return [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'url' => $url,
        'id' => isset($data['id']) ? (int)$data['id'] : null
    ];
}

/**
 * Creates a new friend in the database.
 *
 * @param PDO $pdo The database connection object.
 * @param array $data The friend data.
 * @return string JSON response.
 */
function createFriend(PDO $pdo, array $data): string {
    $sanitizedData = validateAndNormalize($data);
    if (isset($sanitizedData['error'])) {
        return json_encode($sanitizedData);
    }

    $sql = "INSERT INTO barangan_friends (name, email, phone, url) VALUES (:name, :email, :phone, :url)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $sanitizedData['name'],
            ':email' => $sanitizedData['email'],
            ':phone' => $sanitizedData['phone'],
            ':url' => $sanitizedData['url']
        ]);
        $id = $pdo->lastInsertId();
        $friend = $pdo->query("SELECT * FROM barangan_friends WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
        return json_encode(['success' => 'Friend added successfully.', 'friend' => $friend]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Integrity constraint violation (UNIQUE)
            return json_encode(['error' => 'Email or phone number already exists.']);
        }
        return json_encode(['error' => 'Database error occurred.']);
    }
}

/**
 * Updates an existing friend in the database.
 *
 * @param PDO $pdo The database connection object.
 * @param array $data The friend data including the ID.
 * @return string JSON response.
 */
function updateFriend(PDO $pdo, array $data): string {
    $sanitizedData = validateAndNormalize($data);
    if (isset($sanitizedData['error'])) {
        return json_encode($sanitizedData);
    }
    if (empty($sanitizedData['id'])) {
        return json_encode(['error' => 'Friend ID is missing.']);
    }

    $sql = "UPDATE barangan_friends SET name = :name, email = :email, phone = :phone, url = :url WHERE id = :id";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $sanitizedData['name'],
            ':email' => $sanitizedData['email'],
            ':phone' => $sanitizedData['phone'],
            ':url' => $sanitizedData['url'],
            ':id' => $sanitizedData['id']
        ]);
        $friend = $pdo->query("SELECT * FROM barangan_friends WHERE id = {$sanitizedData['id']}")->fetch(PDO::FETCH_ASSOC);
        return json_encode(['success' => 'Friend updated successfully.', 'friend' => $friend]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            return json_encode(['error' => 'Email or phone number already exists.']);
        }
        return json_encode(['error' => 'Database error occurred.']);
    }
}

/**
 * Deletes a friend from the database.
 *
 * @param PDO $pdo The database connection object.
 * @param int $id The ID of the friend to delete.
 * @return string JSON response.
 */
function deleteFriend(PDO $pdo, int $id): string {
    if (empty($id)) {
        return json_encode(['error' => 'Friend ID is missing.']);
    }
    $sql = "DELETE FROM barangan_friends WHERE id = :id";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return json_encode(['success' => 'Friend deleted successfully.']);
    } catch (PDOException $e) {
        return json_encode(['error' => 'Database error occurred.']);
    }
}
?>