<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
    <style>
        /* Global Reset and Base Styles */
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box; /* Include padding and border in element's total width/height */
        }

        /* Smooth scrolling for the entire document */
        html {
            scroll-behavior: smooth;
        }

        /* Main body styling with grid pattern background */
        body {
            background-color: #fafafa;
            background-image: linear-gradient(to right, white 2px, transparent 0),
            linear-gradient(to bottom, white 2px, transparent 0);
            background-size: 22px 22px; /* Grid cell size */
            min-height: 100vh; /* Ensure body takes at least full viewport height */
        }

        /* Header section styling */
        .header {
            padding: 1.25rem 2.5rem; /* 20px 40px in rem units */
        }

        /* Logo image styling */
        .logo {
            height: 1.87rem; /* ~30px */
        }

        /* Hero section (main banner area) */
        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 4.375rem 0 3.75rem; /* 70px top, 60px bottom */
            gap: 1.5rem; /* Space between child elements */
        }

        /* Hero image styling */
        .hero img {
            width: 7.5rem; /* 120px */
        }

        /* Hero title text */
        .hero .title {
            font-size: 2.5rem; /* 40px */
            font-weight: bold;
            letter-spacing: 3px;
            color: hsla(219, 24%, 25%, 1); /* Dark blue-gray color */
        }

        /* Hero subtitle text */
        .hero .text {
            font-weight: 700;
            color: hsla(219, 24%, 25%, 1);
            font-size: 1.125rem; /* 18px */
            letter-spacing: 1px;
        }

        /* Main content container */
        .main {
            padding: 0 2.5rem;
            display: flex;
            gap: 2.1875rem; /* 35px */
            position: relative;
        }

        /* Primary content area */
        .content {
            flex: 1; /* Take remaining space */
        }

        /* Navigation sidebar */
        .nav {
            width: 20rem; /* 320px */
            background-color: white;
            border: 3px solid hsla(0, 0%, 97%, 1); /* Very light gray border */
            border-radius: 1.25rem; /* 20px */
            height: 44rem; /* Fixed height */
            overflow: visible;
            padding: 2.5rem 1.875rem; /* 40px 30px */
            display: flex;
            flex-direction: column;
            gap: 0.625rem; /* 10px between items */
            box-shadow: 0 0 18px rgba(243, 243, 243, 0.25);
            position: sticky;
            top: 0; /* Stick to top of viewport */
            right: 0;
        }

        /* Mini navigation for mobile */
        .nav-mini {
            display: none; /* Hidden by default */
            width: 3.75rem; /* 60px */
            height: 3.75rem;
            border: 3px solid rgba(246, 246, 246, 1);
            background-color: white;
            border-radius: 0.625rem; /* 10px */
            justify-content: center;
            align-items: center;
            font-size: 1.75rem; /* 28px */
            font-weight: 900;
            position: relative;
            color: rgba(49, 59, 79, 1); /* Dark blue-gray */
        }

        /* Triangle indicator for active nav item */
        .triangle {
            width: 0;
            height: 0;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-left: 12px solid rgba(232, 63, 36, 1); /* Red-orange color */
            position: absolute;
            left: -10px;
            top: calc(2.5rem + 5px); /* Position below first item */
            transition: top .2s ease; /* Smooth movement */
        }

        /* Navigation list items */
        .nav li {
            list-style: none;
            display: flex;
            justify-content: start;
            font-size: 1.125rem; /* 18px */
            color: rgba(147, 147, 147, 1); /* Gray color */
            letter-spacing: 1px;
            font-weight: 700;
            gap: 1.25rem; /* 20px */
            cursor: pointer;
            transition: color .3s ease;
            margin-bottom: 1rem; /* Space between items */
        }

        /* Navigation item hover state */
        .nav li:hover {
            color: rgba(49, 59, 79, 1); /* Dark blue-gray */
        }

        /* Navigation button styling */
        .nav button {
            width: 100%;
            border: 1px solid rgba(49, 59, 79, 1);
            color: rgba(49, 59, 79, 1);
            border-radius: 1.875rem; /* 30px */
            height: 2.1875rem; /* 35px */
            background-color: transparent;
            margin: 0;
            cursor: pointer;
            transition: all .3s ease;
        }

        /* Button hover effect */
        .nav button:hover {
            box-shadow: 0 0 .4rem rgb(223, 222, 222);
        }

        /* Content item cards (yellow background) */
        .content-item {
            background-color: #F9E500; /* Yellow */
            border-radius: 1.25rem; /* 20px */
            border: 6px solid rgba(241, 229, 0, 1);
            padding: 2.5rem 3.75rem; /* 40px 60px */
            gap: 2.5rem; /* 40px between children */
            display: flex;
            flex-direction: column;
        }

        /* Space between content items */
        .content-item:not(:last-child) {
            margin-bottom: 2.5rem; /* 40px */
        }

        /* Top section of content item */
        .content-item .item-top {
            display: flex;
            width: 100%;
            justify-content: space-between;
        }

        /* Image in content item */
        .content-item .item-top .index-img {
            width: 22.5rem; /* 360px */
            height: 11.25rem; /* 180px */
            border-radius: 1.25rem; /* 20px */
        }

        /* Name/Title section styling */
        .content-item .item-top .name {
            position: relative;
            width: 100%;
        }

        /* Large index number styling */
        .content-item .item-top .name .index {
            position: absolute;
            top: 50%;
            left: -1.875rem; /* -30px */
            transform: translateY(-50%);
            font-weight: 900;
            font-size: 10rem; /* 160px */
            color: rgba(225, 214, 0, 1); /* Light yellow */
            z-index: 0; /* Behind other content */
        }

        /* Title text styling */
        .content-item .item-top .name .index-title {
            font-weight: 700;
            font-size: 2rem; /* 32px */
            letter-spacing: 1px;
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            word-break: break-all; /* Prevent word breaking */
            color: rgba(49, 59, 79, 1); /* Dark blue-gray */
            z-index: 1; /* Above index number */
        }

        /* White card within content item */
        .content-item .item-card {
            background-color: white;
            border-radius: 1.25rem; /* 20px */
            padding: 1.25rem 2.5rem; /* 20px 40px */
        }

        /* Card title section */
        .content-item .item-card .item-card-title {
            display: flex;
            align-items: end;
        }

        /* Title content area */
        .content-item .item-card .item-card-title .title-content {
            flex: 1;
            color: rgba(49, 59, 79, 1);
            display: flex;
            flex-direction: column;
            gap: .5rem; /* 8px */
        }

        /* Price styling */
        .content-item .item-card .item-card-title .price {
            color: rgba(232, 63, 36, 1); /* Red-orange */
            font-weight: 700;
            font-size: 2rem; /* 32px */
        }

        /* Main title text */
        .content-item .item-card .item-card-title .title-content .title-text {
            font-size: 1.5rem; /* 24px */
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* Description text */
        .content-item .item-card .item-card-title .title-content .title-desc {
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 1rem; /* 16px */
        }

        /* Card content area */
        .content-item .item-card .item-card-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.625rem; /* 10px */
            margin-top: 0.625rem; /* 10px */
        }

        /* Individual card items */
        .content-item .item-card .item-card-content .item-card-item {
            flex: 1;
            font-weight: 700;
            color: rgba(49, 59, 79, 1);
            letter-spacing: 1px;
            font-size: 1rem; /* 16px */
            height: 4rem; /* 64px */
        }

        /* Item spans */
        .content-item .item-card .item-card-content .item-card-item span {
            display: block;
            margin: auto;
            height: 2rem; /* 32px */
            text-align: center;
        }

        /* Dashed border item */
        .content-item .item-card .item-card-content .item-card-item .item {
            border-radius: 1.25rem; /* 20px */
            border: 1px dashed rgba(219, 219, 219, 1); /* Light gray */
            padding: .25rem 1.25rem; /* 4px 20px */
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis; /* Handle overflow text */
        }

        /* Time display */
        .content-item .item-card .item-card-content .item-card-item .time {
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        /* Circular indicator */
        .content-item .item-card .item-card-content .item-card-item .circle {
            border-radius: 50%;
            border: 1px solid rgba(49, 59, 79, 1);
            width: 1rem; /* 16px */
            height: 1rem;
        }

        /* Section title styling */
        .content-title {
            background-color: rgba(245, 233, 0, 1); /* Yellow */
            border-radius: 3.75rem; /* 60px */
            border: 3px solid rgba(241, 229, 0, 1);
            padding: 0.625rem; /* 10px */
            text-align: center;
            font-size: 2rem; /* 32px */
            color: rgba(49, 59, 79, 1);
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 4rem; /* 64px */
        }

        /* Cases section */
        .content-cases {
            padding: 5rem 3.75rem 1.875rem; /* 80px 60px 30px */
        }

        /* Case title */
        .content-cases .case-title {
            font-weight: 700;
            font-size: 2rem; /* 32px */
            letter-spacing: 1px;
            color: rgba(49, 59, 79, 1);
            margin-bottom: 1.25rem; /* 20px */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Case price number */
        .content-cases .case-title .price .num {
            font-size: 3rem; /* 48px */
            font-weight: 700;
            color: rgba(232, 63, 36, 1); /* Red-orange */
            margin-left: 1.25rem; /* 20px */
        }

        /* Case row container */
        .content-cases .case-row {
            display: flex;
            gap: 1.25rem; /* 20px */
            justify-content: space-around;
            height: 26.25rem; /* 420px */
        }

        /* Space between case rows */
        .content-cases .case-row:not(:last-child) {
            margin-bottom: 5rem; /* 80px */
        }

        /* Individual case column */
        .content-cases .case-col {
            background-color: white;
            border-radius: 1.25rem; /* 20px */
            border: 3px solid rgba(240, 240, 240, 1);
            padding: 1.25rem; /* 20px */
            height: 100%;
            position: relative;
        }

        /* Case image */
        .content-cases .case-img {
            width: 100%;
            height: 12.1875rem; /* 195px */
            border-radius: 1.25rem; /* 20px */
            object-fit: cover; /* Maintain aspect ratio */
        }

        /* Case content */
        .content-cases .case-content {
            margin-top: 1.25rem; /* 20px */
            display: flex;
            flex-direction: column;
            gap: 0.375rem; /* 6px */
            font-weight: 700;
            color: rgba(49, 59, 79, 1);
            font-size: 0.75rem; /* 12px */
            letter-spacing: 1px;
        }

        /* Case description text */
        .content-cases .case-desc {
            color: rgba(147, 147, 147, 1); /* Gray */
        }

        /* Case name */
        .content-cases .case-name {
            font-size: 1.5rem; /* 24px */
        }

        /* Case tip text */
        .content-cases .case-tip {
            font-size: 1rem; /* 16px */
        }

        /* Bottom section of case */
        .content-cases .case-bottom {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: end;
            padding: 1.25rem; /* 20px */
        }

        /* Case price */
        .content-cases .case-bottom .price {
            font-weight: 700;
            font-size: 2rem; /* 32px */
            color: rgba(232, 63, 36, 1); /* Red-orange */
        }

        /* Footer styling */
        .footer {
            background-color: rgba(250, 238, 0, 1); /* Yellow */
            padding: 1.875rem 2.5rem; /* 30px 40px */
            display: flex;
            flex-direction: column;
            gap: 0.625rem; /* 10px */
            justify-content: space-around;
            align-items: center;
            color: rgba(65, 65, 65, 1); /* Dark gray */
            font-size: 0.75rem; /* 12px */
            letter-spacing: 4px;
            font-weight: 400;
            margin-top: 1.25rem; /* 20px */
        }

        /* Mobile Responsive Styles */
        @media (max-width: 1086px) {
            /* Center header on mobile */
            .header {
                padding: 1.875rem; /* 30px */
                display: flex;
                justify-content: center;
            }

            /* Adjust hero spacing */
            .hero {
                margin-bottom: 1.25rem; /* 20px */
            }

            /* Stack main content vertically (reverse order for nav) */
            .main {
                flex-direction: column-reverse;
                gap: 0;
                padding: 0 1.875rem; /* 0 30px */
            }

            /* Show mini nav button */
            .main .nav-mini {
                display: flex;
                margin-left: auto; /* Push to right */
            }

            /* Center triangle in mini nav */
            .main .nav-mini .triangle {
                top: 50%;
                transform: translateY(-50%);
            }

            /* Full width nav for mobile */
            .main .nav {
                position: relative;
                width: 100%;
                margin-top: 0.875rem; /* 14px */
            }

            /* Adjust content positioning */
            .main .content {
                margin-top: 7.1875rem; /* 115px */
                margin-left: -1.875rem; /* -30px */
                margin-right: -1.875rem; /* -30px */
            }

            /* Full width content items */
            .main .content .content-item {
                border-radius: 0;
                padding: 2.5rem 1.25rem; /* 40px 20px */
            }

            /* Stack top section vertically */
            .main .content .content-item .item-top {
                flex-direction: column;
                align-items: center;
                position: relative;
            }

            /* Adjust name positioning */
            .main .content .content-item .item-top .name {
                width: 100%;
                display: block;
                position: static;
                text-align: center;
                margin-bottom: 1.25rem; /* 20px */
            }

            /* Center index number */
            .main .content .content-item .item-top .name .index {
                position: absolute;
                left: 50%;
                top: calc(50% + 2.5rem); /* 40px below center */
                transform: translate(-50%, -50%);
                z-index: 1;
                color: rgba(225, 214, 0, .5); /* Semi-transparent yellow */
            }

            /* Reset title positioning */
            .main .content .content-item .item-top .name .index-title {
                position: static;
            }

            /* Full width image */
            .main .content .content-item .item-top .index-img {
                width: 100%;
                height: auto; /* Maintain aspect ratio */
            }

            /* Stack card title vertically */
            .content-item .item-card .item-card-title {
                flex-direction: column;
                align-items: start;
            }

            /* Push price to right */
            .content-item .item-card .item-card-title .price {
                margin-left: auto;
            }

            /* Stack card content vertically */
            .content-item .item-card .item-card-content {
                flex-direction: column;
                align-items: start;
            }

            /* Adjust item card layout */
            .content-item .item-card .item-card-content .item-card-item {
                display: flex;
                justify-content: start;
            }

            /* Fixed width for items */
            .content-item .item-card .item-card-content .item-card-item .item {
                width: 5.625rem; /* 90px */
                margin-right: 0.625rem; /* 10px */
            }

            /* Hide cases section on mobile */
            .content-cases {
                display: none;
            }
        }
    </style>
</head>
<body>
<header class="header">
    <img class="logo" src="assets/img/logo.png" alt="logo">
</header>

<div class="hero">
    <img src="assets/img/logo-icon.png" alt="logo-icon">
    <span class="title">動画制作メニュー</span>
    <span class="text">むびるで対応している動画制作のメニュー一覧</span>
</div>

<div class="main">
    <div class="content">
        <div id="item-1" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">01</span>
                    <span class="index-title">まるっとプラン</span>
                </div>

                <img class="index-img" src="assets/img/img-1.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">まるっとプラン（1時間）</div>
                        <p class="title-desc">すべてをお任せして動画を制作するプラン</p>
                    </div>
                    <span class="price">¥100,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">企画</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">撮影</span>
                        <span class="time">半日/カメラ1台</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">1時間</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">〜4時間</span>
                    </div>
                </div>
            </div>


            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">丸っとプラン（30分）</div>
                        <p class="title-desc">すべてをお任せして動画を制作するプラン</p>
                    </div>
                    <span class="price">¥80,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">企画</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">撮影</span>
                        <span class="time">3時間/カメラ1台</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">30分</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">〜2時間</span>
                    </div>
                </div>
            </div>


            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">丸っとプラン（15分）</div>
                        <p class="title-desc">すべてをお任せして動画を制作するプラン</p>
                    </div>
                    <span class="price">¥60,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">企画</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">撮影</span>
                        <span class="time">1時間/カメラ1台</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">15分</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">〜1時間</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="item-2" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">02</span>
                    <span class="index-title">撮影プラン</span>
                </div>

                <img class="index-img" src="assets/img/img-2.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">撮影プラン（半日）</div>
                        <p class="title-desc">カメラマン1名カメラ1台で撮影</p>
                    </div>
                    <span class="price">¥70,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">撮影</span>
                        <span class="time">半日/カメラ1台</span>
                    </div>
                </div>
            </div>


            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">撮影プラン（3時間）</div>
                        <p class="title-desc">カメラマン1名カメラ1台で撮影</p>
                    </div>
                    <span class="price">¥60,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">撮影</span>
                        <span class="time">3時間/カメラ1台</span>
                    </div>
                </div>
            </div>


            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">撮影プラン（3時間）</div>
                        <p class="title-desc">カメラマン1名カメラ1台で撮影</p>
                    </div>
                    <span class="price">¥50,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">撮影</span>
                        <span class="time">1時間/カメラ1台</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="item-3" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">03</span>
                    <span class="index-title">編集プラン</span>
                </div>

                <img class="index-img" src="assets/img/img-3.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">編集プラン（60分）</div>
                        <p class="title-desc">撮影素材を投げていただき編集</p>
                    </div>
                    <span class="price">¥50,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">1時間</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">〜4時間</span>
                    </div>
                </div>
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">編集プラン（30分）</div>
                        <p class="title-desc">撮影素材を投げていただき編集</p>
                    </div>
                    <span class="price">¥30,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">30分</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">〜1時間</span>
                    </div>
                </div>
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">編集プラン（15分）</div>
                        <p class="title-desc">撮影素材を投げていただき編集</p>
                    </div>
                    <span class="price">¥20,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">15分</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">〜30分</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="item-4" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">04</span>
                    <span class="index-title">テロップ入れ</span>
                </div>

                <img class="index-img" src="assets/img/img-4.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">撮影プラン（半日）</div>
                        <p class="title-desc">編集済みの素材にテロップを追加</p>
                    </div>
                    <span class="price">¥35,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">1時間</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">1時間</span>
                    </div>
                </div>
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">編集プラン（30分）</div>
                        <p class="title-desc">撮影素材を投げていただき編集</p>
                    </div>
                    <span class="price">¥20,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">30分</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">30分</span>
                    </div>
                </div>
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">編集プラン（15分）</div>
                        <p class="title-desc">撮影素材を投げていただき編集</p>
                    </div>
                    <span class="price">¥10,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">15分</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">15分</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="item-5" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">05</span>
                    <span class="index-title">BGM・効果音の挿入</span>
                </div>

                <img class="index-img" src="assets/img/img-5.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">BGM・効果音の挿入</div>
                        <p class="title-desc">編集済みの素材に音楽を追加</p>
                    </div>
                    <span class="price">¥5,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">15分</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">15分</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="item-6" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">06</span>
                    <span class="index-title">撮影機材の追加</span>
                </div>

                <img class="index-img" src="assets/img/img-6.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">照明</div>
                        <p class="title-desc">照明1機を追加</p>
                    </div>
                    <span class="price">¥30,000</span>
                </div>
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">カメラ</div>
                        <p class="title-desc">カメラ1機を追加</p>
                    </div>
                    <span class="price">¥30,000</span>
                </div>
            </div>
        </div>

        <div id="item-7" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">07</span>
                    <span class="index-title">アシスタント追加</span>
                </div>

                <img class="index-img" src="assets/img/img-7.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">アシスタント追加</div>
                        <p class="title-desc">アシスタントを1名追加</p>
                    </div>
                    <span class="price">¥30,000</span>
                </div>
            </div>
        </div>

        <div id="item-8" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">08</span>
                    <span class="index-title">サムネイルの作成</span>
                </div>

                <img class="index-img" src="assets/img/img-8.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">サムネイルの作成</div>
                        <p class="title-desc">サムネイルを1枚作成</p>
                    </div>
                    <span class="price">¥30,000</span>
                </div>
            </div>
        </div>

        <div id="item-9" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">09</span>
                    <span class="index-title">ダイジェストショート動画の制作</span>
                </div>

                <img class="index-img" src="assets/img/img-9.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">ダイジェストショート動画の制作</div>
                        <p class="title-desc">ダイジェストを1本制作</p>
                    </div>
                    <span class="price">¥30,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">1分</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="item-10" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">10</span>
                    <span class="index-title">丸っとショート動画</span>
                </div>

                <img class="index-img" src="assets/img/img-10.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">丸っとショート動画（3時間）</div>
                        <p class="title-desc">新規でショート動画の制作</p>
                    </div>
                    <span class="price">¥70,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">企画</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">撮影</span>
                        <span class="time">3時間/カメラ1台</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">1分</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">3時間まで</span>
                    </div>
                </div>
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">丸っとショート動画（1時間）</div>
                        <p class="title-desc">新規でショート動画の制作</p>
                    </div>
                    <span class="price">¥50,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">企画</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">撮影</span>
                        <span class="time">3時間/カメラ1台</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">1分</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">3時間まで</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="item-11" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">11</span>
                    <span class="index-title">投稿代行</span>
                </div>

                <img class="index-img" src="assets/img/img-11.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">投稿代行</div>
                        <p class="title-desc">動画を1本代行で投稿する</p>
                        <p class="title-desc">タイトル・本文の作成・提供サムネ・動画を投稿</p>
                    </div>
                    <span class="price">¥3,000</span>
                </div>
            </div>
        </div>

        <div id="item-12" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">12</span>
                    <span class="index-title">企画提案</span>
                </div>

                <img class="index-img" src="assets/img/img-12.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">企画提案</div>
                        <p class="title-desc">動画を1本分の企画の提案</p>
                    </div>
                    <span class="price">¥20,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">企画</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div id="item-13" class="content-item">
            <div class="item-top">
                <div class="name">
                    <span class="index">13</span>
                    <span class="index-title">ショート動画の編集</span>
                </div>

                <img class="index-img" src="assets/img/img-13.png">
            </div>

            <div class="item-card">
                <div class="item-card-title">
                    <div class="title-content">
                        <div class="title-text">ショート動画の編集</div>
                        <p class="title-desc">撮影素材を投げていただき編集</p>
                    </div>
                    <span class="price">¥10,000</span>
                </div>

                <div class="item-card-content">
                    <div class="item-card-item">
                        <span class="item">編集</span>
                        <span class="time">
                            <span class="circle"></span>
                        </span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">完成尺</span>
                        <span class="time">1分</span>
                    </div>
                    <div class="item-card-item">
                        <span class="item">素材尺</span>
                        <span class="time">〜30分</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="nav">
        <span id="triangle" class="triangle"></span>
        <ul id="nav">
            <li>
                <span>01</span>
                <span>まるっとプラン</span>
            </li>
            <li>
                <span>02</span>
                <span>撮影プラン</span>
            </li>
            <li>
                <span>03</span>
                <span>編集プラン</span>
            </li>
            <li>
                <span>04</span>
                <span>テロップ入れ</span>
            </li>
            <li>
                <span>05</span>
                <span>BGM・効果音</span>
            </li>
            <li>
                <span>06</span>
                <span>撮影機材</span>
            </li>
            <li>
                <span>07</span>
                <span>アシスタント</span>
            </li>
            <li>
                <span>08</span>
                <span>サムネイル</span>
            </li>
            <li>
                <span>09</span>
                <span>ダイジェストショート動画</span>
            </li>
            <li>
                <span>10</span>
                <span>丸っとショート動画</span>
            </li>
            <li>
                <span>11</span>
                <span>投稿代行</span>
            </li>
            <li>
                <span>12</span>
                <span>企画提案</span>
            </li>
            <li>
                <span>13</span>
                <span>ショート動画編集</span>
            </li>
        </ul>

        <button>
            組み合わせ事例
        </button>
    </div>
    <div class="nav-mini">
        <span class="triangle"></span>
        <span class="nav-num">01</span>
    </div>
</div>

<div class="content-cases">
    <div class="content-title">
        組み合わせ事例
    </div>

    <h3 class="case-title">
        <span>ここに組み合わせタイトルが入ります。</span>

        <span class="price">
            <span>合計金額</span>
            <span class="num">¥93,000</span>
        </span>
    </h3>
    <div class="case-row">
        <div class="case-col">
            <img class="case-img" src="assets/img/col-1.png">
            <div class="case-content">
                <p class="case-desc">丸っとショート動画</p>
                <h4 class="case-name">丸っとショート動画（3時間）</h4>
                <p class="case-tip">撮影素材を投げていただき編集</p>
            </div>

            <div class="case-bottom">
                <span class="price">
                    ¥70,000
                </span>
            </div>
        </div>
        <div class="case-col">
            <img class="case-img" src="assets/img/col-2.png">
            <div class="case-content">
                <p class="case-desc">投稿代行</p>
                <h4 class="case-name">投稿代行</h4>
                <p class="case-tip">動画を1本代行で投稿する<br>
                    タイトル・本文の作成・提供サムネ・動画を投稿</p>
            </div>

            <div class="case-bottom">
                <span class="price">
                    ¥3,000
                </span>
            </div>
        </div>
        <div class="case-col">
            <img class="case-img" src="assets/img/col-3.png">
            <div class="case-content">
                <p class="case-desc">企画提案</p>
                <h4 class="case-name">企画提案</h4>
                <p class="case-tip">動画を1本分の企画の提案</p>
            </div>

            <div class="case-bottom">
                <span class="price">
                    ¥20,000
                </span>
            </div>
        </div>
    </div>

    <h3 class="case-title">
        <span>ここに組み合わせタイトルが入ります。</span>

        <span class="price">
            <span>合計金額</span>
            <span class="num">¥93,000</span>
        </span>
    </h3>
    <div class="case-row">
        <div class="case-col">
            <img class="case-img" src="assets/img/col-1.png">
            <div class="case-content">
                <p class="case-desc">丸っとショート動画</p>
                <h4 class="case-name">丸っとショート動画（3時間）</h4>
                <p class="case-tip">撮影素材を投げていただき編集</p>
            </div>

            <div class="case-bottom">
                <span class="price">
                    ¥70,000
                </span>
            </div>
        </div>
        <div class="case-col">
            <img class="case-img" src="assets/img/col-2.png">
            <div class="case-content">
                <p class="case-desc">投稿代行</p>
                <h4 class="case-name">投稿代行</h4>
                <p class="case-tip">動画を1本代行で投稿する<br>
                    タイトル・本文の作成・提供サムネ・動画を投稿</p>
            </div>

            <div class="case-bottom">
                <span class="price">
                    ¥3,000
                </span>
            </div>
        </div>
        <div class="case-col">
            <img class="case-img" src="assets/img/col-3.png">
            <div class="case-content">
                <p class="case-desc">企画提案</p>
                <h4 class="case-name">企画提案</h4>
                <p class="case-tip">動画を1本分の企画の提案</p>
            </div>

            <div class="case-bottom">
                <span class="price">
                    ¥20,000
                </span>
            </div>
        </div>
    </div>

    <h3 class="case-title">
        <span>ここに組み合わせタイトルが入ります。</span>

        <span class="price">
            <span>合計金額</span>
            <span class="num">¥93,000</span>
        </span>
    </h3>
    <div class="case-row">
        <div class="case-col">
            <img class="case-img" src="assets/img/col-1.png">
            <div class="case-content">
                <p class="case-desc">丸っとショート動画</p>
                <h4 class="case-name">丸っとショート動画（3時間）</h4>
                <p class="case-tip">撮影素材を投げていただき編集</p>
            </div>

            <div class="case-bottom">
                <span class="price">
                    ¥70,000
                </span>
            </div>
        </div>
        <div class="case-col">
            <img class="case-img" src="assets/img/col-2.png">
            <div class="case-content">
                <p class="case-desc">投稿代行</p>
                <h4 class="case-name">投稿代行</h4>
                <p class="case-tip">動画を1本代行で投稿する<br>
                    タイトル・本文の作成・提供サムネ・動画を投稿</p>
            </div>

            <div class="case-bottom">
                <span class="price">
                    ¥3,000
                </span>
            </div>
        </div>
        <div class="case-col">
            <img class="case-img" src="assets/img/col-3.png">
            <div class="case-content">
                <p class="case-desc">企画提案</p>
                <h4 class="case-name">企画提案</h4>
                <p class="case-tip">動画を1本分の企画の提案</p>
            </div>

            <div class="case-bottom">
                <span class="price">
                    ¥20,000
                </span>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <img class="logo" src="assets/img/logo.png">
    <p>動画制作をプロへ依頼なら『むびる』！</p>
    <p>&copy; むびる All Rights Reserved.</p>
</div>

<script>
    // Get all list items in the navigation
    const navItems = document.getElementById('nav').getElementsByTagName('li');
    // Add click event listeners to each navigation item
    for (let i = 0; i < navItems.length; i++) {
        navItems[i].addEventListener('click', function (e) {
            // Update window hash to reflect the clicked item
            window.location.hash = `#item-${i + 1}`;
        });
    }

    // Get the triangle indicator element
    const triangleIndicator = document.getElementById('triangle');


    /**
     * Position the triangle indicator next to the active navigation item
     * @param {number|string} itemNumber - The 1-based index of the active item
     */
    function positionTriangleIndicator(itemNumber) {
        const itemIndex = Number(itemNumber) - 1;
        if (itemIndex >= 0 && itemIndex < navItems.length) {
            const itemTopPosition = navItems[itemIndex].offsetTop;
            triangleIndicator.style.top = `${itemTopPosition + 6}px`;
        }
    }

    // Create an IntersectionObserver to detect which content item is currently visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.id;
                if (id) {
                    // Extract the item number from the ID (format: 'item-1')
                    const parts = id.split('-');
                    if (parts.length > 1) {
                        positionTriangleIndicator(parts[1]);
                    }
                }
            }
        });
    }, {threshold: 0.5}); // Trigger when 50% of the item is visible

    // Observe all content items for intersection changes
    const contentItems = document.getElementsByClassName('content-item');
    for (let i = 0; i < contentItems.length; i++) {
        observer.observe(contentItems[i]);
    }
</script>
</body>
</html>