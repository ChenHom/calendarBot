<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineBotController extends Controller
{
    public function index(Request $request)
    {
        $lineBotConfig = config('app.line_bot');

        $client = new CurlHTTPClient($lineBotConfig['channel_id']);
        $bot = new \LINE\LINEBot($client, ['channelSecret' => $lineBotConfig['channel_secret']]);

        $body = $request->getContent();

        foreach ($bot->parseEventRequest($body, $request->header(HTTPHeader::LINE_SIGNATURE)) as $event) {
            if ($event instanceof TextMessage) {
                Log::info([$event->getUserId(), $event->getText()]);
                $bot->pushMessage($event->getUserId(), new TextMessageBuilder($event->getText()));
            }
        }
    }
}
