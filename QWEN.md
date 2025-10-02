# Berani ERP Platform

## Project Overview
This is the Berani ERP Platform, a comprehensive enterprise resource planning system built with Laravel. The platform includes various modules including a recruitment system.

## Current Work Focus
We are currently working on the recruitment module, specifically focused on:
- Job listings and applications
- Customer-facing career pages
- Application forms and validation
- Job application management

## Recruitment Plugin
The recruitment functionality is organized in a plugin structure:
- Plugin directory: `plugins/webkul/recruitments/`
- Contains controllers, models, views, routes, and configuration
- Handles job listings, applications, and related functionality

## Key Files Currently Being Worked On
- `resources/views/customer/layouts/app.blade.php` - Customer layout for recruitment pages
- `routes/web.php` - Web routes for recruitment
- `src/Http/Requests/StoreJobApplicationRequest.php` - Form request for job applications
- `src/Http/Controllers/CustomerJobController.php` - Controller handling job-related operations
- Various view files for career pages and job details