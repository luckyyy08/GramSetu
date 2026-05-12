# GramSetu Deployment Guide 🚀

Follow these steps to deploy the **GramSetu** Smart Village Platform.

## 1. Local Setup (XAMPP/WAMP)
1. Copy the `GramSetu` folder to your `htdocs` directory.
2. Open `phpMyAdmin` (http://localhost/phpmyadmin).
3. Create a new database named `gramsetu`.
4. Import the `database/schema.sql` file.
5. Open `config/database.php` and verify the credentials.
6. Access the project at `http://localhost/GramSetu`.

## 2. InfinityFree / cPanel Deployment
1. **Upload Files**: Use FTP (like FileZilla) or cPanel File Manager to upload all files to the `public_html` (or `htdocs` for InfinityFree) directory.
2. **Database Setup**:
   - Create a MySQL database in your hosting panel.
   - Create a database user and assign it to the database with all privileges.
   - Import `database/schema.sql` using the hosting's phpMyAdmin.
3. **Configuration**:
   - Edit `config/database.php` on the server:
     - Change `DB_HOST` to the host provided by your hosting (often `localhost`).
     - Update `DB_USER`, `DB_PASS`, and `DB_NAME` with your new credentials.
   - Edit `config/app.php`:
     - Change `APP_URL` to your actual domain name (e.g., `https://yourvillage.infinityfreeapp.com`).
4. **Permissions**:
   - Ensure the `uploads/` directory and its subdirectories have write permissions (CHMOD 755 or 777 if needed).

## 3. Security Best Practices
- **PHP Version**: Use PHP 7.4 or higher (8.1+ recommended).
- **SSL**: Enable HTTPS using Let's Encrypt (usually free in cPanel).
- **.htaccess**: Create an `.htaccess` file to prevent directory listing.
- **Validation**: Ensure all file uploads are validated (implemented in this MVP).

## 4. Default Admin Login
- **Phone**: `0000000000`
- **Password**: `admin123`

---
*Built with ❤️ for Digital India*
