<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);

        $body = $request->getContent();

        foreach ($bot->parseEventRequest($body, $signature) as $event) {
            if ($event instanceof TextMessage) {
                $bot->pushMessage($event->getUserId(), new TextMessageBuilder($event->getText()));
            }
        }
    }
}
