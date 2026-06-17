Smart Interview Preparation System 🎓

A modern, dashboard-driven web platform designed to help candidates prepare for job interviews through timed multiple-choice mock quizzes. The system provides a seamless experience for students to track their progress and offers administrators powerful tools to manage question banks and analyze performance data.

🌟 Key Features
👨‍🎓 Student Portal
Secure Authentication: User registration and login system.

Interactive Dashboard: Real-time overview of total quiz attempts, average score, highest score, and the total number of available questions.

Customizable Mock Interviews: * Take timed multiple-choice quizzes (e.g., 10 minutes for 10 questions).

Filter questions by Category (e.g., HR, Technical, Soft Skills) and Difficulty Level.

Progress Tracking: View a detailed history of past quiz attempts, including dates, scores, and overall accuracy percentages.

Account Management: Secure portal to update passwords.

👨‍💻 Admin Portal
Admin Dashboard: High-level analytics showing total registered students, total questions available, and total quiz submissions across the platform.

Question Bank Management: * Add Questions: Create new MCQs with categories, difficulty levels, four options, and a defined correct answer.

Edit/Delete: Full CRUD (Create, Read, Update, Delete) operations to easily maintain and update the question bank.

Student Analytics: View comprehensive tables of all student quiz results.

Data Export: Export all quiz results to a CSV file for external reporting and analysis.

Security: Secure admin login and password management capabilities.

🛠️ Technologies Used
Frontend: HTML5, CSS3, Vanilla JavaScript

Backend: PHP (Core)

Database: SQL (MySQL / MariaDB)

Styling: Custom CSS (Dark Theme UI)

🚀 Installation & Setup
To run this project locally, you will need a local server environment like XAMPP

1. Clone the Repository
Clone this project into your local server's root directory (e.g., the htdocs folder if you are using XAMPP, or www for WAMP).
```bash
git clone https://github.com/Bhuvan4545/smart-interview-system.git
cd smart-interview-system
```

2. Configure Environment
Copy the example environment file and fill in your database credentials:
```bash
cp .env.example .env
# Edit .env with your DB credentials and a strong admin seed password
```

3. Import the Database
Import `DB.sql` into MySQL / MariaDB to create the required tables.

4. Run the Application
Open your web browser and navigate to the project folder on your localhost:
```
http://localhost/smart-interview-system
```

🔒 Admin Setup
The first admin account is seeded automatically from the `ADMIN_SEED_EMAIL` and
`ADMIN_SEED_PASS` environment variables (only when the admin table is empty).
**Never commit real credentials to the repository.**
