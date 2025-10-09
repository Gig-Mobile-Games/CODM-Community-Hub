# CODM Community Hub

A comprehensive web application for Call of Duty: Mobile community content management, built with PHP and MySQL.

## Features

### Public Features
- **Dynamic Homepage** - Featured content, trending videos, latest news
- **Guides Section** - Comprehensive guides and tutorials with categories
- **Video Management** - Embedded YouTube and TikTok videos
- **News Section** - Latest game updates and community news
- **Content Submission** - User-generated content submission system
- **Search Functionality** - Full-text search across all content
- **Responsive Design** - Mobile-friendly interface

### Admin Features
- **Dashboard Analytics** - Site statistics and user engagement metrics
- **Content Management** - Create, edit, and manage guides, videos, and news
- **User Management** - Manage user accounts and permissions
- **Content Moderation** - Review and approve user submissions
- **Settings Panel** - Configure site settings and preferences
- **Analytics Reporting** - Detailed traffic and engagement reports

### Technical Features
- **User Authentication** - Secure login/registration system
- **Role-Based Access** - Admin, moderator, content creator, and member roles
- **Content Categories** - Organized content with custom categories
- **Like System** - User engagement with content
- **Analytics Tracking** - Page views and user behavior tracking
- **SEO Optimized** - Meta tags and search engine friendly URLs

## Installation

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.3 or higher
- Web server (Apache/Nginx)
- mod_rewrite enabled (for clean URLs)

### Quick Installation

1. **Download and Extract**
   ```bash
   # Clone or download the project
   git clone https://github.com/yourusername/codm-community-hub.git
   cd codm-community-hub
   ```

2. **Database Setup**
   - Create a MySQL database
   - Import the schema: `database/schema.sql`
   - Or use the web installer at `install.php`

3. **Configuration**
   - Update `config/database.php` with your database credentials
   - Set proper file permissions for uploads directory

4. **Web Installer**
   - Navigate to `http://yourdomain.com/install.php`
   - Follow the installation wizard
   - Create your admin account

### Manual Installation

1. **Database Configuration**
   ```php
   // config/database.php
   private $host = 'localhost';
   private $db_name = 'codm_community_hub';
   private $username = 'your_username';
   private $password = 'your_password';
   ```

2. **Import Database Schema**
   ```sql
   mysql -u username -p database_name < database/schema.sql
   ```

3. **Set Permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 config/database.php
   ```

## File Structure

```
codm-community-hub/
├── admin/                  # Admin dashboard
│   ├── index.php
│   └── settings.php
├── api/                    # API endpoints
│   ├── search.php
│   └── like.php
├── assets/                 # Static assets
│   ├── css/
│   ├── js/
│   └── images/
├── config/                 # Configuration files
│   └── database.php
├── database/               # Database schema
│   └── schema.sql
├── includes/               # Core functionality
│   ├── auth.php
│   └── functions.php
├── uploads/                # User uploads
├── index.php              # Homepage
├── guides.php             # Guides page
├── videos.php             # Videos page
├── news.php               # News page
├── submit.php             # Content submission
├── login.php              # User login
├── register.php           # User registration
├── install.php            # Installation script
└── README.md
```

## Usage

### Admin Dashboard

1. **Access Admin Panel**
   - Navigate to `/admin/`
   - Login with admin credentials

2. **Content Management**
   - Create new guides, videos, and news articles
   - Edit existing content
   - Manage categories and content types

3. **User Management**
   - View registered users
   - Manage user roles and permissions
   - Moderate user submissions

4. **Analytics**
   - View site traffic statistics
   - Monitor popular content
   - Track user engagement

### Content Creation

1. **Guides**
   - Write comprehensive tutorials
   - Add categories and tags
   - Include images and formatting

2. **Videos**
   - Embed YouTube and TikTok videos
   - Add creator information
   - Categorize by type

3. **News**
   - Publish game updates
   - Share community news
   - Feature important announcements

### User Features

1. **Registration**
   - Create user accounts
   - Choose content creator role
   - Submit content for review

2. **Content Submission**
   - Submit guides and tutorials
   - Share video content
   - Contribute news articles

## Configuration

### Site Settings

Access the admin panel to configure:
- Site name and description
- Contact information
- User registration settings
- Content submission permissions
- Default content status

### Database Settings

Update `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'your_database';
private $username = 'your_username';
private $password = 'your_password';
```

## Security Features

- **Password Hashing** - Secure password storage
- **SQL Injection Protection** - Prepared statements
- **XSS Prevention** - Input sanitization
- **CSRF Protection** - Form token validation
- **Role-Based Access** - Permission system
- **File Upload Security** - Type and size validation

## API Endpoints

### Search API
```
GET /api/search.php?q=search_term
```

### Like API
```
POST /api/like.php
Content-Type: application/x-www-form-urlencoded

content_id=123&content_type=guide&action=like
```

## Customization

### Themes
- Modify `assets/css/style.css` for styling
- Update color variables in CSS root
- Customize admin panel in `assets/css/admin.css`

### Content Types
- Add new content types in database
- Update content management system
- Modify submission forms

### Features
- Extend user roles
- Add new content categories
- Implement additional analytics
- Create custom API endpoints

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials
   - Ensure MySQL service is running
   - Verify database exists

2. **Permission Errors**
   - Set proper file permissions
   - Check upload directory permissions
   - Verify web server configuration

3. **Installation Issues**
   - Ensure PHP extensions are installed
   - Check error logs
   - Verify database schema import

### Debug Mode

Enable debug mode in `config/database.php`:
```php
// Add this for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:
- Create an issue on GitHub
- Contact the development team
- Check the documentation

## Changelog

### Version 1.0.0
- Initial release
- Complete admin dashboard
- User authentication system
- Content management features
- Analytics and reporting
- Mobile-responsive design

## Roadmap

- [ ] Advanced analytics dashboard
- [ ] Email notifications
- [ ] Social media integration
- [ ] Mobile app API
- [ ] Advanced search filters
- [ ] Content versioning
- [ ] Multi-language support
- [ ] Advanced user profiles

---

**Note**: This is a fan-made website and is not affiliated with Activision or Call of Duty.
