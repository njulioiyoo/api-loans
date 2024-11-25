<?php

namespace App\Repositories;

use Respect\Validation\Validator as v;

class LoanRepository
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../storage/applications.json';
    }

    /**
     * Save the loan application and return a result.
     *
     * @param array<string, mixed> $data The loan application data.
     * @return array<string, mixed> An array with a status message or validation errors.
     */
    public function saveApplication(array $data): array
    {
        // Validate the application data
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // Save the application to file (as a simple example)
        $applications = $this->getApplications();
        $applications[] = $data;
        file_put_contents($this->filePath, json_encode($applications, JSON_PRETTY_PRINT));

        return ['message' => 'Application submitted successfully!'];
    }

    /**
     * Validates the given loan application data according to predefined rules.
     *
     * @param array<string, mixed> $data The loan application data to be validated.
     * @return array<string> An array of validation errors, where keys are field names
     * and values are error messages. Returns an empty array if validation passes.
     */
    private function validate(array $data): array
    {
        $errors = [];

        $rules = [
            'name' => v::stringType()
                ->notEmpty()
                ->regex('/^\S+\s+\S+/') // At least two names (separated by spaces)
                ->setName('Full Name'),
            'ktp' => v::callback(function ($value) use ($data) {
                return $this->validateKTP(
                    (string) $value, // Explicitly cast to string
                    isset($data['sex']) ? (string) $data['sex'] : null,
                    isset($data['date_of_birth']) ? (string) $data['date_of_birth'] : null
                );
            })->setName('KTP'),
            'loan_amount' => v::intVal()->between(1000, 10000),
            'loan_purpose' => v::regex('/\b( |vacation|renovation|electronics|wedding|rent|car|investment)\b/i'),
            'date_of_birth' => v::date('Y-m-d'),
            'sex' => v::in(['male', 'female']),
        ];

        foreach ($rules as $field => $validator) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                try {
                    $validator->assert($value);
                } catch (\Exception $e) {
                    $errors[$field] = $e->getMessage();
                }
            } else {
                $errors[$field] = "$field is required.";
            }
        }

        return $errors;
    }

    /**
     * Validates KTP number against the specified logic.
     *
     * @param string $ktp KTP number.
     * @param string|null $sex Gender (male or female).
     * @param string|null $dob Date of birth (Y-m-d).
     * @return bool True if valid, false otherwise.
     */
    private function validateKTP(string $ktp, ?string $sex, ?string $dob): bool
    {
        // Validate KTP format and ensure required fields are provided
        if (!preg_match('/^\d{16}$/', $ktp) || !$sex || !$dob) {
            return false;
        }

        // Extract date of birth and KTP parts
        [$year, $month, $day] = explode('-', $dob) + [null, null, null];
        if (!$year || !$month || !$day) {
            return false;
        }

        $ktpDay = (int)substr($ktp, 6, 2);
        $ktpMonth = (int)substr($ktp, 8, 2);
        $ktpYear = (int)substr($ktp, 10, 2);

        // Adjust day for females
        if ($sex === 'female') {
            $day = (int)$day + 40;  // Explicitly cast $day to int before adding 40
        }

        // Match extracted values
        return $ktpDay === (int)$day && $ktpMonth === (int)$month && $ktpYear === (int)substr($year, -2);
    }

    /**
     * Fetch the existing applications from the file.
     *
     * @return array<string, mixed> The applications data.
     */
    private function getApplications(): array
    {
        if (file_exists($this->filePath)) {
            $fileContent = file_get_contents($this->filePath);
            if ($fileContent !== false) {
                $applications = json_decode($fileContent, true);
                return is_array($applications) ? $applications : [];
            }
        }
        return [];
    }
}
