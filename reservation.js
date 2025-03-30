// 予約フォームの日付選択に関するJavaScript

// フォーム内の要素を取得
document.addEventListener('DOMContentLoaded', function() {
  const reservationForm = document.getElementById('reservation-form');
  const stepIndicators = document.querySelectorAll('.form-step');
  const basicInfoDiv = document.getElementById('basic-info');
  const dateTimeSelectionDiv = document.getElementById('date-time-selection');
  const confirmationDiv = document.getElementById('confirmation-section');

  // ボタン
  const goToStep2Button = document.querySelector('#basic-info .btn-primary');
  const backToStep1Button = document.getElementById('back-to-step1');
  const goToStep3Button = document.getElementById('go-to-step3');
  const backToStep2Button = document.getElementById('back-to-step2');
  const submitFormButton = document.getElementById('submit-form');

  // 各年月日の選択要素を取得
  const yearSelects = [
    document.getElementById('date1-year'),
    document.getElementById('date2-year'),
    document.getElementById('date3-year')
  ];

  const monthSelects = [
    document.getElementById('date1-month'),
    document.getElementById('date2-month'),
    document.getElementById('date3-month')
  ];

  const daySelects = [
    document.getElementById('date1-day'),
    document.getElementById('date2-day'),
    document.getElementById('date3-day')
  ];

  // 指定要素までスクロールする関数
  function scrollToElement(element, offset = 30) {
    if (!element) return;
    
    // フォームコンテナまでスクロール（余白を考慮）
    const formContainer = document.querySelector('.form-container');
    if (formContainer) {
      const rect = formContainer.getBoundingClientRect();
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      const targetPosition = scrollTop + rect.top - offset;
      
      window.scrollTo({
        top: targetPosition,
        behavior: 'smooth'
      });
    }
  }

  // 日数を更新する関数
  function updateDays(yearSelect, monthSelect, daySelect) {
    // 既存の選択を保存
    const selectedDay = daySelect.value;
    
    // 日の選択肢をリセット（PHPでセット済みの選択値があれば保持）
    const selectedOption = daySelect.querySelector('option[selected]');
    daySelect.innerHTML = '<option value="">選択</option>';
    if (selectedOption) {
      daySelect.appendChild(selectedOption.cloneNode(true));
    }
    
    // 年と月が選択されていない場合は更新しない
    if (!yearSelect.value || !monthSelect.value) {
      return;
    }
    
    // 選択された年と月の最終日を取得
    const year = parseInt(yearSelect.value);
    const month = parseInt(monthSelect.value);
    const lastDay = new Date(year, month, 0).getDate();
    
    // 日の選択肢を追加
    for (let i = 1; i <= lastDay; i++) {
      // すでに選択済みの値と同じなら追加しない
      if (selectedOption && parseInt(selectedOption.value) === i) {
        continue;
      }
      const option = document.createElement('option');
      option.value = i;
      option.textContent = `${i}日`;
      daySelect.appendChild(option);
    }
    
    // 以前の選択が有効なら復元
    if (selectedDay && parseInt(selectedDay) <= lastDay) {
      daySelect.value = selectedDay;
    }
  }

  // 年月の変更イベントをリッスン
  for (let i = 0; i < 3; i++) {
    if (yearSelects[i] && monthSelects[i] && daySelects[i]) {
      yearSelects[i].addEventListener('change', function() {
        updateDays(yearSelects[i], monthSelects[i], daySelects[i]);
      });
      
      monthSelects[i].addEventListener('change', function() {
        updateDays(yearSelects[i], monthSelects[i], daySelects[i]);
      });
    }
  }

  // フォームステップ間の移動処理
  if (goToStep2Button) {
    goToStep2Button.addEventListener('click', function(e) {
      e.preventDefault();
      console.log('希望日時選択ボタンがクリックされました'); // デバッグ用
      
      // 基本情報の入力チェック（簡易的なバリデーション）
      const requiredInputs = basicInfoDiv.querySelectorAll('input[required], select[required]');
      let isValid = true;
      
      requiredInputs.forEach(input => {
        if (!input.value.trim()) {
          isValid = false;
          input.classList.add('error');
        } else {
          input.classList.remove('error');
        }
      });
      
      // プライバシーポリシーの同意確認
      const privacyCheckbox = document.getElementById('privacy-policy');
      if (!privacyCheckbox.checked) {
        isValid = false;
        privacyCheckbox.parentElement.classList.add('error');
      } else {
        privacyCheckbox.parentElement.classList.remove('error');
      }
      
      if (!isValid) {
        alert('必須項目を入力し、プライバシーポリシーに同意してください。');
        return;
      }
      
      // ステップ2に移動
      basicInfoDiv.style.display = 'none';
      dateTimeSelectionDiv.style.display = 'block';
      stepIndicators[1].classList.add('active');
      
      // フォームコンテナまでスクロール
      scrollToElement(dateTimeSelectionDiv);
    });
  } else {
    console.error('希望日時選択ボタンが見つかりません'); // デバッグ用
  }

  // 「基本情報へ戻る」ボタンの処理
  if (backToStep1Button) {
    backToStep1Button.addEventListener('click', function() {
      dateTimeSelectionDiv.style.display = 'none';
      basicInfoDiv.style.display = 'block';
      
      // フォームコンテナまでスクロール
      scrollToElement(basicInfoDiv);
    });
  }

  // 「確認画面へ進む」ボタンの処理
  if (goToStep3Button) {
    goToStep3Button.addEventListener('click', function() {
      // 第1希望の入力チェック
      if (!document.getElementById('date1-year').value ||
          !document.getElementById('date1-month').value ||
          !document.getElementById('date1-day').value ||
          !document.getElementById('date1-time').value) {
        alert('第1希望の日時は必須です。年月日と時間をすべて選択してください。');
        return;
      }
      
      // 入力内容の確認画面へ移動する処理
      dateTimeSelectionDiv.style.display = 'none';
      if (confirmationDiv) {
        confirmationDiv.style.display = 'block';
        
        // 入力内容を確認画面に反映
        // 基本情報
        document.getElementById('confirm-name').textContent = 
          document.getElementById('last-name').value + ' ' + document.getElementById('first-name').value;
        document.getElementById('confirm-email').textContent = document.getElementById('email').value;
        document.getElementById('confirm-phone').textContent = document.getElementById('phone').value;
        
        // 選択項目のテキストを取得する関数
        function getSelectedText(selectId) {
          const select = document.getElementById(selectId);
          return select.value ? select.options[select.selectedIndex].text : '未選択';
        }
        
        document.getElementById('confirm-age').textContent = getSelectedText('age-group') || '未選択';
        document.getElementById('confirm-interest').textContent = getSelectedText('interest');
        document.getElementById('confirm-experience').textContent = getSelectedText('experience');
        document.getElementById('confirm-type').textContent = getSelectedText('consultation-type');
        document.getElementById('confirm-message').textContent = document.getElementById('message').value || '記入なし';
        
        // 日時情報
        // 第1希望
        const date1Text = `${document.getElementById('date1-year').value}年 ${getSelectedText('date1-month')} ${getSelectedText('date1-day')} ${document.getElementById('date1-time').value}`;
        document.getElementById('confirm-date1').textContent = date1Text;
        
        // 第2希望（入力されている場合のみ）
        if (document.getElementById('date2-year').value && 
            document.getElementById('date2-month').value && 
            document.getElementById('date2-day').value && 
            document.getElementById('date2-time').value) {
          const date2Text = `${document.getElementById('date2-year').value}年 ${getSelectedText('date2-month')} ${getSelectedText('date2-day')} ${document.getElementById('date2-time').value}`;
          document.getElementById('confirm-date2').textContent = date2Text;
          document.getElementById('date2-container').style.display = 'block';
        } else {
          document.getElementById('date2-container').style.display = 'none';
        }
        
        // 第3希望（入力されている場合のみ）
        if (document.getElementById('date3-year').value && 
            document.getElementById('date3-month').value && 
            document.getElementById('date3-day').value && 
            document.getElementById('date3-time').value) {
          const date3Text = `${document.getElementById('date3-year').value}年 ${getSelectedText('date3-month')} ${getSelectedText('date3-day')} ${document.getElementById('date3-time').value}`;
          document.getElementById('confirm-date3').textContent = date3Text;
          document.getElementById('date3-container').style.display = 'block';
        } else {
          document.getElementById('date3-container').style.display = 'none';
        }
        
        stepIndicators[2].classList.add('active');
        
        // フォームコンテナまでスクロール
        scrollToElement(confirmationDiv);
      }
    });
  }

  // 戻るボタンの処理
  if (backToStep2Button) {
    backToStep2Button.addEventListener('click', function() {
      confirmationDiv.style.display = 'none';
      dateTimeSelectionDiv.style.display = 'block';
      
      // フォームコンテナまでスクロール
      scrollToElement(dateTimeSelectionDiv);
    });
  }

  // 初期表示設定
  if (basicInfoDiv) basicInfoDiv.style.display = 'block';
  if (dateTimeSelectionDiv) dateTimeSelectionDiv.style.display = 'none';
  if (confirmationDiv) confirmationDiv.style.display = 'none';

  // 初期の日付選択肢を設定
  for (let i = 0; i < 3; i++) {
    if (yearSelects[i] && monthSelects[i] && daySelects[i]) {
      updateDays(yearSelects[i], monthSelects[i], daySelects[i]);
    }
  }

  // URLからエラーフラグを確認して、エラーメッセージを表示
  if (window.location.hash === '#error') {
    const errorContainer = document.getElementById('error-container');
    if (errorContainer) {
      errorContainer.style.display = 'block';
      
      // エラーが表示されている場所までスクロール
      errorContainer.scrollIntoView({ behavior: 'smooth' });
    }
  }
});