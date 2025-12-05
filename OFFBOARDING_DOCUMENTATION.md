# NUJUM - Software Developer Offboarding Documentation

**Project Name:** NUJUM (AI Prediction Analysis System)  
**Version:** 1.0  
**Last Updated:** 2025  
**Documentation Type:** Comprehensive Offboarding Guide

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Getting Started Guide](#2-getting-started-guide)
3. [Project & System](#3-project--system)
4. [Code-Level](#4-code-level)
5. [Troubleshooting Guide](#5-troubleshooting-guide)
6. [DevOps & Infrastructure](#6-devops--infrastructure)
7. [Database](#7-database)
8. [Operations](#8-operations)
9. [Access & Accounts](#9-access--accounts)
10. [Glossary of Terms](#10-glossary-of-terms)
11. [Master Handover Document](#11-master-handover-document)

---

## 1. Executive Summary

### What the System Does (Plain Language)

NUJUM is an AI-powered prediction and analysis platform that helps organizations make informed decisions about the future. Think of it as a sophisticated crystal ball that uses artificial intelligence to analyze any topic you provide and generate comprehensive reports about:

- **What's happening now** (current situation analysis)
- **What might happen in the future** (predictions for different time periods)
- **What risks to watch out for** (risk assessment)
- **What actions to take** (strategic recommendations)

The system can analyze topics ranging from business strategies and market trends to policy decisions and social phenomena. Users can provide text, upload documents (PDFs, Excel files, spreadsheets), and include web links as sources. The AI then processes all this information and generates detailed, structured reports.

Additionally, NUJUM includes a **Social Media Analysis** feature that can analyze a person's social media presence across Facebook, Instagram, and TikTok to provide insights about their professional profile, personality traits, work ethic, and cultural fit - useful for recruitment and professional assessment.

### Who Uses It (User Types/Roles)

The system has three distinct user roles, each with different permissions and capabilities:

1. **Regular Users (Clients)**
   - Create prediction analyses on any topic
   - Perform social media analyses
   - View their own analysis history
   - Export reports as PDFs
   - Access personal analytics
   - Send and receive messages with admins

2. **Admins**
   - Everything regular users can do
   - Manage their organization's clients (create, edit, delete user accounts)
   - View all predictions and analyses from their organization's users
   - Access organization-wide analytics
   - Send messages to users and other admins
   - Set client limits (how many users they can manage)
   - View user activity and usage statistics

3. **Super Admins**
   - Everything admins can do
   - Manage all admins across the entire system
   - Manage all users across all organizations
   - Access system-wide analytics and logs
   - Configure system settings (AI provider selection, etc.)
   - View system health and performance metrics
   - Export system-wide data

### Key Business Processes

1. **Prediction Analysis Workflow:**
   - User enters a topic and provides input data (text, files, URLs)
   - System processes files and scrapes web content from URLs
   - AI service analyzes the combined information
   - System generates structured prediction report
   - User can view, export, or share the results

2. **Social Media Analysis Workflow:**
   - User enters a username or profile identifier
   - System searches across Facebook, Instagram, and TikTok
   - Collects profile data, posts, and activity information
   - AI analyzes the data for professional insights
   - Generates comprehensive personality and professional assessment

3. **User Management Workflow:**
   - Admin creates client accounts for their organization
   - Clients log in and use the system
   - Admin monitors usage and manages accounts
   - Super Admin oversees all organizations and admins

4. **Analytics & Reporting:**
   - System tracks every analysis (tokens used, costs, processing time)
   - Admins and Super Admins can view usage statistics
   - Export capabilities for data analysis
   - Cost tracking and projections

### Main Features

1. **AI-Powered Prediction Analysis**
   - Multi-horizon predictions (2 days to 2 years)
   - Source URL integration with web scraping
   - File upload support (PDF, Excel, CSV, TXT)
   - Structured output (executive summary, predictions, risks, recommendations)
   - Confidence scoring

2. **Social Media Intelligence**
   - Multi-platform analysis (Facebook, Instagram, TikTok)
   - Professional profile assessment
   - Personality and communication style analysis
   - Work ethic indicators
   - Cultural fit evaluation

3. **User Management System**
   - Role-based access control (User, Admin, Super Admin)
   - Organization-based user grouping
   - Client limit management
   - Activity tracking

4. **Analytics Dashboard**
   - Token usage tracking
   - Cost analysis and projections
   - Performance metrics
   - User behavior insights
   - Export capabilities

5. **Communication Features**
   - Internal messaging system
   - Real-time chat functionality
   - Unread message notifications

6. **Export & Reporting**
   - PDF export for predictions
   - PDF export for social media analyses
   - CSV export for analytics data

### System Status

**Current State:**
- ✅ Production-ready application
- ✅ Full AI integration (Gemini and ChatGPT support)
- ✅ Multi-role authentication system
- ✅ Analytics tracking implemented
- ✅ File processing capabilities
- ✅ Web scraping functionality
- ✅ Social media analysis feature
- ✅ Messaging and chat system

**Technology Stack:**
- Backend: Laravel 12 (PHP 8.2+)
- Frontend: Blade templates with Tailwind CSS
- Database: SQLite (default), supports MySQL/PostgreSQL
- AI Services: Google Gemini 2.5 Flash, OpenAI GPT-4o
- Additional Services: Apify (for social media scraping)

**Known Limitations:**
- AI API rate limits (15 requests/minute for Gemini free tier)
- Web scraping may fail on sites with anti-bot protection
- File upload size limit: 10MB per file
- Social media scraping depends on third-party Apify service availability

---

## 2. Getting Started Guide

### Prerequisites Checklist

Before you can work on this project, ensure you have the following installed and configured:

**Required Software:**
- [ ] PHP 8.2 or higher
- [ ] Composer (PHP dependency manager)
- [ ] Node.js and npm (for Puppeteer if needed)
- [ ] Git (version control)
- [ ] A code editor (VS Code, PhpStorm, etc.)

**Required Accounts & API Keys:**
- [ ] Google Gemini API key (from [Google AI Studio](https://makersuite.google.com/app/apikey))
- [ ] (Optional) OpenAI API key (from [OpenAI Platform](https://platform.openai.com/api-keys))
- [ ] (Optional) Apify API token (for social media scraping, from [Apify](https://apify.com))

**Database Options (choose one):**
- [ ] SQLite (default, no setup needed)
- [ ] MySQL/MariaDB (if preferred)
- [ ] PostgreSQL (if preferred)

**Development Tools (Recommended):**
- [ ] Laravel Sail (Docker-based development environment)
- [ ] Laravel Tinker (for testing)
- [ ] Postman or similar (for API testing)

### Step-by-Step Setup with Explanations

#### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd prediction-ai
```

**Why:** This downloads the project code to your local machine.

#### Step 2: Install PHP Dependencies

```bash
composer install
```

**Why:** Composer installs all PHP packages required by the application (Laravel framework, PDF generation, Excel processing, etc.). This creates the `vendor/` directory with all dependencies.

#### Step 3: Install Node Dependencies (if needed)

```bash
npm install
```

**Why:** Installs JavaScript dependencies. Currently, the project uses Puppeteer for web scraping, which requires Node.js packages.

#### Step 4: Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

**Why:** 
- `.env.example` is a template with all configuration variables
- Copying it creates your local `.env` file where you'll store your actual configuration
- `key:generate` creates a unique encryption key for your application (required for security)

#### Step 5: Configure Database

**For SQLite (Easiest - Default):**
```bash
touch database/database.sqlite
```

Then in your `.env` file, ensure:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

**For MySQL:**
In your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nujum
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Why:** The application needs a database to store users, predictions, analyses, and other data. SQLite is the simplest option for development (no separate database server needed).

#### Step 6: Configure API Keys

Edit your `.env` file and add:

```env
# Google Gemini (Required)
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_SSL_VERIFY=false  # Set to true in production

# OpenAI ChatGPT (Optional)
OPENAI_API_KEY=your_openai_api_key_here
OPENAI_MODEL=gpt-4o
OPENAI_MAX_TOKENS=6000
OPENAI_TEMPERATURE=1
OPENAI_TIMEOUT=300

# Apify (Optional - for social media scraping)
APIFY_API_TOKEN=your_apify_token_here
```

**Why:** 
- Gemini API is required for the core prediction analysis feature
- ChatGPT is optional but allows switching between AI providers
- Apify is needed for social media analysis feature

#### Step 7: Run Database Migrations

```bash
php artisan migrate
```

**Why:** Migrations create all the database tables needed by the application (users, predictions, social_media_analyses, messages, analytics, etc.). This sets up your database schema.

#### Step 8: Create Storage Link

```bash
php artisan storage:link
```

**Why:** Creates a symbolic link so uploaded files can be accessed via the web. This allows users to download files they've uploaded.

#### Step 9: Clear Configuration Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**Why:** Ensures Laravel picks up your new `.env` configuration and clears any cached settings from previous runs.

#### Step 10: Start the Development Server

```bash
php artisan serve
```

**Why:** Starts a local web server (usually at `http://localhost:8000`) so you can access the application in your browser.

#### Step 11: Create Your First User

You can either:
- Use Laravel Tinker to create a user:
```bash
php artisan tinker
>>> $user = \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'role' => 'superadmin']);
```

- Or create a seeder (recommended for development):
```bash
php artisan make:seeder UserSeeder
# Edit the seeder file, then run:
php artisan db:seed --class=UserSeeder
```

**Why:** You need at least one user account to log in and test the system.

### First Day Checklist

After completing setup, verify everything works:

- [ ] Can access the application at `http://localhost:8000`
- [ ] Can log in with a test user account
- [ ] Can create a prediction analysis
- [ ] Can view prediction results
- [ ] Can access admin dashboard (if admin user)
- [ ] Can test AI API connection at `/predictions/api/test`
- [ ] Can view logs in `storage/logs/laravel.log`
- [ ] Database tables exist (check with `php artisan tinker` and `\DB::table('users')->count()`)

### Learning Path (First Week)

**Day 1-2: Understanding the Basics**
- Read this documentation thoroughly
- Explore the codebase structure
- Understand Laravel basics (if new to Laravel):
  - MVC architecture
  - Routes, Controllers, Models, Views
  - Eloquent ORM
  - Blade templating

**Day 3-4: Core Features**
- Study `PredictionController` and `Prediction` model
- Understand how AI services work (`GeminiService`, `ChatGPTService`)
- Learn about the `AIServiceFactory` pattern
- Review file processing (`FileProcessingService`)
- Study web scraping (`WebScrapingService`)

**Day 5: Advanced Features**
- Social media analysis (`SocialMediaService`, `SocialMediaController`)
- Analytics system (`AnalyticsService`)
- User management and roles
- Messaging system

**Day 6-7: Integration & Testing**
- Test all features end-to-end
- Review error handling and logging
- Understand the deployment process
- Practice making small changes

### Common First-Time Issues and Solutions

**Issue 1: "Class not found" errors**
- **Solution:** Run `composer dump-autoload` to regenerate the autoloader

**Issue 2: "SQLite database not found"**
- **Solution:** Ensure `database/database.sqlite` exists. Create it with `touch database/database.sqlite` if missing.

**Issue 3: "Permission denied" on storage/logs**
- **Solution:** On Linux/Mac: `chmod -R 775 storage bootstrap/cache`. On Windows, ensure the folder is writable.

**Issue 4: "API key not working"**
- **Solution:** 
  - Verify the API key is correct in `.env`
  - Run `php artisan config:clear` to refresh config
  - Check API key format (Gemini keys start with "AIza", OpenAI keys start with "sk-")

**Issue 5: "SSL certificate verify failed"**
- **Solution:** Set `GEMINI_SSL_VERIFY=false` or `OPENAI_SSL_VERIFY=false` in `.env` for development (use `true` in production)

**Issue 6: "Route not found" errors**
- **Solution:** Run `php artisan route:clear` and `php artisan route:cache` (in production)

**Issue 7: "View not found"**
- **Solution:** Run `php artisan view:clear` to clear view cache

### Framework/Language Basics Explained

**Laravel Framework:**
- **What it is:** A PHP web framework that provides structure and tools for building web applications
- **Why we use it:** It handles common tasks (routing, database access, authentication) so we can focus on business logic
- **Key concepts:**
  - **Routes** (`routes/web.php`): Define URLs and what code runs when someone visits them
  - **Controllers** (`app/Http/Controllers/`): Handle the logic for each route
  - **Models** (`app/Models/`): Represent database tables and provide easy ways to query data
  - **Views** (`resources/views/`): HTML templates that display data to users
  - **Migrations** (`database/migrations/`): Define database structure in code
  - **Middleware**: Code that runs before/after requests (like authentication checks)

**PHP Basics (if new to PHP):**
- PHP is a server-side scripting language
- Variables start with `$`
- Arrays can be indexed (`$arr[0]`) or associative (`$arr['key']`)
- Functions are defined with `function name() {}`
- Classes use `class Name {}` syntax
- Namespaces help organize code (`namespace App\Models;`)

**Blade Templating:**
- Laravel's template engine
- Mixes PHP with HTML: `{{ $variable }}` outputs a variable
- `@if`, `@foreach`, `@extends` provide control structures
- `@section` and `@yield` allow template inheritance

---

## 3. Project & System

### High-Level Architecture

```
+-------------------------------------------------------------+
|                        Web Browser                           |
|                    (User Interface)                          |
+-------------------------+-------------------------------------+
                         |
                         | HTTP Requests
                         |
+------------------------+-------------------------------------+
|                    Laravel Application                       |
|  +------------------------------------------------------+  |
|  |              Routes (web.php)                        |  |
|  |  Defines: /predictions, /admin, /social-media, etc.  |  |
|  +-------------------+----------------------------------+  |
|                      |                                    |
|  +-------------------+----------------------------------+  |
|  |          Middleware Layer                             |  |
|  |  - Authentication (auth)                               |  |
|  |  - Admin Authorization (admin)                       |  |
|  |  - SuperAdmin Authorization (superadmin)             |  |
|  +-------------------+----------------------------------+  |
|                      |                                    |
|  +-------------------+----------------------------------+  |
|  |          Controllers                                  |  |
|  |  - PredictionController                              |  |
|  |  - SocialMediaController                             |  |
|  |  - AdminController                                   |  |
|  |  - SuperAdminController                              |  |
|  |  - MessageController, ChatController                 |  |
|  +-------------------+----------------------------------+  |
|                      |                                    |
|  +-------------------+----------------------------------+  |
|  |          Services Layer                               |  |
|  |  - AIServiceFactory (creates AI services)            |  |
|  |  - GeminiService / ChatGPTService                    |  |
|  |  - FileProcessingService                             |  |
|  |  - WebScrapingService                                |  |
|  |  - SocialMediaService                                |  |
|  |  - AnalyticsService                                  |  |
|  +-------------------+----------------------------------+  |
|                      |                                    |
|  +-------------------+----------------------------------+  |
|  |          Models (Database Layer)                     |  |
|  |  - User, Prediction, SocialMediaAnalysis             |  |
|  |  - Message, AnalysisAnalytics, SystemSetting         |  |
|  +-------------------+----------------------------------+  |
+---------------------+----------------------------------------+
                      |
                      | Database Queries
                      |
+---------------------+----------------------------------------+
|                    Database (SQLite/MySQL)                   |
|  - users, predictions, social_media_analyses               |
|  - messages, analysis_analytics, system_settings            |
+-------------------------------------------------------------+

External Services:
+------------------+  +------------------+  +------------------+
|  Google Gemini   |  |  OpenAI ChatGPT |  |  Apify Platform  |
|      API         |  |      API         |  |  (Social Media)  |
+------------------+  +------------------+  +------------------+
```

**Request Flow Example (Creating a Prediction):**

1. User submits form → Browser sends POST request to `/predictions`
2. Route matches → Laravel routes to `PredictionController@store`
3. Middleware checks → Authentication middleware verifies user is logged in
4. Controller processes → Validates input, processes files, scrapes URLs
5. Service layer → Calls `AIServiceFactory` to get AI service
6. AI service → Sends request to Gemini/ChatGPT API
7. Response handling → Processes AI response, saves to database
8. View rendering → Returns prediction results page
9. Browser displays → User sees the analysis results

### Technology Stack with "Why" Explanations

**Backend Framework: Laravel 12**
- **What:** Modern PHP web framework
- **Why:** 
  - Provides robust authentication, routing, and database abstraction out of the box
  - Excellent documentation and large community
  - Built-in security features (CSRF protection, SQL injection prevention)
  - Eloquent ORM makes database work easier
  - Blade templating is intuitive and powerful

**PHP 8.2+**
- **What:** Server-side programming language
- **Why:**
  - Required by Laravel 12
  - PHP 8.2 offers significant performance improvements
  - Strong typing support for better code quality

**Database: SQLite (default) / MySQL / PostgreSQL**
- **What:** Data storage systems
- **Why SQLite for development:**
  - No separate database server needed
  - Easy setup (just a file)
  - Perfect for development and small deployments
- **Why MySQL/PostgreSQL for production:**
  - Better performance for large datasets
  - More features (stored procedures, triggers)
  - Better concurrent access handling

**AI Services: Google Gemini & OpenAI ChatGPT**
- **What:** Cloud-based AI APIs that generate text
- **Why both:**
  - Gemini: Fast, cost-effective, good for structured analysis
  - ChatGPT: Alternative option, sometimes better for creative tasks
  - Factory pattern allows easy switching between providers
  - Redundancy if one service is down

**Frontend: Blade Templates + Tailwind CSS**
- **What:** 
  - Blade: Laravel's templating engine
  - Tailwind: Utility-first CSS framework
- **Why:**
  - Blade integrates seamlessly with Laravel
  - Tailwind allows rapid UI development without writing custom CSS
  - Responsive design is easier with Tailwind utilities

**File Processing Libraries:**
- **DomPDF** (`barryvdh/laravel-dompdf`): PDF generation
- **Maatwebsite Excel** (`maatwebsite/excel`): Excel file reading
- **PDF Parser** (`smalot/pdfparser`): Extract text from PDFs
- **Why:** These handle the complex task of reading different file formats so we don't have to write that code ourselves

**Web Scraping: Symfony DomCrawler + Guzzle**
- **What:** Libraries for downloading and parsing web pages
- **Why:** 
  - Guzzle: Modern HTTP client for making web requests
  - DomCrawler: Parses HTML to extract content
  - Together they enable the source URL scraping feature

**Social Media Scraping: Apify Platform**
- **What:** Third-party service that provides pre-built scrapers
- **Why:**
  - Social media platforms have complex anti-bot protection
  - Building scrapers ourselves would be time-consuming and fragile
  - Apify maintains working scrapers for Facebook, Instagram, TikTok
  - More reliable than DIY solutions

**Node.js / Puppeteer**
- **What:** JavaScript runtime and browser automation tool
- **Why:** 
  - Some web scraping requires JavaScript execution (modern SPAs)
  - Puppeteer can render JavaScript-heavy pages
  - Currently used for advanced web scraping scenarios

### Project Setup Guide

**Directory Structure:**

```
prediction-ai/
+-- app/
|   +-- Http/
|   |   +-- Controllers/        # Request handlers
|   |   +-- Middleware/         # Request filters
|   |   +-- Requests/           # Form validation
|   +-- Models/                 # Database models
|   +-- Services/               # Business logic
|   +-- Providers/              # Service providers
+-- bootstrap/                  # Application bootstrap
+-- config/                     # Configuration files
+-- database/
|   +-- migrations/             # Database schema
|   +-- seeders/                # Test data
+-- public/                     # Web-accessible files
+-- resources/
|   +-- views/                  # Blade templates
+-- routes/
|   +-- web.php                 # Route definitions
+-- storage/                    # Logs, cache, uploads
+-- tests/                      # Automated tests
+-- .env                        # Environment config (not in git)
+-- composer.json               # PHP dependencies
+-- package.json                # Node dependencies
```

**Key Configuration Files:**

1. **`.env`** - Environment-specific settings (API keys, database, etc.)
2. **`config/app.php`** - Application-wide settings
3. **`config/database.php`** - Database connections
4. **`config/services.php`** - Third-party service configurations
5. **`routes/web.php`** - All application routes

**Environment Variables Reference:**

```env
# Application
APP_NAME=NUJUM
APP_ENV=local                    # local, staging, production
APP_DEBUG=true                   # false in production
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite

# AI Services
GEMINI_API_KEY=your_key_here
GEMINI_SSL_VERIFY=false
OPENAI_API_KEY=your_key_here
OPENAI_MODEL=gpt-4o

# Social Media
APIFY_API_TOKEN=your_token_here

# File Storage
FILESYSTEM_DISK=local
```

### API Documentation

**Internal API Endpoints (for frontend AJAX calls):**

**Prediction Endpoints:**
- `POST /predictions` - Create new prediction
- `GET /predictions` - List user's predictions
- `GET /predictions/{id}` - View specific prediction
- `GET /predictions/history` - View prediction history
- `GET /predictions/analytics` - User analytics
- `DELETE /predictions/{id}` - Delete prediction
- `GET /predictions/{id}/export` - Export as PDF
- `POST /predictions/validate-urls` - Validate source URLs
- `GET /predictions/api/test` - Test AI API connection

**Social Media Endpoints:**
- `POST /social-media/analyze` - Analyze social media account
- `GET /social-media` - Analysis form
- `GET /social-media/history` - Analysis history
- `GET /social-media/{id}` - View analysis results
- `GET /social-media/{id}/export` - Export as PDF
- `DELETE /social-media/{id}` - Delete analysis

**Admin Endpoints:**
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/users` - List users
- `POST /admin/users` - Create user
- `GET /admin/predictions` - View all predictions
- `GET /admin/analytics` - Organization analytics

**Super Admin Endpoints:**
- `GET /superadmin/dashboard` - Super admin dashboard
- `GET /superadmin/settings` - System settings
- `POST /superadmin/ai-provider` - Change AI provider
- `GET /superadmin/logs` - System logs

**Messaging Endpoints:**
- `GET /admin/messages` - List messages
- `POST /admin/messages` - Send message
- `POST /admin/messages/{id}/mark-read` - Mark as read
- `GET /admin/messages/unread-count` - Get unread count

**Chat Endpoints:**
- `GET /admin/chat` - Chat interface
- `GET /admin/chat/messages/{partner}` - Get chat messages
- `POST /admin/chat/send` - Send chat message

**Response Formats:**

Most endpoints return JSON for AJAX requests or HTML views for page loads.

Example JSON response:
```json
{
  "success": true,
  "data": { ... },
  "message": "Operation completed"
}
```

Error response:
```json
{
  "success": false,
  "error": "Error message",
  "errors": { "field": ["Validation error"] }
}
```

### Coding Standards

**PHP/Laravel Standards:**

1. **PSR-12 Coding Style:**
   - Use 4 spaces for indentation (no tabs)
   - Class names in PascalCase: `PredictionController`
   - Method names in camelCase: `getUserAnalytics()`
   - Constants in UPPER_SNAKE_CASE: `STATUS_COMPLETED`

2. **Naming Conventions:**
   - Models: Singular, PascalCase (`User`, `Prediction`)
   - Controllers: Singular + "Controller" (`PredictionController`)
   - Services: Singular + "Service" (`GeminiService`)
   - Migrations: Descriptive with timestamp prefix

3. **File Organization:**
   - One class per file
   - Namespace matches directory structure
   - Use type hints for parameters and return types

4. **Code Structure:**
   ```php
   <?php
   
   namespace App\Http\Controllers;
   
   use App\Models\Prediction;
   use Illuminate\Http\Request;
   
   class PredictionController extends Controller
   {
       public function index()
       {
           // Controller logic here
       }
   }
   ```

5. **Database:**
   - Use Eloquent models, not raw queries when possible
   - Use migrations for schema changes
   - Always use transactions for multi-step operations

6. **Error Handling:**
   - Use try-catch blocks for external API calls
   - Log errors with context: `Log::error('Message', ['context' => $data])`
   - Return meaningful error messages to users

7. **Security:**
   - Always validate user input
   - Use Laravel's built-in CSRF protection
   - Check authorization before sensitive operations
   - Never expose API keys or sensitive data

8. **Comments:**
   - Document complex logic
   - Use PHPDoc for classes and methods:
   ```php
   /**
    * Analyze text using AI service
    *
    * @param string $text The text to analyze
    * @param string $analysisType Type of analysis
    * @return array Analysis results
    */
   ```

**Blade Template Standards:**

1. Use `@extends` for layout inheritance
2. Use `@section` and `@yield` for content blocks
3. Escape output: `{{ $variable }}` (auto-escaped)
4. Raw output only when safe: `{!! $html !!}`
5. Use `@if`, `@foreach` for control structures
6. Keep logic minimal in views (move to controllers/services)

---

## 4. Code-Level

### Work-in-Progress Report

**Completed Features:**
- ✅ Core prediction analysis system
- ✅ AI integration (Gemini and ChatGPT)
- ✅ File upload and processing (PDF, Excel, CSV, TXT)
- ✅ Web scraping for source URLs
- ✅ Social media analysis (Facebook, Instagram, TikTok)
- ✅ User management system (User, Admin, Super Admin roles)
- ✅ Analytics tracking system
- ✅ Messaging and chat system
- ✅ PDF export functionality
- ✅ Analytics dashboards (user, admin, super admin)
- ✅ Multi-organization support
- ✅ Client limit management
- ✅ AI provider switching (Gemini/ChatGPT)
- ✅ Responsive design
- ✅ Authentication and authorization

**In-Progress Features:**
- 🔄 Enhanced error handling and user feedback
- 🔄 Performance optimization for large file processing
- 🔄 Advanced analytics visualizations

**Pending Features (from README roadmap):**
- ⏳ Enhanced prediction modeling algorithms
- ⏳ Industry-specific analysis templates
- ⏳ Real-time data integration
- ⏳ Advanced visualization capabilities
- ⏳ Multi-language support
- ⏳ API rate limiting and optimization
- ⏳ Background job processing for long-running tasks
- ⏳ Email notifications
- ⏳ API authentication for third-party integrations

### Critical Code Areas

**1. AI Service Integration (`app/Services/`)**

**Why it's critical:** This is the core functionality. If AI services fail, the application can't generate predictions.

**Key Files:**
- `AIServiceFactory.php` - Creates the appropriate AI service
- `GeminiService.php` - Google Gemini API integration
- `ChatGPTService.php` - OpenAI API integration
- `AIServiceInterface.php` - Common interface for AI services

**What to watch for:**
- API key configuration
- Rate limiting (Gemini: 15 req/min on free tier)
- Error handling and fallbacks
- Token usage and cost tracking
- Response parsing and validation

**Example of critical code:**
```php
// In GeminiService.php - API request handling
$response = Http::timeout(300)
    ->withHeaders(['x-goog-api-key' => $this->apiKey])
    ->post($this->baseUrl, ['contents' => $contents]);
```

**2. Prediction Processing (`app/Http/Controllers/PredictionController.php`)**

**Why it's critical:** This orchestrates the entire prediction workflow.

**Key methods:**
- `store()` - Creates new prediction (file processing, URL scraping, AI analysis)
- `show()` - Displays prediction results
- `export()` - PDF generation

**What to watch for:**
- File upload validation and processing
- URL scraping error handling
- AI service integration
- Database transaction management
- Analytics tracking

**3. Authentication & Authorization (`app/Http/Middleware/`)**

**Why it's critical:** Security depends on proper access control.

**Key Files:**
- `AdminMiddleware.php` - Restricts access to admins
- `SuperAdminMiddleware.php` - Restricts access to super admins
- Laravel's built-in `auth` middleware

**What to watch for:**
- Role checking logic
- Unauthorized access attempts
- Session management

**4. Database Models (`app/Models/`)**

**Why it's critical:** Data integrity and relationships.

**Key Models:**
- `User.php` - User management, role checking
- `Prediction.php` - Prediction data and status management
- `SocialMediaAnalysis.php` - Social media analysis data
- `AnalysisAnalytics.php` - Analytics tracking

**What to watch for:**
- Relationship definitions (hasMany, belongsTo)
- Data validation in model setters
- Status management (enum values)

**5. File Processing (`app/Services/FileProcessingService.php`)**

**Why it's critical:** Handles user-uploaded files which feed into AI analysis.

**What to watch for:**
- File type validation
- File size limits
- Text extraction accuracy
- Error handling for corrupted files

**6. Web Scraping (`app/Services/WebScrapingService.php`)**

**Why it's critical:** Provides additional context for predictions from source URLs.

**What to watch for:**
- Anti-bot protection handling
- Timeout management
- Content extraction accuracy
- Error recovery

### Known Bugs & Limitations

**1. Web Scraping Failures**
- **Issue:** Some websites block automated scraping
- **Impact:** Source URLs may fail to scrape, reducing analysis quality
- **Workaround:** System continues with available data, logs failed URLs
- **Status:** Expected behavior, not a bug (websites intentionally block scrapers)

**2. Large File Processing**
- **Issue:** Very large files (>5MB) may cause timeouts
- **Impact:** Analysis may fail or take very long
- **Workaround:** Current limit is 10MB per file
- **Status:** Known limitation, consider background processing for large files

**3. AI API Rate Limits**
- **Issue:** Gemini free tier: 15 requests/minute
- **Impact:** Multiple simultaneous requests may be throttled
- **Workaround:** System includes retry logic, but users may experience delays
- **Status:** Expected behavior based on API tier

**4. Social Media Scraping Dependencies**
- **Issue:** Depends on Apify service availability
- **Impact:** Social media analysis may fail if Apify is down
- **Workaround:** Error messages inform users of service unavailability
- **Status:** External dependency limitation

**5. SQLite Concurrency**
- **Issue:** SQLite doesn't handle high concurrent writes well
- **Impact:** May experience slowdowns with many simultaneous users
- **Workaround:** Use MySQL/PostgreSQL for production
- **Status:** SQLite is fine for development, not recommended for production

**6. PDF Export Styling**
- **Issue:** Complex CSS may not render perfectly in PDFs
- **Impact:** Exported PDFs may have minor styling differences
- **Workaround:** PDF templates use simplified styling
- **Status:** Limitation of DomPDF library

**7. Memory Usage with Large Analyses**
- **Issue:** Processing very large text inputs may consume significant memory
- **Impact:** Server may run out of memory on very large analyses
- **Workaround:** Current limits prevent most issues, but edge cases exist
- **Status:** Monitor in production, consider chunking for very large inputs

**8. Timezone Handling**
- **Issue:** Timestamps may display in server timezone, not user timezone
- **Impact:** Minor UX issue
- **Status:** Low priority, consider user timezone preferences

---

## 5. Troubleshooting Guide

### Common Issues by Category

#### Authentication & Authorization Issues

**Problem: "Access denied" errors**
- **Check:** User role in database (`users.role` column)
- **Check:** Middleware is applied correctly in routes
- **Solution:** Verify user has correct role: `user`, `admin`, or `superadmin`

**Problem: Can't log in**
- **Check:** User exists in database
- **Check:** Password is hashed correctly (use `bcrypt()`)
- **Check:** Session storage is writable (`storage/framework/sessions`)
- **Solution:** Clear session cache: `php artisan cache:clear`

**Problem: "CSRF token mismatch"**
- **Check:** Session is working
- **Check:** `@csrf` directive is in forms
- **Solution:** Clear config cache: `php artisan config:clear`

#### AI Service Issues

**Problem: "API key not configured"**
- **Check:** `.env` file has `GEMINI_API_KEY` or `OPENAI_API_KEY`
- **Check:** Config cache is cleared: `php artisan config:clear`
- **Check:** API key format (Gemini: starts with "AIza", OpenAI: starts with "sk-")
- **Solution:** Verify key in Google AI Studio or OpenAI Platform

**Problem: "API request timeout"**
- **Check:** Internet connection
- **Check:** API service status (Google/OpenAI status pages)
- **Check:** Timeout settings in `config/services.php`
- **Solution:** Increase timeout or retry later

**Problem: "Rate limit exceeded"**
- **Check:** Number of requests in short time period
- **Check:** API tier (free tier has lower limits)
- **Solution:** Wait before retrying, consider upgrading API tier

**Problem: "Invalid response format"**
- **Check:** AI service response structure
- **Check:** Logs in `storage/logs/laravel.log`
- **Solution:** AI service may have changed API format, update service code

#### Database Issues

**Problem: "Table not found"**
- **Check:** Migrations have run: `php artisan migrate:status`
- **Solution:** Run migrations: `php artisan migrate`

**Problem: "SQLite database locked"**
- **Check:** Multiple processes accessing database
- **Check:** File permissions on `database/database.sqlite`
- **Solution:** Use MySQL/PostgreSQL for production, or ensure single process access

**Problem: "Migration failed"**
- **Check:** Database connection in `.env`
- **Check:** Previous migrations completed
- **Solution:** Check migration file for syntax errors, rollback if needed: `php artisan migrate:rollback`

#### File Upload Issues

**Problem: "File too large"**
- **Check:** `upload_max_filesize` in `php.ini`
- **Check:** `post_max_size` in `php.ini`
- **Check:** Validation rules in controller (currently 10MB limit)
- **Solution:** Increase PHP limits or reduce file size

**Problem: "File type not allowed"**
- **Check:** Validation rules in controller
- **Check:** Allowed types: `pdf`, `xlsx`, `xls`, `csv`, `txt`
- **Solution:** Convert file to allowed format

**Problem: "Can't extract text from file"**
- **Check:** File is not corrupted
- **Check:** File format is supported
- **Check:** Required libraries installed (PDF parser, Excel reader)
- **Solution:** Check logs for specific error, verify file format

#### Web Scraping Issues

**Problem: "Failed to scrape URL"**
- **Check:** URL is accessible
- **Check:** Website has anti-bot protection (common)
- **Check:** Internet connection
- **Solution:** This is expected for many sites, system continues with available data

**Problem: "Scraping timeout"**
- **Check:** Website is slow or unresponsive
- **Check:** Timeout settings in `WebScrapingService`
- **Solution:** Increase timeout or skip problematic URLs

#### Performance Issues

**Problem: "Slow page loads"**
- **Check:** Database query performance (use `DB::enableQueryLog()`)
- **Check:** N+1 query problems (use eager loading: `with()`)
- **Check:** Large datasets without pagination
- **Solution:** Optimize queries, add indexes, implement caching

**Problem: "Memory limit exceeded"**
- **Check:** Processing very large files or text
- **Check:** PHP memory limit (`memory_limit` in `php.ini`)
- **Solution:** Increase memory limit or process data in chunks

**Problem: "Application timeout"**
- **Check:** Long-running operations (AI API calls, file processing)
- **Check:** PHP `max_execution_time` setting
- **Solution:** Increase timeout for specific operations, consider background jobs

### Solutions

**General Debugging Steps:**

1. **Check Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Look for error messages, stack traces, and context

2. **Clear All Caches:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Test Database Connection:**
   ```bash
   php artisan tinker
   >>> \DB::connection()->getPdo();
   ```

4. **Test AI API Connection:**
   - Visit `/predictions/api/test` in browser
   - Or use Tinker:
   ```bash
   php artisan tinker
   >>> \App\Services\AIServiceFactory::testCurrentProvider();
   ```

5. **Check Environment Variables:**
   ```bash
   php artisan tinker
   >>> config('services.gemini.api_key');  // Should show your key
   >>> env('GEMINI_API_KEY');  // Should show your key
   ```

### Debugging Tips

**1. Enable Detailed Error Display:**
In `.env`:
```env
APP_DEBUG=true
APP_ENV=local
```
**Warning:** Never set `APP_DEBUG=true` in production!

**2. Use Laravel Tinker for Testing:**
```bash
php artisan tinker

# Test model queries
>>> \App\Models\User::count();
>>> \App\Models\Prediction::latest()->first();

# Test services
>>> $service = app(\App\Services\GeminiService::class);
>>> $service->testConnection();

# Test relationships
>>> $user = \App\Models\User::first();
>>> $user->predictions;
```

**3. Log Custom Debug Information:**
```php
use Illuminate\Support\Facades\Log;

Log::info('Debug message', ['data' => $variable]);
Log::error('Error occurred', ['exception' => $e]);
Log::debug('Detailed info', ['context' => $data]);
```

**4. Database Query Debugging:**
```php
// Enable query logging
\DB::enableQueryLog();

// Run your code
$predictions = Prediction::where('user_id', 1)->get();

// Check queries
dd(\DB::getQueryLog());
```

**5. Check Route List:**
```bash
php artisan route:list
```
Shows all registered routes and their middleware

**6. Test API Endpoints:**
Use Postman or curl:
```bash
curl -X POST http://localhost:8000/predictions/api/test \
  -H "Content-Type: application/json"
```

**7. Monitor Real-time Logs:**
```bash
# Linux/Mac
tail -f storage/logs/laravel.log

# Windows PowerShell
Get-Content storage/logs/laravel.log -Wait
```

**8. Check File Permissions:**
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**9. Verify Service Providers:**
```bash
php artisan tinker
>>> app()->getLoadedProviders();
```

**10. Test Specific Features:**
- Test file upload: Create a small test file and upload it
- Test AI: Use the test endpoint or create a simple prediction
- Test database: Run a simple query in Tinker
- Test authentication: Try logging in with different users

---

## 6. DevOps & Infrastructure

### Deployment Process

**Pre-Deployment Checklist:**
- [ ] All tests passing
- [ ] Environment variables configured
- [ ] Database migrations ready
- [ ] API keys obtained and configured
- [ ] File permissions set correctly
- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production` in production
- [ ] SSL certificate configured (for HTTPS)

**Deployment Steps:**

**1. Prepare Server:**
```bash
# Install PHP 8.2+, Composer, Node.js
# Install web server (Nginx or Apache)
# Install database (MySQL/PostgreSQL recommended for production)
```

**2. Clone Repository:**
```bash
git clone <repository-url>
cd prediction-ai
```

**3. Install Dependencies:**
```bash
composer install --optimize-autoloader --no-dev
npm install --production
```

**4. Configure Environment:**
```bash
cp .env.example .env
# Edit .env with production values
php artisan key:generate
```

**5. Set File Permissions:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**6. Run Migrations:**
```bash
php artisan migrate --force
```

**7. Create Storage Link:**
```bash
php artisan storage:link
```

**8. Optimize Application:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

**9. Set Up Web Server:**

**For Nginx:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/prediction-ai/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**For Apache:**
Ensure `.htaccess` file exists in `public/` directory (it should be included).

**10. Set Up Queue Worker (if using queues):**
```bash
php artisan queue:work --daemon
# Or use supervisor for process management
```

**11. Set Up Scheduled Tasks (if using):**
Add to crontab:
```bash
* * * * * cd /path/to/prediction-ai && php artisan schedule:run >> /dev/null 2>&1
```

**12. SSL Certificate (Let's Encrypt):**
```bash
sudo certbot --nginx -d your-domain.com
```

### Server/Cloud Documentation

**Recommended Server Specifications:**

**Minimum (Small Deployment):**
- CPU: 2 cores
- RAM: 2GB
- Storage: 20GB SSD
- Bandwidth: 1TB/month

**Recommended (Medium Deployment):**
- CPU: 4 cores
- RAM: 4GB
- Storage: 50GB SSD
- Bandwidth: 5TB/month

**Production (Large Deployment):**
- CPU: 8+ cores
- RAM: 8GB+
- Storage: 100GB+ SSD
- Bandwidth: 10TB+/month

**Cloud Platform Options:**

1. **DigitalOcean:**
   - Droplets with Laravel one-click install
   - Managed databases available
   - Easy scaling

2. **AWS:**
   - EC2 for application server
   - RDS for database
   - S3 for file storage (optional)
   - Elastic Beanstalk for easy deployment

3. **Heroku:**
   - Simple deployment with Git
   - Add-ons for database
   - Automatic scaling

4. **Laravel Forge:**
   - Specialized for Laravel deployments
   - Automated server setup
   - Easy SSL and deployment

**Database Recommendations:**

- **Development:** SQLite (file-based, no setup)
- **Small Production:** MySQL/MariaDB (good performance, easy setup)
- **Large Production:** PostgreSQL (better for complex queries, better concurrency)

### Environment Configuration

**Production Environment Variables:**

```env
# Application
APP_NAME=NUJUM
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (use MySQL/PostgreSQL in production)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nujum_production
DB_USERNAME=nujum_user
DB_PASSWORD=strong_password_here

# AI Services
GEMINI_API_KEY=your_production_key
GEMINI_SSL_VERIFY=true
OPENAI_API_KEY=your_production_key
OPENAI_SSL_VERIFY=true

# Social Media
APIFY_API_TOKEN=your_production_token

# File Storage
FILESYSTEM_DISK=local
# Or use S3:
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=...
# AWS_SECRET_ACCESS_KEY=...
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=your-bucket-name

# Mail (if using email features)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=file
# Or use Redis:
# CACHE_DRIVER=redis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# Queue (if using)
QUEUE_CONNECTION=database
# Or use Redis:
# QUEUE_CONNECTION=redis
```

**Security Checklist:**
- [ ] `APP_DEBUG=false`
- [ ] Strong database passwords
- [ ] API keys are secure and not exposed
- [ ] SSL/HTTPS enabled
- [ ] File permissions set correctly (storage writable, but not executable)
- [ ] `.env` file not accessible via web (should be outside `public/`)
- [ ] Regular security updates
- [ ] Firewall configured
- [ ] Regular backups configured

**Backup Strategy:**

1. **Database Backups:**
```bash
# MySQL
mysqldump -u user -p database_name > backup.sql

# PostgreSQL
pg_dump database_name > backup.sql

# SQLite
cp database/database.sqlite backups/database_$(date +%Y%m%d).sqlite
```

2. **File Backups:**
```bash
# Backup uploaded files
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/
```

3. **Automated Backups:**
Set up cron job:
```bash
# Daily database backup
0 2 * * * /path/to/backup-script.sh
```

**Monitoring:**

1. **Application Logs:**
   - Monitor `storage/logs/laravel.log`
   - Set up log rotation
   - Consider centralized logging (Papertrail, Loggly)

2. **Server Monitoring:**
   - CPU, RAM, disk usage
   - Uptime monitoring
   - Response time monitoring

3. **Application Monitoring:**
   - Error tracking (Sentry, Bugsnag)
   - Performance monitoring (New Relic, DataDog)
   - Uptime monitoring (Pingdom, UptimeRobot)

---

## 7. Database

### Database Schema (ERD)

```
+-------------------------------------------------------------+
|                         users                                |
+-------------------------------------------------------------+
| id (PK)                  BIGINT                              |
| name                     VARCHAR(255)                        |
| email                    VARCHAR(255) UNIQUE                 |
| password                 VARCHAR(255)                        |
| role                     ENUM('user','admin','superadmin')   |
| organization             VARCHAR(255) NULL                   |
| client_limit             INTEGER NULL                         |
| last_login_at            TIMESTAMP NULL                       |
| email_verified_at         TIMESTAMP NULL                       |
| remember_token           VARCHAR(100) NULL                    |
| created_at               TIMESTAMP                            |
| updated_at               TIMESTAMP                            |
+-------------------------------------------------------------+
         |
         | 1
         |
         | *
+--------+-----------------------------------------------------+
|                    predictions                                |
+-------------------------------------------------------------+
| id (PK)                  BIGINT                              |
| user_id (FK)             BIGINT -> users.id                 |
| topic                     VARCHAR(255)                        |
| target                    VARCHAR(1000) NULL                 |
| input_data                JSON                               |
| prediction_horizon         ENUM(...)                          |
| source_urls                JSON NULL                          |
| uploaded_files             JSON NULL                          |
| extracted_text             TEXT NULL                          |
| prediction_result          JSON NULL                           |
| confidence_score           DECIMAL(5,4) NULL                  |
| model_used                 VARCHAR(255) NULL                  |
| processing_time            DECIMAL(8,4) NULL                  |
| status                     ENUM(...)                          |
| created_at                 TIMESTAMP                          |
| updated_at                 TIMESTAMP                          |
+-------------------------------------------------------------+
         |
         | 1
         |
         | 1
+--------+-----------------------------------------------------+
|              analysis_analytics                              |
+-------------------------------------------------------------+
| id (PK)                  BIGINT                             |
| prediction_id (FK)        BIGINT -> predictions.id           |
| user_id (FK)              BIGINT -> users.id                 |
| input_tokens              INTEGER                            |
| output_tokens              INTEGER                            |
| total_tokens               INTEGER                            |
| estimated_cost             DECIMAL(10,6)                      |
| cost_currency              VARCHAR(3)                         |
| api_response_time          DECIMAL(8,4)                        |
| total_processing_time      DECIMAL(8,4)                        |
| retry_attempts             INTEGER                            |
| retry_reason               VARCHAR(255) NULL                  |
| model_used                 VARCHAR(255)                        |
| api_endpoint               VARCHAR(500)                       |
| http_status_code           INTEGER NULL                       |
| api_error_message          TEXT NULL                          |
| input_text_length          INTEGER                            |
| scraped_urls_count         INTEGER                            |
| successful_scrapes          INTEGER                            |
| uploaded_files_count       INTEGER                            |
| total_file_size_bytes       INTEGER                            |
| user_agent                 VARCHAR(500) NULL                   |
| ip_address                 VARCHAR(45) NULL                   |
| analysis_type              VARCHAR(255)                       |
| prediction_horizon          VARCHAR(255) NULL                  |
| analysis_started_at         TIMESTAMP                          |
| analysis_completed_at       TIMESTAMP NULL                     |
| created_at                 TIMESTAMP                          |
| updated_at                 TIMESTAMP                          |
+-------------------------------------------------------------+

+-------------------------------------------------------------+
|              social_media_analyses                           |
+-------------------------------------------------------------+
| id (PK)                  BIGINT                              |
| user_id (FK)             BIGINT -> users.id                  |
| username                  VARCHAR(255)                        |
| platform_data             JSON                                |
| ai_analysis               JSON NULL                           |
| model_used                VARCHAR(255) NULL                    |
| processing_time           DECIMAL(8,4) NULL                    |
| status                    ENUM(...)                            |
| created_at                TIMESTAMP                            |
| updated_at                TIMESTAMP                            |
+-------------------------------------------------------------+

+-------------------------------------------------------------+
|                      messages                                |
+-------------------------------------------------------------+
| id (PK)                  BIGINT                              |
| sender_id (FK)           BIGINT -> users.id                   |
| recipient_id (FK)        BIGINT -> users.id                  |
| subject                  TEXT                                |
| message                  LONGTEXT                            |
| is_read                  BOOLEAN                              |
| read_at                  TIMESTAMP NULL                       |
| created_at               TIMESTAMP                            |
| updated_at               TIMESTAMP                            |
+-------------------------------------------------------------+

+-------------------------------------------------------------+
|                  system_settings                            |
+-------------------------------------------------------------+
| id (PK)                  BIGINT                              |
| key                      VARCHAR(255) UNIQUE                 |
| value                    TEXT                                |
| description               TEXT NULL                           |
| created_at               TIMESTAMP                            |
| updated_at               TIMESTAMP                            |
+-------------------------------------------------------------+
```

**Key Relationships:**
- `users` 1 → * `predictions` (one user has many predictions)
- `users` 1 → * `social_media_analyses` (one user has many analyses)
- `users` 1 → * `messages` as sender (one user sends many messages)
- `users` 1 → * `messages` as recipient (one user receives many messages)
- `predictions` 1 → 1 `analysis_analytics` (one prediction has one analytics record)
- `users` 1 → * `analysis_analytics` (one user has many analytics records)

### Migration History

**Core Tables:**
1. `0001_01_01_000000_create_users_table.php` - User accounts
2. `0001_01_01_000001_create_cache_table.php` - Cache storage
3. `0001_01_01_000002_create_jobs_table.php` - Queue jobs
4. `2024_01_01_000000_create_predictions_table.php` - Prediction analyses

**User Enhancements:**
5. `2024_01_02_000000_add_last_login_at_to_users_table.php` - Track logins
6. `2025_08_22_005520_add_role_to_users_table.php` - Role-based access
7. `2025_09_02_144800_add_organization_to_users_table.php` - Multi-org support
8. `2025_09_03_000000_add_client_limit_to_users_table.php` - Admin limits

**Prediction Enhancements:**
9. `2025_08_23_083931_add_source_url_to_predictions_table.php` - URL support
10. `2025_08_23_084520_modify_source_url_to_json_in_predictions_table.php` - Multiple URLs
11. `2025_08_24_064600_add_prediction_horizon_to_predictions_table.php` - Time horizons
12. `2025_08_25_084100_add_file_uploads_to_predictions_table.php` - File uploads
13. `2025_08_26_151529_update_predictions_status_column.php` - Enhanced statuses
14. `2025_08_26_153902_add_next_two_days_to_prediction_horizon_enum.php` - New horizon
15. `2025_09_02_091825_add_target_to_predictions_table.php` - Target focus

**Analytics & Features:**
16. `2025_08_27_000000_create_analysis_analytics_table.php` - Analytics tracking
17. `2025_09_04_084407_create_messages_table.php` - Messaging system
18. `2025_10_13_100224_add_ai_provider_to_system_settings.php` - AI provider settings
19. `2025_11_27_095238_create_social_media_analyses_table.php` - Social media feature

**Running Migrations:**
```bash
# Run all pending migrations
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Rollback and re-run
php artisan migrate:refresh
```

**Creating New Migrations:**
```bash
# Create migration
php artisan make:migration create_example_table

# Create migration with model
php artisan make:model Example -m
```

---

## 8. Operations

### Common Tasks (Step-by-Step Guides)

#### Creating a New User Account

**As Admin:**
1. Log in as admin
2. Navigate to `/admin/users`
3. Click "Create New User"
4. Fill in:
   - Name
   - Email
   - Password
   - Role: "user" (client)
   - Organization: (auto-filled from admin's org)
5. Click "Create User"

**As Super Admin:**
1. Log in as super admin
2. Navigate to `/superadmin/users` or `/superadmin/admins`
3. Follow same process, can set any role and organization

**Via Command Line:**
```bash
php artisan tinker
>>> $user = \App\Models\User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password123'),
    'role' => 'user',
    'organization' => 'Acme Corp'
]);
```

#### Changing AI Provider

**Via Super Admin Interface:**
1. Log in as super admin
2. Navigate to `/superadmin/settings`
3. Select AI provider from dropdown (Gemini or ChatGPT)
4. Click "Save Provider"
5. Optionally click "Test Connection" to verify

**Via Command Line:**
```bash
php artisan tinker
>>> \App\Services\AIServiceFactory::setProvider('chatgpt');
>>> \App\Services\AIServiceFactory::getCurrentProvider(); // Verify
```

#### Viewing Analytics

**User Analytics:**
1. Log in as user
2. Navigate to `/predictions/analytics`
3. View personal usage statistics

**Admin Analytics:**
1. Log in as admin
2. Navigate to `/admin/analytics`
3. View organization-wide statistics
4. Can filter by date range
5. Can export as CSV

**Super Admin Analytics:**
1. Log in as super admin
2. Navigate to `/superadmin/analytics`
3. View system-wide statistics
4. Can view per-user analytics
5. Can export data

#### Exporting Data

**Export Prediction as PDF:**
1. View prediction at `/predictions/{id}`
2. Click "Export PDF" button
3. PDF downloads with full analysis

**Export Analytics:**
1. Navigate to analytics page
2. Click "Export" button
3. CSV file downloads with all data

#### Managing Client Limits

**Set Client Limit for Admin:**
1. Log in as super admin
2. Navigate to `/superadmin/admins`
3. Find admin user
4. Click "Edit"
5. Set "Client Limit" (number or leave null for unlimited)
6. Save

**Check Remaining Slots:**
- Admin can see remaining client slots on their dashboard
- Or via code:
```php
$admin = \App\Models\User::where('role', 'admin')->first();
$remaining = $admin->getRemainingClientSlots();
```

#### Viewing System Logs

**Via Super Admin Interface:**
1. Log in as super admin
2. Navigate to `/superadmin/logs`
3. View recent log entries
4. Filter by log level (INFO, ERROR, DEBUG, etc.)

**Via Command Line:**
```bash
# View last 50 lines
tail -n 50 storage/logs/laravel.log

# Follow logs in real-time
tail -f storage/logs/laravel.log

# Search for errors
grep ERROR storage/logs/laravel.log
```

#### Testing AI API Connection

**Via Web Interface:**
1. Navigate to `/predictions/api/test`
2. View connection status and test results

**Via Command Line:**
```bash
php artisan tinker
>>> $service = \App\Services\AIServiceFactory::create();
>>> $result = $service->testConnection();
>>> print_r($result);
```

#### Clearing Caches

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Or clear everything at once (custom command if created)
php artisan optimize:clear
```

#### Database Backup

**SQLite:**
```bash
cp database/database.sqlite backups/database_$(date +%Y%m%d_%H%M%S).sqlite
```

**MySQL:**
```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

**PostgreSQL:**
```bash
pg_dump database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### User/Admin Manual

#### For Regular Users

**Creating a Prediction Analysis:**
1. Log in to your account
2. Click "New Prediction" or navigate to `/predictions/create`
3. Enter:
   - **Topic:** What you want to analyze (e.g., "Market trends in renewable energy")
   - **Target:** Who/what this affects (optional, e.g., "Solar panel manufacturers")
   - **Prediction Horizon:** Time period (2 days to 2 years)
   - **Input Data:** Detailed information about the topic
   - **Source URLs:** (Optional) Web links with relevant information
   - **Files:** (Optional) Upload PDFs, Excel files, or text files
4. Click "Create Prediction"
5. Wait for analysis to complete (usually 30-60 seconds)
6. View results with executive summary, predictions, risks, and recommendations

**Viewing Your Predictions:**
- Click "My Predictions" to see all your analyses
- Click on any prediction to view full details
- Use "Export PDF" to download a report
- Use "Delete" to remove old predictions

**Social Media Analysis:**
1. Navigate to "Social Media Analysis"
2. Enter a username (Facebook, Instagram, or TikTok)
3. Select platform or "Auto" to search all
4. Click "Analyze"
5. View comprehensive profile analysis

**Viewing Analytics:**
- Navigate to "Analytics" to see your usage statistics
- View token usage, costs, and performance metrics

#### For Admins

**Managing Clients:**
1. Navigate to "Users" in admin dashboard
2. Create new client accounts
3. Edit existing accounts
4. Delete accounts (if needed)
5. Monitor client activity

**Viewing Organization Analytics:**
1. Navigate to "Analytics" in admin dashboard
2. View organization-wide usage
3. See top users and analysis types
4. Export data for reporting

**Messaging:**
1. Navigate to "Messages"
2. Send messages to clients or other admins
3. View message history
4. Mark messages as read

#### For Super Admins

**System Management:**
1. Access super admin dashboard
2. Manage all admins and users
3. Configure system settings
4. View system-wide analytics
5. Monitor system health
6. Review system logs

**AI Provider Management:**
1. Navigate to "Settings"
2. Switch between Gemini and ChatGPT
3. Test API connections
4. Monitor API usage

### Maintenance Tasks

**Daily:**
- [ ] Check application logs for errors
- [ ] Monitor server resources (CPU, RAM, disk)
- [ ] Verify AI API services are operational
- [ ] Check database size and growth

**Weekly:**
- [ ] Review analytics for unusual patterns
- [ ] Check for failed predictions/analyses
- [ ] Review user activity
- [ ] Backup database
- [ ] Clear old log files (if needed)

**Monthly:**
- [ ] Review and optimize database (if needed)
- [ ] Update dependencies (composer update, npm update)
- [ ] Review security updates
- [ ] Analyze cost trends (AI API usage)
- [ ] Review and archive old data (if needed)

**As Needed:**
- [ ] Clear application caches
- [ ] Restart web server (if issues occur)
- [ ] Update API keys (if rotated)
- [ ] Scale server resources (if needed)
- [ ] Update application code

**Maintenance Commands:**
```bash
# Update dependencies
composer update
npm update

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear old data (custom command if created)
php artisan cleanup:old-predictions --days=90

# Check system health
php artisan tinker
>>> \DB::table('predictions')->count();
>>> \App\Models\User::count();
```

---

## 9. Access & Accounts

### Credential Inventory

**Required API Keys & Accounts:**

1. **Google Gemini API**
   - **Where to get:** [Google AI Studio](https://makersuite.google.com/app/apikey)
   - **Format:** Starts with "AIza"
   - **Stored in:** `.env` as `GEMINI_API_KEY`
   - **Required for:** Core prediction analysis feature
   - **Rate Limits:** 15 requests/minute (free tier)

2. **OpenAI ChatGPT API (Optional)**
   - **Where to get:** [OpenAI Platform](https://platform.openai.com/api-keys)
   - **Format:** Starts with "sk-"
   - **Stored in:** `.env` as `OPENAI_API_KEY`
   - **Required for:** Alternative AI provider option
   - **Rate Limits:** Varies by plan

3. **Apify API Token (Optional)**
   - **Where to get:** [Apify Platform](https://apify.com)
   - **Format:** Alphanumeric token
   - **Stored in:** `.env` as `APIFY_API_TOKEN`
   - **Required for:** Social media scraping feature
   - **Rate Limits:** Based on subscription plan

**Database Credentials:**
- **SQLite:** No credentials needed (file-based)
- **MySQL:** Username, password, host, database name
- **PostgreSQL:** Username, password, host, database name
- **Stored in:** `.env` file (never commit to git!)

**Server Access:**
- **SSH:** Server IP, username, SSH key or password
- **FTP/SFTP:** (If used) Host, username, password
- **Database:** (If remote) Host, port, credentials

**Application Admin Accounts:**
- **Super Admin:** Highest level access
- **Admin:** Organization-level access
- **Regular User:** Client access

**Where Credentials Are Stored:**
- **Development:** `.env` file (local machine, not in git)
- **Production:** `.env` file on server (secure, not web-accessible)
- **Version Control:** Never commit `.env` files (they're in `.gitignore`)

### Security Notes

**API Key Security:**
- ✅ Never commit API keys to version control
- ✅ Use different keys for development and production
- ✅ Rotate keys periodically
- ✅ Monitor API usage for unauthorized access
- ✅ Restrict API key permissions when possible
- ✅ Use environment variables, never hardcode

**Database Security:**
- ✅ Use strong, unique passwords
- ✅ Limit database user permissions (only what's needed)
- ✅ Use SSL connections in production
- ✅ Regular backups with encryption
- ✅ Restrict database access to application server only

**Application Security:**
- ✅ Keep Laravel and dependencies updated
- ✅ Set `APP_DEBUG=false` in production
- ✅ Use HTTPS in production (SSL certificates)
- ✅ Implement rate limiting (consider for API endpoints)
- ✅ Regular security audits
- ✅ Monitor logs for suspicious activity

**File Upload Security:**
- ✅ Validate file types and sizes
- ✅ Scan uploaded files for malware (consider)
- ✅ Store uploaded files outside web root when possible
- ✅ Use unique filenames to prevent overwrites
- ✅ Set appropriate file permissions

**User Account Security:**
- ✅ Enforce strong password policies
- ✅ Implement password reset functionality
- ✅ Track login attempts (prevent brute force)
- ✅ Use secure session management
- ✅ Implement two-factor authentication (future enhancement)

**Access Control:**
- ✅ Verify user authorization for all sensitive operations
- ✅ Use middleware for route protection
- ✅ Check user roles before allowing actions
- ✅ Log access to sensitive data
- ✅ Implement IP whitelisting for admin access (optional)

**Backup Security:**
- ✅ Encrypt backup files
- ✅ Store backups in secure location
- ✅ Test backup restoration regularly
- ✅ Maintain multiple backup copies
- ✅ Secure backup access credentials

**Monitoring:**
- ✅ Monitor for failed login attempts
- ✅ Track API usage anomalies
- ✅ Monitor server resource usage
- ✅ Set up alerts for critical errors
- ✅ Regular security log reviews

---

## 10. Glossary of Terms

### Technical Terms Explained Simply

**API (Application Programming Interface)**
- **What:** A way for different software systems to communicate with each other
- **In this project:** We use APIs to send requests to Google Gemini and OpenAI, and they send back AI-generated text

**Authentication**
- **What:** Verifying who you are (logging in)
- **In this project:** Users log in with email and password to access the system

**Authorization**
- **What:** Determining what you're allowed to do (permissions)
- **In this project:** Regular users can only see their own data, admins can manage their organization's users, super admins can manage everything

**Blade**
- **What:** Laravel's template engine for creating HTML pages
- **In this project:** All the web pages you see are created using Blade templates

**Cache**
- **What:** Storing frequently used data in fast memory to speed things up
- **In this project:** Laravel caches configuration and routes to improve performance

**Controller**
- **What:** Code that handles user requests and decides what to do
- **In this project:** `PredictionController` handles creating and viewing predictions

**Database Migration**
- **What:** Code that defines and changes the database structure
- **In this project:** Migrations create tables like `users`, `predictions`, etc.

**Eloquent ORM**
- **What:** A way to work with databases using PHP code instead of SQL queries
- **In this project:** We use Eloquent to create, read, update, and delete data

**Environment Variables (.env)**
- **What:** Configuration settings stored in a file, separate from code
- **In this project:** API keys, database credentials, and other settings are in `.env`

**Factory Pattern**
- **What:** A design pattern that creates objects without specifying the exact class
- **In this project:** `AIServiceFactory` creates either Gemini or ChatGPT service based on settings

**HTTP Request/Response**
- **What:** How web browsers and servers communicate
- **In this project:** When you click a button, your browser sends an HTTP request, and the server sends back a response (usually a web page)

**JSON (JavaScript Object Notation)**
- **What:** A format for storing and exchanging data
- **In this project:** AI responses, file metadata, and many database fields use JSON format

**Middleware**
- **What:** Code that runs before or after handling a request
- **In this project:** Authentication middleware checks if you're logged in before allowing access

**Model**
- **What:** Code that represents a database table and provides ways to work with it
- **In this project:** `User` model represents the users table, `Prediction` model represents predictions table

**MVC (Model-View-Controller)**
- **What:** A way to organize code into three parts: data (Model), display (View), and logic (Controller)
- **In this project:** Laravel uses MVC architecture

**ORM (Object-Relational Mapping)**
- **What:** A technique that lets you work with databases using object-oriented code
- **In this project:** Eloquent is Laravel's ORM

**Route**
- **What:** A URL path and what code should run when someone visits it
- **In this project:** `/predictions` route shows the predictions page, `/admin/users` shows the user management page

**Service**
- **What:** Code that handles specific business logic or external integrations
- **In this project:** `GeminiService` handles AI API calls, `FileProcessingService` handles file uploads

**Session**
- **What:** Temporary storage that remembers you're logged in as you navigate the site
- **In this project:** Laravel uses sessions to keep you logged in

**Token**
- **What:** A piece of data used for authentication or API access
- **In this project:** API keys are tokens that authenticate our requests to AI services

**Web Scraping**
- **What:** Automatically downloading and extracting information from websites
- **In this project:** We scrape content from source URLs to include in predictions

### Business Terms

**Analytics**
- **What:** The collection and analysis of data to understand usage patterns
- **In this project:** We track how many predictions are made, token usage, costs, etc.

**Client Limit**
- **What:** Maximum number of user accounts an admin can create
- **In this project:** Super admins can set limits on how many clients each admin can manage

**Confidence Score**
- **What:** A number indicating how certain the AI is about its predictions
- **In this project:** Each prediction includes a confidence score (0.0 to 1.0)

**Executive Summary**
- **What:** A brief overview of key points from a longer report
- **In this project:** AI-generated predictions include an executive summary

**Organization**
- **What:** A group of users managed by an admin
- **In this project:** Admins belong to organizations and manage users within their organization

**Prediction Horizon**
- **What:** The time period for which predictions are made
- **In this project:** Users can choose from 2 days to 2 years

**Risk Assessment**
- **What:** Identifying potential problems and their likelihood
- **In this project:** AI predictions include a risk assessment section

**Social Media Analysis**
- **What:** Examining someone's social media presence to understand their personality and professional profile
- **In this project:** We analyze Facebook, Instagram, and TikTok profiles

**Strategic Recommendations**
- **What:** Actionable advice based on analysis
- **In this project:** AI predictions include recommendations for what to do

**Target**
- **What:** The specific person, group, or thing that predictions focus on
- **In this project:** Users can specify a target to focus the analysis

### Acronyms

**API** - Application Programming Interface  
**CSRF** - Cross-Site Request Forgery (security protection)  
**CSS** - Cascading Style Sheets (styling)  
**ERD** - Entity Relationship Diagram (database design)  
**HTML** - HyperText Markup Language (web pages)  
**HTTP** - HyperText Transfer Protocol (web communication)  
**HTTPS** - HTTP Secure (encrypted web communication)  
**JSON** - JavaScript Object Notation (data format)  
**MVC** - Model-View-Controller (architecture pattern)  
**ORM** - Object-Relational Mapping  
**PDF** - Portable Document Format  
**PHP** - PHP: Hypertext Preprocessor (programming language)  
**REST** - Representational State Transfer (API style)  
**SQL** - Structured Query Language (database queries)  
**SSL** - Secure Sockets Layer (encryption)  
**UI** - User Interface  
**URL** - Uniform Resource Locator (web address)  
**UX** - User Experience  

---

## 11. Master Handover Document

### Project Summary

**Project Name:** NUJUM - AI Prediction Analysis System  
**Purpose:** AI-powered platform for generating comprehensive prediction analyses and social media intelligence  
**Technology:** Laravel 12 (PHP 8.2+), Google Gemini/OpenAI ChatGPT, SQLite/MySQL/PostgreSQL  
**Status:** Production-ready, actively maintained  
**Key Features:** Prediction analysis, social media analysis, multi-role user management, analytics tracking

### Quick Links

**Documentation:**
- This document: `OFFBOARDING_DOCUMENTATION.md`
- README: `README.md`
- Gemini Setup: `GEMINI_SETUP.md`
- ChatGPT Integration: `CHATGPT_INTEGRATION.md`
- Analytics Feature: `ANALYTICS_FEATURE.md`

**Code Locations:**
- Controllers: `app/Http/Controllers/`
- Models: `app/Models/`
- Services: `app/Services/`
- Views: `resources/views/`
- Routes: `routes/web.php`
- Migrations: `database/migrations/`
- Config: `config/`

**Important Files:**
- Environment config: `.env` (create from `.env.example`)
- Dependencies: `composer.json`, `package.json`
- Main routes: `routes/web.php`
- Database config: `config/database.php`
- Services config: `config/services.php`

**Key Endpoints:**
- Home: `/`
- Login: `/login`
- Predictions: `/predictions`
- Admin: `/admin/dashboard`
- Super Admin: `/superadmin/dashboard`
- API Test: `/predictions/api/test`
- Health Check: `/health`

### Handover Checklist

**For New Developer:**

**Setup & Configuration:**
- [ ] Repository cloned
- [ ] Dependencies installed (`composer install`, `npm install`)
- [ ] Environment configured (`.env` file created and configured)
- [ ] API keys obtained and configured (Gemini, optionally ChatGPT, Apify)
- [ ] Database set up and migrations run
- [ ] Application running locally (`php artisan serve`)
- [ ] Can log in with test user account

**Understanding:**
- [ ] Read this documentation
- [ ] Understand Laravel basics (if new to Laravel)
- [ ] Reviewed code structure and key files
- [ ] Understand AI service integration
- [ ] Understand user roles and permissions
- [ ] Tested creating a prediction
- [ ] Tested social media analysis (if Apify configured)
- [ ] Reviewed analytics system

**Access:**
- [ ] Have access to code repository
- [ ] Have API keys for AI services
- [ ] Have database access (if applicable)
- [ ] Have server access (if applicable)
- [ ] Have access to monitoring/logging tools (if applicable)

**Knowledge Transfer:**
- [ ] Met with previous developer (if possible)
- [ ] Understand deployment process
- [ ] Know who to contact for API issues
- [ ] Understand backup procedures
- [ ] Know maintenance schedule

**For Handing Over Developer:**

**Documentation:**
- [ ] This documentation is complete and up-to-date
- [ ] Code comments are clear
- [ ] Known issues are documented
- [ ] Future roadmap items are noted

**Code:**
- [ ] Code is committed to repository
- [ ] No sensitive data in code (API keys, passwords)
- [ ] Tests are written (if applicable)
- [ ] Code follows project standards

**Access:**
- [ ] New developer has repository access
- [ ] New developer has necessary API keys
- [ ] New developer has database/server access (if needed)
- [ ] Credentials are securely shared

**Communication:**
- [ ] Introduced new developer to stakeholders
- [ ] Provided contact information for support
- [ ] Shared knowledge of any quirks or workarounds
- [ ] Documented any ongoing issues or concerns

### Critical Information

**Must-Know Items:**
1. **API Keys:** Never commit to git, always use `.env` file
2. **Database:** SQLite for dev, MySQL/PostgreSQL for production
3. **AI Services:** Gemini is default, ChatGPT is optional alternative
4. **User Roles:** Three levels - user, admin, superadmin
5. **File Limits:** 10MB per file, supports PDF, Excel, CSV, TXT
6. **Rate Limits:** Gemini free tier: 15 requests/minute
7. **Security:** Always set `APP_DEBUG=false` in production

**Emergency Contacts:**
- **API Issues:** Check Google AI Studio or OpenAI Platform status pages
- **Server Issues:** Contact hosting provider
- **Database Issues:** Check database logs and connection settings
- **Application Errors:** Check `storage/logs/laravel.log`

**Important Notes:**
- The system depends on external APIs (Gemini, ChatGPT, Apify) - if they're down, features won't work
- Web scraping may fail on sites with anti-bot protection - this is expected
- Large files or analyses may take time - consider background processing for future
- SQLite is fine for development but use MySQL/PostgreSQL for production

### Next Steps for New Developer

1. **Week 1:** Complete setup, read documentation, explore codebase
2. **Week 2:** Make small changes, fix minor bugs, understand workflows
3. **Week 3:** Take on feature development, understand architecture deeply
4. **Week 4:** Be comfortable with deployment, maintenance, and troubleshooting

### Support Resources

**Laravel Documentation:** https://laravel.com/docs  
**Google Gemini API:** https://ai.google.dev/docs  
**OpenAI API:** https://platform.openai.com/docs  
**Apify Documentation:** https://docs.apify.com  
**Laravel Community:** https://laracasts.com, https://laravel.io  

---

**End of Documentation**

*This documentation is a living document. Update it as the system evolves.*

*Last Updated: 2025*  
*Version: 1.0*
