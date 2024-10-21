<?php
namespace App\Service;

use DateTimeImmutable;
//On utilise JWT.io pour creer des (Tokens) Json Web Token
class JWTService
{
    //On génére le token

    /**
     * Génération du JWT
     * @param array $header 
     * @param array $payload 
     * @param string $secret 
     * @param int $validity 
     * @return string 
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string //10800 correspond a 10800 secondes, soit 3h!
    {
        if($validity > 0){
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;
    
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        //On encode le tout en base 64

        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        //On nettoie les valeurs encodés (retrait des + / et =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        //On génère la signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true); 

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);
        
        
        //On crée le token
        
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;


        return $jwt;
    }

    //On verifie que le token est valide(correctement formé)

    public function isValid(string $token): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    // On vérifie la validité du token (expiré ou pas)

    // on recupere le payload

    public function getPayload(string $token): array{
        //on démonte le token
        $array = explode('.', $token);

        //ondécode le payload
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    // on récupére le header pour pouvoir par la suite vérfier la signature du token
    public function getHeader(string $token): array{
        //on démonte le token
        $array = explode('.', $token);

        //ondécde le header
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }
    
    //On vérifie sie le token a expiré
    public function isExpired(string $token): bool{
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();
        
        return $payload['exp'] < $now->getTimeStamp();
    }

    // On verifie la signature du token
    public function check(string $token, string $secret){

        //on récupére le header et le payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        //On regénére un token

        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
    }
}