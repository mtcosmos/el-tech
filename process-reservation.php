<?php
session_start();

// POSTリクエストかどうか確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reservation-form.html');
    exit;
}

// フォームデータを取得
$formData = [
    'last-name' => isset($_POST['last-name']) ? $_POST['last-name'] : '',
    'first-name' => isset($_POST['first-name']) ? $_POST['first-name'] : '',
    'email' => isset($_POST['email']) ? $_POST['email'] : '',
    'phone' => isset($_POST['phone']) ? $_POST['phone'] : '',
    'age-group' => isset($_POST['age-group']) ? $_POST['age-group'] : '',
    'interest' => isset($_POST['interest']) ? $_POST['interest'] : '',
    'experience' => isset($_POST['experience']) ? $_POST['experience'] : '',
    'consultation-type' => isset($_POST['consultation-type']) ? $_POST['consultation-type'] : '',
    'message' => isset($_POST['message']) ? $_POST['message'] : '',
    // 希望日時情報
    'date1' => [
        'year' => isset($_POST['date1-year']) ? $_POST['date1-year'] : '',
        'month' => isset($_POST['date1-month']) ? $_POST['date1-month'] : '',
        'day' => isset($_POST['date1-day']) ? $_POST['date1-day'] : '',
        'time' => isset($_POST['date1-time']) ? $_POST['date1-time'] : ''
    ]
];

// 第2希望と第3希望が入力されていれば追加
if (!empty($_POST['date2-year']) && !empty($_POST['date2-month']) && 
    !empty($_POST['date2-day']) && !empty($_POST['date2-time'])) {
    $formData['date2'] = [
        'year' => $_POST['date2-year'],
        'month' => $_POST['date2-month'],
        'day' => $_POST['date2-day'],
        'time' => $_POST['date2-time']
    ];
}

if (!empty($_POST['date3-year']) && !empty($_POST['date3-month']) && 
    !empty($_POST['date3-day']) && !empty($_POST['date3-time'])) {
    $formData['date3'] = [
        'year' => $_POST['date3-year'],
        'month' => $_POST['date3-month'],
        'day' => $_POST['date3-day'],
        'time' => $_POST['date3-time']
    ];
}

// 必須項目の検証
$requiredFields = [
    'last-name', 'first-name', 'email', 'phone',
    'interest', 'experience', 'consultation-type'
];

$errors = [];
foreach ($requiredFields as $field) {
    if (empty($formData[$field])) {
        $errors[] = $field;
    }
}

// 第1希望日時の検証
if (empty($formData['date1']['year']) || empty($formData['date1']['month']) || 
    empty($formData['date1']['day']) || empty($formData['date1']['time'])) {
    $errors[] = 'date1';
}

// プライバシーポリシーの同意確認
if (!isset($_POST['privacy-policy'])) {
    $errors[] = 'privacy-policy';
}

// エラーがある場合はリダイレクトして表示
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $formData; // 入力データを戻す
    header('Location: reservation-form.html#error');
    exit;
}

// 選択肢の表示名を取得する関数
function getInterestName($value) {
    $options = [
        'office' => 'オフィスソフト（Word, Excel, PowerPoint など）',
        'web' => 'Web基礎（HTML, CSS, WordPress など）',
        'programming' => 'プログラミング入門（Python, JavaScript など）',
        'design' => 'デザイン入門（Canva, Photoshop など）',
        'other' => 'その他'
    ];
    
    return isset($options[$value]) ? $options[$value] : $value;
}

function getExperienceName($value) {
    $options = [
        'beginner' => '初心者（経験なし）',
        'basic' => '基礎レベル（少し使ったことがある）',
        'intermediate' => '中級者（趣味や仕事で使用経験あり）',
        'advanced' => '上級者（さらなるスキルアップを目指したい）'
    ];
    
    return isset($options[$value]) ? $options[$value] : $value;
}

function getConsultationTypeName($value) {
    $options = [
        'in-person' => '対面で相談したい',
        'online' => 'オンラインで相談したい'
    ];
    
    return isset($options[$value]) ? $options[$value] : $value;
}

// 確認表示用に日本語の月名と曜日を取得する関数
function getJapaneseDateTime($year, $month, $day, $time) {
    $months = [
        1 => '1月', 2 => '2月', 3 => '3月', 4 => '4月', 5 => '5月', 6 => '6月',
        7 => '7月', 8 => '8月', 9 => '9月', 10 => '10月', 11 => '11月', 12 => '12月'
    ];
    
    $dateString = $year . '年 ' . $months[(int)$month] . ' ' . $day . '日 ' . $time;
    
    return $dateString;
}

// 表示用のデータを整形
$_SESSION['display_data'] = [
    'full_name' => $formData['last-name'] . ' ' . $formData['first-name'],
    'email' => $formData['email'],
    'phone' => $formData['phone'],
    'interest' => getInterestName($formData['interest']),
    'experience' => getExperienceName($formData['experience']),
    'consultation_type' => getConsultationTypeName($formData['consultation-type']),
    'message' => $formData['message'],
    'date1' => getJapaneseDateTime(
        $formData['date1']['year'], 
        $formData['date1']['month'], 
        $formData['date1']['day'], 
        $formData['date1']['time']
    )
];

// 追加日時があれば表示用データに追加
if (isset($formData['date2'])) {
    $_SESSION['display_data']['date2'] = getJapaneseDateTime(
        $formData['date2']['year'], 
        $formData['date2']['month'], 
        $formData['date2']['day'], 
        $formData['date2']['time']
    );
}

if (isset($formData['date3'])) {
    $_SESSION['display_data']['date3'] = getJapaneseDateTime(
        $formData['date3']['year'], 
        $formData['date3']['month'], 
        $formData['date3']['day'], 
        $formData['date3']['time']
    );
}

// 送信時に表示用のデータをセッションに格納
$_SESSION['form_data'] = $formData;

// ======= メール送信処理 =======

// 管理者へのメール
$admin_email = 'info@tech-challenge.example.com'; // 管理者のメールアドレスを設定
$subject = '【テックチャレンジ】無料相談予約を受け付けました';

// メール本文の作成
$message = "テックチャレンジに無料相談予約が入りました。\n\n";
$message .= "■ お客様情報 ■\n";
$message .= "氏名: " . $formData['last-name'] . ' ' . $formData['first-name'] . "\n";
$message .= "メールアドレス: " . $formData['email'] . "\n";
$message .= "電話番号: " . $formData['phone'] . "\n";
if (!empty($formData['age-group'])) {
    $message .= "年齢層: " . $formData['age-group'] . "\n";
}
$message .= "ご興味のある内容: " . getInterestName($formData['interest']) . "\n";
$message .= "スキルレベル: " . getExperienceName($formData['experience']) . "\n";
$message .= "相談方法: " . getConsultationTypeName($formData['consultation-type']) . "\n\n";

$message .= "■ 希望日時 ■\n";
$message .= "第1希望: " . $_SESSION['display_data']['date1'] . "\n";
if (isset($_SESSION['display_data']['date2'])) {
    $message .= "第2希望: " . $_SESSION['display_data']['date2'] . "\n";
}
if (isset($_SESSION['display_data']['date3'])) {
    $message .= "第3希望: " . $_SESSION['display_data']['date3'] . "\n";
}
$message .= "\n";

if (!empty($formData['message'])) {
    $message .= "■ ご質問・ご要望 ■\n";
    $message .= $formData['message'] . "\n\n";
}

$message .= "※このメールはシステムより自動送信されています。";

// メールヘッダーの設定
$headers = 'From: ' . $formData['email'] . "\r\n" .
           'Reply-To: ' . $formData['email'] . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

// 文字コードを指定（日本語メールの場合）
$subject = mb_encode_mimeheader($subject, "ISO-2022-JP", "UTF-8");
$message = mb_convert_encoding($message, "ISO-2022-JP", "UTF-8");
$headers = mb_encode_mimeheader($headers, "ISO-2022-JP", "UTF-8");

// 管理者へのメール送信
$admin_mail_sent = @mail($admin_email, $subject, $message, $headers);

// お客様へのお礼メール
$customer_subject = '【テックチャレンジ】無料相談予約を受け付けました';
$customer_message = $formData['last-name'] . ' ' . $formData['first-name'] . " 様\n\n";
$customer_message .= "この度は、テックチャレンジの無料相談予約をお申し込みいただき、誠にありがとうございます。\n";
$customer_message .= "以下の内容で予約を受け付けました。\n\n";

$customer_message .= "■ ご予約内容 ■\n";
$customer_message .= "ご相談内容: " . getInterestName($formData['interest']) . "\n";
$customer_message .= "相談方法: " . getConsultationTypeName($formData['consultation-type']) . "\n\n";

$customer_message .= "■ 希望日時 ■\n";
$customer_message .= "第1希望: " . $_SESSION['display_data']['date1'] . "\n";
if (isset($_SESSION['display_data']['date2'])) {
    $customer_message .= "第2希望: " . $_SESSION['display_data']['date2'] . "\n";
}
if (isset($_SESSION['display_data']['date3'])) {
    $customer_message .= "第3希望: " . $_SESSION['display_data']['date3'] . "\n";
}
$customer_message .= "\n";

$customer_message .= "営業担当より、ご希望の日時でのご予約可否を2営業日以内にご連絡いたします。\n";
$customer_message .= "なお、ご不明な点やご質問がございましたら、お気軽にお問い合わせください。\n\n";

$customer_message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$customer_message .= "テックチャレンジ | 大人のパソコン&プログラミング教室\n";
$customer_message .= "〒123-4567 東京都新宿区新宿1-2-3 テックビル 5階\n";
$customer_message .= "TEL: 03-1234-5678\n";
$customer_message .= "Email: info@tech-challenge.example.com\n";
$customer_message .= "URL: https://www.tech-challenge.example.com\n";
$customer_message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// 文字コードを指定（日本語メールの場合）
$customer_subject = mb_encode_mimeheader($customer_subject, "ISO-2022-JP", "UTF-8");
$customer_message = mb_convert_encoding($customer_message, "ISO-2022-JP", "UTF-8");

// お客様用のヘッダー
$customer_headers = 'From: ' . $admin_email . "\r\n" .
                   'Reply-To: ' . $admin_email . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();
$customer_headers = mb_encode_mimeheader($customer_headers, "ISO-2022-JP", "UTF-8");

// お客様へのメール送信
$customer_mail_sent = @mail($formData['email'], $customer_subject, $customer_message, $customer_headers);

// メール送信結果をセッションに保存
$_SESSION['mail_status'] = [
    'admin_mail_sent' => $admin_mail_sent,
    'customer_mail_sent' => $customer_mail_sent
];

// 処理が完了したら完了ページへリダイレクト
header('Location: thank-you.php');
exit;