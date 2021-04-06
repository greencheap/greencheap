<?php

namespace GreenCheap\Mail\Controller;

use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\User\Annotation\Access;
use GreenCheap\Util\Arr;

/**
 * @Access("system: access settings", admin=true)
 */
class MailController
{
    /**
     * @Request({"option": "array"}, csrf=true)
     * @param array $option
     * @return array
     */
    public function smtpAction($option = []): array
    {
        try {
            App::mailer()->testSmtpConnection($option["host"], $option["port"], $option["username"], $option["password"], $option["encryption"]);

            return ["success" => true, "message" => __("Connection established!")];
        } catch (\Exception $e) {
            return ["success" => false, "message" => sprintf(__("Connection not established! (%s)"), $e->getMessage())];
        }
    }

    /**
     * Note: If the mailer is accessed prior to this controller action, this will possibly test the wrong mailer
     *
     * @Request({"option": "array"}, csrf=true)
     * @param array $option
     * @return array
     */
    public function emailAction($option = []): array
    {
        try {
            $config = Arr::merge(App::module("system/mail")->config(), $option);

            $response["success"] = (bool) App::mailer()
                ->create(__("Test email!"), __("Testemail"), $config["from_address"])
                ->send();
            $response["message"] = $response["success"] ? __("Mail successfully sent!") : __("Mail delivery failed!");
        } catch (\Exception $e) {
            $response = ["success" => false, "message" => sprintf(__("Mail delivery failed! (%s)"), $e->getMessage())];
        }

        return $response;
    }
}
