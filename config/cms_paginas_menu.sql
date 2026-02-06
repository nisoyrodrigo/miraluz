-- =====================================================
-- SQL para crear las páginas del menú principal
-- Solo inserta las NUEVAS plantillas y artículos
-- (seguimiento, pedido, facturacion ya existen)
-- Ejecutar una sola vez en la base de datos
-- =====================================================

-- 1. Insertar SOLO las nuevas plantillas (template_type = 1 significa archivo PHP)
INSERT INTO cms_template (name, content, template_type, user, created) VALUES
('Catálogo de Servicios', 'catalogo', 1, 1, NOW()),
('Productos', 'productos', 1, 1, NOW()),
('Sucursales', 'sucursales', 1, 1, NOW()),
('Promociones', 'promos', 1, 1, NOW()),
('Blog', 'blog', 1, 1, NOW()),
('Citas', 'citas', 1, 1, NOW()),
('Home 2 (Nuevo Diseño)', 'home2', 1, 1, NOW());

-- 2. Obtener los IDs de las plantillas
SET @tpl_catalogo = (SELECT id FROM cms_template WHERE content = 'catalogo' LIMIT 1);
SET @tpl_productos = (SELECT id FROM cms_template WHERE content = 'productos' LIMIT 1);
SET @tpl_sucursales = (SELECT id FROM cms_template WHERE content = 'sucursales' LIMIT 1);
SET @tpl_promos = (SELECT id FROM cms_template WHERE content = 'promos' LIMIT 1);
SET @tpl_blog = (SELECT id FROM cms_template WHERE content = 'blog' LIMIT 1);
SET @tpl_citas = (SELECT id FROM cms_template WHERE content = 'citas' LIMIT 1);
SET @tpl_home2 = (SELECT id FROM cms_template WHERE content = 'home2' LIMIT 1);

-- 3. Insertar SOLO los nuevos artículos (content_type = 2 significa usar plantilla)
INSERT INTO cms_content (name, url, content_type, template, user, created) VALUES
('Catálogo de Servicios', 'catalogo', 2, @tpl_catalogo, 1, NOW()),
('Productos', 'productos', 2, @tpl_productos, 1, NOW()),
('Sucursales', 'sucursales', 2, @tpl_sucursales, 1, NOW()),
('Promociones', 'promos', 2, @tpl_promos, 1, NOW()),
('Blog', 'blog', 2, @tpl_blog, 1, NOW()),
('Agendar Cita', 'citas', 2, @tpl_citas, 1, NOW()),
('Home Nuevo Diseño', 'home2', 2, @tpl_home2, 1, NOW());

-- 4. Verificar los artículos insertados
SELECT c.id, c.name, c.url, t.name as template_name
FROM cms_content c
JOIN cms_template t ON c.template = t.id
WHERE c.url IN ('catalogo', 'productos', 'sucursales', 'promos', 'blog', 'citas', 'home2')
ORDER BY c.id;
