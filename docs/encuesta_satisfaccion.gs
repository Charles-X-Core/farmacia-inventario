/**
 * ENCUESTA DE SATISFACCION - Dielventory
 * Sistema de Inventario para Farmacia
 *
 * INSTRUCCIONES:
 * 1. Ve a https://script.google.com
 * 2. Crea un nuevo proyecto ("Proyecto sin nombre")
 * 3. Borra todo el codigo existente y pega este script
 * 4. Haz clic en "Ejecutar" (boton verde play)
 * 5. Autoriza cuando te pida permisos
 * 6. Se creara automaticamente la encuesta en tu Google Forms
 * 7. Copia el link que aparece en la consola
 */

function crearEncuesta() {
  var form = FormApp.create('Encuesta de Satisfaccion - Dielventory');

  // Configuracion general
  form.setDescription('Encuesta de satisfaccion del usuario sobre el Sistema de Inventario Dielventory para Farmacia. Tu respuesta es anonima y nos ayudara a mejorar el sistema.');
  form.setIsQuiz(false);
  form.setAllowResponseEdits(true);
  form.setCollectEmail(false);

  // ============================================================
  // DATOS GENERALES
  // ============================================================
  var seccion = form.addSectionHeaderItem();
  seccion.setTitle('Datos Generales');

  form.addMultipleChoiceItem()
    .setTitle('1. ¿Cual es tu rol en la farmacia?')
    .setChoiceValues([
      'Administrador',
      'Vendedor / Cajero',
      'Almacenero / Encargado de stock',
      'Gerente',
      'Otro'
    ])
    .setRequired(true);

  form.addMultipleChoiceItem()
    .setTitle('2. ¿Cada cuanto utilizas el sistema de inventario?')
    .setChoiceValues([
      'Diariamente',
      'Varias veces por semana',
      'Una vez por semana',
      'Varias veces al mes',
      'Rara vez / Solo cuando es necesario'
    ])
    .setRequired(true);

  form.addMultipleChoiceItem()
    .setTitle('3. ¿Cuantos anos de experiencia tienes trabajando con sistemas de inventario?')
    .setChoiceValues([
      'Ninguna (este es mi primer sistema)',
      'Menos de 1 ano',
      '1 a 3 anos',
      'Mas de 3 anos'
    ])
    .setRequired(true);

  // ============================================================
  // MODULO DE PRODUCTOS
  // ============================================================
  var seccion = form.addSectionHeaderItem();
  seccion.setTitle('Modulo de Productos');

  form.addScaleItem()
    .setTitle('4. ¿Que tan facil es agregar un nuevo producto al sistema?')
    .setBounds(1, 5)
    .setLabels('Muy dificil', 'Muy facil')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('5. ¿Que tan claro es el formulario de registro de productos?')
    .setBounds(1, 5)
    .setLabels('Nada claro', 'Muy claro')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('6. ¿Que tan util es la busqueda y filtrado de productos?')
    .setBounds(1, 5)
    .setLabels('Nada util', 'Muy util')
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('7. ¿Que mejoraria en el modulo de productos?')
    .setRequired(false);

  // ============================================================
  // MODULO DE LOTES
  // ============================================================
  var seccion = form.addSectionHeaderItem();
  seccion.setTitle('Modulo de Lotes');

  form.addScaleItem()
    .setTitle('8. ¿Que tan intuitivo es el control de lotes?')
    .setBounds(1, 5)
    .setLabels('Nada intuitivo', 'Muy intuitivo')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('9. ¿Que tan util es la informacion de vencimiento de lotes?')
    .setBounds(1, 5)
    .setLabels('Nada util', 'Muy util')
    .setRequired(true);

  form.addMultipleChoiceItem()
    .setTitle('10. ¿El sistema te ayuda a reducir perdidas por productos vencidos?')
    .setChoiceValues([
      'Si, significativamente',
      'Si, un poco',
      'No lo noto',
      'No, para nada'
    ])
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('11. ¿Que mejoraria en el modulo de lotes?')
    .setRequired(false);

  // ============================================================
  // MODULO DE MOVIMIENTOS
  // ============================================================
  var seccion = form.addSectionHeaderItem();
  seccion.setTitle('Modulo de Movimientos');

  form.addScaleItem()
    .setTitle('12. ¿Que tan facil es registrar una entrada de mercaderia?')
    .setBounds(1, 5)
    .setLabels('Muy dificil', 'Muy facil')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('13. ¿Que tan facil es registrar una salida de mercaderia?')
    .setBounds(1, 5)
    .setLabels('Muy dificil', 'Muy facil')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('14. ¿Que tan claro es el historial de movimientos?')
    .setBounds(1, 5)
    .setLabels('Nada claro', 'Muy claro')
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('15. ¿Que mejoraria en el modulo de movimientos?')
    .setRequired(false);

  // ============================================================
  // MODULO DE VENTAS
  // ============================================================
  var seccion = form.addSectionHeaderItem();
  seccion.setTitle('Modulo de Ventas');

  form.addScaleItem()
    .setTitle('16. ¿Que tan rapido es procesar una venta?')
    .setBounds(1, 5)
    .setLabels('Muy lento', 'Muy rapido')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('17. ¿Que tan clara es la seleccion de productos durante la venta?')
    .setBounds(1, 5)
    .setLabels('Nada clara', 'Muy clara')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('18. ¿Que tan util es la seleccion de lotes durante la venta?')
    .setBounds(1, 5)
    .setLabels('Nada util', 'Muy util')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('19. ¿Que tan intuitivo es el proceso de pago con multiples metodos?')
    .setBounds(1, 5)
    .setLabels('Nada intuitivo', 'Muy intuitivo')
    .setRequired(true);

  form.addMultipleChoiceItem()
    .setTitle('20. ¿El proceso de venta es mas rapido que el metodo manual (cuaderno/factura)?')
    .setChoiceValues([
      'Si, mucho mas rapido',
      'Si, un poco mas rapido',
      'Mas o menos igual',
      'Es mas lento que el metodo manual'
    ])
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('21. ¿Que mejoraria en el modulo de ventas?')
    .setRequired(false);

  // ============================================================
  // MODULO DE CLIENTES
  // ============================================================
  var seccion = form.addSectionHeaderItem();
  seccion.setTitle('Modulo de Clientes');

  form.addScaleItem()
    .setTitle('22. ¿Que tan facil es buscar un cliente existente?')
    .setBounds(1, 5)
    .setLabels('Muy dificil', 'Muy facil')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('23. ¿Que tan util es la opcion de venta a "Cliente Final" (sin registro)?')
    .setBounds(1, 5)
    .setLabels('Nada util', 'Muy util')
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('24. ¿Que mejoraria en el modulo de clientes?')
    .setRequired(false);

  // ============================================================
  // KARDEX
  // ============================================================
  var seccion = form.addSectionHeaderItem();
  seccion.setTitle('Kardex');

  form.addScaleItem()
    .setTitle('25. ¿Que tan clara es la informacion del kardex?')
    .setBounds(1, 5)
    .setLabels('Nada clara', 'Muy clara')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('26. ¿Que tan util es el historial de movimientos por producto?')
    .setBounds(1, 5)
    .setLabels('Nada util', 'Muy util')
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('27. ¿Que mejoraria en el kardex?')
    .setRequired(false);

  // ============================================================
  // INTERFAZ Y DISEÑO
  // ============================================================
  var seccion = form.addSectionHeaderItem();
  seccion.setTitle('Interfaz y Diseno');

  form.addScaleItem()
    .setTitle('28. ¿Que tan facil es navegar entre los diferentes modulos?')
    .setBounds(1, 5)
    .setLabels('Muy dificil', 'Muy facil')
    .setRequired(true);

  form.addScaleItem()
    .setTitle('29. ¿El diseno visual del sistema es atractivo y profesional?')
    .setBounds(1, 5)
    .setLabels('Nada atractivo', 'Muy atractivo')
    .setRequired(true);

  form.addMultipleChoiceItem()
    .setTitle('30. ¿Accedes al sistema desde que dispositivo?')
    .setChoiceValues([
      'Computadora de escritorio',
      'Laptop',
      'Tablet',
      'Celular',
      'Combinacion de dispositivos'
    ])
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('31. ¿Que mejoraria en la interfaz del sistema?')
    .setRequired(false);

  // ============================================================
  // SATISFACCION GENERAL
  // ============================================================
  var seccion = form.addSectionHeaderItem();
  seccion.setTitle('Satisfaccion General');

  form.addScaleItem()
    .setTitle('32. ¿Que tan satisfecho estas con el sistema Dielventory en general?')
    .setBounds(1, 5)
    .setLabels('Muy insatisfecho', 'Muy satisfecho')
    .setRequired(true);

  form.addMultipleChoiceItem()
    .setTitle('33. ¿Recomendarías este sistema a otras farmacias?')
    .setChoiceValues([
      'Si, definitivamente',
      'Probablemente si',
      'No estoy seguro',
      'Probablemente no',
      'No, para nada'
    ])
    .setRequired(true);

  form.addMultipleChoiceItem()
    .setTitle('34. ¿Comparado con tu metodo anterior de gestion, el sistema es?')
    .setChoiceValues([
      'Mucho mejor',
      'Algo mejor',
      'Igual',
      'Algo peor',
      'Mucho peor'
    ])
    .setRequired(true);

  form.addScaleItem()
    .setTitle('35. ¿Que tan probable es que sigas usando el sistema en el futuro?')
    .setBounds(1, 5)
    .setLabels('Muy improbable', 'Muy probable')
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('36. ¿Cuales son las 3 cosas que mas te gustan del sistema?')
    .setRequired(false);

  form.addParagraphTextItem()
    .setTitle('37. ¿Cuales son las 3 cosas que menos te gustan o que mas dificultan tu trabajo?')
    .setRequired(false);

  form.addParagraphTextItem()
    .setTitle('38. ¿Tienes alguna otra sugerencia o comentario?')
    .setRequired(false);

  // ============================================================
  // RESULTADO
  // ============================================================
  Logger.log('Encuesta creada exitosamente!');
  Logger.log('URL de la encuesta: ' + form.getPublishedUrl());
  Logger.log('URL para editar: ' + form.getEditUrl());

  // Mostrar link en la consola
  console.log('====================================');
  console.log('ENCUESTA CREADA!');
  console.log('Link para compartir:');
  console.log(form.getPublishedUrl());
  console.log('====================================');
}
