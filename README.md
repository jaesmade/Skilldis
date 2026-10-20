# SkillDis

SkillDis is an inclusive job matching web app that connects job seekers, including persons with disabilities (PWDs), with employers. It helps users find opportunities through resume and skill matching, workplace accommodation filters, and application tracking.

## Features

- Separate accounts and dashboards for general job seekers, PWD job seekers, and employers.
- Resume uploads in `.txt` and `.docx` formats, with text extraction for matching.
- PWD profile and skill management, job recommendations, and accommodation filters.
- Job applications and status tracking for PWD job seekers.
- Employer tools to post and manage jobs, review candidates, and update application statuses.

## Built With

PHP, MySQL through PDO, HTML, JavaScript, Tailwind CSS, and Font Awesome.

## Local Setup

1. Install PHP with PDO MySQL, ZIP, and DOM extensions, and a MySQL server.
2. Configure the database connection in `Skilldis/db.php` and provision the database tables. A database schema or SQL import file is not included in this project.
3. Ensure `Skilldis/uploads/resumes/` is writable by the PHP server.
4. From the project root, start the local development server:

   ```sh
   php -S localhost:8000 -t Skilldis
   ```

5. Open [SkillDis locally](http://localhost:8000). Internet access is needed for external frontend assets and online matching services.
