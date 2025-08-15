# Inventory Management CRUD App

This is a sleek and modern full-stack CRUD (Create, Read, Update, Delete) application for managing an inventory of items. The project features a dynamic, single-page interface built with vanilla JavaScript and a powerful PHP backend that interacts with an SQLite database. The user interface is styled with a professional "glassmorphism" design, providing a clean and intuitive user experience.

 <!-- Placeholder: Replace with a real screenshot URL -->

## ✨ Features

- **Full CRUD Functionality:** Create, read, update, and delete inventory items seamlessly.
- **Modern Glassmorphism UI:** A beautiful, responsive interface with blurred, semi-transparent elements.
- **Single-Page Experience:** The UI updates dynamically without page reloads, thanks to AJAX (Fetch API).
- **Interactive Modals:** Confirmation modals for delete and delete-all actions to prevent accidental data loss.
- **Undo Deletions:** Users can instantly undo both single and bulk deletions via a temporary notification.
- **Real-time Notifications:** Professional, non-intrusive notifications for all actions (item added, deleted, restored, etc.).
- **Keyboard Navigation:** The item collection can be scrolled using the up and down arrow keys for improved accessibility.
- **Clean & Efficient Backend:** A secure and well-structured PHP and SQLite backend handles all data operations.

## 🛠️ Tech Stack

- **Frontend:**
  - HTML5
  - CSS3 (with Custom Properties for theming)
  - Vanilla JavaScript (ES6+)
- **Backend:**
  - PHP
  - SQLite (via PDO for database interaction)
- **Development Environment:**
  - MAMP, WAMP, XAMPP, or any local server with PHP support.

## 🚀 Getting Started

Follow these instructions to get the project up and running on your local machine.

### Prerequisites

- A local server environment (e.g., [MAMP](https://www.mamp.info/en/downloads/), [XAMPP](https://www.apachefriends.org/index.html)) with PHP and SQLite enabled.

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/MahnoorPortfolio/Inventory_Management_System.git
   ```

2. **Navigate to your server's web directory:**
   - Move the project folder into your server's document root (e.g., `htdocs` for MAMP/XAMPP).

3. **Database Setup:**
   - The application is designed to automatically create the `database.sqlite` file and the `items` table the first time it runs. No manual database setup is required.

4. **Run the application:**
   - Start your MAMP/XAMPP server.
   - Open your web browser and navigate to the project's URL (e.g., `http://localhost/DEN_FOURTH_TASK`).

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
