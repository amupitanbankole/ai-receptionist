<?php

namespace App\Http\Controllers;

use App\Models\CampaignRecipient;
use App\Models\Suppression;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class CampaignUnsubscribeController extends Controller
{
    public function __invoke(Request $request, CampaignRecipient $recipient): Response
    {
        abort_unless($request->hasValidSignature(), 403);

        Suppression::updateOrCreate(
            ['email' => strtolower($recipient->email)],
            [
                'reason' => 'unsubscribe',
                'suppressed_at' => now(),
            ]
        );

        $recipient->update([
            'status' => 'unsubscribed',
            'next_step_at' => null,
        ]);

        return response('<!doctype html><html><head><meta charset="utf-8"><title>Unsubscribed</title></head><body style="font-family:Arial,sans-serif;padding:40px"><h1>You have been unsubscribed.</h1><p>You will not receive further campaign emails from us.</p></body></html>');
    }
}
