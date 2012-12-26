Caja Muncipal
=============

Desarrollo de un sistema de caja, que no dependa de otra aplicacion, ni de otra base de datos.

Leerá desde el codigo de barra del comprobante, este codigo de barra esta establecido para la municipalidad, y es de 44 posiciones fijas.

No soporta en esta version, otra definicion de codigo de barra.

- Para ingresar al sistema, el usuario se loguea, el ingreso con su usuario, le determina en que caja esta trabajando, (De esta forma, el usuario donde se conecte sera su caja, y el numero se establece dinamicamente, otra forma seria, que en el momento de ingresar elija a que caja se loguea, entonces las cajas son estaticas, creo que esta es la mas recomendada. tmb se podria leer algo localmente para determinar la caja).

- Una vez ingresado, la pantalla por defecto es la pantalla de registro. 

El procedimiento para registrar es el siguiente:

- El lugar de lectura o tipeo del codigo de barra es unico, entrea cada tipeo o lectura, se van agregando lineas, registro o items al detalle de comprobantes, a esto lo llamaremos lote de pago.  Para identificar que es un nuevo lote, no tiene que haber ningun item o reglon en el detalle de comprobantes.  



