<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Application;

use InvalidArgumentException;

final class Login
{
    /**
     * Login to Cognito and get the access token to use it on the Verifier, this is only for development purposes.
     * On production you should cache the access token to avoid call this method on every request and use the refresh token to get a new access token when the current one expires.
     *
     * @param  string               $oauth2CognitoUrl URL to get the access token from Cognito (https://{userPoolName}.auth.{userPoolAwsRegion}.amazoncognito.com/oauth2/token)
     * @param  string               $cognitoAppId     Cognito App ID (You can get this from the Cognito App Client
     * @param  string               $cognitoAppSecret Cognito App Secret (You can get this from the Cognito App Client)
     * @return array<string,string> Array with the access_token, expires_in and token_type. (https://docs.aws.amazon.com/cognito/latest/developerguide/token-endpoint.html)
     */
    public function __invoke(string $oauth2CognitoUrl, string $cognitoAppId, string $cognitoAppSecret): array
    {
        $accessTokenBase64 = base64_encode($cognitoAppId . ':' . $cognitoAppSecret);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $oauth2CognitoUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . $accessTokenBase64,
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new InvalidArgumentException('Curl error: ' . curl_error($curl));
        }

        curl_close($curl);

        return json_decode($response, true);
    }
}
