<?php
namespace App\OpenApi;
use OpenApi\Annotations as OA;
/**
 * @OA\Info(
 * title="Meeting Room Api",
 * version="1.0.0",
 * description="API documentation for meeting room api with Passport (client_credentials)"
 * )
 *
 * @OA\Server(
 * url="http://localhost:8000",
 * description="Local development server"
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="passport",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT",
 * description="Use the access token from Passport client_credentials"
 * )
 */
class SwaggerConfig
{
}
