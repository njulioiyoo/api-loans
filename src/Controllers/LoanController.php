<?php

namespace App\Controllers;

use App\Repositories\LoanRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LoanController
{
    private LoanRepository $loanRepository;

    public function __construct(LoanRepository $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }

    /**
     * Method to process a loan application.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function apply(Request $request, Response $response): Response
    {
        // Parse the request body to get the input data
        $data = $request->getParsedBody();

        // Check if $data is indeed an associative array
        if (!is_array($data) || empty($data) || array_values($data) === $data) {
            // If not an associative array, return an error response with status 400
            $response->getBody()->write($this->safeJsonEncode(['error' => 'Invalid input']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Explicitly tell PHPStan that this is an associative array
        /** @var array<string, mixed> $data */

        // Pass data to repository for validation and saving
        $result = $this->loanRepository->saveApplication($data);

        // Check for validation errors in result
        if (isset($result['errors'])) {
            $response->getBody()->write($this->safeJsonEncode($result));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // If no errors, return success message
        $response->getBody()->write($this->safeJsonEncode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    private function safeJsonEncode(mixed $data): string
    {
        $encoded = json_encode($data);
        if ($encoded === false) {
            $encoded = json_encode(['error' => 'Failed to encode response']);
            if ($encoded === false) {
                return '{"error": "Unknown error occurred while encoding JSON."}';
            }
        }
        return $encoded;
    }
}
