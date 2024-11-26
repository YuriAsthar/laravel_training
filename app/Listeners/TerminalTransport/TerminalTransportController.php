<?php

namespace App\Listeners\TerminalTransport;

use App\Models\TerminalTransport;
use App\Resources\TerminalTransport as TerminalTransportResource;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TerminalTransportController
{
    public function __construct(private readonly Factory $auth, private readonly TerminalTransport $terminalTransport)
    {
    }

    public function index(Request $request): JsonResource
    {
        return TerminalTransportResource::collection($this->terminalTransport->latest()->with(['transport', 'terminal'])->simplePaginate(
            $request->input('per_page', 10),
        ));
    }
}
