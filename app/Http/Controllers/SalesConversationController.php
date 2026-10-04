<?php

namespace App\Http\Controllers;

use App\Models\SalesConversation;
use App\Services\SalesConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesConversationController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = SalesConversation::with(['company', 'contact', 'lead'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('intent'), fn ($q) => $q->where('intent', $request->string('intent')))
            ->latest('last_message_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('sales_conversations.index', compact('conversations'));
    }

    public function show(SalesConversation $conversation): View
    {
        $conversation->load([
            'company',
            'contact',
            'lead',
            'messages' => fn ($q) => $q->oldest('id'),
            'aiReplySuggestions' => fn ($q) => $q->latest('id')->limit(10),
        ]);

        return view('sales_conversations.show', compact('conversation'));
    }

    public function generate(SalesConversation $conversation, SalesConversationService $service): RedirectResponse
    {
        try {
            $service->generateReplySuggestion($conversation);

            return back()->with('success', 'AI reply suggestion generated.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'AI reply generation failed. Check storage/logs/laravel.log for details.');
        }
    }

    public function status(Request $request, SalesConversation $conversation, SalesConversationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,waiting,closed'],
        ]);

        $service->markStatus($conversation, $validated['status']);

        return back()->with('success', 'Conversation status updated.');
    }

    public function simulate(): View
    {
        return view('sales_conversations.simulate');
    }

    public function simulateInbound(Request $request, SalesConversationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'lead_id' => ['nullable', 'exists:leads,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'from_address' => ['required', 'email'],
            'to_address' => ['nullable', 'email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        try {
            $conversation = $service->ingestInbound($validated);

            return redirect()
                ->route('sales-conversations.show', $conversation)
                ->with('success', 'Inbound reply captured and analyzed.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Inbound reply processing failed. Check storage/logs/laravel.log for details.');
        }
    }

    public function webhook(Request $request, SalesConversationService $service): JsonResponse
    {
        $expected = (string) config('services.sales_inbound.webhook_token');
        $provided = (string) $request->bearerToken();

        if (!$expected || !$provided || !hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'from_address' => ['required', 'email'],
            'to_address' => ['nullable', 'email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'channel' => ['nullable', 'in:email,whatsapp,sms'],
            'provider_message_id' => ['nullable', 'string', 'max:255'],
            'lead_id' => ['nullable', 'integer'],
            'company_id' => ['nullable', 'integer'],
            'contact_id' => ['nullable', 'integer'],
            'campaign_recipient_id' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
        ]);

        $conversation = $service->ingestInbound($validated);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'intent' => $conversation->intent,
            'priority' => $conversation->priority,
            'next_action' => $conversation->next_action,
        ], 201);
    }
}
