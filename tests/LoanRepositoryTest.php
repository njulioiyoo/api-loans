<?php

use PHPUnit\Framework\TestCase;
use App\Repositories\LoanRepository;

class LoanRepositoryTest extends TestCase
{
    private LoanRepository $loanRepository;

    protected function setUp(): void
    {
        $this->loanRepository = new LoanRepository();
    }

    /**
     * Test valid loan application submission.
     */
    public function testValidLoanApplication()
    {
        $data = [
            'name' => 'John Doe',
            'ktp' => '1234561501851238',
            'loan_amount' => 5000,
            'loan_period' => 12,
            'loan_purpose' => 'vacation',
            'date_of_birth' => '1985-01-15',
            'sex' => 'male'
        ];

        $response = $this->loanRepository->create($data);

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Application submitted successfully!', $response['message']);
    }

    /**
     * Test loan application submission with missing required fields.
     */
    public function testMissingRequiredFields()
    {
        $data = [
            'ktp' => '1234561501851238',
            'loan_amount' => 5000,
            'loan_purpose' => 'vacation',
            'date_of_birth' => '1985-01-15',
            'sex' => 'male'
        ];

        $response = $this->loanRepository->create($data);

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('name', $response['errors']);
        $this->assertEquals('name is required.', $response['errors']['name']);
    }

    /**
     * Test loan application submission with invalid KTP format.
     */
    public function testInvalidKTPFormat()
    {
        $data = [
            'name' => 'John Doe',
            'ktp' => '12345ABCDEF12345',
            'loan_amount' => 5000,
            'loan_period' => 12,
            'loan_purpose' => 'vacation',
            'date_of_birth' => '1985-01-15',
            'sex' => 'male'
        ];

        $response = $this->loanRepository->create($data);

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('ktp', $response['errors']);
        $this->assertStringContainsString('KTP must match', $response['errors']['ktp']);
    }
}
