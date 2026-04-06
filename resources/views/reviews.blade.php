@extends('layouts.master')
@section('content')
<style>
    /* تحسينات عامة للفورم */
    .review-form-section {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
        margin-top: 72px;
        border: 1px solid var(--border-color);
    }

    .review-form {
        max-width: 862px;
        margin: 0 auto;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
    }

    .form-row .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .form-control,
    textarea {
        width: 100%;
        padding: 17px;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--background-light);
        font-family: inherit;
        text-align: right;
        color: #5f6c79;
    }

    .form-control:hover,
    textarea:hover {
        border-color: #F28123;
        background: white;
        box-shadow: 0 5px 15px rgba(242, 129, 35, 0.2);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    .form-control:focus,
    textarea:focus {
        border-color: #F28123;
        background: white;
        box-shadow: 0 0 19px rgba(242, 129, 35, 0.3), 0 0 0 2px rgba(242, 129, 35, 0.2);
        transform: perspective(500px) translateZ(5px);
        transition: all 0.3s ease;
        outline: none;
    }

    /* تحسينات قسم النجوم في الفورم */
    .rating-section {
        background: var(--background-light);
        border: 2px solid var(--border-color);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }

    .rating-section:hover {
        border-color: #F28123;
        box-shadow: 0 5px 15px rgba(242, 129, 35, 0.1);
    }

    .rating-label {
        display: block;
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 15px;
        font-size: 1.1rem;
        text-align: center;
    }


    .star-rating:hover {
        border-color: #F28123;
        background: white;
        box-shadow: 0 5px 15px rgba(242, 129, 35, 0.2);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    .star-rating {
        font-size: 0;
        background: var(--background-light);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        display: flex;
        flex-direction: row-reverse;
        height: 38px;
        align-items: center;
        justify-content: flex-end;
        width: 100%;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        font-size: 30px;
        color: #ddd;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0 5px;
        padding: 5px;
        border-radius: 50%;
    }

    .star-rating label:hover {
        color: #ffc107 !important;
        transform: scale(1.2);

    }

    .star-rating input:checked+label,
    .star-rating input:checked+label~label {
        color: #ffc107;
        text-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
    }

    .rating-feedback {
        text-align: center;
        margin-top: 15px;
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-color);
        min-height: 25px;
        padding: 8px;
        background: linear-gradient(135deg, rgba(242, 129, 35, 0.1), rgba(255, 193, 7, 0.05));
        border-radius: 8px;
        transition: all 0.3s ease;
        border-color: rgb(242, 129, 35);
    }

    .rating-feedback.active {
        background: rgba(242, 129, 35, 0.1);
        border: 1px solid;
        border-color: rgb(242, 129, 35);
    }

    /* تحسين زر الإرسال */
    .submit-btn {
        background: linear-gradient(135deg, var(--primary-color), #e67e22);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(242, 129, 35, 0.3);
        display: block;
        margin: 30px auto 0;
        position: relative;
        overflow: hidden;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(242, 129, 35, 0.4);
    }

    .submit-btn:active {
        transform: translateY(-1px);
    }

    .submit-btn i {
        margin-left: 8px;
        transition: transform 0.3s ease;
    }

    .submit-btn:hover i {
        transform: translateX(5px);
    }

    /* تحسينات للأخطاء */
    .text-danger {
        color: #dc3545;
        font-size: 0.9rem;
        display: block;
        margin-top: 8px;
        text-align: right;
        padding: 5px 10px;
        background: rgba(220, 53, 69, 0.05);
        border-radius: 5px;
        border-right: 3px solid #dc3545;
    }

    /* رسالة الترحيب للمستخدم المسجل */
    .welcome-message {
        background: linear-gradient(135deg, #fffaf1, #fdf3e9);
        border: 2px solid #f28123;
        border-radius: 12px;
        padding: 6px;
        margin-bottom: 21px;
        text-align: center;
        color: #000000;
        font-weight: 600;
        display: none;
    }

    .welcome-message i {
        margin-left: 10px;
        color: #ec6b00;
    }

    /* بقية الأنماط كما هي */
    :root {
        --primary-color: #F28123;
        --secondary-color: #2c3e50;
        --background-light: #f8f9fa;
        --border-color: #e9ecef;
    }

    .reviews-page {
        direction: rtl;
        background: var(--background-light);
        min-height: 100vh;
        padding: 50px 0;
        font-family: 'Arial', sans-serif;
    }

    .section-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--secondary-color);
        text-align: center;
        margin-bottom: 40px;
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        right: 50%;
        transform: translateX(50%);
        width: 100px;
        height: 4px;
        background: linear-gradient(135deg, var(--primary-color), #e67e22);
        border-radius: 2px;
    }

    /* تصميم الكومنتات (يبقى كما هو) */
    .reviews-list-section {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
    }

    .comments-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .comment-card {
        max-width: 100%;
        margin: 20px auto;
        padding: 25px;
        background-color: var(--background-light);
        border-radius: 12px;
        font-family: Arial, sans-serif;
        border: 1px solid var(--border-color);
        position: relative;
        transition: all 0.3s ease;
    }

    .comment-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        transform: translateY(-3px);
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--primary-color);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 18px;
        font-weight: bold;
        margin-left: 10px;
    }

    .name {
        font-weight: 600;
        color: var(--secondary-color);
        font-size: 16px;
        text-transform: uppercase;
        padding: 7px;
    }

    .source-info {
        display: flex;
        align-items: center;
    }

    .handle {
        color: var(--primary-color);
        font-size: 14px;
        margin-left: 8px;
    }

    .circle-m1 {
        border-radius: 50%;
        background-color: #28a745;
        width: 15px;
        height: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 5px;
    }

    .verified-checkmark {
        color: #ffffff;
        font-size: 9px;
        font-weight: bold;
    }

    .stars {
        font-size: 20px;
        margin-bottom: 10px;
        direction: ltr;
        text-align: right;
    }

    .stars span {
        color: #ffc107;
        margin-left: -2px;
    }

    .comment-text {
        color: var(--secondary-color);
        font-size: 16px;
        line-height: 1.5;
        margin-bottom: 15px;
        font-weight: 500;
        text-align: right;
    }

    .comment-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: #95a5a6;
    }

    .hashtag {
        color: var(--primary-color);
        font-weight: bold;
        text-decoration: none;
    }

    .time {
        color: #95a5a6;
        display: flex;
    }

    /* تحسينات للشاشات الصغيرة */
    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
        }

        .rating-section {
            padding: 15px;
        }

        .star-rating label {
            font-size: 28px;
            margin: 0 3px;
        }

        .star-rating-wrapper {
            max-width: 100%;
        }

        .comment-header {
            display: flex;
        }

        .source-info {
            margin-right: auto;
        }

        .comment-footer {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .handle {
            font-size: 10px;
            margin-left: 0px;
        }

        .circle-m1 {
            width: 11px;
            height: 11px;
        }

        .verified-checkmark {
            font-size: 5px;
        }

        .name {
            font-size: 12px;
            padding: 0px;
        }
    }

    .no-reviews {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }

    .no-reviews i {
        font-size: 4rem;
        color: #bdc3c7;
        margin-bottom: 20px;
    }
</style>

<div class="reviews-page">
    <div class="container">

        <div class="review-form-section">
            <h2 class="section-title">شاركنا برأيك</h2>

            <!-- رسالة الترحيب للمستخدم المسجل -->


            <div class="review-form col-lg-14">
                <form method="POST" action="/storeReview">
                    @csrf()
                    <div class="form-group col-lg-14">
                        <div class="welcome-message" id="welcomeMessage">
                            <i class="fas fa-user-check"></i>
                            <span id="welcomeText">مرحباً بك ! سيتم تعبئة بياناتك تلقائياً</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg-6">
                            <input type="text" name="name" class="form-control" placeholder="اسمك الكريم"
                                value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}" required
                                id="nameInput">
                            @error('name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-lg-6">
                            <input type="email" name="email" class="form-control" placeholder="بريدك الإلكتروني"
                                value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}"
                                id="emailInput">
                            @error('email')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-lg-6">
                            <input type="tel" name="phone" class="form-control" placeholder="رقم هاتفك"
                                value="{{ old('phone', auth()->check() ? auth()->user()->phone ?? '' : '') }}" required
                                id="phoneInput">
                            @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group col-lg-6">
                            <div class="star-rating">
                                <input type="radio" id="star5" name="subject" value="5-stars" {{
                                    old('subject')=='5-stars' ? 'checked' : '' }} required>
                                <label for="star5" title="ممتاز - 5 نجوم">&#9733;</label>

                                <input type="radio" id="star4" name="subject" value="4-stars" {{
                                    old('subject')=='4-stars' ? 'checked' : '' }}>
                                <label for="star4" title="جيد جداً - 4 نجوم">&#9733;</label>

                                <input type="radio" id="star3" name="subject" value="3-stars" {{
                                    old('subject')=='3-stars' ? 'checked' : '' }}>
                                <label for="star3" title="جيد - 3 نجوم">&#9733;</label>

                                <input type="radio" id="star2" name="subject" value="2-stars" {{
                                    old('subject')=='2-stars' ? 'checked' : '' }}>
                                <label for="star2" title="مقبول - 2 نجوم">&#9733;</label>

                                <input type="radio" id="star1" name="subject" value="1-star" {{ old('subject')=='1-star'
                                    ? 'checked' : '' }}>
                                <label for="star1" title="سيء - 1 نجمة">&#9733;</label>
                                <span class="name" style="color:#5f6c79;font-size:13px;padding: 12px;">تقييمك يهمنى
                                    :</span>
                            </div>
                            @error('subject')
                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <textarea name="message" class="form-control" placeholder="اكتب رأيك بالتفصيل..." rows="5"
                            required>{{ old('message') }}</textarea>
                        @error('message')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="rating-feedback" id="ratingFeedback"> تقييمك لمنتجاتنا يهمنا 💛</div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> نشر الرأي
                    </button>
                </form>
            </div>
        </div>

        <!-- بقية الكود الخاص بالتعليقات يبقى كما هو -->
        <div class="reviews-list-section">
            <h2 class="section-title">آراء العملاء</h2>
            <div class="comments-container">
                @if (isset($reviews) && $reviews->count() > 0)
                @foreach ($reviews as $item)
                <div class="comment-card">
                    <div class="comment-header">
                        <div class="reviewer-info">
                            <div class="avatar">
                                {{ mb_substr($item->name, 0, 1) ?? 'U' }}
                            </div>
                            <span class="name">{{ $item->name ?? 'مستخدم' }}</span>
                        </div>
                        <div class="source-info">
                            <div class="circle-m1">
                                <span class="verified-checkmark">&#10003;</span>
                            </div>
                            <span class="handle">{{ $item->email ?? 'لا يوجد بريد' }}</span>
                        </div>
                    </div>

                    <div class="stars">
                        @php
                        $stars = 3;
                        $subject = $item->subject ?? '';

                        if (str_contains($subject, '5') || $subject == 'happy' || $subject == 'love') {
                        $stars = 5;
                        } elseif (str_contains($subject, '4')) {
                        $stars = 4;
                        } elseif (str_contains($subject, '3') || $subject == 'neutral') {
                        $stars = 3;
                        } elseif (str_contains($subject, '2') || $subject == 'confused') {
                        $stars = 2;
                        } elseif (str_contains($subject, '1') || $subject == 'angry') {
                        $stars = 1;
                        }
                        @endphp

                        @for ($i = 1; $i <= 5; $i++) @if ($i <=$stars) <span>&#9733;</span>
                            @else
                            <span style="color: #ddd;">&#9733;</span>
                            @endif
                            @endfor
                    </div>

                    <div class="comment-text">
                        {{ $item->message ?? 'هذا رأي العميل حول المنتج أو الخدمة.' }}
                    </div>
                    <span class="time">
                        @if (isset($item->created_at))
                        {{ $item->created_at->diffForHumans() }}
                        @else
                        منذ فترة
                        @endif
                    </span>
                </div>
                @endforeach
                @else
                <!-- نموذج التعليق الافتراضي -->
                <div class="comment-card">
                    <div class="comment-header">
                        <div class="reviewer-info">
                            <div class="avatar">A</div>
                            <span class="name">ANIL KUMAR</span>
                        </div>
                        <div class="source-info">
                            <div class="circle-m1">
                                <span class="verified-checkmark">&#10003;</span>
                            </div>
                            <span class="handle">@arabesco.ae</span>
                        </div>
                    </div>

                    <div class="stars">
                        <span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
                    </div>

                    <div class="comment-text">
                        Excellent service!!! Highly professional team members and 100% satisfied with their service.
                        Highly recommending to those who are coming first time in UAE. All the best to the entire
                        team at Arabesco!!
                    </div>

                    <div class="comment-footer">
                        <span class="hashtag">#betterlivinguae</span>
                        <span class="time">6 years ago</span>
                    </div>
                </div>

                <div class="no-reviews">
                    <i class="fas fa-comments"></i>
                    <h3>لا توجد آراء أخرى حتى الآن</h3>
                    <p>كن أول من يشارك رأيه حول منتجاتنا</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if ($reviews->count() > 3)
    <div class="text-center mt-4 d-flex justify-content-center gap-3 flex-wrap">
        <button id="showMoreBtn" class="submit-btn ">عرض المزيد من الآراء</button>
        <button id="showLessBtn" class="submit-btn" style="display: none; background: #6c757d;">
            عرض عدد أقل من الآراء
        </button>
    </div>
    @endif
</div>

<script>
    // التحقق من تسجيل الدخول وعرض رسالة الترحيب
        document.addEventListener('DOMContentLoaded', function() {
            const welcomeMessage = document.getElementById('welcomeMessage');
            const welcomeText = document.getElementById('welcomeText');
            const nameInput = document.getElementById('nameInput');
            const emailInput = document.getElementById('emailInput');
            const phoneInput = document.getElementById('phoneInput');

            // التحقق إذا كان المستخدم مسجل الدخول (في Laravel)
            @if (auth()->check())
                const userName = "{{ auth()->user()->name }}";
                const userEmail = "{{ auth()->user()->email }}";
                const userPhone = "{{ auth()->user()->phone ?? '' }}";

                // عرض رسالة الترحيب
                welcomeMessage.style.display = 'block';
                welcomeText.textContent = `مرحباً ${userName} سيتم تعبئة بياناتك تلقائياً`;

                // تعبئة الحقول تلقائياً إذا كانت فارغة
                if (!nameInput.value) nameInput.value = userName;
                if (!emailInput.value) emailInput.value = userEmail;
                if (!phoneInput.value && userPhone) phoneInput.value = userPhone;

                // إضافة تأثير للحقول المعبأة تلقائياً
                if (nameInput.value === userName) {
                    nameInput.style.background = 'linear-gradient(135deg, rgba(242, 129, 35, 0.1), rgba(255, 193, 7, 0.05))';
                    nameInput.style.borderColor = '#F28123';
                }
                if (emailInput.value === userEmail) {
                    emailInput.style.background = 'linear-gradient(135deg, rgba(242, 129, 35, 0.1), rgba(255, 193, 7, 0.05))';
                    emailInput.style.borderColor = '#F28123';
                }
                if (phoneInput.value === userPhone && userPhone) {
                    phoneInput.style.background = 'linear-gradient(135deg, rgba(242, 129, 35, 0.1), rgba(255, 193, 7, 0.05))';
                    phoneInput.style.borderColor = '#F28123';
                }
            @else
                // إخفاء رسالة الترحيب إذا لم يكن مسجلاً
                welcomeMessage.style.display = 'none';
            @endif

            // تحسين التفاعل مع النجوم
            const starInputs = document.querySelectorAll('.star-rating input');
            const ratingFeedback = document.getElementById('ratingFeedback');

            const ratingMessages = {
                '1-star': '⭐ (سيء - تحتاج لتحسين كبير)',
                '2-stars': '⭐⭐ (مقبول - يحتاج لتطوير)',
                '3-stars': '⭐⭐⭐ (جيد - راضي عن الخدمة)',
                '4-stars': '⭐⭐⭐⭐ (جيد جداً - تجربة ممتازة)',
                '5-stars': '⭐⭐⭐⭐⭐ (ممتاز - تجربة رائعة)'
            };

            starInputs.forEach(star => {
                star.addEventListener('change', function() {
                    const value = this.value;
                    ratingFeedback.textContent = ratingMessages[value];
                    ratingFeedback.style.color = '#f28123';
                    ratingFeedback.style.fontWeight = 'bold';
                    ratingFeedback.classList.add('active');
                });

                star.addEventListener('mouseenter', function() {
                    const value = this.value;
                    ratingFeedback.textContent = ratingMessages[value];
                    ratingFeedback.style.color = '#666';
                });
            });

            const starRating = document.querySelector('.star-rating');
            if (starRating) {
                starRating.addEventListener('mouseleave', function() {
                    const checked = document.querySelector('.star-rating input:checked');
                    if (!checked) {

                        ratingFeedback.style.color = '#666';
                        ratingFeedback.style.fontWeight = 'normal';
                        ratingFeedback.classList.remove('active');
                    }
                });
            }
        });

        // تأثيرات الكروت
        document.addEventListener('DOMContentLoaded', function() {
            const commentCards = document.querySelectorAll('.comment-card');
            commentCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.15)';
                    this.style.transform = 'translateY(-3px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
                    this.style.transform = 'translateY(0)';
                });
            });
        });

        // عرض المزيد/عرض أقل
        document.addEventListener('DOMContentLoaded', function() {
            const comments = document.querySelectorAll('.comment-card');
            const showMoreBtn = document.getElementById('showMoreBtn');
            const showLessBtn = document.getElementById('showLessBtn');
            const step = 3;
            let visibleCount = 3;

            function updateVisibility() {
                comments.forEach((comment, index) => {
                    comment.style.display = index < visibleCount ? 'block' : 'none';
                });

                if (visibleCount >= comments.length) {
                    showMoreBtn.style.display = 'none';
                    showLessBtn.style.display = 'inline-block';
                } else if (visibleCount > 3) {
                    showMoreBtn.style.display = 'inline-block';
                    showLessBtn.style.display = 'inline-block';
                } else {
                    showMoreBtn.style.display = 'inline-block';
                    showLessBtn.style.display = 'none';
                }
            }

            if (comments.length > 0) {
                updateVisibility();

                showMoreBtn?.addEventListener('click', function() {
                    visibleCount = Math.min(visibleCount + step, comments.length);
                    updateVisibility();
                });

                showLessBtn?.addEventListener('click', function() {
                    visibleCount = Math.max(visibleCount - step, 3);
                    updateVisibility();
                });
            }
        });
</script>
@endsection