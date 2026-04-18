# Cell Caribe Arg — Sitio Web

Sitio web completo para **Cell Caribe Arg**, tienda de productos Apple y servicio técnico especializado ubicada en Florida 537, Local 394, CABA.

🌐 **[cellcaribe.com.ar](https://cellcaribe.com.ar)**

---

## ¿Qué tiene el proyecto?

### Para los clientes
- Catálogo de productos con filtros por categoría y precios por variante
- Stock de iPhones usados con color, batería y precio
- Carrito de compras con formulario de pedido
- Sistema de turnos para servicio técnico con calendario de disponibilidad
- Sección de promo semanal con cuenta regresiva en tiempo real
- Buscador de productos en la navegación

### Para el local
- Panel de administración con contraseña para gestionar todo sin tocar código
- Gestión de productos, stock, pedidos, turnos y promo semanal
- Sistema de imágenes: subida desde PC o por URL

### Técnico
- Analytics con Google Analytics 4 y Microsoft Clarity
- SEO completo: Schema.org LocalBusiness, Open Graph, sitemap.xml, robots.txt
- Backend en PHP con autenticación por header
- Datos persistidos en JSON en el servidor

---

## Stack

- **Frontend:** HTML, CSS, JavaScript vanilla
- **Backend:** PHP 8+
- **Datos:** JSON (sin base de datos)
- **Hosting:** Hostinger (shared hosting)
- **Analytics:** GA4 + Microsoft Clarity

---

## Estructura

```
cellcaribearg/
├── index.html          # Sitio principal
├── admin.html          # Panel de administración
├── save.php            # Guarda productos y stock
├── save_order.php      # Gestión de pedidos
├── save_turno.php      # Gestión de turnos
├── save_promo.php      # Gestión de promo semanal
├── upload_img.php      # Subida de imágenes
├── products.json       # Catálogo de productos
├── stock.json          # Stock nuevo y usado
├── orders.json         # Pedidos recibidos
├── turnos.json         # Turnos agendados
├── promo.json          # Config promo semanal
├── imgs/               # Imágenes subidas desde el admin
├── sitemap.xml
└── robots.txt
```

---

## Instalación en servidor

1. Subir todos los archivos a `public_html` vía FTP o el administrador de archivos
2. Asegurarse que los `.json` tienen permisos de escritura (`chmod 644`)
3. La carpeta `imgs/` necesita permisos `755`
4. Acceder al panel en `tudominio.com/admin.html`

---

## Contacto

📍 Florida 537, Local 394 · Buenos Aires  
📱 +54 11 6826-5794  
📸 [@cellcaribearg](https://instagram.com/cellcaribearg)
