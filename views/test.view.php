<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: #fafafa;
            background-image: linear-gradient(to right, white 2px, transparent 0),
            linear-gradient(to bottom, white 2px, transparent 0);
            background-size: 22px 22px;
            min-height: 100vh;
        }

        .header {
            padding: 1.25rem 2.5rem;
        }

        .logo {
            height: 1.87rem;
        }

        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 4.375rem;
            margin-bottom: 3.75rem;
            gap: 1.5rem;
        }

        .hero img {
            width: 7.5rem;
        }

        .hero .title {
            font-size: 2.5rem;
            font-weight: bold;
            letter-spacing: 3px;
            color: hsla(219, 24%, 25%, 1);
        }

        .hero .text {
            font-weight: 700;
            color: hsla(219, 24%, 25%, 1);
            font-size: 1.125rem;
            letter-spacing: 1px;
        }

        .main {
            padding: 0 2.5rem;
            display: flex;
            gap: 2.1875rem;
            position: relative;
        }

        .content {
            flex: 1;
        }

        .nav {
            width: 20rem;
            background-color: white;
            border: 3px solid hsla(0, 0%, 97%, 1);
            border-radius: 1.25rem;
            height: 44rem;
            overflow: visible;
            padding: 2.5rem 1.875rem;
            display: flex;
            flex-direction: column;
            gap: 0.625rem;
            box-shadow: 0 0 18px rgba(243, 243, 243, 0.25);
            position: sticky;
            top: 0;
            right: 0;
        }

        .nav-mini {
            display: none;
            width: 3.75rem;
            height: 3.75rem;
            border: 3px solid rgba(246, 246, 246, 1);
            background-color: white;
            border-radius: 0.625rem;
            justify-content: center;
            align-items: center;
            font-size: 1.75rem;
            font-weight: 900;
            position: relative;
            color: rgba(49, 59, 79, 1);
        }

        .triangle {
            width: 0;
            height: 0;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-left: 12px solid rgba(232, 63, 36, 1);
            position: absolute;
            left: -10px;
            top: calc(2.5rem + 5px);
            transition: top .2s ease;
        }

        .nav li {
            list-style: none;
            display: flex;
            justify-content: start;
            font-size: 1.125rem;
            color: rgba(147, 147, 147, 1);
            letter-spacing: 1px;
            font-weight: 700;
            gap: 1.25rem;
            cursor: pointer;
            transition: color .3s ease;
            margin-bottom: 1rem;
        }

        .nav li:hover {
            color: rgba(49, 59, 79, 1);
            transition: color .3s ease;
        }

        .nav button {
            width: 100%;
            border: 1px solid rgba(49, 59, 79, 1);
            color: rgba(49, 59, 79, 1);
            border-radius: 1.875rem;
            height: 2.1875rem;
            background-color: transparent;
            margin: 0;
            cursor: pointer;
            transition: all .3s ease;
        }

        .nav button:hover {
            box-shadow: 0 0 .4rem rgb(223, 222, 222);
            transition: all .3s ease;
        }

        .content-item {
            background-color: #F9E500;
            border-radius: 1.25rem;
            border: 6px solid rgba(241, 229, 0, 1);
            padding: 2.5rem 3.75rem;
            gap: 2.5rem;
            display: flex;
            flex-direction: column;
        }

        .content-item:not(:last-child) {
            margin-bottom: 2.5rem;
        }

        .content-item .item-top {
            display: flex;
            width: 100%;
            justify-content: space-between;
        }

        .content-item .item-top .index-img {
            width: 22.5rem;
            height: 11.25rem;
            border-radius: 1.25rem;
        }

        .content-item .item-top .name {
            position: relative;
        }

        .content-item .item-top .name .index {
            position: absolute;
            top: 50%;
            left: -1.875rem;
            transform: translateY(-50%);
            font-weight: 900;
            font-size: 10rem;
            color: rgba(225, 214, 0, 1);
        }

        .content-item .item-top .name .index-title {
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: 1px;
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            word-break: keep-all;
            color: rgba(49, 59, 79, 1);
        }

        .content-item .item-card {
            background-color: white;
            border-radius: 1.25rem;
            padding: 1.25rem 2.5rem;
        }

        .content-item .item-card .item-card-title {
            display: flex;
            align-items: end;
        }

        .content-item .item-card .item-card-title .title-content {
            flex: 1;
            color: rgba(49, 59, 79, 1);
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .content-item .item-card .item-card-title .price {
            color: rgba(232, 63, 36, 1);
            font-weight: 700;
            font-size: 2rem;
        }

        .content-item .item-card .item-card-title .title-content .title-text {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .content-item .item-card .item-card-title .title-content .title-desc {
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 1rem;
        }

        .content-item .item-card .item-card-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.625rem;
            margin-top: 0.625rem;
        }

        .content-item .item-card .item-card-content .item-card-item {
            flex: 1;
            font-weight: 700;
            color: rgba(49, 59, 79, 1);
            letter-spacing: 1px;
            font-size: 1rem;
            height: 4rem;
        }

        .content-item .item-card .item-card-content .item-card-item span {
            display: block;
            margin: auto;
            height: 2rem;
            text-align: center;
        }

        .content-item .item-card .item-card-content .item-card-item .item {
            border-radius: 1.25rem;
            border: 1px dashed rgba(219, 219, 219, 1);
            padding: .25rem 1.25rem;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .content-item .item-card .item-card-content .item-card-item .time {
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .content-item .item-card .item-card-content .item-card-item .circle {
            border-radius: 50%;
            border: 1px solid rgba(49, 59, 79, 1);
            width: 1rem;
            height: 1rem;
        }

        .content-title {
            background-color: rgba(245, 233, 0, 1);
            border-radius: 3.75rem;
            border: 3px solid rgba(241, 229, 0, 1);
            padding: 0.625rem;
            text-align: center;
            font-size: 2rem;
            color: rgba(49, 59, 79, 1);
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 4rem;
        }

        .content-cases {
            padding: 5rem 3.75rem 1.875rem 3.75rem;
        }

        .content-cases .case-title {
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: 1px;
            color: rgba(49, 59, 79, 1);
            margin-bottom: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-cases .case-title .price .num {
            font-size: 3rem;
            font-weight: 700;
            color: rgba(232, 63, 36, 1);
            margin-left: 1.25rem;
        }

        .content-cases .case-row {
            display: flex;
            gap: 1.25rem;
            justify-content: space-around;
            height: 26.25rem;
        }

        .content-cases .case-row:not(:last-child) {
            margin-bottom: 5rem;
        }

        .content-cases .case-col {
            background-color: white;
            border-radius: 1.25rem;
            border: 3px solid rgba(240, 240, 240, 1);
            padding: 1.25rem;
            height: 100%;
            position: relative;
        }

        .content-cases .case-img {
            width: 100%;
            height: 12.1875rem;
            border-radius: 1.25rem;
            object-fit: cover;
        }

        .content-cases .case-content {
            margin-top: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
            font-weight: 700;
            color: rgba(49, 59, 79, 1);
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .content-cases .case-desc {
            color: rgba(147, 147, 147, 1);
        }

        .content-cases .case-name {
            font-size: 1.5rem;
        }

        .content-cases .case-tip {
            font-size: 1rem;
        }

        .content-cases .case-bottom {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: end;
            padding: 1.25rem;
        }

        .content-cases .case-bottom .price {
            font-weight: 700;
            font-size: 2rem;
            color: rgba(232, 63, 36, 1);
        }

        .footer {
            background-color: rgba(250, 238, 0, 1);
            padding: 1.875rem 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.625rem;
            justify-content: space-around;
            align-items: center;
            color: rgba(65, 65, 65, 1);
            font-size: 0.75rem;
            letter-spacing: 4px;
            font-weight: 400;
            margin-top: 1.25rem;
        }

        @media (max-width: 855px) {
            .header {
                padding: 1.875rem;
                display: flex;
                justify-content: center;
            }

            .hero {
                margin-bottom: 1.25rem;
            }

            .main {
                flex-direction: column-reverse;
                gap: 0;
                padding: 0 1.875rem;
            }

            .main .nav-mini {
                display: flex;
                margin-left: auto;
            }

            .main .nav-mini .triangle {
                top: 50%;
                transform: translateY(-50%);
            }

            .main .nav {
                position: relative;
                width: 100%;
                margin-top: 0.875rem;
            }

            .main .content {
                margin-top: 7.1875rem;
                margin-left: -1.875rem;
                margin-right: -1.875rem;
            }

            .main .content .content-item {
                border-radius: 0;
                padding: 2.5rem 1.25rem;
            }

            .main .content .content-item .item-top {
                flex-direction: column;
                align-items: center;
                position: relative;
            }

            .main .content .content-item .item-top .name {
                width: 100%;
                display: block;
                position: static;
                text-align: center;
                margin-bottom: 1.25rem;
            }

            .main .content .content-item .item-top .name .index {
                position: absolute;
                left: 50%;
                top: calc(50% + 2.5rem);
                transform: translate(-50%, -50%);
                z-index: 1;
                color: rgba(225, 214, 0, .5);
            }

            .main .content .content-item .item-top .name .index-title {
                position: static;
            }

            .main .content .content-item .item-top .index-img {
                width: 100%;
                height: auto;
            }

            .content-item .item-card .item-card-title {
                flex-direction: column;
                align-items: start;
            }

            .content-item .item-card .item-card-title .price {
                margin-left: auto;
            }

            .content-item .item-card .item-card-content {
                flex-direction: column;
                align-items: start;
            }

            .content-item .item-card .item-card-content .item-card-item {
                display: flex;
                justify-content: start;
            }

            .content-item .item-card .item-card-content .item-card-item .item {
                width: 5.625rem;
                margin-right: 0.625rem;
            }

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
    const lis = document.getElementById('nav').getElementsByTagName('li');
    for (let i in lis) {
        lis.item(i).addEventListener('click', function (e) {
            const n = Number(i) + 1;
            if (!n) {
                return;
            }
            window.location.hash = '#item-' + n;
        });
    }

    const triangle = document.getElementById('triangle');

    function triangle2(n) {
        const height = lis.item(Number(n) - 1).offsetTop;
        triangle.style.top = (height + 6) + 'px';
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                if (id) {
                    const p = id.split('-');
                    if (p[1]) {
                        triangle2(p[1]);
                    }
                }
            }
        });
    }, {threshold: 0.5});

    const items = document.getElementsByClassName('content-item');
    for (let k in items) {
        observer.observe(items.item(k));
    }
</script>
</body>
</html>