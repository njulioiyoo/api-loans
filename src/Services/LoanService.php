<?php

namespace App\Services;

use App\Repositories\LoanRepository;
use App\Validators\LoanValidator;

class LoanService
{
    private LoanRepository $repository;
    private LoanValidator $validator;

    /**
     * LoanService constructor.
     *
     * @param LoanRepository $repository The repository for managing loan data.
     * @param LoanValidator $validator The validator for validating loan application data.
     */
    public function __construct(LoanRepository $repository, LoanValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Submits a loan application.
     *
     * Validates the given data first, returning an array of validation errors if
     * any occur. Otherwise, saves the application in the repository and returns
     * a success message.
     *
     * @param array<string, mixed> $data The loan application data to be validated and saved.
     *
     * @return array<string, mixed> An associative array containing either an "errors" key with
     * an array of validation errors, or a "message" key with a success message.
     */
    public function submitApplication(array $data): array
    {
        // Validate the loan application data
        $errors = $this->validator->validate($data);
        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // Save the application if validation passes
        $this->repository->saveApplication($data);

        return ['message' => 'Application submitted successfully!'];
    }
}
