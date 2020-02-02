

###### Como usar la api #####
#############################################


/api/v1/product   MÉTODO POST

Permite añadir un producto a la base de datos.

Recibe los parámetros
name (string)
category (int)
price (float)
currency(EUR / USD)
featured (boolean)

#############################################


api/v1/products MÉTODO GET

Devuelve el listado de los productos en la base de datos.

#############################################


api/v1/products/featured{?currency} MÉTODO GET

Devuelve el listado de los productos destacados en la base de datos.
Puede recibir el parametro currency (EUR,USD) mediante el cual mostrará los precios en la moneda elegida.

##############################################


/api/v1/category   MÉTODO POST

Permite añadir una categoria a la base de datos.

Recibe los parámetros
name(string)
description(text)

#############################################


api/v1/categories MÉTODO GET
Devuelve el listado de categorías de la base de datos.

##############################################


api/v1/category/{id} MÉTODO DELETE
Borra una categoría de la base de datos.

##############################################


api/v1/category/{id} MÉTODO PUT
Modifica una categoría de la base de datos.
name(string)
description(text)

##############################################




