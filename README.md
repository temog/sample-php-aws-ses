# aws SES 利用手順

## Domain を認証する

DKIM

```
DKIM (DomainKeys Identified Mail)は、 電子メールにおける送信ドメイン認証技術の一つであり、 メールを送信する際に送信元が電子署名を行い、 受信者がそれを検証することで、 送信者のなりすましやメールの改ざんを検知できるようにするもの
```

これをやらない場合、amazonses.com 経由 (Gmail の場合) などとメーラで表示される

1. aws console ログイン
2. SES を表示
3. Domains > Verify a New Domain
4. ドメイン入れる、Generate DKIM Settings にチェックして Verify This Domain
5. dns の設定(txt, cname) が表示されるので ドメインの dns 設定をやる
   - mx は受信しない場合不要
6. dns 設定後しばらく待つ (最大 72 時間 ?)
7. Verification Status と DKIM Status が verified になったら完了

## 送信元のアドレスを認証する

1. Email Addresses
2. Verify New Email Address
3. メールアドレス入れて、Verify this email address
4. メールを受信して URL を開き認証する

## 認証したアドレス以外に送信できるようにする

- 初期状態は sandbox なので認証していないアドレスに送信できない。
- サポートから申請して sandbox を外す

1. サポート > サポートセンター > Craete case
2. Service limit increase
3. Case details
   - Limit type : SES 送信制限
   - メールの種類 : やることに応じて
   - ウェブサイトの URL : 任意
   - メールを明確にリクエストした受信者のみに送信する方法 : 利用許諾を認可したユーザ、とか
   - バウンス通知、および苦情通知を受け取った場合に
4. Requests
   - Request 1
     - リージョン : Asia Pacific (Tokyo) とか
     - Limit : 1 日あたりの送信クォータ
     - New limit value : 1 日あたりに送信可能な最大数
     - Add another request をぽちる
   - Request 2
     - リージョン : Asia Pacific (Tokyo) とか
     - Limit : 希望する最大送信レート
     - New limit value : 1 秒あたりに送信できるメール数
5. Case description
   - メール送信のユースケースを記述する。
6. submit

処理されるのに結構時間がかかるかもしれない

## aws sdk を使ってメール送信

```
composer install
```

sendMail.php の 設定値と ~/.aws/credentials を設定して実行
