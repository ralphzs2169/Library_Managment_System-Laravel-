<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\BookCopy;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use App\Services\BorrowService;
use App\Policies\BorrowPolicy;

class BorrowController extends Controller
{
    protected $borrowService;

    public function __construct(BorrowService $borrowService)
    {
        $this->borrowService = $borrowService;
    }

    public function validateBorrow(Request $request)
    {
        $result = BorrowPolicy::canBorrow(true, $request->all());

        switch ($result['result']) {
            case 'success': return $this->jsonResponse('valid', 'Validation passed', 200, ['borrower_fullname' => $result['borrower_fullname']]);
            case 'not_found': return $this->jsonResponse('not_found', $result['message'], 404);
            case 'invalid_input': return $this->jsonResponse('invalid_input', $result['message'], 422, ['errors' => $result['errors']]);
            case 'business_rule_violation': return $this->jsonResponse('business_rule_violation', $result['message'], 400);
            default: return $this->jsonResponse('error', 'Unknown validation error', 500);
        }
    }

    public function performBorrow(Request $request)
    {
        try {
            $bookCopy = BookCopy::findOrFail($request->input('book_copy_id'));
            $transaction = $this->borrowService->borrowBook($request, $bookCopy, $request->boolean('is_from_reservation', false));
            return $this->jsonResponse('success', 'Book borrowed successfully', 201, ['transaction' => $transaction]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The borrower or book copy could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', $e .'Something went wrong while borrowing the book. Please try again later.', 500);
        }
    }

}