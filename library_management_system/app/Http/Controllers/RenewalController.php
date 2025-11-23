<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RenewalService;
use App\Models\User;
use App\Models\BorrowTransaction;
use Illuminate\Support\Facades\Log;
use App\Policies\RenewalPolicy;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RenewalController extends Controller
{
    protected $renewalService;

    public function __construct(RenewalService $renewalService)
    {
        $this->renewalService = $renewalService;
    }

    public function validateRenewal(Request $request)
    {
        $renewer = User::find($request->input('renewer_id'));
        $transaction = BorrowTransaction::where('id', $request->input('transaction_id'))
            ->where('user_id', $renewer?->id)
            ->whereNull('returned_at')
            ->first();

        $result = RenewalPolicy::canRenew($renewer, $transaction, true, $request->all());
        
        switch ($result['result']) {
                case 'success': return $this->jsonResponse('valid', 'Validation passed', 200, ['renewer_fullname' => $result['renewer_fullname']]);
                case 'not_found': return $this->jsonResponse('not_found', $result['message'], 404);
                case 'invalid_input': return $this->jsonResponse('invalid_input', $result['message'], 422, ['errors' => $result['errors']]);
                case 'business_rule_violation': return $this->jsonResponse('business_rule_violation', $result['message'], 400);
                default: return $this->jsonResponse('error', 'Unknown validation error', 500);
        }
    }

    public function performRenewal(Request $request)
    {
        try {
            $transaction = $this->renewalService->renewBook($request);
            return $this->jsonResponse('success', 'Book renewed successfully', 200, ['transaction' => $transaction]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The book or transaction could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while renewing the book. Please try again later.', 500);
        }
    }
}
