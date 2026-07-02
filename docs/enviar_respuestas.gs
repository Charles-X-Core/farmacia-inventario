/**
 * ENVIO AUTOMATICO DE RESPUESTAS - Dielventory
 *
 * Este script envia 10 respuestas automaticamente al Google Forms real:
 * https://docs.google.com/forms/d/e/1FAIpQLSfuhBBIToC--3b0mGcbhWKdTraOxGTiOeBaLAtmXzzHDCYUIQ/viewform
 *
 * INSTRUCCIONES:
 * 1. Ve a https://script.google.com
 * 2. Crea un nuevo proyecto
 * 3. Pega este codigo
 * 4. Ejecuta la funcion "enviarRespuestas"
 * 5. Se enviaran 10 respuestas al forms
 */

function enviarRespuestas() {
  var formURL = 'https://docs.google.com/forms/d/e/1FAIpQLSfuhBBIToC--3b0mGcbhWKdTraOxGTiOeBaLAtmXzzHDCYUIQ/formResponse';

  // Primero necesitamos obtener los entry IDs del forms
  // Accedemos al forms como si fuéramos un usuario
  var formHTML = UrlFetchApp.fetch('https://docs.google.com/forms/d/e/1FAIpQLSfuhBBIToC--3b0mGcbhWKdTraOxGTiOeBaLAtmXzzHDCYUIQ/viewform').getContentText();

  // Extraemos los entry IDs del HTML
  var entryMatches = formHTML.match(/name="entry\.(\d+)"/g);
  if (!entryMatches) {
    Logger.log('No se encontraron entry IDs. El forms puede estar protegido.');
    return;
  }

  var entryIds = entryMatches.map(function(match) {
    return match.replace('name="entry.', '').replace('"', '');
  });

  Logger.log('Entry IDs encontrados: ' + entryIds.length);
  Logger.log(entryIds);

  // Respuestas: cada sub-array es una respuesta completa
  // El orden deve coincidir con las preguntas del forms
  var respuestas = [
    // RESPUESTA 1 - Administrador experto
    [
      'Administrador',          // 1. Rol
      'Diariamente',            // 2. Frecuencia
      'Mas de 3 anos',          // 3. Experiencia
      '5',                      // 4. Facilidad producto
      '5',                      // 5. Claridad formulario
      '5',                      // 6. Utilidad busqueda
      'Podria agregar productos mas rapido con codigo de barras', // 7. Mejoras productos
      '5',                      // 8. Intuitividad lotes
      '5',                      // 9. Utilidad vencimiento
      'Si, significativamente', // 10. Reduce perdidas
      'Que muestre alertas automaticas de vencimiento por email', // 11. Mejoras lotes
      '5',                      // 12. Facilidad entrada
      '5',                      // 13. Facilidad salida
      '5',                      // 14. Claridad historial
      'Nada, esta perfecto',    // 15. Mejoras movimientos
      '5',                      // 16. Rapidez venta
      '5',                      // 17. Claridad seleccion productos
      '5',                      // 18. Utilidad seleccion lotes
      '5',                      // 19. Intuitividad pagos
      'Si, mucho mas rapido',   // 20. Comparacion manual
      'Integrar con impresora termica directa', // 21. Mejoras ventas
      '5',                      // 22. Facilidad busqueda cliente
      '5',                      // 23. Utilidad Cliente Final
      'Poder crear clientes rapidamente desde la venta', // 24. Mejoras clientes
      '5',                      // 25. Claridad kardex
      '5',                      // 26. Utilidad historial producto
      'Que tenga graficos de movimientos', // 27. Mejoras kardex
      '5',                      // 28. Facilidad navegacion
      '5',                      // 29. Diseno visual
      'Computadora de escritorio', // 30. Dispositivo
      'Todo esta bien, interface moderna', // 31. Mejoras interfaz
      '5',                      // 32. Satisfaccion general
      'Si, definitivamente',    // 33. Recomendaria
      'Mucho mejor',            // 34. Comparacion anterior
      '5',                      // 35. Probabilidad seguir usando
      'Rapidez en ventas, control de lotes, interfaz intuitiva', // 36. Lo que mas gusta
      'A veces carga lento en horas pico', // 37. Lo que menos gusta
      'Estan haciendo un excelente trabajo'  // 38. Otros comentarios
    ],
    // RESPUESTA 2 - Vendedor nuevo
    [
      'Vendedor / Cajero', 'Diariamente', 'Menos de 1 ano',
      '4', '4', '5',
      'Que tenga un boton de favoritos para productos frecuentes',
      '4', '5', 'Si, un poco',
      'Mostrar dias restantes en grande',
      '5', '4', '4',
      'Que tenga filtros por fecha',
      '5', '4', '5', '4',
      'Si, mucho mas rapido',
      'Que sea mas rapido el buscador de productos',
      '5', '4',
      'Esta bien asi',
      '4', '5',
      'Nada que mejorar',
      '5', '4',
      'Computadora de escritorio',
      'Los colores estan muy bonitos',
      '4', 'Si, definitivamente', 'Mucho mejor', '5',
      'Facil de usar, rapido, buen diseno',
      'A veces no encuentra el producto que busco',
      'Seguid asi'
    ],
    // RESPUESTA 3 - Almacenero experimentado
    [
      'Almacenero / Encargado de stock', 'Varias veces por semana', '1 a 3 anos',
      '5', '4', '4',
      'Poder ver stock de todos los almacenes juntos',
      '4', '5', 'Si, significativamente',
      'Que muestre el lote mas proximo a vencer primero',
      '4', '5', '4',
      'Agregar motivo de salida',
      '4', '4', '5', '4',
      'Si, un poco mas rapido',
      'Que al buscar producto muestre el stock disponible',
      '5', '5',
      'Que permita guardar clientes frecuentes',
      '5', '4',
      'Graficos de entradas vs salidas',
      '4', '5',
      'Laptop',
      'Nada, esta muy bien',
      '4', 'Si, definitivamente', 'Mucho mejor', '5',
      'Control de lotes, kardex, facilidad de uso',
      'A veces se confunde con los numeros de lote',
      'Muy buen sistema para la farmacia'
    ],
    // RESPUESTA 4 - Vendedor sin experiencia
    [
      'Vendedor / Cajero', 'Una vez por semana', 'Ninguna (este es mi primer sistema)',
      '3', '4', '3',
      'Mas tutoriales o guias en pantalla',
      '3', '4', 'No lo noto',
      'Que explique mejor que es cada cosa',
      '3', '3', '3',
      'Que tenga instrucciones paso a paso',
      '3', '4', '3', '3',
      'Mas o menos igual',
      'Me cuesta un poco encontrar las cosas',
      '3', '5',
      'Que sea mas claro donde buscar',
      '3', '4',
      'No entiendo el kardex',
      '3', '4',
      'Celular',
      'Que sea responsive, se vea bien en celular',
      '3', 'Probablemente si', 'Algo mejor', '3',
      'Lo poco que use es facil',
      'No se usar el kardex, me confundo con los movimientos',
      'Necesito mas capacitacion'
    ],
    // RESPUESTA 5 - Gerente
    [
      'Gerente', 'Varias veces por semana', 'Mas de 3 anos',
      '5', '5', '5',
      'Reportes de ventas por dia/mes',
      '5', '5', 'Si, significativamente',
      'Que genere reportes automaticos de vencidos',
      '5', '5', '5',
      'Exportar movimientos a Excel',
      '5', '5', '5', '5',
      'Si, mucho mas rapido',
      'Dashboard con metricas de ventas',
      '5', '5',
      'Historial de compras por cliente',
      '5', '5',
      'Graficos de tendencias',
      '5', '5',
      'Computadora de escritorio',
      'Profesional y moderna',
      '5', 'Si, definitivamente', 'Mucho mejor', '5',
      'Todo: ventas, lotes, kardex, clientes',
      'Le falta reportes gerenciales',
      'Excelente para farmacia'
    ],
    // RESPUESTA 6 - Vendedor rapido
    [
      'Vendedor / Cajero', 'Diariamente', 'Menos de 1 ano',
      '4', '5', '4',
      'Que al escanear codigo de barras lo agregue solo',
      '4', '4', 'Si, un poco',
      'Destacar mas los que estan por vencer',
      '4', '4', '5',
      'Filtro por tipo de movimiento',
      '4', '5', '4', '4',
      'Si, mucho mas rapido',
      'Que tenga acceso rapido a productos populares',
      '4', '4',
      'Una busqueda mas rapida',
      '4', '5',
      'Nada',
      '4', '4',
      'Computadora de escritorio',
      'Esta bien, nada que criticar',
      '4', 'Si, definitivamente', 'Mucho mejor', '4',
      'Rapido, facil, bonito',
      'A veces se demora buscando',
      'Sigue mejorando'
    ],
    // RESPUESTA 7 - Administrador perfectionista
    [
      'Administrador', 'Diariamente', '1 a 3 anos',
      '5', '5', '5',
      'Que tenga modulos de promociones/descuentos',
      '5', '5', 'Si, significativamente',
      'Alertas de stock minimo por producto',
      '5', '5', '5',
      'Historial completo con todos los filtros',
      '5', '5', '5', '5',
      'Si, mucho mas rapido',
      'Integrar con balanza para pesados',
      '5', '5',
      'Campos personalizables por cliente',
      '5', '5',
      'Reportes en formato PDF mas detallados',
      '5', '5',
      'Computadora de escritorio',
      'Excelente interfaz, muy profesional',
      '5', 'Si, definitivamente', 'Mucho mejor', '5',
      'Velocidad, precision, control total',
      'Nada por ahora',
      'Perfecto para farmacia, lo recomiendo al 100%'
    ],
    // RESPUESTA 8 - Almacenero mayor
    [
      'Almacenero / Encargado de stock', 'Rara vez / Solo cuando es necesario', 'Menos de 1 ano',
      '3', '3', '3',
      'Que sea mas simple, menos opciones',
      '3', '3', 'No lo noto',
      'No entiendo como funciona bien',
      '3', '3', '3',
      'No se usar el sistema bien',
      '2', '3', '3', '2',
      'Mas o menos igual',
      'Me gusta mas el cuaderno',
      '3', '4',
      'Que sea igual al cuaderno',
      '3', '3',
      'Que sea mas simple',
      '3', '3',
      'Computadora de escritorio',
      'Me parece complicada',
      '3', 'No estoy seguro', 'Igual', '2',
      'Lo poco que entendi es util',
      'Me cuesta entenderlo, soy mayor y no manejo bien la compu',
      'Necesito que alguien me explique paso a paso'
    ],
    // RESPUESTA 9 - Vendedor constante
    [
      'Vendedor / Cajero', 'Diariamente', '1 a 3 anos',
      '5', '4', '5',
      'Que guarde los ultimos productos buscados',
      '4', '5', 'Si, significativamente',
      'Colores diferentes por estado de vencimiento',
      '4', '5', '5',
      'Filtro por rango de fechas',
      '5', '5', '5', '5',
      'Si, mucho mas rapido',
      'Nada, funciona perfecto',
      '4', '5',
      'Esta bien',
      '5', '5',
      'Nada',
      '5', '4',
      'Computadora de escritorio',
      'Limpia y ordenada',
      '5', 'Si, definitivamente', 'Mucho mejor', '5',
      'Rapidez, lotes, busqueda de clientes',
      'Nada que mejorar',
      'Muy buen trabajo, lo uso todos los dias sin problemas'
    ],
    // RESPUESTA 10 - Otro rol
    [
      'Otro', 'Varias veces por semana', '1 a 3 anos',
      '4', '4', '4',
      'Que tenga modulo de proveedores',
      '4', '4', 'Si, un poco',
      'Falta contacto con proveedores',
      '4', '4', '4',
      'Que tenga alertas de stock minimo',
      '4', '4', '4', '4',
      'Si, un poco mas rapido',
      'Modulo de pedidos a proveedores',
      '4', '4',
      'Integrar con WhatsApp para notificar clientes',
      '4', '4',
      'Graficos de productos mas vendidos',
      '4', '4',
      'Laptop',
      'Esta bien, le faltan algunas cosas',
      '4', 'Probablemente si', 'Algo mejor', '4',
      'Organizacion, rapidez, control',
      'Le falta modulo de proveedores y reportes',
      'Buen sistema, pero necesita crecer'
    ]
  ];

  var enviados = 0;
  var errores = 0;

  for (var i = 0; i < respuestas.length; i++) {
    try {
      var formData = {};
      for (var j = 0; j < entryIds.length && j < respuestas[i].length; j++) {
        formData['entry.' + entryIds[j]] = respuestas[i][j];
      }

      var options = {
        'method': 'post',
        'payload': formData,
        'followRedirects': true,
        'muteHttpExceptions': true
      };

      var response = UrlFetchApp.fetch(formURL, options);
      var code = response.getResponseCode();

      if (code === 200 || code === 302) {
        enviados++;
        Logger.log('Respuesta ' + (i + 1) + ' enviada OK (HTTP ' + code + ')');
      } else {
        errores++;
        Logger.log('Respuesta ' + (i + 1) + ' fallo (HTTP ' + code + ')');
      }

      // Esperar 2 segundos entre cada envio para no ser bloqueado
      Utilities.sleep(2000);

    } catch (e) {
      errores++;
      Logger.log('Error en respuesta ' + (i + 1) + ': ' + e.toString());
    }
  }

  Logger.log('====================================');
  Logger.log('ENVIO COMPLETADO');
  Logger.log('Enviados: ' + enviados);
  Logger.log('Errores: ' + errores);
  Logger.log('====================================');

  console.log('====================================');
  console.log('ENVIO COMPLETADO!');
  console.log('Enviados: ' + enviados + '/10');
  console.log('Errores: ' + errores);
  console.log('====================================');
}
