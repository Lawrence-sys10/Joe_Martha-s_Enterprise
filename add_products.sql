-- Add products script
-- Run this in your database or use the PHP script below

-- First, get the category IDs
SET @beverages_id = (SELECT id FROM categories WHERE name = 'Beverages' LIMIT 1);
SET @food_id = (SELECT id FROM categories WHERE name = 'Food Items' LIMIT 1);
SET @household_id = (SELECT id FROM categories WHERE name = 'Household' LIMIT 1);
SET @personal_id = (SELECT id FROM categories WHERE name = 'Personal Care' LIMIT 1);
SET @stationery_id = (SELECT id FROM categories WHERE name = 'Stationery' LIMIT 1);
SET @electronics_id = (SELECT id FROM categories WHERE name = 'Electronics' LIMIT 1);

-- Insert products (skip if already exists)
INSERT IGNORE INTO products (name, sku, unit_price, cost_price, stock_quantity, minimum_stock, unit, category_id, is_active, tax_rate, description, created_at, updated_at) VALUES
('Sprite 50cl', 'SPR001', 5.00, 3.50, 85, 20, 'bottle', @beverages_id, 1, 12.5, 'Refreshing lemon-lime soda', NOW(), NOW()),
('Fanta Orange 50cl', 'FAN001', 5.00, 3.50, 92, 20, 'bottle', @beverages_id, 1, 12.5, 'Orange flavored soda', NOW(), NOW()),
('Milo 400g', 'MIL001', 25.00, 18.00, 45, 10, 'tin', @beverages_id, 1, 5, 'Chocolate malt drink', NOW(), NOW()),
('Bournvita 400g', 'BOU001', 24.00, 17.00, 38, 10, 'tin', @beverages_id, 1, 5, 'Rich chocolate malt drink', NOW(), NOW()),
('Voltic Water 1.5L', 'VOL001', 3.00, 2.00, 120, 30, 'bottle', @beverages_id, 1, 0, 'Pure drinking water', NOW(), NOW()),
('Gino Tomato Mix 400g', 'GIN001', 8.00, 5.50, 75, 15, 'tin', @food_id, 1, 5, 'Tomato paste for cooking', NOW(), NOW()),
('Mackerel Sardines 125g', 'MAC001', 12.00, 8.50, 60, 20, 'tin', @food_id, 1, 5, 'Premium sardines in oil', NOW(), NOW()),
('Gari 1kg', 'GAR001', 15.00, 10.00, 50, 15, 'bag', @food_id, 1, 0, 'Fresh cassava gari', NOW(), NOW()),
('Rice 5kg', 'RIC001', 45.00, 35.00, 30, 10, 'bag', @food_id, 1, 0, 'Premium long grain rice', NOW(), NOW()),
('Sugar 1kg', 'SUG001', 12.00, 8.00, 85, 20, 'packet', @food_id, 1, 5, 'White granulated sugar', NOW(), NOW()),
('Cooking Oil 2L', 'OIL001', 35.00, 25.00, 40, 10, 'bottle', @food_id, 1, 5, 'Vegetable cooking oil', NOW(), NOW()),
('Bleach 1L', 'BLE001', 10.00, 6.00, 55, 15, 'bottle', @household_id, 1, 12.5, 'Laundry bleach', NOW(), NOW()),
('Air Freshener', 'AIR001', 18.00, 12.00, 42, 10, 'spray', @household_id, 1, 12.5, 'Room freshener spray', NOW(), NOW()),
('Insecticide Spray', 'INS001', 25.00, 18.00, 35, 10, 'can', @household_id, 1, 12.5, 'Mosquito and insect killer', NOW(), NOW()),
('Dishwashing Liquid 500ml', 'DIS001', 8.00, 4.50, 70, 20, 'bottle', @household_id, 1, 12.5, 'Kitchen dish soap', NOW(), NOW()),
('Toothpaste 100g', 'TOO001', 12.00, 7.00, 90, 25, 'tube', @personal_id, 1, 5, 'Fluoride toothpaste', NOW(), NOW()),
('Body Cream 200ml', 'BOD001', 22.00, 14.00, 48, 15, 'jar', @personal_id, 1, 5, 'Moisturizing body cream', NOW(), NOW()),
('Shampoo 250ml', 'SHA001', 18.00, 11.00, 52, 15, 'bottle', @personal_id, 1, 5, 'Hair shampoo', NOW(), NOW()),
('Deodorant', 'DEO001', 15.00, 9.00, 60, 20, 'stick', @personal_id, 1, 12.5, 'Men\'s deodorant stick', NOW(), NOW()),
('Ballpoint Pen Blue', 'PEN001', 1.50, 0.80, 200, 50, 'piece', @stationery_id, 1, 5, 'Blue ink ballpoint pen', NOW(), NOW()),
('Exercise Book 80pg', 'EXE001', 5.00, 3.00, 150, 40, 'book', @stationery_id, 1, 0, '80-page exercise book', NOW(), NOW()),
('Phone Charger', 'CHA001', 35.00, 22.00, 25, 8, 'piece', @electronics_id, 1, 12.5, 'USB phone charger', NOW(), NOW()),
('Earphones', 'EAR001', 20.00, 12.00, 30, 10, 'pair', @electronics_id, 1, 12.5, 'Wired earphones', NOW(), NOW()),
('Flashlight', 'FLA001', 25.00, 15.00, 35, 10, 'piece', @electronics_id, 1, 5, 'LED flashlight with batteries', NOW(), NOW());