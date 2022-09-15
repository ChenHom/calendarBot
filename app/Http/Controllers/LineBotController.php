<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;

class LineBotController extends Controller
{
    public function index(Request $request)
    {
        $lineBotConfig = config('app.line_bot');

        $client = new CurlHTTPClient($lineBotConfig['channel_id']);
        $bot = new \LINE\LINEBot($client, ['channelSecret' => $lineBotConfig['channel_secret']]);

        $body = $request->getContent();
        foreach ($bot->parseEventRequest($body, $request->header(HTTPHeader::LINE_SIGNATURE)) as $event) {
            $replyToken = $event->getReplyToken();
            if ($event instanceof TextMessage) {
                Log::info([$event->getText(), $replyToken]);
                // $bot->replyMessage($replyToken, new TextMessageBuilder($event->getText()));

                // $bot->replyMessage($replyToken, new TemplateMessageBuilder(
                //     '選擇',
                //     new ButtonTemplateBuilder('行事曆', '文字', actionBuilders: [
                //         new DatetimePickerTemplateActionBuilder('選擇時間', 'storeId=12345', 'datetime')
                //     ])
                // ));
            }
        }
    }
}
