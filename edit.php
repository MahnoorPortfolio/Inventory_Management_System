<?php
session_start();
include 'database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Handle form submission for update
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare('UPDATE items SET name = ?, description = ? WHERE id = ?');
    if ($stmt->execute([$name, $description, $id])) {
        $_SESSION['message'] = 'Item updated successfully!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error: Could not update item.';
        $_SESSION['message_type'] = 'danger';
    }

    header('Location: index.php');
    exit;
}

// Fetch the item to edit
$stmt = $pdo->prepare('SELECT * FROM items WHERE id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="header">
        <div class="header-content">
            <h1>Edit Item</h1>
            <nav class="navigation">
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>Modify Item Details</h2>
            <form action="edit.php?id=<?= htmlspecialchars($item['id']) ?>" method="post" class="form-container">
                <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
                <textarea name="description" required><?= htmlspecialchars($item['description']) ?></textarea>
                <button type="submit" name="update" class="btn btn-primary">Update Item</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Inventory Management. All Rights Reserved.</p>
    </footer>
</body>
</html>
