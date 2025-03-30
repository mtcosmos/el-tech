// モバイルメニューの表示/非表示
document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
  document.querySelector('.main-nav').classList.toggle('active');
});

// スクロール時のヘッダー変更
window.addEventListener('scroll', function() {
  const header = document.querySelector('header');
  if (window.scrollY > 100) {
    header.style.padding = '10px 0';
    header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
  } else {
    header.style.padding = '15px 0';
    header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
  }
});

// スムーススクロールの実装
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    
    const targetId = this.getAttribute('href');
    if (targetId === '#') return;
    
    const targetElement = document.querySelector(targetId);
    if (!targetElement) return;
    
    const headerHeight = document.querySelector('header').offsetHeight;
    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
    
    window.scrollTo({
      top: targetPosition,
      behavior: 'smooth'
    });
    
    // モバイルメニューが開いている場合は閉じる
    if (window.innerWidth <= 768) {
      document.querySelector('.main-nav').classList.remove('active');
    }
  });
});

// タブ切り替え
const tabBtns = document.querySelectorAll('.tab-btn');
tabBtns.forEach(btn => {
  btn.addEventListener('click', function() {
    // タブボタンのアクティブ状態を切り替え
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    
    // タブコンテンツの表示/非表示を切り替え
    const tabId = this.getAttribute('data-tab');
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
  });
});

// トップへ戻るボタンの表示/非表示
const backToTopButton = document.getElementById('back-to-top');

window.addEventListener('scroll', () => {
  if (window.scrollY > 300) {
    backToTopButton.classList.add('visible');
  } else {
    backToTopButton.classList.remove('visible');
  }
});

// トップへ戻るボタンのクリックイベント
backToTopButton.addEventListener('click', function(e) {
  e.preventDefault();
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
});