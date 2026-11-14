-- Farm Management System Database Schema
-- VTU 5th Semester Mini Project
-- Database: farm_db

CREATE DATABASE IF NOT EXISTS `farm_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `farm_db`;

-- --------------------------------------------------------
-- Table structure for table `users` (Admin/User Login)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (Password: admin123)
-- Hash generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `role`) VALUES
(1, 'admin', '$2y$10$8.j6K2wE07Q7zGfK.N/vmeJzZkI3J/R7FjGZ3pWjYw5Y1Y7W1Y7W1', 'System Administrator', 'admin'),
(2, 'farmer', '$2y$10$8.j6K2wE07Q7zGfK.N/vmeJzZkI3J/R7FjGZ3pWjYw5Y1Y7W1Y7W1', 'Lead Farmer User', 'farmer');

-- --------------------------------------------------------
-- 1. Table structure for table `farmer`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `farmer` (
  `farmer_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `address` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 2. Table structure for table `crop`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `crop` (
  `crop_id` INT AUTO_INCREMENT PRIMARY KEY,
  `farmer_id` INT NOT NULL,
  `crop_name` VARCHAR(100) NOT NULL,
  `season` VARCHAR(30) NOT NULL,
  `planting_date` DATE NOT NULL,
  `harvest_date` DATE DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`farmer_id`) REFERENCES `farmer`(`farmer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 3. Table structure for table `seed`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `seed` (
  `seed_id` INT AUTO_INCREMENT PRIMARY KEY,
  `crop_id` INT NOT NULL,
  `seed_type` VARCHAR(100) NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL,
  `supplier` VARCHAR(100) NOT NULL,
  `cost` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`crop_id`) REFERENCES `crop`(`crop_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 4. Table structure for table `labour`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `labour` (
  `labour_id` INT AUTO_INCREMENT PRIMARY KEY,
  `crop_id` INT NOT NULL,
  `labour_name` VARCHAR(100) NOT NULL,
  `working_days` INT NOT NULL,
  `daily_wage` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`crop_id`) REFERENCES `crop`(`crop_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 5. Table structure for table `expense`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense` (
  `expense_id` INT AUTO_INCREMENT PRIMARY KEY,
  `crop_id` INT NOT NULL,
  `category` ENUM('Seeds','Labour','Transportation','Other') NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `expense_date` DATE NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`crop_id`) REFERENCES `crop`(`crop_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 6. Table structure for table `equipment`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `equipment` (
  `equipment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `farmer_id` INT NOT NULL,
  `equipment_name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `purchase_date` DATE NOT NULL,
  `cost` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`farmer_id`) REFERENCES `farmer`(`farmer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 7. Table structure for table `production`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `production` (
  `production_id` INT AUTO_INCREMENT PRIMARY KEY,
  `crop_id` INT NOT NULL,
  `quantity_kg` DECIMAL(10,2) NOT NULL,
  `production_date` DATE NOT NULL,
  `notes` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`crop_id`) REFERENCES `crop`(`crop_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 8. Table structure for table `sales`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sales` (
  `sale_id` INT AUTO_INCREMENT PRIMARY KEY,
  `crop_id` INT NOT NULL,
  `buyer_name` VARCHAR(100) NOT NULL,
  `quantity_kg` DECIMAL(10,2) NOT NULL,
  `price_per_kg` DECIMAL(10,2) NOT NULL,
  `sale_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`crop_id`) REFERENCES `crop`(`crop_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ========================================================
-- SAMPLE DATA FOR DEMONSTRATION & TESTING
-- ========================================================

-- Farmers
INSERT INTO `farmer` (`farmer_id`, `name`, `phone`, `address`) VALUES
(1, 'Ramesh Kumar', '9845012345', 'Hassan, Karnataka'),
(2, 'Suresh Gowda', '9880198765', 'Holenarasipura, Hassan'),
(3, 'Ananya Patel', '9448233445', 'Channarayapatna, Hassan'),
(4, 'Venkatesh Naik', '9741566778', 'Arsikere, Hassan'),
(5, 'Basavaraj Patil', '9900877665', 'Belur, Hassan');

-- Crops
INSERT INTO `crop` (`crop_id`, `farmer_id`, `crop_name`, `season`, `planting_date`, `harvest_date`) VALUES
(1, 1, 'Paddy (Rice)', 'Kharif', '2026-06-15', '2026-10-30'),
(2, 1, 'Maize', 'Rabi', '2025-11-01', '2026-03-15'),
(3, 2, 'Sugarcane', 'Annual', '2025-01-10', '2026-01-20'),
(4, 2, 'Ragi (Finger Millet)', 'Kharif', '2026-07-01', '2026-11-10'),
(5, 3, 'Cotton', 'Kharif', '2026-05-20', '2026-11-30'),
(6, 4, 'Tomatoes', 'Zaid', '2026-02-15', '2026-05-30'),
(7, 4, 'Wheat', 'Rabi', '2025-11-15', '2026-03-25'),
(8, 5, 'Turmeric', 'Annual', '2025-05-10', '2026-02-15');

-- Seeds
INSERT INTO `seed` (`seed_id`, `crop_id`, `seed_type`, `quantity`, `supplier`, `cost`) VALUES
(1, 1, 'Sona Masoori Hybrid', 25.00, 'Karnataka State Seeds Corp', 4500.00),
(2, 2, 'DeKalb C1921 Maize', 15.00, 'Pioneer Agri Seeds', 3200.00),
(3, 3, 'Co 86032 Cane Setts', 500.00, 'Hassan Sugar Factory Depot', 12000.00),
(4, 4, 'GPU 28 Ragi', 10.00, 'Raitha Samparka Kendra', 1500.00),
(5, 5, 'Bt Cotton Seed III', 8.00, 'Nuziveedu Seeds', 6400.00),
(6, 6, 'Abhinav Hybrid Tomato', 2.50, 'Syngenta Seeds', 2800.00);

-- Labour
INSERT INTO `labour` (`labour_id`, `crop_id`, `labour_name`, `working_days`, `daily_wage`) VALUES
(1, 1, 'Manjunatha & Team (4 workers)', 12, 600.00),
(2, 1, 'Weeding Team (2 workers)', 5, 500.00),
(3, 2, 'Harvester Labour', 8, 650.00),
(4, 3, 'Cane Cutting Gang', 25, 700.00),
(5, 4, 'Local Farm Hands', 6, 450.00),
(6, 6, 'Plucking & Packing Group', 10, 550.00);

-- Expenses
INSERT INTO `expense` (`expense_id`, `crop_id`, `category`, `amount`, `expense_date`, `description`) VALUES
(1, 1, 'Seeds', 4500.00, '2026-06-10', 'Sona Masoori seeds purchase'),
(2, 1, 'Labour', 7200.00, '2026-07-05', 'Planting and transplantation wages'),
(3, 1, 'Transportation', 3500.00, '2026-10-25', 'Tractor transport to APMC Market'),
(4, 2, 'Seeds', 3200.00, '2025-10-28', 'Maize seeds'),
(5, 2, 'Labour', 5200.00, '2026-03-16', 'Harvesting labour cost'),
(6, 3, 'Seeds', 12000.00, '2025-01-05', 'Sugarcane setts cost'),
(7, 3, 'Labour', 17500.00, '2026-01-22', 'Cane cutting group payment'),
(8, 3, 'Transportation', 8000.00, '2026-01-25', 'Truck freight to Sugar Mill'),
(9, 4, 'Seeds', 1500.00, '2026-06-25', 'Ragi seeds purchase'),
(10, 6, 'Seeds', 2800.00, '2026-02-10', 'Hybrid tomato seeds'),
(11, 6, 'Labour', 5500.00, '2026-05-28', 'Tomato picking workers');

-- Equipment
INSERT INTO `equipment` (`equipment_id`, `farmer_id`, `equipment_name`, `type`, `purchase_date`, `cost`) VALUES
(1, 1, 'Mahindra 575 DI Tractor', 'Tractor', '2024-03-15', 680000.00),
(2, 1, 'Power Tiller', 'Tillage', '2023-08-10', 125000.00),
(3, 2, 'Drip Irrigation Kit', 'Irrigation System', '2025-02-01', 85000.00),
(4, 3, 'Knapsack Sprayer', 'Sprayer', '2025-06-12', 4500.00),
(5, 4, 'Rotavator Attachment', 'Tillage', '2024-11-20', 95000.00);

-- Production
INSERT INTO `production` (`production_id`, `crop_id`, `quantity_kg`, `production_date`, `notes`) VALUES
(1, 1, 4500.00, '2026-10-28', 'Grade A Sona Masoori Paddy yield'),
(2, 2, 3200.00, '2026-03-18', 'Dry grain maize harvest'),
(3, 3, 42000.00, '2026-01-24', 'Sugarcane tonnage harvest'),
(4, 4, 1800.00, '2026-11-12', 'Cleaned Ragi grain'),
(5, 6, 2500.00, '2026-05-29', 'Fresh Red Tomatoes harvest');

-- Sales
INSERT INTO `sales` (`sale_id`, `crop_id`, `buyer_name`, `quantity_kg`, `price_per_kg`, `sale_date`) VALUES
(1, 1, 'APMC Hassan Wholesaler', 4000.00, 24.50, '2026-10-29'),
(2, 2, 'Karnataka Feed Mills', 3000.00, 21.00, '2026-03-20'),
(3, 3, 'Hassan Cooperative Sugar Mill', 42000.00, 3.20, '2026-01-26'),
(4, 4, 'Local Grain Merchant', 1500.00, 36.00, '2026-11-15'),
(5, 6, 'Vegetable APMC Trader', 2400.00, 18.00, '2026-05-30');
