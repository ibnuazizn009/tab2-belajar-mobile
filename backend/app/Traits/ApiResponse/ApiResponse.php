<?php

namespace App\Traits\ApiResponse;

use Carbon\Carbon;
use App;

trait ApiResponse
{
    /**
     * Generate a success response.
     *
     * @param string $message
     * @param mixed|null $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse(string $message, $data = null, int $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'code' => $statusCode,
            'data' => $data,
            'team' => 'Tab2@team',
        ], $statusCode);
    }

    /**
     * Generate a success created response.
     *
     * @param string $message
     * @param mixed|null $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */

    protected function successCreatedResponse(string $message, $data = null, int $statusCode = 201)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'code' => $statusCode,
            'data' => $data,
            'team' => 'Tab2@team',
        ], $statusCode);
    }

    protected function successCreatedResponseNonMessage($data = null, int $statusCode = 201)
    {
        return response()->json([
            'success' => true,
            'code' => $statusCode,
            'data' => $data,
            'team' => 'Tab2@team',
        ], $statusCode);
    }

    protected function failedResponseNonMessage(int $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'code' => $statusCode,
            'team' => 'Tab2@team',
        ], $statusCode);
    }
    /**
     * Generate a failed response.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function failedResponse(string $message, int $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'code' => $statusCode,
            'message' => $message,
            'team' => 'Tab2@team',
        ], $statusCode);
    }

    /**
     * Generate an error response.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = 500)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $statusCode,
            'team' => 'Tab2@team',
        ], $statusCode);
    }

    /**
     * Generate a validation error response.
     *
     * @param array $errors
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationErrorResponse(array $errors, int $statusCode = 422)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $errors,
            'code' => $statusCode,
            'team' => 'Tab2@team',
        ], $statusCode);
    }

    /**
     * Generate a resource not found response.
     *
     * @param string $resource
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function resourceNotFoundResponse(string $resource, int $statusCode = 404)
    {
        return response()->json([
            'success' => false,
            'message' => "$resource",
            'code' => $statusCode,
            'team' => 'Tab2@team',
        ], $statusCode);
    }

    /**
     * Generate an unauthorized response.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized', int $statusCode = 401)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $statusCode,
            'team' => 'Tab2@team',
        ], $statusCode);
    }

    // protected function kdProfile()
    // {
    //     $profile = \App\Models\Profile\Profile::first();
    //     return $profile->kdprofile;
    // }

    // private function getMonthlyEmployeesCount($worksId, $year)
    // {
    //     $monthlyCounts = [];

    //     for ($month = 1; $month <= 12; $month++) {
    //         $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
    //         $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

    //         $count = App\Models\Master\Employee::where('works_id', $worksId)
    //             ->whereBetween('tglmasuk', [$startDate, $endDate])
    //             ->count();

    //         $monthlyCounts[] = $count;
    //     }

    //     return $monthlyCounts;
    // }

    // private function calculateAge($birthdate)
    // {
    //     return Carbon::parse($birthdate)->age;
    // }

    // private function PostInventoryMovement($request, $receiving, $item, $movement_type)
    // {
    //     $inventoryMovement = App\Models\Master\InventoryMovement::create([
    //         'kdprofile' => $this->kdProfile(),
    //         'statusenabled' => '1',
    //         'product_id' => $item->product_id,
    //         'warehouse_id' => $request->warehouse_id,
    //         'quantity' =>  $item->quantity,
    //         'movement_type' => $movement_type,
    //         'movement_date' => date('Y-m-d H:i:s'),
    //         'description' => 'Receiving ' . $receiving->receving_code . ' from ' . $receiving->purchaseOrder->supplier->name . ' for ' . $item->product->name . ' with quantity ' . $item->quantity . ' units',
    //     ]);
    //     if ($inventoryMovement) {
    //         $log = $this->logActivity('Create Inventory Movement in', $request, json_encode($inventoryMovement));
    //     }
    //     return $inventoryMovement;
    // }

    // private function PostInventoryMovementSo($request, $salesOrder, $item, $movement_type)
    // {
    //     $inventoryMovement = App\Models\Master\InventoryMovement::create([
    //         'kdprofile' => $this->kdProfile(),
    //         'statusenabled' => '1',
    //         'product_id' => $item->product_id,
    //         'warehouse_id' => $request->warehouse_id,
    //         'quantity' =>  $item->quantity,
    //         'movement_type' => $movement_type,
    //         'movement_date' => date('Y-m-d H:i:s'),
    //         'description' => 'Receiving ' . $salesOrder->so_number . ' To ' . $salesOrder->customer->namalengkap . ' for ' . $item->product->name . ' with quantity ' . $item->quantity . ' units',
    //     ]);
    //     if ($inventoryMovement) {
    //         $log = $this->logActivity('Create Inventory Movement in', $request, json_encode($inventoryMovement));
    //     }
    //     return $inventoryMovement;
    // }
}
