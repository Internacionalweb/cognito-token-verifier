<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Infrastructure;

use Firebase\JWT\JWK;
use Firebase\JWT\Key;
use CognitoTokenVerifier\Domain\KeysRepository;

final class FromFileKeysRepository implements KeysRepository
{

    # Cognito file with keys, more info: (https://docs.aws.amazon.com/cognito/latest/developerguide/amazon-cognito-user-pools-using-tokens-verifying-a-jwt.html) 
    # You can download the keys for your userpool in: https://cognito-idp.{awsRegion}.amazonaws.com/{userPoolId}/.well-known/jwks.json
    # Dont use the URL, download the file, add it to your project then set the absolute path in $keysFilePath
    public function __construct(private string $keysFilePath)
    {
    }

    public function findKeyByKid(string $kid): ?Key
    {
        if (empty($this->keysFilePath)) {
            return null;
        }

        try {
            if (!is_file($this->keysFilePath) && strpos("http", $this->keysFilePath)) {
                return null;
            }

            $content = file_get_contents(is_file($this->keysFilePath) ? realpath($this->keysFilePath) : $this->keysFilePath);
            $keys = json_decode($content, true)['keys'];

            foreach ($keys as $key) {
                if ($kid === $key['kid']) {
                    $jwk = new JWK();
                    return $jwk->parseKey($key);
                }
            }
            return null;
        } catch (\Exception) {
            return null;
        }
    }
}
