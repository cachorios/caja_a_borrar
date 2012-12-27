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




**Luis Rios**




 



 



  
  
