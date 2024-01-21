<script>
    import { Link } from '@inertiajs/inertia-vue3'

    export default {
        components: {
            Link,
        }
    }
</script>

<template>
    <header id="menu" class="navbar navbar-dark fixed-top navbar-expand-md" role="navigation">
        <nav id="navbar">
            <div id="logo">
                <a href="/">
                    <picture>
                        <source type="image/avif" srcset="../../images/logo.avif">
                        <source type="image/webp" srcset="../../images/logo.webp">
                        <source type="image/png" srcset="../../images/logo.png">
                        <img src="../../images/logo.png" alt="Logo">
                    </picture>
                </a>
            </div>

            <ul class="nav navbar-nav navbar-nav-left">
                <slot name="navbar-left" />
            </ul>
            <ul class="nav navbar-nav navbar-nav-right">
                <slot name="navbar-right" />
            </ul>
        </nav>
    </header>
    <slot name="body">
        <div id="content">
            <slot name="content" />
        </div>
    </slot>
</template>

<style scoped>
    /* ==========================================================================
        01. Navigation
    ========================================================================== */
    #menu {
        background: var(--main);
        margin-bottom: 0;
    }

    nav#navbar {
        width: 100%;
        height: 50px;
        display: flex;
    }
    @media (max-width: 767px) {
        .navbar {
            min-height: 30px;
        }
    }
    @media (min-width: 768px) {
        .navbar-nav {
            float: none !important;
            width: 100%;
            text-align: center;
            margin-left: -16px;
        }

        .navbar-nav > li {
            display: inline-block;
            float: none;
        }
    }

    .navbar-brand h2 {
        margin-top: 0;
        font-weight: bold;
        color: white;
    }

    .navbar-brand {
        padding-top: 8px;
        padding-bottom: 0;
    }

    .nav.navbar-nav-left {
        display: inline-flex;
        justify-content: flex-end;
        flex: 1;
        margin-right: 60px;
    }

    .nav.navbar-nav-right {
        display: inline-flex;
        justify-content: flex-start;
        flex: 1;
        margin-left: 60px;
    }

    #logo {
        display: inline-block;
        height: 100%;
    }

    #logo a {
        width: 100%;
        height: 100%;
        display: inline-block;
    }

    /* ==========================================================================
        02. Content
    ========================================================================== */
    #content {
        padding-top: 140px;
        padding-bottom: 60px;
    }
</style>