<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Infrastructure;

use Firebase\JWT\JWK;
use Firebase\JWT\Key;
use CognitoTokenVerifier\Domain\KeysRepository;

final class FromFileKeysRepository implements KeysRepository
{
    /**
     * @param string $keysFilePath AbsolutePath or URL to the file with the keys, if you use the URL, the file will be downloaded on every request, so is recommended to download the file and use the absolute path
     *
     * @see https://docs.aws.amazon.com/cognito/latest/developerguide/amazon-cognito-user-pools-using-tokens-verifying-a-jwt.html (More info about the keys)
     * @see https://cognito-idp.{awsRegion}.amazonaws.com/{userPoolId}/.well-known/jwks.json (Download the keys for your userpool, you can use this URL on development, but on production is recommended to download the file and use the absolute path)
     */
    public function __construct(private string $keysFilePath)
    {
    }

    public function findKeyByKid(string $kid): ?Key
    {
        if (empty($this->keysFilePath)) {
            return null;
        }

        try {
            $keys = $this->fetchKeys($this->keysFilePath);

            if (empty($keys)) {
                return null;
            }

            foreach ($keys as $key) {
                if ($kid === $key['kid']) {
                    return (new JWK())->parseKey($key);
                }
            }

            return null;
        } catch (\Exception) {
            return null;
        }
    }

    private function fetchKeys(string $keysFilePath): array
    {
        if (!is_file($keysFilePath) && strpos("http", $keysFilePath)) {
            return [];
        }

        $content = file_get_contents(is_file($keysFilePath) ? realpath($keysFilePath) : $keysFilePath);
        return json_decode($content, true)['keys'];
    }
}
