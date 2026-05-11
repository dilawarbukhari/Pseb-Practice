<?php 
require  './vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class JwtHandler
{
private $secretkey;

public function __construct(){
    $this->secretkey = '0556c0e43d8220208a0a2125c45fd4e2'; 
}
public function decodeJwt($token){
    $decodeToken = JWT::decode($token, new Key( $this->secretkey, 'HS256'));
    return array($decodeToken);
}
  public function generateaccessToken($userId,$IsChanged,$IsEmailVerified,$expiry=3600): string
    {
        $issuedAt = time();
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $issuedAt + $expiry;
         $payload['$userId'] = $userId;
         $payload['isChanged'] = $IsChanged;
         $payload['isEmailVerified']= $IsEmailVerified;
        return JWT::encode($payload, $this->secretkey, 'HS256');
    }
     public function generaterefreshToken(array $permissions  = [],$userId,$expiry=(7*24*60*60)): string
    {
        $issuedAt = time();     
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $issuedAt + $expiry;
        $payload['permissions'] = $permissions;
        $payload['$userId'] = $userId;
        return JWT::encode($payload, $this->secretkey, 'HS256');
    }
}
?>