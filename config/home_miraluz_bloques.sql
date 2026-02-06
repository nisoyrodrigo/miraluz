-- =====================================================
-- SQL para insertar bloques del nuevo Home Miraluz
-- Ejecutar una sola vez en la base de datos
-- =====================================================

-- 1. Crear la región Body si no existe
INSERT IGNORE INTO cms_region (name, user, created)
VALUES ('Body', 1, NOW());

-- 2. Obtener el ID de la región Body
SET @region_id = (SELECT id FROM cms_region WHERE name = 'Body' LIMIT 1);

-- 3. Eliminar bloques anteriores del home (opcional - comenta si no quieres eliminar)
-- DELETE FROM cms_block WHERE region = @region_id AND name LIKE 'Home %';

-- 4. Insertar los nuevos bloques
-- content_type = 2 significa archivo PHP en templates/block/
-- El campo content debe contener el nombre del archivo (sin .tpl.php)
-- show_in = 'front' para que solo aparezcan en la página principal
-- iquals_show_in = 1 significa "mostrar solo en estas páginas"

INSERT INTO cms_block (name, region, content, content_type, order_block, show_in, iquals_show_in, user, created) VALUES
('Home Hero Slider', @region_id, 'home_hero', 2, 1, 'front', 1, 1, NOW()),
('Home Stats Bar', @region_id, 'home_stats', 2, 2, 'front', 1, 1, NOW()),
('Home Servicios', @region_id, 'home_servicios', 2, 3, 'front', 1, 1, NOW()),
('Home Productos', @region_id, 'home_productos', 2, 4, 'front', 1, 1, NOW()),
('Home Sucursales', @region_id, 'home_sucursales', 2, 5, 'front', 1, 1, NOW()),
('Home Promociones', @region_id, 'home_promos', 2, 6, 'front', 1, 1, NOW()),
('Home Blog', @region_id, 'home_blog', 2, 7, 'front', 1, 1, NOW()),
('Home Citas', @region_id, 'home_citas', 2, 8, 'front', 1, 1, NOW()),
('Home Lealtad', @region_id, 'home_lealtad', 2, 9, 'front', 1, 1, NOW()),
('Home Mi Cuenta', @region_id, 'home_cuenta', 2, 10, 'front', 1, 1, NOW()),
('Home CTA Final', @region_id, 'home_cta', 2, 11, 'front', 1, 1, NOW());

-- 5. Verificar los bloques insertados
SELECT b.id, b.name, b.content, b.order_block, r.name as region
FROM cms_block b
JOIN cms_region r ON b.region = r.id
WHERE r.name = 'Body'
ORDER BY b.order_block;
