<?php
session_start();

// セッションから送信データを取得
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : null;
// メール送信状態を取得
$mailStatus = isset($_SESSION['mail_status']) ? $_SESSION['mail_status'] : null;

// リダイレクトされてきたか確認（直接アクセスを防止）
if (!$formData) {
    header('Location: reservation-form.html');
    exit;
}

// セッションをクリア
unset($_SESSION['form_data']);
unset($_SESSION['mail_status']);

// メール送信が成功したかどうか
$isMailSuccess = isset($mailStatus) && isset($mailStatus['customer_mail_sent']) && $mailStatus['customer_mail_sent'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>送信完了 - テックチャレンジ | 大人のパソコン&プログラミング教室</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="reservation-style.css">
    <link rel="stylesheet" href="thank-you-style.css">
    <style>
        /* インラインスタイル追加 - 送信結果強調表示用 */
        .email-status-banner {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 1.1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .email-success {
            background-color: #e3f7e9;
            border-left: 5px solid var(--primary);
            color: var(--primary);
        }
        
        .email-warning {
            background-color: #ffe6e6;
            border-left: 5px solid #e74c3c;
            color: #c0392b;
        }
        
        .email-status-banner i {
            font-size: 1.6rem;
            margin-right: 15px;
        }
        
        .success-icon-large {
            width: 100px;
            height: 100px;
            margin: 20px auto 30px;
        }
        
        .warning-icon {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>
<!-- ヘッダー -->
<header>
<div class="container header-content">
  <a href="index.html" class="logo">
    <img src="https://placehold.jp/2c8d6a/ffffff/300x60.png?text=TechChallenge" alt="テックチャレンジ | 大人のパソコン&プログラミング教室">
  </a>
  
  <button class="mobile-menu-btn">
    <i class="fas fa-bars"></i>
  </button>
  
  <nav class="main-nav">
    <ul>
      <li><a href="index.html#intro">サービス紹介</a></li>
      <li><a href="index.html#recommendations">こんな方にオススメ</a></li>
      <li><a href="index.html#courses">学習内容</a></li>
      <li><a href="index.html#features">当教室の特長</a></li>
      <li><a href="index.html#flow">受講の流れ</a></li>
      <li><a href="index.html#access">アクセス</a></li>
      <li><a href="reservation-form.html">お問い合わせ</a></li>
    </ul>
  </nav>
</div>
</header>

<!-- ページヘッダー -->
<section class="page-header">
  <div class="container">
    <h1 class="page-title">送信<?php echo $isMailSuccess ? '完了' : '受付'; ?></h1>
  </div>
</section>

<!-- パンくずリスト -->
<div class="breadcrumb">
  <div class="container breadcrumb-inner">
    <a href="index.html">ホーム</a>
    <i class="fas fa-chevron-right"></i>
    <span>送信<?php echo $isMailSuccess ? '完了' : '受付'; ?></span>
  </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="form-container">
            <h2 class="section-title">
                <?php if ($isMailSuccess): ?>
                    お問い合わせありがとうございます
                <?php else: ?>
                    お問い合わせを受け付けました
                <?php endif; ?>
            </h2>
            
            <!-- メール送信状態を強調表示（上部に配置） -->
            <?php if (isset($mailStatus)): ?>
                <?php if ($isMailSuccess): ?>
                    <div class="email-status-banner email-success">
                        <i class="fas fa-envelope-circle-check"></i>
                        <span>送信内容の確認メールをお送りしました。お客様のメールボックスをご確認ください。</span>
                    </div>
                <?php else: ?>
                    <div class="email-status-banner email-warning">
                        <i class="fas fa-triangle-exclamation"></i>
                        <span>メール送信に問題が発生しました。お手数ですがお電話（03-1234-5678）でのご連絡をお願いいたします。</span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        
            <div class="thank-you-message">
                <?php if ($isMailSuccess): ?>
                    <!-- 成功時の表示 -->
                    <div class="success-icon success-icon-large">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p class="message-text">お問い合わせを受け付けました。担当者より折り返しご連絡いたします。</p>
                <?php else: ?>
                    <!-- エラー時の表示 -->
                    <div class="success-icon success-icon-large warning-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <p class="message-text warning-message">申し訳ございませんが、メール送信に問題が発生しました。<br>データは受け付けておりますので、お電話にてご連絡ください。</p>
                <?php endif; ?>
                
                <?php if ($formData): ?>
                <div class="submission-details">
                    <h3>送信内容</h3>
                    <div class="confirmation-list">
                        <?php if (isset($formData['last-name']) && isset($formData['first-name'])): ?>
                        <div class="confirmation-item">
                            <div class="confirmation-label">お名前</div>
                            <div class="confirmation-value"><?php echo htmlspecialchars($formData['last-name'] . ' ' . $formData['first-name']); ?></div>
                        </div>
                        <?php elseif (isset($formData['name'])): ?>
                        <div class="confirmation-item">
                            <div class="confirmation-label">お名前</div>
                            <div class="confirmation-value"><?php echo htmlspecialchars($formData['name']); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="confirmation-item">
                            <div class="confirmation-label">メールアドレス</div>
                            <div class="confirmation-value"><?php echo htmlspecialchars($formData['email']); ?></div>
                        </div>
                        
                        <?php if (!empty($formData['phone'])): ?>
                        <div class="confirmation-item">
                            <div class="confirmation-label">電話番号</div>
                            <div class="confirmation-value"><?php echo htmlspecialchars($formData['phone']); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($formData['interest'])): ?>
                        <div class="confirmation-item">
                            <div class="confirmation-label">ご興味のある内容</div>
                            <div class="confirmation-value"><?php echo htmlspecialchars($formData['interest']); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($formData['message'])): ?>
                        <div class="confirmation-item">
                            <div class="confirmation-label">お問い合わせ内容</div>
                            <div class="confirmation-value"><?php echo nl2br(htmlspecialchars($formData['message'])); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($formData['date1'])): ?>
                        <div class="confirmation-item">
                            <div class="confirmation-label">希望日時（第1希望）</div>
                            <div class="confirmation-value">
                                <?php 
                                echo htmlspecialchars($formData['date1']['year']) . '年 ' . 
                                     htmlspecialchars($formData['date1']['month']) . '月 ' . 
                                     htmlspecialchars($formData['date1']['day']) . '日 ' . 
                                     htmlspecialchars($formData['date1']['time']); 
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($formData['date2'])): ?>
                        <div class="confirmation-item">
                            <div class="confirmation-label">希望日時（第2希望）</div>
                            <div class="confirmation-value">
                                <?php 
                                echo htmlspecialchars($formData['date2']['year']) . '年 ' . 
                                     htmlspecialchars($formData['date2']['month']) . '月 ' . 
                                     htmlspecialchars($formData['date2']['day']) . '日 ' . 
                                     htmlspecialchars($formData['date2']['time']); 
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($formData['date3'])): ?>
                        <div class="confirmation-item">
                            <div class="confirmation-label">希望日時（第3希望）</div>
                            <div class="confirmation-value">
                                <?php 
                                echo htmlspecialchars($formData['date3']['year']) . '年 ' . 
                                     htmlspecialchars($formData['date3']['month']) . '月 ' . 
                                     htmlspecialchars($formData['date3']['day']) . '日 ' . 
                                     htmlspecialchars($formData['date3']['time']); 
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!$isMailSuccess): ?>
                <div class="contact-info-box">
                    <h3>お電話でのお問い合わせ</h3>
                    <p><i class="fas fa-phone"></i> <strong>03-1234-5678</strong>（受付時間：平日10:00〜21:00、土日祝10:00〜18:00）</p>
                    <p>お問い合わせの際に「Webからの予約」とお伝えいただくとスムーズです。</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <a href="index.html" class="btn btn-primary"><i class="fas fa-home"></i> トップページに戻る</a>
            </div>
        </div>
    </div>
</section>

<!-- フッター -->
<footer>
<div class="container">
  <div class="footer-content">
    <div class="footer-info">
      <div class="footer-logo">テックチャレンジ</div>
      <p class="footer-desc">初心者から上級者まで、あなたのITスキルアップを全力でサポートします。マンツーマン指導で確実にスキルを身につけましょう。</p>
    </div>
    
    <div class="footer-nav">
      <div class="footer-kids-info">
        <h4><i class="fas fa-child"></i> 子供向けプログラミング教室も開講中！</h4>
        <p>小学生・中学生向けのプログラミング教室も運営しています。<br>楽しみながら論理的思考力を身につけるカリキュラムをご用意。</p>
        <a href="#" class="footer-kids-link">子供向け教室の詳細はこちら <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
    
    <div class="footer-links">
      <a href="reservation-form.html">相談予約フォーム</a> | <a href="privacy-policy.html">プライバシーポリシー</a>
    </div>
  </div>
  
  <div class="footer-bottom">
    <p class="copyright">© 2025 テックチャレンジ | 大人のパソコン&プログラミング教室 All Rights Reserved.</p>
  </div>
</div>
</footer>

<!-- トップへ戻るボタン -->
<div id="back-to-top" class="back-to-top">
  <a href="#" aria-label="ページトップへ戻る">
    <i class="fas fa-arrow-up"></i>
  </a>
</div>

<script src="script.js"></script>
</body>
</html>