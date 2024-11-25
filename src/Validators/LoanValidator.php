<?php

namespace App\Validators;

use Respect\Validation\Validator as v;

class LoanValidator
{
    /**
     * Validates the given loan application data according to predefined rules.
     *
     * @param array $data The loan application data to be validated.
     * @return array An array of validation errors, where keys are field names
     * and values are error messages. Returns an empty array if validation passes.
     */
    public function validate(array $data): array
    {
        $errors = [];

        $rules = [
            'name' => v::stringType()
                ->notEmpty()
                ->regex('/^\S+\s+\S+/') // At least two names (separated by spaces)
                ->setName('Full Name'),
            'ktp' => v::callback(function ($value) use ($data) {
                return $this->validateKTP($value, $data['sex'] ?? null, $data['date_of_birth'] ?? null);
            })->setName('KTP'),
            'loan_amount' => v::intVal()->between(1000, 10000),
            'loan_purpose' => v::regex('/\b( |renovation|electronics|wedding|rent|car|investment)\b/i'),
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
            $day += 40;
        }

        // Match extracted values
        return $ktpDay === (int)$day && $ktpMonth === (int)$month && $ktpYear === (int)substr($year, -2);
    }
}
