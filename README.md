# AUSTbook - A Simple Social Media App

AUSTbook is a dynamic web application built with PHP and hosted on an AWS EC2 instance. It serves as a practical example of a full-stack project, demonstrating user authentication, content management, and administrative roles. The front-end is designed with the Bootstrap 5 framework for a clean and responsive user experience.

## Features

-   **User Authentication:** Secure user sign-up and login system with password hashing.
-   **Post Management (CRUD):** Logged-in users can create, read, update, and delete their own posts.
-   **Dynamic Feed:** A central feed that displays all posts from all users in chronological order.
-   **User Profiles:** A dedicated profile page for each user, displaying their information and a list of their posts.
-   **Admin Role:** A special admin user who can view all posts on the feed and has the authority to delete any post.
-   **Responsive Design:** Utilizes Bootstrap 5 for a layout that works on desktops, tablets, and mobile devices.

## Technology Stack

-   **Cloud Provider:** Amazon Web Services (AWS)
-   **Virtual Server:** EC2 (Elastic Compute Cloud)
-   **Operating System:** Amazon Linux 2
-   **Web Server:** Apache (`httpd`)
-   **Backend Language:** PHP
-   **Database:** SQLite (a simple, file-based SQL database)
-   **Frontend Framework:** Bootstrap 5

## Setup and Installation

To deploy this project on your own EC2 instance, follow these steps:

1.  **Launch an EC2 Instance:**
    -   Use an Amazon Linux 2 AMI.
    -   Choose a `t3.micro` instance (Free Tier eligible).
    -   Configure a security group to allow inbound traffic on **HTTP (port 80)** and **SSH (port 22)**.

2.  **Install Software:**
    -   Connect to your instance via SSH.
    -   Run the following commands to install the web server, PHP, and the SQLite extension:
        ```bash
        sudo yum update -y
        sudo yum install -y httpd php php-sqlite3
        sudo systemctl start httpd
        sudo systemctl enable httpd
        ```

3.  **Deploy Application Files:**
    -   Clone this repository or copy all the `.php` files into the web root directory: `/var/www/html/`.

4.  **Set Permissions:**
    -   The web server needs permission to write to the directory to create the database file. Run this command:
        ```bash
        sudo chown -R apache:apache /var/www/html
        ```

5.  **Create an Admin User:**
    -   To create your first admin account, you'll need to temporarily create a script. Make a file named `create_admin.php` in `/var/www/html/` with the following content (set your own secure password):
        ```php
        <?php
        require 'db.php';
        $admin_username = 'admin';
        $admin_password = 'password123';

        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 1)");
        $stmt->execute([$admin_username, $hashed_password]);
        echo "Admin user created. Please delete this file immediately.";
        ?>
        ```
    -   Visit `http://http://65.0.27.104//create_admin.php` in your browser to run the script.
    -   **CRITICAL:** After creating the admin, delete the file from your server: `sudo rm /var/www/html/create_admin.php`.

6.  **Access Your Site:**
    -   You can now access your AUSTbook application by navigating to your EC2 instance's public IP address: `http://http://65.0.27.104//signup.php`.