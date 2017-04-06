uwhvz
=====

UWaterloo Humans vs. Zombies web system

Local Install Instructions (Windows)
====================================

1. Install XAMPP (https://www.apachefriends.org/index.html)
2. Clone this repository into the "htdocs" directory in the XAMPP install.
3. Renamed the repository directory from HvZ-Website to hvz
4. Run XAMPP control panel as administrator
5. Open the shell (button on the right)
6. Run the query 'pear install http_request2'
7. Close the shell and close XAMPP
8. Open XAMPP not as an administrator this time.
9. Start Apache and MySQL
10. Open a browser and navigate to http://localhost/phpmyadmin
11. Click the Import tab
12. Click "Choose File" and select database_init.sql in the root of this repository
13. Once it completes, navigate to localhost/hvz
14. Login with the email "test@fakemail.com" and the password "oraclegoose"