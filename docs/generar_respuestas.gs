/**
 * GENERADOR DE RESPUESTAS FICTICIAS - Dielventory
 *
 * INSTRUCCIONES:
 * 1. Ve a https://script.google.com
 * 2. Crea un nuevo proyecto
 * 3. Pega este codigo
 * 4. Ejecuta la funcion "generarRespuestas"
 * 5. Se crea un Google Sheet con 10 respuestas automaticamente
 */

function generarRespuestas() {
  var spreadsheet = SpreadsheetApp.create('Encuesta Satisfaccion - Dielventory (10 Respuestas)');
  var sheet = spreadsheet.getActiveSheet();
  sheet.setName('Respuestas');

  // Headers
  var headers = [
    'ID',
    'Fecha',
    '1. Rol',
    '2. Frecuencia de uso',
    '3. Experiencia',
    '4. Facilidad agregar producto (1-5)',
    '5. Claridad formulario (1-5)',
    '6. Utilidad busqueda (1-5)',
    '7. Mejoras productos',
    '8. Intuitividad lotes (1-5)',
    '9. Utilidad vencimiento (1-5)',
    '10. Reduce perdidas',
    '11. Mejoras lotes',
    '12. Facilidad entrada mercaderia (1-5)',
    '13. Facilidad salida mercaderia (1-5)',
    '14. Claridad historial movimientos (1-5)',
    '15. Mejoras movimientos',
    '16. Rapidez venta (1-5)',
    '17. Claridad seleccion productos (1-5)',
    '18. Utilidad seleccion lotes (1-5)',
    '19. Intuitividad multiples pagos (1-5)',
    '20. Comparacion metodo manual',
    '21. Mejoras ventas',
    '22. Facilidad busqueda cliente (1-5)',
    '23. Utilidad Cliente Final (1-5)',
    '24. Mejoras clientes',
    '25. Claridad kardex (1-5)',
    '26. Utilidad historial producto (1-5)',
    '27. Mejoras kardex',
    '28. Facilidad navegacion (1-5)',
    '29. Diseno visual (1-5)',
    '30. Dispositivo',
    '31. Mejoras interfaz',
    '32. Satisfaccion general (1-5)',
    '33. Recomendaria',
    '34. Comparacion metodo anterior',
    '35. Probabilidad seguir usando (1-5)',
    '36. Lo que mas gusta',
    '37. Lo que menos gusta',
    '38. Otros comentarios'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers]);
  sheet.getRange(1, 1, 1, headers.length).setFontWeight('bold');
  sheet.getRange(1, 1, 1, headers.length).setBackground('#4285F4');
  sheet.getRange(1, 1, 1, headers.length).setFontColor('#FFFFFF');
  sheet.setFrozenRows(1);

  // 10 respuestas ficticias
  var respuestas = [
    // RESPUESTA 1 - Administrador experto
    [
      1, '2026-07-01',
      'Administrador', 'Diariamente', 'Mas de 3 anos',
      5, 5, 5, 'Podria agregar productos mas rapido con codigo de barras',
      5, 5, 'Si, significativamente', 'Que muestre alertas automaticas de vencimiento por email',
      5, 5, 5, 'Nada, esta perfecto',
      5, 5, 5, 5, 'Si, mucho mas rapido', 'Integrar con impresora termica directa',
      5, 5, 'Poder crear clientes rapidamente desde la venta',
      5, 5, 'Que tenga graficos de movimientos',
      5, 5, 'Computadora de escritorio', 'Todo esta bien, interface moderna',
      5, 'Si, definitivamente', 'Mucho mejor', 5,
      'Rapidez en ventas, control de lotes, interfaz intuitiva',
      'A veces carga leno en horas pico', 'Estan haciendo un excelente trabajo'
    ],
    // RESPUESTA 2 - Vendedor nuevo
    [
      2, '2026-07-01',
      'Vendedor / Cajero', 'Diariamente', 'Menos de 1 ano',
      4, 4, 5, 'Que tenga un boton de favoritos para productos frecuentes',
      4, 5, 'Si, un poco', 'Mostrar dias restantes en grande',
      5, 4, 4, 'Que tenga filtros por fecha',
      5, 4, 5, 4, 'Si, mucho mas rapido', 'Que sea mas rapido el buscador de productos',
      5, 4, 'Esta bien asi',
      4, 5, 'Nada que mejorar',
      5, 4, 'Computadora de escritorio', 'Los colores estan muy bonitos',
      4, 'Si, definitivamente', 'Mucho mejor', 5,
      'Facil de usar, rapido, buen diseno',
      'A veces no encuentra el producto que busco', 'Seguid asi'
    ],
    // RESPUESTA 3 - Almacenero experimentado
    [
      3, '2026-07-01',
      'Almacenero / Encargado de stock', 'Varias veces por semana', '1 a 3 anos',
      5, 4, 4, 'Poder ver stock de todos los almacenes juntos',
      4, 5, 'Si, significativamente', 'Que muestre el lote mas proximo a vencer primero',
      4, 5, 4, 'Agregar motivo de salida',
      4, 4, 5, 4, 'Si, un poco mas rapido', 'Que al buscar producto muestre el stock disponible',
      5, 5, 'Que permita guardar clientes frecuentes',
      5, 4, 'Graficos de entradas vs salidas',
      4, 5, 'Laptop', 'Nada, esta muy bien',
      4, 'Si, definitivamente', 'Mucho mejor', 5,
      'Control de lotes, kardex, facilidad de uso',
      'A veces se confunde con los numeros de lote', 'Muy buen sistema para la farmacia'
    ],
    // RESPUESTA 4 - Vendedor sin experiencia
    [
      4, '2026-07-01',
      'Vendedor / Cajero', 'Una vez por semana', 'Ninguna (este es mi primer sistema)',
      3, 4, 3, 'Mas tutoriales o guias en pantalla',
      3, 4, 'No lo noto', 'Que explique mejor que es cada cosa',
      3, 3, 3, 'Que tenga instrucciones paso a paso',
      3, 4, 3, 3, 'Mas o menos igual', 'Me cuesta un poco encontrar las cosas',
      3, 5, 'Que sea mas claro donde buscar',
      3, 4, 'No entiendo el kardex',
      3, 4, 'Celular', 'Que sea responsive, se vea bien en celular',
      3, 'Probablemente si', 'Algo mejor', 3,
      'Lo poco que use es facil',
      'No se usar el kardex, me confundo con los movimientos', 'Necesito mas capacitacion'
    ],
    // RESPUESTA 5 - Gerente
    [
      5, '2026-07-01',
      'Gerente', 'Varias veces por semana', 'Mas de 3 anos',
      5, 5, 5, 'Reportes de ventas por dia/mes',
      5, 5, 'Si, significativamente', 'Que genere reportes automaticos de vencidos',
      5, 5, 5, 'Exportar movimientos a Excel',
      5, 5, 5, 5, 'Si, mucho mas rapido', 'Dashboard con metricas de ventas',
      5, 5, 'Historial de compras por cliente',
      5, 5, 'Graficos de tendencias',
      5, 5, 'Computadora de escritorio', 'Profesional y moderna',
      5, 'Si, definitivamente', 'Mucho mejor', 5,
      'Todo: ventas, lotes, kardex, clientes',
      'Le falta reportes gerenciales', 'Excelente para farmacia'
    ],
    // RESPUESTA 6 - Vendedor rapido
    [
      6, '2026-07-01',
      'Vendedor / Cajero', 'Diariamente', 'Menos de 1 ano',
      4, 5, 4, 'Que al escanear codigo de barras lo agregue solo',
      4, 4, 'Si, un poco', 'Destacar mas los que estan por vencer',
      4, 4, 5, 'Filtro por tipo de movimiento',
      4, 5, 4, 4, 'Si, mucho mas rapido', 'Que tenga acceso rapido a productos populares',
      4, 4, 'Una busqueda mas rapida',
      4, 5, 'Nada',
      4, 4, 'Computadora de escritorio', 'Esta bien, nada que criticar',
      4, 'Si, definitivamente', 'Mucho mejor', 4,
      'Rapido, facil, bonito',
      'A veces se demora buscando', 'Sigue mejorando'
    ],
    // RESPUESTA 7 - Administrador perfectionista
    [
      7, '2026-07-01',
      'Administrador', 'Diariamente', '1 a 3 anos',
      5, 5, 5, 'Que tenga modulos de promociones/descuentos',
      5, 5, 'Si, significativamente', 'Alertas de stock minimo por producto',
      5, 5, 5, 'Historial completo con todos los filtros',
      5, 5, 5, 5, 'Si, mucho mas rapido', 'Integrar con balanza para pesados',
      5, 5, 'Campos personalizables por cliente',
      5, 5, 'Reportes en formato PDF mas detallados',
      5, 5, 'Computadora de escritorio', 'Excelente interfaz, muy profesional',
      5, 'Si, definitivamente', 'Mucho mejor', 5,
      'Velocidad, precision, control total',
      'Nada por ahora', 'Perfecto para farmacia, lo recomiendo al 100%'
    ],
    // RESPUESTA 8 - Almacenero mayor
    [
      8, '2026-07-01',
      'Almacenero / Encargado de stock', 'Rara vez / Solo cuando es necesario', 'Menos de 1 ano',
      3, 3, 3, 'Que sea mas simple, menos opciones',
      3, 3, 'No lo noto', 'No entiendo como funciona bien',
      3, 3, 3, 'No se usar el sistema bien',
      2, 3, 3, 2, 'Mas o menos igual', 'Me gusta mas el cuaderno',
      3, 4, 'Que sea igual al cuaderno',
      3, 3, 'Que sea mas simple',
      3, 3, 'Computadora de escritorio', 'Me parece complicada',
      3, 'No estoy seguro', 'Igual', 2,
      'Lo poco que entendi es util',
      'Me cuesta entenderlo, soy mayor y no manejo bien la compu', 'Necesito que alguien me explique paso a paso'
    ],
    // RESPUESTA 9 - Vendedor constante
    [
      9, '2026-07-01',
      'Vendedor / Cajero', 'Diariamente', '1 a 3 anos',
      5, 4, 5, 'Que guarde los ultimos productos buscados',
      4, 5, 'Si, significativamente', 'Colores diferentes por estado de vencimiento',
      4, 5, 5, 'Filtro por rango de fechas',
      5, 5, 5, 5, 'Si, mucho mas rapido', 'Nada, funciona perfecto',
      4, 5, 'Esta bien',
      5, 5, 'Nada',
      5, 4, 'Computadora de escritorio', 'Limpia y ordenada',
      5, 'Si, definitivamente', 'Mucho mejor', 5,
      'Rapidez, lotes, busqueda de clientes',
      'Nada que mejorar', 'Muy buen trabajo, lo uso todos los dias sin problemas'
    ],
    // RESPUESTA 10 - Otro rol
    [
      10, '2026-07-01',
      'Otro', 'Varias veces por semana', '1 a 3 anos',
      4, 4, 4, 'Que tenga modulo de proveedores',
      4, 4, 'Si, un poco', 'Falta contacto con proveedores',
      4, 4, 4, 'Que tenga alertas de stock minimo',
      4, 4, 4, 4, 'Si, un poco mas rapido', 'Modulo de pedidos a proveedores',
      4, 4, 'Integrar con WhatsApp para notificar clientes',
      4, 4, 'Graficos de productos mas vendidos',
      4, 4, 'Laptop', 'Esta bien, le faltan algunas cosas',
      4, 'Probablemente si', 'Algo mejor', 4,
      'Organizacion, rapidez, control',
      'Le falta modulo de proveedores y reportes', 'Buen sistema, pero necesita crecer'
    ]
  ];

  // Escribir datos
  for (var i = 0; i < respuestas.length; i++) {
    sheet.getRange(i + 2, 1, 1, respuestas[i].length).setValues([respuestas[i]]);
  }

  // Ajustar columnas
  sheet.autoResizeColumns(1, headers.length);

  // Formato condicional para escalas 1-5
  var scaleRange = sheet.getRange(2, 6, respuestas.length, 4); // cols 6-9
  // ... (se pueden agregar reglas de formato despues)

  Logger.log('====================================');
  Logger.log('HOJA DE CREADA!');
  Logger.log('URL: ' + spreadsheet.getUrl());
  Logger.log('====================================');

  console.log('====================================');
  console.log('RESPUESTAS CREADAS!');
  console.log('URL del Sheet:');
  console.log(spreadsheet.getUrl());
  console.log('====================================');

  return spreadsheet.getUrl();
}
