# LOR Generation and Document Management System

A web-based application developed for students of DBIT to streamline the Letter of Recommendation (LOR) process and maintain academic documentation efficiently. This system allows students to request LORs, upload relevant documents, and enables faculty to manage and respond to these requests.

## 🧩 Features

- Student registration and login
- Automated LOR generation using AI
- Teacher dashboard to review and approve LORs
- Secure document upload and download (GRE, GMAT, TOEFL,etc)
- Dynamic student profile with academic achievements
- Admin panel for managing users and data

## 🛠 Tech Stack

- **Frontend:** HTML, CSS, Bootstrap, JavaScript
- **Backend:** PHP
- **AJAX:** For asynchronous requests and smoother UX
- **Database:** MySQL
- **Server:** XAMPP (Apache + MySQL)
- **Utilities:** Ghostscript (for compressing uploaded PDFs)

## 🧠 AI Integration

- Integrates with Cohere API to generate personalized LORs based on student input and achievements.

## ⚙️ Setup Instructions

1. **Install XAMPP:**
   Download and install [XAMPP](https://www.apachefriends.org/index.html).

2. **Clone or copy the project:**
   Place it in `htdocs` directory.

3. **Import MySQL Database:**
   - Open phpMyAdmin (`localhost/phpmyadmin`)
   - Create a new database: `higher_studies`
   - Import the provided `.sql` file

4. **Configure `db.php`:**
   ```php
   $conn = new mysqli("localhost", "root", "", "higher_studies");

5. **Start the Server**
   - Open XAMPP Control Panel
   - Start Apache and MySQL
   - Access the system at: http://localhost/student-lor/
  


Let me know if you also want a sample SQL schema or `.sql` export structure documented in the `README` or if you're preparing a `user manual` or `deployment guide` for your college!


