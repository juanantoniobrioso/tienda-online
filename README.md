# Tienda Online

Este proyecto se trata de una tienda online en la que
inicias sesión y puedes añadir productos al carrito, ver los
productos que has visto recientemente y comprarlos.

## Funciones

Primero inicias sesión con una de las cuentas proporcionadas abajo. Al hacerlo,
verás 3 productos destacados (los cuáles he decidido yo arbitrariamente, no van cambiando) que puedes añadir al carrito las veces que quieras o ver detalle. Ver detalle te da una ampliación de la imagen junto a una descripción, además de guardar ese producto en la sección de vistos recientemente. Esta sección puede tener hasta 5 productos vistos y se ponen en primer puesto los últimos productos a los que se ha hecho click en "Ver detalle"

En la sección de categorías puedes ver más productos según su tipo. Tenemos de tipo tecnología, ropa y hogar, con un total de 7 productos entre ellos.

Finalmente en el carrito puedes ver todos los productos añadidos junto a su cantidad y su precio, seguido del precio total y un botón para simular una compra, lo cual te pone un mensaje de que has comprado todo y vacía el carrito

El botón de Salir cierra sesión, elimina el token y devuelve al login

## Usuarios para iniciar sesión

Usuario: admin
contraseña: 123

Usuario: cliente
contraseña: abc

## Principios SOLID

Los principios SOLID son:
**S**ingle Responsibility Principle (Principio de Responsabilidad Única)
**O**pen-Closed Principle (Principio de Abierto-Cerrado)
**L**iskov Substitution Principle (Principio de Sustitución de Liskov)
**I**nterface Segregation Principle (Principio de Segregación de Interfaz)
**D**ependency Inversion Principle (Principio de Inversión de Dependencia)

### ¿Qué significa cada principio?

El Principio de Responsabilidad Única dice que una clase debe hacer una cosa y, por lo tanto, debe tener una sola razón para cambiar.

El principio de apertura y cierre exige que las clases deban estar abiertas a la extensión y cerradas a la modificación.

El Principio de Sustitución de Liskov establece que las subclases deben ser sustituibles por sus clases base.

El Principio de Segregación de Interfaces se trata de separar las interfaces.

El principio de inversión de dependencia establece que nuestras clases deben depender de interfaces o clases abstractas en lugar de clases y funciones concretas.

## Errores

He utilizado enlaces para las imágenes en vez de rutas. Si por lo que sea los dueños de esos enlaces deciden tirar su página o su imagen, aquí ya no se verían. Lo ideal es usar rutas de archivo, pero no he podido conseguir que funcionen del todo bien.

Tampoco he sabido implementar el sistema para evitar la manipulación de precios.