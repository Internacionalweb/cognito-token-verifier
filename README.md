
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

        # Cognito file with keys (ABSOLUT PATH OR URL)
        AWS_COGNITO_KEYS_FILE = xxxxxxx/xxxx/xxx
        
    ```
    ```
        #######
        OPCIONALES
        #######
        
        # Agrega el scope de las rutas
        AWS_SCOPE_URL = http://test.com 
    ```

3. Instalar el bundle Security
    ```
    composer require symfony/security-bundle
    ```
4. Configura el Custom Authenticator. 
    ```PHP
    // Ejemplo de un authenticator
    final class CognitoAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
    {
        public function __construct(private JWTAuthenticatorVerify $jwtAuthenticatorVerify)
        {
        }

        public function start(Request $request, AuthenticationException $authException = null): JsonResponse
        {
            return new JsonResponse(['message' => 'Authentication Required'], 401);
        }

        public function supports(Request $request): ?bool
        {
            if (false === $request->headers->has('Authorization')) {
                throw new AuthenticationException('Access Token is required', 401);
            }

            return true;
        }

        public function authenticate(Request $request): Passport
        {
            // Extract token from header
            $token = $this->getBearerHeader($request);

             $token = $this->getBearerHeader($request);

            try {
                
                $this->jwtAuthenticatorVerify->__invoke($token, $request->attributes->get('required_scopes'));

            }catch(\Exception $e) {
                throw new AuthenticationException($e->getMessage(), 401);
            }

            return new SelfValidatingPassport(new UserBadge($token, function ($token) {
                return new User($token);
            }));
        }



        public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
        {
            return null;
        }

        public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
        {
            return new JsonResponse(['message' => "Authentication failed"], 401);
        }

        private function getBearerHeader(Request $request): ?string
        {
            $header = $request->headers->get('Authorization');
            if (empty($header)) {
                return null;
            }

            if (0 !== strpos($header, 'Bearer ')) {
                return null;
            }

            return trim(ltrim($header, 'Bearer'));
        }
    }
    ```  


5. Ready!


<p align="right">(<a href="#readme-top">Ir arriba</a>)</p>


[PHP]: https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white

