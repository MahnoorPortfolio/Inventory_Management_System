<?php
include 'database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request.'];

if (isset($_POST['action'])) {
    if ($_POST['action'] === 'create' && isset($_POST['name'], $_POST['description'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];

        $stmt = $pdo->prepare('INSERT INTO items (name, description) VALUES (?, ?)');
        if ($stmt->execute([$name, $description])) {
            $newItemId = $pdo->lastInsertId();
            $response = ['success' => true, 'message' => 'Item added successfully!', 'item' => ['id' => $newItemId, 'name' => $name, 'description' => $description]];
        } else {
            $response['message'] = 'Error: Could not add item.';
        }
    } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt_fetch = $pdo->prepare('SELECT * FROM items WHERE id = ?');
        $stmt_fetch->execute([$id]);
        $item = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $stmt_delete = $pdo->prepare('DELETE FROM items WHERE id = ?');
            if ($stmt_delete->execute([$id])) {
                $response = ['success' => true, 'message' => 'Item deleted successfully!', 'deleted' => $item];
            } else {
                $response['message'] = 'Error: Could not delete item.';
            }
        } else {
            $response['message'] = 'Error: Item not found.';
        }
    } elseif ($_POST['action'] === 'delete_all') {
        $stmt_fetch = $pdo->query('SELECT * FROM items');
        $items = $stmt_fetch->fetchAll(PDO::FETCH_ASSOC);

        $stmt_delete = $pdo->query('DELETE FROM items');
        if ($stmt_delete) {
            $response = ['success' => true, 'message' => 'All items have been deleted.', 'deleted' => $items];
        } else {
            $response['message'] = 'Error: Could not delete all items.';
        }
    } elseif ($_POST['action'] === 'recreate_items' && isset($_POST['items'])) {
        $items = json_decode($_POST['items'], true);
        $newItems = [];
        $success = true;

        foreach ($items as $item) {
            $stmt = $pdo->prepare('INSERT INTO items (name, description) VALUES (?, ?)');
            if ($stmt->execute([$item['name'], $item['description']])) {
                $newItemId = $pdo->lastInsertId();
                $newItems[] = ['id' => $newItemId, 'name' => $item['name'], 'description' => $item['description']];
            } else {
                $success = false;
            }
        }

        if ($success) {
            $response = ['success' => true, 'message' => 'Items restored!', 'items' => $newItems];
        } else {
            $response['message'] = 'Error: Could not restore all items.';
        }
    }
}

echo json_encode($response);
exit;
