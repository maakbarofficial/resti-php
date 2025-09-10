<?php

namespace App\Core;

class JWT
{
  private $secret;

  public function __construct($secret)
  {
    $this->secret = $secret;
  }

  /**
   * Encode a payload into a JWT
   * @param array $payload The JWT payload (e.g., ['sub' => 1, 'role' => 'user'])
   * @return string The JWT
   */
  public function encode(array $payload)
  {
    $header = [
      'alg' => 'HS256',
      'typ' => 'JWT'
    ];

    // Encode header and payload
    $headerEncoded = $this->base64UrlEncode(json_encode($header));
    $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

    // Create signature
    $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $this->secret, true);
    $signatureEncoded = $this->base64UrlEncode($signature);

    // Combine into JWT
    return "$headerEncoded.$payloadEncoded.$signatureEncoded";
  }

  /**
   * Decode and verify a JWT
   * @param string $jwt The JWT string
   * @return array|null The decoded payload or null if invalid
   * @throws \Exception If token is malformed or invalid
   */
  public function decode($jwt)
  {
    // Split JWT into parts
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
      throw new \Exception('Invalid JWT format');
    }

    list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;

    // Verify signature
    $signature = $this->base64UrlDecode($signatureEncoded);
    $expectedSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $this->secret, true);
    if (!hash_equals($signature, $expectedSignature)) {
      throw new \Exception('Invalid JWT signature');
    }

    // Decode header and payload
    $header = json_decode($this->base64UrlDecode($headerEncoded), true);
    $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

    if (!$header || !$payload) {
      throw new \Exception('Invalid JWT encoding');
    }

    // Verify algorithm
    if ($header['alg'] !== 'HS256') {
      throw new \Exception('Unsupported algorithm');
    }

    // Verify expiration
    if (isset($payload['exp']) && $payload['exp'] < time()) {
      throw new \Exception('Token expired');
    }

    return $payload;
  }

  /**
   * Base64 URL encode (replaces + and /, removes padding)
   * @param string $data
   * @return string
   */
  private function base64UrlEncode($data)
  {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
  }

  /**
   * Base64 URL decode
   * @param string $data
   * @return string
   */
  private function base64UrlDecode($data)
  {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
  }
}
