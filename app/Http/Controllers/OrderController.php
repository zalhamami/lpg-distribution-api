<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Order;
use App\OrderStatus;
use App\Repositories\OrderRepository;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends ApiController
{
    /**
     * OrderController constructor.
     * @param OrderRepository $repo
     */
    public function __construct(OrderRepository $repo) {
        $this->repo = $repo;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->repo->getAll();
        return $this->collectionData($data);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->repo->getById($id);
        return $this->singleData($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyOrders(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:buyer,seller'],
            'level' => ['required', 'in:user,agent,supplier'],
        ]);

        $user = $request->user();
        $targetId = $user->id;
        if ($request['level'] === 'agent') {
            $agent = Agent::where('user_id', $user->id)->firstOrFail();
            $targetId = $agent->id;
        }
        if ($request['level'] === 'supplier') {
            $supplier = Supplier::where('user_id', $user->id)->firstOrFail();
            $targetId = $supplier->id;
        }

        $data = $this->repo->getAllUserOrders($targetId, $request['type'], $request['level']);
        return $this->collectionData($data);
    }

    public function storeStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => ['required', Rule::in([
                OrderStatus::CREATED,
                OrderStatus::APPROVED,
                OrderStatus::REJECTED,
                OrderStatus::PROCESSED,
                OrderStatus::FINISHED,
            ])],
            'note' => ['nullable', 'string'],
        ]);

        $order = $this->repo->getById($id);
        $status = $order->status()->create($request->all());

        if ($request['status'] === OrderStatus::FINISHED && $order->buyer_type === Order::TYPE_AGENT) {
            $this->saveAgentStocks($order);
        }

        return $this->singleData($status, 201);
    }

    /**
     * @param Order $order
     */
    private function saveAgentStocks(Order $order)
    {
        $agent = Agent::findOrFail($order->buyer_id);
        foreach ($order->items as $item) {
            $agentStock = $agent->stocks->filter(function ($stock) use (&$item) {
                return $stock->product_id === $item->product_id;
            })->first();

            if ($agentStock) {
                $agentStock->quantity += $item->quantity;
                $agentStock->save();
            } else {
                $agent->stocks()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }
    }
}
