<?php

namespace App\Http\Controllers;

use App\Services\ReturnService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Policies\ReturnPolicy;
use App\Models\BorrowTransaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ReturnController extends Controller
{
    protected $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function validateReturn(Request $request)
    {
        $borrower = User::find($request->input('borrower_id'));
        if (!$borrower) {
            return ['result' => 'not_found', 'message' => 'Borrower not found.'];
        }

        $bookCopyId = $request->input('book_copy_id');
        if (!$bookCopyId) {
            return ['result' => 'not_found', 'message' => 'Missing book copy identifier.'];
        }

        // Check if active transaction exists
        $transaction = BorrowTransaction::where('user_id', $borrower->id)
            ->where('book_copy_id', $bookCopyId)
            ->whereNull('returned_at')
            ->first();

        if (!$transaction) {
            return ['result' => 'not_found', 'message' => 'No active borrow transaction found for this book.'];
        }

        $result = ReturnPolicy::canBeReturned($transaction, $borrower);
        
        switch ($result['result']) {
            case 'success': return $this->jsonResponse('valid', 'Validation passed', 200, ['returner_fullname' => $result['returner_fullname']]);
            case 'not_found': return $this->jsonResponse('not_found', $result['message'], 404);
            case 'business_rule_violation': return $this->jsonResponse('business_rule_violation', $result['message'], 400);
            default: return $this->jsonResponse('error', 'Unknown validation error', 500);
        }

         return ['status' => 'success'];
    }

    public function performReturn(Request $request)
    {
        try {
            $transaction = $this->returnService->performReturn($request);
            return $this->jsonResponse('success', 'Book returned successfully', 200, ['transaction' => $transaction, 'action_performed_by' => $request->user()->role]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The book or transaction could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while returning the book. Please try again later.', 500);
        }
    }
}
