Caja Muncipal
=============

Desarrollo de un sistema de caja, que no dependa de otra aplicacion, ni de otra base de datos.

Leerá desde el codigo de barra del comprobante, este codigo de barra esta establecido para la municipalidad, y es de 44 posiciones fijas.

No soporta en esta version, otra definicion de codigo de barra.

Ingreso
-------
Para ingresar al sistema, el usuario se loguea, el ingreso con su usuario, le determina en que caja esta trabajando, (De esta forma, el usuario donde se conecte sera su caja, y el numero se establece dinamicamente, otra forma seria, que en el momento de ingresar elija a que caja se loguea, entonces las cajas son estaticas, creo que esta es la mas recomendada. tmb se podria leer algo localmente para determinar la caja).

Inicio o Apertura
-----------------
Una vez ingresado, la pantalla por defecto es la pantalla de registro, siempre que ya este habilitado la caja (la apertura de caja), sino, lo primero es realizar la apertura. 

Registro de pago
----------------

- 1.El lugar de lectura o tipeo del codigo de barra es unico.
- 2.Cada tipeo o lectura genera lineas, registro o items al detalle de comprobantes, a esto lo llamaremos lote de pago.  
- 3.Para identificar que es un nuevo lote, no tiene que haber ningun item o reglon en el detalle de comprobantes.
- 4.Agregado todos los comprobante, se procede al cobro, en la solapa de cobro, detallando las formas de pagos, `hasta que quede el saldo en cero`, o sea si la cobranza es por $350, agrgo un pago en cheque por $150, dejando un saldo de $200, $120 en tarjeta de creido Visa, quedando un saldo de $80, por lo que se paga $80 en efectivo, y el saldo final es cero, esto habilita el boton de confirmacion de pago y se procede al pago, si el saldo fuese menor a cero, eso implica que hay un cambio igual al monto en negativo, que se mostrara en la etiqueta de cambio, cada etapa del detalle de pago es previa recepcion de cheque, efectivo, o tarjeta ya procesada.
- 5.Confirmado el pago, se iran imprimiendo los tikets, para cada comprobante, el numero de cada comprobante es el nro de lote * 100 + nro de item, lo que indica que solo se podran registrar 99 comprobante por lote.
- 6.Si se ingresa un comprobante ya cobrado el sistema muestra un alerta. Si se quiere anular, tendra que ir a la opcion de anulacion de lote.

Estos pasos fueron para el procedimiento de pago, de aca se visualiza que el operador puede determinar la longitud o tamaño del lote.

Para Anular un comprobante
--------------------------

- 1.Se accede a la opcion de Anulacion
- 2.Se ingresa o lee un comprobante (codigo de barra)
- 3.Muestra todos los comprobante del lote, y su detelle de pagos
- 4.Se debe recuperar todos los tikets, del lotes que se debera adjuntar como testigo de la anulacion.
- 5.Cumplido todo los requsito se anula el lote.
- 6.Se deberá registrar todos los demas comprobante en un nuevo lote. (Que pasará con los que fue echo con tarjeta de credito?) analizar anular solo un comprobante dejando intacto el resto del lote.


Cierre de caja
--------------
El cierre muestra por pantalla un total por tributo y un total general.
Totales por las distintas formas de pagos.
y el boton de cierre habilitado, este genera un archivo comprimido que se embiará a la cuenta configurada, imprime 
los detalles en el tiket y cierra el tiket.

Habra opciones para reenvios y reimpresiones sin requerir cierres simpre que no haya una apertura.



CODIGO DE BARRA
---------------
La Identificacion del Cliente que es a partir de la Posicion Nº 18 y ocupa 16 caracteres dice asi:

Posicion 1: Indica un 1 si es proveniente del Sistema VIEJO, Indica un 2 si es proveniente del Sistema NUEVO
(El nuevo es todo lo emitido desde nuestro desarrollo via WEB)`

### Para el Caso de Sistema NUEVO

- Posición 2 y 3: Indica el Sistema desde el cual fue impreso, y se refiere exactamente al ID de la taba TIPO_SECCION, los casos mas típicos son: 01 = INMUEBLES / 02 = PATENTES / 03 =  CONVENIOS / 04 = COMERCIO / 05 = .... 06 = OTRAS TASAS / 07 = MICRO CREDITOS / 10 = PUBLICIDAD / 11 = CEMENTERIO. Sin dudas el sistema debería consultar esta tabla para informar al cajero de que se trata la cobranza

- Posición 4 a 6: Indica el Código de Tributo que se esta cobrando. Es mas util para el caso de que se este cobrando CONVENIOS, ya que te indica de que tributo es el convenio. Otro caso Útil es para cuando la boleta es de OTRAS TASAS que te indica también el tributo que se abona. En este caso en otras tasas puede haber mas de un tributo por boleta, pero en el código de barras solamente sale el primero.

- Posición 7 a 16: Indica el numero de comprobante que se está abonando, que luego será utilizado para impactar los pagos en el sistema (una vez que las cobranzas son informadas). Si el sistema de cajas es vía WEB y funciona en linea, con este comprobante podemos brindar mas informacion al cajero del detalle de lo que se esta cobrando, haciendo las consultas sobre las tablas relacionadas.

### Para el Caso de Sistema VIEJO

para el caso de que se identifique como sistema viejo, que hoy día como ejemplo esta comercio y otras tasas también, los 15 caracteres restantes difieren de acuerdo a cada sistema, esto ya lo conoces (cada uno manejaba ahí como se le antojara) Despues podemos ver cada caso para informar en pantalla algún detalle.


# COMO MANEJAR LOS CODIGOS DE BARRA #

Para poder automatizar, ya se vislumbra que minimamente hay dos codigos de barras, sistema nuevo y viejo, y posiblemente, cada subsistema del sistema viejo, tenga un codigo de barra para cada subsistema.

Inicialmente tendriamos dos codigos de barra, ambos de 44 caracteres de longitud, por lo que tiene que haber una forma de identificarlo, ambos empiezan con 9339, en la posicion 18, hay un caracter que identifica, 1 sistema viejo, 2 sistema nuevo, inicialmente utilizaremos esta configuracion.
Para identificar el codigo, la notacion seria [1,4][18,1] para definicion, y el valor seria 93391 o 93392 en la definicion del codigo de barra.


##APERTURA##

Las aperturas son los contenedore de las transacciones de los pagos, o sea cada pago esta asociada a una apertura,
cada apaertura esta asociado a la caja activa, indica en inicio del cobra hasta el ultimo cobro.
Solo puede haber una apertura habilitada por caja, una apertura habilitada es una apertura abierta, o sea no tiene fecha de cierre, no so podra crear otra apertura hasta que se cierre la activa.


##REGISTRO##

**Luis Rios**




 



 



  
  
