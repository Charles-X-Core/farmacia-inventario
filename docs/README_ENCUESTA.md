# Encuesta de Satisfaccion - Dielventory

## Como crear la encuesta en Google Forms

### Paso 1: Abrir Google Apps Script
1. Ve a **https://script.google.com**
2. Haz clic en **"Nuevo proyecto"**

### Paso 2: Pegar el codigo
1. Borra todo el codigo que aparece por defecto
2. Abre el archivo `encuesta_satisfaccion.gs` (esta en la carpeta `docs/` del proyecto)
3. Copia **TODO** el contenido y pegalo en el editor de Apps Script

### Paso 3: Ejecutar
1. Haz clic en el boton **"Ejecutar"** (icono de play verde)
2. Te pedira autorizar la app - haz clic en **"Revisar permisos"**
3. Selecciona tu cuenta de Google
4. Si aparece "Google no ha verificado esta app", haz clic en **"Avanzado"** -> **"Ir a Dielventory (no seguro)"**
5. Dale permisos y acepta

### Paso 4: Obtener el link
1. Ve a la pestana **"Registro"** (Execution log) en la parte inferior
2. Busca el link que empieza con `https://docs.google.com/forms/...`
3. Copia ese link y compartelo con los encuestados

### Paso 5: Ver respuestas
1. Ve a **https://forms.google.com**
2. Busca "Encuesta de Satisfaccion - Dielventory"
3. Haz clic en la pestana **"Respuestas"** para ver los resultados

## Preguntas de la encuesta (38 total)

### Datos Generales (3 preguntas)
- Rol en la farmacia
- Frecuencia de uso
- Experiencia con sistemas de inventario

### Modulo de Productos (4 preguntas)
- Facilidad para agregar productos
- Claridad del formulario
- Utilidad de busqueda/filtrado
- Mejoras sugeridas

### Modulo de Lotes (4 preguntas)
- Intuitividad del control de lotes
- Utilidad de info de vencimiento
- Reduccion de perdidas
- Mejoras sugeridas

### Modulo de Movimientos (4 preguntas)
- Facilidad de entrada de mercaderia
- Facilidad de salida de mercaderia
- Claridad del historial
- Mejoras sugeridas

### Modulo de Ventas (6 preguntas)
- Rapidez de proceso de venta
- Claridad de seleccion de productos
- Utilidad de seleccion de lotes
- Intuitividad de multiples metodos de pago
- Comparacion con metodo manual
- Mejoras sugeridas

### Modulo de Clientes (3 preguntas)
- Facilidad de busqueda
- Utilidad de opcion "Cliente Final"
- Mejoras sugeridas

### Kardex (3 preguntas)
- Claridad de informacion
- Utilidad del historial
- Mejoras sugeridas

### Interfaz y Diseno (4 preguntas)
- Navegacion entre modulos
- Diseno visual
- Dispositivo utilizado
- Mejoras sugeridas

### Satisfaccion General (6 preguntas)
- Satisfaccion general
- Recomendacion a otras farmacias
- Comparacion con metodo anterior
- Probabilidad de seguir usando
- Lo que mas gusta
- Lo que menos gusta / sugerencias

## Tipos de pregunta utilizados
- **Multiple choice** (opcion unica)
- **Escala Likert** (1-5)
- **Texto de parrafo** (abierta)
- **Secciones** (para organizar por modulo)
