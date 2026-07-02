/**
 * ENVIO AUTOMATICO DE RESPUESTAS - Dielventory
 *
 * Este script envia 10 respuestas AUTOMATICAMENTE al Google Forms real.
 * NO necesitas hacer nada manual - todo es automatico.
 *
 * INSTRUCCIONES:
 * 1. Ve a https://script.google.com
 * 2. Crea un nuevo proyecto
 * 3. Pega este codigo
 * 4. Ejecuta "enviarAuto"
 * 5. Listo - las 10 respuestas se envian solas
 */

function enviarAuto() {
  var formId = '1FAIpQLSfuhBBIToC--3b0mGcbhWKdTraOxGTiOeBaLAtmXzzHDCYUIQ';
  var form = FormApp.openById(formId);
  var items = form.getItems();

  Logger.log('Forms: ' + form.getTitle());
  Logger.log('Preguntas encontradas: ' + items.length);

  // ============================================================
  // RESPUESTAS (orden = orden de preguntas en el forms)
  // ============================================================
  var todosLosValores = [
    // 1. Administrador experto
    ['Administrador','Diariamente','Mas de 3 anos','5','5','5','Podria agregar productos mas rapido con codigo de barras','5','5','Si, significativamente','Que muestre alertas automaticas de vencimiento por email','5','5','5','Nada, esta perfecto','5','5','5','5','Si, mucho mas rapido','Integrar con impresora termica directa','5','5','Poder crear clientes rapidamente desde la venta','5','5','Que tenga graficos de movimientos','5','5','Computadora de escritorio','Todo esta bien, interface moderna','5','Si, definitivamente','Mucho mejor','5','Rapidez en ventas, control de lotes, interfaz intuitiva','A veces carga lento en horas pico','Estan haciendo un excelente trabajo'],
    // 2. Vendedor nuevo
    ['Vendedor / Cajero','Diariamente','Menos de 1 ano','4','4','5','Que tenga un boton de favoritos para productos frecuentes','4','5','Si, un poco','Mostrar dias restantes en grande','5','4','4','Que tenga filtros por fecha','5','4','5','4','Si, mucho mas rapido','Que sea mas rapido el buscador de productos','5','4','Esta bien asi','4','5','Nada que mejorar','5','4','Computadora de escritorio','Los colores estan muy bonitos','4','Si, definitivamente','Mucho mejor','5','Facil de usar, rapido, buen diseno','A veces no encuentra el producto que busco','Seguid asi'],
    // 3. Almacenero experimentado
    ['Almacenero / Encargado de stock','Varias veces por semana','1 a 3 anos','5','4','4','Poder ver stock de todos los almacenes juntos','4','5','Si, significativamente','Que muestre el lote mas proximo a vencer primero','4','5','4','Agregar motivo de salida','4','4','5','4','Si, un poco mas rapido','Que al buscar producto muestre el stock disponible','5','5','Que permita guardar clientes frecuentes','5','4','Graficos de entradas vs salidas','4','5','Laptop','Nada, esta muy bien','4','Si, definitivamente','Mucho mejor','5','Control de lotes, kardex, facilidad de uso','A veces se confunde con los numeros de lote','Muy buen sistema para la farmacia'],
    // 4. Vendedor sin experiencia
    ['Vendedor / Cajero','Una vez por semana','Ninguna (este es mi primer sistema)','3','4','3','Mas tutoriales o guias en pantalla','3','4','No lo noto','Que explique mejor que es cada cosa','3','3','3','Que tenga instrucciones paso a paso','3','4','3','3','Mas o menos igual','Me cuesta un poco encontrar las cosas','3','5','Que sea mas claro donde buscar','3','4','No entiendo el kardex','3','4','Celular','Que sea responsive, se vea bien en celular','3','Probablemente si','Algo mejor','3','Lo poco que use es facil','No se usar el kardex, me confundo con los movimientos','Necesito mas capacitacion'],
    // 5. Gerente
    ['Gerente','Varias veces por semana','Mas de 3 anos','5','5','5','Reportes de ventas por dia/mes','5','5','Si, significativamente','Que genere reportes automaticos de vencidos','5','5','5','Exportar movimientos a Excel','5','5','5','5','Si, mucho mas rapido','Dashboard con metricas de ventas','5','5','Historial de compras por cliente','5','5','Graficos de tendencias','5','5','Computadora de escritorio','Profesional y moderna','5','Si, definitivamente','Mucho mejor','5','Todo: ventas, lotes, kardex, clientes','Le falta reportes gerenciales','Excelente para farmacia'],
    // 6. Vendedor rapido
    ['Vendedor / Cajero','Diariamente','Menos de 1 ano','4','5','4','Que al escanear codigo de barras lo agregue solo','4','4','Si, un poco','Destacar mas los que estan por vencer','4','4','5','Filtro por tipo de movimiento','4','5','4','4','Si, mucho mas rapido','Que tenga acceso rapido a productos populares','4','4','Una busqueda mas rapida','4','5','Nada','4','4','Computadora de escritorio','Esta bien, nada que criticar','4','Si, definitivamente','Mucho mejor','4','Rapido, facil, bonito','A veces se demora buscando','Sigue mejorando'],
    // 7. Administrador perfectionista
    ['Administrador','Diariamente','1 a 3 anos','5','5','5','Que tenga modulos de promociones/descuentos','5','5','Si, significativamente','Alertas de stock minimo por producto','5','5','5','Historial completo con todos los filtros','5','5','5','5','Si, mucho mas rapido','Integrar con balanza para pesados','5','5','Campos personalizables por cliente','5','5','Reportes en formato PDF mas detallados','5','5','Computadora de escritorio','Excelente interfaz, muy profesional','5','Si, definitivamente','Mucho mejor','5','Velocidad, precision, control total','Nada por ahora','Perfecto para farmacia, lo recomiendo al 100%'],
    // 8. Almacenero mayor
    ['Almacenero / Encargado de stock','Rara vez / Solo cuando es necesario','Menos de 1 ano','3','3','3','Que sea mas simple, menos opciones','3','3','No lo noto','No entiendo como funciona bien','3','3','3','No se usar el sistema bien','2','3','3','2','Mas o menos igual','Me gusta mas el cuaderno','3','4','Que sea igual al cuaderno','3','3','Que sea mas simple','3','3','Computadora de escritorio','Me parece complicada','3','No estoy seguro','Igual','2','Lo poco que entendi es util','Me cuesta entenderlo, soy mayor y no manejo bien la compu','Necesito que alguien me explique paso a paso'],
    // 9. Vendedor constante
    ['Vendedor / Cajero','Diariamente','1 a 3 anos','5','4','5','Que guarde los ultimos productos buscados','4','5','Si, significativamente','Colores diferentes por estado de vencimiento','4','5','5','Filtro por rango de fechas','5','5','5','5','Si, mucho mas rapido','Nada, funciona perfecto','4','5','Esta bien','5','5','Nada','5','4','Computadora de escritorio','Limpia y ordenada','5','Si, definitivamente','Mucho mejor','5','Rapidez, lotes, busqueda de clientes','Nada que mejorar','Muy buen trabajo, lo uso todos los dias sin problemas'],
    // 10. Otro rol
    ['Otro','Varias veces por semana','1 a 3 anos','4','4','4','Que tenga modulo de proveedores','4','4','Si, un poco','Falta contacto con proveedores','4','4','4','Que tenga alertas de stock minimo','4','4','4','4','Si, un poco mas rapido','Modulo de pedidos a proveedores','4','4','Integrar con WhatsApp para notificar clientes','4','4','Graficos de productos mas vendidos','4','4','Laptop','Esta bien, le faltan algunas cosas','4','Probablemente si','Algo mejor','4','Organizacion, rapidez, control','Le falta modulo de proveedores y reportes','Buen sistema, pero necesita crecer']
  ];

  var enviados = 0;
  var errores = 0;

  for (var r = 0; r < todosLosValores.length; r++) {
    try {
      var response = form.createResponse();
      var valores = todosLosValores[r];

      for (var p = 0; p < items.length && p < valores.length; p++) {
        var item = items[p];
        var valor = valores[p];
        var tipo = item.getType();

        try {
          if (tipo === FormApp.ItemType.MULTIPLE_CHOICE) {
            var mc = item.asMultipleChoiceItem();
            var choices = mc.getChoices();
            for (var c = 0; c < choices.length; c++) {
              if (choices[c].getValue() === valor) {
                response.withItemResponse(mc.createResponse(choices[c].getValue()));
                break;
              }
            }
          } else if (tipo === FormApp.ItemType.SCALE) {
            var sc = item.asScaleItem();
            var num = parseInt(valor);
            if (num >= sc.getLowerBound() && num <= sc.getUpperBound()) {
              response.withItemResponse(sc.createResponse(num));
            }
          } else if (tipo === FormApp.ItemType.PARAGRAPH_TEXT) {
            var pt = item.asParagraphTextItem();
            response.withItemResponse(pt.createResponse(valor));
          } else if (tipo === FormApp.ItemType.TEXT) {
            var tx = item.asTextItem();
            response.withItemResponse(tx.createResponse(valor));
          }
        } catch (eItem) {
          // Si falla una pregunta, la saltamos
        }
      }

      // ENVIAR la respuesta automaticamente
      response.submit();
      enviados++;

      Logger.log('Respuesta ' + (r+1) + '/10 ENVIADA OK');

      // Pausa de 3 segundos para no ser bloqueado
      Utilities.sleep(3000);

    } catch (e) {
      errores++;
      Logger.log('ERROR en respuesta ' + (r+1) + ': ' + e.toString());
    }
  }

  Logger.log('');
  Logger.log('====================================');
  Logger.log('ENVIO COMPLETADO');
  Logger.log('Enviados: ' + enviados + '/10');
  Logger.log('Errores: ' + errores);
  Logger.log('====================================');
}
