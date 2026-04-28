<div align="center">
  <br />
  <div style="background: rgba(20, 184, 166, 0.1); width: 80px; height: 80px; border-radius: 24px; border: 2px solid rgba(20, 184, 166, 0.3); display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2.5">
      <polygon points="12 2 2 7 12 12 22 7 12 2"/>
      <polyline points="2 17 12 22 22 17"/>
      <polyline points="2 12 12 17 22 12"/>
    </svg>
  </div>

  <h1>CorpPortal</h1>
  <p><strong>A Modern, Premium Role-Based Management Solution</strong></p>

  <p>
    <img src="https://img.shields.io/badge/PHP-8.x-777bb4?style=flat-square&logo=php" alt="PHP" />
    <img src="https://img.shields.io/badge/MySQL-8.0-4479a1?style=flat-square&logo=mysql" alt="MySQL" />
    <img src="https://img.shields.io/badge/UI-Custom_CSS-14b8a6?style=flat-square" alt="Custom UI" />
  </p>
</div>

---

## 🌟 Overview

**CorpPortal** is a high-fidelity, role-based internal portal designed for modern enterprises. It provides a seamless experience for both administrators and employees, featuring a clean, editorial aesthetic built with a focus on usability and performance.

The platform utilizes a **Deep Teal & Warm Coral** design system, leveraging modern CSS features like glassmorphism, dynamic gradients, and a responsive bento-grid layout for data visualization.

## ✨ Key Features

-   **🔐 Secure Authentication**: Integrated login and signup system with Bcrypt password hashing.
-   **🎭 Role-Based Access (RBAC)**: Distinct dashboards and permissions for **Admins** and **Employees**.
-   **📊 Premium Dashboards**:
    -   **Admin**: Overview of user statistics, system activity, and management tools.
    -   **User**: Personal progress, profile summary, and quick access to tools.
-   **👥 User Management**: Full CRUD capabilities for administrators to manage the organization's workforce.
-   **👤 Profile System**: Personalized profile pages with banner customization and detailed meta-information.
-   **📱 Fully Responsive**: Optimized for all devices, from ultra-wide monitors to mobile smartphones.

## 🛠️ Technology Stack

-   **Backend**: PHP 8.x (PDO for secure database interactions)
-   **Database**: MySQL / MariaDB
-   **Frontend**: 
    -   Vanilla HTML5 & Semantic Elements
    -   Custom CSS3 (Glassmorphism, CSS Variables, Flexbox/Grid)
    -   Vanilla JavaScript (ES6+)
-   **Typography**: Plus Jakarta Sans (Google Fonts)

## 🚀 Getting Started

### Prerequisites

-   **XAMPP** or a similar PHP/MySQL local environment.
-   **PHP 8.0+**

### Installation

1.  **Clone the Repository**:
    ```bash
    git clone https://github.com/DivyankBaluni/CorpPortal.git
    cd CorpPortal
    ```

2.  **Database Setup**:
    -   Open PHPMyAdmin.
    -   Create a new database named `role_portal`.
    -   Import the SQL schema from `sql/schema.sql`.

3.  **Configuration**:
    -   Open `config/db.php`.
    -   Ensure the database credentials match your environment:
        ```php
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'role_portal');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_PORT', '3307'); // Default for some XAMPP setups
        ```

4.  **Run the Project**:
    -   Move the project to your `htdocs` folder.
    -   Access via `http://localhost/CorpPortal/`.

### Default Credentials

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@company.com` | `Admin@123` |
| **User** | `jane@company.com` | `Admin@123` |

---

## 🎨 Design Philosophy

CorpPortal follows a "Glass-Editorial" design philosophy:
-   **Contrast**: Deep Teal backgrounds paired with Slate text for maximum readability.
-   **Depth**: Multi-layered shadows and subtle `backdrop-filter` effects.
-   **Fluidity**: Smooth transitions and micro-animations for an interactive feel.

---

<div align="center">
  <p>Built with ❤️ for Modern Organizations</p>
  <span>By Divyank Baluni</span>
</div>
