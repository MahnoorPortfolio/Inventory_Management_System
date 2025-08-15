<?php
include 'database.php';

// Read - Initial data load
$stmt = $pdo->query('SELECT * FROM items ORDER BY id DESC');
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="header">
        <div class="header-content">
            <h1>Inventory Management</h1>

        </div>
    </header>

    <div class="container">
        <div id="notification-container"></div>
        <div class="card">
            <h2>Add New Item</h2>
            <form id="add-item-form" class="form-container">
                <input type="text" name="name" placeholder="Item Name" required>
                <textarea name="description" placeholder="Item Description" required></textarea>
                <button type="submit" name="create" class="btn btn-primary">Add Item</button>
            </form>
        </div>

        <div class="card">
            <div class="collection-header">
                <h2>Item Collection</h2>
                <button id="delete-all-btn" class="btn btn-danger">Delete All</button>
            </div>
            <div class="item-grid">
                <?php foreach ($items as $item): ?>
                    <div class="item-card" data-id="<?= $item['id'] ?>">
                        <div class="item-card-content">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p><?= htmlspecialchars($item['description']) ?></p>
                        </div>
                        <div class="item-card-actions">
                            <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-action btn-edit">Edit</a>
                            <button class="btn btn-danger delete-btn" data-id="<?= $item['id'] ?>">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Inventory Management. All Rights Reserved.</p>
    </footer>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal-overlay" style="display: none;">
        <div class="modal-card">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to permanently delete this item?</p>
            <div class="modal-actions">
                <button class="btn cancel-btn">Cancel</button>
                <button id="confirm-delete-one" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>

    <!-- Delete All Confirmation Modal -->
    <div id="delete-all-modal" class="modal-overlay" style="display: none;">
        <div class="modal-card">
            <h3>DELETE ALL ITEMS?</h3>
            <p>This action is irreversible. All items will be permanently deleted.</p>
            <div class="modal-actions">
                <button class="btn cancel-btn">Cancel</button>
                <button id="confirm-delete-all" class="btn btn-danger">Yes, Delete All</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const addItemForm = document.getElementById('add-item-form');
        const itemGrid = document.querySelector('.item-grid');
        const deleteModal = document.getElementById('delete-modal');
        const deleteAllModal = document.getElementById('delete-all-modal');
        let itemToDeleteId = null;

        // --- Show Notification --- //
        function showNotification(message, type = 'success', undoCallback = null) {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
                        const icon = type === 'success' ? '✓' : '✗';
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-icon">${icon}</span>
                    <span>${message}</span>
                </div>
            `;

            // Trigger the animation
            setTimeout(() => notification.classList.add('show'), 10);
            if (undoCallback) {
                const undoBtn = document.createElement('button');
                undoBtn.className = 'btn-undo';
                undoBtn.textContent = 'Undo';
                undoBtn.onclick = () => {
                    undoCallback();
                    notification.remove();
                };
                notification.appendChild(undoBtn);
            }
            container.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // --- Create Item Card HTML --- //
        function createItemCard(item) {
            return `
                <div class="item-card" data-id="${item.id}">
                    <div class="item-card-content">
                        <h3>${item.name}</h3>
                        <p>${item.description}</p>
                    </div>
                    <div class="item-card-actions">
                        <a href="edit.php?id=${item.id}" class="btn btn-action btn-edit">Edit</a>
                        <button class="btn btn-danger delete-btn" data-id="${item.id}">Delete</button>
                    </div>
                </div>`;
        }

        // --- Handle Add Item --- //
        addItemForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(addItemForm);
            formData.append('action', 'create');
            const response = await fetch('api.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showNotification(result.message);
                const newItem = result.item;
                const itemCardHTML = createItemCard(newItem);
                itemGrid.insertAdjacentHTML('afterbegin', itemCardHTML);
                addItemForm.reset();
            } else {
                showNotification(result.message, 'danger');
            }
        });

        // --- Handle All Click Events on the Page using Event Delegation --- //
        document.body.addEventListener('click', async (e) => {

            const target = e.target;

            // --- Open Single Delete Modal --- //
            if (target.classList.contains('delete-btn')) {
                itemToDeleteId = target.dataset.id;
                deleteModal.style.display = 'flex';
            }
            // --- Confirm Single Delete --- //
            else if (target.id === 'confirm-delete-one') {
                if (!itemToDeleteId) return;
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', itemToDeleteId);
                const response = await fetch('api.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.success) {
                    const deletedItem = result.deleted;
                    const undo = () => {
                        const formData = new FormData();
                        formData.append('action', 'recreate_items');
                        formData.append('items', JSON.stringify([deletedItem]));
                        fetch('api.php', { method: 'POST', body: formData })
                            .then(res => res.json())
                            .then(recreateResult => {
                                if (recreateResult.success) {
                                    const newItem = recreateResult.items[0];
                                    const itemCard = createItemCard(newItem);
                                    itemGrid.insertAdjacentHTML('afterbegin', itemCard);
                                    showNotification('Item restored!');
                                }
                            });
                    };
                    showNotification(result.message, 'success', undo);

                    const cardToDelete = document.querySelector(`.item-card[data-id='${itemToDeleteId}']`);
                    if (cardToDelete) {
                        cardToDelete.style.transition = 'opacity 0.3s ease';
                        cardToDelete.style.opacity = '0';
                        setTimeout(() => cardToDelete.remove(), 300);
                    }
                } else {
                    showNotification(result.message, 'danger');
                }
                deleteModal.style.display = 'none';
                itemToDeleteId = null;
            }
            // --- Open Delete All Modal --- //
            else if (target.id === 'delete-all-btn') {
                deleteAllModal.style.display = 'flex';
            }
            // --- Confirm Delete All --- //
            else if (target.id === 'confirm-delete-all') {
                const formData = new FormData();
                formData.append('action', 'delete_all');
                const response = await fetch('api.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.success) {
                    const deletedItems = result.deleted;
                    const undo = () => {
                        const formData = new FormData();
                        formData.append('action', 'recreate_items');
                        formData.append('items', JSON.stringify(deletedItems));
                        fetch('api.php', { method: 'POST', body: formData })
                            .then(res => res.json())
                            .then(recreateResult => {
                                if (recreateResult.success) {
                                    recreateResult.items.forEach(item => {
                                        const itemCard = createItemCard(item);
                                        itemGrid.insertAdjacentHTML('beforeend', itemCard);
                                    });
                                    showNotification('Items restored!');
                                }
                            });
                    };
                    showNotification(result.message, 'success', undo);

                    const itemCards = itemGrid.querySelectorAll('.item-card');
                    itemCards.forEach(card => {
                        card.style.transition = 'opacity 0.3s ease';
                        card.style.opacity = '0';
                        setTimeout(() => card.remove(), 300);
                    });
                } else {
                    showNotification(result.message, 'danger');
                }
                deleteAllModal.style.display = 'none';
            }
            // --- Close Modals --- //
            else if (target.classList.contains('cancel-btn') || target.classList.contains('modal-overlay')) {
                deleteModal.style.display = 'none';
                deleteAllModal.style.display = 'none';
                itemToDeleteId = null;
            }
        });
    });
    </script>
</body>
</html>
