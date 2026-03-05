-- Run this in phpMyAdmin to fix settings that were imported with wrong values
UPDATE prom_settings SET setting_value = 'Iwacu Garden, Kicukiro' WHERE setting_key = 'prom_venue';
UPDATE prom_settings SET setting_value = '25000' WHERE setting_key = 'ticket_price_internal';
UPDATE prom_settings SET setting_value = '30000' WHERE setting_key = 'ticket_price_external';
