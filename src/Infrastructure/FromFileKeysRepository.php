<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Infrastructure;

use Firebase\JWT\JWK;
use Firebase\JWT\Key;
use CognitoTokenVerifier\Domain\KeysRepository;

final class FromFileKeysRepository implements KeysRepository
{
    public function findKeyWithKid(string $kid): ?Key
    {
        $keysFile = $_ENV['AWS_COGNITO_KEYS_FILE'];

        if (empty($keysFile)) {
            return null;
        }

        try {

            if(!is_file($keysFile) && strpos("http",$keysFile)){
                return null;
            }

            $content = file_get_contents(is_file($keysFile) ? realpath($keysFile) : $keysFile);
            $keys = json_decode($content, true)['keys'];
                
            foreach ($keys as $key) {
                if ($kid === $key['kid']) {
                    $jwk = new JWK();
                    return $jwk->parseKey($key);
                }
            }
            return null;

        } catch (\Exception $e) {
            return null;
        }
    }
}
