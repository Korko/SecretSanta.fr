<script>
    import Bmc from './bmc.vue';
    import RandomForm from './random/form.vue';

    export default {
        components: {
            Bmc,
            RandomForm
        },
        props: {
            bmc: {
                type: String,
                required: true
            }
        }
    }
</script>

<template>
    <div id="header">
        <div class="bg-overlay"></div>
        <div class="center text-center">
            <div class="banner">
                <h1>{{ $t('form.title') }}</h1>
            </div>
            <div class="subtitle">
                <h4>{{ $t('form.subtitle') }}</h4>
            </div>
        </div>
        <div class="bottom text-center">
            <a id="scrollDownArrow" href="#"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>

    <div id="what" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">{{ $t('form.section.what.title') }}</h2>
            <p class="lead main text-center">{{ $t('form.section.what.subtitle') }}</p>
            <div class="row text-center what">
                <ul class="media-list w-100">
                    <li class="media media-icon-left media-calendar-icon">
                        <div class="media-body">
                            <h4 class="media-heading">{{ $t('form.section.what.heading1') }}</h4>
                            <p class="paragraph">{{ $t('form.section.what.content1') }}</p>
                        </div>
                    </li>
                </ul>
                <div class="card w-100">
                    <div class="bg-light card-body">
                        <h6 class="card-title">{{ $t('form.fyi') }}</h6>
                        <p class="card-text">
                            <p class="paragraph">{{ $t('form.section.what.notice') }}</p>
                            <bmc :login="bmc"></bmc>
                        </p>
                    </div>
                </div>
            </div>
        </div><!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div><!-- #what -->

    <div class="parallax">
        <div class="container inner"></div>
    </div>

    <div id="how" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">{{ $t('form.section.how.title') }}</h2>
            <p class="lead main text-center">{{ $t('form.section.how.subtitle') }}</p>
            <div class="row text-center how">
                <ul class="media-list w-100">
                    <li class="media media-icon-left media-user-icon">
                        <div class="media-body">
                            <h4 class="media-heading">{{ $t('form.section.how.heading1') }}</h4>
                            <p class="paragraph">{{ $t('form.section.how.content1') }}</p>
                        </div>
                    </li>
                    <li class="media media-icon-right media-paper-icon">
                        <div class="media-body">
                            <h4 class="media-heading">{{ $t('form.section.how.heading2') }}</h4>
                            <p class="paragraph">{{ $t('form.section.how.content2') }}</p>
                        </div>
                    </li>
                    <li class="media media-icon-left media-mail-icon">
                        <div class="media-body">
                            <h4 class="media-heading">{{ $t('form.section.how.heading3') }}</h4>
                            <p class="paragraph">{{ $t('form.section.how.content3') }}</p>
                        </div>
                    </li>
                    <li class="media media-icon-right media-clock-icon">
                        <div class="media-body">
                            <h4 class="media-heading">{{ $t('form.section.how.heading4') }}</h4>
                            <p class="paragraph">{{ $t('form.section.how.content4') }}</p>
                        </div>
                    </li>
                </ul>
                <div class="card w-100">
                    <div class="bg-light card-body">
                        <h6 class="card-title">{{ $t('form.fyi') }}</h6>
                        <p class="card-text paragraph" v-html="$t('form.section.how.notice')"></p>
                    </div>
                </div>
            </div>
        </div><!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div><!--/#how-->

    <div class="parallax parallax2">
        <div class="container inner"></div>
    </div>

    <div id="form" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">{{ $t('form.section.go.title') }}</h2>
            <p class="lead main text-center">{{ $t('form.section.go.subtitle') }}</p>
            <random-form id="randomForm" action="/"></random-form>
        </div><!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div>
</template>

<style>
    /* ==========================================================================
        02. Header
    ========================================================================== */
    #header {
        background-image: url("../../images/gifts-1.jpg");
        /*background-image: image-set(
            url("../../images/gifts-1.avif") type("image/avif"),
            url("../../images/gifts-1.webp") type("image/webp"),
            url("../../images/gifts-1.jpg") type("image/jpeg")
        );*/
        -moz-background-size: cover;
        background-size: cover;
        background-position: center center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        position: relative;
    }
    /*@media (max-width: 1140px) {
        #header {
            background-image: url("../../images/gifts-1x2.jpg");
            background-image: image-set(
                url("../../images/gifts-1x2.avif") type("image/avif"),
                url("../../images/gifts-1x2.webp") type("image/webp"),
                url("../../images/gifts-1x2.jpg") type("image/jpeg")
            );
        }
    }
    @media (max-width: 960px) {
        #header {
            background-image: url("../../images/gifts-1x3.jpg");
            background-image: image-set(
                url("../../images/gifts-1x3.avif") type("image/avif"),
                url("../../images/gifts-1x3.webp") type("image/webp"),
                url("../../images/gifts-1x3.jpg") type("image/jpeg")
            );
        }
    }
    @media (max-width: 480px) {
        #header {
            background-image: url("../../images/gifts-1x4.jpg");
            background-image: image-set(
                url("../../images/gifts-1x4.avif") type("image/avif"),
                url("../../images/gifts-1x4.webp") type("image/webp"),
                url("../../images/gifts-1x4.jpg") type("image/jpeg")
            );
        }
    }*/

    #header .center {
        position: relative;
        z-index: 1;
        color: white;
        padding: 70px 0;
    }

    #header .bottom {
        color: white;
    }

    #header .center .slogan {
        font-size: 26px;
        text-transform: uppercase;
    }

    #header .banner h1 {
        font-size: 100px;
        color: white;
        text-transform: uppercase;
        font-weight: bold;
        display: inline-block;
        background: #a00;
        padding: 0 18px;
    }

    #header .subtitle h4 {
        display: inline-block;
        background: white;
        color: #a00;
        font-size: 38px;
        padding: 0 15px;
    }

    #header .bottom {
        text-align: center;
        width: 100%;
        position: absolute;
        bottom: 30px;
    }

    #header .bottom a {
        font-size: 36px;
        color: whitesmoke;
        position: relative;
        top: -5px;
    }

    .bg-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(44, 33, 5, 0.2);
        z-index: 0;
    }

    /* ==========================================================================
        04. Parallax
    ========================================================================== */
    .parallax {
        background: url("../../images/gifts-2.jpg") fixed no-repeat center center;
        /*background-image: image-set(
            url("../../images/gifts-2.avif") type("image/avif"),
            url("../../images/gifts-2.webp") type("image/webp"),
            url("../../images/gifts-2.jpg") type("image/jpeg")
        );*/
        -moz-background-size: cover;
        background-size: cover;
        position: relative;
        color: #fff;
    }

    .parallax .inner {
        padding-top: 130px;
        padding-bottom: 130px;
    }

    .parallax.parallax2 {
        background: url("../../images/gifts-3.jpg");
        /*background-image: image-set(
            url("../../images/gifts-3.avif") type("image/avif"),
            url("../../images/gifts-3.webp") type("image/webp"),
            url("../../images/gifts-3.jpg") type("image/jpeg")
        );*/
    }

    /* ==========================================================================
        05. What/How
    ========================================================================== */
    .light-wrapper {
        background: #fbfbfb;
    }

    .light-wrapper .inner {
        padding-top: 50px;
        padding-bottom: 50px;
    }

    .section-title {
        font-size: 36px;
        line-height: 40px;
        text-transform: uppercase;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .main.lead {
        margin-bottom: 80px;
    }

    .lead {
        font-size: 17px;
        line-height: 24px;
        font-weight: normal;
        text-transform: uppercase;
        margin-bottom: 15px;
        color: #2e2e2e;
        position: relative;
    }

    .lead::after {
        position: absolute;
        content: ' ';
        background: #a00;
        width: 80px;
        height: 3px;
        bottom: -22px;
        left: 50%;
        margin-left: -40px;
    }

    .media {
        min-height: 128px;
    }

    .media-icon-left {
        background-position-x: left;
        padding-left: 128px;
    }
    .media-icon-right {
        background-position-x: right;
        padding-right: 128px;
    }

    .media-calendar-icon {
        background-image: url("../../images/calendar-icon.png");
        /*background-image: image-set(
            url("../../images/calendar-icon.avif") type("image/avif"),
            url("../../images/calendar-icon.webp") type("image/webp"),
            url("../../images/calendar-icon.png") type("image/png")
        );*/
        background-repeat: no-repeat;
    }
    .media-user-icon {
        background-image: url("../../images/user-icon.png");
        /*background-image: image-set(
            url("../../images/user-icon.avif") type("image/avif"),
            url("../../images/user-icon.webp") type("image/webp"),
            url("../../images/user-icon.png") type("image/png")
        );*/
        background-repeat: no-repeat;
    }
    .media-paper-icon {
        background-image: url("../../images/paper-icon.png");
        /*background-image: image-set(
            url("../../images/paper-icon.avif") type("image/avif"),
            url("../../images/paper-icon.webp") type("image/webp"),
            url("../../images/paper-icon.png") type("image/png")
        );*/
        background-repeat: no-repeat;
    }
    .media-mail-icon {
        background-image: url("../../images/mail-icon.png");
        /*background-image: image-set(
            url("../../images/mail-icon.avif") type("image/avif"),
            url("../../images/mail-icon.webp") type("image/webp"),
            url("../../images/mail-icon.png") type("image/png")
        );*/
        background-repeat: no-repeat;
    }
    .media-clock-icon {
        background-image: url("../../images/clock-icon.png");
        /*background-image: image-set(
            url("../../images/clock-icon.avif") type("image/avif"),
            url("../../images/clock-icon.webp") type("image/webp"),
            url("../../images/clock-icon.png") type("image/png")
        );*/
        background-repeat: no-repeat;
    }

    .media-body {
        width: auto;
        text-align: left;
        margin: auto;
    }

    .media-heading {
        font-weight: bold;
    }

    div.card-body {
        font-style: italic;
    }

    div.card-body::first-line {
        font-weight: bold;
        font-style: normal;
        text-transform: uppercase;
    }
</style>