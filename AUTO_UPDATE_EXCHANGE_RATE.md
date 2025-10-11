# Sistema de Actualización Automática de Tasa de Cambio

## 🎯 Descripción

El sistema ahora actualiza **automáticamente** la tasa de cambio del dólar (USD a BsF) desde el BCV cada **4 horas**, sin necesidad de configuración externa ni intervención manual.

## ✨ Características

- ✅ **Completamente automático**: No requiere configuración de tareas programadas ni servicios externos
- ✅ **Actualización cada 4 horas**: Desde el momento de la última actualización
- ✅ **Contador en tiempo real**: Muestra cuánto tiempo falta para la próxima actualización
- ✅ **Actualización manual disponible**: Los usuarios pueden actualizar manualmente cuando lo deseen
- ✅ **Sin impacto en rendimiento**: Solo verifica y actualiza cuando es necesario
- ✅ **Funciona en cualquier servidor**: Windows, Linux, sin configuración adicional

## 🚀 ¿Cómo Funciona?

### Actualización Automática

El sistema utiliza un **middleware** que se ejecuta en cada petición web. Este middleware:

1. Verifica si han pasado 4 horas desde la última actualización
2. Si es necesario, actualiza automáticamente la tasa desde el BCV
3. Si no es necesario, permite que la petición continúe normalmente

**Importante**: La actualización solo ocurre cuando alguien está usando el sistema. Si nadie usa el sistema por más de 4 horas, la actualización ocurrirá en la primera petición que se haga después de ese tiempo.

### Contador Visual

En la página de **Gestión de Tasa de Cambio** (`/exchange-rates`), los usuarios pueden ver:

- Un contador en tiempo real que muestra cuántas horas, minutos y segundos faltan para la próxima actualización automática
- La tasa actual y cuándo fue actualizada por última vez
- Un botón para actualizar manualmente si lo desean

## 📍 Ubicación de Archivos

### Archivos Principales

1. **Middleware**: `app/Http/Middleware/AutoUpdateExchangeRate.php`
   - Verifica y actualiza automáticamente la tasa cada 4 horas

2. **Modelo**: `app/Models/ExchangeRate.php`
   - Métodos agregados:
     - `needsUpdate()`: Verifica si han pasado 4 horas
     - `getTimeUntilNextUpdate()`: Calcula el tiempo restante

3. **Controlador**: `app/Http/Controllers/ExchangeRateController.php`
   - Pasa la información del countdown a la vista

4. **Vista**: `resources/views/exchange-rates/index.blade.php`
   - Muestra el contador en tiempo real

5. **Configuración**: `bootstrap/app.php`
   - Registra el middleware en el stack web

6. **Comando Manual**: `app/Console/Commands/UpdateExchangeRateCommand.php`
   - Permite actualizar manualmente desde la línea de comandos

## 🎮 Uso

### Para Usuarios Finales

1. **Ver el estado actual**:
   - Ir a `Dashboard` → `Tasas de Cambio`
   - Ver la tasa actual y el tiempo para la próxima actualización

2. **Actualizar manualmente** (opcional):
   - Hacer clic en el botón "Actualizar Ahora"
   - El sistema actualizará la tasa y reiniciará el contador de 4 horas

3. **Dejar que se actualice automáticamente**:
   - No hacer nada, el sistema se encargará automáticamente

### Para Administradores

**Actualizar desde línea de comandos** (opcional):
```bash
php artisan exchange-rate:update
```

**Ver el estado actual**:
```bash
php artisan tinker
>>> $rate = App\Models\ExchangeRate::getCurrentRate();
>>> echo "Tasa: 1 USD = {$rate->usd_to_bsf} BsF";
>>> echo "Última actualización: {$rate->last_updated_at}";
>>> $time = $rate->getTimeUntilNextUpdate();
>>> echo "Faltan: {$time['hours']}h {$time['minutes']}m";
```

## 🔧 Configuración

### Cambiar el Intervalo de Actualización

Si deseas cambiar el intervalo de 4 horas a otro valor, edita:

1. **`app/Models/ExchangeRate.php`**, método `needsUpdate()`:
```php
// Cambiar "4" por el número de horas deseado
return now()->diffInHours($this->last_updated_at) >= 4;
```

2. **`app/Models/ExchangeRate.php`**, método `getTimeUntilNextUpdate()`:
```php
// Cambiar "4" por el número de horas deseado
$nextUpdate = $this->last_updated_at->copy()->addHours(4);
```

### Deshabilitar la Actualización Automática

Si por alguna razón necesitas deshabilitar temporalmente la actualización automática:

**Opción 1**: Comentar el middleware en `bootstrap/app.php`:
```php
// Middleware para auto-actualizar tasa de cambio cada 4 horas
// $middleware->web(append: [
//     \App\Http\Middleware\AutoUpdateExchangeRate::class,
// ]);
```

**Opción 2**: Modificar el middleware para que retorne sin hacer nada:
```php
public function handle(Request $request, Closure $next): Response
{
    // Actualización automática deshabilitada temporalmente
    // return $next($request);
    
    try {
        // ... código existente
    }
}
```

## 📊 Monitoreo

### Ver el Log de Actualizaciones

Las actualizaciones automáticas se registran en `storage/logs/laravel.log`:

```bash
# Ver las últimas actualizaciones
Get-Content storage/logs/laravel.log | Select-String "Auto-actualizando tasa"
```

Busca líneas como:
```
[2025-10-10 05:50:55] local.INFO: Auto-actualizando tasa de cambio desde BCV (4 horas transcurridas)
```

### Verificar que Funciona

1. **Método 1**: Esperar a que pasen 4 horas y verificar el log
2. **Método 2**: Modificar temporalmente el tiempo en `needsUpdate()` a 1 minuto para probar
3. **Método 3**: Usar el comando manual y verificar que actualiza correctamente

## ❓ Preguntas Frecuentes

### ¿Qué pasa si el servidor está inactivo por más de 4 horas?

La actualización ocurrirá en la primera petición web después de que pasen las 4 horas. Es decir, cuando alguien acceda al sistema.

### ¿Impacta el rendimiento?

No. El middleware solo hace una verificación rápida de la fecha. La actualización desde el BCV solo ocurre cada 4 horas y tarda aproximadamente 1 segundo.

### ¿Qué pasa si falla la API del BCV?

El sistema maneja el error gracefully y registra el fallo en el log. La tasa anterior se mantiene hasta que la API esté disponible nuevamente.

### ¿Puedo ver el tiempo restante en otras páginas?

Actualmente, el contador solo está visible en la página de gestión de tasas de cambio. Si deseas mostrarlo en otras páginas, puedes incluir el widget del contador en el layout principal.

### ¿Los clientes necesitan hacer algo especial?

No. El sistema funciona automáticamente sin ninguna configuración. Los clientes solo necesitan usar el sistema normalmente.

## 🎉 Ventajas de Esta Implementación

✅ **No requiere configuración externa**: No hay que configurar cron jobs, tareas programadas de Windows, ni servicios adicionales

✅ **Funciona en cualquier servidor**: Hosting compartido, VPS, servidor dedicado, Windows, Linux

✅ **Fácil de entregar**: Solo copiar los archivos y funciona inmediatamente

✅ **Transparente para el usuario**: Los clientes no necesitan entender nada técnico

✅ **Contador visual**: Los usuarios pueden ver cuándo será la próxima actualización

✅ **Actualización manual disponible**: Si necesitan actualizar antes, pueden hacerlo

## 📝 Notas Técnicas

- El middleware se ejecuta en **todas las rutas web** (`web` middleware group)
- Solo actualiza si `needsUpdate()` retorna `true` (4+ horas desde última actualización)
- El contador JavaScript se actualiza cada segundo en el cliente
- Al actualizar manualmente, la página se recarga para obtener el nuevo countdown
- La actualización es **asíncrona** y no bloquea la petición del usuario

## 🔒 Seguridad

El middleware solo verifica y actualiza. No expone ninguna información sensible y no permite ninguna acción maliciosa. La actualización desde el BCV es la misma que cuando un usuario autorizado hace clic en el botón.

---

**Creado**: Octubre 2025  
**Última actualización**: Octubre 2025  
**Versión**: 1.0

