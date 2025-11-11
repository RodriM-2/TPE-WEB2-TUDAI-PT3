# TPE-WEB2-TUDAI-PT3
Parte 3 API RESTful del TPE - TUDAI 2025

*ENDPOINTS:*

el .htconfig esta configurado para que sea

    /api/gatos
  
    /api/peluqueros

En mi caso como lo use desde el htdocs de XAMPP tengo que especificar los folders donde esta el trabajo:

    http://127.0.0.1/web2/TPE_P3/api/peluqueros

    http://127.0.0.1/web2/TPE_P3/api/gatos

*Method GET*:

Sin especificar mas que los endpoints va a traer todos los elementos ORDENADOS POR ID EN FORMA ASCENDENTE

    /api/gatos

    /api/peluqueros

 Para traer un item en particular usar

    /api/gatos/{ID}
  
    /api/peluqueros/{ID}

*LOS PARAMS SE ACCEDEN USANDO:*

    /api/gatos?param={text}
  
    /api/peluqueros?param={text}
  
(No son case sensitive ni los param ni el orden!)

*ADICIONALMENTE PUEDEN TAMBIEN USAR ORDEN DE ESTA FORMA:*

    /api/gatos?param={text}&order={ASC/DESC}
  
    /api/peluqueros?param={text}&order={ASC/DESC}

*PARAMETROS VALIDOS PARA QUERY:*

  *GATOS:*
  
    peso_kg
    
    edad_meses
    
    id_peluquero
    
  *PELUQUEROS:*
  
    edad

*EJEMPLOS DE LOS QUERY PARAMS:*

    /api/gatos?param=peso_kg&order=DESC
    
    /api/gatos?param=id_peluquero
  
    /api/peluqueros?param=edad&order=DESC

*Method POST:*

  Se accede usando el endpoint base de GET
  
    /api/peluqueros
    
    /api/gatos
    
DATOS OBLIGATORIOS PARA CREAR:

  *GATOS:*
  
    {

    "nombre": "",
    
    "edad_meses": ,
    
    "raza": "Gato naranja",
    
    "peso_kg": ,
    
    "observaciones": ""
    
    }

  PELUQUEROS:
  
    {

    "nombre_apellido": "",
    
    "telefono": "",
    
    "edad": ,
    
    "especialidad": ""
    
    }

*Method PATCH y DELETE:*

  Ambos usan el endpoint junto con un ID
  
    /api/peluqueros/{id}
    
    /api/gatos/{id}

  NO se puede borrar un peluquero si aun tiene gatos asignados a su ID

  ADICIONALMENTE uso metodo PATCH asi que no hace falta actualizar todos los campos!
