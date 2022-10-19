
<a name="readme-top"></a>
![PHP][PHP]

<!-- PROJECT LOGO -->
<br />
<div align="center">
<h3 align="center">JWT Cognito Signature</h3>

  <p align="center">

  </p>
</div>

## Sobre el Proyecto
Este paquete permite a los usuarios validar la firma de cognito. Creando un Authenticator en Symfony el proceso de validación de los tokens recae en esta librería.

<p align="right">(<a href="#readme-top">Ir arriba</a>)</p>


### Instalación

1. Establecer el repositorio en el `composer.json`
    ```JSON
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/Internacionalweb/jwt-cognito-signature"
            }
        ],
    ```
2. Instalar la librería
    ```
      composer require barymont/jwt-cognito-signature
    ```
3. Configurar las variables de entorno necesarias. `.env`
    ```
        #######
        REQUERIDOS
        #######

        # Si esta en dev salta la validación de firma. 
        APP_ENV = dev|prod  
        
        # Los clientes permitidos , separados por comas. 
        AWS_CLIENTS_ID_ALLOWNED = XXXXXX 

        AWS_COGNITO_REGION = XXX
        AWS_COGNITO_USER_POOL_ID = XXX

        # Cognito file with keys
        AWS_COGNITO_KEYS_FILE = xxxxxxx/xxxx/xxx
        
    ```
    ```
        #######
        OPCIONALES
        #######
        
        # Agrega el scope de las rutas
        AWS_SCOPE_URL = http://test.com 
    ```

3. Configurar el Custom-Authenticator en Symfony

<p align="right">(<a href="#readme-top">Ir arriba</a>)</p>


[PHP]: https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white

