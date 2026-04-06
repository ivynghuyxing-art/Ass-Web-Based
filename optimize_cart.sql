-- Database optimization for faster "Add to Cart" performance
-- Run these SQL commands in your MySQL database

-- Add composite index for faster cart_item lookups (WHERE cart_id = ? AND product_id = ?)
-- This significantly speeds up the check to see if product already exists in cart
ALTER TABLE `cart_item` ADD INDEX `idx_cart_product` (`cart_id`, `product_id`);

-- Ensure user_id index exists on cart table (for faster lookups when creating cart)
-- This might already exist as 'cust_id fk', but a dedicated index helps
ALTER TABLE `cart` ADD INDEX `idx_user_id` (`user_id`);

-- Optional: Add index on product for stock lookups
ALTER TABLE `product` ADD INDEX `idx_stock` (`stock_quantity`);
