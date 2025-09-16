<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TravelOrder;
use App\Notifications\TravelOrderApproved;
use App\Notifications\TravelOrderCancelled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class TravelOrderController extends Controller
{
    /**
     * Create a new travel order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'requester_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date|after:today',
            'return_date' => 'required|date|after:departure_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $travelOrder = TravelOrder::create([
            'user_id' => Auth::id(),
            'requester_name' => $request->requester_name,
            'destination' => $request->destination,
            'departure_date' => $request->departure_date,
            'return_date' => $request->return_date,
            'status' => TravelOrder::STATUS_REQUESTED,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Travel order created successfully',
            'data' => $travelOrder->load('user')
        ], 201);
    }

    /**
     * Get all travel orders for authenticated user
     */
    public function index(Request $request)
    {
        $query = TravelOrder::where('user_id', Auth::id());

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by destination
        if ($request->has('destination')) {
            $query->byDestination($request->destination);
        }

        // Filter by period
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byPeriod($request->start_date, $request->end_date);
        }

        // Filter by departure date range
        if ($request->has('departure_start') && $request->has('departure_end')) {
            $query->whereBetween('departure_date', [$request->departure_start, $request->departure_end]);
        }

        $travelOrders = $query->with('user')->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $travelOrders
        ]);
    }

    /**
     * Get specific travel order
     */
    public function show($id)
    {
        $travelOrder = TravelOrder::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $travelOrder->load('user')
        ]);
    }

    /**
     * Update travel order status (Admin only)
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:aprovado,cancelado',
            'cancellation_reason' => 'required_if:status,cancelado|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $travelOrder = TravelOrder::findOrFail($id);

        if (!Auth::user()->is_admin && $travelOrder->user_id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change the status of your own travel order'
            ], 403);
        }

        $oldStatus = $travelOrder->status;
        
        if ($request->status === 'aprovado') {
            if (!$travelOrder->approve()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Travel order cannot be approved in its current status'
                ], 422);
            }
            
            // Send notification
            $travelOrder->user->notify(new TravelOrderApproved($travelOrder));
            
        } elseif ($request->status === 'cancelado') {
            if (!$travelOrder->cancel($request->cancellation_reason)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Travel order cannot be cancelled in its current status'
                ], 422);
            }
            
            // Send notification
            $travelOrder->user->notify(new TravelOrderCancelled($travelOrder));
        }

        return response()->json([
            'success' => true,
            'message' => 'Travel order status updated successfully',
            'data' => $travelOrder->load('user')
        ]);
    }

    /**
     * Cancel travel order by user
     */
    public function cancel(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $travelOrder = TravelOrder::where('user_id', Auth::id())->findOrFail($id);

        if (!$travelOrder->canBeCancelledByUser()) {
            return response()->json([
                'success' => false,
                'message' => 'Travel order cannot be cancelled by user in its current status'
            ], 422);
        }

        if (!$travelOrder->cancel($request->cancellation_reason)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel travel order'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Travel order cancelled successfully',
            'data' => $travelOrder->load('user')
        ]);
    }

    /**
     * Get all travel orders (Admin view)
     */
    public function adminIndex(Request $request)
    {
        $query = TravelOrder::query();

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by destination
        if ($request->has('destination')) {
            $query->byDestination($request->destination);
        }

        // Filter by period
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byPeriod($request->start_date, $request->end_date);
        }

        // Filter by departure date range
        if ($request->has('departure_start') && $request->has('departure_end')) {
            $query->whereBetween('departure_date', [$request->departure_start, $request->departure_end]);
        }

        $travelOrders = $query->with('user')->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $travelOrders
        ]);
    }
}