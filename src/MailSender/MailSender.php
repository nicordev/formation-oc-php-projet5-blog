<?php

namespace Application\MailSender;

class MailSender
{
    /**
     * Send a mail
     *
     * @param string $to
     * @param string $subject
     * @param string|null $message
     * @param string|null $from
     * @param string|null $replyTo
     * @param bool|null $htmlContent
     * @return bool
     */
    public static function send(string $to, string $subject, ?string $message = null, ?string $from = null, ?string $replyTo = null, ?bool $htmlContent = false): bool
    {
        return mail($to, $subject, $message, self::buildHeader($from, $replyTo, $htmlContent));
    }

    /**
     * @param string|null $from
     * @param string|null $replyTo
     * @param bool|null $html
     * @return string|null
     */
    private static function buildHeader(?string $from = null, ?string $replyTo = null, ?bool $html = false): ?string
    {
        $header = null;

        if ($from) {
            $header = 'From:' . $from;
        }

        if ($replyTo) {
            $header = $header ?? $header . self::newLine();
            $header .= 'Reply-To: ' . $replyTo;
        }

        if ($html) {
            $header = $header ?? $header . self::newLine();
            $header .= 'Content-type: text/html; charset= utf8';
        } else {
            $header = $header ?? $header . self::newLine();
            $header .= 'Content-type: text/plain; charset= utf8';
        }

        return $header;
    }

    /**
     * @return string
     */
    private static function newLine(): string
    {
        return "\r\n";
    }
}