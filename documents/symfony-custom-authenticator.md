### This is an example of implementation in Symfony. Here are the steps:

1. Install the dependency and configure the environment variables check the [README](../README.md) file, in the section [Installation](../README.md#installation)

2. Install the Security bundle in your Symfony project.
    ```
    composer require symfony/security-bundle
    ```
    
3. Configure the custom Authenticator:
    ```PHP

    /**
     * Example of an Authenticator class that uses the JWTAuthenticatorVerify class to verify the token
     * and the DummySymfonyUser class to create a dummy user to be used in the Symfony security system
     * This class is an example of implementation, you can use your own classes to verify the token and create the user
     */
    final class CognitoAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
    {
        public function __construct(private Verifier $verifier)
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

            $token = $this->getBearerHeader($request);

            try {
                
                $this->verifier->__invoke($token, $request->attributes->get('required_scopes'));

            }catch(\Exception $e) {
                throw new AuthenticationException($e->getMessage(), 401);
            }

            return new SelfValidatingPassport(new UserBadge($token, function ($token) {
                return new DummySymfonyUser($token);
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

4. Configute the service and enable the autowiring in the `services.xml` file:
  ```XML
      <service id="CognitoTokenVerifier\Application\Verifier" autowire="true"/>
  ```
