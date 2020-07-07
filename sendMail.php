<?php
require(__DIR__ . '/vendor/autoload.php');
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

/*
    aws SES メール送信サンプル
    認証は、~/.aws/credentials

    公式 document
    https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.Ses.SesClient.html
*/

// 設定値
$config = (object) [
    'region' => 'ap-northeast-1', // region
    'profile' => 'default',    // 使用するprofile
    'senderName' => 'てすとくん', // 送信者名
    'senderEmail' => 'example@example.com', // 送信者アドレス  (aws console で検証済みであること)
    'toAddress' => [  // 宛先アドレス
        'user1@example.com',
        'user2@example.com',
    ],
    'ccAddress' => ['user3@example.com'],
    'bccAddress' => ['user4@example.com'],
    'subject' => 'てすてす',
    'plaintext_body' => 'メール本文だよ',
    'html_body' => '<b style="color:red">メール本文</b>だよ',
    'char_set' => 'ISO-2022-JP', // UTF-8, ISO-2022-JP など
];

$SesClient = new SesClient([
    'region'  => $config->region,
    'profile' => $config->profile,
    'version' => '2010-12-01',
]);

try {
    $result = $SesClient->sendEmail([
        'Destination' => [
            'ToAddresses' => $config->toAddress,
            'CcAddresses' => $config->ccAddress,
            'BccAddresses' => $config->bccAddress,
        ],
        'ReplyToAddresses' => [$config->senderEmail],
        'Source' => mb_encode_mimeheader($config->senderName, $config->char_set) . ' <' . $config->senderEmail . '>',
        'Message' => [
          'Body' => [
              'Html' => [
                  'Charset' => $config->char_set,
                  'Data' => $config->html_body,
              ],
              'Text' => [
                  'Charset' => $config->char_set,
                  'Data' => $config->plaintext_body,
              ],
          ],
          'Subject' => [
              'Charset' => $config->char_set,
              'Data' => $config->subject,
          ],
        ],
    ]);
    $messageId = $result['MessageId'];
    echo "Email sent! Message ID: $messageId", PHP_EOL;
} catch (AwsException $e) {
    echo $e->getMessage(), PHP_EOL;
    echo "aws error message ", $e->getAwsErrorMessage(), PHP_EOL;
}
