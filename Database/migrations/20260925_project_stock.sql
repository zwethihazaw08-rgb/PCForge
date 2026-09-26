-- User-authorized starting inventory for this school project.
-- 10 units is an assumption, not supplier-verified physical stock.
-- Only imported, active products with unknown stock are initialized.
-- Existing counted stock (including zero) is preserved; reruns are safe.
START TRANSACTION;
UPDATE cpu p JOIN product_data_sources s ON s.category='cpu' AND s.product_id=p.id SET p.stock=10 WHERE p.status='active' AND p.stock IS NULL;
UPDATE gpu p JOIN product_data_sources s ON s.category='gpu' AND s.product_id=p.id SET p.stock=10 WHERE p.status='active' AND p.stock IS NULL;
UPDATE mb p JOIN product_data_sources s ON s.category='mb' AND s.product_id=p.id SET p.stock=10 WHERE p.status='active' AND p.stock IS NULL;
UPDATE memory p JOIN product_data_sources s ON s.category='memory' AND s.product_id=p.id SET p.stock=10 WHERE p.status='active' AND p.stock IS NULL;
UPDATE storage p JOIN product_data_sources s ON s.category='storage' AND s.product_id=p.id SET p.stock=10 WHERE p.status='active' AND p.stock IS NULL;
UPDATE psu p JOIN product_data_sources s ON s.category='psu' AND s.product_id=p.id SET p.stock=10 WHERE p.status='active' AND p.stock IS NULL;
UPDATE case_box p JOIN product_data_sources s ON s.category='case_box' AND s.product_id=p.id SET p.stock=10 WHERE p.status='active' AND p.stock IS NULL;
UPDATE cooling p JOIN product_data_sources s ON s.category='cooling' AND s.product_id=p.id SET p.stock=10 WHERE p.status='active' AND p.stock IS NULL;
UPDATE monitor p JOIN product_data_sources s ON s.category='monitor' AND s.product_id=p.id SET p.stock=10 WHERE p.status='active' AND p.stock IS NULL;
COMMIT;
