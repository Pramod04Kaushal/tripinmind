# TripInMind – Personalized Travel Destination Recommendation System

TripInMind is a web-based travel recommendation system designed to help users discover suitable travel destinations in Sri Lanka based on their preferences, interests, and travel conditions.

The system allows users to explore destinations through categories, one day trip suggestions, seasonal recommendations, and personalized recommendation logic.

This project was developed as a Final Year Project for the IT degree program.

---

## Features

User Account System

* User registration and login
* Profile management
* Profile image upload
* Save travel preferences

Personalized Recommendation System

* Recommend destinations based on:

  * Travel purpose
  * Category preferences
  * Trip duration
  * Seasonal suitability
* Monthly recommendation suggestions

Destination Exploration

* Category based browsing
* Sub-category filtering
* One day trip suggestions
* Popular destinations based on user activity
* Seasonal travel suggestions

User Interaction Features

* Save favorite places
* Comment on destinations
* View recommendation history

Interface Features

* Responsive modern UI
* Image gallery for locations
* Scrollable trip cards
* User profile dashboard
* Dark mode toggle

---

## System Technologies

Frontend

* HTML
* CSS
* JavaScript

Backend

* PHP

Database

* MySQL

Tools

* XAMPP
* Git
* GitHub

---

## System Modules

Authentication Module

* User registration
* Login system
* Session management

Recommendation Module

* Rule-based recommendation logic
* Preference matching
* Seasonal filtering

Destination Module

* Category filtering
* Subcategory filtering
* Popular destination ranking

Profile Module

* Edit user details
* Save travel preferences
* Manage favorites
* View comments

---

## Database

The system uses a MySQL database to store:

* Users
* Locations
* Categories
* Sub categories
* User preferences
* Comments
* Favorites
* Seasonal destination data

Database file:
tripinmind.sql

---

## Installation Guide

Step 1 – Install XAMPP
Download and install XAMPP from:
https://www.apachefriends.org/

Step 2 – Copy project
Copy project folder into:
xampp/htdocs/

Example:
C:/xampp/htdocs/tripinmind

Step 3 – Create database
Open phpMyAdmin:
http://localhost/phpmyadmin

Create database:
tripinmind

Import SQL file:
tripinmind.sql

Step 4 – Configure database connection
Open:
config/db.php

Update database settings if needed:
localhost
root
password

Step 5 – Run project
Open browser and go to:
http://localhost/tripinmind

---

## Future Improvements

* Machine learning based recommendation system
* User rating system
* Google Maps integration
* Travel route planning
* Mobile app version
* Admin dashboard improvements

---

## Author

Pramod Kaushal Fernando

Final Year Undergraduate – IT

Sri Lanka

---

## Project Purpose

This system was developed for academic purposes to demonstrate skills in:

* Web development
* Database design
* Recommendation system logic
* User interface design
* Full stack development

---

## License

This project is developed for educational purposes.
