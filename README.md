# PHP Blog

A simple PHP/MySQL blog with public posts and comments, plus an admin area to manage posts. Styled with Bootstrap 5.

## Features

- Public site
  - List posts on the home page in reverse chronological order
  - View a single post with its comments
  - Add comments to a post
- Admin authentication
  - First-time admin creation built into the login page (when no admin exists)
  - Secure login/logout using PHP sessions
  - Session helpers (`is_admin_logged_in`, `require_admin`, etc.)
- Admin dashboard
  - View all posts in a table
  - Add new post
  - Edit existing post (title and content)
  - Delete post with confirmation and CSRF token
- Security and robustness
  - Database access uses prepared statements for write operations
  - CSRF protection for delete action
  - `require_once` includes with absolute paths to avoid redeclaration errors
  - Basic output escaping for post and comment content
- UI/UX
  - Bootstrap 5 via CDN (responsive layout, navbar, cards, forms, alerts)
  - Subdirectory-friendly links (works under `/blog`)

## Project structure

- `index.php` — Home page: lists posts
- `post.php` — Single post page with comments
- `includes/db.php` — Database connection
- `includes/auth.php` — Session helpers and auth utilities
- `includes/header.php` — Shared header (Bootstrap, navbar)
- `includes/footer.php` — Shared footer (Bootstrap bundle)
- `admin/login.php` — Admin login (also handles first admin creation)
- `admin/logout.php` — Ends session
- `admin/dashboard.php` — Admin landing; list posts with actions
- `admin/add.php` — Create post
- `admin/edit.php` — Update post
- `admin/delete.php` — Confirm and delete post

## Requirements

- PHP 7.4+ (PHP 8.x recommended)
- MySQL/MariaDB
- Web server (Apache, Nginx). Example assumes Apache with PHP enabled

## Setup

1. Create the database and tables

   ```sql
   CREATE DATABASE IF NOT EXISTS blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE blog_db;

   -- posts table
   CREATE TABLE IF NOT EXISTS posts (
     id INT AUTO_INCREMENT PRIMARY KEY,
     title VARCHAR(255) NOT NULL,
     content MEDIUMTEXT NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   -- comments table
   CREATE TABLE IF NOT EXISTS comments (
     id INT AUTO_INCREMENT PRIMARY KEY,
     post_id INT NOT NULL,
     name VARCHAR(100) NOT NULL,
     comment MEDIUMTEXT NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     CONSTRAINT fk_comments_post
       FOREIGN KEY (post_id) REFERENCES posts(id)
       ON DELETE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   -- admins table (also auto-created at runtime if missing)
   CREATE TABLE IF NOT EXISTS admins (
     id INT AUTO_INCREMENT PRIMARY KEY,
     username VARCHAR(100) NOT NULL UNIQUE,
     password_hash VARCHAR(255) NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   ```

2. Configure database credentials

   - Edit `includes/db.php` and set `$host`, `$user`, `$password`, `$dbname` for your environment.

3. Deploy files under your web root

   - Example path: `/opt/lampp/htdocs/blog`
   - Access the site at `http://localhost/blog/`

4. Create the first admin user

   - Go to `http://localhost/blog/admin/login.php`
   - If no admin exists, you’ll see a “Create Admin Account” form above the login form
   - After creating, log in and you’ll be redirected to the dashboard

## Usage

- Public
  - Home: `/blog/index.php`
  - Post page: `/blog/post.php?id={POST_ID}`
- Admin
  - Login: `/blog/admin/login.php`
  - Dashboard: `/blog/admin/dashboard.php`
  - Add: `/blog/admin/add.php`
  - Edit: `/blog/admin/edit.php?id={POST_ID}`
  - Delete: `/blog/admin/delete.php?id={POST_ID}`

## Notes

- Pretty URLs are not required; paths are relative and work when the site is served under a subdirectory like `/blog`.
- `admin/login.php` includes a sign-up flow automatically when no admins exist. You don’t need a separate `signup.php`.

## Troubleshooting

- Cannot redeclare function errors
  - This project uses `require_once` with absolute paths to avoid redeclaration
  - If you still see errors on some shared hosts, clear OPcache or disable it temporarily

- Blank page / PHP warnings
  - Check your server error logs
  - Run a syntax check: `php -l path/to/file.php`

- Database connection errors
  - Verify credentials in `includes/db.php`
  - Confirm the database and tables exist and your user has permissions

## License

MIT
