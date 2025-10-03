CREATE DATABASE taskforce
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE taskforce;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(256) NOT NULL,
  email VARCHAR(128) NOT NULL UNIQUE,
  password VARCHAR(128) NOT NULL,
  town_id INT NOT NULL,
  is_executor BOOLEAN NOT NULL,
  reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  avatar VARCHAR(256),
  birth_date TIMESTAMP,
  phone VARCHAR(64),
  telegram VARCHAR(128),
  information TEXT(1024)
);

CREATE TABLE towns (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL,
  rus_name VARCHAR(128) NOT NULL
);

CREATE TABLE executor_category (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  category_id INT NOT NULL
);

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(256) NOT NULL,
  description TEXT(2048) NOT NULL,
  category_id INT NOT NULL,
  location_id INT,
  budget INT,
  deadline TIMESTAMP,
  customer_id INT NOT NULL,
  executor_id INT,
  status_id INT NOT NULL,
  date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE locations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  latitude DECIMAL(10, 8) NOT NULL,
  longitude DECIMAL(10, 8) NOT NULL,
  town_id INT NOT NULL
);

CREATE TABLE tasks_files (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  file_path VARCHAR(256) NOT NULL,
  file_size INT NOT NULL,
  user_filename VARCHAR(256) NOT NULL
);

CREATE TABLE task_statuses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL,
  rus_name VARCHAR(128) NOT NULL
);

CREATE TABLE task_responses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  executor_id INT NOT NULL,
  description TEXT(1024),
  budget INT NOT NULL,
  accepted BOOLEAN NOT NULL,
  date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customer_reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  executor_id INT NOT NULL,
  task_id INT NOT NULL,
  description TEXT(1024),
  rating INT NOT NULL,
  date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

